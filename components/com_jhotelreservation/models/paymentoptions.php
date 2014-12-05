<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * PaymentOptions model
 *
 */
class JHotelReservationModelPaymentOptions extends JModelItem{
	
	protected function populateState(){
		$app = JFactory::getApplication('site');
		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
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
	
	function getPaymentMethods(){
		$paymentMethods = PaymentService::getPaymentProcessors();
		return $paymentMethods;
	}
	
}