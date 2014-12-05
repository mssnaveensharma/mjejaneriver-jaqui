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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JHotelReservationControllerHotel extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function showHotel(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("showHotel");
		//initialize search criteria
		//remove user data
		$data = JRequest::get("get");
		UserDataService::initializeUserData();
		if(isset($data["init_hotel"])){
			UserDataService::initializeReservationData();
		}
		
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotel_id");
		
		if(!empty($reservedItems)){
			UserDataService::updateRooms($hotelId, $reservedItems);
		}
		
		//$userData = UserDataService::getUserData();
		//dmp($userData->reservedItems);
		JRequest::setVar("view","hotel");
		parent::display();
	}
	
	function changeSearch(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
	//	$log->LogDebug("changeSearch");
		UserDataService::initializeUserData();
		$data = JRequest::get("post");
		UserDataService::initializeReservationData();
		$hotel = HotelService::getHotel($data["hotel_id"]);
		$link = JHotelUtil::getHotelLink($hotel);
		$this->setRedirect($link);	
	}
	
	function reserveRoom(){
		$data = JRequest::get("post");
				
		$reservedItems = JRequest::getVar("reservedItems");
		$extraOptions = JRequest::getVar("extraOptions");
		
		$reservedItem = UserDataService::reserveRoom($data["hotel_id"], $data["reserved_item"], $data["current"]);
		$reservedItems = empty($reservedItems)?$reservedItem:$reservedItems."||".$reservedItem;

		$extraParam ="";
		if(!empty( $extraOptions )){
			$extraParam = "&extraOptions=".$extraOptions ;
		}

		$current = count(explode('#',$reservedItems));
	
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=extraoptions.showExtras&hotelId='.$data["hotel_id"].'&current='.$current.'&reservedItems='.$reservedItems.$extraParam, false));
	}
	
	function getRoomCalendar(){
		//header('Content-type: XML');
	
		$year = JRequest::getVar("year");
		$month = JRequest::getVar("month");
		$identifier = JRequest::getVar("identifier");
		
		//dmp($year);
		//dmp($month);
		
		$calendars = $this->generateCalendarData($year, $month);
		$calendar = $calendars[$identifier];
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer identifier="'.$identifier.'" calendar="'.htmlspecialchars($calendar).'" />';
		echo '</room_statement>';
		echo '</xml>';
	
		JFactory::getApplication()->close();
	}
	
	function getRoomCalendars(){
		
		$calendars = $this->generateCalendarData();
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		foreach($calendars as $key=>$value){
			echo '<answer identifier="'.$key.'" calendar="'.htmlspecialchars($value).'" />';
		}
		echo '</room_statement>';
		echo '</xml>';
	
		JFactory::getApplication()->close();
	}
	
	function generateCalendarData($year = 0, $month=0){
		
		$session = JFactory::getSession();
		$userData =  $_SESSION['userData'];

		$post = JRequest::get('post');
		$get = JRequest::get('get');
		if(!count($post))
			$post = $get;
		
		if(!isset($post["hotel_id"])){
			$post["hotel_id"] = JRequest::getInt( 'hotel_id');
		}
	
		$year_start = $userData->year_start;
		$month_start = $userData->month_start;
		$day_start = 1;
		$year_end = $userData->year_end;
		$month_end = $userData->month_start;
		$day_end =  date('t', mktime(0, 0, 0, $userData->month_start, 1, $userData->year_start));
	
		$hotelId = $post["hotel_id"];
		$currentRoom = $post["current_room"];
		//dmp($currentRoom);
		$adults = $userData->adults;
		$children = $userData->children;
		//dmp($userData);
		if(isset($userData->roomGuests)){
			$adults = $userData->roomGuests[$currentRoom-1];
		}
		
		$post["guest_adult"] = $adults;
		$post["guest_child"] = $userData->children;
		$post["rooms"] = $userData->rooms;
	
		if($year!=0){
			$post["year_start"] = $year;
			$post["year_end"] = $year;
			$year_start = $year;
			$year_end = $year;
		}
		if($month != 0){
			$post["month_start"] = $month;
			$post["month_end"] = $month;
			$month_start = $month;
			$month_end = $month;
		}
			
		$number_persons = $post["guest_adult"];
		
		//dmp($post);
		$datasi			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_start, $userData->day_start,$userData->year_start )	);
		$dataei			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_end, $userData->day_end,$userData->year_end ));
		
		$diff = abs(strtotime($dataei) - strtotime($datasi));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		
		$initialNrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		//dmp($initialNrDays);
	
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $month_start, $day_start,$year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $month_end, $day_end + 7,$year_end ));
	
		//echo $adults." ";
		//dmp($adults);
		
	/* 	dmp($year_start);
		dmp($month_start);
		dmp($datas);
		dmp($datae); */
		//exit;
		$offers =HotelService::getHotelOffers($hotelId,$datas, $datae,array(),$adults, $children);
		$rooms =HotelService::getHotelRooms($hotelId,$datas, $datae,array(),$adults, $children);
		
		
		$bookingsDays = BookingService::getNumberOfBookingsPerDay($hotelId,$datas, $datae);
		$hoteAvailability = HotelService::getHotelAvailabilyPerDay($hotelId,$datas, $datae);
		
		$temporaryReservedRooms= BookingService::getReservedRooms($userData->reservedItems);
		$temporaryReservedRooms["datas"]= $datasi;
		$temporaryReservedRooms["datae"]= $dataei;
		//dmp($temporaryReservedRooms);

		
		$roomsCalendar =HotelService::getRoomsCalendar($rooms,$initialNrDays,$adults,$children,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
		$offersCalendar = HotelService::getOffersCalendar($offers,$initialNrDays,$adults,$children,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
		
		//dmp($roomsCalendar);
		//dmp($offersCalendar);
	
		//combining the calendars
		$calendar = array_combine(
				array_merge(array_keys($roomsCalendar),array_keys($offersCalendar)),
				array_merge(array_values($roomsCalendar),array_values($offersCalendar))
		);
		//dmp($calendar);
		return $calendar;
	}
	
	public function checkReservationPendingPayments(){
		BookingService::checkReservationPendingPayments();
		JFactory::getApplication()->close();
	}
	
	public function checkAvailability(){
		$hotelId = 110;
		$startDate ="2013-10-05";
		$endDate ="2013-10-07";
		
		$isHotelAvailable = HotelService::checkAvailability($hotelId, $startDate, $endDate);
		dmp($isHotelAvailable);
		
		if(!$isHotelAvailable){
			EmailService::sendNoAvailabilityEmail($hotelId, $startDate, $endDate);
		}
	}
}