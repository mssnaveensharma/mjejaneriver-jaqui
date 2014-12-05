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

if (!checkUserAccess(JFactory::getUser()->id,"currency_settings")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewManageCurrencies extends JViewLegacy
{
	function display($tpl = null)
	{
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
		) 
		{
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_CURRENCY_SETTINGS',true), 'generic.png' );
			// JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::addNew('managecurrencies.edit'); 
			JToolBarHelper::editList('managecurrencies.edit');
			JToolBarHelper::deleteList( '', 'managecurrencies.delete', JText::_('LNG_DELETE',true));
			JToolBarHelper::custom( 'managecurrencies.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
			$items		= $this->get('Datas'); 
			$this->items =  $items; 
		}
		else
		{
			$item				= $this->get('Data'); 
			$this->item =  $item; 
		
			JToolBarHelper::title(   'J-Hotel Reservation : '.( $item->currency_id > 0? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_CURRENCY',true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::cancel('managecurrencies.back');
			JToolBarHelper::save('managecurrencies.save'); 
		}
		parent::display($tpl);
	}
}