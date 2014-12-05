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

if (!checkUserAccess(JFactory::getUser()->id,"manage_arrival_options")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewManageArrivalOptions extends JViewLegacy
{
	function display($tpl = null)
	{
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
		) 
		{
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_MANAGE_ARRIVAL_OPTIONS',true), 'generic.png' );
			//JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home', 'Back', false, false );
			$hotel_id =  $this->get('HotelId'); 
			if( $hotel_id > 0 )
			{
				JToolBarHelper::deleteList( JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE',true), 'Delete', 'Delete', 'Detele button', false, false );
				JToolBarHelper::editList();
				JToolBarHelper::addNewX(); 
			}
			$this->hotel_id =  $hotel_id; 
			$items		= $this->get('Datas'); 
			$this->items =  $items; 
			
			$hotels		= $this->get('Hotels'); 
			$hotels = checkHotels(JFactory::getUser()->id,$hotels);
				
			$this->hotels =  $hotels; 
			
		}
		else
		{
			$item				= $this->get('Data'); 
			$this->item =  $item; 

			$hotel_id =  $this->get('HotelId'); 
			$this->hotel_id =  $hotel_id; 
			
			$hotel		= $this->get('Hotel'); 
			$this->hotel =  $hotel; 
		
			JToolBarHelper::title(    'J-Hotel Reservation : '.( $item->arrival_option_id > 0? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_MANAGE_ARRIVAL_OPTION',true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::cancel();
			JToolBarHelper::save(); 
		}
		parent::display($tpl);
	}
}