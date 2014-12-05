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


//echo JPATH_COMPONENT_SITE.DS.'models'.DS.'confirmations.php';
class JHotelReservationModelManageReservations extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('confirmation_id',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
		
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		if(!$limit){
			$limit = 50;
		}
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->_total = 0;		
	}
	
	function setId($confirmation_id)
	{
		// Set id and wipe data
		$this->_confirmation_id		= $confirmation_id;
		$this->_data		= null;
	}

	function &getAppSettings()
	{
		// Load the data

		$query = ' SELECT * FROM #__hotelreservation_applicationsettings ';
		$this->_db->setQuery( $query );
		$appSettings = $this->_db->loadObject();
		
		return $appSettings;
	}

	
	function setTotal($filter_hotel_id,$filter_first_name, $filter_last_name, $filter_status_reservation, $filter_room_types, $filter_voucher)
	{
		
		$paymentFilter =' where 1';
			if(strcmp($filter_status_reservation,PAYMENT_STATUS_PENDING)==0){
				$filter_status_reservation == 1;	
				$paymentFilter =" inner join #__hotelreservation_confirmations_payments cp on c.confirmation_id= cp.confirmation_id where cp.payment_status = '".PAYMENT_STATUS_PENDING."' ";
			} else if(strcmp($filter_status_reservation,PAYMENT_STATUS_WAITING)==0){
				$filter_status_reservation == 1;
				$paymentFilter =" inner join #__hotelreservation_confirmations_payments cp on c.confirmation_id= cp.confirmation_id where cp.payment_status = '".PAYMENT_STATUS_WAITING."' ";
			}
			
		$query = "
								SELECT 
									count(*)  
									
								FROM #__hotelreservation_confirmations c 
								INNER JOIN #__hotelreservation_status_reservation				s	USING(reservation_status)
								INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
								LEFT JOIN 
								(
									SELECT 
										p.*
									FROM #__hotelreservation_paymentsettings				s
									INNER JOIN #__hotelreservation_confirmations_payments	p	USING(payment_id)
									WHERE p.payment_type_id = ".PENALTY_PAYMENT_ID."
									
								) 																p 	USING(confirmation_id)
								LEFT JOIN #__hotelreservation_confirmations_feature_options 	fo	ON ( fo.confirmation_id 		= c.confirmation_id )
								LEFT JOIN #__hotelreservation_confirmations_rooms_packages 		pk	ON ( pk.confirmation_id 		= c.confirmation_id )	
								LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 				= r.hotel_id )		
								LEFT JOIN #__hotelreservation_confirmations_rooms_arrival_options 	ao	ON ( ao.confirmation_id 		= c.confirmation_id )
								LEFT JOIN #__hotelreservation_confirmations_rooms_airport_transfer 	at	ON ( at.confirmation_id 		= c.confirmation_id )						
									
								$paymentFilter ".
		(strlen($filter_first_name) > 0 ? " AND c.first_name LIKE '%".$filter_first_name."%'" 				: "").
		(strlen($filter_last_name) > 0 	? " AND c.last_name LIKE '%".$filter_last_name."%'" 				: "").
		(strlen($filter_voucher) > 0 ? " AND c.voucher = '".$filter_voucher."'" 				: "").
		($filter_status_reservation > 0 ? " AND s.reservation_status  = ".$filter_status_reservation 	: "").
		($filter_hotel_id > 0 			? " AND r.hotel_id  = ".$filter_hotel_id 							: "").
		($filter_room_types > 0 		? " AND r.room_id  = ".$filter_room_types 							: "")
		." GROUP BY c.confirmation_id "	;
	
		//dmp($query);
		
		$this->_db->setQuery($query);
		if(!$this->_db->query())
			dmp($this->_db->getErrorMsg());
		$this->_total =$this->_db->getNumRows();
		//dmp($this->_total);
	}	
	
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');

			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function &getPaymentProcessors( )
	{
		// Load the data
		$paymentsprocessors = array();
		
		$query = " SELECT * 
						FROM #__hotelreservation_paymentprocessors WHERE is_available = 1 ORDER BY paymentprocessor_name
					";
		$paymentsprocessors = $this->_getList( $query );
			
		
		
		return $paymentsprocessors;
	}

	
	/**
	 * Method to get 
	 * @return object with data
	 */
	function &getReservations( $filter_hotel_id, $filter_first_name, $filter_last_name, $filter_status_reservation, $filter_room_types, $filter_voucher )
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$paymentFilter =' where 1';
			if(strcmp($filter_status_reservation,PAYMENT_STATUS_PENDING)==0){
				$filter_status_reservation == 1;	
				$paymentFilter =" inner join #__hotelreservation_confirmations_payments cp on c.confirmation_id= cp.confirmation_id where cp.payment_status = '".PAYMENT_STATUS_PENDING."' ";
			} else if(strcmp($filter_status_reservation,PAYMENT_STATUS_WAITING)==0){
				$filter_status_reservation == 1;
				$paymentFilter =" inner join #__hotelreservation_confirmations_payments cp on c.confirmation_id= cp.confirmation_id where cp.payment_status = '".PAYMENT_STATUS_WAITING."' ";
			}
			
			$query = " 
						SELECT 
							c.*,  
							s.status_reservation_name,
							s.bkcolor,
							s.color,
							s.is_modif,
							at.airport_transfer_type_id,
							at.airline_id,
							at.airport_transfer_flight_nr,
							at.airport_transfer_flight_nr,
							at.airport_transfer_date,
							at.airport_transfer_time_hour,
							at.airport_transfer_time_min,
							at.airport_transfer_guest,
							IFNULL(p.payment_percent,0)				AS payment_penalty_percent, 
							IFNULL(p.payment_value,0)				AS payment_penalty_value,
							GROUP_CONCAT( DISTINCT r.room_id)		AS room_ids,
							GROUP_CONCAT( DISTINCT fo.option_id)	AS option_ids,
							GROUP_CONCAT( DISTINCT pk.package_id)	AS package_ids,
							GROUP_CONCAT( DISTINCT ao.arrival_option_id)	AS arrival_option_ids,
							h.hotel_name							AS hotel_name
						FROM #__hotelreservation_confirmations c 
						INNER JOIN #__hotelreservation_status_reservation				s	USING(reservation_status)
						INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
						LEFT JOIN 
						(
							SELECT 
								p.*
							FROM #__hotelreservation_paymentsettings				s
							INNER JOIN #__hotelreservation_confirmations_payments	p	USING(payment_id)
							WHERE p.payment_type_id = ".PENALTY_PAYMENT_ID."
						) 																p 	USING(confirmation_id)
						LEFT JOIN #__hotelreservation_confirmations_feature_options 	fo	ON ( fo.confirmation_id 			= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_rooms_packages 		pk	ON ( pk.confirmation_id 			= c.confirmation_id )	
						LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= r.hotel_id )		
						LEFT JOIN #__hotelreservation_confirmations_rooms_arrival_options 	ao	ON ( ao.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_rooms_airport_transfer 	at	ON ( at.confirmation_id 		= c.confirmation_id )
						$paymentFilter						
						 ".
					(strlen($filter_first_name) > 0 ? " AND c.first_name LIKE '%".$filter_first_name."%'" 				: "").
					(strlen($filter_last_name) > 0 	? " AND c.last_name LIKE '%".$filter_last_name."%'" 				: "").
					(strlen($filter_voucher) > 0 ? " AND c.voucher = '".$filter_voucher."'" 				: "").
					($filter_status_reservation > 0 ? " AND s.reservation_status  = ".$filter_status_reservation 	: "").
					($filter_hotel_id > 0 			? " AND r.hotel_id  = ".$filter_hotel_id 							: "").
					($filter_room_types > 0 		? " AND r.room_id  = ".$filter_room_types 							: "")
					."
					GROUP BY c.confirmation_id 
					ORDER BY c.confirmation_id DESC 
					"
			;
			//dmp($this->getState('limitstart'));
			//dmp($this->getState('limit'));
			//exit;
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			//dmp($this->_data);
			//exit;
			$this->setTotal($filter_hotel_id, $filter_first_name, $filter_last_name, $filter_status_reservation, $filter_room_types, $filter_voucher);
				
			foreach( $this->_data as $key => $value )
			{
				$query = 		" 	SELECT 
										p.payment_id											AS payment_id,
										p.payment_type_id										AS payment_type_id,
										p.paymentprocessor_id									AS paymentprocessor_id,
										pp.paymentprocessor_type								AS paymentprocessor_type,
										p.payment_percent										AS payment_percent,
										p.payment_value											AS payment_value,
										p.payment_explication									AS payment_explication,
										p.payment_status										AS payment_status,
										p.confirmation_id									
									FROM #__hotelreservation_confirmations_payments				p
									LEFT JOIN #__hotelreservation_paymentprocessors			pp USING(paymentprocessor_id)
									WHERE p.confirmation_id = ".$value->confirmation_id." 	
							";

							//$this->_db->setQuery( $query );
				
				
				$this->_data[ $key ]->itemPayments = $this->_getList( $query );
				// $query = 	" 	SELECT 
									// *
								// FROM #__hotelreservation_confirmations_rooms_packages_dates
								// WHERE confirmation_id = ".$value->confirmation_id." 	
								// ORDER BY package_data
							// ";
				// $res = $this->_getList( $query );
				// $this->_data[ $key ]->package_day = '';
				// foreach( $res as $v ) 
				// {
					// if( strlen($this->_data[ $key ]->package_day) > 0 )
						// $this->_data[ $key ]->package_day	.=',';
					// $this->_data[ $key ]->package_day	.= $v->package_id.'|'.$v->package_data;
				// }
				
				// if( $this->_data[ $key ]->room_ids == null )
					// $this->_data[ $key ]->room_ids = '';
				// if( $this->_data[ $key ]->option_ids == null )
					// $this->_data[ $key ]->option_ids = '';
				// if( $this->_data[ $key ]->package_ids == null )
					// $this->_data[ $key ]->package_ids = '';
				// if( $this->_data[ $key ]->package_day == null )
					// $this->_data[ $key ]->package_day = '';
				// if( $this->_data[ $key ]->arrival_option_ids == null )
					// $this->_data[ $key ]->arrival_option_ids = '';
				
				$this->_confirmation_id = $this->_data[ $key ]->confirmation_id;
				// $itemRoomsCapacity =  $this->getRoomsCapacity(); 
				//$itemRoomsDiscounts =  $this->getRoomsDiscounts();
				// $itemPackageNumbers =  $this->getPackageNumbers();		
				
				// $modelVariables = new JHotelReservationModelVariables($this->hotel_id);
				// $this->_data[ $key ]->itemRoomsCapacity 	= $modelVariables->getStringRoomsCapacity($itemRoomsCapacity);
				// $this->_data[ $key ]->itemPackageNumbers	= $modelVariables->getStringPackageNumbers($itemPackageNumbers);
				// $this->_data[ $key ]->itemPayments			= $modelVariables->getConfirmationPayments();
			
			}
			$this->_confirmation_id = 0;
		}
		
		return $this->_data;
	}
	
	function getPackageNumbers()
	{
		$query = 	" SELECT 
							p.*,
							pk.package_number	AS nr_max
						FROM #__hotelreservation_confirmations_rooms_packages 				p
						LEFT JOIN #__hotelreservation_packages			 					pk	USING(package_id)
					".
					" WHERE p.confirmation_id = ".$this->_confirmation_id.
					" GROUP BY p.confirmation_package_id ";
		
		$res = $this->_getList( $query );
		$arr = array();
		foreach( $res as $value )
		{
			$arr[$value->package_id] = array( $value->nr_max, $value->package_number);
		}
		// dmp($arr);
		return $arr;
	}
	
	function getRoomsCapacity()
	{
		$query = 	" SELECT 
							*
						FROM #__hotelreservation_confirmations_rooms 					r	
					".
					" WHERE r.confirmation_id = ".$this->_confirmation_id.
					" GROUP BY r.confirmation_room_id ";
		
		$res = $this->_getList( $query );
		$arr = array();
		foreach( $res as $value )
		{
			$arr[$value->room_id] = array( 0, $value->rooms);
		}
		
		return $arr;
	}
	
	function getRoomsDiscounts()
	{
		$query = 	" SELECT 
							*
						FROM #__hotelreservation_confirmations_rooms_discounts 					d	
					".
					" WHERE d.confirmation_id = ".$this->_confirmation_id.
					" GROUP BY d.confirmation_discount_id ";
		
		$arr = $this->_getList( $query );
		return $arr;
	}
	
	function &getHotels()
	{
		$query = 	" SELECT 
							*
						FROM #__hotelreservation_hotels 					
					WHERE  is_available = 1
					ORDER BY hotel_name ";
		// dmp($query);
		$arr = $this->_getList( $query );
		return $arr;
	}
	
	
	function &getStatusReservation()
	{
		// Load the data
		$query = ' SELECT * FROM #__hotelreservation_status_reservation ORDER BY `order` ';
		//echo $query;
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
	}
	
	
	function &getPaymentSettings()
	{
		// Load the data
		$query = ' SELECT * FROM #__hotelreservation_paymentsettings WHERE is_available = 1 ORDER BY payment_order ';

		//echo $query;
		//$this->_db->setQuery( $query );
		$res =  $this->_getList( $query );
		return $res;
	}

	
	function &getRoomTypes()
	{
		// Load the data
		$query = ' SELECT * FROM #__hotelreservation_rooms WHERE is_available = 1 ';
		//echo $query;
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
	}
	
	function checkStatusLate()
	{
		$query = 	" 	UPDATE #__hotelreservation_confirmations 
						SET 
							reservation_status = IF( datas < '".date('Y-m-d')."' AND reservation_status = ".RESERVED_ID." , ".LATE_ID.",  ".RESERVED_ID." )
						WHERE FIND_IN_SET(reservation_status, '".RESERVED_ID.", ".LATE_ID."')
					";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			
		} 
	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	" SELECT 
								c.*
							FROM #__hotelreservation_confirmations c
						".
						" WHERE c.confirmation_id = ".$this->_confirmation_id;
			$this->_db->setQuery( $query );
			// dmp($this->_db);
			
			$this->_data = $this->_db->loadObject();
			//dmp($this->_data);
			$modelVariables = new JHotelReservationModelVariables($this->_data->hotel_id);
			$modelVariables->edit_mode=1;
			JRequest::setVar( 'tip_oper',5);
			$post = JRequest::get( 'post' );
			$params = array();
			if(isset($post["room_guests"])){
				$params["room_guests"]= $post["room_guests"];
			}
			$modelVariables->load( $this->_data->confirmation_id, $this->_data->email, $modelVariables->itemCurrency, $params);
		
			$modelVariables->checkAvalability();
			
			return $modelVariables;
		}

		return $this->_data;
	}
	
	
	function &getFeatureOptions( )
	{
		// Load the data

		$query = "  SELECT * FROM #__hotelreservation_room_features ORDER BY feature_name  ";
		//$this->_db->setQuery( $query );
		$features = $this->_getList( $query );
		
		
		foreach( $features as $key => $feature )
		{
			$query = "  SELECT * FROM #__hotelreservation_room_feature_options WHERE feature_id= ".$feature->feature_id." ORDER BY option_name  ";
			//$this->_db->setQuery( $query );
			
			$features[ $key ]->options = $this->_getList( $query );
		}
		
		return $features;
	}
	
	function &getRooms( )
	{
		// Load the data

		$query = "  SELECT * FROM #__hotelreservation_rooms WHERE is_available ORDER BY room_name  ";
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
		
	}
	
	function &getPackages( )
	{
		// Load the data

		$query = "  SELECT * FROM #__hotelreservation_packages WHERE is_available ORDER BY package_name  ";
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
		
	}
	
	function store($data)
	{	
		$row = $this->getTable('confirmations');
		
		// Bind the form fields to the table
		
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store() ) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		
		$row = $this->getTable('confirmationsguests');
		
		// Bind the form fields to the table
		
		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Store the web link table to the database
		if (!$row->store() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'confirmation_id', array(0), 'post', 'array' );
		
		try
		{ 
			$db =JFactory::getDBO();
			$db->setQuery("START TRANSACTION");
			$db->query();

			$row = $this->getTable('confirmations');

			if (count( $cids )) {
				foreach($cids as $cid) 
				{
					if (!$row->delete( $cid )) {
						throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_feature_options WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_extra_options WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 			
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_packages WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_packages_dates WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_payments WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
					
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_nr_date_discs WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_numbers_dates WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_taxes WHERE confirmation_id = ".$this->_confirmation_id ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
				
		}
		catch( Exception $ex )
		{
			// dmp($ex);
			// exit;
			$db->setQuery("ROLLBACK");
			$db->query();
			return false;
		}

		$db->setQuery("COMMIT");
		$db->query();
		

		return true;

	}
	
	function status( $values )
	{
		$reservation_status 				= $values['reservation_status'];
		$payment_id							= $values['payment_id'];
		$payment_type_id					= $values['payment_type_id'];
		$payment_percent 					= $values['payment_percent'];
		$payment_explication 				= $values['payment_explication'];
		$confirmation_id 					= $values['confirmation_id'];
		$is_enable_payment 					= $values['is_enable_payment'];
		$tip_confirmation_payment_status	= $values['tip_confirmation_payment_status'];
		$total								= isset($values['total'])? $values['total'] : 0;
		$total_payed						= isset($values['total_payed'])? $values['total_payed'] : 0;
		$paymentprocessor_id				= isset($values['payment_processor_sel_id']) ? $values['payment_processor_sel_id'] : 0 ;
		$paymentprocessor_type				= isset($values['payment_processor_sel_type']) ? $values['payment_processor_sel_type'] : '' ; 
	// dmp($values);
	// exit;
		if( !is_numeric($reservation_status ) )
			return false;

		if( $confirmation_id ==0 || $reservation_status ==0)
			return false;		
		try
		{ 		
			if( $total !=0 && $total == $total_payed )
			{
				//setam sarea rezervarii
				$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $reservation_status WHERE confirmation_id = ".$confirmation_id;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					//JError::raiseWarning( 500, "Error change Room state !" );
					throw( new Exception($this->_db->getErrorMsg()) );
				}
				//~setam starea rezervarii
			}
			else
			{
				//setam sarea rezervarii
				$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $reservation_status WHERE confirmation_id = ".$confirmation_id;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					//JError::raiseWarning( 500, "Error change Room state !" );
					throw( new Exception($this->_db->getErrorMsg()) );
				}
				//~setam starea rezervarii
				
				//clean all mark as cash pay
				$query = 	" 
								DELETE FROM #__hotelreservation_confirmations_payments
								WHERE 
									payment_type_id= ".CASH_ID."
									AND
									confirmation_id = ".$confirmation_id." 
								
								";
				// dmp($query);
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					//JError::raiseWarning( 500, "Error change Room state !" );
					// dmp($this->_db);
					// exit;
					throw( new Exception($this->_db->getErrorMsg()) );
				}
				//~clean all mark as cash pay
				
				
				//check all block payment
				if( $reservation_status == CHECKEDOUT_ID )
				{
					$query = 	" 	SELECT
														*
													FROM #__hotelreservation_confirmations_payments
													WHERE 
														confirmation_id = ".$confirmation_id." 
					AND
														payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
					AND
					tip='card'
													";
												
									$res = $this->_getList( $query );
					foreach( $res as $v )
					{
					//dmp($v);
					$total_payed -= $v->AMT;
					}
				}
				
					// dmp($total_payed);
						// exit;
					//~check all block payment
				
				
				if( $is_enable_payment == false )
				{
					if( $reservation_status == CANCELED_ID ) //&& $payment_percent > 0) 
					{	//daca avem CANCELED
						
						//notify  all payments
						$query = 	" 
									UPDATE #__hotelreservation_confirmations_payments
										SET 
											data 			= now(),
											TRXTYPE 		= IF( tip ='card', 'V', '' ),
											payment_status  = IF( tip ='card', '".PAYMENT_STATUS_RELEASED."', '".PAYMENT_STATUS_PAYED."' )
											
									WHERE 
										confirmation_id = ".$confirmation_id." 
										AND 
										payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
										#AND 
										#payment_id <> ".PREAUTHORIZATION_PAYMENT_ID."
									";
						
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
						//~notify  all payments
						
						$query = 	" 
									INSERT INTO #__hotelreservation_confirmations_payments
											( 
												confirmation_id, 
												paymentprocessor_id,
												payment_id, 
												payment_type_id,
												tip,
												data, 
												payment_percent, 
												payment_explication, 
												payment_status, 
												TRXTYPE
											)
									VALUES( 
											$confirmation_id, 
											$paymentprocessor_id,
											$payment_id, 
											$payment_type_id,
											'$tip_confirmation_payment_status', 
											now(), 
											'$payment_percent', 
											'$payment_explication', 
											'".PAYMENT_STATUS_PAYED."', 
											'' 
										)
									ON DUPLICATE KEY UPDATE
										payment_id			= $payment_id,  
										payment_type_id		= $payment_type_id,  
										data 				= now(), 
										payment_percent 	= '$payment_percent', 
										payment_explication = '$payment_explication',
										payment_status		='".PAYMENT_STATUS_PAYED."'
									";
					
						$this->_db->setQuery( $query );
						// dmp($this->_db);
							// exit;
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							//dmp($this->_db);
							//exit;
							throw( new Exception($this->_db->getErrorMsg()) );
						}
					}
					else if( $reservation_status == CHECKEDOUT_ID )
					{
						//notify  all payments
						$query = 	" 
									UPDATE #__hotelreservation_confirmations_payments
										SET 
											data 			= now(),
											TRXTYPE 		= IF( tip ='card', 'V', '' ),
											payment_status  = IF( tip='card', '".PAYMENT_STATUS_RELEASED."', '".PAYMENT_STATUS_PAYED."' )
											
									WHERE 
										confirmation_id = ".$confirmation_id." 
										AND 
										payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
										#AND 
										#payment_id <> ".PREAUTHORIZATION_PAYMENT_ID."
									";
						
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
						//~notify  all payments
						
						$query = 	" 
										INSERT INTO #__hotelreservation_confirmations_payments
										( 
											confirmation_id, 
											paymentprocessor_id,
											payment_id, 
											payment_type_id,
											data, 
											payment_value, 
											payment_explication, 
											payment_status, 
											tip,
											TRXTYPE,
											AMT
										)
										VALUES
										( 
											$confirmation_id, 
											$paymentprocessor_id,
											$payment_id,
											".DONE_PAYMENT_ID.",											
											now(), 
											'".($total-$total_payed ) ."', 
											'PAY RESERVATION', 
											'".PAYMENT_STATUS_PAYED."' ,
											'$tip_confirmation_payment_status',
											'' ,
											'' 
										)
										ON DUPLICATE KEY UPDATE
											payment_id			= 	$payment_id, 
											payment_type_id		= 	".DONE_PAYMENT_ID.", 
											data				=	now(), 
											payment_value 		= 	'".($total-$total_payed )."', 
											payment_explication = 	'PAY RESERVATION',  
											payment_status		=	'".PAYMENT_STATUS_PAYED."' ,
											TRXTYPE				= 	'',
											AMT					= 	''
									";
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
					}
					
					
				}
				else //enable payment
				{
					if(  $reservation_status == CANCELED_ID )
					{
						//notify  all payments
						$query = 	" 
									UPDATE #__hotelreservation_confirmations_payments
										SET 
											data 			= now(),
											TRXTYPE 		= IF( tip ='card', 'V', '' ),
											payment_status  = IF( tip='card', '".PAYMENT_STATUS_RELEASED."', '".PAYMENT_STATUS_PAYED."' )
											
									WHERE 
										confirmation_id = ".$confirmation_id." 
										AND 
										payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
										#AND 
										#payment_id <> ".PREAUTHORIZATION_PAYMENT_ID."
									";
						
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
						//~notify  all payments
							
						$query = 	" 
									INSERT INTO #__hotelreservation_confirmations_payments
										( 
											confirmation_id, 
											paymentprocessor_id,
											payment_id, 
											payment_type_id, 
											data, 
											payment_percent, 
											payment_explication,
											payment_status, 
											tip,
											TRXTYPE 
										)
									VALUES
										( 
											$confirmation_id, 
											$paymentprocessor_id,
											$payment_id, 
											$payment_type_id, 
											now(), 
											'$payment_percent', 
											'$payment_explication' , 
											'".($tip_confirmation_payment_status=='card' ? PAYMENT_STATUS_NOTPAYED : PAYMENT_STATUS_PAYED)."',
											'$tip_confirmation_payment_status',
											'".($tip_confirmation_payment_status=='card' ? 'S' : '')."'
										)
									ON DUPLICATE KEY UPDATE
										payment_id 			= ".$payment_id.", 
										payment_type_id		= ".$payment_type_id.", 
										data 				= now(), 
										payment_percent 	= '$payment_percent', 
										payment_explication = '$payment_explication',  
										payment_status		='".($tip_confirmation_payment_status=='card' ? PAYMENT_STATUS_NOTPAYED : PAYMENT_STATUS_PAYED)."' ";
					
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Payment state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
						
					}
					else if( $reservation_status == CHECKEDOUT_ID )
					{
					
						//notify  all payments
						$query = 	" 
									UPDATE #__hotelreservation_confirmations_payments
										SET 
											data 			= now(),
											TRXTYPE 		= IF( tip='card', 'V', '' ),
											payment_status  = IF( tip='card', '".PAYMENT_STATUS_RELEASED."', '".PAYMENT_STATUS_PAYED."' )
									WHERE 
										confirmation_id = ".$confirmation_id." 
										AND 
										payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
									";
						// dmp($query);
						// exit;
					
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							throw( new Exception($this->_db->getErrorMsg()) );
						}
						//~notify  all payments
					
						$query = 	" 
										INSERT INTO #__hotelreservation_confirmations_payments
										( 
											confirmation_id, 
											paymentprocessor_id,
											payment_id, 
											payment_type_id,
											data, 
											payment_value, 
											payment_explication, 
											payment_status, 
											tip,
											TRXTYPE,
											AMT
										)
										VALUES
										( 
											$confirmation_id, 
											$paymentprocessor_id,
											$payment_id,
											".DONE_PAYMENT_ID.", 
											now(), 
											'".($total-$total_payed ) ."', 
											'PAY RESERVATION', 
											'".($tip_confirmation_payment_status=='card'?  PAYMENT_STATUS_NOTPAYED : PAYMENT_STATUS_PAYED)."',
											'$tip_confirmation_payment_status',
											'".($tip_confirmation_payment_status=='card'?  'S' : '')."' ,
											'".($tip_confirmation_payment_status=='card'?  ($total-$total_payed ) : '')."' 
										)
										ON DUPLICATE KEY UPDATE
											payment_id			= 	$payment_id,
											payment_type_id 	= 	".DONE_PAYMENT_ID.", 
											data				=	now(), 
											payment_value 		= 	'".$values['total'] ."', 
											payment_explication = 	'PAY RESERVATION',  
											payment_status		=	'".($tip_confirmation_payment_status=='card'?  PAYMENT_STATUS_NOTPAYED : PAYMENT_STATUS_PAYED)."' ,
											TRXTYPE				= 	'".($tip_confirmation_payment_status=='card'?  'S' : '')."',
											AMT					= 	'".($tip_confirmation_payment_status=='card'?  ($values['total'] ) : '')."'
									";
						
						$this->_db->setQuery( $query );
						if (!$this->_db->query()) 
						{
							//JError::raiseWarning( 500, "Error change Room state !" );
							return false;
						}
					
					}
				}
			}
		}
		catch( Exception $ex )
		{
		dmp($ex);
		exit;
			return false;
		}

		return true;
	}	
	
	
	function changePaymentConfirmation( $values )
	{
		
		$is_part_payment				= $values['is_part_payment'];
		$confirmation_id				= $values['confirmation_id'];
		$reservation_status			= $values['reservation_status'];
		$payment_id						= $values['payment_id'];
		$payment_type_id				= $values['payment_type_id'];
		$payment_explication			= $values['payment_explication'];
		$tip_confirmation_payment_status= $values['tip_confirmation_payment_status'];
		$confirmation_payment_status	= $values['confirmation_payment_status'];
		$paymentprocessor_id			= isset($values['payment_processor_sel_id'])? $values['payment_processor_sel_id'] : 0;
		$paymentprocessor_type			= isset($values['payment_processor_sel_type'])? $values['payment_processor_sel_type'] : '';
		$payment_value					= $is_part_payment ? $values['payment_value'] : $values['total'] - $values['total_payed'];
		// dmp($values);
		// exit;
		try
		{ 	
			if( $paymentprocessor_type == PROCESSOR_BANK_ORDER )
			{
				$query = 	" 
							UPDATE #__hotelreservation_confirmations_payments
									SET 
										data 				= now(), 
										payment_percent		= 0, 
										payment_value		= ".( $is_part_payment? 'payment_value' : $payment_value ).", 
										payment_explication = '$payment_explication', 
										payment_status		= '".PAYMENT_STATUS_PAYED."'
							WHERE 
								paymentprocessor_id= ".$paymentprocessor_id."
								AND
								confirmation_id = ".$confirmation_id." 
							
							";
			}
			else if( $is_part_payment == false )
			{
				$query = 	" 
							INSERT INTO #__hotelreservation_confirmations_payments
									( confirmation_id, paymentprocessor_id, payment_id, payment_type_id, tip,data, payment_percent, payment_value, payment_explication, payment_status, TRXTYPE)
							VALUES( $confirmation_id, '$paymentprocessor_id', $payment_id, $payment_type_id, '$tip_confirmation_payment_status', now(), '0', '$payment_value', '$payment_explication', '', '".($tip_confirmation_payment_status=='card'? "S" : "")."' )
							ON DUPLICATE KEY UPDATE
								payment_id= ".$payment_id.", payment_type_id= ".$payment_type_id.", data = now(), payment_percent = '0',payment_value='$payment_value', payment_explication = '$payment_explication', payment_status='' ";
			}
			$this->_db->setQuery( $query );
			
			if (!$this->_db->query()) 
			{
				//JError::raiseWarning( 500, "Error change Room state !" );
				// dmp($this->_db);
				// exit;
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			
			if( $is_part_payment )
				return true;
			//clean all mark as cash pay
			$query = 	" 
							DELETE FROM #__hotelreservation_confirmations_payments
							WHERE 
								payment_type_id	= ".CASH_ID."
								AND
								confirmation_id = ".$confirmation_id." 
							
							";
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				//JError::raiseWarning( 500, "Error change Room state !" );
				// dmp($this->_db);
				// exit;
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			//~clean all mark as cash pay
			
			/*
			release payflow
			*/
			//~notify  all payments
			$query = 	" 
						UPDATE #__hotelreservation_confirmations_payments
							SET 
								data 			= now(),
								TRXTYPE 		= IF( tip ='card', 'V', '' ),
								payment_status  = IF( tip='card', '".PAYMENT_STATUS_RELEASED."', '".PAYMENT_STATUS_PAYED."' )
								
						WHERE 
							confirmation_id = ".$confirmation_id." 
							AND 
							payment_status  IN( '".PAYMENT_STATUS_BLOCK."', '".PAYMENT_STATUS_NOTPAYED."' )
							#AND 
							#payment_id <> ".PREAUTHORIZATION_PAYMENT_ID."
						";
			
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				//JError::raiseWarning( 500, "Error change Room state !" );
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			//~notify  all payments
				
			$query = 	" 
						UPDATE #__hotelreservation_confirmations
							SET 
								data 							= now(),
								reservation_status			= $reservation_status,
								confirmation_payment_status		= '$confirmation_payment_status'
						WHERE 
							confirmation_id = ".$confirmation_id." 
						";
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				//JError::raiseWarning( 500, "Error change Room state !" );
				throw( new Exception($this->_db->getErrorMsg()) );
			}
		}
		catch( Exception $ex )
		{
			// dmp($ex);
			// exit;
			return false;
		}
// exit;
		return true;
	}

	
	function penalty( $values, &$msg ) 
	{
		$paymentprocessor_id	= isset($values['payment_processor_sel_id']) ? $values['payment_processor_sel_id'] : 0 ;		
		$payment_id				= $values['payment_id'];
		$payment_percent		= $values['payment_percent'];
		$payment_explication	= $values['payment_explication'];
		$confirmation_id		= $values['confirmation_id'];
		$is_enable_payment		= $values['is_enable_payment'];
		$paymentprocessor_type	= isset($values['payment_processor_sel_type']) && strlen($values['payment_processor_sel_type']) > 0 ? $values['payment_processor_sel_type'] : PROCESSOR_CASH ; 
		if( $paymentprocessor_type== PROCESSOR_CASH )
			$tip_payment_penalties	= 'cash';
		else if( $paymentprocessor_type== PROCESSOR_BANK_ORDER )
			$tip_payment_penalties	= 'bank';
		else
			$tip_payment_penalties	= 'card';
		// dmp($paymentprocessor_type);
		// exit;
		if( $confirmation_id == 0 )
			return false;
		
		if( $payment_percent == 0 )
		{
			JError::raiseWarning( 500, JText::_('LNG_PAYMENT_AMOUNT_ERROR',true) );
			return false;
		}
		

	/*
		if( (!is_numeric($payment_percent ) && $payment_percent != '' )  || $payment_percent == 0 )
		{
			$query = 	" DELETE FROM #__hotelreservation_confirmations_payments WHERE confirmation_id = ".$confirmation_id." AND payment_id = ".PENALTY_PAYMENT_ID ;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				return false;
			}
			//return false;
		}
		else
		{
			*/$query = 	" 
							INSERT INTO #__hotelreservation_confirmations_payments
									( confirmation_id, paymentprocessor_id, payment_id, payment_type_id, data, payment_percent, payment_explication, tip,  payment_status ,TRXTYPE )
							VALUES( 
									$confirmation_id, 
									'$paymentprocessor_id',
									'$payment_id',
									".PENALTY_PAYMENT_ID.", 
									now(), 
									'$payment_percent', 
									'$payment_explication', 
									'$tip_payment_penalties', 
									'".($tip_payment_penalties=='card' && $is_enable_payment ?  PAYMENT_STATUS_BLOCK : PAYMENT_STATUS_NOTPAYED)."' ,
									'".($tip_payment_penalties=='card' && $is_enable_payment?  'A' : '')."' 	
									)
							ON DUPLICATE KEY UPDATE
								payment_id 			= ".$payment_id.", 
								payment_type_id 	= ".PENALTY_PAYMENT_ID.", 
								paymentprocessor_id	= '$paymentprocessor_id',
								data				=	now(), 
								payment_percent 	= '$payment_percent', 
								payment_explication = '$payment_explication',  
								tip					= '$tip_payment_penalties',  
								payment_status		= '".($is_enable_payment && $tip_payment_penalties=='card' ?  PAYMENT_STATUS_BLOCK : PAYMENT_STATUS_NOTPAYED)."',
								TRXTYPE				= '".($is_enable_payment && $tip_payment_penalties=='card'  ?  'A'  : '')."'"
								;
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				//$msg = JText::_('LNG_ERROR_APPLY_PENALTIES',true);
				JError::raiseWarning( 500, JText::_('LNG_ERROR_APPLY_PENALTIES',true) );
				return false;
			}
		//}
		return true;
	}



	function delete_confirmation_payment( $confirmation_id )
	{
		$query = 	" 
						DELETE 
							FROM #__hotelreservation_confirmations_payments
						WHERE confirmation_id = $confirmation_id
								AND
								payment_id = ".DONE_PAYMENT_ID." ";
	
		$this->_db->setQuery( $query );
		$this->_db->query();
		
	}
	
	//function to delete unpayed bank order, check limit day date
	function removeBankOrderUnpayedLate()
	{
	
		$paymentprocessors 	 = $this->getPaymentProcessors();
		$paymentsettings 	 = $this->getPaymentSettings();
		$is_max_days 	= false;
		$max_days 		= 0;
		foreach( $paymentsettings as $val )
		{
			if( $val->payment_id == BANK_ORDER_ID )
			{
				//exit;
				$explication 	= $val->payment_name;
				$is_max_days	= $val->is_check_days;
				if( $val->is_check_days )
				{
					foreach( $paymentprocessors as $process )
					{
						if( $process->paymentprocessor_type == PROCESSOR_BANK_ORDER )
						{
							$max_days = $process->paymentprocessor_timeout_days;		
							break;
						}
						break;
					}
				}
				//break;
			}
		}
		
		if( $is_max_days ==false )
			return;
		
		$query = " SELECT 
					c.confirmation_id , 
					c.data
				FROM #__hotelreservation_confirmations c 
				INNER JOIN #__hotelreservation_confirmations_payments  	cp USING(confirmation_id)
				INNER JOIN #__hotelreservation_paymentprocessors  		pp USING(paymentprocessor_id)
				
				WHERE cp.payment_status='waiting' AND pp.paymentprocessor_type  = '".PROCESSOR_BANK_ORDER."'
				";
		$conf = $this->_getList( $query );
		// dmp($query);
		foreach( $conf as $c )
		{
			if( strtotime($c->data." + $max_days day") < strtotime('now') )
			{
				JRequest::setVar( 'confirmation_id', $c->confirmation_id);
				$this->_confirmation_id = $c->confirmation_id;
				$this->remove();
			}
		}
	}



}
?>