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

if (!checkUserAccess(JFactory::getUser()->id,"manage_rooms")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}


class JHotelReservationViewRooms extends JViewLegacy
{
	
	protected $items;
	protected $pagination;
	protected $state;
	protected $hotels;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$lang 		= JFactory::getLanguage();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
	
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);
		//var_dump($this->hotels);
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	
		parent::display($tpl);
		$this->addToolbar();
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{		
		$canDo = JHotelReservationHelper::getActions();
		
	
		JToolBarHelper::title(JText::_('LNG_MANAGE_ROOMS',true), 'menumgr.png');
	
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('room.add');
			JToolBarHelper::editList('room.edit');
		}
		JToolBarHelper::divider();
		JToolBarHelper::publish('rooms.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('rooms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'rooms.delete', 'JTOOLBAR_DELETE');
		}
		JToolBarHelper::custom( 'hotels.back', JHotelUtil::getDashboardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
		
		JToolBarHelper::divider();
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jhotelreservation');
		}
	
		JToolBarHelper::help('JHELP_ROOM_MANAGER');
		JHotelReservationHelper::addSubmenu('rooms');
		
	}
}