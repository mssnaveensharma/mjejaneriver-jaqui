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


//require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'assets'.DS.'classes.payflow.php'; 
//require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'assets'.DS.'classes.mpesa.php'; 
//require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'assets'.DS.'classes.pesaPI.php'; 
error_reporting(E_ALL);
ini_set('display_errors','On');

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport('joomla.user.helper');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');


class JHotelReservationModelVariables extends JModelLegacy
{

	var $confirmation_id;
	var $tip_oper;
	var $tmp;
	var $year_start;
	var $month_start;
	var $day_start;
	var $year_end;
	var $month_end;
	var $day_end;
	var $guest_adult				= 0;
	var $guest_child				= 0;
	var $rooms						= 0;
	var $coupon_code				= '';
	var $hotel_id					= 0;
	var $tabId;
	var $option_ids;
	var $room_ids;
	var $package_ids;					//offer_id | $room_id | current | package_id
	var $arrival_option_ids;			//offer_id | $room_id | current | arrival_option_id| 0 or 1 =>select or not
	var $extraOptionIds;
	var $airport_airline_ids;			//offer_id | $room_id | current | airline_id| airport_transfer_type_id|airport_transfer_date|airport_transfer_time_hour|airport_transfer_flight_nr|airport_transfer_guest
	var $airport_transfer_type_ids;
	var $airport_transfer_type_id;
	var $airport_transfer_dates;
	var $airport_transfer_time_hours;
	var $airport_transfer_time_mins;
	var $airport_transfer_flight_nrs;
	var $airport_transfer_guests;
	var $package_day;
	var $first_name					= '';
	var $details					= '';
	var $last_name					= '';
	var $address					= '';
	var $city						= '';
	var $state_name					= '';
	var $country					= '';
	var $postal_code				= '';
	var $tel						= '';
	var $email						= '';
	var $conf_email					= '';
	var $card_type_id				= '';
	var $card_type_name				= '';
	var $card_name					= '';
	var $card_number				= '';
	var $card_expiration_month 		= '';
	var $card_expiration_year 		= '';
	var $card_card_security_code	= '';
	var $email_confirmation			= '';

	var $itemCurrency;
	var $roomsAvailable;
	var $offersAvailable;
	var $itemFeatureOptionsAvailable;
	var $itemPackageNumbers;
	var $itemArrivalOptions;
	var $itemAirportTransferTypes;
	var $itemArrivalAirlines;
	
	var $itemRoomsSelected;
	var $itemHotelSelected;	
	var $itemRoomsCapacity;
	var $itemRoomsNumbers;
	var $itemPackages;
	var $itemFeatureOptions;
	var $itemTaxes;
	//var $itemRoomsDiscounts;
	var $itemPayments;
	var $itemPaymentProcessors;
	var $reservation_status;
	var $payment_processor_sel_id;
	var $payment_processor_sel_type;
	
	var $confirmation_payment_status  = PAYMENT_STATUS_NOTPAYED;
	
	var $Reservation_Details;
	var $Reservation_Details_EMail;
	var $Guest_Details;
	var $Payment_Information;
	var $Confirmation;
	
	var $payment_variables;
	
	var $val_rooms 			= 0;
	var $total_payments 	= 0;
	var $total_payments_ok 	= 0;
	var	$total				= 0;
	var	$total_payed		= 0;
	var	$total_init			= 0;
	var	$total_cost			= 0;
	
	
	var $ID_PAYMENT_PAYFLOW = 0;
	var $ID_PAYMENT_MPESA 	= 0;
	
	var $currency_selector  	= '';
	var $guest_first_name 		= '';
	var $guest_last_name		= '';
	var $guest_identification_number;
	
	var $reserve_offer_id 	= 0;
	var $reserve_room_id	= 0;
	var $reserve_current	= 0;
	var $items_reserved		= array();
	
	var $payment_name		= '';
	var $payment_tel		= '';
	var $payment_code		= '';
	var $need_preauthorized	= false;
	
	var $key_control_reservation = '';
	
	var $voucher = '';
	var $mediaReferer = '';
	var $subscribeToNewsletter='';
	var $company_name;
	var $edit_mode;
	var $discount_code;
	var $room_guests = 0; 
	
	var $guest_type = 0;
	
	var $guest_types = array();
	
	var $GuestsDetails		= '';
	
	var $showDiscounts = false;
	
	function __construct($hotel_id = 0 )
	{
		parent::__construct();
		
			
		$this->hotel_id 					= $hotel_id;
		$this->reservation_status		= RESERVED_ID;
		$this->confirmation_id				= 0;
		$this->tip_oper						= JRequest::getInt( 'tip_oper');
		$this->tmp							= JRequest::getInt( 'tmp');
		$this->year_start					= date('Y');
		$this->month_start					= date('m');
		$this->day_start					= date('d');
		$this->year_end						= date('Y', strtotime('+ 1 day'));
		$this->month_end					= date('m', strtotime('+ 1 day'));
		$this->day_end						= date('d', strtotime('+ 1 day'));
		$this->rooms						= 0;
		$this->guest_adult					= 2;
		$this->guest_child					= 0;
		$this->state_name					= '';
		$this->coupon_code					= '';
		$this->email_confirmation			= '';
		$this->option_ids					= array();
		$this->room_available_ids 			= array();
		$this->room_feature_available_ids	= array();
		$this->room_ids 					= array();
		$this->package_ids					= array();
		$this->airport_transfer_type_id 	= 0;
		$this->airport_transfer_date		= '';
		$this->airline_id					= 0;
		$this->airport_transfer_time_hour	= 0;
		$this->airport_transfer_time_min	= 0;
		$this->airport_transfer_guest		= '';
		$this->airport_transfer_flight_nr	= 0;
		$this->arrival_option_ids			= array();
		$this->extraOptionIds				= array();
		$this->airport_airline_ids			= array();
		$this->airport_transfer_type_ids	= array();
		$this->airport_transfer_dates		= array();
		$this->airport_transfer_time_hours	= array();
		$this->airport_transfer_time_mins	= array();
		$this->airport_transfer_flight_nrs	= array();
		$this->airport_transfer_guests		= array();
		
		$this->package_day					= array();
		$this->payment_processor_sel_id		= 0;
		$this->payment_processor_sel_type	= '';
		
		$this->currency_selector			= '';
		$this->guest_first_name 			= array();
		$this->guest_last_name				= array();
		$this->guest_identification_number	= array();	
		$this->edit_mode = 0;	
		$this->discount_code= '';
		
		if(isset(JFactory::getUser()->id) && JFactory::getUser()->id != 0){
			$reservationInfo = $this->getTable('managehotelusers');
			$reservationInfo = $reservationInfo->getUserById(JFactory::getUser()->id);
			if(count($reservationInfo)>0){			
				$this->first_name				= $reservationInfo->first_name;
				$this->last_name				= $reservationInfo->last_name;
				$this->details					= '';
				$this->address					= $reservationInfo->address;
				$this->city						= $reservationInfo->city;
				$this->state_name				= $reservationInfo->state_name;
				$this->country					= $reservationInfo->country;
				$this->postal_code				= $reservationInfo->postal_code;
				$this->tel						= $reservationInfo->tel;
				$this->email					= $reservationInfo->email;
				$this->conf_email				= $reservationInfo->email;
				$this->card_type_id				= 0;
				$this->card_type_name			= '';
				$this->card_name				= '';
				$this->card_number				= '';
				$this->card_expiration_month 	= '';
				$this->card_expiration_year 	= '';
				$this->card_security_code		= '';
			}
			
		}
		else if(DSC_FILL_GUEST )
		{
			$this->first_name				= 'George';
			$this->last_name				= 'B';
			$this->details					= 'This is my test !';
			$this->address					= 'xx';
			$this->city						= 'xx';
			$this->state_name				= 'xx';
			$this->country					= 'xx';
			$this->postal_code				= '1';
			$this->tel						= 'xx';
			$this->email					= 'george.bara@gmail.com';
			$this->conf_email				= 'george.bara@gmail.com';
			$this->card_type_id				= 1;
			$this->card_type_name			= 'Visa';
			$this->card_name				= 'MASTER';
			$this->card_number				= '4111111111111111';
			$this->card_expiration_month 	= '8';
			$this->card_expiration_year 	= '2012';
			$this->card_security_code 		= '';
		}
		else
		{
			$this->first_name				= '';
			$this->last_name				= '';
			$this->details					= '';
			$this->address					= '';
			$this->city						= '';
			$this->state_name				= '';
			$this->country					= '';
			$this->postal_code				= '';
			$this->tel						= '';
			$this->email					= '';
			$this->conf_email				= '';
			$this->card_type_id				= 0;
			$this->card_type_name			= '';
			$this->card_name				= '';
			$this->card_number				= '';
			$this->card_expiration_month 	= '';
			$this->card_expiration_year 	= '';
			$this->card_security_code		= '';
		}
		$this->itemFeatureOptionsAvailable	= array();
		$this->itemRoomsSelected			= array();
		$this->itemRoomsCapacity			= array();
		$this->itemRoomsNumbers				= array();
		$this->itemPackageNumbers			= array();
		$this->itemArrivalOptions			= array();
		$this->itemAirportTransferTypes		= array();
		
		$this->itemFeatures					= array();
		
		$this->itemCurrency 				= $this->getCurrency();
		
		$this->itemPaymentProcessors		= array();
		
		$this->payment_variables			= new stdClass;
		
		switch( $this->tip_oper )
		{
			case 1:
				//$this->itemFeatures				= $this->getFeatures($this->itemFeatureOptionsAvailable);
				break;
			default:
				break;
		}
		$this->itemAppSettings			= $this->getAppSettings(); 
		$this->is_enable_payment		= $this->itemAppSettings->is_enable_payment;
		
		$rowTable					= 	 $this->getTable('ConfirmationsPayments');
		$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
		$rowTablePayment->load( $this->getIDPaymentSettings( PREAUTHORIZATION_PAYMENT_ID) );
		if( isset($rowTablePayment->is_available) )
			$this->need_preauthorized		= $rowTablePayment->is_available;
	}
	
	
	function load($confirmation_id, $user, $currency_selector, $post = array() )
	{
		$userFilter = "";
		
		if(isset($user)){
			$userFilter = " AND c.email='$user'";
		}
		
		$query = 	" SELECT 
								c.*,
								#h.*,
								r.room_id as reserve_room_id,
								GROUP_CONCAT( DISTINCT CONCAT(r.nr_guests, '|', r.current) ORDER BY r.current) as room_guests,
								GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id, '|', r.current) ORDER BY r.current )												AS items_reserved,
								GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id) ORDER BY r.current )																AS room_ids,
								GROUP_CONCAT( DISTINCT CONCAT(rnd.offer_id,'|',rnd.room_id, '|',rnd.current, '|',rnd.room_number_number) ORDER BY r.current )				AS itemRoomsNumbers,
								GROUP_CONCAT( DISTINCT fo.option_id)																										AS option_ids,
								GROUP_CONCAT( DISTINCT CONCAT(p.offer_id,'|',p.room_id, '|',p.current, '|',p.package_id) ORDER BY r.current)								AS package_ids,
								GROUP_CONCAT( DISTINCT CONCAT(p.offer_id,'|',p.room_id, '|',p.current, '|',pd.package_id,'|', pd.package_data) ORDER BY r.current )			AS package_day,
								GROUP_CONCAT( DISTINCT CONCAT(p.offer_id,'|',p.room_id, '|',p.current, '|',p.package_id,'|', p.package_number) ORDER BY r.current )			AS itemPackageNumbers,
								GROUP_CONCAT( DISTINCT CONCAT(ao.offer_id, '|', ao.room_id, '|', ao.current, '|', ao.arrival_option_id, '|', 1 ) ORDER BY r.current )		AS arrival_option_ids,
								GROUP_CONCAT( DISTINCT CONCAT(eo.offer_id, '|', eo.room_id, '|', eo.current, '|', eo.extra_option_id, '|', 1,'|', eo.extra_option_persons,'|', eo.extra_option_days) ORDER BY r.current )		AS extraOptionIds,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_type_id ) ORDER BY r.current )		AS airport_transfer_type_ids,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airline_id ) ORDER BY r.current)						AS airport_airline_ids,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_flight_nr ) ORDER BY r.current)		AS airport_transfer_flight_nrs,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_date ) ORDER BY r.current)			AS airport_transfer_dates,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_time_hour ) ORDER BY r.current)		AS airport_transfer_time_hours,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_time_min ) ORDER BY r.current)		AS airport_transfer_time_mins,
								GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_guest ) ORDER BY r.current)			AS airport_transfer_guests,
								
								s.status_reservation_name				AS status_reservation_name,
								s.order									AS status_order,
								s.is_modif								AS status_is_modif							
							FROM #__hotelreservation_confirmations c	
							#inner join #__hotelreservation_hotels h on c.hotel_id= h.hotel_id 
							LEFT JOIN #__hotelreservation_confirmations_rooms 						r	ON ( r.confirmation_id 			= c.confirmation_id )
								
							LEFT JOIN #__hotelreservation_confirmations_rooms_numbers_dates			rnd	ON 
																										( 
																											rnd.confirmation_id 		= r.confirmation_id 
																											AND
																											rnd.offer_id 				= r.offer_id 
																											AND
																											rnd.room_id 				= r.room_id 
																											AND
																											rnd.current 				= r.current
																										)	
							LEFT JOIN #__hotelreservation_confirmations_feature_options 			fo	ON ( fo.confirmation_id 		= c.confirmation_id )
							LEFT JOIN #__hotelreservation_confirmations_rooms_packages 				p	ON ( p.confirmation_id 			= c.confirmation_id )	
							LEFT JOIN #__hotelreservation_confirmations_rooms_packages_dates		pd	ON ( pd.confirmation_id 		= c.confirmation_id )	
							LEFT JOIN #__hotelreservation_status_reservation 						s	ON ( c.reservation_status 	= s.reservation_status )
							LEFT JOIN #__hotelreservation_confirmations_rooms_arrival_options 		ao	ON ( ao.confirmation_id 		= c.confirmation_id )
							LEFT JOIN #__hotelreservation_confirmations_extra_options 				eo	ON ( eo.confirmation_id 		= c.confirmation_id )
							LEFT JOIN #__hotelreservation_confirmations_rooms_airport_transfer 		at	ON ( at.confirmation_id 		= c.confirmation_id )						
						
						".
						" WHERE c.confirmation_id = $confirmation_id 
   		 			 	  GROUP BY c.confirmation_id 
						";
			$this->_db->setQuery( $query );
			//dmp($query);
			$data 	= $this->_db->loadObject();
			//dmp($this->_db->getErrorMsg());
			//dmp($data);
			//$post	= array();
			// dmp($post['itemRoomsNumbers']);
			// dmp($post);
			// exit;
			if( count($data ) == 0 )
			{
				return false;
			}
			if( !isset( $currency_selector ) )
			{
				$this->hotel_id  				= $data->hotel_id; //setam hotel pentru curs
				$currency_selector 				= $this->getCurrency();
			}
			// exit;
			if(isset($currency_selector->description))
				$post['currency_selector'] =  $currency_selector->description;
			
			foreach( $data as $key => $value )
			{
				if( 1 || isset($this->$key ) )
				{
					//nu alteram valorile din post (pentru modificare rezervation)
					if( isset( $post[$key] ) )
					{	
						continue;
					}
					else
					{
						$post[$key] = $value;
						if( $key =='items_reserved' || $key =='itemRoomsNumbers' )
						{
							$ex = explode( ',', $value);
							$post[$key]= array();
							foreach( $ex as $e )
							{
								$post[$key][] = $e;
							}
							
							//dmp($post[$key]);
						}
					}
				}
				else
				{
					switch( $key )
					{
						case 'datas' :
							$post['year_start']				= date('Y', strtotime( $value ) );
							$post['month_start']			= date('m', strtotime( $value ) );
							$post['day_start']				= date('d', strtotime( $value ) );
							break;
						case 'datae' :
							$post['year_end']				= date('Y', strtotime( $value ) );
							$post['month_end']				= date('m', strtotime( $value ) );
							$post['day_end']				= date('d', strtotime( $value ) );
							break;
						case 'state' :
							$post['state_name']				= $value;
							break;
						default:
							// dmp($this->$key);
							// dmp($key .''.$value);
							break;
					}
				}
			}
			
			if(isset($post['room_guests'])){
				$room_numbers = explode(",", $post['room_guests']);
				$result = array();
				foreach($room_numbers as $room_number){
					$values = explode("|",$room_number);
					$result[] = $values[0];
				}
				$post['room_guests'] = implode(",",$result);
				//dmp($post['room_guests']);
			}
			
			if(!isset($post['guest_first_name'])){
				//load guest details
				$query 		= "SELECT * from #__hotelreservation_confirmations_guests where confirmation_id=".$confirmation_id." order by id";
				$guests 	= $this->_getList( $query );
				$first_name = array();
				$last_name 	= array();
				$identification_number = array();
				$index =0;
				foreach($guests as $guest)
				{
					$first_name[] 				= $guest->first_name;
					$last_name[] 				= $guest->last_name;
					$identification_number[] 	= $guest->identification_number;
					$index++;
				}
			
				$post['guest_first_name'] 				= $first_name;
				$post['guest_last_name'] 				= $last_name;
				$post['guest_identification_number'] 	= $identification_number;
			}
			// dmp($post);

			$this->store( $post );		
			// dmp($this->package_ids);
			// exit;
			return true;
			
	}

	
	function store( $post )
	{
		//dmp($post);
		$this->tip_oper					= JRequest::getInt( 'tip_oper');
		
		$this->key_control_reservation = isset($_SESSION['key_control_reservation']) ? $_SESSION['key_control_reservation'] : '';
				// dmp($_SESSION);	
		
		if( count($post) > 0 )
		{
			if( isset($post['reserve_room_id']) )
				$this->reserve_room_id	= $post['reserve_room_id'];
			if( isset($post['reserve_offer_id']) )
				$this->reserve_offer_id	= $post['reserve_offer_id'];
			if( isset($post['reserve_current']) )
				$this->reserve_current	= $post['reserve_current'];
			if( !isset($post['items_reserved']) )
				$post['items_reserved'] = array();
			if( isset($post['items_reserved']) )
			{
				$this->items_reserved	= $post['items_reserved'];
			}

			if(isset($this->items_reserved)){
				$sorted_items = array();
				foreach($this->items_reserved as $itemReserved){
					$values = explode("|",$itemReserved);
					
					$sorted_items[$values[2]-1] = $itemReserved;
				}
				ksort($sorted_items);
				$this->items_reserved = $sorted_items;
				//dmp($this->items_reserved);
			}
			
			$session = JFactory::getSession();
			$userData =  $_SESSION['userData'];
			/*
			if( isset($userData) )
			{
				$post["year_start"] 	= $userData->year_start;
				$post["month_start"] 	= $userData->month_start;
				$post["day_start"] 		= $userData->day_start;
				$post["year_end"] 		= $userData->year_end;
				$post["month_end"] 		= $userData->month_end;
				$post["day_end"] 		= $userData->day_end;
				$post["guest_adult"] 	= $userData->adults;
				$post["guest_child"] 	= $userData->childrens;
			}
			else
			{
				$post["year_start"] 	= '';
				$post["month_start"] 	= '';
				$post["day_start"] 		= '';
				$post["year_end"] 		= '';
				$post["month_end"] 		= '';
				$post["day_end"] 		= '';
				$post["guest_adult"] 	= '';
				$post["guest_child"] 	= '';
			}
			*/
							
			if( isset($post['reservation_status']) )
				$this->reservation_status	= $post['reservation_status'];
			if( isset($post['year_start']) )
				$this->year_start				= $post['year_start'];
			else if( isset($post['jhotelreservation_datas']) )
			{
				$this->year_start				= date('Y', strtotime($post['jhotelreservation_datas'] ) );
			}
			if( isset($post['month_start']) )
				$this->month_start				= $post['month_start'];
			else if( isset($post['jhotelreservation_datas']) )
			{
				$this->month_start				= date('m', strtotime($post['jhotelreservation_datas'] ) );
			}
			
			if( isset($post['day_start']) )
				$this->day_start				= $post['day_start'];
			else if(isset($post['jhotelreservation_datas']) )
			{
				$this->day_start				= date('d', strtotime($post['jhotelreservation_datas'] ) );
			}
			
			if( isset($post['year_end']) )
				$this->year_end	= $post['year_end'];
			else if(isset($post['jhotelreservation_datae']) )
			{
				$this->year_end				= date('Y', strtotime($post['jhotelreservation_datae'] ) );
			}
			
			if( isset($post['month_end']) )
				$this->month_end				= $post['month_end'];
			else if( isset($post['jhotelreservation_datae']) )
			{
				$this->month_end				= date('m', strtotime($post['jhotelreservation_datae'] ) );
			}
			
			if( isset($post['day_end']) )
				$this->day_end					= $post['day_end'];
			else if( isset($post['jhotelreservation_datae']) )
			{
				$this->day_start				= date('d', strtotime($post['jhotelreservation_datae'] ) );
			}
				
			if( isset($post['datas']) && strlen($post['datas'])==10 )
			{
				$post['datas'] = JHotelUtil::convertToMysqlFormat($post['datas']);
				$d = explode("-",$post['datas']);
				$this->day_start 	= $d[2];
				$this->month_start 	= $d[1];
				$this->year_start 	= $d[0];
			}
			
			if( isset($post['datae']) && strlen($post['datae'])==10 )
			{
				$post['datae'] = JHotelUtil::convertToMysqlFormat($post['datae']);
				$d = explode("-",$post['datae']);
				$this->day_end 		= $d[2];
				$this->month_end 	= $d[1];
				$this->year_end 	= $d[0];
			}
			
			if( isset($post['rooms']) )
				$this->rooms					= $post['rooms'];
			
			if( isset($post['confirmation_id']) )
				$this->confirmation_id			= $post['confirmation_id'];
			
			//dmp($post['guest_adult']);
			if( isset($post['room_guests']) ){
				$this->room_guests = explode(",", $post['room_guests']);
			}else if( isset($userData->roomGuests) ){
				$this->room_guests = $userData->roomGuests;
			}else{
				if($this->rooms>1){
					$this->room_guests = array();
					for($i=0;$i<$this->rooms;$i++){
						$this->room_guests[] = isset($post['guest_adult'])?$post['guest_adult']:2;
					}
				}else{
					$this->room_guests = isset($post['guest_adult'])?$post['guest_adult']:2;
				}
			}
			//dmp($post['room_guests']);
			//dmp($this->room_guests);
			
			if( isset($post['hotel_id']) )
				$this->hotel_id					= $post['hotel_id'];

			if( isset($post['voucher']) )
				$this->voucher					= $post['voucher'];
			if( isset($post['mediaReferer']) )
				$this->mediaReferer				= $post['mediaReferer'];

			if( isset($post['voucher']) )
				$this->voucher					= $post['voucher'];
				
			if( isset($post['discount_code']) )
				$this->discount_code			= $post['discount_code'];
			
			$this->tabId = JRequest::getVar("tabId");
			if(!isset($this->tabId) || $this->tabId == 0)
				$this->tabId = 1 ;
			
			if( isset($post['guest_adult']) )
				$this->guest_adult				= $post['guest_adult'];
			if( isset($post['guest_child']) )
				$this->guest_child				= $post['guest_child'];
			if( isset($post['coupon_code']) )
				$this->coupon_code				= $post['coupon_code'];
			
			//if( isset($post['option_ids']) )
			if( !isset($post['option_ids']) )
				$post['option_ids'] = '';
			if( isset($post['option_ids']) )
				$this->option_ids				= is_array($post['option_ids'])? 	$post['option_ids']  		: explode(',', $post['option_ids']);
			
			if( !isset($post['room_ids']) )
				$post['room_ids'] = '';
			if( isset($post['room_ids']) )
				$this->room_ids					= is_array($post['room_ids'])? 		$post['room_ids']  			: explode(',', $post['room_ids']);
			
			$this->prepareArray( 5, $post['package_ids'], $this->package_ids);
			//dmp($this->package_ids);
			$this->prepareArray( 0, $post['package_day'], $this->package_day);
			//if($this->confirmation_id == 0)
				$this->prepareArray( 2, $post['itemPackageNumbers'], 		$this->itemPackageNumbers);
			//else
			//	$this->itemPackageNumbers = $this->getPackageNumbers( $this->confirmation_id );
			
			
			$this->prepareArray( 1, $post['arrival_option_ids'], 			$this->arrival_option_ids);
			$this->prepareArray( 1, $post['extraOptionIds'], 				$this->extraOptionIds);
			$this->prepareArray( 4, $post['airport_airline_ids'], 			$this->airport_airline_ids);
			$this->prepareArray( 4, $post['airport_transfer_type_ids'], 	$this->airport_transfer_type_ids);
			$this->prepareArray( 4, $post['airport_transfer_dates'], 		$this->airport_transfer_dates);
			$this->prepareArray( 4, $post['airport_transfer_time_hours'], 	$this->airport_transfer_time_hours);
			$this->prepareArray( 4, $post['airport_transfer_time_mins'], 	$this->airport_transfer_time_mins);
			$this->prepareArray( 4, $post['airport_transfer_flight_nrs'], 	$this->airport_transfer_flight_nrs);
			$this->prepareArray( 4, $post['airport_transfer_guests'], 		$this->airport_transfer_guests);
	
			//dmp($this->extraOptionIds);
			//settings persons and days for extra
			foreach($this->extraOptionIds as &$extraOption){
				if($extraOption[5]>0 || $extraOption[6]>0)
					continue;
				if(isset($post["extra-option-days-".$extraOption[3]])){
					$extraOption[6] = $post["extra-option-days-".$extraOption[3]];
				}
				if(isset($post["extra-option-persons-".$extraOption[3]])){
					$extraOption[5] = $post["extra-option-persons-".$extraOption[3]];
				}
			}
			//dmp($this->extraOptionIds);
			
			
			//saving guest informations
			if( isset($post['payment_name']) )
				$this->payment_name					= $post['payment_name'];
			if( isset($post['payment_tel']) )
				$this->payment_tel					= $post['payment_tel'];
			if( isset($post['payment_code']) )
				$this->payment_code					= $post['payment_code'];

				
			if(isset($post['guest_first_name']))
				$this->guest_first_name = $post['guest_first_name'];
			if(isset($post['guest_last_name']))
				$this->guest_last_name = $post['guest_last_name'];
			if(isset($post['guest_identification_number']))
				$this->guest_identification_number = $post['guest_identification_number'];

			if( isset($post['company_name']) )
				$this->company_name				= $post['company_name'];
			if( isset($post['guest_type']) )
				$this->guest_type				= $post['guest_type'];
			if( isset($post['first_name']) )
				$this->first_name				= $post['first_name'];
			if( isset($post['last_name']) )
				$this->last_name				= $post['last_name'];
			if( isset($post['details']) )
				$this->details					= $post['details'];
			if( isset($post['address']) )
				$this->address					= $post['address'];
			if( isset($post['city']) )
				$this->city						= $post['city'];
			if( isset($post['state_name']) )
				$this->state_name				= $post['state_name'];
			if( isset($post['country']) )
				$this->country					= $post['country'];
			if( isset($post['postal_code']) )
				$this->postal_code				= $post['postal_code'];
			if( isset($post['tel']) )
				$this->tel						= $post['tel'];
			if( isset($post['email']) )
				$this->email					= $post['email'];
			if( isset($post['conf_email']) )
				$this->conf_email				= $post['conf_email'];
			if( isset($post['card_type_id']) )
				$this->card_type_id				= $post['card_type_id'];
			if( isset($post['card_name']) )
				$this->card_name				= $post['card_name'];
			if( isset($post['card_number']) )
				$this->card_number				= $post['card_number'];
			if( isset($post['card_expiration_month']) )
				$this->card_expiration_month	= $post['card_expiration_month'];
			if( isset($post['card_expiration_year']) )
				$this->card_expiration_year		= $post['card_expiration_year'];
			if( isset($post['card_security_code']) )
				$this->card_security_code		= $post['card_security_code'];
			if( isset($post['card_type']) )
				$this->card_type				= $this->getCardTypeById($this->card_type_id);
			if( isset($post['confirmation_payment_status']) )
				$this->confirmation_payment_status	= $post['confirmation_payment_status'];
			if( isset($post['email_confirmation']) )
				$this->email_confirmation		= $post['email_confirmation'];	
			if( isset($post['room_available_ids']) )
				$this->room_available_ids		= is_array($post['room_available_ids'])? 	$post['room_available_ids']  	: explode(',', $post['room_available_ids']);
			if( isset($post['room_feature_available_ids']) )
				$this->room_feature_available_ids		= is_array($post['room_feature_available_ids'])? 	$post['room_feature_available_ids']  	: explode(',', $post['room_feature_available_ids']);
			
			$this->itemCurrency 				= $this->getCurrency();
			if( isset($post['currency_selector']) )
				$this->currency_selector		= $post['currency_selector'];
			else if(( !isset($post['currency_selector'] )  || $post['currency_selector'] == '' ) & $this->itemCurrency)
				$this->currency_selector= $this->itemCurrency->description ;
			if( isset($post['itemRoomsCapacity']) )
			{
				if( is_array( $post['itemRoomsCapacity']))
				{
					$ex = array();
					foreach(  $post['itemRoomsCapacity'] as $v )
					{
						$ex1 = explode('|', $v);
						if( count($ex1) == 3 )
							$ex[ $ex1[0] ] = array($ex1[1], $ex1[2]);
					}
					$this->itemRoomsCapacity		= $ex;
				}
				else
				{
					$ex = explode(',', $post['itemRoomsCapacity']);
					foreach( $ex as $value )
					{
						$ex1 = explode('|', $value );
						if( count($ex1) ==3 )
							$this->itemRoomsCapacity[$ex1[0]]		= array($ex1[1],$ex1[2]);
					}
				}
			}
			else if($this->confirmation_id > 0)
			{
				$this->itemRoomsCapacity = $this->getRoomsCapacity( $this->confirmation_id );
			}
			
			
			if( isset($post['itemArrivalOptions']) )
			{
				if( is_array( $post['itemArrivalOptions']))
				{
					$this->itemArrivalOptions		= $post['itemArrivalOptions'];
				}
				else
				{
					$ex = explode(',', $post['itemArrivalOptions']);
					$this->itemArrivalOptions			= $ex;
				}
			}
			// dmp($post);
			if( isset($post['itemRoomsNumbers']) )
			{
				if( is_array( $post['itemRoomsNumbers']))
				{
					$ex = array();
					foreach(  $post['itemRoomsNumbers'] as $v )
					{
						$ex1 = explode('|', $v);
						if( count($ex1) == 4 )
							$ex[ $ex1[0].'|'.$ex1[1].'|'.$ex1[2] ] = $ex1[3];
					}
					$this->itemRoomsNumbers		= $ex;
				}
				else
				{
					$ex = explode(',', $post['itemRoomsNumbers']);
					foreach( $ex as $value )
					{
						$ex1 = explode('|', $value );
						if( count($ex1) == 4 )
							$this->itemRoomsNumbers[ $ex1[0].'|'.$ex1[1].'|'.$ex1[2] ]		= $ex1[1];
					}
				}
			}
			// dmp($this->key_control_reservation);
			// dmp($_SESSION);
			
			if( isset($post['payment_processor_sel_id']) )
				$this->payment_processor_sel_id	= $post['payment_processor_sel_id'];
			
			if( isset($post['payment_processor_sel_type']) )
				$this->payment_processor_sel_type	= $post['payment_processor_sel_type'];
			if( isset($post['subscribeToNewsletter']) )
				$this->subscribeToNewsletter				= $post['subscribeToNewsletter'];

		
			$this->itemHotelSelected			=$this->getHotelByID($this->hotel_id);
			
			$this->guest_types[] = JHTML::_('select.option',1,JText::_('LNG_GUEST_TYPE_1',true));
			$this->guest_types[] = JHTML::_('select.option',2,JText::_('LNG_GUEST_TYPE_2',true));
			$this->guest_types[] = JHTML::_('select.option',3,JText::_('LNG_GUEST_TYPE_3',true));
				
			$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
			$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);
			
			switch( $this->tip_oper )
			{
				case -1:
					$this->key_control_reservation 			= $this->getUniqueCode();
					$_SESSION['key_control_reservation'] 	= $this->key_control_reservation;
					$adults = $this->room_guests;
					if( is_array($this->room_guests) ){
						$adults= $this->room_guests[$this->getReservedItems()];
					}
					
					$this->roomsAvailable	= $this->getHotelRooms($this->hotel_id, $datas, $datae,array(), $adults, $adults);
					$this->offersAvailable	= $this->getHotelOffers($this->hotel_id, $datas, $datae,array(), $adults, $adults);
					$this->checkRoomAvailability($this->roomsAvailable,$this->items_reserved, $this->hotel_id, $datas ,$datae);
					$this->checkRoomAvailability($this->offersAvailable,$this->items_reserved, $this->hotel_id, $datas ,$datae);
					$this->setRoomDisplayPrice($this->roomsAvailable);
					$this->setOfferDisplayPrice($this->offersAvailable);
					$this->cleanAllUnwantedReservations();
					break;
					
				case 2:
					$this->key_control_reservation 			= $this->getUniqueCode();
					$_SESSION['key_control_reservation'] 	= $this->key_control_reservation;
					$adults = $this->room_guests;
					if( is_array($this->room_guests) ){
						$adults= $this->room_guests[$this->getReservedItems()];
					}
					$this->roomsAvailable		= $this->getHotelRooms($this->hotel_id, $datas, $datae,array(), $adults, $adults);
					$this->offersAvailable		= $this->getHotelOffers($this->hotel_id, $datas, $datae,array(), $adults, $adults);
					$this->setRoomDisplayPrice($this->roomsAvailable);
					$this->setOfferDisplayPrice($this->offersAvailable);
					
					$this->itemPackages				= $this->getPackages( $this->package_ids);
					$this->itemArrivalOptions		= $this->getArrivalOptions($this->arrival_option_ids);
					$this->itemAirportTransferTypes	= $this->getAirportTransferTypes($this->airport_transfer_type_ids);
					$this->itemArrivalAirlines		= $this->getArrivalAirlines($this->airport_airline_ids);
					break;
					
				case 3:
					$this->key_control_reservation 			= $this->getUniqueCode();
					$_SESSION['key_control_reservation'] 	= $this->key_control_reservation;
					
					$roomReserved = $this->items_reserved[$this->getReservedItems()-1];
					$roomReservedInfo = explode("|",$roomReserved);
					$this->extraOptions = $this->getHotelExtraOptions($this->hotel_id, $datas, $datae, array(), $roomReservedInfo[1], $roomReservedInfo[0]);
					
					$this->itemPackages				= $this->getPackages($this->package_ids);
					$this->itemArrivalOptions		= $this->getArrivalOptions($this->arrival_option_ids);
					$this->itemAirportTransferTypes	= $this->getAirportTransferTypes($this->airport_transfer_type_ids);
					$this->itemArrivalAirlines		= $this->getArrivalAirlines($this->airport_airline_ids);
					//$this->itemPaymentProcessors	= $this->getPaymentProcessors();
					break;
				case 4:
					//dmp($this->items_reserved);
					$this->itemTypeCards			= $this->getTypeCards(); 
					$this->roomsAvailable			= array();//$this->getRoomsAvailable();//$this->room_available_ids);
					$this->itemRoomsSelected		= array();
					$sorted_items = array();
					foreach($this->items_reserved as $room_reserved){
						//dmp($room_reserved);
						$values = explode("|",$room_reserved);
						$nr_guests= 0;
						//dmp($values);
						//dmp($this->room_guests	);
						if( isset($this->room_guests) ){
							$nr_guests= $this->room_guests[$values[2]-1];
						}
						//dmp($values);
						$itemSelected = null;
						if($values[0]==0){
							$itemSelected = $this->getHotelRooms($this->hotel_id, $datas, $datae,array($values[1]),$nr_guests,0);
						}else{
							$itemSelected = $this->getHotelOffers($this->hotel_id, $datas, $datae,array($room_reserved),$nr_guests,0);
						}
						$this->itemRoomsSelected[$values[2]-1] = $itemSelected[0];
						$sorted_items[$values[2]-1] = $room_reserved;
					}
					ksort($this->itemRoomsSelected);
					ksort($sorted_items);
					
					foreach($this->itemRoomsSelected as $index=>$item){
						$item->current= $index+1;
					}
					
					$this->items_reserved =$sorted_items;
					$this->itemPackages				= $this->getPackages($this->package_ids);
					$this->itemArrivalOptions		= $this->getArrivalOptions($this->arrival_option_ids);
					if(isset($this->extraOptionIds) && count($this->extraOptionIds)>0){
						$this->extraOptions = $this->getHotelExtraOptions($this->hotel_id, $datas, $datae, $this->extraOptionIds, $this->reserve_room_id, $this->reserve_offer_id);
					}
					$this->itemAirportTransferTypes	= $this->getAirportTransferTypes($this->airport_transfer_type_ids);
					$this->itemArrivalAirlines		= $this->getArrivalAirlines($this->airport_airline_ids);
					$this->itemFeatureOptions		= $this->getFeatureOptions($this->option_ids);
					$this->itemTaxes				= $this->getTaxes();
					$this->itemPayments				= $this->getConfirmationPayments();
					$this->itemPaymentProcessors	= $this->getPaymentProcessors();
					$this->Guest_Details			= $this->getGuestDetails();
					$this->countries				= $this->getCountries();
					
					//dmp($this->itemRoomsSelected);		
					foreach( $this->itemRoomsSelected as $room )
					{
						if($room->hasDiscounts){
							$this->showDiscounts = true;
						}
						
						if( !isset($this->itemRoomsCapacity[ $room->room_id ]) || $this->itemRoomsCapacity[ $room->room_id ][1] == 0 )
							continue;

						
						//$this->rooms 		+= $this->itemRoomsCapacity[ $room->room_id ][1];
						//$this->guest_adult 	+= $this->itemRoomsCapacity[ $room->room_id ][1];// * $room->room_capacity;
					}
					
					if($this->itemAppSettings->is_enable_payment )
						$this->Payment_Information		= $this->getPaymentInformation();
					else 
						$this->Payment_Information		= '';
					
					$this->Reservation_Details		= $this->getReservationDetails($this, true);
					$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
					//$this->itemPaymentProcessors	= $this->getPaymentProcessors();
					
					
					break;
				case 5:

					$this->itemTypeCards			= $this->getTypeCards(); 
					$this->card_type_name			= $this->getCardTypeById($this->card_type_id); 
					$this->roomsAvailable		= array();//$this->getRoomsAvailable();//$this->room_available_ids);
					$this->itemRoomsSelected		= array();
					$sorted_items = array();
					foreach($this->items_reserved as $room_reserved){
						//dmp($room_reserved);
						$values = explode("|",$room_reserved);
						$nr_guests= 0;
						//dmp($values);
						//dmp($this->room_guests	);
						if( isset($this->room_guests) ){
							$nr_guests= $this->room_guests[$values[2]-1];
						}
						
						$itemSelected = null;
						if($values[0]==0){
							$itemSelected = $this->getHotelRooms($this->hotel_id, $datas, $datae,array($values[1]),$nr_guests,0);
						}else{
							$itemSelected = $this->getHotelOffers($this->hotel_id, $datas, $datae,array($room_reserved),$nr_guests,0);
						}
						$this->itemRoomsSelected[$values[2]-1] = $itemSelected[0];
						$sorted_items[$values[2]-1] = $room_reserved;
					}
					ksort($this->itemRoomsSelected);
					ksort($sorted_items);
					
					foreach($this->itemRoomsSelected as $index=>$item){
						$item->current= $index+1;
					}
					//dmp($this->itemRoomsSelected);
					$this->items_reserved =$sorted_items;
					$this->itemPackages				= $this->getPackages($this->package_ids);;
					$this->itemArrivalOptions		= $this->getArrivalOptions($this->arrival_option_ids);
					$this->extraOptions 			= $this->getHotelExtraOptions($this->hotel_id, $datas, $datae, $this->extraOptionIds, $this->reserve_room_id, $this->reserve_offer_id);
						
					$this->itemAirportTransferTypes	= $this->getAirportTransferTypes($this->airport_transfer_type_ids);
					$this->itemArrivalAirlines		= $this->getArrivalAirlines($this->airport_airline_ids);

					$this->itemFeatureOptions		= $this->getFeatureOptions($this->option_ids);
					$this->itemTaxes				= $this->getTaxes();
					$this->itemPayments				= $this->getConfirmationPayments();
					$this->itemPaymentProcessors	= $this->getPaymentProcessors();
					$this->countries				= $this->getCountries();

					// $this->itemRoomsDiscounts		= $this->getRoomsDiscounts();
					
					// $this->rooms 					= 0;
					//$this->guest_adult  			= 0;
					foreach( $this->itemRoomsSelected as $key_room => $room )
					{
						if( !isset($this->itemRoomsCapacity[ $room->room_id ]) 
							|| 
							count($this->itemRoomsCapacity[ $room->room_id ]) == 0 
							|| 
							$this->itemRoomsCapacity[ $room->room_id ][1] == 0 
						)
							continue;
							
						//$this->rooms 		+= $this->itemRoomsCapacity[ $room->room_id ][1];
						//$this->guest_adult 	+= $this->itemRoomsCapacity[ $room->room_id ][1] ;//* $room->room_capacity;
					}
					$this->Reservation_Details		= $this->getReservationDetails($this, true);
					$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
					$this->Guest_Details			= $this->getGuestDetails();
					if($this->itemAppSettings->save_all_guests_data )
						$this->GuestsDetails		= $this->prepareGuestDetails();
					else
						$this->GuestsDetails		= '';

					
					if($this->itemAppSettings->is_enable_payment )
						$this->Payment_Information		= $this->getPaymentInformation();
					else 
						$this->Payment_Information		= '';
					
					$this->Confirmation				= $this->getConfirmation();
					
					
					$this->itemPaymentProcessors	= $this->getPaymentProcessors();
					
					break;
			
			}
		}
		
	}
	
	
	function &getTypeCards()
	{
		// Load the data

		$query = ' SELECT * FROM #__hotelreservation_type_cards INNER JOIN #__hotelreservation_applicationsettings ON FIND_IN_SET( type_card_id, card_type_ids)';
		//$this->_db->setQuery( $query );
		$type_cards = $this->_getList( $query );
		
		return $type_cards;
	}

	function checkExistReservation()
	{
		if( strlen($this->email) == 0 )
			return false;
			
			
		//dmp($this->key_control_reservation);

		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);

		$this->cleanSameReservationPending( $this->email, $datas, $datae, $this->key_control_reservation );

		$query = " 
						SELECT * FROM #__hotelreservation_confirmations
						WHERE 
							email = '".$this->email."'
							AND
							datas = '".$datas."'
							AND
							datae = '".$datae."'
							AND
							hotel_id = '".$this->hotel_id."'
							AND key_control_reservation = '".$this->key_control_reservation."'
					";
		
		//$ret = $this->_getList( $query );
		//$this->_db->setQuery( $query );
		//$type_cards = $this->_getList( $query );
		// dmp($query);
		// exit;
		return count( $this->_getList( $query ) ) > 0 ? true : false;
	}
	
	function &getCardTypeById($card_type_id)
	{
		// Load the data

		$query = " SELECT description FROM #__hotelreservation_type_cards INNER JOIN #__hotelreservation_applicationsettings ON FIND_IN_SET( type_card_id, card_type_ids) WHERE type_card_id = $card_type_id ";
		$this->_db->setQuery( $query );
		$type_cards = $this->_db->loadObject();

		return $type_cards->description;
	}

	
	function &getCurrency()
	{
		// Load the data

		$query = ' 	SELECT 
						description, currency_id, currency_symbol
					FROM  #__hotelreservation_hotels 
					INNER JOIN #__hotelreservation_currencies USING( currency_id )
					WHERE hotel_id = "'.$this->hotel_id.'"';
					
		$this->_db->setQuery( $query );
		$currency = $this->_db->loadObject();

		return $currency;
	}
	
	function &getCurrencyName($id)
	{
		// Load the data

		$query = " SELECT description, currency_id FROM #__hotelreservation_currencies WHERE currency_id =$id ";
		$this->_db->setQuery( $query );
		$currency = $this->_db->loadObject();

		return $currency;
	}

	
	function getCountries(){
		$query = ' SELECT * FROM #__hotelreservation_countries';
		$this->_db->setQuery( $query );
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
	
	function &getHotelByID($id)
	{
		// Load the data
		$hotel = null;
		if($id==0)
			return $hotel;
		//$query = ' SELECT * FROM #__hotelreservation_hotels WHERE hotel_id = '.$id.' AND is_available = 1 ORDER BY hotel_name';
		$query = ' SELECT 
						h.*,
						c1.country_name,
						c2.description	AS hotel_currency
					FROM #__hotelreservation_hotels 			h
					LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
					LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
					
					WHERE h.hotel_id = '.$id.' AND h.is_available = 1 
					';
		$this->_db->setQuery( $query );
		$hotel = $this->_db->loadObject();
		
		$hotel->hotel_name = stripslashes($hotel->hotel_name);
		
		$hotel->pictures	= array();
					
		$query = "  SELECT	*
						FROM #__hotelreservation_hotel_pictures 
						WHERE hotel_id = ".$id." AND hotel_picture_enable = 1
						ORDER BY hotel_picture_id
						 ";
		//dmp($query);
		$hotel->pictures = $this->_getList( $query );
		
		$hotel->facilities = array();
		$query = "  SELECT	hf.*
								FROM #__hotelreservation_hotel_facilities hf
								inner join  #__hotelreservation_hotel_facility_relation hfc on hf.id = hfc.facilityId
								WHERE hfc.hotelId = ".$id." 
								ORDER BY hf.name";								 
		//dmp($query);
		$hotel->facilities = $this->_getList( $query );
		
		$hotel->types = array();
		$query = "  SELECT	hf.*
						FROM #__hotelreservation_hotel_types hf
						inner join  #__hotelreservation_hotel_type_relation hfc on hf.id = hfc.typeId
						WHERE hfc.hotelId = ".$id."
						ORDER BY hf.name";
		//dmp($query);
		
		$hotel->types = $this->_getList( $query );
		//dmp($hotel->types);
		
		$hotel->reviewAnwersScore =  $this->getHotelReviewScore($id);
		
		$hotel->reviews = $this->getHotelReviews($id);
		
		$informationsTable = $this->getTable('ManageHotelInformations');
		$hotel->informations =  $informationsTable->getHotelInformations($id);
		$cancellationText ='';
		if($hotel->informations->uvh_agree==1){
			$cancellationText = JText::_('LNG_CANCELATION_UVH',true).' ';
		}
		if(count($hotel->types)==0){
			$type = new stdClass();
			$type->id=0;
			$hotel->types[0]=$type;
		}
		//dmp($hotel->types);
		//dmp()
		$cancellationText = (isset($hotel->types) && $hotel->types[0]->id == PARK_TYPE_ID) ? "": $cancellationText.str_replace("<<days>>", $hotel->informations->cancellation_days, JText::_('LNG_CANCELLATION_RULE',true)).' ';
		
		$hotel->informations->cancellation_conditions = $cancellationText.$hotel->informations->cancellation_conditions;

		$informationsTable = $this->getTable('ManageHotelInformations');
		$hotel->paymentOptions =  $informationsTable->getHotelPaymentOptions($id);
		
		return $hotel;
	}
	
	function getNumberOfBookings($hoteId, $startDate, $endDate){
		$reserved_rooms = array();
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			
			$query = "select room_id,count(hc.rooms) as reserved_rooms from #__hotelreservation_confirmations hc
						inner join #__hotelreservation_confirmations_rooms hcr on hc.confirmation_id = hcr.confirmation_id
						where '".(date('Y-m-d', $d))."' between hc.datas and hc.datae and hc.hotel_id = $hoteId
						group by hcr.room_id";
			//dmp($query);
			$this->_db->setQuery($query);
			$reservationInfos = $this->_db->loadObjectList();
			//dmp($reservationInfos);
			if(count($reservationInfos) > 0){
				foreach($reservationInfos as $reservationInfo){
					if(isset($reservationInfo->reserved_rooms)){ 
						if(!isset($reserved_rooms[$reservationInfo->reserved_rooms]) 
								|| $reserved_rooms[$reservationInfo->reserved_rooms] < $reservationInfo->reserved_rooms){
							$reserved_rooms[$reservationInfo->room_id] = $reservationInfo->reserved_rooms;
						}
					}
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		
		return $reserved_rooms;
	}
	
	
	function getNumberOfBookingsPerDay($hoteId, $startDate, $endDate){
		$reservedRooms = array();

		$query = "select hcr.room_id, hcr.rooms, hcr.datas, hcr.datae 
				  from #__hotelreservation_confirmations_rooms hcr
				  left join #__hotelreservation_confirmations c on c.confirmation_id= hcr.confirmation_id 
				  where (hcr.datae >='$startDate' and hcr.datas <'$endDate') and c.reservation_status <> ".CANCELED_ID." and hcr.hotel_id = $hoteId";
		
		$this->_db->setQuery($query);
		$reservationInfos = $this->_db->loadObjectList();
		
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
				$dayString = date("Y-m-d", $d);
				foreach($reservationInfos as $reservationInfo){
					if( strtotime($reservationInfo->datas)<= $d && $d<strtotime($reservationInfo->datae) ){
						if(!isset($reservedRooms[$reservationInfo->room_id]) || !isset($reservedRooms[$reservationInfo->room_id][$dayString]) ){
							$reservedRooms[$reservationInfo->room_id][$dayString] = 0;
						}
						$reservedRooms[$reservationInfo->room_id][$dayString] = $reservedRooms[$reservationInfo->room_id][$dayString] +1;
					}
				}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}

		return $reservedRooms;
	}

	
	function getHotelAvailabilyPerDay($hotelId, $startDate, $endDate){
		$hotelTable	= 	 $this->getTable('hotels');
		$hotel = $hotelTable->getHotel($hotelId);
	
	
		$availability = array();
		
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			$available = true;
			
			if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>$d ){
				$available = false;
			}
			
			if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<$d ){
				$available = false;
			}
			
			$ignoredDays = explode(',',$hotel->ignored_dates);
			if(count($ignoredDays)>0){
				foreach($ignoredDays as $ignoredDay){
			
					if( $d == strtotime($ignoredDay)){
						$available = false;
					}
				}
			}
			
			$availability[$dayString]=$available;
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		
		return $availability;
	}
	
	function isHotelAvailable($hotelId, $startDate, $endDate){
		$hotelTable	= 	 $this->getTable('hotels');
		$hotel = $hotelTable->getHotel($hotelId);
	
		if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>strtotime($startDate) ){
			return false;
		}
	
		if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<strtotime($endDate) ){
			return false;
		}
	
		//dmp($hotel);
		//dmp($startDate);
		//dmp($endDate);
	
		$ignoredDays = explode(',',$hotel->ignored_dates);
		//dmp($ignoredDays);
		if(count($ignoredDays)>0){
			foreach($ignoredDays as $ignoredDay){
	
				if( strtotime($startDate) <= strtotime($ignoredDay) && strtotime($ignoredDay) < strtotime($endDate)){
					return false;
				}
			}
		}
	
		return true;
	}
	
	

	
	
	
	
	
	public function checkRoomAvailability(&$rooms,$items_reserved, $hotel_id, $datas ,$datae){
		//number of reserved rooms for each room type
		$rooms_reserved = $this->getNumberOfBookingsPerDay($hotel_id, $datas ,$datae);
		//dmp("R");
		//dmp($rooms_reserved);
		$temporaryReservedRooms = $this->getReservedRooms($items_reserved);
		//dmp("T");
		//dmp($temporaryReservedRooms);
		
		foreach($rooms as $room){
		//	dmp("NR: ".$room->room_id." ".$room->nrRoomsAvailable);
			//dmp($room->daily);
			foreach($room->daily as $day){
				
				$totalNumberRoomsReserved = 0;
				//dmp($day["data"]);
				if(isset($rooms_reserved[$room->room_id][$day["data"]]))
					$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["data"]];
	
				if(isset($temporaryReservedRooms[$room->room_id])){
					$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
				}
				
				
				if($day["nrRoomsAvailable"] <= $totalNumberRoomsReserved )
				{
					$room->is_disabled = true;
				}
			}
		}
				
	}
	
	//deprecated
	function &getRoomsAvailable($ids=array(), $check_room_number = false, $is_all_rooms=true , $nr_guests=0)
	{
		// dmp($ids);
		$is_offers			= false;
		$is_rooms			= false;
		$room_available_ids = array();
		$result_rooms		= array();
		$rooms_numbers_already_selected		= array();
		
		foreach($this->itemRoomsSelected as $itemSelected){
			if(isset($itemSelected->room_number_number))
				$rooms_numbers_already_selected[$itemSelected->room_id][] = $itemSelected->room_number_number;
		}
		foreach( $ids as $id )
		{
			$ex 							= explode('|', $id);
			$room_available_ids[ $ex[2] ] 	= array( $ex[1], $ex[1]);
			
			if( $ex[0] > 0 )
				$is_offers 	= true;
			else
				$is_rooms 	= true;
		}
		
		if( $this->tip_oper > 3  && count( $room_available_ids ) == 0  & $this->confirmation_id == 0 )
		{
			return $result_rooms;
		}
		
		//trebuie sa intram cel putin o1 pentru a incarca rooms, cand tip_oper <=3
		if( count($room_available_ids) == 0 && $is_all_rooms == true )
		{
			$room_available_ids[] 	= array(0,0);
			$is_offers				= true;
			$is_rooms				= true;
		}
		
		//dmp($is_offers);
		//$this->itemAppSettings->is_enable_offers = false;
		// Load the data
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);
		
		//number of reserved rooms for each room type
		$rooms_reserved = $this->getNumberOfBookings($this->hotel_id, $datas ,$datae);
		
		$isHotelAvailable = true;
		if(!$this->isHotelAvailable($this->hotel_id, $datas,$datae)){
			$isHotelAvailable = false;
		}
		//dmp($room_available_ids);
		for( $i = ($this->itemAppSettings->is_enable_offers ? 0 : 1); $i <=1; $i ++ )
		{
			
			if( ($i==0 && $is_offers==false) || ($i==1 && $is_rooms == false) )
				continue;

			foreach( $room_available_ids as $k_room => $val_room )
			{
				$id_room	= $val_room[1];
				$query 		= " 
							
							SELECT 
								$k_room															AS `current`,
								r.*,
								
								r_n.numbers_available,
								r_n.room_number_reserved,
								r_n.room_number_skiped,
								''	AS room_preferences,
								''	AS info_price
							FROM
							(
								SELECT 
									".($i==0? " ho.offer_id " : "  0 ")."						AS offer_id,
									".($i==0? " ho.offer_code " : "''")."						AS offer_code,
									".($i==0? " ho.offer_name " : "''")."						AS offer_name,
									".($i==0? " ho.offer_description " : "''")."				AS offer_description,
									".($i==0? " ho.offer_content " : "''")."					AS offer_content,
									".($i==0? " ho.offer_other_info " : "''")."					AS offer_other_info,
									".($i==0? " ho.offer_datas " : "0000-00-00")."				AS offer_datas,
									".($i==0? " ho.offer_datae " : "0000-00-00")."				AS offer_datae,
									".($i==0? " ho.offer_order " : "0")."						AS offer_order,
									".($i==0? " ho.offer_min_nights " : "0")."					AS offer_min_nights,
									".($i==0? " ho.offer_max_nights " : "0")."					AS offer_max_nights,
									".($i==0? " ho.offer_day_1 " : "0")."						AS offer_day_1,
									".($i==0? " ho.offer_day_2 " : "0")."						AS offer_day_2,
									".($i==0? " ho.offer_day_3 " : "0")."						AS offer_day_3,
									".($i==0? " ho.offer_day_4 " : "0")."						AS offer_day_4,
									".($i==0? " ho.offer_day_5 " : "0")."						AS offer_day_5,
									".($i==0? " ho.offer_day_6 " : "0")."						AS offer_day_6,
									".($i==0? " ho.offer_day_7 " : "0")."						AS offer_day_7,
									".($i==0? " ho.offer_reservation_cost_val " : "0")."		AS offer_reservation_cost_val,
									".($i==0? " ho.offer_reservation_cost_proc " : "0")."		AS offer_reservation_cost_proc,
									".($i==0? " ho.public " : "0")."	AS public,
									r.*,
									h.reservation_cost_val										AS reservation_cost_val,
									h.reservation_cost_proc										AS reservation_cost_proc,
									".
									(
										$i==1 ?
										"0"
										:
										" 
										IF(hod.offer_price_extranights <> 0,1,0) 
										"
									)
									."															AS is_extra_nights
								FROM #__hotelreservation_rooms r
								INNER JOIN #__hotelreservation_hotels 				h		ON h.hotel_id = r.hotel_id
								".(
									$i==0?
									"
									INNER JOIN #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
									INNER JOIN #__hotelreservation_offers		 			ho 		ON hor.offer_id 	= ho.offer_id
									INNER JOIN #__hotelreservation_offers_rooms_price 	hod 	ON ( hod.room_id	= hor.room_id AND hod.offer_id 	= hor.offer_id)
									"
									:
									""
								)."
								WHERE 
									r.is_available = 1
									AND
									r.hotel_id = '".$this->hotel_id."'
									".
									(
										$i==0?
										"
										AND
										ho.is_available = 1
										AND
										IF(
											ho.offer_datasf <> '0000-00-00'
											AND
											ho.offer_dataef <> '0000-00-00',
											DATE(now()) BETWEEN ho.offer_datasf  AND ho.offer_dataef,
											IF( 
												ho.offer_datasf <> '0000-00-00',
												DATE(now()) >= ho.offer_datasf,
												DATE(now()) <=ho.offer_dataef
											)
										)	
										#AND
										#'$datas' BETWEEN ho.offer_datas AND ho.offer_datae  
										#AND
										#( '$datae' BETWEEN ho.offer_datas AND ho.offer_datae) 
										"
										:
										""
									)
									."
								".
								(
									$id_room > 0?
									"AND r.room_id = $id_room "
									:
									""
								)."
								ORDER BY offer_order
							) r
							LEFT JOIN 
							(
								SELECT 
									r_n.room_id,
									c_r_n.room_number_reserved,
									GROUP_CONCAT( r_n.room_number_number ORDER BY r_n.room_number_number )	AS numbers_available,
									room_number_skiped 
								FROM
								(	
									#this is select for available number of rooms available, skiped etc
									SELECT 
										r_n.*,
										SUM( 	
											IF( 
												ISNULL(r_n_d_i.room_number_date_data), 
												0, 
												IF(r_n_d_i.room_number_date_data BETWEEN '$datas'  AND  '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."',1,0) 
											) 
										)												AS room_number_skiped
									FROM #__hotelreservation_rooms 									r
									INNER JOIN 	#__hotelreservation_rooms_numbers					r_n			ON ( r.room_id = r_n.room_id )
									LEFT JOIN 	#__hotelreservation_rooms_numbers_date_ignored		r_n_d_i		ON ( r_n.room_number_id = r_n_d_i.room_number_id )
									#WHERE 
									#	r_n.room_number_datas <= '$datas' 
									#	AND 
									#	IF( r_n.room_number_datae ='0000-00-00', 1, r_n.room_number_datae >=  '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."' )
										
										
									GROUP BY r.room_id, r_n.room_number_id
									#HAVING room_number_skiped = 0
									#~this is select for available number of rooms available
								) r_n
								LEFT JOIN
								(
									SELECT 
										hcrdn.room_id,
										hcrdn.room_number_number,
										COUNT(hcrdn.room_number_number)			AS room_number_reserved,
										count(hc.rooms) as reserved_rooms
									FROM #__hotelreservation_confirmations_rooms_numbers_dates	hcrdn
									INNER JOIN #__hotelreservation_confirmations 				hc			
																					ON hc.confirmation_id = hcrdn.confirmation_id
									WHERE 
										hcrdn.room_number_data BETWEEN '$datas' AND '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."'
										AND
										hc.reservation_status <> ".CANCELED_ID."
										AND
										hc.confirmation_id <> ".$this->confirmation_id."
									GROUP BY room_id , room_number_number
									#HAVING room_number_reserved = 0
								) c_r_n 
									ON ( r_n.room_id = c_r_n.room_id AND r_n.room_id = c_r_n.room_id )
								GROUP BY room_id
							) r_n	USING(room_id)
							WHERE NOT ISNULL(r_n.numbers_available) AND r_n.numbers_available > 0 
							ORDER BY room_order, room_name 
						";
					$rooms = $this->_getList( $query );
					//dmp($rooms);
				 //dmp( ($query) );
				if(count($rooms)>0)
				foreach( $rooms as $key => $value )
				{
					$room_nr_availables_tmp = explode(',',$value->numbers_available);
					// if( count($room_nr_availables_tmp) <= $value->room_number_reserved)
						// continue;
					//dmp( $value->numbers_available.' >> '.$value->room_number_reserved);
								
					$available_numbers 	= array();
					//dmp($value);
					$daily 				= array();
					$numbers			= array();
					$arr_dates			= array();
					
					$number_days 		= (strtotime($datae) - strtotime($datas) ) / 	( 60 * 60 * 24) ;
					$value->room_number_reserved = $value->room_number_reserved/$number_days;
					
					$number_persons	= $this->guest_adult;
					if($nr_guests > 0){
						$number_persons = $nr_guests;
					}
					
					$rooms[$key]->is_disabled = !$isHotelAvailable;

					
					//check if we have continue time period
					$no_continuitate = false;
					if( $value->offer_id > 0 )
					{
						/*for( $d = strtotime($datas);$d < strtotime($datae); )
						{
							$nr_d =  'offer_day_'.date("N", $d);
							// exit;
							if( $value->{ $nr_d } == 0 )
							{
								$no_continuitate = true;
								//$rooms[$key]->is_disabled = true;
								break;
							}
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						}*/
						//if( $no_continuitate == true )
						//	continue;
						
						//change the mechanism 
						$d = strtotime($datas);
						$nr_d =  'offer_day_'.date("N", $d);
						if( $value->{ $nr_d } == 0 ){
							$rooms[$key]->is_disabled = true;
						}
// 						dmp($value->offer_name);
// 						dmp($value->offer_datas);
// 						dmp($value->offer_datae);
// 						dmp("start");
						//check if offer is available in selected inverval
						for( $d = strtotime($datas);$d < strtotime($datae); ){
// 							dmp(date('Y-m-d', $d));
							if(!(strtotime($value->offer_datas) <= $d && $d<=strtotime($value->offer_datae) )){
 								//dmp("disable");
								$rooms[$key]->is_disabled = true;
								break;
							}
								
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						}
// 						dmp($rooms[$key]->is_disabled==true?"true":"false");
					}
					//~check if we have continue time period	
					$no_continuitate 		= false;
					$day_price 				= 0;
					$is_pers_price			= false;
					$offer_max_nights_tmp	= $value->offer_max_nights;
					
					for( $d = strtotime($datas);$d < strtotime($datae); )
					{
						$is_pers_price = ($value->pers_price ? true : false);
						
						switch( $value->type_price )
						{
							case 0:
								{
									switch( date("N", $d) )
									{
										case 1:
											$day_price = $value->room_price_1 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 2:
											$day_price = $value->room_price_2 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 3:
											$day_price = $value->room_price_3 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 4:
											$day_price = $value->room_price_4 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 5:
											$day_price = $value->room_price_5 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 6:
											$day_price = $value->room_price_6 * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 7:
											$day_price = $value->room_price_7 * ($is_pers_price==true? $number_persons :  1 );
											break;
									}
								}
								break;
							case 1: 
								{
									$day_price = $value->room_price * ($is_pers_price==true? $number_persons :  1 );
									break;
								}
							case 2:
								{
									switch( date("N", $d) )
									{
										case 7:
										case 1:
										case 2:
										case 3:
										case 4:
											$day_price = $value->room_price_midweek * ($is_pers_price==true? $number_persons :  1 );
											break;
										case 5:
										case 6:
											$day_price = $value->room_price_weekend * ($is_pers_price==true? $number_persons :  1 );
											break;
									}
									break;
								}
							
						}
						
						//dmp("apply single price");
						//if the price is per person apply single supplement , if is for room apply discount - only for single use
						if($number_persons==1){
							 if($is_pers_price==true){
								$day_price = $day_price + $value->single_supplement;
							}else{
								$day_price = $day_price - $value->single_discount;
							}
						}
						
						if( $i == 1 ) //ofertele nu sunt pe sezoane
						{
							//find season available price
							$sql_season_price = "
										SELECT 
											hrs.* 
										FROM #__hotelreservation_rooms_seasons 						hrs 
										LEFT JOIN #__hotelreservation_rooms_seasons_date_ignored	hrsdi	USING(room_season_id)
										WHERE 
											hrs.room_id = ".$value->room_id ." 
											AND 
											hrs.is_available = 1 
											AND 
											IF( room_season_datas <> '0000-00-00', '".date("Y-m-d", $d)."' >= room_season_datas , 1 )
											AND
											IF( room_season_datae <> '0000-00-00', '".date("Y-m-d", $d)."' <= room_season_datae , 1 )
											AND
											IF( ISNULL(hrsdi.room_season_date_ignored_id), 1, room_season_data <> '".date("Y-m-d", $d)."' )
										LIMIT 1
									";
							//dmp($sql_season_price);
							$value_season_price = $this->_getList( $sql_season_price );
							if( $value_season_price != null && count($value_season_price) == 1 )
							{
								$value->info_price 				= 'Season : '.$value_season_price[0]->room_season_name;
								//~find season available price
								// dmp($value_season_price);
								$is_pers_price					= $value_season_price[0]->room_season_pers_price;
								switch( $value_season_price[0]->room_season_type_price )
								{
									case 0:
										{
											switch( date("N", $d) )
											{
												case 1:
													$day_price = $value_season_price[0]->room_season_price_1 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 2:
													$day_price = $value_season_price[0]->room_season_price_2 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 3:
													$day_price = $value_season_price[0]->room_season_price_3 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 4:
													$day_price = $value_season_price[0]->room_season_price_4 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 5:
													$day_price = $value_season_price[0]->room_season_price_5 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 6:
													$day_price = $value_season_price[0]->room_season_price_6 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
												case 7:
													$day_price = $value_season_price[0]->room_season_price_7 * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
													break;
											}
										}
										break;
									case 1: 
										{
											$day_price = $value_season_price[0]->room_season_price * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);
											break;
										}
									case 2:
										{
											switch( date("N", $d) )
											{
												case 7:
												case 1:
												case 2:
												case 3:
												case 4:
													$day_price = $value_season_price[0]->room_season_price_midweek * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);;
													break;
												case 5:
												case 6:
													$day_price = $value_season_price[0]->room_season_price_weekend * ( $value_season_price[0]->room_season_pers_price ==1 ? $number_persons : 1);;
													break;
											}
											break;
										}
								}
							
							}
						}
						
						$day 	= array( 
											'is_offer'			=> $value->offer_id > 0 ? true : false, 
											'pers_price'		=> $is_pers_price,
											'data'				=> date( 'Y-m-d', $d),
											'price'				=> $day_price,
											'price_final'		=> $day_price,
											'info_price'		=> $value->info_price,
											'is_extra_nights'	=> $value->is_extra_nights,
											'numbers'			=> array(),
											'discounts'			=> array()
										);
						//dmp($value->is_extra_nights);
						
						if( $i == 0 )
						{
							$query = "  SELECT 
											d.*
								
										FROM #__hotelreservation_offers_rooms_price d
										WHERE 
											d.room_id = ".$value->room_id."
											AND
											d.offer_id = ".$value->offer_id."
							";
						}
						else
						{
							$query = "  SELECT 
											discount_id,
											discount_name,
											discount_datas,
											discount_datae,
											discount_value,
											minimum_number_days,
											minimum_number_persons,
											code
										FROM #__hotelreservation_discounts 
										WHERE 
											is_available = 1 
											AND
											FIND_IN_SET( ".$value->room_id.", discount_room_ids  )
											AND 
											'".date( 'Y-m-d', $d)."' BETWEEN discount_datas AND discount_datae 
											AND 
											IF( minimum_number_days > 0, minimum_number_days <= $number_days, 1 )
											AND 
											IF( minimum_number_persons > 0, minimum_number_persons <= $number_persons, 1 )								

										ORDER BY discount_datas 
										
									";
						}
						
						$res = $this->_getList( $query );
						if(count($res)>0)
						foreach( $res as &$v )
						{
							//dmp($v);
							if( $i==0 )
							{
								//verificam daca avem extranights, daca DA update all price cu offer extra nights
								//if( strtotime($value->offer_datae) < $d ) //merge daca se vrea zi->oferta, zi->oferta, zi->extranights
								//if( $value->is_extra_nights == 1 ) //daca nu intra in schema => pretul de extra nights
								//dmp($res);
								if( $offer_max_nights_tmp <= 0  ) //tot ce depaseste max nights => pretul de extra nights
								{
									//$v->offer_room_discount_val	 	= $v->offer_price_extranights;
// 									$v->offer_room_discount_type	= $v->offer_price_type_extranights;
									
									
									for( $j=1; $j<=7; $j++ )
									{
										$string_price = "price_".$j;
										$v->$string_price = $v->offer_price_extranights;
									}	
								
									
									$ws 	= explode(',', $v->week_types);
									foreach( $ws as $k => $w )
									{
										$ws[$k] = $v->offer_price_type_extranights;
									}	
									$v->week_types = implode($ws, ',');
									//dmp($v);
								}
								
								if( $v->offer_pers_price == 1 )
								{
									$day['pers_price'] = true;
								}
								
								
								/*
								if($v->offer_pers_price && $v->offer_room_discount_type!='%')
									$v->offer_room_discount_val= $v->offer_room_discount_val * $number_persons;
									*/
								
								for( $j=1; $j<=7; $j++ )
								{
									if($v->offer_pers_price){
										$string_price = "price_".$j;
										$v->$string_price = $v->$string_price* $number_persons;
									}
								}
								
								$v->week_types = explode(',', $v->week_types);
								$day[ 'discounts' ][] = $v;
							}else{
								
								if((isset($v->code) && $v->code == $this->discount_code) || !isset($v->code) || strlen($v->code)==0){
									$day[ 'discounts' ][] = $v;
								}
							}
						}
						$arr_dates[]	= date( 'Y-m-d', $d);
						$daily[] 		= $day;
						//dmp($day);
						// exit;
						
						$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						$offer_max_nights_tmp--;
					}
					
					if( $check_room_number )
					{
						$arr_conditions_numbers = array();
						foreach( $this->itemRoomsNumbers as $keyItemRoomNumber => $vRoom )
						{
							if( $keyItemRoomNumber  == $value->offer_id.'|'.$value->room_id.'|'.$value->current )
								$arr_conditions_numbers[] = " r.room_id = '".$value->room_id."' AND room_number_number = $vRoom";
						}
						//dmp($arr_conditions_numbers );
						$queryN 	= "  
											
											SELECT 
												r_n.*
											FROM
											(	
												#this is select for available number of rooms available
												SELECT 
													r.pers_price,
													r_n.*,
													SUM( 	
														IF( 
															ISNULL(r_n_d_i.room_number_date_data), 
															0, 
															IF(r_n_d_i.room_number_date_data BETWEEN '$datas'  AND  '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."',1,0)  
														) 
													)		AS room_number_skiped
												FROM #__hotelreservation_rooms 									r
												INNER JOIN 	#__hotelreservation_rooms_numbers					r_n			ON ( r.room_id = r_n.room_id )
												LEFT JOIN 	#__hotelreservation_rooms_numbers_date_ignored		r_n_d_i		ON ( r_n.room_number_id = r_n_d_i.room_number_id )
												WHERE 
													r.room_id =".$value->room_id."
													AND
													r_n.room_number_datas <= '$datas' 
													AND 
													IF( r_n.room_number_datae ='0000-00-00', 1, r_n.room_number_datae >=  '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."' )
													".(count($arr_conditions_numbers) > 0 ? " AND ( ( ".implode( ") OR (", $arr_conditions_numbers).") )" : "")."
													".(isset($rooms_numbers_already_selected[ $value->room_id ] )? " AND room_number_number NOT IN (". implode(',', $rooms_numbers_already_selected[ $value->room_id ] ).")" : "")."
												GROUP BY r.room_id, r_n.room_number_number
												#HAVING room_number_skiped = 0
												#~this is select for available number of rooms available
											) r_n
											LEFT JOIN
											(
												SELECT 
													hcrdn.room_id,
													hcrdn.room_number_number,
													COUNT(hcrdn.room_number_number)			AS room_number_reserved
												FROM #__hotelreservation_confirmations_rooms_numbers_dates	hcrdn
												INNER JOIN #__hotelreservation_confirmations 				hc			
																								ON hc.confirmation_id = hcrdn.confirmation_id
												WHERE 
													hcrdn.room_id 			= ".$value->room_id."
													AND
													hcrdn.room_number_data BETWEEN '$datas' AND '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."'
													AND
													hc.reservation_status <> ".CANCELED_ID."
													AND
													hc.confirmation_id <> ".$this->confirmation_id."
												GROUP BY room_id, room_number_number
												
											) c_r_n 
												ON ( r_n.room_id = c_r_n.room_id AND r_n.room_number_number = c_r_n.room_number_number )
											WHERE IF( ISNULL(room_number_reserved), 1, room_number_reserved=0)
											ORDER BY room_number_number 
											".($this->tip_oper>=4 ? "LIMIT ".$this->itemRoomsCapacity[$value->room_id][1]."" : "")."
								
									";
						// dmp($queryN);
						$number_liniars	= array();
						$resN = $this->_getList( $queryN );
						//dmp($value->offer_name);
						//dmp($resN);
						//dmp($rooms_numbers_already_selected);
						 if(count($resN) == 0 ){
							$rooms[$key]->is_disabled = true;
						 }
						 else
						foreach( $resN AS $valN )
						{
							if($this->isItemSelected($this->items_reserved, $value->offer_id, $value->room_id)){
								$rooms_numbers_already_selected[ $value->room_id ][] = $valN->room_number_number;
							}
							
							$available_numbers[] = array(
															'id'			=>  $valN->room_number_id,
															'nr'			=>	$valN->room_number_number,
															'price_1'		=> 	$valN->room_number_price_1 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_2'		=> 	$valN->room_number_price_2 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_3'		=> 	$valN->room_number_price_3 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_4'		=> 	$valN->room_number_price_4 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_5'		=> 	$valN->room_number_price_5 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_6'		=> 	$valN->room_number_price_6 * ( $valN->pers_price==1? $number_persons : 1 ),
															'price_7'		=> 	$valN->room_number_price_7 * ( $valN->pers_price==1? $number_persons : 1 )
														);
							

												
							foreach($arr_dates as $kDate => $str_data )
							{
								$disc 			= 0;
								$arr_disc		= array();
								$val			= $day_price;
								// dmp($daily[$kDate]);
								if( isset( $daily[$kDate][ 'discounts' ]) )
								{
									$val 	= &$daily[$kDate]['price_final'];
									foreach( $daily[$kDate][ 'discounts' ] as $d )
									{
										//dmp($d);
										if( $i == 0 )
										{
											$k = date('N', strtotime($daily[$kDate]['data']));
											// dmp($k);
											// dmp($d->week_types);
											// dmp($d->week_vals);
											
											$string_price ="price_".$k;
											$price_value = $d->$string_price;
											
											if( $d->week_types[$k-1] =='%'){
												$disc += $price_value;
											}else{
												$val = $price_value;
											}
											
											if($number_persons==1){
												if($d->offer_pers_price==1){
													$val = $val + $d->single_supplement;
												}else{ 
													$val = $val - $d->single_discount;
												}
											}
											
										}
										else
										{
											$disc += $d->discount_value;
										}
										$arr_disc[] = $d;
									}
								}
								// dmp($val);
								// dmp($disc);
								$price  = JHotelUtil::my_round($val - JHotelUtil::my_round($val * ($disc/100),2),2);
								// dmp($price);
								
								$daily[$kDate][ 'numbers' ][] = array(
																		'data'			=>	$str_data,
																		'id'			=>  $valN->room_number_id,
																		'nr'			=>	$valN->room_number_number,
																		'price'			=> 	$price,
																		'discounts'		=> 	$daily[$kDate][ 'discounts' ]
																);
								$rooms[$key]->room_number_number = $valN->room_number_number;
							}
							
						}
						// if( $this->tip_oper > 3)
						// {
							// dmp($daily);
							// exit;
						// }
						//~cautam liniaritate in numere de camere
						
					}
					// dmp($daily);
					// exit;
					$rooms[$key]->available_numbers		= $available_numbers;
					$rooms[$key]->daily_medium_prices 	= $this->getDailyMediumPrice( $i==0?true : false, $daily );
					$rooms[$key]->daily 				= $daily;
					
					
					
					if( count( $rooms[$key]->daily_medium_prices ) > 0 )
					{
						$total = 0;
						foreach( $rooms[$key]->daily_medium_prices as $p => $v )
						{
							$total += $p;
						}
						$rooms[$key]->room_average_price = JHotelUtil::fmt(JHotelUtil::my_round($total/count($rooms[$key]->daily_medium_prices),2),2);
					}
					else
					{
						$rooms[$key]->room_average_price = JHotelUtil::fmt($day_price,2);
						//dmp($day_price);
					}
					
					
					
					
					$query = "  SELECT 
									rf.is_multiple_selection, 
									GROUP_CONCAT( DISTINCT option_name SEPARATOR '~~~')  AS room_preference
								FROM #__hotelreservation_room_features 					rf
								INNER JOIN #__hotelreservation_room_feature_options 	rfo USING(feature_id)  
								WHERE 
									FIND_IN_SET( option_id, '". $value->option_ids."')
								GROUP BY feature_id
								ORDER BY feature_name  ";
					$features = $this->_getList( $query );
					foreach( $features as $feature )
					{
						$feature->room_preference = str_replace( '~~~', ( $feature->is_multiple_selection? ", " : " / " ), $feature->room_preference);
						if($rooms[$key]->room_preferences !='')
							$rooms[$key]->room_preferences.=' | ';
						$rooms[$key]->room_preferences .= $feature->room_preference;
					}
					
					$rooms[$key]->pictures	= array();
					
					$query = "  SELECT 
									*
								FROM #__hotelreservation_rooms_pictures 
								WHERE room_id = ".$value->room_id." AND room_picture_enable = 1
								ORDER BY room_picture_id
								 ";
					//dmp($query);
					$rooms[$key]->pictures = $this->_getList( $query );
					
					if( $i==0 ){
						$query = "  SELECT 
									hov.*
									FROM #__hotelreservation_offers of
									left join #__hotelreservation_offers_vouchers hov on hov.offerId = of.offer_id  
									WHERE hov.offerId = ".$value->offer_id ;
						
						$rooms[$key]->vouchers = $this->_getList( $query );
						//dmp($rooms[$key]->vouchers);
					}
					
					
					$rooms[$key]->offer_pictures = array();
					if( $i==0 )
					{
						$query = "  SELECT 
									*
								FROM #__hotelreservation_offers_pictures 
								WHERE 
									offer_id = ".$value->offer_id." 
									AND 
									offer_picture_enable = 1
								ORDER BY offer_picture_id
							 ";
						
						$rooms[$key]->offer_pictures = $this->_getList( $query );
					}		

					$rooms[$key]->offer_detalii = array();
					if( $i==0 )
					{
						$query = "  SELECT 
										p.package_id,
										p.package_description,
										p.package_name,
										is_price_day,
										''				 AS days
									FROM #__hotelreservation_offers_rooms_packages 	op
									INNER JOIN #__hotelreservation_packages 		p		ON p.package_id = op.package_id
									WHERE 
										op.offer_id 	= ".$value->offer_id." 
										AND
										op.room_id 		= ".$value->room_id." 
									ORDER BY p.package_name
							 ";
							 
						
						$rooms[$key]->offer_detalii['packages'] = $this->_getList( $query );
						foreach( $rooms[$key]->offer_detalii['packages'] as $p )
						{	
							if( !is_array($p->days) )
								$p->days = array();
							if( $p->is_price_day==false )
							{
								
							}
							else
							{
								for( $d = strtotime($datas);$d < strtotime($datae); )
								{
									$p->days[] = $p->package_id.'|'.date('Y-m-d', $d);
									$d = strtotime( date('Y-m-d', $d).' + 1 day ');
									
								}
							}
						}
					
						$query = "  SELECT 
										ao.arrival_option_id,
										ao.arrival_option_description,
										ao.arrival_option_name
									FROM #__hotelreservation_offers_rooms_arrival_options 	oao
									INNER JOIN #__hotelreservation_arrival_options 			ao		ON oao.arrival_option_id = ao.arrival_option_id
									WHERE 
										oao.offer_id 		= ".$value->offer_id." 
										AND
										oao.room_id 		= ".$value->room_id." 
									ORDER BY ao.arrival_option_name
							 ";
						//dmp($query);
						$rooms[$key]->offer_detalii['arrival_options'] = $this->_getList( $query );
					}
					
					$temporaryReservedRooms = $this->getReservedRooms($this->items_reserved);
					//dmp($temporaryReservedRooms);
					//$totalNumberRoomsReserved = $value->room_number_reserved;
					$totalNumberRoomsReserved = 0;
					if(isset($rooms_reserved[$value->room_id]))
						$totalNumberRoomsReserved = $rooms_reserved[$value->room_id];
					//dmp($totalNumberRoomsReserved);
					if(isset($temporaryReservedRooms[$value->room_id])){
						$totalNumberRoomsReserved += $temporaryReservedRooms[$value->room_id];
					}
					
					//dmp($value->room_id);
					//dmp(count($room_nr_availables_tmp));
					//dmp($totalNumberRoomsReserved);
					if( count($room_nr_availables_tmp) <= $totalNumberRoomsReserved || ($value->room_number_skiped > 0))
					{
						$rooms[$key]->is_disabled = true;
					}
					// dmp($rooms[$key]->offer_id);
					$is_ok_add = false;
					if( $is_all_rooms )
					{
						$is_ok_add = true;
					}
					else
					{
						foreach(  $this->items_reserved as $id_tmp )
						{
							$ex = explode('|', $id_tmp);
							if
							(
								$rooms[$key]->offer_id 	== $ex[0] 
								&&
								$rooms[$key]->room_id 	== $ex[1]
								&&
								$rooms[$key]->current 	== $ex[2]
							)
							{
								$is_ok_add = true;
								break;
							}
						}
					}
					if( $is_ok_add == true )
						$result_rooms[] = unserialize(serialize($rooms[$key]));
				}
				
				
			}
			// dmp($result_rooms);
			// exit;
			//check exceptions offers
			
			$this->checkExceptionsOffers( 
											$result_rooms, 	
											$is_all_rooms?
												$result_rooms  
												:
												($this->tip_oper>3?$this->roomsAvailable : $result_rooms)
										);  
			//~check exceptions offers
		
		}
		// exit;
		$this->setRoomDisplayPrice($result_rooms);
		return $result_rooms;
	}
	
	function isItemSelected($items_reserved, $offerId, $roomId){
		
		foreach(  $items_reserved as $id_tmp )
		{
			$ex = explode('|', $id_tmp);
			if
			(
					$offerId 	== $ex[0]
					&&
					$roomId 	== $ex[1]
			)
			{
				return true;
			}
		}
		return false;
	}
	
	function getReservedRooms($reservedItems){
		$result = array();
		if(is_array($reservedItems) && count($reservedItems)){
			foreach($reservedItems as $item){
				$value = explode("|",$item);
				if(isset($result[$value[1]]))
					$result[$value[1]] =$result[$value[1]] +1;
				else
					$result[$value[1]] =1 ;
			}
		}
		return $result;
	}
	
	function checkExceptionsOffers( &$rooms, $rooms_compare )
	{
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);
		$dif_day 		= (strtotime($datae) - strtotime($datas) ) /60/60/24;
		foreach( $rooms as $keyRoom => $valueRoom )
		{
			if( $valueRoom->offer_id ==0 )
				continue;
				
			$bCheckTipZi 	= false;
			$bCheckMax 		= false;
			// verificam daca avem restrictie pe tip zi ( 1-7 ) ...daca cel putin una e selectada
			if( 
				$valueRoom->offer_day_1
				+ 
				$valueRoom->offer_day_2
				+ 
				$valueRoom->offer_day_3
				+
				$valueRoom->offer_day_4
				+
				$valueRoom->offer_day_5
				+
				$valueRoom->offer_day_6
				+
				$valueRoom->offer_day_7
				> 0 
			)
				$bCheckTipZi = true;
				
			if( $valueRoom->offer_min_nights > $dif_day )
			{
				//unset( $rooms[$keyRoom] );
				//continue;
				$valueRoom->is_disabled = true;
			}	

			
			if( $valueRoom->offer_max_nights !=0 )
				$bCheckMax = true;
			
			
			if( $bCheckTipZi == false && $bCheckMax == false )
				continue;
			// dmp($rooms_compare);
			// exit;
			$nr_day_reserved = 0;
			foreach( $valueRoom->daily as $key => $day )
			{
				//dmp($day['is_extra_nights'] );
				$inc_day		 = 1;
				if( $bCheckMax && $nr_day_reserved >= $valueRoom->offer_max_nights && $day['is_extra_nights']== false)
				{
					foreach( $rooms_compare as $keyRoomTmp => $valueRoomTmp )
					{	
						if( $keyRoomTmp == $keyRoom )
							continue;
							
						if( 
							$valueRoom->room_id == $valueRoomTmp->room_id 
							&&
							$valueRoomTmp->offer_id	== 0
						)
						{
							//am gasit camera si cautam ziua
							foreach( $valueRoomTmp->daily as $keyTmp => $dayTmp )
							{
								if( $dayTmp['data'] == $day['data']  )
								{
									$rooms[$keyRoom]->daily[ $key ] = $dayTmp;
									$inc_day = 0;
									break;
								}
							}								
							//dmp($valueRoomTmp);
							// exit;
						}
					}
				}
				else if( $bCheckTipZi ) //verificam tipul de zi
				{
			
					$name_field_zi 			= 'offer_day_'.date('N', strtotime($day['data']));
					//dmp($valueRoom->{$name_field_zi});
					/*
					if( $valueRoom->{$name_field_zi} == 0  ) //daca nu e setat cautam setarile aferente respectivei camere
					{
						foreach( $rooms_compare as $keyRoomTmp => $valueRoomTmp )
						{	
							if( $keyRoomTmp == $keyRoom )
								continue;
								
							if( 
								$valueRoom->room_id == $valueRoomTmp->room_id 
								&&
								$valueRoomTmp->offer_id	== 0
							)
							{
								//am gasit camera si cautam ziua
								foreach( $valueRoomTmp->daily as $keyTmp => $dayTmp )
								{
									if( $dayTmp['data'] == $day['data']  )
									{
										$rooms[$keyRoom]->daily[ $key ] = $dayTmp;
										$inc_day = 0;
										break;
									}
								}								
							}
						}
					}
					*/
				}
				$nr_day_reserved += $inc_day;
			}
			// exit;
		}
	}
	
	function &getFeatures( $featureOptionAvailable_ids )
	{
		// Load the data

		$query = "  SELECT * 
						FROM #__hotelreservation_room_feature_options 		fo
						INNER JOIN #__hotelreservation_room_features		f	USING(feature_id) 
						WHERE FIND_IN_SET(fo.option_id, '".(is_array($featureOptionAvailable_ids)? implode(',', $featureOptionAvailable_ids) : $featureOptionAvailable_ids)."') 
					GROUP BY f.feature_id ORDER BY f.feature_name  ";
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
	
	function &getFeatureOptions( $option_ids )
	{
		// Load the data
		$query = " SELECT 
						*
						FROM #__hotelreservation_room_feature_options	
					".
					(
						count($option_ids) > 0 ?
						"WHERE FIND_IN_SET( option_id, '".(is_array($option_ids)? implode(',', $option_ids) : $option_ids)."')"
						:
						""
					)
					."
					ORDER BY option_name
				";
		//$this->_db->setQuery( $query );
		$featureOptions = $this->_getList( $query );
		$this->setOptionDisplayPrice($featureOptions);
		return $featureOptions;
	}
	
	function &getPackages( $package_ids )
	{
		//dmp($package_ids);
		//dmp( $this->tip_oper);
		//exit;
		// Load the data
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);
		
		$arr_selected_packages_days = array();
		if($this->edit_mode)
			$package_ids = array();
		// dmp($package_ids);
		
		//~init
		if( $this->reserve_room_id !=0  )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_packages 
						WHERE is_available = 1
						ORDER BY package_name ";
			//$this->_db->setQuery( $query );
			$pack_res = $this->_getList( $query );
			foreach( $pack_res  as $value )
			{
				$is_init =  $this->tip_oper <=3 || $this->edit_mode ? true : false;
				if( !isset( $package_ids ))
				{
					//do nothing
				}
				else			
				{
					// dmp($value);
					foreach( $this->package_day as $v )
					{
						if( 
							$v[0] == $this->reserve_offer_id
							&&
							$v[1] == $this->reserve_room_id
							&&
							$v[2] == $this->reserve_current
							&&
							$v[3] == $value->package_id
						)
						{
								$is_init = false;
								break;
						}
							
					}
				}

				if( $is_init == true )
					$package_ids[] = array( $this->reserve_offer_id,$this->reserve_room_id,$this->reserve_current,$value->package_id );
			}
		}
		//init
		
		foreach( $this->package_day as $v )
		{
			foreach( $v as $v_d)
			{
				if( is_array($v_d) )
					$val_package = $v_d;
				else
					$val_package = explode( "|", $v_d);
				if( count( $val_package) == 5 )
				{
					$arr_selected_packages_days[ $val_package[0].'|'.$val_package[1].'|'.$val_package[2].'|'.$val_package[3] ][] = $val_package[4];
				}
			}
		}
		$packages = array();
		// dmp($this->itemPackageNumbers);
		// exit;
		
		foreach( $package_ids as  $pck )
		{
			// dmp($pck);
			// dmp($arr_selected_packages_days);
			
			$query = " SELECT 
							* 
						FROM #__hotelreservation_packages 
						WHERE 
							is_available = 1
							AND 
							hotel_id = '".$this->hotel_id."'
							AND 
							FIND_IN_SET( CONCAT(".$pck[0].", '|', ".$pck[1].", '|', ".$pck[2].", '|', package_id), '".(is_array($pck)? implode('|', $pck) : $pck)."')
						ORDER BY package_name ";
			$this->_db->setQuery( $query );
			$pack_res = $this->_getList( $query );
			// dmp($this->_db);
			foreach( $pack_res as $key => $value )
			{
				// dmp($value->is_price_day);
				$key = implode('|', $pck);
				$packages[$key]	= $value;
				
				$packages[$key]->offer_id 		= $pck[0];
				$packages[$key]->room_id 		= $pck[1];
				$packages[$key]->current 		= $pck[2];
					
				$packages[$key]->price_final 	= $value->package_price;
			
				$packages[$key]->days = null;
				$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
				$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);
				$number_days 	= (strtotime($datae) - strtotime($datas) ) / 	( 60 * 60 * 24) - 1;
				$day_price 		= 0;		
				$packages[$key]->daily = array();
				
				$price_final	= 0;
				
				
				for( $d = strtotime($datas);$d < strtotime($datae); )
				{
					if( $value->is_price_day==false )
					{
						$day_price = $value->package_price;
					}
					else
					{
						switch( $value->package_type_price)
						{
							case 0:
								switch( date("N", $d) )
								{
									case 1:
										$day_price = $value->package_price_1;
										break;
									case 2:
										$day_price = $value->package_price_2;
										break;
									case 3:
										$day_price = $value->package_price_3;
										break;
									case 4:
										$day_price = $value->package_price_4;
										break;
									case 5:
										$day_price = $value->package_price_5;
										break;
									case 6:
										$day_price = $value->package_price_6;
										break;
									case 7:
										$day_price = $value->package_price_7;
										break;
								}
								break;
							case 1:
								$day_price = $value->package_price;
								break;
							case 2:
								switch( date("N", $d) )
								{
									case 7:
									case 1:
									case 2:
									case 3:
									case 4:
										$day_price = $value->package_price_midweek;
										break;
									case 5:
									case 6:
										$day_price = $value->package_price_weekend;
										break;
								}
								break;
								break;
						}
					}
					$is_sel = true;
					//dmp($arr_selected_packages_days);
					if( 
						(count( $arr_selected_packages_days ) == 0 && count( $package_ids ) == 0 ) 
						||
						$packages[$key]->offer_id > 0
					)	
						$is_sel = true;
					else if( 
						!isset( $arr_selected_packages_days[ $pck[0].'|'.$pck[1].'|'.$pck[2].'|'.$value->package_id ] ) 
						|| 
						!in_array( date( 'Y-m-d', $d), $arr_selected_packages_days[ $pck[0].'|'.$pck[1].'|'.$pck[2].'|'.$value->package_id ] )
					)
					{
						$is_sel = false;
					}
					// dmp($is_sel);
					//$day_price = $this->convertToCurrency($day_price, $this->itemCurrency->description, $this->currency_selector);
					$is_offer = false;// $this->getRoomDayIsOffer( $packages[$key]->offer_id, $packages[$key]->room_id, date( 'Y-m-d', $d) );
					// dmp($is_offer);
					// exit;
					$day 	= array( 
										'is_offer'		=> $is_offer,
										'data'			=> date( 'Y-m-d', $d),
										'price'			=> $day_price,
										'price_final'	=> $day_price,
										'is_sel'		=> $is_sel,
										'package_number'=> isset($this->itemPackageNumbers[ implode('|', $pck) ][4])? $this->itemPackageNumbers[ implode('|', $pck) ][4] : 0
									);
					
					$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
					$is_add = true;
				
					//check is available day
					$query = " 
								SELECT 
									*
								FROM #__hotelreservation_packages p
								LEFT JOIN #__hotelreservation_packages_date_ignored pdi ON ( pdi.package_date_data = '".date( 'Y-m-d', $d)."' AND pdi.package_id = p.package_id )
								WHERE 
									p.package_id = ".$value->package_id."
									AND
									IF( p.package_datas <> '0000-00-00', p.package_datas <= '".date( 'Y-m-d', $d)."', 1 )
									AND
									IF( p.package_datae <> '0000-00-00', p.package_datae >= '".date( 'Y-m-d', $d)."', 1 )
									AND
									IF( ISNULL(pdi.package_date_data), 1, 0 )
							";
					$check_data = $this->_getList( $query );
					if( !isset($check_data) || count($check_data) != 1 )
					{
						$is_add = false;
					}
						
					if($is_add == true)
					{
						$packages[$key]->daily[] = $day;
						
						$price_final += $day['price'];
					}
					//~check is available day
					
				}
				if( count($packages[$key]->daily) == 0 )
				{
					unset($packages[$key]);
				}
				else
				{
					if( $value->is_price_day==true )
						$packages[$key]->price_final = JHotelUtil::my_round($price_final/count($packages[$key]->daily),2);
				}
			}
			
		}
		
		$packages = $this->setPackageDisplayPrice($packages);
		return $packages;
	}

	
	function getRoomDayIsOffer( $offer_id, $room_id, $data )
	{
		//dmp($offer_id.' >>'.$room_id.' >> '.$data);
	
		foreach( $this->itemRoomsSelected as $keyRoom => $valueRoom )
		{	
			if( 
				$valueRoom->offer_id	== $offer_id
				&&
				$valueRoom->room_id 	== $room_id 
			)
			{
				//am gasit camera si cautam ziua
				foreach( $valueRoom->daily as $keyDay => $day )
				{
					if( $data == $day['data']  )
					{
						//dmp($day['is_offer']);
						return $day['is_offer'];
					}
				}	
			}
		}
		return -1;
	}
	function &getTaxes( )
	{
		// Load the data

		$query = " SELECT * 
					FROM #__hotelreservation_taxes 
					WHERE is_available = 1  AND hotel_id  = '".$this->hotel_id."'
					ORDER BY tax_name ";
		//$this->_db->setQuery( $query );
		$taxes = $this->_getList( $query );

		$this->setTaxDisplayPrice($taxes);

		return $taxes;
	}
	
	function &getConfirmationPayments( )
	{
		// Load the data
		$payments = array();
		if( $this->confirmation_id > 0 )
		{
			$query = " 	SELECT 
							cp.*,
							pp.paymentprocessor_type
						FROM #__hotelreservation_confirmations_payments cp
						LEFT JOIN #__hotelreservation_paymentprocessors pp	USING(paymentprocessor_id)
						WHERE confirmation_id=".$this->confirmation_id."
						ORDER BY confirmations_payments_id, data ";
						
			$payments = $this->_getList( $query );
			//dmp($payments);
			//exit;
		}
		
		
		return $payments;
	}
	
	function &getPaymentProcessors( )
	{
		// Load the data
		$paymentsprocessors = array();
		
		$query = " (SELECT * 
						FROM #__hotelreservation_paymentprocessors WHERE is_available = 1 ORDER BY paymentprocessor_name
					)";
		//$query .= ' UNION ALL '.$query;
		$paymentsprocessors = $this->_getList( $query );
		
		return $paymentsprocessors;
	}
	
	
	function &getAppSettings()
	{
		// Load the data

		$query	= "	SELECT * FROM #__hotelreservation_applicationsettings fas
				inner join  #__hotelreservation_date_formats df on fas.date_format_id=df.id";
		$this->_db->setQuery( $query );
		$appSettings = $this->_db->loadObject();
		
		return $appSettings;
	}
	
	function getExtraOptionIds($extraOptionIds, $index){
		$result = array();
		foreach($extraOptionIds as $extraOptionId){
			if($index == $extraOptionId[2]){
				$result[]=$extraOptionId[3];
			}	
		}
		return $result;
	}
		
	function getExtraOptionInfo($extraOptionIds, $index){
		$result = array();
		//dmp($index);
		foreach($extraOptionIds as $extraOptionId){
			if($index == $extraOptionId[2]){
				//dmp($extraOptionId);
				$extrInfo = new stdClass();
				$extrInfo->id = $extraOptionId[3];
				$extrInfo->persons = $extraOptionId[5];
				$extrInfo->days = $extraOptionId[6];
				$result[$extrInfo->id] = $extrInfo;
			}
		}
		return $result;
	}
	
	function getReservationDetails($modelData, $showDisplayPrice = false)
	{
		$bIsCostV 	= false;
		$costV		= 0;
		$bIsCostP 	= false;
		$costP		= 0;
		$percent 	= 0;
		$isFinalCost = false;
		
		ob_start();
		?>
		
		<table class="reservation_details" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid rgb(190, 188, 183); background: none repeat scroll 0% 0% rgb(248, 247, 245);">
		  <thead>
			<tr bgcolor="#C7D9E7" class='rsv_dtls_main_header'>
				<th  colspan="7" align="left" style="padding: 3px 9px;">
					<strong><?php echo JText::_('LNG_RESERVATION_DETAILS',true); ?></strong>
				</th>
			</tr>
			<tr bgcolor="#F8F7F5" class='rsv_dtls_hotel_container'>
				<td style="padding: 3px 9px;">
					<table>
						<tr>
							<td>
								<div style=" -moz-box-shadow: 0 2px 5px #969696; 	-webkit-box-shadow: 0px 2px 5px #969696; box-shadow: 0px 2px 5px #969696; float: left; padding: 2px;background-color: #FFFFFF;">
									<img height="70" style="height: 70px;border: medium none; float: left;"
									src="<?php echo JURI::root() ."administrator/components/".getBookingExtName(). $this->itemHotelSelected->pictures[0]->hotel_picture_path ?>" alt="Hotel Image" />
								</div>
							</td>
							<td style="padding-left: 10px;">
								<span style="float: left;font-size: 15px !important; font-weight: bold;  line-height: 24px; margin: 0;"><?php echo $this->itemHotelSelected->hotel_name?></span>
								<span style="  float: left;    margin-left: 10px;    margin-top: 3px;">
									<?php
									for ($i=1; $i<=$this->itemHotelSelected->hotel_stars; $i++){ ?>
										<img  src='<?php echo JURI::root() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
									<?php } ?>
								</span>
								<br>
								<div class="hotel-address"  style="display: inline-block; font-size: 11px; margin-bottom: 5px; width: 100%;">
									<?php echo $this->itemHotelSelected->hotel_address?>, <?php echo $this->itemHotelSelected->hotel_city?>, <?php echo $this->itemHotelSelected->hotel_county?>, <?php echo $this->itemHotelSelected->country_name?>
								</div>		
								<span><?php echo JText::_('LNG_TELEPHONE_NUMBER',true).' '.$this->itemHotelSelected->hotel_phone  ?> </span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr bgcolor="#D9E5EE" class='rsv_dtls_header'>
				<th colspan="6" align="left" style="padding: 3px 9px;"><?php echo JText::_('LNG_ITEM',true)?></th>

				<th align="right" style="padding: 3px 9px;"><?php echo JText::_('LNG_SUBTOTAL',true)?></th>
			</tr>
			</thead>

			<tbody bgcolor="#F8F7F5" class='rsv_dtls_container'>
				<?php
				if( $modelData->confirmation_id > 0 )
				{
				?>
				<tr>
					<td colspan="3"  align="left" valign="top" style="padding: 3px 9px;">
						<strong><?php echo JText::_('LNG_ID_RESERVATION',true); ?></strong>
					</td>
					<td style="padding: 3px 9px;" colspan="4" align="left">
						<span class='title_ID'><?php echo $modelData->JHotelUtil::getStringIDConfirmation()?></span>
					</td>
				</tr>
				<?php
				}
				?>
				
				<tr>
					<td colspan="3"  align="left" valign="top" style="padding: 3px 9px;">
						<strong><?php echo isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_NUMBER_OF_PARKS',true) : JText::_('LNG_NUMBER_OF_ROOMS',true); ?></strong>
					</td>
					<td colspan="4" align="left" style="padding: 3px 9px;">
						<?php echo $modelData->rooms > 0? $modelData->rooms.'&nbsp;'.($modelData->rooms >1? strtolower(isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARKS',true): JText::_('LNG_ROOMS',true)) :  strtolower(isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARK',true) : JText::_('LNG_ROOM',true)) ) : ""?>
					</td>
				</tr>
			
				<tr>	
					<td colspan="3" align="left" valign="top" style="padding: 3px 9px;">
						<strong><?php echo JText::_('LNG_GUESTS',true); ?></strong>
					</td>
					<td  colspan="4" align="left" valign="top" style="padding: 3px 9px;">
							<?php echo $modelData->guest_adult > 0? $modelData->guest_adult.'&nbsp;'.JText::_('LNG_ADULT_S',true) : ""?>
							&nbsp;&nbsp;&nbsp;<?php echo $modelData->guest_child > 0? $modelData->guest_child.'&nbsp;'.JText::_('LNG_CHILD_S',true) : ""?>
					</td>					
				</tr>
			
			
				<?php
				$arr_val_amount_to_pay	= array();
				$val_rooms 				= 0;
				// $val_offers_rooms 		= 0;
				$counter = 0;
				// dmp(count($modelData->items_reserved));
				// dmp($this->itemRoomsSelected);
				// exit;
				//start display room selected
				$nr_crt = 1;
				
				//dmp($this->itemRoomsSelected);
				foreach( $this->items_reserved as $keyRoomReserved => $valueRoomReserved )
				{				
					$exRoomReserved 		= explode( '|', $valueRoomReserved );
					$nr_days_except_offers	= 0; 
					foreach( $modelData->itemRoomsSelected as $room )
					{
						
						if(
								$room->offer_id 	!= $exRoomReserved[0]
								||
								$room->room_id 		!= $exRoomReserved[1]
								||
								$room->current 		!= $exRoomReserved[2]
						)
							continue;
						
						$val_room 	= 0;
						$counter++;
					
						$is_title 		= false;
						$price_period	= 0;
						
						$dayCounter = 0;
						//start - incarcam room
						foreach( $room->daily as $day)
						{
							if($showDisplayPrice)
								$price_day		= $day['display_price_final'];
							else
								$price_day		= $day['price_final'];
							$val_d			= 0;
							$info_discount	= '';
							$dayCounter ++;
							//dmp($day['discounts']);
							foreach( $day['discounts'] as $d )
							{
								if($d->maximum_number_days >=$dayCounter){
									$val_d += $d->discount_value;
									if( strlen($info_discount)>0)
										$info_discount	.='<BR>';
									$info_discount	.= $d->discount_name.' '.JHotelUtil::fmt(-1 * $d->discount_value).''.($d->percent==1?"%":" ".$modelData->itemCurrency->description);
								}
							}
							
							if( strlen($info_discount)>0)
								$info_discount = "<div class='discount_info'>".$info_discount.'</div>';
							
							
							?>
							<tr class='rsv_dtls_room_info'>
								<?php
								if( $is_title == false)
								{
									?>
									<td colspan=5 align="left" valign="top" style="border-top:solid <?php echo $room->offer_id==0? " 2px black" : " 1px grey"?>;padding: 3px 9px;"	rowspan='<?php echo count($room->daily)?>'>
										<?php
										if( count($this->items_reserved) > 1 )
										{
										?>
										<strong>#<?php echo $exRoomReserved[2]?></strong>
										<?php
										}
										?>
										<?php
												if($room->offer_id  > 0){
													echo "<strong>".$room->offer_name."</strong> <br/>";
													echo $room->offer_content;
												} 
												else
												{ 
													//echo $room->room_name .' (<i>'.JText::_('LNG_CAPACITY',true).' '.$room->room_capacity.' '.( $room->room_capacity > 1 ? JText::_('LNG_PERS',true): JText::_('LNG_PER',true) ).'</i>)';
													echo '<strong>'.$room->room_name.'</strong>'.' (<i>'.JText::_('LNG_CAPACITY',true).' '.$room->max_adults.' '.strtolower(JText::_('LNG_ADULTS',true)).($room->max_children > 0 ?' | '.$room->max_children.' '.JText::_('LNG_CHILDREN',true):'').'</i>)';
												}
											?>
		
											<?php
												if($room->offer_id  > 0 && $room->offer_max_nights <count($room->daily)){
													echo "<br/>";
													echo JText::_('LNG_EXTRA_NIGHT_BREAKFAST_INCLUDED',true);
												}
											?>
									</td>
									<?php
									$is_title = true;
								}
								?>
								<td align="left" valign="top" style="border-top:solid <?php echo $room->offer_id==0? " 2px black" : " 1px grey"?>;padding: 3px 9px;" nowrap="nowrap">
									
									<?php echo JHotelUtil::getDateGeneralFormat($day['data']);?>
									<?php
								
										//echo strlen($info_discount)>0 ? (JHotelUtil::fmt($price_day,2)."<strong> x </strong>".$modelData->itemRoomsCapacity[ $room->room_id ][1]."".($modelData->itemRoomsCapacity[ $room->room_id ][1] >1? JText::_('LNG_ROOMS',true):  JText::_('LNG_ROOM',true))):'';
										echo $info_discount;
								
										//echo "<strong> x </strong>".$modelData->itemRoomsCapacity[ $room->room_id ][1]."".strtolower($modelData->itemRoomsCapacity[ $room->room_id ][1] >1? JText::_('LNG_ROOMS',true):  JText::_('LNG_ROOM',true));*/
									?>
								</td>
								<td align="right" valign="top" style="border-top:solid <?php echo $room->offer_id==0? " 2px black" : " 1px grey"?>;padding: 3px 9px;">
									&nbsp;
									<?php
									echo JHotelUtil::fmt($price_day * /*$days_dif * */$modelData->itemRoomsCapacity[ $room->room_id ][1],2);
									?>
								</td>
								
								<?php
								$price_period += $price_day;
								?>
							</tr>
							<?php
							//dmp($room);
							
							if( isset($room->offer_id) && $room->offer_id > 0)
							{
								$nr_days_except_offers++;
							}
						}
						
						//dmp($nr_days_except_offers);
						$val_room += $price_period * $modelData->itemRoomsCapacity[ $room->room_id ][1];
						//end - incarcam room
						
						//start - incarcam options room
						$i=1;
						$val_options 			= 0;
						$itemOptions		  	= $modelData->getFeatureOptions(array());
						$room_feature_options 	= explode(",",  $room->option_ids);
						foreach( $itemOptions as $option )
						{
							if( !in_array($option->option_id, $room_feature_options) )
								continue;
							?>
							
							<?php 
							if($i==1)
							{ 
							?>
							<tr class='rsv_dtls_container_features'>
								<td style="padding: 3px 9px;" align="left" colspan="7">
									<strong><?php echo JText::_('LNG_ROOM_PREFERENCES',true);?></strong>
								</td>
							</tr>
							<?php 
							} 
							?>
							<tr>
								<td colspan=5 align=left style="padding: 3px 9px 3px 20px;">
									<?php echo$room->offer_id >0? "*" : ""?>
									<?php echo $option->option_name;?>
								</td>
								<td align=right>
									&nbsp;
								</td>
								<td align="right" nowrap style="padding: 3px 9px">
									&nbsp;
									<?php
									if( $room->offer_id == 0 || $nr_days_except_offers > 0 )
									{
										echo JHotelUtil::fmt($showDisplayPrice == true? $option->option_display_price: $option->option_price);
									}
									?>
								</td>
							</tr>
							<?php
							if( $room->offer_id == 0 || $nr_days_except_offers > 0 )
								$val_options += ($showDisplayPrice == true? $option->option_display_price: $option->option_price);
							$i++;
						}
						
						if( ($room->offer_id == 0 || $nr_days_except_offers > 0 ) && $val_room + $val_options != 0 )
						{
							?>
							<tr class='rsv_dtls_subtotal'  bgcolor="#EFEDE9">

								<td colspan=6 align="right">
									<strong><?php echo JText::_('LNG_ROOM_SUBTOTAL',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
								</td>
								<td align=right style="padding: 3px 9px;">
									<strong><?php echo JHotelUtil::fmt($val_room + $val_options,2)?></strong>
								</td>
					
							</tr>
							<?php
						}
						//end - incarcam options room
						
						//start - incarcam packages
						$val_packages 	= 0;
						$is_title 		= false;
						
						// exit;
						//dmp($room);
						foreach( $modelData->itemPackages as $package)
						{
							$keyPackage = $package->offer_id.'|'.$package->room_id.'|'.$package->current.'|'.$package->package_id;
							
							// dmp($package->offer_id 		.'!= '.$room->offer_id );
							// dmp($package->room_id 		.'!= '.$room->room_id );
							// dmp($package->current 		.'!= '.$room->current );
							
							if( 
								$package->offer_id 		!= $room->offer_id 
								||
								$package->room_id 		!= $room->room_id 
								
							)
								continue;
							
							if( $is_title == false )
							{
								?>
								<tr>
									<td style="padding: 3px 9px;" align="left" colspan="7">
										<strong><?php echo JText::_('LNG_PACKAGES',true)?></strong>
									</td>
								</tr>
								<?php
								$is_title = true;
							}	
							
							if( $package->is_price_day )
							{
								$nr_p = 1;
								foreach( $package->daily as $day )
								{
									if( $day['is_sel']==false )
										continue;
								?>
									<tr>
										<td colspan=5 align="left" style="padding: 3px 9px 3px 20px;">
											<?php echo $day['is_offer']==true? "*" : ""?>
											<?php  echo "<i>".$package->package_name."</i> | ".getDateGeneralFormatDay($day['data'])?>
										</td>
										<td align=left style="padding: 3px 9px;">
											&nbsp;
											<?php
											if( $day['is_offer']==false )
											{
												echo ($showDisplayPrice == true? $day['display_price_final']: $day['price_final']) .' x '.$modelData->itemPackageNumbers[$keyPackage][4];
											}
											?>
										</td>						
										<td align=right style="padding: 3px 9px;">
											&nbsp;
											<?php
											if( $day['is_offer']==false )
											{
												echo JHotelUtil::fmt(($showDisplayPrice == true? $day['display_price_final']: $day['price_final']) * $modelData->itemPackageNumbers[$keyPackage][4]);
											}
											?>
										</td>
									
									</tr>
									<?php
									$nr_p			++;
									if( $day['is_offer']==false )
										$val_packages 	+=($showDisplayPrice == true? $day['display_price_final']: $day['price_final']) * $modelData->itemPackageNumbers[$keyPackage][4];
								}
							}
							else
							{
								?>
								<tr>
									<td colspan=5 align="left" style="padding: 3px 9px 3px 20px;">
										<?php echo$package->offer_id >0? "*" : ""?>
										<?php echo "<i>".$package->package_name."</i>"?>
									</td>
									<td align=left style="padding: 3px 9px;">
										&nbsp;
										<?php
										if( $package->offer_id ==0 )
										{
											echo ($showDisplayPrice == true? $package->display_price_final: $package->price_final) .' x '.$modelData->itemPackageNumbers[$keyPackage][4];
										}
										?>
									</td>
									<td align=right style="padding: 3px 9px;">
										&nbsp;
										<?php
										if( $package->offer_id ==0 )
										{
											echo JHotelUtil::fmt(($showDisplayPrice == true? $package->display_price_final: $package->price_final) * $modelData->itemPackageNumbers[$keyPackage][4]);
										}
										?>
									</td>
								</tr>
								<?php
								if( $package->offer_id == 0 )
									$val_packages += ($showDisplayPrice == true? $package->display_price_final: $package->price_final)  * $modelData->itemPackageNumbers[$keyPackage][4] ;
								// dmp($val_packages);
								// exit;
							}
						}
						
						if( $val_packages != 0 )
						{
							?>
							<tr class='rsv_dtls_package_price'  bgcolor="#EFEDE9">
								<td colspan=6 align="right">
									<strong><?php echo JText::_('LNG_PACKAGES_SUBTOTAL',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
								</td>
								<td align=right style="padding: 3px 9px;">
									<strong><?php echo JHotelUtil::fmt($val_packages,2)?></strong>
								</td>
					
							</tr>
							<?php
						}
						//end - incarcam packages
						// dmp($modelData->itemPackages);
						// exit;
						//dmp($modelData->extraOptions);
						$extraOptionsIds = $this->getExtraOptionIds($modelData->extraOptionIds,$counter);
						$extraOptionsInfo = $this->getExtraOptionInfo($modelData->extraOptionIds,$counter);
						//dmp($extraOptionsInfo);
						$extraOptionsAmount	= 0;
						if( isset($modelData->extraOptions) && count($modelData->extraOptions) > 0 && count($extraOptionsIds)>0 ){ 
							//dmp($modelData->extraOptionIds);
							
							?>
							<tr class='rsv_dtls_arrival_options'>
								<td nowrap colspan=7 align=left style="padding: 3px 9px">
									<strong><?php echo JText::_('LNG_EXTRAS',true)?></strong>
								</td>
							</tr>
							<?php
							foreach( $modelData->extraOptions as $extraOption){
								if(!in_array($extraOption->id,$extraOptionsIds)){
									continue;
								}
								
								$extrOptionInfo = $extraOptionsInfo[$extraOption->id];
								$extraOption->nrPersons= $extrOptionInfo->persons;
								$extraOption->nrDays=$extrOptionInfo->days;
								
								$amount =$extraOption->price;
								
								if($extraOption->price_type == 1){
									$amount = $amount * $extraOption->nrPersons;
								}
								if($extraOption->is_per_day == 1 || $extraOption->is_per_day == 2){
									$amount = $amount * $extraOption->nrDays;
								}
								?>
								<tr>
									<td nowrap align=left colspan=6 style="padding: 3px 9px 3px 20px;">
										<?php
											echo $extraOption->name.", ".$this->itemCurrency->currency_symbol." ". JHotelUtil::fmt($extraOption->price,2)." ". ($extraOption->price_type == 1?strtolower(JText::_('LNG_PER_PERSON',true))." ":"" )."".($extraOption->is_per_day == 1 ?strtolower(JText::_('LNG_PER_DAY',true)):"" )."".($extraOption->is_per_day == 2 ?strtolower(JText::_('LNG_PER_NIGHT',true)):"" );
											
											if($extraOption->nrPersons > 0 || $extraOption->nrDays > 0){
												echo "<br/><i>(";
												$showDelimiter = false;
												if($extraOption->nrPersons > 0){
													echo strtolower(JText::_('LNG_NUMBER_OF_PERSONS',true))." ".$extraOption->nrPersons;
													$showDelimiter = true;
												}
												
												if($extraOption->nrDays > 0){
													if($showDelimiter){
														echo ", ";
													}
													echo strtolower(($extraOption->is_per_day == 1 ?JText::_('LNG_NUMBER_OF_DAYS',true):JText::_("LNG_NUMBER_OF_NIGHTS",true)))." ".$extraOption->nrDays;
												}
												echo ")</i>";
											}
										?>
									</td>	
									<td align=right nowrap style="padding: 3px 9px">
										&nbsp;
										<?php
											echo JHotelUtil::fmt($amount,2);
										?>
									</td>

								</tr>
								<?php
								
								$extraOptionsAmount += $amount;
							
							}?>
							<tr class='rsv_dtls_room_price' bgcolor="#EFEDE9">

								<td colspan="6" style="padding: 3px 0px;"  align="right">
									<strong><?php echo JText::_('LNG_ARRIVAL_OPTIONS_SUBTOTAL',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
								</td>
								<td align="right" style="padding: 3px 9px" >
									<strong><?php echo JHotelUtil::fmt($extraOptionsAmount,2)?></strong>
								</td>

							</tr>
							<?php
						}
						//end - incarcam arrival options
						//dmp($modelData->airport_transfer_type_ids);
						//start - incarcam arrival airport transfer
						$val_airport_transfer 	= 0;
						if( count( $modelData->airport_transfer_type_ids ) > 0 )
						{
							// dmp($modelData->itemAirportTransferTypes);
							foreach( $modelData->itemAirportTransferTypes as $keyAirportTransfer => $airport_transfer )
							{
								$eKeyAirportTransfer = explode( '|', $keyAirportTransfer );
								if( 
									$exRoomReserved[0] != $eKeyAirportTransfer[0]
									||
									$exRoomReserved[1] != $eKeyAirportTransfer[1]
									||
									$exRoomReserved[2] != $eKeyAirportTransfer[2]
								)
									continue;
									
								$pr_info = ($showDisplayPrice == true?$airport_transfer->airport_transfer_type_display_price: $airport_transfer->airport_transfer_type_price).
										($airport_transfer->airport_transfer_type_vat !=0 ? (" + ".$airport_transfer->airport_transfer_type_vat." %".JText::_('LNG_VAT',true)) : "");
													
								$val_airport_transfer = ($showDisplayPrice == true?$airport_transfer->airport_transfer_type_display_price: $airport_transfer->airport_transfer_type_price);
								if( $airport_transfer->airport_transfer_type_vat > 0 )
									$val_airport_transfer += ($val_airport_transfer * $airport_transfer->airport_transfer_type_vat / 100);
								?>
								<tr class='tr_airport_transfer_title'>
									<td nowrap colspan=7 align=left style="padding: 3px 9px;">
										<strong><?php echo JText::_('LNG_AIRPORT_TRANSFER',true) ?></strong>
									</td>
								</tr>
								<tr >	
									<td colspan=5 align=left style="padding: 3px 9px 3px 20px;">
										<?php echo $airport_transfer->airport_transfer_type_name?>						
									</td>
									<td>
										<?php echo $pr_info?>
									</td>
									<td align=right style="padding: 3px 9px;" >
										<?php echo JHotelUtil::fmt($val_airport_transfer)?>
									</td>

								</tr>
								<tr>
									<td colspan=7 align=left style="padding: 3px 9px 3px 40px;">
										<table class='table_airport_transfer' cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td nowrap ><?php echo JText::_('LNG_AIRLINE',true)?> :&nbsp;</td>
												<td  colspan=3>
													<?php 
													foreach( $modelData->itemArrivalAirlines as  $keyAirline => $valueAirline )
													{
														$exKeyAirline = explode( '|', $keyAirline );
														if( 
															$exRoomReserved[0] != $exKeyAirline[0]
															||
															$exRoomReserved[1] != $exKeyAirline[1]
															||
															$exRoomReserved[2] != $exKeyAirline[2]
														)
															continue;
														echo $valueAirline->airline_name;
													}
													?>
												</td>
											</tr>
											<tr>
												<td width=15% nowrap><?php echo JText::_('LNG_FLIGHT_NR',true)?> :</td>
												<td width=35%>
													<?php 
													foreach( $modelData->airport_transfer_flight_nrs as  $keyTransferFlightNr => $valueTransferFlightNr )
													{
														if( 
															$exRoomReserved[0] != $valueTransferFlightNr[0]
															||
															$exRoomReserved[1] != $valueTransferFlightNr[1]
															||
															$exRoomReserved[2] != $valueTransferFlightNr[2]
														)
															continue;
														echo $valueTransferFlightNr[3];
													}
													?>
												</td>
												<td width=15% nowrap><?php echo JText::_('LNG_GUEST',true)?> :</td>
												<td width=35%>
													<?php 
													foreach( $modelData->airport_transfer_guests as  $keyTransferGuest => $valueTransferGuest )
													{
														if( 
															$exRoomReserved[0] != $valueTransferGuest[0]
															||
															$exRoomReserved[1] != $valueTransferGuest[1]
															||
															$exRoomReserved[2] != $valueTransferGuest[2]
														)
															continue;
														echo $valueTransferGuest[3];
													}
													?>
												</td>
											</tr>
											<tr>
												<td width=10% nowrap><?php echo JText::_('LNG_DATE',true)?> :</td>
												<td width=35%>
													<?php 
													foreach( $modelData->airport_transfer_dates as  $keyTransferDate => $valueTransferDate )
													{
														if( 
															$exRoomReserved[0] != $valueTransferDate[0]
															||
															$exRoomReserved[1] != $valueTransferDate[1]
															||
															$exRoomReserved[2] != $valueTransferDate[2]
														)
															continue;
														echo $valueTransferDate[3];
													}
													?>
												</td>
												<td width=15%><?php echo JText::_('LNG_TIME',true)?> :</td>
												<td width=35%>
													<?php 
													foreach( $modelData->airport_transfer_time_hours as  $keyTransferTimeHour => $valueTransferTimeHour )
													{
														if( 
															$exRoomReserved[0] != $valueTransferTimeHour[0]
															||
															$exRoomReserved[1] != $valueTransferTimeHour[1]
															||
															$exRoomReserved[2] != $valueTransferTimeHour[2]
														)
															continue;
														echo $valueTransferTimeHour[3];
													}
													echo ":";
													foreach( $modelData->airport_transfer_time_mins as  $keyTransferTimeMin => $valueTransferTimeMin )
													{
														if( 
															$exRoomReserved[0] != $valueTransferTimeMin[0]
															||
															$exRoomReserved[1] != $valueTransferTimeMin[1]
															||
															$exRoomReserved[2] != $valueTransferTimeMin[2]
														)
															continue;
														echo $valueTransferTimeMin[3];
													}
													?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<!--
								<tr class='rsv_dtls_room_price' bgcolor="#EFEDE9">
									<td colspan="6" style="padding: 3px 9px;"  align="right">
										<strong><?php echo JText::_('LNG_AIRPORT_TRANSFER_SUBTOTAL',true)?>(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
									</td>
					
									<td align="right" style="padding: 3px 9px" >
										<strong><?php echo JHotelUtil::fmt($val_airport_transfer,2)?></strong>
									</td>

								</tr>
								-->
								<?php
							}
						}
						//end  - incarcam arrival airport transfer
						if( $room->offer_id > 0 )
						{
							// $val_options			= 0;
							// $val_packages			= 0;
							// $val_arrival_option		= 0;
						    //$val_airport_transfer	= 0;
						}
						
						$val_room 	+=	$val_options;
						$val_room 	+=	$val_packages;
						$val_room 	+=	$extraOptionsAmount;
						$val_room	+= 	$val_airport_transfer;
						
						
						?>
						<tr class='rsv_dtls_subtotal'  bgcolor="#EFEDE9" style="display:none">
							<td colspan=6 align="right">
								<strong><?php echo JText::_('LNG_ESTIMATED_SUBTOTAL',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
							</td>
							<td align=right style="padding: 3px 9px;">
								<strong><?php echo JHotelUtil::fmt($val_room,2)?></strong>
							</td>
				
						</tr>
						
						<?php
						$val_taxes			 	= 0;
				
						foreach( $modelData->itemTaxes as $tax)
						{
							if( $tax->tax_type =='Fixed')
								$val_taxes = ($showDisplayPrice == true?$tax->tax_display_value:$tax->tax_value);
							else
								$val_taxes = ($tax->tax_value * ($val_room - $val_airport_transfer) / 100);
								
							if( $val_taxes == 0.00 )
								continue;
							?>
							<tr>
								<td colspan=6 align="right" style="padding: 3px 9px;">
									<?php echo $tax->tax_name?>
									(<?php echo (($showDisplayPrice == true && $tax->tax_type=='Fixed'?$tax->tax_display_value:$tax->tax_value).' '.($tax->tax_type=='Fixed'? ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description) : ' % ') )?>)
								</td>

								<td align="right" style="padding: 3px 9px;">
									<?php echo JHotelUtil::fmt($val_taxes)?>
								</td>

							</tr>
							<?php
							$val_room 	+= $val_taxes;
						}
						?>
						<tr class='rsv_dtls_subtotal' bgcolor="#EFEDE9">
							<td colspan=6 align="right">
								<strong><?php echo JText::_($room->offer_id > 0  ? "LNG_ESTIMATED_SUBTOTAL" : "LNG_ESTIMATED_SUBTOTAL",true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
							</td>
							<td align=right style="padding: 3px 9px;">
								<strong><?php echo JHotelUtil::fmt( $val_room,2)?></strong>
							</td>
						</tr>
						<?php
						$val_rooms 				+= $val_room;
					
						if(
								(( $room->offer_id  > 0 && ( $room->offer_reservation_cost_val > 0 || $room->offer_reservation_cost_proc > 0 ) )
								||
								( $room->offer_id == 0 && ( $room->reservation_cost_val > 0 || $room->reservation_cost_proc > 0 ) ))
								&&
								!$isFinalCost
						)
						{
							$bIsCostVi 	= ($room->offer_id  > 0 && $room->offer_reservation_cost_val > 0 ) || ($room->offer_id  == 0 && $room->reservation_cost_val > 0 );
							$costVi		= $room->offer_id  > 0 ? $room->offer_reservation_cost_val : $room->reservation_cost_val;
							$bIsCostPi 	= ($room->offer_id  > 0 && $room->offer_reservation_cost_proc > 0) || ($room->offer_id  == 0 && $room->reservation_cost_proc > 0 );
							$costPi		= ($room->offer_id  > 0 ? $room->offer_reservation_cost_proc : $room->reservation_cost_proc) * $val_room / 100;
							$percent	= ($room->offer_id  > 0 ? $room->offer_reservation_cost_proc : $room->reservation_cost_proc);
							
							if($bIsCostVi && ($costV < $costVi)){
								$bIsCostV = $bIsCostVi;
								$costV = $costVi;
							}
							
							if($bIsCostPi && ($costP < $costPi)){
								$bIsCostP = $bIsCostPi;
								$costP = $costPi;
							}
							
							$isFinalCost = ($room->offer_id == 0);
						}
					}
				}
				//end display room/ofers selected
			
				
				if( count($modelData->itemRoomsSelected) > 1 )
				{
				?>
				<tr class='rsv_dtls_total_room_price' bgcolor="#EFEDE9">
					<td align="right" colspan="6" style="border-top:solid 2px black;padding: 3px 9px;"  >
						<strong><?php echo JText::_('LNG_TOTAL_ROOMS_RATES',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
					</td>
					<td align="right" style="border-top:solid 2px black;padding: 3px 9px" >
						<strong><?php echo JHotelUtil::fmt($val_rooms)?></strong>
					</td>

				</tr>
				<?php
				}

				if($this->itemAppSettings->charge_only_reservation_cost)
					$bIsCostV = true;
				
				//dmp($bIsCostP);
				if( $bIsCostV || $bIsCostP )
					$arr_val_amount_to_pay[] = array(
							'nr'	=> $exRoomReserved[2],
							'room'	=> $val_rooms ,
							'val'	=> $costV,
							'proc'	=> array('p'=>$percent, 'v'=>$costP)
					);

				$val_rooms 	+= $costV;
				
				if( $bIsCostV )
				{
					?>
					<tr class='rsv_dtls_subtotal' bgcolor="#EFEDE9">
						<td colspan=6 align="right"><strong><?php echo JText::_($room->offer_id > 0  ? "LNG_OFFER_COST_VALUE" : "LNG_COST_VALUE",true)?>
								(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
						</td>
						<td align=right style="padding: 3px 9px;"><strong><?php echo JHotelUtil::fmt( $costV,2)?>
						</strong>
						</td>
					</tr>
					<?php
				 }
				 
				 if( $bIsCostV )
				 {
					?>
					<tr class='rsv_dtls_subtotal' bgcolor="#EFEDE9">
						<td colspan=6 align="right"><strong><?php echo JText::_($room->offer_id > 0  ? "LNG_ESTIMATED_TOTAL" : "LNG_ESTIMATED_TOTAL",true)?>
								(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
						</td>
						<td align=right style="padding: 3px 9px;"><strong><?php echo JHotelUtil::fmt( $val_rooms,2)?>
						</strong>
						</td>
					</tr>
					<?php
				 }
				
				$modelData->total_init 		= $val_rooms;

				$val_costs	= 0;
				//dmp($this->tip_oper);
				// dmp($arr_val_amount_to_pay );

			foreach($arr_val_amount_to_pay  as $v)
			{
				//$arr_val_amount_to_pay[] = array( 'room'=> $val_room , 'val'=> $costV, 'proc'=> array('p'=>$percent, 'v'=>$costP) );
							
				if( $this->tip_oper < 5  )
				{
				?>
				<tr class='rsv_dtls_subtotal'  bgcolor="#EFEDE9">
					<td colspan=6 align="right">
						<strong>
							
							<?php echo JText::_('LNG_AMOUNT_PAY',true)?> 
							
							<?php
							if($v['val']  > 0 )
								echo "(".JText::_('LNG_COST_VALUE',true);
							if($v['val']  > 0 && $v['proc']['p']  > 0  )
								echo ' + ';
							if($v['proc']['p']  > 0 )
								echo $v['proc']['p'].'% '.JText::_('LNG_ESTIMATED_SUBTOTAL',true);
							if($v['val']  > 0 )
								echo ")";
							?>
							
							(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)
						</strong>
					</td>
					<td align=right style="padding: 3px 9px;">
						<strong><?php echo JHotelUtil::fmt( $v['val'] + $v['proc']['p'] * $v['room'] /100,2)?></strong>
					</td>
				</tr>
				<?php
				}
				$val_costs  +=  $v['val'] + $v['proc']['p'] * $v['room'] /100;
			}
			
			if( count($arr_val_amount_to_pay) > 1)
			{
				
				?>
				<tr class='rsv_dtls_total_price' bgcolor="#dee5e8" style='border-top:solid 3px black'>

					<td align="right" colspan="6" style="padding: 3px 9px;">
						<strong><?php echo JText::_('LNG_GRAND_TOTAL',true)?> (<?php echo($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
					</td>

					<td align="right" style="padding: 3px 9px;">
						<strong><?php echo JHotelUtil::fmt($val_rooms)?></strong>
					</td>
				</tr>
				
				<?php
				?>
				<tr class='rsv_dtls_subtotal'  bgcolor="#EFEDE9">
					<td colspan=6 align="right">
						<strong><?php echo JText::_('LNG_COST_TOTAL',true)?> 
						(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)
						</strong>
					</td>
					<td align=right style="padding: 3px 9px;">
						<strong><?php echo JHotelUtil::fmt( $val_costs,2)?></strong>
					</td>
				</tr>
				<?php
			}
			
			$modelData->total_cost 		= $val_costs;
			
			if( $val_costs > 0  & $this->tip_oper < 5 )
			{
				?>
				<tr class='rsv_dtls_subtotal'  bgcolor="#EFEDE9">
					<td colspan=6 align="right">
						<strong><?php echo isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_REMAINING_PARK_PAY',true) : JText::_('LNG_REMAINING_PAY',true)?> 
						(<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)
						</strong>
					</td>
					<td align=right style="padding: 3px 9px;">
						<strong><?php echo JHotelUtil::fmt( $val_rooms - $val_costs,2)?></strong>
					</td>
				</tr>
				<?php
			}
			$total_payments 	= 0;
			$total_payments_ok 	= 0;
			$is_cancelation		= false;
			$suma_cancelation	= 0;
					
			if( count($modelData->itemPayments) > 0 )
			{
				//dmp($modelData->itemPayments);
				$is_canceled	= false;
				foreach( $modelData->itemPayments as $pay)
				{
					if( $pay->payment_type_id == CANCELED_PAYMENT_ID  )
					{
						$is_canceled	= true;
						break;
					}
				}
				$total = $modelData->total_init;
				$total_pay				= 0;
				$total_pay_pending		= 0;
				$total_pay_ok			= 0;
				// dmp($modelData->itemPayments);
				foreach( $modelData->itemPayments as $pay)
				{
					if( $is_canceled && $pay->tip == 'cash' )
					{
						if( $pay->payment_type_id == CANCELED_PAYMENT_ID  )
						{
							//do nothing, then release all
						}
						else
						{
							$pay->payment_explication 	= JText::_('LNG_SKIP_PAYMENT',true);
							$pay->payment_percent		= 0;
							$pay->payment_value			= 0;
						}
					}
						
					// amount to pay	
					$val_pay 	= 0;
					//amount paid
					$val_pay_ok = 0;
					if( $pay->payment_type_id == DONE_PAYMENT_ID &&  $pay->tip =='')
					{
						$pay->payment_explication 	= JText::_('LNG_SKIP_PAYMENT',true);
						//$pay->payment_percent		= 100;
						$pay->payment_value		= JHotelUtil::my_round($total  - $total_pay_ok,2);
						$sign = "-";
						$val_pay_ok +=  $total  - $total_pay_ok;
					}
					else if( $pay->payment_type_id == PREAUTHORIZATION_PAYMENT_ID )
						$sign = "";
					else if( $pay->payment_type_id == DONE_PAYMENT_ID )
						$sign = "-";
					else if( $pay->payment_type_id == CANCELED_PAYMENT_ID  )
						$sign = "-/+";
					else if( $pay->payment_type_id == PAYPAL_ID || $pay->payment_type_id == BUCKAROO_ID || $pay->payment_type_id == IDEAL_OMNIKASSA_ID || $pay->payment_type_id == BANK_ORDER_ID  || $pay->payment_type_id == CASH_ID || $pay->payment_type_id == MPESA_ID || $pay->payment_type_id == P4B_ID )
						$sign = "";
					else
						$sign = "+";
					// dmp( $pay->payment_type_id);	
					$bkcolor = '#FFFFFF';
					
					if( ($pay->payment_type_id == DONE_PAYMENT_ID || $pay->payment_type_id == PREAUTHORIZATION_PAYMENT_ID) &&  $pay->tip =='card')
					{
						$val_pay_ok +=  JHotelUtil::my_round($pay->payment_value,2);
					}
					else if( $pay->payment_type_id != PREAUTHORIZATION_PAYMENT_ID && $pay->payment_type_id != DONE_PAYMENT_ID )
					{
						if( $pay->payment_type_id == CANCELED_PAYMENT_ID  )
						{
							$val_pay = $val_pay_ok 	=  $pay->payment_percent !=0? JHotelUtil::my_round($total * $pay->payment_percent / 100,2)  : 0;
							
						}
						else
						{
							if( $pay->payment_type_id == BANK_ORDER_ID || $pay->payment_type_id == CASH_ID || $pay->payment_type_id == MPESA_ID )
							{
								//do not increment
							}
							else
							{
								// suplimentary tax, fee , penalties
								if( $pay->payment_percent !=0 )
									$val_pay +=  JHotelUtil::my_round($total * $pay->payment_percent / 100,2) ;
								if( $pay->payment_value !=0 )
									$val_pay +=  $pay->payment_value;
							}
						}
					}
					

					$str_val_temporary 	= '';
					if( $pay->payment_type_id != CANCELED_PAYMENT_ID  )
					{
					
						switch( $pay->payment_status )
						{
							case '':
								$bkcolor = '#FF9900';
								break;
							case PAYMENT_STATUS_BLOCK:
							case PAYMENT_STATUS_PAYED:
							case PAYMENT_STATUS_PENDING:
							case PAYMENT_STATUS_WAITING:

								if( $pay->payment_status == PAYMENT_STATUS_BLOCK || $pay->payment_status == PAYMENT_STATUS_PENDING )
									$bkcolor = '#E9E9DC';
								else
									$bkcolor = '#99FF99';
									
								if( 
									$pay->payment_percent !=0 
									&&
									$pay->payment_status != PAYMENT_STATUS_RELEASED
									&& 
									$pay->payment_status != PAYMENT_STATUS_PENDING
									&& 
									$pay->payment_status != PAYMENT_STATUS_WAITING
									&& 
									( 
										$pay->tip == 'card'  
										|| 
										$pay->tip == 'website'  
										||
										$pay->tip == 'bank'  
										||										
										$pay->tip == 'phone'  
										||
										(
											$modelData->confirmation_payment_status == PAYMENT_STATUS_PAYED  
											&& 
											($pay->payment_type_id == DONE_PAYMENT_ID && $pay->tip == 'cash' )
										) 
									) 
								)
								{
									$val_pay_ok +=  JHotelUtil::my_round($total * $pay->payment_percent / 100,2) ;
								}
								
								if( 
									$pay->payment_value !=0 
									&&
									$pay->payment_status != PAYMENT_STATUS_PENDING
									&&
									$pay->payment_status != PAYMENT_STATUS_WAITING
									&& 
									$pay->payment_status != PAYMENT_STATUS_RELEASED
									&& 
									( 
										$pay->tip == 'card'  
										|| 
										$pay->tip == 'bank'  
										|| 
										$pay->tip == 'website'  
										|| 
										$pay->tip == 'phone'  
										|| 
										(
											$modelData->confirmation_payment_status == PAYMENT_STATUS_PAYED  
											&& 
											($pay->payment_type_id == DONE_PAYMENT_ID && $pay->tip == 'cash' )
										) 
									)  
								)
								{
									
									$val_pay_ok +=  JHotelUtil::my_round($pay->payment_value,2);
								}
								
								
								break;
							case PAYMENT_STATUS_REJECTED:
								$bkcolor = '#FF0000';
								break;
							case PAYMENT_STATUS_NOTPAYED:
								$bkcolor = '#FF99999';
								break;
						}
					
						if( 
							$pay->payment_percent !=0 
							&&
							(
								( $pay->tip == 'cash' && $pay->payment_type_id == PENALTY_PAYMENT_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == PAYPAL_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == IDEAL_OMNIKASSA_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == BUCKAROO_ID  )
								||
								( $pay->tip == 'bank' && $pay->payment_type_id == BANK_ORDER_ID  )
								||
								( $pay->tip == 'cash' && ($pay->payment_type_id == CASH_ID && $pay->paymentprocessor_id != 0 )  )
								||
								( $pay->tip == 'phone' && $pay->payment_type_id == MPESA_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == P4B_ID )
							)
						)
						{
							$str_val_temporary	+= JHotelUtil::my_round($total * $pay->payment_percent / 100,2) ;
							if( $pay->payment_status != PAYMENT_STATUS_PAYED )	
							{
								$str_val_temporary  = "<i>".JHotelUtil::fmt(JHotelUtil::my_round($total * $pay->payment_percent / 100,2))."</i>";
								$total_pay_pending += JHotelUtil::my_round($total * $pay->payment_percent / 100,2) ;
							}
						}
						
						if( 
							$pay->payment_value !=0 
							&&
							(
								( $pay->tip == 'cash' && $pay->payment_type_id == PENALTY_PAYMENT_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == PAYPAL_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == BUCKAROO_ID  )
								||
								( $pay->tip == 'website' && $pay->payment_type_id == IDEAL_OMNIKASSA_ID  )
								||
								( $pay->tip == 'bank' && $pay->payment_type_id == BANK_ORDER_ID  )
								||
								( $pay->tip == 'cash' && ($pay->payment_type_id == CASH_ID && $pay->paymentprocessor_id != 0 )  )
								||
								( $pay->tip == 'phone' && $pay->payment_type_id == MPESA_ID  )
							)
						)
						{
							$str_val_temporary	+= $pay->payment_value;
							if( $pay->payment_status != PAYMENT_STATUS_PAYED )	
							{
								$str_val_temporary 	= "<i>".JHotelUtil::fmt( $pay->payment_value)."</i>";
								$total_pay_pending += $pay->payment_value;
							}
							
						}
					
					}
					$total_pay 		+= $val_pay;
					$total_pay_ok 	+= $val_pay_ok;
					
					
					//manevra de afisare
					$p_tip 				= $pay->tip;
					$p_payment_status 	= $pay->payment_status;
					switch(  $pay->tip )
					{
						case 'cash':
							break;
						case 'card':
							break;
						case 'website':
							if( $pay->payment_status ==PAYMENT_STATUS_PAYED)
							{
								$p_tip			 	= JText::_('LNG_STATUS_PAYED',true);
								$p_payment_status 	= JText::_('LNG_VIA',true);
							}
							break;
						case 'bank':
							if( $pay->payment_status ==PAYMENT_STATUS_PAYED)
							{
								$p_tip			 	= JText::_('LNG_STATUS_PAYED',true);
								$p_payment_status 	= '  ';
							}
							else
							{
								$p_tip			 	= JText::_('LNG_BANK_ORDER',true);
								$p_payment_status 	= ' '.JText::_('LNG_PAYMENT',true).' ';
							}
							break;
						case 'phone':
							if( $pay->payment_status ==PAYMENT_STATUS_PAYED)
							{
								$p_tip			 	= JText::_('LNG_STATUS_PAYED',true);
								$p_payment_status 	= JText::_('LNG_VIA',true);
							}
							break;
					}
					//~manevra de afisare
				?>
				<tr class="confirmation_card_information" bgcolor="#dee5e8">

					<td align="right" colspan="6" >
						<strong><?php echo  $p_tip?></strong>
						<strong><?php //echo  $modelData->is_enable_payment == false && $pay->payment_type_id != DONE_PAYMENT_ID? "" : $p_payment_status?></strong>
						<?php //echo $pay->payment_explication ?> 
						<?php echo ($pay->payment_percent !='0' || $pay->payment_type_id == CANCELED_PAYMENT_ID ? "(".$pay->payment_percent .' % )': '')?>
						<?php echo "<strong>$sign</strong>"?>
						
					</td>

					<td align=right style="padding: 3px 9px">
						<strong>
							<?php echo $val_pay_ok != 0 ? JHotelUtil::fmt($val_pay_ok) : (strlen($str_val_temporary) > 0 ? $str_val_temporary: "&nbsp;" ) ?>
						</strong>
					</td>
				</tr>
				<?php
				
					if( $sign !="")
						$total_payments 	+= ($sign=='-'? (-1) : 1 ) * $val_pay;
						
					$total_payments_ok 		+= $val_pay_ok;
					
					if( $pay->payment_type_id == CANCELED_PAYMENT_ID )
					{
						$is_cancelation 	= true;
						$suma_cancelation	= $val_pay;
					}
					
				}
				$total += $total_pay  - $total_pay_ok;
				
				
				if( $is_cancelation )
				{
					$val_rooms=$total_payments = 0;
					$val_rooms=$suma_cancelation;
				}
				
				$remaining_payment =  JHotelUtil::my_round($val_rooms+$total_payments-$total_payments_ok,2)	;	   
				
				$remaining_payment -= $total_pay_pending;
				//dmp($total_pay_pending);
				
				?>
			
				<tr bgcolor="#dee5e8" class="confirmation_total" >

					<td align=right colspan=6 style="padding: 3px 9px">
						<strong><?php echo isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_TOTAL_REST_PARK',true) : JText::_('LNG_TOTAL_REST',true)?> (<?php echo ($showDisplayPrice ? $modelData->currency_selector: $modelData->itemCurrency->description)?>)</strong>
					</td>
					<td align=right style="padding: 3px 9px">
						<strong><?php echo $remaining_payment!= 0.00 ? JHotelUtil::fmt($remaining_payment, 2) : "&nbsp;"?></strong>
					</td>
				</tr>
				
				<?php
			}
			//exit;
			?>
			
			
		</table>
		<?php
		
		$modelData->payment_variables->currency_code			= $modelData->itemCurrency->description;
		$modelData->payment_variables->amount 					= JHotelUtil::my_round( 
																			$modelData->confirmation_id == 0 && $val_costs > 0? 
																				$val_costs > 0 
																				: 
																				$val_rooms+$total_payments 
																		,2);
		$modelData->payment_variables->first_name				= $modelData->first_name;
		$modelData->payment_variables->last_name				= $modelData->last_name;
		$modelData->payment_variables->city 					= $modelData->city;
		$modelData->payment_variables->state_name				= $modelData->state_name;
		$modelData->payment_variables->country					= $modelData->country;
		$modelData->payment_variables->postal_code 				= $modelData->postal_code;
		$modelData->payment_variables->email 					= $modelData->email;
		$modelData->payment_variables->card_type_name 			= $modelData->card_type_name;
		$modelData->payment_variables->card_number 				= $modelData->card_number;
		$modelData->payment_variables->card_expiration_month 	= $modelData->card_expiration_month;
		$modelData->payment_variables->card_expiration_year 	= $modelData->card_expiration_year;
		$modelData->payment_variables->card_security_code 		= $modelData->card_security_code;
		
		$buff = ob_get_contents();
		ob_end_clean(); 

		$modelData->val_rooms 		= $val_rooms;
		// $modelData->val_offers_rooms	= $val_offers_rooms;
		$modelData->total_payments 	= $total_payments;
		$modelData->total	 		= $val_rooms + $total_payments;
		$modelData->total_payed		= $total_payments_ok;
		//echo $buff;
		// exit;
		return $buff;
	}
	
	
	function getGuestDetails()
	{
		$gender = JText::_("LNG_ADDRESS_GUEST_TYPE_".$this->guest_type,true);
		ob_start();
		?>
		<?php echo isset($this->company_name)? $this->company_name."<br/>":"" ?>
		<?php echo  $gender.' '.$this->first_name.' '.$this->last_name?> <br/>
		<?php echo $this->address?><br/>							
		<?php echo $this->postal_code ." " ?>	<?php echo $this->city?><br/>
		<?php echo $this->country?><br/>
		T: <?php echo $this->tel?><br/>
		<?php if($this->itemAppSettings->hide_user_email != 1){ ?>
			<a href='mailto:<?php echo $this->email?>'><?php echo $this->email?></a><br/><br/>
		<?php } ?>
		<i><?php echo $this->details?></i>

		<?php
		$buff = ob_get_contents();
		ob_end_clean(); 

		return $buff;
	}

	function getPaymentInformation()
	{
		//dmp($this->itemPaymentProcessors);
		// exit;
		ob_start();
		$arr_payment_type 	= array();
		$arr_payment_info 	= array();
		$arr_conf_payment 	= array();
		
		//load existent payments
		if( count( $this->itemPayments ) > 0 )
		{
			foreach( $this->itemPayments as $p )
			{
				// $arr_payment_id[] 	= $p->paymentprocessor_id;
				$arr_payment_type[$p->paymentprocessor_id] = $p->paymentprocessor_type;
				foreach( $this->itemPaymentProcessors as $proc )
				{
					if( $proc->paymentprocessor_id == $p->paymentprocessor_id )
					{
						$arr_payment_info[ $p->paymentprocessor_id ] 			= $proc;
						$arr_payment_info[ $p->paymentprocessor_id ]->is_conf	= true;	
						$arr_conf_payment[ $p->paymentprocessor_id ] 			= $p;
					}
				}
			}
		}
		//~load existent payments
		
		if( count($arr_payment_type)==0)
		{
			if( $this->payment_processor_sel_type == PROCESSOR_PAYPAL_EXPRESS )
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_PAYPAL_EXPRESS;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_PAYFLOW)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_PAYFLOW;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_AUTHORIZE)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_AUTHORIZE;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_BANK_ORDER)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_BANK_ORDER;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_CASH)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_CASH;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_MPESA)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_MPESA;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_IDEAL_OMNIKASSA)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_IDEAL_OMNIKASSA;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_BUCKAROO)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_BUCKAROO;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_4B)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_4B;
			}
			else if( $this->payment_processor_sel_type == PROCESSOR_EENMALIGE_INCASO)
			{
				$arr_payment_type[$this->payment_processor_sel_id] = PROCESSOR_EENMALIGE_INCASO;
			}
				
			foreach( $this->itemPaymentProcessors as $proc )
			{
			// dmp($proc);
				if( $proc->paymentprocessor_id == $this->payment_processor_sel_id )
				{
					$arr_payment_info[ $proc->paymentprocessor_id ] 			= $proc;
					$arr_payment_info[ $proc->paymentprocessor_id ]->is_conf 	= false;	
				}
			}
		}
		// dmp($this->payment_processor_sel_type);
		// dmp($arr_payment_type);
		// dmp($arr_conf_payment);
		// exit;
		echo "<ul style='margin:0px;padding-left: 0;list-style:none'>";
		foreach( $arr_payment_type as $id => $type)
		{
			echo "<li style='margin-left: 0px'>";
			switch($type)
			{
				case PROCESSOR_PAYFLOW:
					echo JText::_('LNG_CREDIT_CARD_TYPE',true); ?>:<?php echo $this->card_type_name."<br/>";
					echo JText::_('LNG_CARD_NAME',true); ?>: <?php echo $this->card_name."<br/>";
					echo JText::_('LNG_CREDIT_CARD_NUMBER',true).":"; 
					$ex = $this->card_number;
					if( strlen($ex) <= 4 )
					{
						for( $i =0; $i < strlen($ex); $i++ )
						{
							echo str_repeat("X", strlen($ex[$i]));
							if( $i < count($ex)-1 )
								echo "-";
						}
					}
					else
						echo str_repeat("*", strlen($this->card_number)-4).substr($this->card_number,-4);
					
					echo "<br/>";
					echo JText::_('LNG_EXPIRATION_DATE',true).": ".$this->card_expiration_month .' / '.$this->card_expiration_year;
					
					echo "<br/>";
					if( isset($arr_conf_payment[$id]) )
					{
						// dmp($arr_conf_payment[$id]);
						echo "<B>".$arr_conf_payment[$id]->tip."&nbsp;".$arr_conf_payment[$id]->payment_status."</B>&nbsp;&nbsp;";
						echo $arr_conf_payment[$id]->payment_explication." = ";
						if($arr_conf_payment[$id]->payment_percent > 0 )
						{
							echo $arr_conf_payment[$id]->payment_percent.'%&nbsp;';
						}
						else if($arr_conf_payment[$id]->payment_value > 0 )
						{
							echo JHotelUtil::my_round($arr_conf_payment[$id]->payment_value,2).' &nbsp;';
						}
					}
					break;
					case PROCESSOR_AUTHORIZE:
						echo JText::_('LNG_CREDIT_CARD_TYPE',true); ?>:<?php echo $this->card_type_name."<br/>";
						echo JText::_('LNG_CARD_NAME',true); ?>: <?php echo $this->card_name."<br/>";
						echo JText::_('LNG_CREDIT_CARD_NUMBER',true).":"; 
						$ex = $this->card_number;
						if( strlen($ex) <= 4 )
						{
							for( $i =0; $i < strlen($ex); $i++ )
							{
								echo str_repeat("X", strlen($ex[$i]));
								if( $i < count($ex)-1 )
									echo "-";
							}
						}
						else
							echo str_repeat("*", strlen($this->card_number)-4).substr($this->card_number,-4);
						
						echo "<br/>";
						echo JText::_('LNG_EXPIRATION_DATE',true).": ".$this->card_expiration_month .' / '.$this->card_expiration_year;
						
						echo "<br/>";
						if( isset($arr_conf_payment[$id]) )
						{
							// dmp($arr_conf_payment[$id]);
							echo "<B>".$arr_conf_payment[$id]->tip."&nbsp;".$arr_conf_payment[$id]->payment_status."</B>&nbsp;&nbsp;";
							echo $arr_conf_payment[$id]->payment_explication." = ";
							if($arr_conf_payment[$id]->payment_percent > 0 )
							{
								echo $arr_conf_payment[$id]->payment_percent.'%&nbsp;';
							}
							else if($arr_conf_payment[$id]->payment_value > 0 )
							{
								echo JHotelUtil::my_round($arr_conf_payment[$id]->payment_value,2).' &nbsp;';
							}
						}
						break;
				case PROCESSOR_PAYPAL_EXPRESS:
					echo JText::_('LNG_PROCESSOR_PAYPAL_EXPRESS',true);
					break;
				case PROCESSOR_IDEAL_OMNIKASSA:
					echo JText::_('LNG_PROCESSOR_IDEAL_OMNIKASSA',true);
					break;
				case PROCESSOR_BUCKAROO:
					echo JText::_('LNG_PROCESSOR_BUCKAROO',true);
					break;
				case PROCESSOR_4B:
					echo JText::_('LNG_PROCESSOR_4B',true);
					break;
				case PROCESSOR_BANK_ORDER:
					
					$str = str_replace(EMAIL_MAX_DAYS_PAYD,							$arr_payment_info[$id]->paymentprocessor_timeout_days,	isset($this->itemHotelSelected->types) & $this->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PROCESSOR_PAYMENT_MANUAL_PARK',true) : JText::_('LNG_PROCESSOR_PAYMENT_MANUAL',true));
					$str = str_replace(EMAIL_RESERVATION_COST, 					    JHotelUtil::fmt( ($this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed),2),	$str);
					echo $str;
					
					?>

					<TABLE cellpadding=0 cellspacing=0 width=100%>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_BANK_NAME',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_payment_info[$id]->paymentprocessor_name?></B>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_BANK_ADDRESS',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_address?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_BANK_CITY',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_city?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_BANK_COUNTRY',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_country?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_BANK_HOLDER_NAME',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_username?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_ACCOUNT_NUMBER',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_number?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_SWIFT_CODE',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_swift_code?>
							</TD>
						</TR>
						<!--TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_PROCESSOR_TIMEOUT_DAYS',true)?> :
							</TD>
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_timeout_days>0?$arr_payment_info[$id]->paymentprocessor_timeout_days : ''?>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_OTHER_INFO',true)?> :
							</TD> 
							<TD>
								<?php echo $arr_payment_info[$id]->paymentprocessor_other_infos?>
							</TD>
						</TR-->
					</TABLE>
					<?php
					break;
				case PROCESSOR_CASH:
					echo  JText::_('LNG_PROCESSOR_CASH',true);
					break;
				case PROCESSOR_EENMALIGE_INCASO:
					echo  "<strong>".JText::_('LNG_PROCESSOR_EENMALIGE_INCASO',true)."</strong><br/>";
					
					$emailText = JText::_('LNG_PROCESSOR_EENMALIGE_INCASO_EMAIL_TEXT',true);
					$emailText = str_replace(EMAIL_RESERVATION_COST, number_format( $this->total_cost,2),	$emailText);
						
					echo  $emailText;
					break;
				case PROCESSOR_MPESA:
					//dmp($this->itemPaymentProcessors);
					?>
					<TABLE cellpadding=0 cellspacing=0 width=100%>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_PROCESSOR_TYPE',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_payment_info[$id]->paymentprocessor_front_name?></B>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_PROCESSOR_VENDOR',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_payment_info[$id]->paymentprocessor_username?></B>
							</TD>
						</TR>
						<?php
						if( isset($arr_conf_payment[$id]) )
						{
						?>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_NAME',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_conf_payment[$id]->payment_name?></B>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_TELEPHONE_NUMBER',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_conf_payment[$id]->payment_tel?></B>
							</TD>
						</TR>
						<TR>
							<TD align=left width=40% nowrap>
								<?php echo JText::_('LNG_MPESA_CODE',true)?> :
							</TD> 
							<TD>
								<B><?php echo $arr_conf_payment[$id]->payment_code?></B>
							</TD>
						</TR>
						<?php
						}
						?>
					</TABLE>
					<?php
					// dmp($arr_payment_info);
					// exit;
					break;
			}
			echo "</li>";
		}
		echo "</ul>";
		$buff = ob_get_contents();
		ob_end_clean(); 
		// dmp($buff);
		// exit;
		return $buff;
	}

	
	function getPaymentInformation_old()
	{
		ob_start();
		?>	
		<?php echo JText::_('LNG_CREDIT_CARD_TYPE',true); ?>:<?php echo $this->card_type_name?>  <br/>
		<?php echo JText::_('LNG_CARD_NAME',true); ?>: <?php echo $this->card_name?><br/>
		<?php echo JText::_('LNG_CREDIT_CARD_NUMBER',true); ?>: 
		<?php 
			$ex = $this->card_number;
			if( strlen($ex) <= 4 )
			{
				for( $i =0; $i < strlen($ex); $i++ )
				{
					echo str_repeat("X", strlen($ex[$i]));
					if( $i < count($ex)-1 )
						echo "-";
				}
			}
			else
				echo str_repeat("*", strlen($this->card_number)-4).substr($this->card_number,-4);
		?><br/>
		<?php echo JText::_('LNG_EXPIRATION_DATE',true); ?>: <?php echo $this->card_expiration_month .' / '.$this->card_expiration_year?>
		<?php
		$buff = ob_get_contents();
		ob_end_clean(); 
		return $buff;
	}

	function getConfirmation()
	{
		ob_start();
		?>
		<div class="col100">
			<table class="admintable" width=100% cellspacing=0 >
				<tr>
					<td>
						<table cellspacing="0" cellpadding="0" border="0" width="700" align="center">
							<tbody>
								<tr style="">
									<td align=left colspan=5>
										<div class="reservation_info">
											<?php 
												echo $this->itemHotelSelected->hotel_name;
												echo strlen($this->itemHotelSelected->country_name) > 0 ? ", " : "";
												echo $this->itemHotelSelected->country_name;
												echo strlen($this->itemHotelSelected->hotel_county) > 0 ? ", " : "";
												echo $this->itemHotelSelected->hotel_county;
												echo strlen($this->itemHotelSelected->hotel_city) > 0 ? ", " : "";
												echo $this->itemHotelSelected->hotel_city;
												echo strlen($this->itemHotelSelected->hotel_address) > 0 ? ", " : "";
												echo $this->itemHotelSelected->hotel_address;
												
												echo "<BR>";
												echo ' <strong>'.JText::_('LNG_STAY',true).'</strong>: ';
												$data_1 = mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start );
											
												echo JText::_( substr(strtoupper(date( 'l', $data_1 ,true)),0,3));
												echo ',';
												echo JText::_( strtoupper(date( 'F', $data_1)));
												echo ',';
												echo date( ' d, Y', $data_1 );
												
												echo " ".JText::_('LNG_TO',true)." ";
											
												$data_2 = mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end );
											
												echo JText::_( substr(strtoupper(date( 'l', $data_2)),0,3));
												echo ',';
												echo JText::_( strtoupper(date( 'F', $data_2)));
												echo ',';
												echo date( ' d, Y', $data_2 );
												
											?>
										</div>	
									</td>
								</tr>
								<tr>
									<td align=left>	
										<table cellspacing="0" cellpadding="0" border="0" width="100%" >
											<thead>
												<tr >
													<th class="rsv_dtls_main_header" bgcolor="#d9e5ee" align="left" width="48.5%" style="padding:5px 9px 6px 9px;border:1px solid #bebcb7;border-bottom:none;line-height:1em"><?php echo JText::_('LNG_BILLING_INFORMATION',true)?>:</th>
													<th width="3%" class="no-background"></th>
													<th class="rsv_dtls_main_header" bgcolor="#d9e5ee" align="left" width="48.5%" style="padding:5px 9px 6px 9px;border:1px solid #bebcb7;border-bottom:none;line-height:1em"><?php echo JText::_('LNG_PAYMENT_METHOD',true)?>:</th>	
												</tr>
											</thead>
											<tbody>
											<tr>
												<td valign="top" style="padding:7px 9px 9px 9px;border:1px solid #bebcb7;border-top:0;background:#f8f7f5">
												   <?php echo $this->Guest_Details?>
												</td>
												<td>&nbsp;</td>
												<td valign="top" style="padding:7px 9px 9px 9px;border:1px solid #bebcb7;border-top:0;background:#f8f7f5">
													<?php echo $this->Payment_Information?>
												</td>
											</tr>
											</tbody>
										</table>
										<br>
									</td>
								</tr>
								<tr>
									<td valign=top>
										<?php echo $this->Reservation_Details_EMail?>
										<br>
									</td>
								</tr>
								<tr>
									<td align=left>	
										<?php echo $this->GuestsDetails?>
										<br>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</div> 
		<?php 
		$buff = ob_get_contents();
		ob_end_clean(); 

		return $buff;
	}
		
	function addUser()
	{
		$db		=JFactory::getDBO();
		$query = " SELECT * 
					FROM #__users 
						WHERE 
							username 	= '".$this->email."' 
							OR
							email 		= '".$this->email."' 
						";
		$db->setQuery( $query );
		if (!$db->query() ) 
		{
			return false;
		}
		$config = JFactory::getConfig();
	 //dmp($db->getNumRows());
	// exit;
		if( $db->getNumRows() == 0 )
		{
			$acl =JFactory::getACL();
 
			/* get the com_user params */
			 
			jimport('joomla.application.component.helper'); 							// include libraries/application/component/helper.php
			$usersParams = &JComponentHelper::getParams( 'com_users' ); 				// load the Params
			 
			// "generate" a new JUser Object
			$user = JFactory::getUser(0); 												// it's important to set the "0" otherwise your admin user information will be loaded
			 
			$data = array(); // array for all user settings
			$data['groups']= array(); 
			 
			// get the default usertype
			$usertype = $usersParams->get( 'new_usertype' );
			if (!$usertype) 
			{
				$usertype = 'Registered';
			}
			 
			// set up the "main" user information
			 
			$data['name'] 		=   $this->last_name.' '.$this->first_name; 			// add first- and lastname
			$data['username'] 	= 	$this->email; 										// add username
			$data['email'] 		= 	$this->email; 										// add email
			if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
			{
				$data['gid'] 		= 	$acl->get_group_id( '', $usertype, 'ARO' ); 	// generate the gid from the usertype
				/* no need to add the usertype, it will be generated automaticaly from the gid */
				$data['password']	=  	$this->email; 									// set the password
				$data['password2'] 	=  	$this->email; 									// confirm the password
			}
			else 
			{
				$data['password'] 	= 	$this->generatePassword( $this->email, true );
				$data['usertype']	= 	'deprecated';
				$system	= $usersParams->get('new_usertype', 2);//Registered group
				$data['groups'][0] = $system;
			}

			$data['sendEmail'] 	= 	0; 													// should the user receive system mails?
			 
			/* Now we can decide, if the user will need an activation */
			//  dmp($usersParams);
			 // exit;
			$useractivation = $usersParams->get( 'useractivation' ); 					// in this example, we load the config-setting
			if ($useractivation == 1) 
			{							 												// yeah we want an activation
				$data['sitename']	= $config->get('sitename');
				$data['siteurl']	= JUri::base();
				

				$data['sitename']	= $config->get('sitename');
				$data['siteurl']	= JUri::base();
				
				
				jimport('joomla.user.helper'); 											// include libraries/user/helper.php
				$data['block'] = 1; 													// block the User
				$data['activation'] =JUtility::getHash( JUserHelper::genRandomPassword()); // set activation hash (don't forget to send an activation email)

				$uri = JURI::getInstance();
				$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
				$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);
				
				$emailSubject	= JText::sprintf(
								'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
				);
				
				$emailBody = JText::sprintf(
								'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password']);
		
				$fromName		= $this->itemAppSettings->company_name;//$mainframe->getCfg('fromname');
				$confirmEmail	= $this->itemAppSettings->company_email;//$mainframe->getCfg('fromname');
				$response = JMail::sendMail($confirmEmail, $fromName, $data['email'], $emailSubject, $emailBody);
//				$return = JMail::sendMail($this->itemHotelSelected->email,$this->itemHotelSelected->hotel_name, $data['email'], $emailSubject, $emailBody);
				
				if ($response !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED',true));
					JError::raiseWarning(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED',true) );
				}
			}
			else { // no we need no activation
			 
				$data['block'] = 0; // don't block the user
			 
			}
			 
			
			if(!isset($user)){
				return false;
			}
			
			if (!$user->bind($data)) { 													// now bind the data to the JUser Object, if it not works....
				JError::raiseWarning('', JText::_('LNG_COULD_NOT_CREATE_USER',true) );			// ...raise an Warning
				return false; 															// if you're in a method/function return false
			}
	
			if (!$user->save()) { 														// if the user is NOT saved...
				JError::raiseWarning('', JText::_('LNG_COULD_NOT_CREATE_USER',true)); 				// ...raise an Warning
				return false; 															// if you're in a method/function return false
			}
			
			if( JHotelUtil::getCurrentJoomlaVersion() > 1.5 )
			{
				$this->addUserToGroup($user->id,$data['groups']);
			}

			$userTable =  $this->getTable('HotelUsers');
			$obj->user_id			= $user->id;
			$obj->first_name				=	$this->first_name;
			$obj->last_name					=	$this->last_name;
			$obj->details					=	$this->details;
			$obj->address					=	$this->address;
			$obj->postal_code				=	$this->postal_code;
			$obj->city						=	$this->city;
			$obj->state_name				=	$this->state_name;
			$obj->country					=	$this->country;
			$obj->tel						=	$this->tel;
			$obj->email						=	$this->email;

		
			if (!$userTable->bind($obj)) { 	
				JHotelReservationModelVariables::writeMessage("Bind error: add hotel user --".$this->_db->getErrorMsg());
				// now bind the data to the JUser Object, if it not works....
				JError::raiseWarning('', JText::_('LNG_COULD_NOT_CREATE_USER',true) );			// ...raise an Warning
				dmp($this->_db->getErrorMsg());
				dmp("bind error");
				return false; 															// if you're in a method/function return false
			}

			if (!$userTable->check())
			{
				JHotelReservationModelVariables::writeMessage("Check error: add hotel user --".$this->_db->getErrorMsg());
				JError::raiseWarning('', JText::_('LNG_COULD_NOT_CREATE_USER',true) );			// ...raise an Warning
				dmp($this->_db->getErrorMsg());
				dmp("check error");
				return false; 
			}
			
			if (!$userTable->store()) { 														// if the user is NOT saved...
				JHotelReservationModelVariables::writeMessage("Store error: add hotel user -- ".$this->_db->getErrorMsg());
				JError::raiseWarning('', JText::_('LNG_COULD_NOT_CREATE_USER',true)); 				// ...raise an Warning
				dmp($this->_db->getErrorMsg());
				dmp("store error");
				
				return false; 															// if you're in a method/function return false
			}
			return true;
		}
		else{
			$userTable =  $this->getTable('managehotelusers');
			$db->setQuery( $query );
			$joomlaUser = $db->loadObject();
			$userInfo = $userTable->getUserById($joomlaUser->id);
			if(isset($userInfo->id)){
				$userTable->load($userInfo->id);
				$userTable->first_name				=	$this->first_name;
				$userTable->last_name				=	$this->last_name;
				$userTable->address					=	$this->address;
				$userTable->postal_code				=	$this->postal_code;
				$userTable->city					=	$this->city;
				$userTable->state_name				=	$this->state_name;
				$userTable->country					=	$this->country;
				$userTable->tel						=	$this->tel;
				if (!$userTable->store()) {
					// if the user is NOT saved...
					JHotelReservationModelVariables::writeMessage("Store error: update hotel user -- ".$this->_db->getErrorMsg());
					return false; 															// if you're in a method/function return false
				}		
			}
			else 				
				JHotelReservationModelVariables::writeMessage("Hotel User not found");
		}
		return true;
	}
	function addUserToGroup($userId,$groupId){
		$db		=JFactory::getDBO();
		$query = " insert into #__user_usergroup_map values($userId,$groupId)";

		$db->setQuery( $query );
		if (!$db->query() )
		{
			//return false;
		}
	}
	function getUserByEmail($email){
		$usersTable =  $this->getTable('users');
		return $usersTable->getUserByEmail($email);
	}
	
	function setConfirmationUser(){
		try
		{
			$userData = $this->getUserByEmail($this->email);
			if(isset($userData->id) && $userData->id>0){
				$confirmationsTable =  $this->getTable('confirmations');
				$confirmationsTable->load($this->confirmation_id);
				$confirmationsTable->user_id =  $userData->id;
				$confirmationsTable->store();
			}
			else{
				JHotelReservationModelVariables::writeMessage("Cannot find user for email-".$this->email);
			}
		}
		catch(Exception $e){
			JHotelReservationModelVariables::writeMessage("Exception when adding user_id to confirmtion".$e->getMessage());
			echo 'Caught exception: '.$e->getMessage()."\n";
			break;
		}
	}
	
	function generatePassword($text, $is_cripted = false)
	{
		$password 	=  $text;   
		if( $is_cripted ==false )
			return $password;
			
		$salt 		= JUserHelper::genRandomPassword(32);  
		$crypt 		= JUserHelper::getCryptedPassword($password, $salt);  
		$password 	= $crypt.":".$salt;  
		return $password;
	}
	
	
	function prepareEmail( $templEmail )
	{
		$data_1 =  $this->year_start.'-'.$this->month_start.'-'.$this->day_start;
		$datas = JHotelUtil::getDateGeneralFormat($data_1);
		$data_2 = $this->year_end.'-'.$this->month_end.'-'.$this->day_end;
		$datae = JHotelUtil::getDateGeneralFormat($data_2);
		
		$ratingURL='<a href="'.JURI::root().'index.php?option=com_jhotelreservation&controller=hotelratings&view=hotelratings&confirmation_id='.$this->confirmation_id.'">'.JText::_('LNG_CLICK_TO_RATE',true).'</a>';

		//$templEmail = str_replace("\r\n",'<BR>', $templEmail); -- this causes many view problems / don not use it anymore
		$companyLogo = "<img src=\"".JURI::base() ."/components/".getBookingExtName().'/img/logo.jpg'."\" alt=\"Company logo\" />";
		
		$chekInTime = $this->itemHotelSelected->informations->check_in;
		$chekOutTime = $this->itemHotelSelected->informations->check_out;
		$hotelName = $this->itemHotelSelected->hotel_name;
		$cancellationPolicy =  $this->itemHotelSelected->informations->cancellation_conditions;
		$touristTax = $this->itemHotelSelected->informations->city_tax_percent==1? $this->itemHotelSelected->informations->city_tax + '% ': JHotelUtil::fmt($this->itemHotelSelected->informations->city_tax, 2);
		
		$templEmail = str_replace(EMAIL_COMPANY_LOGO, 									$companyLogo,						$templEmail);
		$templEmail = str_replace(EMAIL_SOCIAL_SHARING, 								"",									$templEmail);
		
		$gender = JText::_("LNG_EMAIL_GUEST_TYPE_".$this->guest_type,true);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_RESERVATIONGENDER),			$gender,						$templEmail);
		$templEmail = str_replace(EMAIL_RESERVATIONGENDER, 								$gender,						$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_RESERVATIONFIRSTNAME),			'',								$templEmail);
		$templEmail = str_replace(EMAIL_RESERVATIONFIRSTNAME, 							'',								$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_RESERVATIONLASTNAME), 			$this->last_name,					$templEmail);
		$templEmail = str_replace(EMAIL_RESERVATIONLASTNAME, 							$this->last_name,					$templEmail);
		

		$templEmail = str_replace(EMAIL_START_DATE, 									$datas,								$templEmail);
		$templEmail = str_replace(EMAIL_END_DATE,	 									$datae,								$templEmail);
		$templEmail = str_replace(EMAIL_CHECKIN_TIME, 									$chekInTime,						$templEmail);
		$templEmail = str_replace(EMAIL_CHECKOUT_TIME, 									$chekOutTime,						$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_RESERVATIONDETAILS),			$this->Reservation_Details_EMail, 	$templEmail);
		$templEmail = str_replace(EMAIL_RESERVATIONDETAILS,								$this->Reservation_Details_EMail, 	$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_BILINGINFORMATIONS),			$this->Guest_Details,				$templEmail);
		$templEmail = str_replace(EMAIL_BILINGINFORMATIONS,								$this->Guest_Details, 				$templEmail);

		$templEmail = str_replace(htmlspecialchars(EMAIL_PAYMENT_METHOD),				$this->Payment_Information,			$templEmail);
		$templEmail = str_replace(EMAIL_PAYMENT_METHOD,									$this->Payment_Information, 		$templEmail);

		$templEmail = str_replace(htmlspecialchars(EMAIL_GUEST_DETAILS),				$this->GuestsDetails,				$templEmail);
		$templEmail = str_replace(EMAIL_GUEST_DETAILS,									$this->GuestsDetails, 				$templEmail);
		
		
		$templEmail = str_replace(EMAIL_HOTEL_CANCELATION_POLICY, 						$cancellationPolicy,				$templEmail);
		$templEmail = str_replace(EMAIL_HOTEL_NAME, 									$hotelName,							$templEmail);
		$templEmail = str_replace(EMAIL_TOURIST_TAX, 									$touristTax,						$templEmail);
		
		$emailText = "";
		
		if($this->payment_processor_sel_type == PROCESSOR_BANK_ORDER){
			$emailText = JText::_('LNG_PROCESSOR_BANK_TRANSFER_EMAIL_TEXT',true);
			$emailText = str_replace(EMAIL_RESERVATION_COST, 								JHotelUtil::my_round( $this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed,2),	$emailText);
			$emailText = str_replace(EMAIL_RESERVATION_ID, 									$this->JHotelUtil::getStringIDConfirmation(),						$emailText);
		}
		
		if($this->payment_processor_sel_type == PROCESSOR_EENMALIGE_INCASO){
			$emailText = JText::_('LNG_PROCESSOR_EENMALIGE_INCASO_EMAIL_TEXT',true);
			$emailText = str_replace(EMAIL_RESERVATION_COST, 								JHotelUtil::my_round( $this->total_cost,2),	$emailText);
		}
		
		$templEmail = str_replace(EMAIL_BANK_TRANSFER_DETAILS,							$emailText, 						$templEmail);
		
		$templEmail = str_replace(EMAIL_RATING_URL,										$ratingURL, 						$templEmail);
				
		//$templEmail = str_replace(htmlspecialchars(EMAIL_PLACEHOLDER),				$placeholder,						$templEmail);
		//$templEmail = str_replace(EMAIL_PLACEHOLDER,									$placeholder, 						$templEmail);
	

		$fromName	= $this->itemAppSettings->company_name;

		if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
		{
			global $mainframe;  
			if(strlen($fromName)==0)
				$fromName	= $mainframe->getCfg('fromname'); 
		}
		else
		{
			if(strlen($fromName)==0){
				$config =JFactory::getConfig();
				$fromName	=  $config->get( 'config.fromname' );
			}
		}

		$templEmail = str_replace(htmlspecialchars(EMAIL_COMPANY_NAME),					$fromName,							$templEmail);
		$templEmail = str_replace(EMAIL_COMPANY_NAME,									$fromName, 							$templEmail);
	
		return $templEmail;
	}

	function prepareGuestDetails()
	{
		$htmlContent ='';		
		
		if(isset($this->guest_first_name) && count($this->guest_first_name) > 0 ){
			$htmlContent ='<table cellspacing="0" cellpadding="0" border="0" width="100%" >';
			$htmlContent .='<thead>';
			$htmlContent .='<tr>';
			$htmlContent .='<th class="rsv_dtls_main_header" bgcolor="#d9e5ee" align="left" width="48.5%" style="padding:5px 9px 6px 9px;border:1px solid #bebcb7;border-bottom:none;line-height:1em">'. JText::_('LNG_GUEST_DETAILS',true) .'</th>';
			$htmlContent .='</tr>';
			$htmlContent .='</thead>';
			$htmlContent .='<tbody>';
			$htmlContent .='<tr>';
			$htmlContent .='<td valign="top" style="padding:7px 9px 9px 9px;border:1px solid #bebcb7;border-top:0;background:#f8f7f5">';
			$htmlContent .='<table cellspacing="0" cellpadding="3" border="0">';
			$htmlContent .='<tr><th>'.JText::_('LNG_FIRST_NAME',true).'&nbsp;&nbsp;</th><th>'.JText::_('LNG_LAST_NAME',true).'&nbsp;&nbsp;</th><th>'.JText::_('LNG_PASSPORT_NATIONAL_ID',true).'</th></tr>';
			for($i=0;$i<count($this->guest_first_name);$i++){
				$htmlContent .= "<tr><td>".$this->guest_first_name[$i]."</td><td>".$this->guest_last_name[$i]."</td><td>".$this->guest_identification_number[$i]."</td><tr>";
			}
			$htmlContent .='</table>';
			$htmlContent .='</td>';
			$htmlContent .='</tr>';
			$htmlContent .='</tbody>';
			$htmlContent .='</table>';
		}
		return $htmlContent;
	}
	
	function preparePendingEmail( $id, $templEmail )
	{
		$query = " 	SELECT 
						*
					FROM #__hotelreservation_confirmations
					WHERE 
						confirmation_id = $id
					";
		// echo $query;
		//$this->_db->setQuery( $query );
		$results = $this->_getList( $query );
		if( count($results) == 0 )
			return '';
		foreach( $results as $res )
		{
			$reservationDetails = '
			<div class="col100">
				'.$res->email_confirmation.'
			</div>'; 
			
			$bilingInformations =  	''; 
			$placeholder 		= 	'';
			
			$templEmail = str_replace("\r\n",'<BR>', $templEmail);
			$templEmail = str_replace(htmlspecialchars(EMAIL_RESERVATIONDETAILS),			$reservationDetails, 				$templEmail);
			$templEmail = str_replace(EMAIL_RESERVATIONDETAILS,								$reservationDetails, 				$templEmail);
			
			$templEmail = str_replace(htmlspecialchars(EMAIL_BILINGINFORMATIONS),			$bilingInformations,				$templEmail);
			$templEmail = str_replace(EMAIL_BILINGINFORMATIONS,								$bilingInformations, 				$templEmail);
			
			$templEmail = str_replace(htmlspecialchars(EMAIL_PLACEHOLDER),					$placeholder,						$templEmail);
			$templEmail = str_replace(EMAIL_PLACEHOLDER,									$placeholder, 						$templEmail);
		
			
			$fromName	= $this->itemAppSettings->company_name;
			if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
			{
				global $mainframe;  
				if(strlen($fromName)==0)
					$fromName	= $mainframe->getCfg('fromname'); 
			}
			else
			{
				$config =JFactory::getConfig();
				$fromName	=  $config->get( 'config.mailfrom' );
			}
			$templEmail = str_replace(htmlspecialchars(EMAIL_COMPANY_NAME),					$fromName,							$templEmail);
			$templEmail = str_replace(EMAIL_COMPANY_NAME,									$fromName, 							$templEmail);
		}
	
		return "<html><body>".$templEmail.'</body></html>';
	}

	
	function getTemplateEmail( $hotel_id, $status_id )
	{
		if( $status_id == CANCELED_ID )
			$query = " SELECT * FROM #__hotelreservation_emails WHERE hotel_id=$hotel_id and is_default  = 1 ".($status_id == CANCELED_ID? " AND email_type = 'Cancelation Email' " : " AND email_type = 'Reservation Email'  ");
		else if( $status_id == CANCELED_PENDING_ID )
			$query = " SELECT * FROM #__hotelreservation_emails WHERE hotel_id=$hotel_id and is_default  = 1 ".($status_id == CANCELED_PENDING_ID? " AND email_type = 'Cancelation Pending Email' " : " AND email_type = 'Reservation Email'  ");
		else
			$query = " SELECT * FROM #__hotelreservation_emails WHERE hotel_id=$hotel_id and is_default  = 1 AND email_type = 'Reservation Email'  ";

		$this->_db->setQuery( $query );
		$templ = $this->_db->loadObject();
	
		return $templ;
	}
	
	function getEmailTemplate($template)
	{
		$query = ' SELECT * FROM #__hotelreservation_emails WHERE hotel_id="'.$this->hotel_id.'" AND is_default  = 1 AND email_type = "'.$template.'"';
		$this->_db->setQuery( $query );
		$templ= $this->_db->loadObject();
		return $templ;
	}
	
	function sendHotelEmail($data){
		//dmp($data);
		$body = $data["email_note"];
		$mode		 = 1 ;//html
		//dmp($body);
		$ret = JMail::sendMail(
			$data["email_from_address"],
			$data["email_from_name"],
			$data["email_to_address"],
			"Share hotel",
			$body,
			$mode
			);
		
		if($data["copy_yourself"]==1){
			$ret = JMail::sendMail(
				$data["email_from_address"],
				$data["email_from_name"],
				$data["email_from_address"],
				"Share hotel",
				$body,
				$mode
				);
		}
		return $ret;
	}
	
	function sendNoAvailabilityEmail(){
		$email = JText::_('LNG_NO_AVAILABILITY_EMAIL',true);
		
		//return false;
		//$config =JFactory::getConfig();
		//$this->itemAppSettings->sendmail_from = $config->get( 'config.mailfrom' );
		//$this->itemAppSettings->sendmail_name = $config->get( 'config.fromname' );
		
		$fromName		= $this->itemAppSettings->company_name;//$mainframe->getCfg('fromname');
		$confirmEmail	= $this->itemAppSettings->company_email;//$mainframe->getCfg('fromname');
		
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);

		$datas =  JHotelUtil::convertToFormat($datas);
		$datae =  JHotelUtil::convertToFormat($datae);
		
		$mode		 = 1 ;//html
		$email = str_replace("<<hotel>>", $this->itemHotelSelected->hotel_name, $email);
		$email = str_replace("<<start_date>>", $datas, $email);
		$email = str_replace("<<end_date>>", $datae, $email);
		
		$email_subject = JText::_('LNG_NO_AVAILABILITY_EMAIL_SUBJECT',true);
		$email_subject = str_replace("<<hotel>>", $this->itemHotelSelected->hotel_name, $email_subject);
		JMail::sendMail(
				$this->itemAppSettings->company_email,
				$this->itemAppSettings->company_name,
				$this->itemAppSettings->company_email,
				$email_subject,
				$email,
				$mode,
				null,
				array( $this->itemHotelSelected->email)
		);
	}
	
	function sendEmail($status_id, $toAdminOnly=false)
	{
		$templ 		= $this->getTemplateEmail( $this->hotel_id, $status_id );
		//dmp($templ);
		if( $templ ==null ){
			JError::raiseNotice( 100, JText::_('LNG_EMAIL_TEMPLATE_NOT_FOUND',true));
			return false;
		}
		$templEmail = $this->prepareEmail( $templ->email_content  );
		//return false;
		$config =JFactory::getConfig();
		//$this->itemAppSettings->sendmail_from = $config->get( 'config.mailfrom' );
		//$this->itemAppSettings->sendmail_name = $config->get( 'config.fromname' );
		
		$fromName		= $this->itemAppSettings->company_name;//$mainframe->getCfg('fromname'); 
		$confirmEmail	= $this->itemAppSettings->company_email;//$mainframe->getCfg('fromname'); 
		
		$body		 = $templEmail;
		$mode		 = 1 ;//html
		$ret		 = true;
		//dmp($body);
		if(!$toAdminOnly){
			$toEmail = $this->email;
			$bcc = array($confirmEmail, $this->itemHotelSelected->email);
			if($this->itemAppSettings->hide_user_email == 1){
				$toEmail =$confirmEmail;
				$bcc = array($this->email, $this->itemHotelSelected->email);
			}
			$ret = JMail::sendMail(
										$this->itemAppSettings->company_email, 
										$this->itemAppSettings->company_name, 
										$toEmail, 
										$templ->email_subject, 
										$body, 
										$mode,
										null,
										$bcc
									);
			
			JHotelReservationModelVariables::writeMessage(" Sending emails to  $toEmail $bcc[0] $bcc[1]  result".$ret);
		}else{
			$subject = "Waiting confirmation with id".$this->confirmation_id." confirmed";
			$ret = JMail::sendMail(
					$this->itemAppSettings->company_email,
					$this->itemAppSettings->company_name,
					$confirmEmail,
					$subject,
					$body,
					$mode,
					null
			);
			
			JHotelReservationModelVariables::writeMessage(" Sending email to admin only  ".$confirmEmail."  result".$ret);
		}
		
		
		return $ret;

	}
	
	function sendCancelPendingEmail($id, $status_id, $send_2_user=true)
	{
		if($status_id != CANCELED_PENDING_ID)
			return;
			
		$templ 		= $this->getTemplateEmail($this->hotel_id, $status_id );
		if( $templ ==null )
			return false;
		$templEmail = $this->preparePendingEmail( $id, $templ->email_content  );
		if( strlen($templEmail) == 0 )
			return false;
		
		//return false;
		$config =JFactory::getConfig();
		//$this->itemAppSettings->sendmail_from = $config->get( 'config.mailfrom' );
		//$this->itemAppSettings->sendmail_name = $config->get( 'config.fromname' );
		
		$fromName		= $this->itemAppSettings->company_name;//$mainframe->getCfg('fromname'); 
		$confirmEmail	= $this->itemAppSettings->company_email;//$mainframe->getCfg('fromname'); 
		
		$body		 = $templEmail;
		$mode		 = 1 ;//html
		$ret		 = true;
		
		if($send_2_user==true)
			$ret = JMail::sendMail(
									$this->itemAppSettings->company_email, 
									$this->itemAppSettings->company_name, 
									$this->email, 
									$templ->email_subject, 
									$body, 
									$mode
								);
								
		if( strlen($confirmEmail) > 0)
		{
			JMail::sendMail(
									$this->itemAppSettings->company_email, 
									$this->itemAppSettings->company_name, 
									$confirmEmail, 
									$templ->email_subject, 
									$body, 
									$mode
								);
		}
		return $ret;

	}
	

	
	function sendReviewEmail()
	{
		$template = "Review Email";
		$templ 		= $this->getEmailTemplate($template );

		if( $templ ==null )
			return false;
		$templEmail = $this->prepareEmail( $templ->email_content);
		
		//return false;
		$config =JFactory::getConfig();
		//$this->itemAppSettings->sendmail_from = $config->get( 'config.mailfrom' );
		//$this->itemAppSettings->sendmail_name = $config->get( 'config.fromname' );
	
		$fromName		= $this->itemAppSettings->company_name;//$mainframe->getCfg('fromname');
		$confirmEmail	= $this->itemAppSettings->company_email;//$mainframe->getCfg('fromname');
	
		$hotelName = $this->itemHotelSelected->hotel_name;
		$templ->email_subject = str_replace(EMAIL_HOTEL_NAME, $hotelName,	$templ->email_subject);
		
		$body		 = $templEmail;
		$mode		 = 1 ;//html
		$ret		 = true;
		$ret = JMail::sendMail(
			$this->itemAppSettings->company_email,
			$this->itemAppSettings->company_name,
			$this->email,
			$templ->email_subject,
			$body,
			$mode
		);
		JHotelReservationModelVariables::writeMessage(" Sending review email to  ".$this->email."  result".$ret);
		
		return $ret;
	
	}
	
	function getRoomsCapacity($id)
	{
		$roomCapacity = array();
		$query = 	" SELECT 
							*
						FROM #__hotelreservation_confirmations_rooms 					r	
					".
					" WHERE r.confirmation_id = ".$id.
					" GROUP BY r.confirmation_room_id ";
		$res 	= $this->_getList( $query );
		$r		= array();
		foreach( $res as $value )
		{
			$r[ $value->room_id ] = array(0, $value->rooms);
		}
		
		return $r;
	}
	
	
	function getPackageNumbers($id)
	{
		$query = 	" SELECT 
							p.*,
							pk.package_number	AS nr_max
						FROM #__hotelreservation_confirmations_rooms_packages 				p
						LEFT JOIN #__hotelreservation_packages			 					pk	USING(package_id)
					".
					" WHERE p.confirmation_id = ".$id.
					" GROUP BY p.confirmation_package_id ";
		
		$res = $this->_getList( $query );
		$arr = array();
		foreach( $res as $value )
		{
			$arr[$value->package_id] = array( $value->nr_max, $value->package_number);
		}
		//dmp($arr);
		return $arr;
		
	}
		
	function getArrivalOptions( $arrival_option_ids )
	{

		$arrivalOptions = array();
		if($this->edit_mode)
			$arrival_option_ids = array();
		//~init
		if( $this->reserve_room_id !=0 )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_arrival_options 
						WHERE 
							is_available = 1
							AND
							hotel_id = '".$this->hotel_id."'
						ORDER BY arrival_option_name ";
			//$this->_db->setQuery( $query );
			$res = $this->_getList( $query );
			foreach( $res  as $value )
			{
				$is_init = true;
				if( !isset( $arrival_option_ids ))
				{
					//do nothing
				}
				else			
				{
					// dmp($value);
					foreach( $this->arrival_option_ids as $v )
					{
						if( 
							$v[0] == $this->reserve_offer_id
							&&
							$v[1] == $this->reserve_room_id
							&&
							$v[2] == $this->reserve_current
							&&
							$v[3] == $value->arrival_option_id
						)
						{
								$is_init = false;
								break;
						}
							
					}
				}
				if( $is_init == true)
				{
					$arrival_option_ids[  $this->reserve_offer_id.'|'.$this->reserve_room_id.'|'.$this->reserve_current.'|'.$value->arrival_option_id ] = array( $this->reserve_offer_id,$this->reserve_room_id,$this->reserve_current,$value->arrival_option_id, 0 );
				}
			}
		}
		//init
		// dmp($arrival_option_ids);
		foreach( $arrival_option_ids as $arr )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_arrival_options 
						
							WHERE 
							is_available = 1
							AND
							FIND_IN_SET( CONCAT(".$arr[0].", '|', ".$arr[1].", '|', ".$arr[2].", '|', arrival_option_id, '|' , ".$arr[4]."), '".(is_array($arr)? implode('|', $arr) : $arr)."')
						ORDER BY 
							arrival_option_name 
					";
			//dmp($query);
			$res = $this->_getList( $query );
			foreach( $res as $v )
			{
				$key = implode('|', $arr);
				$arrivalOptions[ $key ] 			= $v;
				$arrivalOptions[$key]->offer_id 	= $arr[0];
				$arrivalOptions[$key]->is_offer 	= $arr[0]>0?true:false;
				$arrivalOptions[$key]->room_id 		= $arr[1];
				$arrivalOptions[$key]->current 		= $arr[2];
				
			}
		}
		//dmp($arrival_option_ids);
		
		//set the display price
		
		$this->setArrivalOptionsDisplayPrice($arrivalOptions);
		return $arrivalOptions;
	}
	
	function getAirportTransferTypes( $airport_transfer_type_ids )
	{
	
		// dmp($airport_transfer_type_ids);
		$airportTransfers = array();
		//~init
		if( $this->reserve_room_id !=0 )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_airport_transfer_types 
						WHERE 
							is_available = 1
							AND
							hotel_id = '".$this->hotel_id."'
						ORDER BY airport_transfer_type_name ";
			//$this->_db->setQuery( $query );
			$res = $this->_getList( $query );
			foreach( $res  as $value )
			{
				$is_init = true;
				if( !isset( $airport_transfer_type_ids ) )
				{
					//do nothing
				}
				else			
				{
					// dmp($value);
					foreach( $this->airport_transfer_type_ids as $v )
					{
						if( 
							$v[0] == $this->reserve_offer_id
							&&
							$v[1] == $this->reserve_room_id
							&&
							$v[2] == $this->reserve_current
							&&
							$v[3] == $value->airport_transfer_type_id
						)
						{
								$is_init = false;
								break;
						}
							
					}
				}
				
				if( $is_init == true )
				{
					$airport_transfer_type_ids[  $this->reserve_offer_id.'|'.$this->reserve_room_id.'|'.$this->reserve_current.'|'.$value->airport_transfer_type_id ] = array( $this->reserve_offer_id,$this->reserve_room_id,$this->reserve_current,$value->airport_transfer_type_id );
				}
			}
		}
		//init
		foreach( $airport_transfer_type_ids as $arr )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_airport_transfer_types 
						
							WHERE 
							is_available = 1
							AND
							FIND_IN_SET( CONCAT(".$arr[0].", '|', ".$arr[1].", '|', ".$arr[2].", '|', airport_transfer_type_id), '".(is_array($arr)? implode('|', $arr) : $arr)."')
						ORDER BY 
							airport_transfer_type_name 
					";
			//dmp($query);
			$res = $this->_getList( $query );
			foreach( $res as $v )
			{
				$key = implode('|', $arr);
				$airportTransfers[ $key ] 			= $v;
				$airportTransfers[$key]->offer_id 	= $arr[0];
				$airportTransfers[$key]->room_id 	= $arr[1];
				$airportTransfers[$key]->current 	= $arr[2];
				
			}
		}
		//dmp($airportTransfers);
		
		//set the display price
		
		$this->setAirportTransferDisplayPrice($airportTransfers);
		return $airportTransfers;
		
	}
	
	function getArrivalAirlines( $airport_airline_ids )
	{
		$airportAirlines = array();
		//~init
		if( $this->reserve_room_id !=0 )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_airlines 
						WHERE 
							is_available = 1
							AND
							hotel_id = '".$this->hotel_id."'
						ORDER BY airline_name ";
			//$this->_db->setQuery( $query );
			$res = $this->_getList( $query );
			foreach( $res  as $value )
			{
				$is_init = true;
				if( !isset( $airport_airline_ids ) )
				{
					//do nothing
				}
				else			
				{
					// dmp($value);
					foreach( $this->airport_airline_ids as $v )
					{
						if( 
							$v[0] == $this->reserve_offer_id
							&&
							$v[1] == $this->reserve_room_id
							&&
							$v[2] == $this->reserve_current
							&&
							$v[3] == $value->airline_id
						)
						{
								$is_init = false;
								break;
						}
							
					}
				}
				
				if( $is_init == true )
				{
					$airport_airline_ids[  $this->reserve_offer_id.'|'.$this->reserve_room_id.'|'.$this->reserve_current.'|'.$value->airline_id ] = array( $this->reserve_offer_id,$this->reserve_room_id,$this->reserve_current,$value->airline_id );
				}
			}
		}
		//init
		foreach( $airport_airline_ids as $arr )
		{
			$query = " SELECT 
							* 
						FROM #__hotelreservation_airlines 
						
							WHERE 
							is_available = 1
							AND
							FIND_IN_SET( CONCAT(".$arr[0].", '|', ".$arr[1].", '|', ".$arr[2].", '|', airline_id), '".(is_array($arr)? implode('|', $arr) : $arr)."')
						ORDER BY 
							airline_name 
					";
			//dmp($query);
			$res = $this->_getList( $query );
			foreach( $res as $v )
			{
				$key = implode('|', $arr);
				$airportAirlines[ $key ] 			= $v;
				$airportAirlines[$key]->offer_id 	= $arr[0];
				$airportAirlines[$key]->room_id 	= $arr[1];
				$airportAirlines[$key]->current 	= $arr[2];
				
			}
		}
		return $airportAirlines;
		
	}

	
	function getRoomNumbers(){
		$query = "  SELECT
								r.*, 
								r_n.numbers_available					AS max_count_reservation
							
						FROM #__hotelreservation_rooms 					r
						";
		$reservation = $this->_getList( $query );
		foreach( $reservation as $value )
		{
			$this->room_available_ids[]  = $value->room_id;
		}
		
	}
	
	function checkAvalability($userData = array(), $is_check_numbers = false )
	{
// 		dmp($this);
		//exit;
		//dmp($this->guest_adult);
		$this->cleanAllUnwantedReservations();

		$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )			);

		//not used for now
		/*if(!$this->isHotelAvailable($this->hotel_id, $datas,$datae)){
			return false;
		} */
		
		if( is_array($this->option_ids) )	
		{
			foreach( $this->option_ids as $key => $value )
			{
				if( $value =='')
					unset($this->option_ids[$key]);
			}
		}
		
		
		$query = "  SELECT 
						r.*, 
						r_n.numbers_available					AS max_count_reservation
					
				FROM #__hotelreservation_rooms 					r
				".
				(
					( is_array($this->option_ids)? count($this->option_ids) > 0 : $this->option_ids !='')? 
					"
					INNER JOIN 
					(
						SELECT 
							* 
						FROM #__hotelreservation_room_feature_options			o
						INNER JOIN #__hotelreservation_room_features			f 	USING(feature_id)
						WHERE 
							FIND_IN_SET( o.option_id, '".(is_array($this->option_ids)? implode(',', $this->option_ids) : $this->option_ids)."')
						GROUP BY feature_id
					) f ON ( FIND_IN_SET( f.option_id, r.option_ids))
					
					"
					:
					""
					
				)."
				INNER JOIN 
				(
						SELECT 
							r_n.room_id,
							GROUP_CONCAT( r_n.room_number_number ORDER BY r_n.room_number_number )	AS numbers_available
						FROM
						(
							SELECT 
								r_n.room_id,
								r_n.room_number_number,
								room_number_skiped,
								room_number_reserved
							FROM
							(	
								#this is select for available number of rooms available, skiped etc
								SELECT 
									r_n.*,
									SUM( 	
										IF( 
											ISNULL(r_n_d_i.room_number_date_data), 
											0, 
											IF(r_n_d_i.room_number_date_data BETWEEN '$datas'  AND  '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."',1,0) 
										) 
									)												AS room_number_skiped
								FROM #__hotelreservation_rooms 									r
								INNER JOIN 	#__hotelreservation_rooms_numbers					r_n			ON ( r.room_id = r_n.room_id )
								LEFT JOIN 	#__hotelreservation_rooms_numbers_date_ignored		r_n_d_i		ON ( r_n.room_number_id = r_n_d_i.room_number_id )
								WHERE 
									r_n.room_number_datas <= '$datas' 
									AND 
									IF( r_n.room_number_datae ='0000-00-00', 1, r_n.room_number_datae >=  '$datae' )
									
									
								GROUP BY r.room_id, r_n.room_number_number
								#HAVING room_number_skiped = 0
								#~this is select for available number of rooms available
							) r_n
							LEFT JOIN
							(
								SELECT 
									hcrdn.room_id,
									hcrdn.room_number_number,
									COUNT(hcrdn.room_number_number)			AS room_number_reserved
								FROM #__hotelreservation_confirmations_rooms_numbers_dates	hcrdn
								INNER JOIN #__hotelreservation_confirmations 				hc			
																				ON hc.confirmation_id = hcrdn.confirmation_id
								WHERE 
									hcrdn.room_number_data BETWEEN '$datas' AND '".date( 'Y-m-d', strtotime($datae.' - 1 day '))."'
									AND
									hc.reservation_status <> ".CANCELED_ID."
									AND
									hc.confirmation_id <> ".$this->confirmation_id."
								GROUP BY room_id, room_number_number
							) c_r_n 
								ON ( r_n.room_id = c_r_n.room_id AND r_n.room_number_number = c_r_n.room_number_number )
							WHERE if( isnull(room_number_reserved), 1, room_number_reserved=0)
							GROUP BY room_id, room_number_number
						) r_n 
						GROUP BY room_id
				) r_n					ON r.room_id = r_n.room_id
				WHERE 
					r.is_available = 1 
					# and	r.room_capacity >= $this->guest_adult
					".($this->hotel_id>0? " AND r.hotel_id = '".$this->hotel_id."'" : "")."
				GROUP BY
					room_id
				ORDER BY room_order
		";
		$reservation = $this->_getList( $query );
		// dmp($this->room_ids);
		$nr = 0;
		$itemFeatureOptionsAvailable = array();
		$count_r	= 0;
		$count_g	= 0;
		
		$oldItemRoomsCapacity  		= $this->itemRoomsCapacity;
		$this->itemRoomsCapacity 	= array();
		foreach( $reservation as $value )
		{
			$value->max_count_reservation = explode(',', $value->max_count_reservation);
			if( $is_check_numbers )
			{
				if( 
					isset( $oldItemRoomsCapacity[ $value->room_id ])
					&&
					$oldItemRoomsCapacity[ $value->room_id ] != 0
					&&
					in_array('0'.$value->room_id, $this->room_ids)
				)
				{
					$count_r += $oldItemRoomsCapacity[ $value->room_id ][1];
					$count_g += ($value->room_capacity 		* $oldItemRoomsCapacity[ $value->room_id ][1]);
			
				}
			}
			
			$this->itemRoomsCapacity[ $value->room_id ] = array(
																	count($value->max_count_reservation), 
																	isset($oldItemRoomsCapacity[ $value->room_id ][1])? $oldItemRoomsCapacity[ $value->room_id ][1] : 0
																);
			
			
			$this->room_available_ids[]  = $value->room_id;
			if( $this->tip_oper == 1 )
			{	
				$f = explode(",", $value->option_ids);
				foreach( $f as $val )
				{
					if($val=='')
						continue;
						
					if( !in_array( $val, $itemFeatureOptionsAvailable ) )
					{
						$itemFeatureOptionsAvailable[] = $val;
					}
				
				}
			}
		}
		
		if($this->tip_oper==1 )
		{
			$this->itemFeatures					= $this->getFeatures($itemFeatureOptionsAvailable);
			$this->room_feature_available_ids  	= $itemFeatureOptionsAvailable;
		}
		//$this->roomsAvailable		= $this->getRoomsAvailable($this->room_available_ids);
		if( $is_check_numbers )
		{
			if( $count_g < $this->guest_adult + $this->guest_child )
			{
				return false;
			}
			/*
			else if( $count_r < $this->rooms )
			{
				return false;
			}*/
		}
		 //dmp($reservation);
		return count($reservation)>0 ? true : false;
		
	}
	

	
	function writeAllInfos()
	{
		$this->itemTaxes				= $this->getTaxes();
		$this->itemPayments				= $this->getConfirmationPayments();
		$this->Reservation_Details		= $this->getReservationDetails($this, true);
		$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
		$this->Guest_Details			= $this->getGuestDetails();
		if($this->itemAppSettings->save_all_guests_data )
			$this->GuestsDetails		= $this->prepareGuestDetails();
		else
			$this->GuestsDetails		= '';
		
		if($this->itemAppSettings->is_enable_payment )
			$this->Payment_Information		= $this->getPaymentInformation();
		else 
			$this->Payment_Information		= '';
		
		$this->Confirmation				= $this->getConfirmation();
	}
	
	function mpesa($order_id,  $invoce, $total, $phone_1, $phone_2 ,$email, $vendor_ref,  $mpesa_code, $url, &$rspProcessorPayment )
	{
		try 
		{ 
			JHotelReservationModelVariables::writeMessage("Paying through MPesa");
			$str_answer 	= '';
			$code_answer 	= -1;
			$txn = new MPesaTransaction();

			
			$txn->order_id			= $order_id;
			$txn->invoce			= $invoce;
			$txn->total				= $total;
			$txn->phone_1			= $phone_1;
			$txn->phone_2			= $phone_2;
			$txn->email				= $email;
			$txn->vendor_ref		= $vendor_ref;
			$txn->mpesa				= $mpesa_code;
			$txn->gateway_url_live	= $url;
			$txn->gateway_url_devel	= $url;

			$txn->debug 			= !true; //uncomment to see debugging information
			$rspProcessorPayment['txn'] 		= $txn->data;
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			
			$rsp = $txn->process();
			
			JHotelReservationModelVariables::writeMessage("Response: ".$rsp);
			$rspProcessorPayment['responce'] 	= $rsp;
			
			if( $rsp == '' || !is_array($rsp) )
				throw new InvalidResponseCodeException(JText::_('LNG_UNKNOWN_ERROR',true) );
			/*
			if( !isset($rsp['RESULT']) )
			{
				$rsp['RESULT'] = 'INVALID COMBINATION';
				return 'ERROR ';
			}
			*/
			// dmp($txn);
			// echo 4;
			// exit;
			if( $rsp['RESULT']  == 0)
			{
				$this->ID_PAYMENT_MPESA = $rsp['ivm'];
			}	
			else
				$this->ID_PAYMENT_MPESA = '';

			// echo "success: " . $txn->txn_successful;
			// echo "response was: " . print_r( $txn->response_arr, true );   
			return array(0, $txn->txn_successful);

		}
		catch( Exception $e ) 
		{
			JHotelReservationModelVariables::writeMessage("Error: ".$e); 
			$tr = $e->getTrace();

			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer 	= $e->getMessage();
			// dmp($str_answer);
			// exit;
			$code_answer 	= isset($tr[0]['args'][0]['RESULT']) ? $tr[0]['args'][0]['RESULT'] + 0 : -1;
		}
		//exit;
		//JError::raiseWarning( 500, $str_answer);
		return array($code_answer, $str_answer);
	}
	
	function pesapi($name, $phone, $amount, $mpesa_code, &$rspProcessorPayment )
	{
		try 
		{ 
			JHotelReservationModelVariables::writeMessage("Paying through MPesa");
			$str_answer 	= '';
			$code_answer 	= -1;
			$txn = new PesaPITransaction();

			
			$txn->process($name, $phone, $amount, $mpesa_code);
			$rsp = $txn->response_arr;
			
			JHotelReservationModelVariables::writeMessage("Response: ".$rsp);
			$rspProcessorPayment['responce'] 	= $rsp;
			
			if( $rsp == '' || !is_array($rsp) )
				throw new InvalidResponseCodeException(JText::_('LNG_UNKNOWN_ERROR',true) );
		
			if( $rsp['RESULT']  == 0)
			{
				$this->ID_PAYMENT_MPESA = $rsp['id'];
			}	
			else
				$this->ID_PAYMENT_MPESA = '';

			// echo "success: " . $txn->txn_successful;
			// echo "response was: " . print_r( $txn->response_arr, true );   
			return array(0, $rsp['RESPONCE']);

		}
		catch( Exception $e ) 
		{
			JHotelReservationModelVariables::writeMessage("Error: ".$e); 
			$tr 	= $e->getTrace();
			$rsp 	= $txn->response_arr;
			$rspProcessorPayment['responce'] 	= $rsp['RESPONCE'];
			$str_answer 	= $e->getMessage();
			//dmp($tr[0]['args']);
			// exit;
			$code_answer 	= $rsp['RESULT'];//isset($tr[0]['args'][0]['RESPONCE']) ? $tr[0]['args'][0]['RESPONCE'] + 0 : -1;
		}
		
		// dmp($code_answer);
		// exit;
		//JError::raiseWarning( 500, $str_answer);
		return array($code_answer, $str_answer);
	}

	function payflow($currency, $value, $tip, $id, $doreautorization, $capturecomplete, &$rspProcessorPayment )
	{
		try 
		{ 
			$str_answer = '';
			$code_answer 	= -1;
			$txn = new PayFlowTransaction();

			//
			//these are provided by your payflow reseller
			//
			foreach( $this->itemPaymentProcessors as $itemPayment )
			{
				if($itemPayment->paymentprocessor_type==PROCESSOR_PAYFLOW) 
				{
					$txn->PARTNER 			= $itemPayment->paymentprocessor_name;
					$txn->USER 				= $itemPayment->paymentprocessor_username;
					$txn->PWD				= $itemPayment->paymentprocessor_password;
					if( $itemPayment->paymentprocessor_mode == 'live' )
					{
						$txn->gateway_url_live	= $itemPayment->paymentprocessor_address;
						$txn->environment		= 'live';
					}
					else
					{
						$txn->gateway_url_devel	= $itemPayment->paymentprocessor_address_devel;
						$txn->environment		= 'test';
					}
					$txn->VENDOR 			= $txn->USER; //or your vendor name
					break;
				}
			}
			
			//
			// transaction information
			//

			$txn->CURRENCY			= $currency;
			$txn->TENDER 			= 'C'; //sets to a cc transaction
			$txn->ACCT 				= $this->card_number; //cc number
			//$txn->ACCT 				= '4111111111111111'; //cc number
			$txt->ACTION 			= 'S';
			$txn->TRXTYPE 			= $tip; //txn type: sale
			$txn->AMT 				= $value; //amount: 1 dollar
			$txn->EXPDATE			= (strlen($this->card_expiration_month)>1? $this->card_expiration_month : '0'.$this->card_expiration_month).substr($this->card_expiration_year,-2); //4 digit expiration date
			if( strlen($this->card_security_code) > 0 )
			{
				$txn->CVV2			= $this->card_security_code;
				$txn->cvv2_required = true;
			}
			//$txn->EXPDATE			= $this->card_expiration_year; //4 digit expiration date

			
			//dmp($txn->EXPDATE);

			$txn->FIRSTNAME 		= $this->first_name;
			$txn->LASTNAME 			= $this->last_name;
			$txn->STREET 			= $this->address;
			$txn->CITY 				= $this->city;
			$txn->STATE 			= $this->state_name;
			$txn->ZIP 				= $this->postal_code;
			$txn->COUNTRY 			= $this->country;
			$txn->ORIGID 			= $id;
			$txn->DOREAUTORIZATION 	= $doreautorization;
			$txn->CAPTURECOMPLETE 	= $capturecomplete;

			$txn->debug 			= !true; //uncomment to see debugging information
			$txn->avs_addr_required = 0; //set to 1 to enable AVS address checking, 2 to force "Y" response
			$txn->avs_zip_required 	= 0; //set to 1 to enable AVS zip code checking, 2 to force "Y" response
			//$txn->cvv2_required = 1; //set to 1 to enable cvv2 checking, 2 to force "Y" response
			$txn->fraud_protection = true; //uncomment to enable fraud protection

			$rspProcessorPayment['txn'] 		= $txn->data;
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			
			$rsp = $txn->process();
			$rspProcessorPayment['responce'] 	= $rsp;
			
			if( $rsp == '' || !is_array($rsp) )
				throw new InvalidResponseCodeException(JText::_('LNG_UNKNOWN_ERROR',true) );
			/*
			if( !isset($rsp['RESULT']) )
			{
				$rsp['RESULT'] = 'INVALID COMBINATION';
				return 'ERROR ';
			}
			*/
			if( $tip  == 'A')
			{
				
				$this->ID_PAYMENT_PAYFLOW = $rsp['PNREF'];
			}	
			else
				$this->ID_PAYMENT_PAYFLOW = '';

			// echo "success: " . $txn->txn_successful;
			// echo "response was: " . print_r( $txn->response_arr, true );   
			return array(0, $txn->txn_successful);
		}/*
		catch( TransactionDataException $tde ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_BAD_TRANSACTION_DATA',true) . $tde->getMessage();
		}
		catch( InvalidCredentialsException $e ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_INVALID_CREDENTIALS',true);
		}
		catch( InvalidResponseCodeException $irc ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_BAD_RESPONSE_CODE',true) . $irc->getMessage();
		}
		catch( AVSException $avse ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_AVS_ERROR',true) . $avse->getMessage();
		}
		catch( CVV2Exception $cvve ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_CVV2_ERROR',true) . $cvve->getMessage();
		}
		catch( FraudProtectionException $fpe ) 
		{
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = JText::_('LNG_FRAUD_PROTECTION_ERROR',true). $fpe->getMessage();
		}*/
		catch( Exception $e ) 
		{
			$tr = $e->getTrace();
			//dmp($rspProcessorPayment);
			$rspProcessorPayment['responce'] 	= $txn->raw_response;
			$str_answer = $e->getMessage();
			$code_answer 	= isset($tr[0]['args'][0]['RESULT']) ? $tr[0]['args'][0]['RESULT'] + 0 : -1;
		}
		
		//JError::raiseWarning( 500, $str_answer);
		return array($code_answer, $str_answer);
	}

	function getStringRoomsCapacity($item)
	{
		$str = '';
		foreach($item as $key => $value )
		{
			if( $str !='')
				$str .= ',';
			$str .="$key|".implode('|',$value);
		}
		
		return $str;
	}
	
	function getStringPackageNumbers($item)
	{
		$str = '';
		foreach($item as $key => $value )
		{
			if( is_array($value) )
			{
				$ex = $value;
			}
			else
			{
				$ex = explode('|',$value);
			}
			//dmp($ex);
			if( count($ex)!=5 )	
				continue;
			if( $ex[4]==0 )	
				continue;
			
			if( $str !='')
				$str .= ',';
			$str .= implode('|', $ex);
		}
		//dmp($str);
		return $str;
	}
	

	/*
	function getStringRoomsDiscounts($item)
	{
	
		$str = '';
		foreach($item as $key => $value )
		{
			if( $str !='')
				$str .= ',';
			$str .=$value->discount_id."|".$value->room_id.'|'.$value->days;
			
		}
		
		return $str;
	}
	*/
	function getIDPaymentSettings( $id_defined, $check_is_available=false )
	{
		//$this->hotel_id =2;
		$query 		 = " SELECT * FROM #__hotelreservation_paymentsettings WHERE payment_type_id =".$id_defined." AND hotel_id= '".$this->hotel_id."'".( $check_is_available == true ? " AND is_available = 1 " : "")."  ";
		//dmp($query);
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
		//dmp($row);
		if( !isset($row) )
			return 0;
		return $row->payment_id;
	}
	
	function getDailyMediumPrice( $is_offer, &$daily )
	{
		$daily_medium_prices = array();
		//dmp($daily);
		if( $is_offer )
		{
			//dmp($daily);
			 
			 //exit;
			//verificam :
			//min-max nights
			foreach( $daily as $key => $day )
			{
				if( count($day['numbers']) == 0 )
				{
					$val 		= $day['price'];
					$disc		= 0;
					foreach( $day['discounts'] as $d )
					{
						$k = date('N', strtotime($day['data']));
						//dmp($d);
						
						$string_price ="price_".$k;
						$price_value = $d->$string_price;

						//dmp($d->week_types);
						if( $d->week_types[$k-1] =='%'){
							$disc += $price_value;
						}else{
							$val = $price_value;
						}
							
						
						if($this->guest_adult==1){
							if($d->offer_pers_price==1){
								$val = $val + $d->single_supplement;
							}else{
								$val = $val - $d->single_discount;
							}
						}
					}
					
					//dmp($disc);
					//dmp($val);
					$pret = JHotelUtil::my_round($val - JHotelUtil::my_round($val * ($disc/100),2),2);
				}
				else
				{
					$val = 0;
					foreach( $day['numbers'] as $n )
					{
						//exit;
						//dmp($n);
						$val += $n['price'];
					}
					$pret = JHotelUtil::my_round($val/count($day['numbers']),2);
				}
				// echo "<HR>";
				// dmp($day);
				$daily_medium_prices[ "$pret" ][] 	= $day['data'];
				$daily[$key]['price_final']			= $pret;
				
				// dmp( $daily_medium_prices );
			}
		}
		else 
		{
			foreach( $daily as $key => $day )
			{
				if( count($day['numbers']) == 0 )
				{
					$val 	= $day['price'];
					$disc	= 0;
					foreach( $day['discounts'] as $d )
					{
						//dmp($n);
						$disc += $d->discount_value;
					}
					// $pret = JHotelUtil::my_round($val*100/(100+$disc),2);
					if( $disc != 0 )
						$pret = JHotelUtil::my_round($val - JHotelUtil::my_round($val * ($disc/100),2),2);
					else
						$pret = JHotelUtil::my_round($val,2);
					
					// dmp($pret);
				}
				else
				{
					$val = 0;
					foreach( $day['numbers'] as $n )
					{
						//dmp($n);
						$val += $n['price'];
					}
					$pret = JHotelUtil::my_round($val/count($day['numbers']),2);
				}
				$daily_medium_prices[ "$pret" ][] = $day['data'];
				$daily[$key]['price_final'] = $pret;
			}
		}
		// dmp($daily_medium_prices);
		// exit;
		return $daily_medium_prices;
	}

	function displayPaymentProcessors( $show_cash_payment = false, $ignoredProcessorTypes = array() )
	{
		// dmp($this->itemPaymentProcessors);
		$count_proc   =count($this->itemPaymentProcessors);
		//dmp($this->itemPaymentProcessors);
		$isSuperUser = isSuperUser(JFactory::getUser()->id);
		if($isSuperUser){
		?>
			<input type='hidden' name='payment_processor_sel_type' id='payment_processor_sel_type' value='<?php echo $count_proc==1? $this->itemPaymentProcessors[0]->paymentprocessor_type : $this->payment_processor_sel_type?>' />
		<?php }else{
			
			if($this->total_cost > 0){
				$this->payment_processor_sel_id = 11;
				?>
				<input type='hidden' name='payment_processor_sel_type' id='payment_processor_sel_type' value='PROCESSOR_BUCKAROO'>
		<?php }else{ 
			$this->payment_processor_sel_id = 3;
			?>
				<input type='hidden' name='payment_processor_sel_type' id='payment_processor_sel_type' value='PROCESSOR_CASH'>
		<?php }
		} ?>
		<TABLE width=100% >
			<?php 
			/*
			if($show_cash_payment == true )
			{
			?>
			<TR>
				<TD align=left>
						<img border=1 height='32px' width='32px'src='<?php echo _PATH_IMG."processor_cash.ico" ?>'/>
						<input 
							id		= 'payment_processor_sel_id'
							name	= 'payment_processor_sel_id'
							type	= 'radio' 
							value	= '0'
							<?php 
								echo $count_proc==1
								||
								$this->payment_processor_sel_id == 0 
								? 
								" checked " 
								: 
								""
							?>
							onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout			=	"this.style.cursor='default'"
							onclick				= 	"
													<?php
													foreach( $this->itemPaymentProcessors as $proc_tmp )
													{
														if($this->payment_processor_sel_id==$proc_tmp->paymentprocessor_type )
														{
														?>
														
														<?php
														}
														else
														{
														?>
														jQuery('#div_procesor_payments_<?php echo $proc_tmp->paymentprocessor_type?>').hide(100);
														<?php
														}
													}
													?>
							"
						>								
					<?php echo JText::_('LNG_CASH',true)?>
				</TD>
			</TR>
			<?php
			}
			*/
			foreach( $this->itemPaymentProcessors as $proc )
			{
				if( in_array($proc->paymentprocessor_type, $ignoredProcessorTypes) )
					continue;
				if($show_cash_payment == false &&  $proc->paymentprocessor_type == PROCESSOR_CASH)
					continue;
				
				//skip unasigned processor to hotel				
				if( $proc->paymentprocessor_type == PROCESSOR_PAYFLOW || $proc->paymentprocessor_type ==PROCESSOR_AUTHORIZE )
				{
					if( $this->getIDPaymentSettings( PREAUTHORIZATION_PAYMENT_ID, true) == 0 )
						continue;
				}
				else  if( $proc->paymentprocessor_type == PROCESSOR_PAYPAL_EXPRESS )
				{
					if( $this->getIDPaymentSettings( PAYPAL_ID, true) == 0 )
						continue;
				}
				else if( $proc->paymentprocessor_type == PROCESSOR_IDEAL_OMNIKASSA )
				{
					if( $this->getIDPaymentSettings( IDEAL_OMNIKASSA_ID, true) == 0 )
						continue;
				}
				else if( $proc->paymentprocessor_type == PROCESSOR_BUCKAROO )
				{
					if( $this->getIDPaymentSettings( BUCKAROO_ID, true) == 0 )
						continue;
				}
				else if( $proc->paymentprocessor_type == PROCESSOR_4B )
				{
					if( $this->getIDPaymentSettings( P4B_ID, true) == 0 )
					continue;
				}
				else if( $proc->paymentprocessor_type == PROCESSOR_BANK_ORDER )
				{
					if( $this->getIDPaymentSettings( BANK_ORDER_ID, true) == 0 )
						continue;
				}
				else if( $proc->paymentprocessor_type == PROCESSOR_EENMALIGE_INCASO )
				{
					if( $this->getIDPaymentSettings( EENMALIGE_INCASO_ID, true) == 0 )
						continue;
				}
				/*else if( $proc->paymentprocessor_type == PROCESSOR_CASH )
				{
					if( $this->getIDPaymentSettings( PAYPAL_ID) == 0 )
						continue;
				}*/
				else if( $proc->paymentprocessor_type == PROCESSOR_MPESA )
				{
					if( $this->getIDPaymentSettings( MPESA_ID, true) == 0 )
						continue;
				}

				
				//skip unasigned processor to hotel
			?>
			<TR>
				<TD align=left>
						<img class="processor_icon" height='32px' width='32px'src='<?php echo _PATH_IMG.strtolower($proc->paymentprocessor_type).".ico" ?>'/>
						<input 
							id		= 'payment_processor_sel_id'
							name	= 'payment_processor_sel_id'
							type	= 'radio' 
							value	= '<?php echo $proc->paymentprocessor_id?>'
							<?php 
								echo $count_proc==1 && $show_cash_payment ==false 
								||
								$this->payment_processor_sel_id == $proc->paymentprocessor_id 
								
								? 
								" checked " 
								: 
								""
							?>
							onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout			=	"this.style.cursor='default'"
							onclick				= 	"
													<?php
													foreach( $this->itemPaymentProcessors as $proc_tmp )
													{
														if($proc->paymentprocessor_type==$proc_tmp->paymentprocessor_type )
														{
														?>
														jQuery('#payment_processor_sel_type').val( '<?php echo $proc->paymentprocessor_type?>');
														jQuery('#div_procesor_payments_<?php echo $proc_tmp->paymentprocessor_type?>').show(100);
														<?php
														}
														else
														{
														?>
														jQuery('#div_procesor_payments_<?php echo $proc_tmp->paymentprocessor_type?>').hide(100);
														<?php
														}
													}
													?>
							"
						>								
				<?php
					echo $proc->paymentprocessor_front_name."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				?>
				</TD>
			</TR>
			<TR>
				<TD>
					<div 
						id		='div_procesor_payments_<?php echo $proc->paymentprocessor_type?>' 
						name	='div_procesor_payments_<?php echo $proc->paymentprocessor_type?>' 
						<?php echo $this->payment_processor_sel_id == $proc->paymentprocessor_id || $count_proc==1 && $show_cash_payment ==false ? "" : "style='display:none'" ?>
					>
						<?php
						if( $proc->paymentprocessor_type == PROCESSOR_PAYFLOW || $proc->paymentprocessor_type ==PROCESSOR_AUTHORIZE)
						{
						?>
						<TABLE width=100% valign=top class='table_data'>
							<TR>
								<TD colspan=3 align=left style="padding-top:10px;padding-bottom:10px;">	
									-<?php echo JText::_('LNG_FIELDS_MARKED_WITH',true);?> <span class="mand">*</span> <?php echo JText::_('LNG_ARE_MANDATORY',true);?>
								</TD>
							</TR>
							<tr style=''>
								<TD colspan=3  align=left>
									-<?php echo JText::_('LNG_CREDIT_CARD_REQUIRED',true);?>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_CREDIT_CARD_TYPE',true);?><span class="mand">*</span>
								</TD>
								<TD colspan=2 align=left>
									<select name='card_type_id' id = 'card_type_id'>
										<option 
											value="0"
											<?php echo $this->card_type_id == 0? " selected " : ""?>
										>
											&nbsp;
										</option>
										<?php
										foreach( $this->itemTypeCards as $card )
										{
										?>
										<option 
											value="<?php echo $card->type_card_id?>"
											<?php echo $this->card_type_id == $card->type_card_id? " selected " : ""?>
										>
											<?php echo $card->description?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_NAME_OF_CARD',true);?> <span class="mand">*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_name'
										id				= 'card_name'
										autocomplete	= 'off'
										size			= 50
										value			= "<?php echo $this->card_name?>"
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_CREDIT_CARD_NUMBER',true);?> <span class="mand">*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_number'
										id				= 'card_number'
										autocomplete	= 'off'
										size			= 50
										value			= "<?php echo $this->card_number?>"
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_EXPIRATION_DATE',true);?> <span class="mand">*</span>
								</TD>
								<TD align=left>
									<select name='card_expiration_month' id = 'card_expiration_month'>
										<option 
											value="0"
											<?php echo $this->card_expiration_month == 0? " selected " : ""?>
										>
											&nbsp;
										</option>
										<?php
										for( $i=1; $i<=12;$i++ )
										{
										?>
										<option 
											value="<?php echo $i?>"
											<?php echo $this->card_expiration_month == $i? " selected " : ""?>
										>
											<?php echo $i?>
										</option>
										<?php
										}
										?>
									</select>
									&nbsp;
									<select name='card_expiration_year' id = 'card_expiration_year'>
										<option 
											value="0"
											<?php echo $this->card_expiration_year == 0? " selected " : ""?>
										>
											&nbsp;
										</option>
										<?php
										for( $i=date('Y'); $i<=date('Y')+5;$i++ )
										{
										?>
										<option 
											value="<?php echo $i?>"
											<?php echo $this->card_expiration_year == $i? " selected " : ""?>
										>
											<?php echo $i?>
										</option>
										<?php
										}
										?>
									</select>
									
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_SECURITY_CODE',true);?>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'card_security_code'
										id				= 'card_security_code'
										autocomplete	= 'off'
										size			= 4
										maxlength		= 4
										value			= "<?php echo $this->card_security_code?>"
									>
								</TD>
							</TR>
						</TABLE>
						<?php
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_PAYPAL_EXPRESS )
						{
							echo JText::_('LNG_PAYPAL_INFO_FRONTEND',true);
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_IDEAL_OMNIKASSA )
						{
							echo JText::_('LNG_IDEAL_OMNIKASSA_INFO_FRONTEND',true);
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_BUCKAROO )
						{
							echo JText::_('LNG_BUCKAROO_INFO_FRONTEND',true);
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_4B)
						{
							echo JText::_('LNG_4B_INFO_FRONTEND',true);
						}						
						else if( $proc->paymentprocessor_type == PROCESSOR_BANK_ORDER )
						{
							$str = str_replace(htmlspecialchars(EMAIL_MAX_DAYS_PAYD),	$proc->paymentprocessor_timeout_days,	JText::_('LNG_PROCESSOR_PAYMENT_MANUAL',true));
							$str = str_replace(EMAIL_MAX_DAYS_PAYD, 					$proc->paymentprocessor_timeout_days,	JText::_('LNG_PROCESSOR_PAYMENT_MANUAL',true));
							$str = str_replace(EMAIL_RESERVATION_COST, 					JHotelUtil::fmt( $this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed,2),	$str);
							//$str = str_replace(EMAIL_RESERVATION_ID, 					$this->JHotelUtil::getStringIDConfirmation(),	$str);
							echo $str;
							// echo JText::_('LNG_BANK_ACCOUNT',true).":".$proc->paymentprocessor_username.',&nbsp;';
							// echo JText::_('LNG_ACCOUNT_NUMBER',true).":".$proc->paymentprocessor_number.',&nbsp;';
							// echo JText::_('LNG_PROCESSOR_TIMEOUT_DAYS',true).":".$proc->paymentprocessor_timeout_days;
						}
						
						else if( $proc->paymentprocessor_type == PROCESSOR_EENMALIGE_INCASO )
						{
							echo JText::_('LNG_INFO_EENMALIGE_INCASO',true);
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_CASH )
						{
							echo JText::_('LNG_INFO_CASH',true);
						}
						else if( $proc->paymentprocessor_type == PROCESSOR_MPESA )
						{
						/*
						?>
						<iframe frameborder=0 width='100%' scrolling='no' src='<?php echo "index.php?option=".JRequest::getVar('option')."&view=mpesa&amount=".$this->total."&number=".$proc->paymentprocessor_number.'&currency='.$this->itemCurrency->description?>'>
								
						</iframe>
						<TABLE width=100% valign=top class='table_data'>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_MPESA_CODE',true);?>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'payment_code'
										id				= 'payment_code'
										autocomplete	= 'off'
										size			= 10
										maxlength		= 10
										value			= "<?php echo $this->payment_code?>"
									>
								</TD>
							</TR>
						</TABLE>
						<?php
						*/
						?>
						<TABLE width=100% valign=top class='table_data' style='margin-left:10px'>
							<TR>
								<TD colspan=3 align=left style="padding-top:10px;padding-bottom:10px;">	
									<div style='font-size:14px;background-color:#AAAAAA'>
										<?php echo JText::_('LNG_READY_TO_PAY',true).' : '.$this->total.' '.$this->itemCurrency->description;;?>
										<BR>
										<?php echo JText::_('LNG_SEND_MONEY_VIA_MPESA',true).' : '.$proc->paymentprocessor_number;?>
									</div>
								</TD>
							</TR>
							<TR>
								<TD colspan=3 align=left style="padding-top:10px;padding-bottom:10px;">	
									<?php echo JText::_('LNG_MPESA_ENTER_CODE_RECEIPT',true);?>
									<HR>
								</TD>
							</TR>
							<TR>
								<TD colspan=3 align=left style="padding-top:10px;padding-bottom:10px;">	
									-<?php echo JText::_('LNG_FIELDS_MARKED_WITH',true);?> <span class="mand">*</span> <?php echo JText::_('LNG_ARE_MANDATORY',true);?>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_NAME',true);?> <span class="mand">*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'payment_name'
										id				= 'payment_name'
										autocomplete	= 'off'
										size			= 50
										value			= "<?php echo $this->payment_name?>"
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_TEL',true);?> <span class="mand">*</span>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'payment_tel'
										id				= 'payment_tel'
										autocomplete	= 'off'
										size			= 50
										value			= "<?php echo $this->payment_tel?>"
									>
								</TD>
							</TR>
							<tr style='background-color:##CCCCCC'>
								<TD colspan=1 width=20%  align=left>
									<?php echo JText::_('LNG_MPESA_CODE',true);?>
								</TD>
								<TD colspan=2 align=left>
									<input 
										type 			= 'text'
										name			= 'payment_code'
										id				= 'payment_code'
										autocomplete	= 'off'
										size			= 10
										maxlength		= 10
										value			= "<?php echo $this->payment_code?>"
									>
								</TD>
							</TR>
						</TABLE>
						<?php
						
						}
						
						?>
					</div>
				</TD>
			</TR>
			<?php
			}
			?>
		</TABLE>
		<?php
	}
	

	function JHotelUtil::getStringIDConfirmation()
	{
		return str_pad($this->confirmation_id, LENGTH_ID_CONFIRMATION, "0", STR_PAD_LEFT);
	}
	
	
	function getHotelReviewScore($hotelId){	
		$reviewAnswersTable	= 	 $this->getTable('ReviewAnswers');
		return $reviewAnswersTable->getAverageReviewAnswersScoreByHotel($hotelId);
	}
	
	function getHotelReviews($hotelId){	
		$reviewAnswersTable	= 	 $this->getTable('ReviewAnswers');
		return $reviewAnswersTable->getHotelReviews($hotelId);
	}
	
		static function encrypt($str)
	{
		return strtr(base64_encode($str), '+/=', '-_,');
		return $str;
		/* # Add PKCS7 padding.
		$block = mcrypt_get_block_size('des', 'ecb');
		if (($pad = $block - (strlen($str) % $block)) < $block) {
		  $str .= str_repeat(chr($pad), $pad);
		}

		return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB); */
	}

	static function decrypt($str)
	{
		return base64_decode(strtr($str, '-_,', '+/=')); 
		return $str;
		/* $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);

		# Strip padding out.
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		if ($pad && $pad < $block && preg_match(
			  '/' . chr($pad) . '{' . $pad . '}$/', $str
												)
		   ) {
		  return substr($str, 0, strlen($str) - $pad);
		}
		return $str; */
	}
	
	function prepareVariablesPayPalExpress( )
	{
		//paypal express work only frontend cost or total value
		
		//TODO - remove this - this is only to calculate exact amount form paypal
		$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
		
		$query = " 	SELECT * 
					FROM #__hotelreservation_paymentprocessors 
					WHERE is_available = 1 AND paymentprocessor_id = ".$this->payment_processor_sel_id."
					ORDER BY paymentprocessor_name
					";
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
		
		if( $row->paymentprocessor_type == PROCESSOR_PAYPAL_EXPRESS )
		{
			if( $row->paymentprocessor_mode =='test' )
			{
			?>
			<form target='_self' name='form_<?php echo $row->paymentprocessor_type?>' id= 'form_<?php echo $row->paymentprocessor_type?>'  method='post' action='<?php echo $row->paymentprocessor_address_devel ?>'>
			<?php
			}
			else
			{
			?>
			<form target='_self' name='form_<?php echo $row->paymentprocessor_type?>' id= 'form_<?php echo $row->paymentprocessor_type?>' method='post' action='<?php echo $row->paymentprocessor_address ?>'>
			<?php
			}
			
			//$url_responce = urlencode( "&id=".JHotelReservationModelVariables::encrypt($this->confirmation_id)."&user=".JHotelReservationModelVariables::encrypt($this->first_name.' '.$this->last_name, KEY)."&type=".JHotelReservationModelVariables::encrypt($row->paymentprocessor_type, KEY)) ;
			$url_responce = "&id=".urlencode( JHotelReservationModelVariables::encrypt($this->confirmation_id));
			$url_responce .= "&user=".urlencode( JHotelReservationModelVariables::encrypt($this->email));
			$url_responce .= "&type=".urlencode( JHotelReservationModelVariables::encrypt($row->paymentprocessor_type)) ;
			$url_responce .= "&currency_selector=".urlencode( JHotelReservationModelVariables::encrypt($this->currency_selector)) ;
			
			$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->month_start, $this->day_start,$this->year_start )	);
			$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->month_end, $this->day_end,$this->year_end )	);
			?>
				<div class='div_redirect_paysite'>
					<?php echo JText::_('LNG_WAIT_TO_REDIRECT_PAY_SITE',true)?>
				</div>
				
				<input type='hidden' name='cmd' id= 'cmd' value='_xclick'/>
				<input TYPE="hidden" name="charset" 		id="charset"		value="utf-8">
				<input type='hidden' name='business' 		id= 'business' 		value='<?php echo $row->paymentprocessor_username ?>'/>
				<input type='hidden' name='item_name' 		id= 'item_name' 	value='<?php echo JText::_('LNG_RESERVATION',true)." : ".$this->itemAppSettings->company_name.' >> '.$datas.' | '.$datae ?>'/>
				<input type='hidden' name='item_number'		id= 'item_number' 	value='<?php echo $this->JHotelUtil::getStringIDConfirmation()?>'/>
				<input type='hidden' name='image_url' 		id= 'image_url' 	value=''/>
				<input type='hidden' name='no_shipping' 	id= 'no_shipping' 	value='1'/>
				<input type='hidden' name='cbt' 			id= 'cbt' 			value='<?php echo JText::_('LNG_CONTINUE_RESERVATION',true)?>'/>
				
				<!--return-->
				<input type='hidden' name='notify_url' 		id= 'notify_url' 	value='<?php echo JURI::base()."index.php?option=com_jhotelreservation&task=processNotify$url_responce"?>'/>
				<input type='hidden' name='return' 			id= 'return' 		value='<?php echo JURI::base()."index.php?option=com_jhotelreservation&task=processOK$url_responce"?>'/>
				<input type='hidden' name='cancel_return'	id= 'cancel_return' value='<?php echo JURI::base()."index.php?option=com_jhotelreservation&task=processCANCEL$url_responce"?>'/>
				<input type='hidden' name='currency_code'	id= 'currency_code' value='<?php echo  $this->itemCurrency->description ?>'/>
				<input type='hidden' name='amount' 			id= 'amount' 		value='<?php echo JHotelUtil::my_round( $this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed,2)?>'/>
				<input type='hidden' name='no_note' 		id= 'no_note' 		value='1'/>
				<input type='hidden' name='custom' 			id= 'custom' 		value='<?php echo $this->confirmation_id."|".$this->email."|".$row->paymentprocessor_type."|".$this->currency_selector ?>'/>
			</form>
			<script>
				window.onload = function(){
											//alert(document.forms['form_<?php echo $row->paymentprocessor_type?>']);
											document.forms['form_<?php echo $row->paymentprocessor_type?>'].submit();
										};
			</script>
			<?php
		}
	}
	
	
	
	function prepareVariablesIdealOmnikassa( )
	{
		
	
		//TODO - remove this - this is only to calculate exact amount
		$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
	
		$query = " 	SELECT *
						FROM #__hotelreservation_paymentprocessors 
						WHERE is_available = 1 AND paymentprocessor_id = ".$this->payment_processor_sel_id."
						ORDER BY paymentprocessor_name
						";
		$this->_db->setQuery( $query );
		$configuration = $this->_db->loadObject();
		
		if( $configuration->paymentprocessor_type == PROCESSOR_IDEAL_OMNIKASSA )
		{
			$iDeal = new OmniKassa();
			if($configuration->paymentprocessor_mode =='test')
				$iDeal->setPaymentServerUrl( $configuration->paymentprocessor_address_devel);
			else
				$iDeal->setPaymentServerUrl($configuration->paymentprocessor_address);
			$iDeal->setMerchantId($configuration->paymentprocessor_username);
			$iDeal->setSecretKey($configuration->paymentprocessor_password);
			$iDeal->setKeyVersion(2);
			$iDeal->addPaymentMeanBrand("IDEAL,VISA,MASTERCARD,MAESTRO,MINITIX");
			$iDeal->setCurrencyNumericCode(978);
			$iDeal->setNormalReturnUrl(JURI::base()."index.php/component/jhotelreservation/omnikassaresponse");
			$iDeal->setAutomaticResponseUrl(JURI::base()."index.php/component/jhotelreservation/omnikassaautomaticresponse");
 			$iDeal->setAmount(JHotelUtil::my_round( $this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed,2));
			$iDeal->setTransactionReference($this->JHotelUtil::getStringIDConfirmation());
			// $iDeal->setOrderId(1);
			$iDeal->setCustomerLanguage('nl');
			?>
				<form target='_self' name='form_<?php echo $configuration->paymentprocessor_type?>' id= 'form_<?php echo $configuration->paymentprocessor_type?>' method='post' action='<?php echo $iDeal->getPaymentServerUrl() ?>'>
				
			
				<div class='div_redirect_paysite'>
					<?php echo JText::_('LNG_WAIT_TO_REDIRECT_PAY_SITE',true)?>
				</div>
				
					<?php echo $iDeal->getHtmlFields(); ?>			
				</form>
			<script>
				window.onload = function(){
											//alert(document.forms['form_<?php echo $configuration->paymentprocessor_type?>']);
											document.forms['form_<?php echo $configuration->paymentprocessor_type?>'].submit();
										};
			</script>
			<?php
		}
	}
	
	function prepareVariablesBuckaroo( )
	{
	
	
		//TODO - remove this - this is only to calculate exact amount
		$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
	
		$query = " 	SELECT *
		FROM #__hotelreservation_paymentprocessors
		WHERE is_available = 1 AND paymentprocessor_id = ".$this->payment_processor_sel_id."
		ORDER BY paymentprocessor_name
		";
		$this->_db->setQuery( $query );
		$configuration = $this->_db->loadObject();
	
		if( $configuration->paymentprocessor_type == PROCESSOR_BUCKAROO )
		{
			$buckaroo = new Buckaroo();
			if($configuration->paymentprocessor_mode =='test')
				$buckaroo->setPaymentServerUrl( $configuration->paymentprocessor_address_devel);
			else
				$buckaroo->setPaymentServerUrl($configuration->paymentprocessor_address);
			
			$buckaroo->setMerchantId($configuration->paymentprocessor_username);
			$buckaroo->setSecretKey($configuration->paymentprocessor_password);
			//$buckaroo->addPaymentMeanBrand("IDEAL,VISA,MASTERCARD");
			$buckaroo->setCurrencyCode('EUR');
			$buckaroo->setNormalReturnUrl(JURI::base()."index.php/component/jhotelreservation/buckarooresponse");
			$buckaroo->setAmount(JHotelUtil::my_round( $this->total_cost > 0 ? $this->total_cost : $this->total - $this->total_payed,2));
			$isSuperUser = isSuperUser(JFactory::getUser()->id);
			//if(!$isSuperUser){
			//	$buckaroo->addRequestedService("iDEAL");
			//	$buckaroo->addRequestedService("transfer");
			//	$buckaroo->addRequestedService("Paypal");
			//	$buckaroo->addRequestedService("Mastercard");
			//	$buckaroo->addRequestedService("Visa");
			//	$buckaroo->addRequestedService("payperemail");
			//}
			//$buckaroo->setAditionalService("creditmanagement");
			
			$buckaroo->setInvoiceNumber($this->JHotelUtil::getStringIDConfirmation());
			$buckaroo->setCulture('nl-NL');
			?>
				<form target='_self' name='form_<?php echo $configuration->paymentprocessor_type?>' id= 'form_<?php echo $configuration->paymentprocessor_type?>' method='post' action='<?php echo $buckaroo->getPaymentServerUrl() ?>'>
					<div class='div_redirect_paysite'>
						<?php echo JText::_('LNG_WAIT_TO_REDIRECT_PAY_SITE',true)?>
					</div>
					
						<?php echo $buckaroo->getHtmlFields(); ?>			
					</form>
				<script>
					window.onload = function(){
												//alert(document.forms['form_<?php echo $configuration->paymentprocessor_type?>']);
												document.forms['form_<?php echo $configuration->paymentprocessor_type?>'].submit();
											};
				</script>
				<?php
			}
		}
	function prepareVariables4B( )
	{
	
		$paymentURL = "";
		//TODO - remove this - this is only to calculate exact amount
		$this->Reservation_Details_EMail= $this->getReservationDetails($this, false);
	
		$query = " 	SELECT *
							FROM #__hotelreservation_paymentprocessors 
							WHERE is_available = 1 AND paymentprocessor_id = ".$this->payment_processor_sel_id."
							ORDER BY paymentprocessor_name
							";
		$this->_db->setQuery( $query );
		$configuration = $this->_db->loadObject();
		$lang = 'es';
	
		if( $configuration->paymentprocessor_type == PROCESSOR_4B )
		{
			if($configuration->paymentprocessor_mode =='test')
				$paymentURL = $configuration->paymentprocessor_address_devel;
			else
				$paymentURL = $configuration->paymentprocessor_address;
			?>
					<form target='_self' name='form_<?php echo $configuration->paymentprocessor_type?>' id= 'form_<?php echo $configuration->paymentprocessor_type?>' method='post' action='<?php echo $paymentURL; ?>'>
					
				
					<div class='div_redirect_paysite'>
						<?php echo JText::_('LNG_WAIT_TO_REDIRECT_PAY_SITE',true)?>
					</div>
						<input type="hidden" name="order" value="<?php echo $this->confirmation_id;?>">
						<input type="hidden" name="store" value="<?php echo $configuration->paymentprocessor_password;?>">
						<input type="hidden" name="Idioma" value="<?php echo $lang;?>">
					</form>
				<script>
					window.onload = function(){
												//alert(document.forms['form_<?php echo $configuration->paymentprocessor_type?>']);
												document.forms['form_<?php echo $configuration->paymentprocessor_type?>'].submit();
											};
				</script>
				<?php
		}
	}

	static function cancelConfirmation($confirmation_id, $cancelcode){
		if( $confirmation_id == 0 )
			return false;
		$db =JFactory::getDBO();
	
		try{
			$details = "Response code: ".$cancelcode;
			$query = 	" update #__hotelreservation_confirmations set reservation_status=".CANCELED_ID.", details='$details'  WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query())
			{
				JHotelReservationModelVariables::writeMessage("Error: ".$this->_db->getErrorMsg());
				throw( new Exception($db->getErrorMsg()) );
			}
				
			$query = " 	UPDATE #__hotelreservation_confirmations_payments set	payment_status = '".PAYMENT_STATUS_REJECTED."'	WHERE confirmation_id = $confirmation_id	";
			//JHotelReservationModelVariables::writeMessage(" $query ");
			$db->setQuery( $query );
			if (!$db->query())
			{
				JHotelReservationModelVariables::writeMessage("Error: ".$this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			return true;
		}catch( Exception $ex )
		{
			return false;
		}
			
	}
	
	static function deletePendingConfirmation( $confirmation_id, $user, $type, $is_email= false, $is_display=true )
	{	
		try
		{ 
			if( $confirmation_id == 0 )
				return false;
			$db =JFactory::getDBO();
			
			$query = " SELECT * FROM #__hotelreservation_confirmations WHERE confirmation_id = $confirmation_id ";
			$db->setQuery( $query );
			$records = $db->loadObject();
			//dmp($query);
			if(count( $records ) ==0 )
				return false;
		
			$db->setQuery(" START TRANSACTION ");
			$db->query();
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_airport_transfer WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_arrival_options WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_feature_options WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
	
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_packages WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_packages_dates WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			} 
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_payments WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_discounts WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_nr_date_discs WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_rooms_numbers_dates WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_taxes WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
			}
			
			$query = 	" DELETE FROM #__hotelreservation_confirmations_guests WHERE confirmation_id = ".$confirmation_id ;
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				throw( new Exception($db->getErrorMsg()) );
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
		if( $is_display ==false )
			return true;
		echo '<link rel="stylesheet" href="components/'.getBookingExtName().'/assets/style.css" type="text/css" />';
		echo "<center><div class='div_deleted_reservation'>".JText::_('LNG_RESERVATION_CANCELED_BECAUSE_IS_NOT_PAYED',true).'<div class="div_deleted_reservation_signature">'.JText::_('LNG_SIGNATURE',true).'</div><div></center>';
		return true;
	} 
	
	function cleanAllUnwantedReservations()
	{	
		$query = " 	SELECT 
						c.confirmation_id,
						c.email,
						hp.paymentprocessor_type 	AS type
					FROM #__hotelreservation_confirmations c
					INNER JOIN #__hotelreservation_confirmations_payments	cp USING( confirmation_id )
					INNER JOIN #__hotelreservation_paymentprocessors		hp USING( paymentprocessor_id )
					WHERE 
						#hp.is_available = 1 
						#AND 
						( hp.paymentprocessor_type = '".PROCESSOR_PAYPAL_EXPRESS."' OR hp.paymentprocessor_type = '".PROCESSOR_IDEAL_OMNIKASSA."' OR hp.paymentprocessor_type = '".PROCESSOR_BUCKAROO."' OR hp.paymentprocessor_type = '".PROCESSOR_4B."' )
						AND
						cp.payment_status ='".PAYMENT_STATUS_PENDING."'
						AND
						IF( hp.paymentprocessor_timeout_mins > 0, TIMESTAMPDIFF(MINUTE, c.data, now() ) > hp.paymentprocessor_timeout_mins, 0 )
					GROUP BY c.confirmation_id
					ORDER BY hp.paymentprocessor_name
					";
		$this->_db->setQuery( $query );
		$reservations_unwanted = $this->_getList( $query );
		if(isset($reservations_unwanted))
		foreach( $reservations_unwanted as $res )
		{
			if( $this->itemAppSettings->is_email_notify_canceled_pending == true )
				$this->sendCancelPendingEmail( $res->confirmation_id, CANCELED_PENDING_ID);
			if( LOGGER_PAYPAL_EXPRESS )
				JHotelReservationModelVariables::writeMessage("\r\nFile:".__FILE__."\r\nLine:".__LINE__."\r\nFunction:".__FUNCTION__."\r\n ID=".$res->confirmation_id.'\r\nInfo:delete pending',1); 
			JHotelReservationModelVariables::cancelConfirmation( $res->confirmation_id, -1 );
		}
	}
	
	function startTransaction()
	{
		$this->_db->setQuery("START TRANSACTION");
		$this->_db->query();
	}
	
	function rollbackTransaction()
	{
		$this->_db->setQuery("ROLLBACK");
		$this->_db->query();
	}
	
	function commitTransaction()
	{
		$this->_db->setQuery("COMMIT");
		$this->_db->query();
	}
	
	static function setStatusWaiting($confirmationId){
		JHotelReservationModelVariables::writeMessage("set status to waiting reservationId= $confirmationId");
		$query = " 	UPDATE #__hotelreservation_confirmations_payments
		SET
		payment_status = '".PAYMENT_STATUS_WAITING."'
		WHERE
		confirmation_id = $confirmationId;
		";
		$db =JFactory::getDBO();
		$db->setQuery( $query );
		if (!$db->query())
		{
			JHotelReservationModelVariables::writeMessage("Error: ".$this->_db->getErrorMsg());
		}
	}
	
	static function getConfirmationPaymentStatus($confirmationId){
		
		$query = "select * from #__hotelreservation_confirmations_payments
				WHERE
				confirmation_id = $confirmationId;";
		$db =JFactory::getDBO();
		$db->setQuery( $query );
		$confirmationPayment = $db->loadObject();
		return $confirmationPayment->payment_status;
	}
	
	function changePaymentStatusPending( $id, $amount)
	{
		try
		{
			//$this->startTransaction();
			JHotelReservationModelVariables::writeMessage("change reservationId= $id totalPaid=".$this->total." "); 
			$query = " 	UPDATE #__hotelreservation_confirmations_payments
							SET 
								payment_status = '".PAYMENT_STATUS_PAYED."',
								payment_value  = '".$amount."',
								payment_percent= 0
							WHERE 
								confirmation_id = $id
						";
			JHotelReservationModelVariables::writeMessage(" $query "); 
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				JHotelReservationModelVariables::writeMessage("Error: ".$this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) ); 
			}
			
			$query = " UPDATE #__hotelreservation_confirmations 
							SET  
								total_payed 				= '".$amount."',
								confirmation_payment_status	= '".PAYMENT_STATUS_PAYED."' 
							WHERE 
								confirmation_id = $id ";
			JHotelReservationModelVariables::writeMessage(" $query "); 
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				JHotelReservationModelVariables::writeMessage("Error: ".$this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) ); 
			}
		
			//$this->commitTransaction();
			$this->confirmation_payment_status  = PAYMENT_STATUS_PAYED;
		}
		catch( Exception $ex )
		{
			JHotelReservationModelVariables::writeMessage("Error accured processing changePaymentStatusPending() function. The transaction will be rolled back. ");
			JHotelReservationModelVariables::writeMessage("Error: ".$ex);
				
			$this->rollbackTransaction();
			return false;
		}
		//$this->commitTransaction();
		return true;
	}

	
	function comparePayedValues()
	{
		$query = " 	SELECT 
						cp.*
					FROM #__hotelreservation_confirmations_payments 	cp
					INNER JOIN #__hotelreservation_paymentprocessors 	pp	USING(paymentprocessor_id)
					WHERE 
						cp.payment_status = '".PAYMENT_STATUS_PAYED."'
						AND
						(
							pp.paymentprocessor_type ='".PROCESSOR_PAYPAL_EXPRESS."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_BANK_ORDER."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_EENMALIGE_INCASO."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_MPESA."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_IDEAL_OMNIKASSA."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_BUCKAROO."'
							OR
							pp.paymentprocessor_type ='".PROCESSOR_4B."'
						)
						AND
						cp.confirmation_id = ".$this->confirmation_id."
					";
		$this->_db->setQuery( $query );
		// dmp($this->_db);
		// exit;
		$payed = $this->_getList( $query );
		$t = 0;
		foreach( $payed as $res )
		{
			$t += $res->payment_value;
		}
		return JHotelUtil::my_round($t,2) <= JHotelUtil::my_round($this->total,2);
	}
	
	function cleanSameReservationPending( $email, $datas, $datae, $key_control_reservation )
	{
		$query = " 	SELECT 
						c.confirmation_id,
						c.email,
						hp.paymentprocessor_type 	AS type
					FROM #__hotelreservation_confirmations c
					INNER JOIN #__hotelreservation_confirmations_payments	cp USING( confirmation_id )
					INNER JOIN #__hotelreservation_paymentprocessors		hp USING( paymentprocessor_id )
					WHERE 
						c.datas='$datas'
						AND
						c.datae='$datae'
						AND
						c.email='$email'
						AND						
						hp.paymentprocessor_type ='".PROCESSOR_PAYPAL_EXPRESS."'
						AND						
						cp.payment_status ='".PAYMENT_STATUS_PENDING."'
						AND
						key_control_reservation = '$key_control_reservation'
					GROUP BY c.confirmation_id
					ORDER BY hp.paymentprocessor_name
					";
		$this->_db->setQuery( $query );
		$reservations_unwanted = $this->_getList( $query );
		foreach( $reservations_unwanted as $res )
		{
			// dmp($res);
			if( LOGGER_PAYPAL_EXPRESS )
				JHotelReservationModelVariables::writeMessage("\r\nFile:".__FILE__."\r\nLine:".__LINE__."\r\nFunction:".__FUNCTION__."\r\n ID=".$res->confirmation_id.'\r\nInfo:delete pending',1); 
			JHotelReservationModelVariables::deletePendingConfirmation( $res->confirmation_id, $res->email, $res->type, false, false );
		}
	}
	
	function cleanSameReservationWaiting( $email, $datas, $datae )
	{
		$query = " 	SELECT 
						c.confirmation_id,
						c.email,
						hp.paymentprocessor_type 	AS type
					FROM #__hotelreservation_confirmations c
					INNER JOIN #__hotelreservation_confirmations_payments	cp USING( confirmation_id )
					INNER JOIN #__hotelreservation_paymentprocessors		hp USING( paymentprocessor_id )
					WHERE 
						c.datas='$datas'
						AND
						c.datae='$datae'
						AND
						c.email='$email'
						AND						
						hp.paymentprocessor_type ='".PROCESSOR_BANK_ORDER."'
						AND						
						cp.payment_status ='".PAYMENT_STATUS_WAITING."'
					GROUP BY c.confirmation_id
					ORDER BY hp.paymentprocessor_name
					";
		$this->_db->setQuery( $query );
		$reservations_unwanted = $this->_getList( $query );
		foreach( $reservations_unwanted as $res )
		{
		// dmp($res);
			JHotelReservationModelVariables::deleteWaitingConfirmation( $res->confirmation_id, $res->email, $res->type, false, false );
		}
	}
	
	static function JHotelUtil::writeMessage($message, $tip=0)
	{
		jimport('joomla.utilities.date');
		switch( $tip )
		{
			case 1:
				$myFile = JPATH_COMPONENT_SITE.DS."paypalexpresslog.txt";
				break;
			case 0:
			default:
				$myFile = JPATH_COMPONENT_SITE.DS."httpdlog_IPN.txt";
				break;
			
		}
		@chmod( $myFile, 0777);
		$fh = fopen($myFile, 'a');
		//var_dump($fh);
		$now = new JDate();
		fwrite($fh, "\n[".$now->toMySQL()."] ".$message);
		fclose($fh);
	}

	
	function setRoomDisplayPrice(&$rooms){	
		foreach( $rooms as &$room ){	
			$room->room_average_display_price = $this->convertToCurrency($room->room_average_price, $this->itemCurrency->description, $this->currency_selector);
			foreach( $room->daily as &$daily )	
			{
				$daily['display_price_final'] = $this->convertToCurrency($daily['price_final'], $this->itemCurrency->description, $this->currency_selector);
			}
		}
	}
	
	function setOfferDisplayPrice(&$offers){
		foreach( $offers as &$offer ){
			$offer->offer_average_display_price = $this->convertToCurrency($offer->offer_average_price, $this->itemCurrency->description, $this->currency_selector);
			foreach( $offer->daily as &$daily )
			{
				$daily['display_price_final'] = $this->convertToCurrency($daily['price_final'], $this->itemCurrency->description, $this->currency_selector);
			}
		}
	}

	function setOptionDisplayPrice(&$featureOptions){
		foreach ($featureOptions as &$featureOption){
			$featureOption->option_display_price = $this->convertToCurrency($featureOption->option_price, $this->itemCurrency->description, $this->currency_selector);
		} 
	}
	
	function setPackageDisplayPrice(&$packages){
		foreach( $packages as &$package ){
			$package->display_price_final = $this->convertToCurrency($package->price_final, $this->itemCurrency->description, $this->currency_selector);
			foreach( $package->daily as &$daily )	{
				$daily['display_price_final'] = $this->convertToCurrency($daily['price_final'], $this->itemCurrency->description, $this->currency_selector);
			}
			
		}
		return $packages;
	}
	
	function setArrivalOptionsDisplayPrice(&$arrivalOptions){
		foreach ($arrivalOptions as &$res){	
			$res -> arrival_option_display_price = $this->convertToCurrency($res -> arrival_option_price, $this->itemCurrency->description, $this->currency_selector);
		}
	}
	
	function setAirportTransferDisplayPrice(&$aiportTransfers){	
		foreach ($aiportTransfers as &$airportTransfer){
			$airportTransfer -> airport_transfer_type_display_price = $this->convertToCurrency($airportTransfer -> airport_transfer_type_price, $this->itemCurrency->description, $this->currency_selector);
		}
	}
	
	function setTaxDisplayPrice(&$taxes){
		foreach ($taxes as &$tax){
			if( $tax->tax_type =='Fixed'){
				$tax->tax_display_value = $this->convertToCurrency($tax->tax_value, $this->itemCurrency->description, $this->currency_selector);
			}
		}
	}
	
	function convertToCurrency($amount, $srcCurrency, $destCurrency){
	// dmp($srcCurrency." <> ".$destCurrency);
		if(strcmp($srcCurrency, $destCurrency) == 0 || strlen($destCurrency)==0 )
			return $amount;
		else{
			return round(($this->convertCurrency($srcCurrency, $destCurrency, $amount)),2);
		}
	}
	
	
	 
	/*
	 * Convert from source currency to dest currency using google converter
	 */
	function convertCurrency($from_Currency, $to_Currency, $amount) {
	
		$amount = urlencode($amount);
	
		$from_Currency = urlencode($from_Currency);
	
		$to_Currency = urlencode($to_Currency);
	
		 
	
		$url = "http://www.google.com/ig/calculator?q=$amount$from_Currency=?$to_Currency";
	
		$ch = curl_init();
	
		$timeout = 0;
	
		curl_setopt ($ch, CURLOPT_URL, $url);
	
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt ($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
		$rawdata = curl_exec($ch);
	
		curl_close($ch);
	

		$data = explode('"', $rawdata);
	
		$data = explode(' ', $data[3]);
	
		$var = $data[0];
	
		return round($var,4);
	
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
	
	function prepareArray( $type, &$val_post, &$arr_save )
	{
		$arr_save = array();
		if( $type == 0 )
		{
			if( !isset($val_post) )
				$val_post = array();
			if( isset($val_post) )
			{
				if( is_array($val_post) )
				{
					$arr = array();
					foreach( $val_post as $v )
					{
						if( is_array($v) )
							$arr = $v;
						else
							$arr = explode('|',$v);
						
						if( count($arr) >= 3 )
							$arr_save[ $arr[0].'|'.$arr[1].'|'.$arr[2].'|'.$arr[3] ][]				= $arr;
					}
				}
				else
				{
					$ex 	= explode(',', $val_post);
					foreach( $ex as $v_ex)
					{
						$v_ex_1 = explode('|', $v_ex);
						if( count($v_ex_1) >= 3 )
						{
							$arr_save[ $v_ex_1[0].'|'.$v_ex_1[1].'|'.$v_ex_1[2].'|'.$v_ex_1[3] ][]				= $v_ex_1;
						}
						
					}
				}
			}
		}
		else if( $type == 1 )
		{
			if( !isset($val_post) )
				$val_post = '';
			if( isset($val_post) )
			{
				if( is_array($val_post) )
				{
					$arr = array();
					foreach( $val_post as $v )
					{
						if( is_array($v)  )
							$arr = $v;
						else
							$arr = explode('|',$v);
					
						if( count($arr) > 3 )
						{
							$arr_save[ $arr[0].'|'.$arr[1].'|'.$arr[2].'|'.$arr[3] ]			= $arr;
						}
					}
				}
				else
				{
					$ex 	= explode(',', $val_post);
					foreach( $ex as $v_ex)
					{
						$v_ex_1 = explode('|', $v_ex);
						if( count($v_ex_1) > 3 )
						{
							$arr_save[ $v_ex_1[0].'|'.$v_ex_1[1].'|'.$v_ex_1[2].'|'.$v_ex_1[3] ]	= $v_ex_1;
						}
						
					}
				}
			}
		}
		else if($type == 2 )
		{
			if( isset($val_post) )
			{
				if( is_array( $val_post))
				{
					$ex = array();
					foreach(  $val_post as $v )
					{
						if( is_array($v) )
						{	
							$ex1 = $v;
						}
						else
							$ex1 = explode('|', $v);

						if(isset($ex1[0]) && isset ($ex1[1]) && isset($ex1[3]))
							$ex[ $ex1[0].'|'.$ex1[1].'|'.$ex1[2].'|'.$ex1[3]  ] = array($ex1[0], $ex1[1], $ex1[2], $ex1[3], $ex1[4]);
					}
					$arr_save		= $ex;
				}
				else
				{
					//dmp($val_post);
					$ex 	= explode(',', $val_post);
					foreach( $ex as $v_ex)
					{
						$v_ex_1 = explode('|', $v_ex);
						if( count($v_ex_1) == 5  )
						{
							$arr_save[ $v_ex_1[0].'|'.$v_ex_1[1].'|'.$v_ex_1[2].'|'.$v_ex_1[3] ]	= array($v_ex_1[0], $v_ex_1[1], $v_ex_1[2], $v_ex_1[3], $v_ex_1[4]); 
						}
						
					}
				}
				//dmp($arr_save);
				//exit;
			}
			
		}
		else if( $type == 4 )
		{
			if( !isset($val_post) )
				$val_post = '';
			if( isset($val_post) )
			{
				if( is_array($val_post) )
				{
					$arr = array();
					foreach( $val_post as $v )
					{
						if( is_array($v)  )
							$arr = $v;
						else
							$arr = explode('|',$v);
					
						if( count($arr) > 3 )
						{
							$arr_save[ $arr[0].'|'.$arr[1].'|'.$arr[2] ]			= $arr;
						}
					}
				}
				else
				{
					$ex 	= explode(',', $val_post);
					foreach( $ex as $v_ex)
					{
						$v_ex_1 = explode('|', $v_ex);
						if( count($v_ex_1) > 3 )
						{
							$arr_save[ $v_ex_1[0].'|'.$v_ex_1[1].'|'.$v_ex_1[2] ]	= $v_ex_1;
						}
						
					}
				}
			}
		}
		else if( $type == 5 )
		{
			if( !isset($val_post) )
				$val_post = array();
				
			if( isset($val_post) )
			{
				if( is_array($val_post) )
				{
					$arr = array();
					foreach( $val_post as $v )
					{
						if( is_array($v)  )
							$arr = $v;
						else
							$arr = explode('|',$v);
					
						if( count($arr) > 0 )
							$arr_save[]			= $arr;
					}
				}
				else
				{
					$ex 	= explode(',', $val_post);
					foreach( $ex as $v_ex)
					{
						$arr_save[] = explode('|', $v_ex);
						
					}
				}
			}
		}
		
		
	}
	
	function displayHiddenValues( $name_field, $array_conditions = array( 'operation'=>'', 'type'=>'array', 'check_field_zero' => 0, 'skip_value'=>'' ) )
	{
		if( !isset($array_conditions['operation']) )
			$array_conditions['operation'] = '';
		if( !isset($array_conditions['type']) )
			$array_conditions['type'] = 'array';
		if( !isset($array_conditions['check_field_zero']) )
			$array_conditions['check_field_zero'] = 0;
		if( !isset($array_conditions['skip_value']) )
			$array_conditions['skip_value'] = array();
			
				
		foreach( $this->{$name_field} as $v)
		{
			
			if( $array_conditions['operation'] == 'edit' )
			{
				$is_continue = true;
				if( is_array($array_conditions['skip_value']) && count($array_conditions['skip_value']) > 0)
				{
					if( $array_conditions['type'] == 'multiarray'  )
					{
						foreach( $v as $v_cmp )
						{
							for( $i = 0; $i < count( $array_conditions['skip_value'] ); $i++ )
							{
								if( isset( $v_cmp[ $i ] ) && $v_cmp[ $i ] != $array_conditions['skip_value'][$i] )
								{
									$is_continue = false;
									break;
								}
							}
						}
					}
					else
					{
						$v_cmp = is_array($v)? $v : explode('|', $v);
						// dmp($v_cmp);
						// dmp($array_conditions['skip_value']);
						for( $i = 0; $i < count( $array_conditions['skip_value'] ); $i++ )
						{
							if( isset( $v_cmp[ $i ] ) && $v_cmp[ $i ] != $array_conditions['skip_value'][$i] )
							{
								$is_continue = false;
								break;
							}
						}
					}
				}
				
				if( $is_continue )
				{
					// echo $name_field;
					continue;
				}
			}
			
			if( is_array($v) && $array_conditions['type'] == 'value' )
			{
				foreach( $v as $v1 )
				{
					$ex = explode( '|', $v1 );
					if( $array_conditions['check_field_zero']  > 0 && $ex[ $array_conditions['check_field_zero'] ] == 0 )
						continue;
					?>
					<input type="hidden" name="<?php echo$name_field?>[]" 					id="<?php echo$name_field?>[]" 				value="<?php echo $v1?>" /> 
					<?php
				}
			}
			else if( is_array($v) && $array_conditions['type'] == 'multiarray' )
			{
				//check is multiple array
				$is_simple  = false;
				foreach( $v as $v1 )
				{
					if( !is_array($v1)) 
						$is_simple = true;
				}
				//~check is multiple array
				
				//dmp($_POST);
				// dmp($name_field);
				if( $is_simple )
				{
					if( $array_conditions['check_field_zero']  > 0 && $v[ $array_conditions['check_field_zero'] ] == 0 )
							continue;
					?>
					<input type="hidden" name="<?php echo$name_field?>[]" 					id="<?php echo$name_field?>[]" 			value="<?php echo implode("|",$v)?>" /> 
					<?php
				}
				else
				{
					foreach( $v as $v1 )
					{
						if( $array_conditions['check_field_zero']  > 0 && $v1[ $array_conditions['check_field_zero'] ] == 0 )
							continue;
						?>
						<input type="hidden" name="<?php echo$name_field?>[]" 					id="<?php echo$name_field?>[]" 			value="<?php echo implode("|",$v1)?>" /> 
						<?php
					}
				}
			}
			else if( is_array($v) && $array_conditions['type'] == 'array' )
			{
				if( $array_conditions['check_field_zero']  > 0 && $v[ $array_conditions['check_field_zero'] ] == 0 )
					continue;
				?>
				<input type="hidden" name="<?php echo$name_field?>[]" 					id="<?php echo$name_field?>[]" 					value="<?php echo implode("|",$v)?>" /> 
				<?php
			}
			else if( $array_conditions['type'] == 'value' )
			{
				$ex = explode( '|', $v );
				if( $array_conditions['check_field_zero']  > 0 && $ex[ $array_conditions['check_field_zero'] ] == 0 )
					continue;
				?>
				<input type="hidden" name="<?php echo$name_field?>[]" 					id="<?php echo$name_field?>[]" 					value="<?php echo $v?>" /> 
				<?php
			}
		}
	}

	function getReservedItems($type = '')
	{
		$nr = 0;
		// dmp($this->items_reserved );
		if(is_array($this->items_reserved) && count($this->items_reserved)>0){
			foreach( $this->items_reserved as $value )
			{
				$ex = explode( '|', $value );
				if(
				$type=='edit'
				&&
				$ex[0] == $this->reserve_offer_id
				&&
				$ex[0] == $this->reserve_room_id
				&&
				$ex[0] == $this->reserve_current
				)
				{
					continue;
				}
				$nr ++;
			}
		}
		return $nr;
	}
	function getUniqueCode($length = "")
	{
		$code = md5(uniqid(rand(), true));

		if ($length != "")
		return substr($code, 0, $length);
		else
		return $code;
	}

	
	function getReservation($reservationId){
		$query = "select hc.*, GROUP_CONCAT(hcr.room_id) as rooms  from #__hotelreservation_confirmations  AS hc
				  inner join #__hotelreservation_confirmations_rooms hcr on hc.confirmation_id= hcr.confirmation_id
				  where hc.confirmation_id= $reservationId";
		$this->_db->setQuery( $query );
		$reservation = $this->_db->loadObject();
		
		return $reservation;
	}
	
}


