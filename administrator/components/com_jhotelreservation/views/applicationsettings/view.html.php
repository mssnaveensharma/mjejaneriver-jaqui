<?php
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!checkUserAccess(JFactory::getUser()->id,"application_settings")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('script','administrator/components/'.getBookingExtName().'/assets/js/jquery.upload.js');

class JHotelReservationViewApplicationSettings extends JViewLegacy
{
	function display($tpl = null)
	{
		$item = $this->get('Data'); 
		$this->item =  $item; 
		
		$elements = new stdClass();
		//hotel important informations
		$elements->show_price_per_person = JHTML::_('select.booleanlist', "show_price_per_person",'',$item->show_price_per_person);
		$elements->charge_only_reservation_cost = JHTML::_('select.booleanlist', "charge_only_reservation_cost" , '', $item->charge_only_reservation_cost);
		$elements->send_invoice_to_email = JHTML::_('select.booleanlist', "send_invoice_to_email" , '', $item->send_invoice_to_email);
		$this->elements = $elements;
		$this->languages = $this->get('Languages');
		$this->addToolbar();
		
		parent::display($tpl);
	}
	
	function addToolbar(){
		$canDo = JHotelReservationHelper::getActions();
	
		JToolBarHelper::title(   JText::_('LNG_APPLICATION_SETTINGS',true), 'generic.png' );
		if ($canDo->get('core.create')){
			JToolBarHelper::apply('applicationsettings.apply');
			JToolBarHelper::save('applicationsettings.save');
		}
		JToolBarHelper::cancel('applicationsettings.cancel');
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jhotelreservation');
		}
		JHotelReservationHelper::addSubmenu('applicationsettings');
	}
	
	function includeStyling(){
		
	}
	
	
}