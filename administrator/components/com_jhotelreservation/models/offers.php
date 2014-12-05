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
jimport('joomla.application.component.modellist');

class JHotelReservationModelOffers extends JModelList
{ 
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'offer_id', 'r.id',
					'title', 'a.title',
					'menutype', 'a.menutype',
			);
		}
		parent::__construct($config);
	}
	
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 0, 'int');
		$this->setState('filter.type', $type);
	
		$hotel_id = JRequest::getVar('hotel_id', null);
		if ($hotel_id) {
			if ($hotel_id != $app->getUserState($this->context.'.filter.hotel_id')) {
				$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
				JRequest::setVar('limitstart', 0);
			}
		}
		else {
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
	
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		$app->setUserState('com_jhotelreservation.rooms.filter.hotel_id', $hotel_id);
		$this->setState('filter.hotel_id', $hotel_id);
		// List state information.
		parent::populateState('ho.offer_order', 'asc');
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
	
	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
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
		$query->select($this->getState('list.select', 'ho.*'));
		$query->from($db->quoteName('#__hotelreservation_offers').' AS ho');
	
		// Join over currency
		$query->select('GROUP_CONCAT(hov.voucher SEPARATOR ", ") as vouchers');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_offers_vouchers').' AS hov ON hov.offerId=ho.offer_id');
	
		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('ho.is_available = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(ho.is_available IN (0, 1))');
		}
	
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('ho.hotel_id = '.$db->quote($hotelId));
		}
	
		$query->group('ho.offer_id');
	
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'ho.offer_order')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function getOfferContent(){
		
		$offerId = JRequest::getVar('offer_id');
		$value = $this->getTable("ManageOffers");
		$value->load($offerId);
		
		$content_info = "<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_CODE',true)." :</B></TD><TD><B>".$value->offer_code."</B></TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_NAME',true)." :</B></TD><TD><B>".$value->offer_name."</B></TD></TR>";
		if( $value->offer_reservation_cost_val !=0 )
			$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_VALUE',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_val."</B></TD></TR>";
		if( $value->offer_reservation_cost_proc !=0 )
			$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_PERCENT',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_proc." %</B></TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_DESCRIPTION',true)." :</B></TD><TD>".$value->offer_description."</TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_PERIOD',true)." :</B></TD><TD>".$value->offer_datas." <> ".$value->offer_datae."</TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_DISPLAY_ON_FRONT',true)." :</B></TD><TD>".($value->offer_datasf !='0000-00-00'?$value->offer_datasf:'&nbsp;')." <> ".($value->offer_dataef !='0000-00-00'?$value->offer_dataef:'&nbsp;')."</TD></TR>";
		
		$query = "
							SELECT
								r.room_id,
								r.room_name
		
							FROM #__hotelreservation_rooms	r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
							WHERE ord.offer_id = ".$value->offer_id."
							GROUP BY r.room_id
							ORDER BY room_name
				";
		
		$room = $this->_getList( $query );
		if( isset($room) )
		{
			$old_room_id 	= 0;
			foreach( $room as $value_room )
			{
				$content_info 	.= "<TR><TD nowrap align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD><TD>";
					
				$query = "
									SELECT
										ord.*
									FROM #__hotelreservation_rooms	r
									INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
									WHERE
										ord.offer_id = ".$value->offer_id."
										AND
										r.room_id = ".$value_room->room_id."
									ORDER BY room_name
						";
				$room_discount = $this->_getList( $query );
				$old_room_id =$value_room->room_id;
		
				// $content_info .= "	</TABLE>";
				$content_info .= "<HR></TD></TR>";
			}
		}
		
		$content_info .= "</TABLE>";
		
 		return $content_info;
		//warning info
		
	}
	
	function getWarningContent(){
		
		$offerId = JRequest::getVar('offer_id');
		$warning_info = "";
		$query = "
							SELECT
								r.room_id,
								r.room_name,
								o.offer_datas,
								o.offer_datae
							FROM #__hotelreservation_rooms				 				r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		ON r.room_id = ord.room_id
							INNER JOIN #__hotelreservation_offers						o		ON o.offer_id = ord.offer_id
							WHERE ord.offer_id = ".$offerId."
							GROUP BY r.room_id
							ORDER BY room_name
				";
		// dmp($query);
		$room = $this->_getList( $query );
		if( isset($room) )
		{
			$old_room_id 	= 0;
			foreach( $room as $value_room )
			{
				$answ = array();
				$is_error_period 		= false;
				$is_error_ignored_days 	= false;
				if(
				$value_room->offer_datas 	!= '0000-00-00'
						)
				{
					$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datas." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_START',true)." )";
				}
		
				if(
				$value_room->offer_datae 	!= '0000-00-00'
						)
		
				{
					$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datae." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_END',true)." )";
				}
		
				$nr_a = 1;
				foreach( $answ as $a )
				{
					$warning_info 	.= "<TR>";
					if( $nr_a == 1 )
						$warning_info 	.= "<TD rowspan = ".count($answ)." nowrap align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD>";
					$warning_info 	.= "	<TD width=90%>$a</TD>";
					if( $nr_a == 1 )
						$warning_info 	.= "<TD rowspan = ".count($answ)." nowrap align=center valign=middle width=30%>".
						"<a href='index.php?option=com_jhotelreservation&view=rooms'>".JText::_('LNG_MORE_DETAILS_ROOM_SETTINGS')."</a>".
						"</TD>";
					$warning_info 	.= "</TR>";
					$nr_a ++;
				}
				if( count($answ) > 0 )
				{
					$warning_info .= "<TR><td colspan=3><HR></TD></TR>";
				}
				$old_room_id =$value_room->room_id;
			}
		}
		if( strlen($warning_info) > 0 )
		{
			$warning_info = "	<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>".
					$warning_info.
					"</TABLE>";
		}
		return $warning_info;
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

	
	

	
	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations					c
					INNER JOIN #__hotelreservation_confirmations_rooms		r USING( confirmation_id )
					WHERE 
						r.offer_id IN (".implode(',', $cids).") 
						AND
						c.hotel_id IN (".$this->getState('filter.hotel_id').") 
						AND 
						c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
						
		$checked_records = $this->_getList( $query );
		if ( count($checked_records) > 0 ) 
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_OFFER_REMOVE',true) );
			return false;
		}

		$row = $this->getTable('ManageOffers');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					$msg = JText::_( 'LNG_OFFER_ERROR_DELETE' ,true);
					return false;
				}
			}
		}
		return true;

	}
	
	
	
	function state()
	{
		$cids = JRequest::getVar( 'cid', array(0));
		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = 	" UPDATE #__hotelreservation_offers SET is_available = IF(is_available, 0, 1) WHERE offer_id = ".$cid ." AND hotel_id = ".$this->getState('filter.hotel_id');;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					//JError::raiseWarning( 500, "Error change Room state !" );
					return false;
					
				}
				return true;
			}
		}
	}
	
	function changeFeaturedState()
	{
		$offerId = JRequest::getVar('offer_id');
		$query = 	" UPDATE #__hotelreservation_offers SET featured = IF(featured, 0, 1) WHERE offer_id = ".$offerId;
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changeTopState()
	{
		$offerId = JRequest::getVar('offer_id',0);
		$query = 	" UPDATE #__hotelreservation_offers SET top = IF(top, 0, 1) WHERE offer_id = ".$offerId;
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	

	function getLastOrderNumber($hotelId){
		$query = "select from max(offer_order) as offer_order from  #__hotelreservation_offers WHERE  hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		$offer = $this->_db->loadObject();
		return $offer->offer_order;
	}
	
	function getLastOrder($offerId)
	{
		$offer_id = 0;
		if( isset($offerId) )
			$offer_id = $offerId;
		$increment = 0;
		if( $offer_id > 0 ){
			$query = 	" SELECT * FROM #__hotelreservation_offers  WHERE offer_id = ".$offer_id;
		} else {
			$query = 	" SELECT * FROM #__hotelreservation_offers  ORDER BY offer_order DESC LIMIT 1 ";
			$increment++;
		}
		
		$db 	= JFactory::getDBO();
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
	
		if(!isset($row ))
			return 1;
	
		return ($row->offer_order+$increment);
	}

	function getExtraOptions(){
		$query = "select * from  #__hotelreservation_extra_options WHERE  hotel_id = ". $this->_hotel_id;
		$this->_db->setQuery( $query );
		$extraOptions = $this->_db->loadObjectList();
		return $extraOptions;
	}
	
	function changeOfferOrder(){

		$tip_order 	= '';
		$offer_id 	= 0;
		if( isset( $_GET['tip_order'] ) )
			$tip_order = $_GET['tip_order'];
		if( isset( $_GET['offer_id'] ) )
			$offer_id = $_GET['offer_id'];
		if( isset( $_GET['hotel_id'] ) )
			$hotel_id = $_GET['hotel_id'];
		
		$ret	= true;
		$up	 	= false;
		$down	= false;
		$e		= '';
		$p		= -1;
		$id_alter = 0;
		if( $tip_order == '' || $offer_id == 0  || $offer_id =='' )
		{
			$ret = false;
			$e = 'Invalid params';
		}
		if( $ret == true )
		{
			$db = JFactory::getDBO();
		
			$db->setQuery( "
					(
					SELECT
					*,
					'up'	AS type_order
					FROM #__hotelreservation_offers
					WHERE
					offer_order <= ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id AND hotel_id = $hotel_id)
					AND
					offer_id <> $offer_id
					AND 
					hotel_id = $hotel_id
					LIMIT 1
			)
					UNION ALL
					(
					SELECT
					*,
					'crt'	AS type_order
					FROM #__hotelreservation_offers
					WHERE offer_order = ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id AND hotel_id = $hotel_id)
					AND hotel_id = $hotel_id
					LIMIT 1
			)
					UNION ALL
					(
					SELECT
					*,
					'down'	AS type_order
					FROM #__hotelreservation_offers
					WHERE
					offer_order >= ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id AND hotel_id = $hotel_id)
					AND
					offer_id <> $offer_id
					AND 
					hotel_id = $hotel_id
					LIMIT 1
			)
						
					" );
					//dmp($db);
			$rows 			= $db->loadObjectList();
			$row_up			= null;
			$row_crt		= null;
			$row_down		= null;
			
			
			foreach( $rows as $value )
			{
				switch( $value->type_order )
				{
					case 'up':
						$row_up = $value;
						break;
					case 'crt':
						$row_crt = $value;
						break;
					case 'down':
						$row_down = $value;
						break;
					}
				}
				$db->setQuery( " START TRANSACTION ");

				if (!$db->query() )
				{
					$ret = false;
					$e = ' sql STATEMENT error !';
				}
				switch( $tip_order )
					{
					case 'up':
						if( $row_up != null && $row_crt != null )
						{
							$id_alter = $row_up->offer_id;
							$db->setQuery( " UPDATE  #__hotelreservation_offers  SET offer_order = ".$row_up->offer_order." WHERE offer_id=".$row_crt->offer_id );
							if (!$db->query() )
							{
								$ret = false;
								$e = 'UPDATE sql STATEMENT error !';
							}
							$db->setQuery( " UPDATE  #__hotelreservation_offers  SET offer_order = ".$row_crt->offer_order." WHERE offer_id=".$row_up->offer_id );
	
							if (!$db->query() )
							{
									$ret = false;
									$e = 'UPDATE sql STATEMENT error !';
							}
						}
						else
							$ret = false;
						
						break;
					case 'down':
						
						if( $row_down != null && $row_crt != null )
						{
							$id_alter = $row_down->offer_id;
							$db->setQuery( " UPDATE  #__hotelreservation_offers  SET offer_order = ".$row_down->offer_order." WHERE offer_id=".$row_crt->offer_id );
							if (!$db->query() )
								{
								$ret = false;
								$e = 'UPDATE sql STATEMENT error !';
								}
			
							$db->setQuery( " UPDATE  #__hotelreservation_offers  SET offer_order = ".$row_crt->offer_order." WHERE offer_id=".$row_down->offer_id );
							if (!$db->query() )
							{
								$ret = false;
								$e = 'UPDATE sql STATEMENT error !';
							}
		
						}
							else
								$ret = false;
							break;
				
					}
					if( $ret == true )
						{
							$db->setQuery( " COMMIT ");
							if (!$db->query() )
							{
								$ret = false;
								$e = ' sql STATEMENT error !';
							}
							//check results
							$db->setQuery( "
									(
									SELECT
									*,
									'up'	AS type_order
									FROM #__hotelreservation_offers
									WHERE
									offer_order <= ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id )
									AND
									offer_id <> $offer_id
									ORDER BY offer_order DESC
									LIMIT 1
									)
									UNION ALL
									(
									SELECT
									*,
									'crt'	AS type_order
									FROM #__hotelreservation_offers
									WHERE offer_order = ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id )
									ORDER BY offer_order
									LIMIT 1
									)
									UNION ALL
									(
									SELECT
									*,
									'down'	AS type_order
									FROM #__hotelreservation_offers
									WHERE
									offer_order >= ( SELECT offer_order FROM #__hotelreservation_offers WHERE offer_id = $offer_id )
									AND
									offer_id <> $offer_id
									ORDER BY offer_order
									LIMIT 1
									)
							" );
							//dmp($db);
							$rows 			= $db->loadObjectList();
							$row_up			= null;
							$row_crt		= null;
							$row_down		= null;
							foreach( $rows as $value )
							{
								switch( $value->type_order )
								{
								case 'up':
									$up = true;
									break;
								case 'crt':
									break;
								case 'down':
									$down = true;
									break;
								}
							}
							//check results
					}
					else
						{
							$db->setQuery( " ROLLBACK ");
							if (!$db->query() )
							{
								$ret = false;
								$e = ' sql STATEMENT error !';
							}
						}
	
		}
		ob_clean();
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<offer_order>';
		echo '<answer up="'.($up? "1" : "0").'" down="'.($down? "1" : "0").'" error="'.($ret ? "0" : "1").'" info="'.$e.'" p="'.$id_alter.'" />';
		echo '</offer_order>';
		echo '</xml>';
		exit;
	}
}
?>