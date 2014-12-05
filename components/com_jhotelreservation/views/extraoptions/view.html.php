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

class JHotelReservationViewExtraOptions extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->extraOptions = $this->get("ExtraOptions");
		
		$this->state = $this->get('State');
		
		$this->userData =  UserDataService::getUserData();
		
		$this->hotel = HotelService::getHotel($this->userData->hotelId);
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		//dmp($this->userData);
		
		parent::display($tpl);
	}
}
?>
