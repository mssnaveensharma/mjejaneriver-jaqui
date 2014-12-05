<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelReservations extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array	An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'confirmation_id', 'c.confirmation_id',
				'name', 'c.first_name',
				'hotel', 'h.hotel_name',
				'voucher', 'c.voucher',
				'created', 'c.created',
				'start_date', 'c.start_date',
				'end_date', 'c.end_date',
				'created', 'c.created',
				'created', 'c.created',
				'created', 'c.created',
			);
		}

		parent::__construct($config);
	}

	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$search = $this->getUserStateFromRequest($this->context.'.search', 'filter_search', '');
		$this->setState('filter.search', $search);
		
		$voucher = $this->getUserStateFromRequest($this->context.'.voucher', 'filter_voucher', '');
		$this->setState('filter.voucher', $voucher);
		
		$startDate = $this->getUserStateFromRequest($this->context.'.start_date', 'filter_start_date', '');
		$this->setState('filter.start_date', $startDate);
		
		$endDate = $this->getUserStateFromRequest($this->context.'.end_date', 'filter_end_date', '');
		$this->setState('filter.end_date', $endDate);
		
		$status = $this->getUserStateFromRequest($this->context.'.status', 'filter_status', '');
		$this->setState('filter.status', $status);
		
		$paymentStatus = $this->getUserStateFromRequest($this->context.'.payment_status', 'filter_payment_status', '');
		$this->setState('filter.payment_status', $paymentStatus);
		
		$hotelId = $this->getUserStateFromRequest($this->context.'.hotel_id', 'filter_hotel_id', '');
		$this->setState('filter.hotel_id', $hotelId);
		
		$roomType = $this->getUserStateFromRequest($this->context.'.room_type', 'filter_room_type', '');
		$this->setState('filter.room_type', $roomType);
		
		$limit = JRequest::getVar("limit",0);
		if($limit==0){
			JRequest::setVar("limit",50);
		}
		
		// List state information.
		parent::populateState('c.confirmation_id', 'desc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.hotel_id');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();
		
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		if (empty($items))
		{
			return array();
		}
		

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'c.confirmation_id,c.hotel_id, c.start_date, c.end_date, c.first_name,c.cancellation_notes , 
										c.last_name, c.reservation_status, c.voucher, c.created, c.adults, c.children,c.rooms, c.total'));
		$query->from($db->quoteName('#__hotelreservation_confirmations').' AS c');
		
		$query->select('h.hotel_name');
		$query->join('LEFT', '#__hotelreservation_hotels AS h ON c.hotel_id=h.hotel_id');
		
		$query->select(' sum(cr.adults) as total_adults,sum(cr.adults) as total_children');
		$query->join('LEFT', '#__hotelreservation_confirmations_rooms  AS cr ON c.confirmation_id=cr.confirmation_id');
		
		$query->select('s.status_reservation_name, s.bkcolor, s.color, s.is_modif');
		$query->join('LEFT', '#__hotelreservation_status_reservation AS s ON c.reservation_status=s.status_reservation_id');
		
		$query->select('min(cp.payment_status) as payment_status, (cp.amount) as amount_paid'); 
		$query->join('LEFT', '#__hotelreservation_confirmations_payments as cp on c.confirmation_id= cp.confirmation_id');
		
		//if other than super user restrict hotels
		$user	= JFactory::getUser();
		if(!$user->get('isRoot')){
			$query->join('INNER', $db->quoteName('#__hotelreservation_user_hotel_mapping').' AS hum ON h.hotel_id=hum.hotel_id');
			$query->where("hum.user_id = ".$user->id);
		}
		
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (is_numeric($search)) {
				$query->where("c.confirmation_id=$search");
			}
			else {
				$query->where("(c.first_name LIKE '%$search%' or c.last_name LIKE '%$search%' or h.hotel_name LIKE '%$search%')");
			}
		}
		
		// Filter by search in title.
		$searchVoucher = $this->getState('filter.voucher');
		if (!empty($searchVoucher)) {
				//dmp($searchVoucher); 
				$query->where("c.voucher LIKE '%".$searchVoucher."%'");
		}
		
		$searchStartDate= $this->getState('filter.start_date');
		$searchEndDate= $this->getState('filter.end_date');
		
		if (!empty($searchEndDate) && !empty($searchStartDate)) {
			$query->where("c.start_date between '".JHotelUtil::convertToMysqlFormat($searchStartDate)."' and '".JHotelUtil::convertToMysqlFormat($searchEndDate)."'");
		}
		else if (!empty($searchStartDate)) {
			$query->where("c.start_date >= ".JHotelUtil::convertToMysqlFormat($searchStartDate));
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('h.hotel_id = '.$hotelId);
		}
		
		// Filter the items over the menu id if set.
		$roomId = $this->getState('filter.room_type');
		if (!empty($roomId)) {
			$query->where('cr.room_id = '.$roomId);
		}
		
		// Filter the items over the menu id if set.
		$status = $this->getState('filter.status');
		if (!empty($status)) {
			$query->where('s.status_reservation_id = '.$status);
		}
		
		// Filter the items over the menu id if set.
		$payment_status = $this->getState('filter.payment_status');
		if ($payment_status!=-1 && $payment_status!="") {
			$query->where('cp.payment_status = '.$db->quote($payment_status));
		}
		
		
		$query->group('c.confirmation_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'c.confirmation_id')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->_hotels )) 
		{
			$query = ' SELECT h.*, c.country_name
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						ORDER BY hotel_name, country_name ';
			$this->_hotels = $this->_getList( $query );
		}
		return $this->_hotels;
	}
	
	function getRoomTypesOptions(){
		$options = array();
		
		$hotelId = $this->getState('filter.hotel_id');
		if(!$hotelId){
			return $options;
		}
		$query = " SELECT *
					FROM #__hotelreservation_rooms
					where hotel_id = $hotelId
					ORDER by room_name ";
		$rooms = $this->_getList( $query );
	
		foreach($rooms as $room){
			$options[]	= JHtml::_('select.option', $room->room_id, $room->room_name);
		}
		return $options;
	}
	
	function getStatusReservation()
	{
		$query = ' SELECT *, status_reservation_id as value, status_reservation_name as text FROM #__hotelreservation_status_reservation ORDER BY `order` ';
		$res = $this->_getList( $query );
		return $res;
	}
	
	function changeReservationStatus($reservationId, $statusId){
		//setam sarea rezervarii
		$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $statusId WHERE confirmation_id = ".$reservationId;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changePaymentStatus($reservationId, $paymentStatusId){
		
	}
	public function exportToCSV(){
		$this->reservationStatuses = JHotelReservationHelper::getReservationStatuses();
		$this->paymentStatuses = JHotelReservationHelper::getPaymentStatuses();
	
		$this->populateState();
		$reservations = $this->getItems();
		$csv_output = "id;name;hotel;voucher;check in;check out;created at;adults;children;rooms;status;payment status"."\n";
		foreach($reservations as $item){
			$reservationStatus = $this->reservationStatuses[$item->reservation_status];
			$paymentStatus = $this->paymentStatuses[$item->payment_status];
			$csv_output .= "$item->confirmation_id;$item->first_name $item->last_name;$item->hotel_name;$item->voucher;$item->start_date;$item->end_date;$item->created;$item->total_adults;$item->total_children;$item->rooms;$reservationStatus;$paymentStatus;";
			$csv_output .= "\n";
		}
	
		ob_clean();
	
		$fileName = "jhotel_reservations_listing";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}
	
	function uploadFile($fileName, &$data, $dest){
	
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar($fileName, null, 'files', 'array');
	
		if($file['name']=='')
		return true;
	
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
			
		//Clean up filename to get rid of strange characters like spaces etc
		$fileNameSrc = JFile::makeSafe($file['name']);
		$data[$fileName] =  $fileNameSrc;
	
		$src = $file['tmp_name'];
		$dest = $dest."/".$fileNameSrc;
	
		//dump($src);
		//dump($dest);
		//exit;
		$result =  JFile::upload($src, $dest);
	
		if($result)
		return $dest;
	
		return null;
	}
	
	
	function batchCancelReservations($filePath, $delimiter){

		$row = 1;
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 3000, $delimiter)) !== FALSE) {
				$reservation = array();
				if($row==1){
					$header = $data;
					$row++;
					continue;
				}
				$num = count($data);
				//dump($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					$reservation[strtolower($header[$c])]= $data[$c];
				}	

				$table = $this->getTable("Confirmations");
				if(intval($reservation['reservation_id'])>0){
					$reservation['reservation_id'] = intval($reservation['reservation_id']);
					if($table->setStatus($reservation['reservation_id'], CANCELED_ID)){
						$table->updateCancelationComments($reservation['reservation_id'], $reservation['cancellation_note']);
						$this->sendCancellationEmail($reservation['reservation_id']);
					}
				}
			}
			fclose($handle);
		}	
		$result = new stdClass();
		return $result;
	
	}
	
	function sendCancellationEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId,-1);
		$sentResult = true;
		if($reservationDetails->hotelId>0)
			 $sentResult = EmailService::sendCancelationEmail($reservationDetails);
		return $sentResult; 
	}
}
