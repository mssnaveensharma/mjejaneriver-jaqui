<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelHotel extends JModelItem{

	protected function populateState(){
		$app = JFactory::getApplication('site');
		
		// Load state from the request.
		$pk = JRequest::getInt('hotel_id');
		$this->setState('hotel.id', $pk);
		
		$tabId = JRequest::getInt('tabId',1);
		$this->setState('hotel.tabId', $tabId);
		
		
		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
		
		UserDataService::updateUserData();
	}
	
	
	public function getItem($pk = null){
		// Initialise variables.
		$hotel = HotelService::getHotel($this->getState('hotel.id'));
		//dmp($hotel->hotel_currency);
		UserDataService::setCurrency($hotel->hotel_currency, $hotel->currency_symbol);
		
		return $hotel;
	}
	
	function getOffers(){
		$userData = UserDataService::getUserData();
		
		$offers =  HotelService::getHotelOffers($this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		BookingService::setRoomAvailability($offers, $userData->reservedItems, $this->getState('hotel.id'),  $userData->start_date,  $userData->end_date);
		
		//dmp($offers);
		return $offers;
	}
	
	function getRooms(){
		$userData = UserDataService::getUserData();
		
		$rooms = HotelService::getHotelRooms($this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		BookingService::setRoomAvailability($rooms, $userData->reservedItems, $this->getState('hotel.id'),  $userData->start_date,  $userData->end_date);
		
		return $rooms;
	}
	
	function getExcursions(){
		$userData = UserDataService::getUserData();
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_EXCURSIONS,$this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		return $excursions;
	}
	
	function getCourses(){
		$userData = UserDataService::getUserData();
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_COURSES,$this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		return $excursions;
	}
	
}