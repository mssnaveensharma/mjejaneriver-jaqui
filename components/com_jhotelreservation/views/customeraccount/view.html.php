<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
UserService::isUserLoggedIn();
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';
?>
<?php
class JHotelReservationViewCustomerAccount extends JViewLegacy
{
	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $tpl = $this->$function();
		parent::display($tpl);
	}
	
	function editaccount(){
		$model = $this->getModel();
		$userData = UserDataService::getUserData();
		
		$personalData = $model->getClientData();
		$this->row = $personalData;
		$tpl = 'editaccount';
		return $tpl;
	}
	function managereservations(){
		$this->rows = ReservationService::getClientReservations(JFactory::getUser()->id);
		$tpl = 'managereservations';
		return $tpl;
	}
	function editReservation(){
		
		$this->item		= $this->get('Item');
		$this->state		= $this->get('State');
		
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$this->roomTypes 	= $this->get('RoomTypesOptions');
		$this->guestTypes = JHotelReservationHelper::getGuestTypes();
		
		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);
		
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('
			var baseUrl="'.(JURI::base().'/administrator/index.php?option='.getBookingExtName()).'";
		');
		
		$doc->addScript('administrator/components/'.getBookingExtName().'/assets/js/reservation.js' );
		
		$tpl = 'editreservation';
		return $tpl;
	}
}