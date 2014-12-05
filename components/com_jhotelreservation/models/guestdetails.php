<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelGuestDetails extends JModelItem{
	
	protected function populateState(){
		$app = JFactory::getApplication('site');
		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
	}
	
	function getGuestDetails(){
		
	}
	
	function getReservationDetails(){
		$userData = UserDataService::getUserData();
		$reservationData = new stdClass;
		$reservationData->userData = $userData;
	
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = HotelService::getHotel($userData->hotelId);
	
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->generateReservationSummary($reservationData);
	
		UserDataService::setReservationDetails($reservationDetails);
		$reservationDetails->reservationData = $reservationData;
	
		return $reservationDetails;
	}
	
	function getCountries(){
		$query = ' SELECT * FROM #__hotelreservation_countries order by country_name';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
}