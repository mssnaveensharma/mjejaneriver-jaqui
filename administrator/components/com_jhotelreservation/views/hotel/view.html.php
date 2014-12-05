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

if (!checkUserAccess(JFactory::getUser()->id,"manage_hotels")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'hoteltranslations.php');

JHTML::_('script', 'http://maps.google.com/maps/api/js?sensor=true&libraries=places');


class JHotelReservationViewHotel extends JViewLegacy
{
	function display($tpl = null)
	{
			$item				=$this->get('Data'); 
			$this->item =  $item; 
					
			$appSettings = JHotelUtil::getApplicationSettings();
			
			$this->lodgingtypes = $this->getModel('lodgingtypes');
			$this->facilities = $this->getModel('facilities');
			$this->accomodationtypes = $this->getModel('accomodationtypes');
			$this->environmenttypes = $this->getModel('environmenttypes');
			$this->paymentoptions = $this->getModel('paymentoptions');
			$this->regiontypes = $this->getModel('regiontypes');
							
			JToolBarHelper::title(    'J-Hotel Reservation : '.( $item->hotel_id > 0? JText::_( "LNG_EDIT",true) : JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_HOTEL',true), 'generic.png' );

			$elements = new stdClass();
			//hotel important informations
			$elements->allowPets = JHTML::_('select.booleanlist', "pets",'',$item->informations->pets);
			$elements->parking = JHTML::_('select.booleanlist', "parking" , '', $item->informations->parking);
			$elements->wifi = JHTML::_('select.booleanlist', "wifi" , '', $item->informations->wifi);
			$elements->publicTransport = JHTML::_('select.booleanlist', "public_transport" , '',$item->informations->public_transport );
			$elements->suitableDisabled = JHTML::_('select.booleanlist', "suitable_disabled" , '',$item->informations->suitable_disabled );
			//recommended
			$elements->recommended = JHTML::_('select.booleanlist', "recommended" , '',$item->recommended );
			$this->elements = $elements;
			
			$hoteltranslationsModel = new JHotelReservationModelhoteltranslations();
			$this->translations = $hoteltranslationsModel->getAllTranslations(HOTEL_TRANSLATION, $this->item->hotel_id);
			
			$this->addToolbar($this->item);
			$this->includeFunctions();
			
		parent::display($tpl);
	}
	function addToolbar($item){
	
		$canDo = JHotelReservationHelper::getActions();
	
		JRequest::setVar( 'hidemainmenu', 1);

		if ($canDo->get('core.create') && ($item->hotel_state==0 || isSuperUser(JFactory::getUser()->id))){
			JToolBarHelper::apply('hotel.apply');
			JToolBarHelper::save('hotel.save');
		}
		JToolBarHelper::cancel('hotel.cancel');
	}
	
	function includeFunctions(){
		$doc =JFactory::getDocument();
		
		$doc->addStyleSheet('components/'.getBookingExtName().'/assets/js/validation/css/validationEngine.jquery.css' );
		$doc->addStyleSheet('components/'.getBookingExtName().'/assets/js/validation/css/template.css' );
		$doc->addStyleSheet('components/'.getBookingExtName().'/assets/js/datepicker/css/datepicker.css');
		$doc->addStyleSheet('components/'.getBookingExtName().'/assets/js/datepicker/css/layout.css');
		
		
		$doc->addScript('components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
		$doc->addScript('components/'.getBookingExtName().'/assets/js/manageHotels.js' );
		$doc->addScript('components/'.getBookingExtName().'/assets/js/datepicker/js/eye.js' );
		$doc->addScript('components/'.getBookingExtName().'/assets/js/datepicker/js/datepicker.js' );
		$doc->addScript('components/'.getBookingExtName().'/assets/js/datepicker/js/utils.js' );
		$doc->addScript('components/'.getBookingExtName().'/assets/js/datepicker/js/layout.js' );
		$doc->addScript('components/'.getBookingExtName().'/assets/js/jquery.upload.js');
		$tag = JHotelUtil::getJoomlaLanguage();
		$doc->addScript('components/'.getBookingExtName().'/assets/js/validation/js/languages/jquery.validationEngine-'.$tag.'.js');
		$doc->addScript('components/'.getBookingExtName().'/assets/js/validation/js/jquery.validationEngine.js');
		
	}
}

