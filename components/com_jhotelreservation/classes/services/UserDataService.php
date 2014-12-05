<?php

require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/logger.php';

class UserDataService{

	public static function getUserData(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("getUserData");
		
		$session = self::getJoomlaSession();
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
			
		if(!isset($userData)){
			//$log->LogDebug("Reserved items: ".serialize($userData->reservedItems));
			$userData = self::initializeUserData();
			$_SESSION['userData'] = $userData;
		}
		
		if(empty($userData->hotelId)){
			$userData->hotelId = 0;
			$_SESSION['userData'] = $userData;
			//$app = JFactory::getApplication();
			//$app->enqueueMessage("Your session has expired", 'warning');
		}
		
		return $userData;
	}
	
	public static function reserveRoom($hotelId, $reservedItem, $current){
	
 		$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
 		$log->LogDebug("reserveRoom hotelId= $hotelId, reservedItem = $reservedItem, current = $current");
		
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		//dmp($userData->reservedItems);
		//dmp($reservedItem);
		//remove all rooms that have same current
		$result = array();
		foreach($userData->reservedItems as $rsvItem){
			$values = explode("|",$rsvItem);
			if( $values[2]!= $current){
				$result[] = $rsvItem;
			}
		}
		$userData->reservedItems = $result;
		
		//add new room
		$reservedItem = $reservedItem."|".$current;
		$userData->reservedItems[] = $reservedItem;
		$userData->hotelId = $hotelId;
		//dmp($userData->reservedItems);
		$log->LogDebug("Reserved items: ".serialize($userData->reservedItems));
		
		$_SESSION['userData'] = $userData;
		return $reservedItem;
	}
	
	public static function updateRooms($hotelId, $reservedItems){
		$userData = $_SESSION['userData'];
		$reservedItems = explode('||',$reservedItems);
		$userData->reservedItems = $reservedItems;
		if(!empty($hotelId)){
			$userData->hotelId = $hotelId;
		}
		$_SESSION['userData'] = null;
		$_SESSION["userData"] = $userData;
	}
	
	public static function removeLastRoom(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("removeLastRoom");
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$reservedItems = $userData->reservedItems;
		//dmp($reservedItems);
		if(isset($reservedItems[count($reservedItems)-1])) {
			unset($reservedItems[count($reservedItems)-1]);
		}
		$current  = count($reservedItems)+1;
		$userData->reservedItems = $reservedItems;
		//dmp($reservedItems);
		$extraOptions = $userData->extraOptionIds;
		//dmp($extraOptions);
		$result = array();
		foreach($extraOptions as $extraOption){
			$values = explode("|",$extraOption);
			if( $values[2]!= $current){
				$result[] = $extraOption;
			}
				
		}
		$userData->extraOptionIds = $result;

		$_SESSION['userData'] = $userData;
	}
	
	public static function reserveOffer($hotelId, $reservedItem){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$reservedItem = $reservedItem."|".(count($userData->reservedItems)+1);
		$userData->reservedItems[] = $reservedItem;
		$userData->hotelId = $hotelId;
	
		$_SESSION['userData'] = $userData;
	}
	
	public static function addExtraOptions($extraOptionsIds){
		//dmp($extraOptionsIds);
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		
		$userData->extraOptionIds = array_merge($userData->extraOptionIds,$extraOptionsIds);
		
		$_SESSION['userData'] = $userData;
	}
	
	public static function addExcursions($excursions){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];

		if(is_array($userData->excursions))
			$userData->excursions = array_merge($userData->excursions,$excursions);
		else if ($excursions!="")
			$userData->excursions = $excursions;
		
		$userData->hotelId = 0;
		$_SESSION['userData'] = $userData;
	}
	
	public static function addGuestDetails($guestDetails){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->first_name = ucfirst($guestDetails["first_name"]);// ucfirst(strtolower($guestDetails["first_name"]));
		$userData->remarks = $guestDetails["remarks"];
		$userData->last_name = ucfirst($guestDetails["last_name"]);//ucfirst(strtolower($guestDetails["last_name"]));
		$userData->address	= ucfirst($guestDetails["address"]);
		$userData->city	= $guestDetails["city"];
		$userData->state_name	= $guestDetails["state_name"];
		$userData->country	= $guestDetails["country"];
		$userData->postal_code= strtoupper($guestDetails["postal_code"]);
		$userData->phone = $guestDetails["phone"];
		$userData->email= $guestDetails["email"];
		$userData->conf_email = $guestDetails["conf_email"];
		$userData->company_name=$guestDetails["company_name"];
		$userData->guest_type = $guestDetails["guest_type"]; 
		
		$_SESSION['userData'] = $userData;
	}
	
	public static function setReservationDetails($reservationDetails){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->total = $reservationDetails->total;
		$userData->cost = $reservationDetails->cost;
		$_SESSION['userData']= $userData;
	}
	
	public static function setCurrency($currencyName, $currencySymbol){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("setCurrency");
		$currency = new stdClass();
		$currency->name = $currencyName;
		$currency->symbol = $currencySymbol;
		
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->currency = $currency;
		
		if($userData->user_currency=="")
			$userData->user_currency = $currency->name;
		$_SESSION['userData'] = $userData;
	}
	
	public static function prepareGuestDetails(){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$guestDetails = array();
		for($i=0;$i<$userData->total_adults;$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name="";
			$guestDetail->last_name="";
			$guestDetail->identification_number="";
			$guestDetails[] = $guestDetail;
		}
		
		$userData->guestDetails = $guestDetails;
		$_SESSION['userData'] = $userData;
	}

	public static function storeGuestDetails($data){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$guestDetails = array();
		for($i=0;$i<count($data["guest_first_name"]);$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name = $data["guest_first_name"][$i];
			$guestDetail->last_name = $data["guest_last_name"][$i];
			$guestDetail->identification_number= $data["guest_identification_number"][$i];
			$guestDetails[] = $guestDetail;
		}
		
		$userData->guestDetails = $guestDetails;
		$_SESSION['userData'] = $userData;
	}
	
	public static function setDiscountCode($discountCode){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->discount_code = $discountCode;
		$_SESSION['userData'] = $userData;
	}
	
	public static function updateUserData(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("updateUserData");
		$session = self::getJoomlaSession();
		$userData = self::getUserData();
		$userData =  $_SESSION['userData'];

		$currentRoom = count($userData->reservedItems);
		//if(isset())
		if(isset($userData->roomGuests[$currentRoom]))
			$userData->adults = $userData->roomGuests[$currentRoom];
		else
			$userData->adults = 2;
		if(isset($userData->roomGuestsChildren[$currentRoom]))
			$userData->children = $userData->roomGuestsChildren[$currentRoom];
		else
			$userData->children = 0;
		//dmp($userData);
		$_SESSION['userData'] = $userData;
	}
	  
	/**
	 * Initialiaze search criteria
	 */
	public static function initializeUserData($resetUserData = false){
 		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
 		//$log->LogDebug("initializeUserData");
		
		$get = JRequest::get( 'get' );
		$post = JRequest::get( 'post' );
		if(count($post)==0)
			$post = $get;
		
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
		//$log->LogDebug("UserData first: ".serialize($userData->reservedItems));
		if(!isset($userData) || isset($post["resetSearch"]) || $resetUserData){
			$userData = self::createUserData($post);
			$_SESSION['userData'] = $userData;
		}
		
		if(JRequest::getVar( 'minNights')!='')
			$userData = self::changeDepatureDate($userData,$userData->start_date ,JRequest::getVar( 'minNights'));
		
		if(isset($get['filterParams'])){
			//dmp(here);
			$userData->voucher='';
			$userData->keyword='';
			$_SESSION['userData'] = $userData;
		}
		
		$userData = self::initializeFilter($userData,$post);
		
 	//	$log->LogDebug("UserData end:  ".serialize($userData->reservedItems));
		$_SESSION['userData'] = $userData;
		return $userData;
	}
	
	public static function initializeReservationData(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("initializeReservationData");
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->reservedItems = array();
		$userData->confirmation_id = 0;
		$userData->totalPaid= 0;
		$userData->extraOptionIds=array();
		$_SESSION['userData'] = $userData;
		//$log->LogDebug("UserData: ".serialize($userData->reservedItems));

		return $userData;
	}
		
	public static function initializeExcursions(){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->excursions = array();
		$_SESSION['userData'] = $userData;
	}

	public static function createUserData($data){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("createUserData");
		$userData = new stdClass();
		//var_dump("create user data");
		if(isset($data) && count($data)>0 && isset($data["jhotelreservation_datas"])){
		
			if(isset($data["keyword"]))
				$userData->keyword = $data["keyword"];
			else
				$userData->keyword = "";
			$userData->start_date = JHotelUtil::convertToMysqlFormat($data["jhotelreservation_datas"]);
			$userData->end_date = JHotelUtil::convertToMysqlFormat($data["jhotelreservation_datae"]);
			$userData->rooms = $data["rooms"];
			$userData->adults = $data["guest_adult"];
			$userData->total_adults = $userData->adults;
			$userData->children = $data["guest_child"];
			$userData->total_children= $userData->children;
			$userData->year_start =$data["year_start"];
			$userData->month_start =$data["month_start"];
			$userData->day_start =$data["day_start"];
			$userData->year_end = $data["year_end"];
			$userData->month_end =$data["month_end"];
			$userData->day_end =$data["day_end"];
			if(isset($data["user_currency"]))
				$userData->user_currency =$data["user_currency"];
			else 
				$userData->user_currency = "";
			if(isset($data["excursions"]))
				$userData->excursions =$data["excursions"];
			else
				$userData->excursions = array();
				
		
			$userData->voucher = isset($data["voucher"])?$data["voucher"]:"";
			//$log->LogDebug("voucher: ".$userData->voucher);
			$userData->filterParams ='';
			$userData->searchFilter = array("filterCategories"=>array());
			
			if(isset($data["room-guests"]) && count($data["room-guests"])>1){
				$userData->roomGuests = $data["room-guests"];
				$userData->rooms = count($userData->roomGuests);
				$userData->total_adults = 0;
				foreach($userData->roomGuests as $guestPerRoom){
					$userData->total_adults+= $guestPerRoom;
				}
				JRequest::setVar('jhotelreservation_rooms',$userData->rooms);
				JRequest::setVar('jhotelreservation_guest_adult',$userData->total_adults);
			}else{
				$userData->roomGuests=array($data["guest_adult"]); 
			}
			
			if(isset($data["room-guests-children"]) && count($data["room-guests-children"])>1){
				$userData->roomGuestsChildren = $data["room-guests-children"];
				$userData->total_children = 0;
				foreach($userData->roomGuestsChildren as $guestPerRoom){
					$userData->total_children+= $guestPerRoom;
				}
				JRequest::setVar('jhotelreservation_guest_child',$userData->total_children);
			}else{
				$userData->roomGuestsChildren=array($data["guest_child"]);
			}
			
			 
			$userData->noDates = JRequest::getVar('no-dates', null);
		}else{
			$userData->searchType = '';
			$userData->keyword = '';
			$userData->start_date = date('Y-m-d');
			$userData->end_date = date( "Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));;
			$userData->rooms = '1';
			$userData->roomGuests = array(2);
			$userData->roomGuestsChildren = array(0);
			$userData->adults = isset($data["guest_adult"])?$data["guest_adult"]:'2';
			$userData->children = isset($data["guest_children"])?$data["guest_children"]:'0';
			$userData->total_adults = $userData->adults;
			$userData->total_children = $userData->children;
			$userData->nights = '1';
			$userData->year_start =date('Y');
			$userData->month_start =date('m');
			$userData->day_start = date('d');
			$userData->year_end = date( "Y",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->month_end = date( "m",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->day_end = date( "d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->voucher = isset($data["voucher"])?$data["voucher"]:"";
			$userData->filterParams ='';
			$userData->searchFilter = array("filterCategories"=>array());
			$userData->excursions=array();
			
			if(isset($data["keyword"]))
				$userData->keyword = $data["keyword"];
			else
				$userData->keyword = "";

			$userData->user_currency = "";

		}
		
		$userData->reservedItems = array();
		$userData->confirmation_id = 0;
		$userData->first_name = '';
		$userData->last_name = '';
		$userData->address	= '';
		$userData->city	= '';
		$userData->state_name	= '';
		$userData->country	= '';
		$userData->postal_code= '';
		$userData->phone = '';
		$userData->email= '';
		$userData->conf_email = '';
		$userData->company_name='';
		$userData->coupon_code='';
		//$userData->voucher='';
		$userData->guest_type = 0;
		$userData->discount_code='';
		$userData->remarks='';
		$userData->remarks_admin='';
		$userData->media_referer='';
		$userData->arrival_time='';
		$userData->totalPaid= 0;
		$userData->extraOptionIds=array();
		
		return $userData;
	}
	
 	public static function getNrDays(){
 		$session = self::getJoomlaSession();
 		$userData =  $_SESSION['userData'];
 		$diff = abs(strtotime($userData->start_date) - strtotime($userData->end_date));
 		$years = floor($diff / (365*60*60*24));
 		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
 		$nrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
 		
 		return $nrDays;
 	}
	
 	
 	
	public static function changeDepatureDate($userData, $currentDate, $days){
		
		$currentDate = strtotime($currentDate);
		$date = date( "Y-m-d",mktime(0, 0, 0, date("m"), date("d")+$days, date("Y")));
		$userData->end_date = date( "Y-m-d",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->year_end = date( "Y",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->month_end = date( "m",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->day_end = date( "d",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		return $userData;
	}
	
	 public static function initializeFilter($userData, $post){
		$userData->filterParams = JRequest::getVar('filterParams');
		if(JRequest::getInt("resetSearch")==1 && JRequest::getInt("searchId",0)==0){
			$userData->filterParams= "";
		}
		$userData->orderBy = JRequest::getVar('orderBy');
		
		if($userData->orderBy=='' && $userData->voucher == '')
			$userData->orderBy ="noBookings desc";
		if(isset($post["voucher"])){
			$userData->voucher = $post["voucher"];
		}
		
		return $userData;
	}
	
	public static function setReservedItems($reservedItems){
		$session = self::getJoomlaSession();
		$userData =  $_SESSION['userData'];
		$userData->reservedItems = $reservedItems;
		$_SESSION['userData']= $userData;
	}
	
	private static function getJoomlaSession(){
		$session = JFactory::getSession();
		
		if(JHotelUtil::isJoomla3())
			$isActive = $session->isActive();
		else 
			$isActive = $session->getState()=="active"?true:false;

		if (!$isActive) {
			$app = JFactory::getApplication();
			$app->enqueueMessage("Your session has expired", 'warning');
			$msg = "Your session has expired";
			$app->redirect( 'index.php?option='.getBookingExtName().'&task=hotels.searchHotels', $msg );
			$app->enqueueMessage("Your session has expired", 'warning');
		}
		else 
			return $session;
	}
	
}

?>