<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelExtraOptions extends JModelItem{
	
	protected function populateState(){
		$app = JFactory::getApplication('site');

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
	}
	
	public function getExtraOptions(){
		
		$userData = UserDataService::getUserData();
	
		$roomReserved = $userData->reservedItems[count($userData->reservedItems)-1];
		$roomReservedInfo = explode("|",$roomReserved);
		//dmp($roomReservedInfo);
		$extraOptions = ExtraOptionsService::getHotelExtraOptions($userData->hotelId, $userData->start_date, $userData->end_date, array(), $roomReservedInfo[1], $roomReservedInfo[0]);
		
		return $extraOptions;
	}
	
	
}