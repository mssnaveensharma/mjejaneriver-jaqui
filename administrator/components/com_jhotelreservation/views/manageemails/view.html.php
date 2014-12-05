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



if (!checkUserAccess(JFactory::getUser()->id,"manage_email_templates")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

class JHotelReservationViewManageEmails extends JViewLegacy
{
	function display($tpl = null)
	{
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
		) 
		{
			JToolBarHelper::title(  'J-Hotel Reservation : '. JText::_('LNG_MANAGE_EMAILS_TEMPLATES',true), 'generic.png' );
			$hotel_id =  $this->get('HotelId'); 
			JHotelReservationHelper::addSubmenu('emailtemplates');
			
			if( $hotel_id > 0 )
			{
				JToolBarHelper::addNew('manageemails.edit'); 
				JToolBarHelper::editList('manageemails.edit');
				JToolBarHelper::deleteList( '', 'manageemails.delete', JText::_('LNG_DELETE',true));
				
			}
			JToolBarHelper::custom( 'manageemailsdefault.show', JHotelUtil::getEmailDefaultIcon(), 'home', JText::_('LNG_MANAGE_EMAILS_DEFAULT'), false, false );
			JToolBarHelper::custom( 'manageemails.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
				
			$this->hotel_id =  $hotel_id; 
			
			$items		= $this->get('Datas'); 
			$this->items =  $items; 
			$this->hoteltranslationsModel = new JHotelReservationModelhoteltranslations();
				
			
			$hotels		= $this->get('Hotels'); 
			$hotels = checkHotels(JFactory::getUser()->id,$hotels);
				
			$this->hotels =  $hotels; 
			
			
		}
		else 
		{
			$item = $this->get('Data'); 
			$this->item =  $item; 
			
			$hotel_id =  $this->get('HotelId'); 
			$this->hotel_id =  $hotel_id; 
			
			$hotel		= $this->get('Hotel'); 
			$this->hotel =  $hotel;

			$hoteltranslationsModel = new JHotelReservationModelhoteltranslations();
			$this->translations = $hoteltranslationsModel->getAllTranslations(EMAIL_TEMPLATE_TRANSLATION, $this->item->email_id);
		
			JToolBarHelper::title(   'J-Hotel Reservation : '.( $item->email_id > 0? JText::_('LNG_EDIT',true) : JText::_('LNG_ADD_NEW',true)).' '.JText::_('LNG_EMAIL',true) , 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::apply('manageemails.apply');
			JToolBarHelper::save('manageemails.save');
			JToolBarHelper::cancel('manageemails.cancel');
		}
		parent::display($tpl);
	}
}