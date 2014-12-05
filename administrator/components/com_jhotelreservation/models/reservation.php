<?php


defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
/**
 * Company Model for Companies.
 *
 */
class JHotelReservationModelReservation extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_RESERVATION';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.reservation';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Confirmations', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	public function populateState()
	{
		$app = JFactory::getApplication('administrator');

		$id = JRequest::getInt('reservationId');
		$this->setState('reservation.id', $id);
		
		$statusId = JRequest::getInt('statusId',0);
		$this->setState('reservation.status',$statusId);

		$hotelId = JRequest::getInt('hotel_id',0);
		//dmp($hotelId);
		if($hotelId){
			$this->setState('reservation.hotel_id',$hotelId);
		}
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null)
	{
		$reservation = null;
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('reservation.id');
		
		if(!$this->getState('reservation.hotel_id') && !$itemId)
			return $reservation;
		
		$reservationService = new ReservationService();
		$reservation = $reservationService->getReservation($itemId, $this->getState('reservation.hotel_id'), false);
		if($reservation)
			$this->setState('reservation.hotel_id', $reservation->reservationData->userData->hotelId);
		
		return $reservation;
	}
	
	
	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jhotelreservation.reservation', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.reservation.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		$id	= (!empty($data['reservationId'])) ? $data['reservationId'] : (int) $this->getState('reservation.id');
		$isNew = empty($id);

		$reservationDetails = $this->getReservationDetails($data);
		//dmp($reservationDetails);
		//exit;
		require_once JPATH_COMPONENT_SITE .'/models/confirmation.php';
		$confirmationModel = new JHotelReservationModelConfirmation();
		$reservaitonId= $confirmationModel->saveConfirmation($reservationDetails);

		if($isNew && $reservaitonId!=-1){
			$reservationDetails->confirmation_id = $reservaitonId;
			$processor = PaymentService::createPaymentProcessor(PROCESSOR_CASH);
			$paymentDetails = $processor->processTransaction($reservationDetails);
			PaymentService::addPayment($paymentDetails);
		}
		
		if($reservaitonId!=-1){
			$this->setState('reservation.id', $reservaitonId);
			
		}else{
			$this->setState('reservation.id', $id);
			$this->setError($confirmationModel->getError());
			return false;
		}
			
		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	function getReservationDetails($data){
		
		$userData = $this->populateReservationDetails($data);
		//dmp($userData);
		$reservationData = new stdClass;
		$reservationData->userData = $userData;
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = HotelService::getHotel($userData->hotelId);
		
		UserDataService::setReservedItems($userData->reservedItems);
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->generateReservationSummary($reservationData, false);
		
		$reservationDetails->reservationData = $reservationData;
		UserDataService::setReservationDetails($reservationDetails);
		
		return $reservationDetails;
	}
	
	public function populateReservationDetails($data){
		$userData = new stdClass();
		$userData->confirmation_id = $data["reservationId"];
		//dmp($data["roomdetails"]);
		$guestInfo = $this->getNumberOfGuests($data["roomdetails"]);
		$userData->roomGuests = $guestInfo->adults;
		//dmp($userData->roomGuests);
		
		
		$userData->total_adults = 0;
		if(isset($userData->roomGuests) && count($userData->roomGuests)>=1){
			foreach($userData->roomGuests as $guestPerRoom){
				$userData->total_adults+= $guestPerRoom;
			}
		}
		
		$userData->adults = $userData->total_adults;
		$userData->children =0;
		
		$userData->first_name = $data["first_name"];
		$userData->last_name = $data["last_name"];
		$userData->address	= $data["address"];
		$userData->city	= $data["city"];
		$userData->state_name	= $data["state_name"];
		$userData->country	= $data["country"];
		$userData->postal_code= $data["postal_code"];
		$userData->phone = $data["phone"];
		$userData->email= $data["email"];
		$userData->company_name=$data["company_name"];
		$userData->guest_type = isset($data["guest_type"])?$data["guest_type"]:0;
		$userData->discount_code =$data["discount_code"];
		$userData->reservedItems = $data["reservedItem"];
		$userData->hotelId = $data["hotelId"];
		$userData->totalPaid = $data["totalPaid"];

		$userData->voucher=$data["voucher"];
		$userData->remarks=$data["remarks"];
		$userData->remarks_admin=$data["remarks_admin"];
		
		$userData->start_date=JHotelUtil::convertToMysqlFormat($data["start_date"]);
		$userData->end_date=JHotelUtil::convertToMysqlFormat($data["end_date"]);
		$hotel = HotelService::getHotel($userData->hotelId);
		$userData->currency=HotelService::getHotelCurrency($hotel); 
		
		$userData->arrival_time = $data["arrival_time"];
		
		$userData->rooms = count($data["roomdetails"]);
		if($data["update_price_type"]==2 || empty ($data["update_price_type"])){
			$userData->roomCustomPrices = $this->prepareCustomPrices($userData->reservedItems, $data["roomdetails"], $userData->start_date);
		}
		
		if(!empty($data["extraOptionIds"])){

			$extraOptions = array();
			if(isset($data["extraOptionIds"])){
				foreach($data["extraOptionIds"] as $key=>$value){
					$extraOption = explode("|",$value);
			
					if($extraOption[5]>0 || $extraOption[6]>0)
						continue;
					if(isset($data["extra-option-days-".$extraOption[3]])){
						$extraOption[6] = $data["extra-option-days-".$extraOption[3]];
					}
					if(isset($data["extra-option-persons-".$extraOption[3]])){
						$extraOption[5] = $data["extra-option-persons-".$extraOption[3]];
					}
						
					$extraOptions[$key] = implode("|",$extraOption);
				}
			}
			$userData->extraOptionIds = $extraOptions;		
		}
	
		$guestDetails = array();
		if(isset($data["guest_first_name"]))
		for($i=0;$i<count($data["guest_first_name"]);$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name = $data["guest_first_name"][$i];
			$guestDetail->last_name = $data["guest_last_name"][$i];
			$guestDetail->identification_number= $data["guest_identification_number"][$i];
			$guestDetails[] = $guestDetail;
		}
		
		$userData->guestDetails = $guestDetails;
		
		
		return $userData;
	}

	function prepareCustomPrices($reservedItems, $roomdetails, $startDate){
		$result = array();
		foreach($reservedItems as $reservedItem){
			$prices = $roomdetails[$reservedItem]["price"];
			
			$d = strtotime($startDate);
			foreach($prices as $price){
				$date = date("Y-m-d", $d);
				$result[]=$reservedItem."|".$date."|".$price;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
		}
		//dmp("custom prices");
		//dmp($result);
		
		return $result;
	}
	
	function getNumberOfGuests($roomdetails){
		$result = new stdClass();
		$result->adults=array();
		$result->children=array();
		
		foreach($roomdetails as $roomdetail){
			$result->adults[]= $roomdetail["adults"];
			if(isset($roomdetail["children"])){
				$result->children[]= $roomdetail["children"];
			}
		}
		
		//dmp($result);
		return $result;
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);
	
		// Get a group row instance.
		$table = $this->getTable("Confirmations");
	
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId)
		{
	
			if (!$table->delete($itemId))
			{
				$this->setError($table->getError());
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}
	
	public function setStatus(){
		$reservationId = $this->getState('reservation.id');
		$status = $this->getState('reservation.status');
		

		$table = $this->getTable("Confirmations");
		$table->setStatus($reservationId, $status);
		
		if($status==CANCELED_ID)
			$this->sendCancellationEmail($reservationId);
	}
	
	public function setPaymentStatus($reservationId, $paymentStatusId){
		$table = $this->getTable("ConfirmationsPayments");
		return $table->setPaymentStatus($reservationId, $paymentStatusId);
	}
	
	function getRoomTypesOptions(){
		$options = array();
		
		$hotelId = $this->getState('reservation.hotel_id');
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
	
	function getRoomHtmlContent($room, $startDate, $endDate){
		ob_start();
		?><fieldset class="roomrate" id="<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>">
			<legend>
				<?php echo (isset( $room->offer_name)?$room->offer_name." - ":"") ?> <?php echo $room->room_name ?>  &nbsp; <span
							onclick="removeRoom('<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>')" class="removeroom">[ <?php echo JText::_('LNG_DELETE',true)?> ]</span>
			</legend>
			<div>
				<input type="hidden" name="reservedItem[]" value="<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>" />
				<div class="persons">
					<?php echo JText::_('LNG_ADULTS',true)?>: 
					<select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][adults]" id="room[<?php echo $room->room_id?>][adults]">
						<?php for($i=1; $i<=$room->max_adults;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->adults ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					<div style="display:none">
						
					 <?php echo JText::_('LNG_JUNIORS',true)?>: 
					 <select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][children]">
					 	<?php for($i=1; $i<=$room->max_children;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->children ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select> 
					</div>
				</div>
				<div class="nights">
					<ul>
						<?php 
						
						for( $d = strtotime($startDate);$d < strtotime($endDate); ){
							$dayString = date( 'Y-m-d', $d);
							$price = $room->daily[$dayString]["price_final"];
							if(isset($room->customPrices) && isset($room->customPrices[$dayString])){
								$price = $room->customPrices[$dayString];
							}
						?>
						<li>
							<?php echo JText::_('LNG_PRICE',true)." - ".$dayString?>: <input type="text"	name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][price][<?php echo $dayString?>]" id="room_price_<?php echo $room->id?>_<?php echo $dayString?>" value="<?php echo $price?>">
							( <?php echo number_format($room->daily[$dayString]["price_final"],2) ?> )
						</li>
						<?php 
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						} 
						?>
					</ul>
				</div>
			</div>
		</fieldset><?php 
		
		$buff = ob_get_contents();
		$buff = htmlspecialchars($buff);
		ob_end_clean();
		
		return $buff;
	}
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->_hotels ))
		{
			$query = ' SELECT
			h.*,
			c.country_name
			FROM #__hotelreservation_hotels 			h
			LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
			ORDER BY hotel_name, country_name ';
			//$this->_db->setQuery( $query );
			$this->_hotels = $this->_getList( $query );
		}
		return $this->_hotels;
	}
	
	function sendEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		return EmailService::sendConfirmationEmail($reservationDetails);
	}
	
	function sendCancellationEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		return EmailService::sendCancelationEmail($reservationDetails);
	}
	
	
	function secretizeCard($reservationId){
		$creditCard = JRequest::getVar("card_number");
		if(isset($creditCard)){
			$creditCard = JHotelUtil::getInstance()->secretizeCreditCard($creditCard);
			$table = $this->getTable("ConfirmationsPayments");
			$table ->secretizeCard($reservationId,$creditCard);
		}
		return true;
	}
}
