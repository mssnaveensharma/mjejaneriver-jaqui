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

if (!checkUserAccess(JFactory::getUser()->id,"manage_room_features")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewManageRoomFeatures extends JViewLegacy
{
	function display($tpl = null)
	{
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
		) 
		{
			JToolBarHelper::title(   'J-Hotel Reservation'.JText::_('LNG_MANAGE_ROOM_FEATURES',true), 'generic.png' );
			// JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home', 'Back',false, false );
			JToolBarHelper::deleteList(JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE',true), 'Delete', 'Delete', 'Delete button', false, false );
			JToolBarHelper::editList();
			JToolBarHelper::addNewX(); 

			
			$items		= $this->get('Datas'); 
			$this->items =  $items; 
			
		}
		else
		{
			$item							= $this->get('Data'); 
			$this->item =  $item; 
			
			$itemsRoomFeatureOptions 		= $this->get('FeatureRoomOptions'); 
			
			$this->itemsRoomFeatureOptions =  $itemsRoomFeatureOptions; 
			
			JToolBarHelper::title(   'J-Hotel Reservation : '.( $item->feature_id > 0?  JText::_('LNG_EDIT',true) : JText::_('LNG_ADD_NEW" ,true) ).' '.JText::_('LNG_FEATURE_ROOM',true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::cancel();
			JToolBarHelper::save(); 
		}
		parent::display($tpl);
	}
}