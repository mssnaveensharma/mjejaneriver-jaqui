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

if (!checkUserAccess(JFactory::getUser()->id,"hotel_ratings")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewManageHotelRatings extends JViewLegacy
{
	function display($tpl = null)
	{
		$hotels = $this->get('Hotels');
		$this->hotel_id = JRequest::getVar('hotel_id');
		if( $this->hotel_id  > 0 )
		{
			$this->hotelInfo = $this->get('Hotel');
		}
		
		JHotelReservationHelper::addSubmenu('reviews');
		
		$hotels = checkHotels(JFactory::getUser()->id,$hotels);
		$this->hotels =  $hotels;
		$this->items =  $this->get('HotelReviews');
		
		JToolBarHelper::title('J-HotelReservation :'.JText::_('LNG_MANAGE_HOTEL_RATINGS',true), 'generic.png' );
		if(!JRequest::getVar('layout')=='ratingsmenu'){
			JToolBarHelper::custom( 'managehotelratings.menuhotelratings', JHotelUtil::getDashBoardIcon(), 'home', 'Back',false, false );
			$this->addToolbar();
		}
		else
			JToolBarHelper::custom( 'managehotelratings.back', JHotelUtil::getDashBoardIcon(), 'home', 'Back',false, false );
			
			
		parent::display($tpl);

	}
	
	function addToolbar(){
		JToolBarHelper::custom( 'managehotelratings.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true),false, false );
	}
	
}