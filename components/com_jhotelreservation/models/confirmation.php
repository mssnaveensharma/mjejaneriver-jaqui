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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport('joomla.user.helper');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelConfirmation extends JModelLegacy
{
	function __construct()
	{
		$this->log = Logger::getInstance();
		parent::__construct();
	}

	function getReservation($reservationId=null){
		if(!isset($reservationId))
			$reservationId = JRequest::getInt("reservationId");
		
		/* $confirmationTable = $this->getTable('Confirmations');
		$reservation = $confirmationTable->getReservationData($reservationId);

		$reservation->reservedItems = explode(",",$reservation->items_reserved);
		$reservation->extraOptionIds = explode(",",$reservation->extraOptionIds);
		$reservation->hotelId = $reservation->hotel_id;
		$hotel = HotelService::getHotel($reservation->hotel_id);
		$reservation->currency = HotelService::getHotelCurrency($hotel);
		
		if(!isset($reservation->totalPaid))
			$reservation->totalPaid = 0;
		
		//dmp($reservation);
		$reservationData = new stdClass;
		$reservationData->userData = $reservation;
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = $hotel;
		
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->generateReservationSummary($reservationData);
		$reservationDetails->reservationData = $reservationData;
		 */
		
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		
		
		return $reservationDetails;
	}
	
	function sendConfirmedPaymentEmail(){
		
	}
	
	function sendGuestList(){
		$reservationService = new ReservationService();
		$startDate = date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 day'));
		$endDate = date('Y-m-d', strtotime($startDate . ' + 1 day'));
	 	$confirmationTable = $this->getTable('Confirmations');
		$reservations = $confirmationTable->getReservationList($startDate, $startDate);
		
		$guestDetailsList="<tr style='text-align: left;'><th>".JText::_("LNG_NAME")."</th><th>".JText::_("LNG_ARRIVAL")."</th><th>".JText::_("LNG_DEPARTURE")."</th><th>".JText::_("LNG_ADULTS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_ROOMS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_OFFERS")."</th></tr>";
		
		if(count($reservations)>0){
			$hotelId = $reservations[0]->hotel_id;
		
			$guestDetailsList.="<tr>";
			$guestDetailsList.="<td>".$reservations[0]->last_name.' '.$reservations[0]->first_name."&nbsp;&nbsp;&nbsp;</td><td nowrap='nowrap'>".JHotelUtil::convertToFormat($reservations[0]->start_date).
			" &nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservations[0]->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->offer_names."&nbsp;&nbsp;&nbsp;</td>";
			$guestDetailsList.="</tr>";
			foreach($reservations as $reservation){
				if($hotelId != $reservation->hotel_id || next($reservations)===false){
					$guestDetailsList.="<tr>";
					$guestDetailsList.="<td>".$reservation->last_name.' '.$reservation->first_name."&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->start_date).
					"&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->offer_names."&nbsp;&nbsp;&nbsp;</td>";
					$guestDetailsList.="</tr>";
					$guestDetailsList = "<table style=text-align:left'>".$guestDetailsList."</table>";
					EmailService::sendGuestListEmail($reservation->hotel_id, $reservation->hotel_name,$reservation->hotel_email, $guestDetailsList, $startDate);
					$guestDetailsList="<tr style='text-align: left;'><th>".JText::_("LNG_NAME")."</th><th>".JText::_("LNG_ARRIVAL")."</th><th>".JText::_("LNG_DEPARTURE")."</th><th>".JText::_("LNG_ADULTS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_ROOMS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_OFFERS")."</th></tr>";
					$hotelId = $reservation->hotel_id;
					break;
				}
				
				$guestDetailsList.="<tr>";
				$guestDetailsList.="<td>".$reservation->last_name.' '.$reservation->first_name."&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->start_date).
									"&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->offer_names."&nbsp;&nbsp;&nbsp;</td>";
				$guestDetailsList.="</tr>";
				
			}
		}
	}

	function sendConfirmationEmail($reservationDetails){
		
		EmailService::sendConfirmationEmail($reservationDetails);
	}
	
	function saveConfirmation($reservationDetails){
		try{
			$reservaitonId = $reservationDetails->reservationData->userData->confirmation_id;
			if(count($reservationDetails->roomNotAvailable)>0){
				foreach($reservationDetails->roomNotAvailable as $room){
					$this->setError($room->room_name." is not available between ".$reservationDetails->reservationData->userData->start_date." and ".$reservationDetails->reservationData->userData->end_date);
				}
				return -1;
			}
			
			$startDate = $reservationDetails->reservationData->userData->start_date;
			$endDate =  $reservationDetails->reservationData->userData->end_date;
			
			$reservaitonId = $this->storeConfirmation($reservationDetails);
			$this->deleteReservaitonRooms($reservaitonId);
			
			foreach($reservationDetails->rooms as $room){
				$confirmationRoomId = $this->storeConfirmationRooms($reservaitonId, $room);
				$this->storeConfirmationRoomPrices($confirmationRoomId, $room, $startDate, $endDate);
			}
			
			if(isset($reservationDetails->reservationData->userData->extraOptionIds) && is_array($reservationDetails->reservationData->userData->extraOptionIds)){
				//dmp($reservationDetails->extraOptions);
				$this->deleteReservaitonExtraOptions($reservaitonId);
				foreach($reservationDetails->reservationData->userData->extraOptionIds as $extraOptionId){
					$extraOption = $this->getExtraOption($reservationDetails->extraOptions, $extraOptionId);
					$this->storeConfirmationExtraOptions($reservaitonId, $extraOption);
				}
			}
			
			if(isset( $reservationDetails->reservationData->userData->guestDetails)){
				$this->deleteGuestDetails($reservaitonId);
				$this->storeConfirmationGuestDetails($reservaitonId, $reservationDetails->reservationData->userData->guestDetails);
			}
			
			if(isset($reservationDetails->reservationData->userData->excursions)){
				if(count($reservationDetails->excursions))
				foreach($reservationDetails->excursions as $excursion){
					$confirmationExcursionId = $this->storeConfirmationExcursions($reservaitonId,$excursion);
					$this->storeConfirmationExcursionPrices($confirmationExcursionId,$excursion,$startDate, $endDate);
				}
			}
			$this->addUser($reservationDetails,$reservaitonId);
			
			//exit;
		}catch( Exception $ex ){
			JError::raiseWarning( 500, $ex->getMessage() );
			return false;
		}
		//exit;
		return $reservaitonId;
	}
	
	function getExtraOption($extraOptions, $extraOptionId){
		$extraOptionValues = explode("|",$extraOptionId);
		foreach($extraOptions as $extraOption){
			if($extraOption->id == $extraOptionValues[3]){	
				$extraOption->nrPersons = $extraOptionValues[5];
				$extraOption->nrDays = $extraOptionValues[6];
				$extraOption->offerId = $extraOptionValues[0];
				$extraOption->roomId = $extraOptionValues[1];
				$extraOption->current = $extraOptionValues[2];
				return $extraOption;
			}
		}
		return null;
	}

	function storeConfirmation($reservationDetails){
		$rowTable						= 	$this->getTable('Confirmations');
		$obj = new stdClass();
		$obj->confirmation_id			=	$reservationDetails->reservationData->userData->confirmation_id;
		$obj->hotel_id					=	$reservationDetails->reservationData->userData->hotelId;
		$obj->start_date				= 	$reservationDetails->reservationData->userData->start_date;
		$obj->end_date					= 	$reservationDetails->reservationData->userData->end_date;
		$obj->adults					= 	$reservationDetails->reservationData->userData->adults;
		$obj->children					= 	$reservationDetails->reservationData->userData->children;
		$obj->rooms						= 	$reservationDetails->reservationData->userData->rooms;
		if(isset($reservationDetails->reservationData->userData->coupon_code))
			$obj->coupon_code				=	$reservationDetails->reservationData->userData->coupon_code;
		$obj->guest_type				=	$reservationDetails->reservationData->userData->guest_type;
		$obj->first_name				=	$reservationDetails->reservationData->userData->first_name;
		$obj->last_name					=	$reservationDetails->reservationData->userData->last_name;
		$obj->remarks					=	$reservationDetails->reservationData->userData->remarks;
		$obj->remarks_admin				=	$reservationDetails->reservationData->userData->remarks_admin;
		$obj->arrival_time				=	$reservationDetails->reservationData->userData->arrival_time;
		$obj->address					=	$reservationDetails->reservationData->userData->address;
		$obj->postal_code				=	$reservationDetails->reservationData->userData->postal_code;
		$obj->city						=	$reservationDetails->reservationData->userData->city;
		$obj->state_name				=	$reservationDetails->reservationData->userData->state_name;
		$obj->country					=	$reservationDetails->reservationData->userData->country;
		$obj->phone						=	$reservationDetails->reservationData->userData->phone;
		$obj->email						=	$reservationDetails->reservationData->userData->email;
		if(isset($reservationDetails->reservationData->userData->conf_email))
			$obj->conf_email				=	$reservationDetails->reservationData->userData->conf_email;
		$obj->confirmation_details		= 	$reservationDetails->reservationInfo;
		if(empty($obj->confirmation_id))
			$obj->reservation_status		=	RESERVED_ID;
		$obj->total						= 	$reservationDetails->total;
		$obj->total_cost				= 	$reservationDetails->cost;
		if(isset($reservationDetails->reservationData->userData->media_referer))
			$obj->media_referer				= 	$reservationDetails->reservationData->userData->media_referer;
		if(isset($reservationDetails->reservationData->userData->voucher))
			$obj->voucher					= 	$reservationDetails->reservationData->userData->voucher;
		if(isset($reservationDetails->reservationData->userData->company_name))
			$obj->company_name				= 	$reservationDetails->reservationData->userData->company_name;
		if(isset($reservationDetails->reservationData->userData->discount_code))
			$obj->discount_code				= 	$reservationDetails->reservationData->userData->discount_code;

		//dmp($obj);
		//exit;
		
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		
		//dmp($obj);

		 if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		return $rowTable->confirmation_id;
	}

	function storeConfirmationRooms($confirmationId, $room){
		$rowTable						= 	 $this->getTable('ConfirmationsRooms');
		$obj = new stdClass();
		$obj->confirmation_id	=	$confirmationId;
		$obj->hotel_id			=	$room->hotel_id;
		$obj->offer_id			=	$room->offer_id;
		$obj->room_id			=	$room->room_id;
		$obj->current			=	$room->current;
		$obj->room_name			=	$room->room_name;
		$obj->adults			= 	$room->adults;
		$obj->children			= 	$room->children;
		//dmp($obj);
		//exit;
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		//dmp($obj);
		
		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		
		return $rowTable->confirmation_room_id;
	}
	
	function deleteReservaitonRooms($reservationId){
		$rowTable = 	 $this->getTable('ConfirmationsRooms');
		$rowTable->deleteRooms($reservationId);
	}
	
	function deleteReservaitonExtraOptions($reservationId){
		$rowTable = $this->getTable('ConfirmationsExtraOptions');
		$rowTable->deleteExtraOptions($reservationId);
	}
	
	function storeConfirmationRoomPrices($confirmationRoomId, $room, $startDate, $endDate){
		
		//dmp($room);
		//dmp($startDate);
		//dmp($endDate);
		for( $d = strtotime($startDate);$d < strtotime($endDate); ){
			$dayString = date( 'Y-m-d', $d);
			
			$rowTable						= 	 $this->getTable('ConfirmationsRoomPrices');
			$obj = new stdClass();
			$obj->confirmation_room_id		=	$confirmationRoomId;
			$obj->current					=	$room->current;
			$obj->date						=	$dayString;
			$obj->price						= 	$room->daily[$dayString]['price_final'];
			
			if(isset ($room->customPrices) && isset($room->customPrices[$dayString])){
				$obj->price = $room->customPrices[$dayString];
			}
			
			//dmp($obj->price);
			
			if (!$rowTable->bind($obj)){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			if (!$rowTable->check()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			if (!$rowTable->store()){
			 throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			//dmp($obj);
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	}

	function storeConfirmationExtraOptions($confirmationId, $extraOptions){
		$rowTable =  $this->getTable('ConfirmationsExtraOptions');
		
		$obj = new stdClass();
		$obj->confirmation_id			=	$confirmationId;
		$obj->hotel_id					=	$extraOptions->hotel_id;
		$obj->offer_id					=	$extraOptions->offerId;
		$obj->room_id					=	$extraOptions->roomId;
		$obj->current					=	$extraOptions->current;
		$obj->extra_option_id			=	$extraOptions->id;
		$obj->extra_option_name			=	$extraOptions->name;
		$obj->extra_option_price		=	$extraOptions->price;
		$obj->extra_option_price_type	=	$extraOptions->price_type;
		$obj->extra_option_is_per_day	=	$extraOptions->is_per_day;
		$obj->extra_option_mandatory	=	$extraOptions->mandatory;
		$obj->extra_option_persons		=	$extraOptions->nrPersons;
		$obj->extra_option_days			=	$extraOptions->nrDays;

		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		//dmp($obj);
		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		} 
		
		//dmp("OK");
	}
	
	function storeConfirmationExcursions($confirmationId, $excursion){
		$rowTable =  $this->getTable('ConfirmationsExcursions');
		$obj = new stdClass();
		$obj->confirmation_id			=	$confirmationId;
		$obj->hotel_id					=	$excursion->hotel_id;
		$obj->excursion_id				=	$excursion->excursion_id;
		$obj->excursion_name			=	$excursion->excursion_name;
		$obj->nr_booked					=	$excursion->nrItemsBooked;
		
	
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	
	
		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	
		//dmp($obj);
		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		return $rowTable->confirmation_excursion_id; 
		//dmp("OK");
	}
	
	function storeConfirmationExcursionPrices($confirmationExcursionId, $excursion, $startDate, $endDate){
	
		for( $d = strtotime($startDate);$d < strtotime($endDate); ){
			$dayString = date( 'Y-m-d', $d);
			$rowTable						= 	 $this->getTable('ConfirmationsExcursionsPrices');
			$obj->confirmation_excursion_id	=	$confirmationExcursionId;
			$obj->date						=	$dayString;
			$obj->price						= 	$excursion->daily[$dayString]['price_final'];
				
			if(isset ($excursion->customPrices) && isset($excursion->customPrices[$dayString])){
				$obj->price = $excursion->customPrices[$dayString];
			}
				
			if (!$rowTable->bind($obj)){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			if (!$rowTable->check()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
	
			if (!$rowTable->store()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			//dmp($obj);
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	}

	
	function deleteGuestDetails($confirmationId){
		$db =JFactory::getDBO();
		$query="delete from #__hotelreservation_confirmations_guests where confirmation_id=".$confirmationId;
		$db->setQuery($query);

		return $db->query();
	}
	
	function storeConfirmationGuestDetails($confirmationId, $guestDetails){
		
		foreach($guestDetails as $guestDetail){
			$guestDetail->confirmation_id = $confirmationId;
			$rowTable	= 	 $this->getTable('ConfirmationsGuests');

			if (!$rowTable->bind($guestDetail))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}

			if (!$rowTable->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}

			if (!$rowTable->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
		}
	}
		
	function storeConfirmationTaxes($confirmationId){
		$rowTable						= 	 $this->getTable('ConfirmationsTaxes');
		$rowTableManage					= 	 $this->getTable( "ManageTaxes" );
			
		$rowTableManage->load( $obj_ids[$j]->tax_id );

		if( $rowTableManage->tax_id +0== 0 )
			continue;

		$obj->confirmation_id			=	$confirmationId;
		$obj->tax_id					=	$rowTableManage->tax_id;
		$obj->tax_name					=	$rowTableManage->tax_name;
		$obj->hotel_id					=	$data->hotel_id;
		$obj->tax_type					=	$rowTableManage->tax_type;
		$obj->tax_value					=	$rowTableManage->tax_value;

		if (!$rowTable->bind($obj))
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->store())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}

	function storeConfirmationAirportTransfer($confirmationId){
		$ex_airport_transfer 	= explode("|", $j_key);
		$ch_airport 			= $ex_airport_transfer[0].'|'.$ex_airport_transfer[1].'|'.$ex_airport_transfer[2];


		$rowTable						= 	 $this->getTable('ConfirmationsRoomsAirportTransfer');

		$rowTableManage					= 	 $this->getTable( "ManageAirportTransferTypes" );
		$rowTableManage->load( $ex_airport_transfer[3] );
			
		if(
				$rowTableManage->airport_transfer_type_id + 0 == 0
				||
				!isset( $data->airport_airline_ids[$ch_airport] )
				||
				!isset( $data->airport_transfer_dates[$ch_airport] )
				||
				!isset( $data->airport_transfer_time_hours[$ch_airport] )
				||
				!isset( $data->airport_transfer_time_mins[$ch_airport] )
				||
				!isset( $data->airport_transfer_flight_nrs[$ch_airport] )
				||
				!isset( $data->airport_transfer_guests[$ch_airport] )
		)
		{
			continue;
		}

		$obj->confirmation_id				=	$confirmationId;
		$obj->hotel_id						=	$data->hotel_id;
		$obj->offer_id						=	$ex_airport_transfer[0];
		$obj->room_id						=	$ex_airport_transfer[1];
		$obj->current						=	$ex_airport_transfer[2];
		$obj->airport_transfer_type_id		=	$rowTableManage->airport_transfer_type_id;
		$obj->airport_transfer_type_name	=	$rowTableManage->airport_transfer_type_name;
		$obj->airport_transfer_type_price	=	$rowTableManage->airport_transfer_type_price;
		$obj->airport_transfer_type_vat		=	$rowTableManage->airport_transfer_type_vat;

		$rowTableManage					= 	 $this->getTable( "ManageAirlines" );
		$rowTableManage->load( $data->airport_airline_ids[ $ch_airport ][3] );


		if( $rowTableManage->airline_id + 0 == 0 )
			continue;
		$obj->airline_id					=	$rowTableManage->airline_id;
		$obj->airline_name					=	$rowTableManage->airline_name;

		$obj->airport_transfer_flight_nr		=	$data->airport_transfer_flight_nrs[$ch_airport][3];
		$obj->airport_transfer_date				=	$data->airport_transfer_dates[$ch_airport][3];
		$obj->airport_transfer_time_hour		=	$data->airport_transfer_time_hours[$ch_airport][3];
		$obj->airport_transfer_time_min			=	$data->airport_transfer_time_mins[$ch_airport][3];
		$obj->airport_transfer_guest			=	$data->airport_transfer_guests[$ch_airport][3];

		if (!$rowTable->bind($obj))
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->store())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}
	function addUser($reservationDetails,$reservationId){
		$user = JFactory::getUser();
		if(!$user->id || $user->guest==1){
			$userObj = UserService::getUserByEmail($reservationDetails->reservationData->userData->email);
			if(isset($userObj->id))
				$userId = $userObj->id;
			else
				$userId = $this->addJoomlaUser($reservationDetails);
		}
		else
			$userId = $user->id;
		
		$this-> setConfirmationUser($reservationId,$userId);
	}
	
	function addJoomlaUser($reservationDetails){

			// "generate" a new JUser Object
			$user = JFactory::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded
			
			jimport('joomla.application.component.helper'); 
			$usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params
			
			$userdata = array(); // place user data in an array for storing.
			$userdata['name'] = $reservationDetails->reservationData->userData->last_name.' '.$reservationDetails->reservationData->userData->first_name; ;
			$userdata['email'] = $reservationDetails->reservationData->userData->email;
			$userdata['username'] = $reservationDetails->reservationData->userData->email;

			//set password
			$userdata['password'] = UserService::generatePassword( $reservationDetails->reservationData->userData->email, true );
			$userdata['password2'] = $userdata['password'];
			
			//set default group.
			$usertype = $usersParams->get( 'new_usertype',2 );
			if (!$usertype)
			{
				$usertype = 'Registered';
			}
			
			//default to defaultUserGroup i.e.,Registered
			$userdata['groups']=array($usertype);
			$useractivation = $usersParams->get( 'useractivation' ); 					// in this example, we load the config-setting
			if ($useractivation == 1)
			{
				$config = JFactory::getConfig();
				$userdata['sitename']	= $config->get('sitename');
				$userdata['siteurl']	= JUri::base();
		
		
				$userdata['sitename']	= $config->get('sitename');
				$userdata['siteurl']	= JUri::base();
		
		
				jimport('joomla.user.helper'); 											// include libraries/user/helper.php
				$userdata['block'] = 1; 													// block the User
				$userdata['activation'] =JApplication::getHash( JUserHelper::genRandomPassword()); // set activation hash (don't forget to send an activation email)
		
				$uri = JURI::getInstance();
				$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
				$userdata['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$userdata['activation'], false);
		
				$emailSubject	= JText::sprintf(
										'COM_USERS_EMAIL_ACCOUNT_DETAILS',
										$userdata['name'],
										$userdata['sitename']
									);
		
				$emailBody = JText::sprintf(
										'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
										$userdata['name'],
										$userdata['sitename'],
										$userdata['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$userdata['activation'],
										$userdata['siteurl'],
										$userdata['username'],
										$userdata['password']
				);
				$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
				
				$fromName		= $appSettings->company_name;
				$confirmEmail	= $appSettings->company_email;
				$mail = new JMail();
				$response = $mail->sendMail($confirmEmail, $fromName, $userdata['email'], $emailSubject, $emailBody);
	
				if ($response !== true) {
					JError::raiseWarning('',JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED',true) );
				}
			}
			else { // no we need no activation
		
				$userdata['block'] = 0; // don't block the user
		
			}
			//now to add the new user to the dtabase.
			if (!$user->bind($userdata)) {
				$this->log->LogDebug("Exception when binding user - confirmtion".JText::_( $user->getError()));
				JError::raiseWarning('', JText::_( $user->getError())); // something went wrong!!
			}
			if (!$user->save()) {
				// now check if the new user is saved
				$this->log->LogDebug("Exception when adding user_id to confirmtion".JText::_( $user->getError()));
				JError::raiseWarning('', JText::_( $user->getError())); // something went wrong!!
			}
		return $user->id;
	}
	
	
	function setConfirmationUser($confirmationId,$userId){
		try
		{
			if(isset($userId) && $userId>0){
				$confirmationsTable =  $this->getTable('confirmations');
				$confirmationsTable->load($confirmationId);
				$confirmationsTable->user_id =  $userId;
				$confirmationsTable->store();
			}
			else{
				$this->log->LogDebug("user id is not set ".$userId);
			}
		}
		catch(Exception $e){
			$this->log->LogDebug("Exception when adding user_id to confirmation(".$confirmationId.")".$e->getMessage());
			break;
		}
	}

	
}