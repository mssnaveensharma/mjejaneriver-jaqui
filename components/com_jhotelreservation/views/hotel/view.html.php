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

jimport( 'joomla.application.component.view');

JHTML::_('stylesheet', 'administrator/components/'.getBookingExtName().'/assets/tabs.css');
JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/css/hotel_gallery.css');
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/jquery.opacityrollover.js');
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/jquery.galleriffic.js');

class JHotelReservationViewHotel extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->hotel = $this->get("Item");
		$this->state = $this->get('State');
		
		$this->offers = $this->get("Offers");
		$this->rooms = $this->get("Rooms");
		
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		if($this->appSettings->is_enable_reservation==0){
			JHotelUtil::getInstance()->showUnavailable();
		}
		$this->userData =  UserDataService::getUserData();
		$this->currencies = CurrencyService::getAllCurrencies();
		//dmp($this->userData);
		
		parent::display($tpl);
	}
}
?>
