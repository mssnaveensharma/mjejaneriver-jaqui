<?php
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model'); 

class JHotelReservationModelManageRoomDiscounts extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array = 	JRequest::getVar('discount_id',  0, '', 'array');
		$h		= 	JRequest::getVar('hotel_id',  0, '');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
		$this->setHotelId((int)$h);
	}
	function setId($discount_id)
	{
		// Set id and wipe data
		$this->discount_id		= $discount_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}

	function setHotelId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}
	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function &getDatas()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT d.*, GROUP_CONCAT( r.room_name ORDER BY r.room_name ) AS discount_rooms 
							FROM #__hotelreservation_discounts 		d'.
						' LEFT JOIN #__hotelreservation_rooms				r ON FIND_IN_SET(r.room_id, d.discount_room_ids)'.
						' WHERE d.hotel_id='.$this->_hotel_id.
						' GROUP BY discount_id';
			//$this->_db->setQuery( $query );
			//dmp($query);
			$this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
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
	
	function &getHotel()
	{
		$query = 	' SELECT 	
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels				h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						'.
					' WHERE 
						hotel_id = '.$this->_hotel_id;
		
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}
	
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT d.*, GROUP_CONCAT( r.room_name ORDER BY r.room_name ) AS discount_rooms FROM #__hotelreservation_discounts d'.
						' LEFT JOIN #__hotelreservation_rooms				r ON FIND_IN_SET(r.room_id, d.discount_room_ids)'.
						' WHERE discount_id = '.$this->discount_id.
						' GROUP BY discount_id';
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
					
			
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->discount_id 	= null;
			$this->_data->hotel_id 			= null;
			$this->_data->discount_name		= null;			
			$this->_data->discount_room_ids	= null;			
			$this->_data->discount_datas	= gmdate('Y-m-d');	
			$this->_data->discount_datae	= gmdate('Y-m-d');		
			$this->_data->discount_value	= null;		
			$this->_data->percent = 0;
			$this->_data->is_available		= null;
			$this->_data->minimum_number_persons = null;
			$this->_data->minimum_number_days = null;
			$this->_data->minimum_amount = null;
			$this->_data->maximum_number_days = null;
			$this->_data->code = null;
			$this->_data->offer_ids = null;
			$this->_data->excursion_ids = null;
			$this->_data->only_on_offers = 0;
			$this->_data->price_type = 0;
			$this->_data->check_full_code = 0;
				
		}
		
		$query = " 	SELECT 
						r.room_id,
						r.room_name	,
						IF( ISNULL(d.discount_id), 0, 1)		AS is_sel							
					FROM #__hotelreservation_rooms r
					LEFT JOIN 
					( 	
						SELECT * FROM #__hotelreservation_discounts WHERE discount_id = ".$this->discount_id. " 
					) d ON FIND_IN_SET(r.room_id, d.discount_room_ids) 
					WHERE r.is_available = 1 AND r.hotel_id  = ".$this->_hotel_id."
					";
		// dmp($query);
		$this->_data->itemRooms = $this->_getList( $query );
		
		$query = "	
		 		select  o.offer_id, o.offer_name	 
		 		from #__hotelreservation_rooms r
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
				where FIND_IN_SET(r.room_id,
				(
				 SELECT discount_room_ids FROM #__hotelreservation_discounts WHERE discount_id = $this->discount_id
				))
			";
		
		$this->_data->offers = $this->_getList( $query );
		
		$selectedOffers = $this->_data->offer_ids;
		//dmp($selectedOffers);
		if(isset($selectedOffers)){
			$selectedOffers = explode(",",$selectedOffers);
			foreach($this->_data->offers as &$offer){
				$offer->is_sel = 0;
				if(in_array($offer->offer_id, $selectedOffers)){
					$offer->is_sel = 1;
				}
			}
		}
		
		
		$query = " 	SELECT 
						r.id,
						r.name as excursion_name	,
						IF( ISNULL(d.discount_id), 0, 1)		AS is_sel							
					FROM #__hotelreservation_excursions r
					LEFT JOIN 
					( 	
						SELECT * FROM #__hotelreservation_discounts WHERE discount_id = ".$this->discount_id. " 
					) d ON FIND_IN_SET(r.id, d.excursion_ids) 
					WHERE r.is_available = 1 AND r.hotel_id  = ".$this->_hotel_id."
					";
		$this->_data->excursions = $this->_getList( $query );
		
		//dmp($this->_db->getErrorMsg());
		//exit;
		$this->_data->discount_datas	= JHotelUtil::convertToFormat($this->_data->discount_datas);
		$this->_data->discount_datae	= JHotelUtil::convertToFormat($this->_data->discount_datae);
		
		return $this->_data;
	}

	function store($data)
	{	
		$row = $this->getTable();

		$data['discount_datas']= JHotelUtil::convertToMysqlFormat($data['discount_datas']);
		$data['discount_datae']= JHotelUtil::convertToMysqlFormat($data['discount_datae']);
		
		
		// Bind the form fields to the table
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'discount_id', array(0), 'post', 'array' );
		
		
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations											c
					WHERE 
						c.discount_code IN (".implode(',', $cids).") AND c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
						
		$checked_records = $this->_getList( $query );
	
		if ( count($checked_records) > 0 ) 
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_DISCOUNT_REMOVE',true) );
			return false;
		}
		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					$msg = JText::_('LNG_ERROR_DELETE_DISCOUNT',true);
					return false;
				}
			}
		}
		return true;
	}
	
	function state()
	{
		$query = 	" UPDATE #__hotelreservation_discounts SET is_available = IF(is_available, 0, 1) WHERE discount_id = ".$this->discount_id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
			
		}
		return true;
	}


	function getHTMLContentOffers($roomIds, $offerIds)
	{
		//dmp($offerIds);
		
		$roomIds = implode(",", $roomIds);
		
		$query = "	
		 		select  o.offer_id, o.offer_name 
		 		from #__hotelreservation_rooms r
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
				where r.room_id in ($roomIds)
			";
		
		$offers = $this->_getList( $query );
		
		$offerIds = explode (',',$offerIds[0]);
		
		ob_start();
		?>
		<select id="offer_ids" multiple="multiple" name="offer_ids[]">
			<option value=""><?php echo JText::_('LNG_SELECT_OFFERS',true); ?></option>
			<?php
			foreach( $offers as $offer )
			{
				
				?>
				<option <?php echo in_array($offer->offer_id,$offerIds)? 'selected="selected"' : ''?> 	value='<?php echo $offer->offer_id?>'><?php echo $offer->offer_name ?></option>
				<?php
			}
			?>
		</select>
		<?php 
		$buff = ob_get_contents();
		ob_end_clean();
		
		return htmlspecialchars($buff);
	}


}
?>