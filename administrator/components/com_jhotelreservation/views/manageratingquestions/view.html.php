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



class JHotelReservationViewManageRatingQuestions extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->items =  $this->get('ReviewQuestions');
		$this->item =  $this->get('ReviewQuestion');
		if(JRequest::getVar('layout')=="edit"){
			$this->editToolbar();
		}
		else{ 
			$this->addToolbar();
		}
		parent::display($tpl);
	}
	
	function addToolbar(){
		JToolBarHelper::title(  'J-HotelReservation :'.JText::_('LNG_MANAGE_RATING_QUESTIONS',true), 'generic.png' );
		JToolBarHelper::addNew('managehotelratings.editratingquestion','New');
		JToolBarHelper::editList('managehotelratings.editratingquestion','Edit');
		JToolBarHelper::deleteList( '', 'managehotelratings.deleteratingquestions', JText::_('LNG_DELETE',true));
		
		JToolBarHelper::custom( 'managehotelratings.menuhotelratings', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_BACK',true),false, false );
		JToolBarHelper::custom( 'managehotelratings.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true),false, false );
	}

	function editToolbar(){
		JToolBarHelper::title(  'J-HotelReservation :'.JText::_('LNG_MANAGE_RATING_QUESTIONS',true), 'generic.png' );
		JToolBarHelper::save( 'managehotelratings.saveratingquestion');
		JToolBarHelper::custom( 'managehotelratings.manageratingquestions', 'cancel.png', 'cancel.png', 'Cancel',false, false );
	}
	
}