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



class JHotelReservationViewManageEmailsDefault extends JViewLegacy
{
	function display($tpl = null)
	{
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
		) 
		{
			JToolBarHelper::title(  'J-Hotel Reservation : '. JText::_('LNG_MANAGE_EMAILS_DEFAULT',true), 'generic.png' );
			JToolBarHelper::custom( 'manageemails.show', JHotelUtil::getDashBoardIcon(), 'home', 'Back', false, false );
			JToolBarHelper::editList('manageemailsdefault.edit');
			
			$items		= $this->get('Datas'); 
			
			$this->items =  $items; 
			$this->hoteltranslationsModel = new JHotelReservationModelhoteltranslations();
				
		}
		else 
		{
		
			$item				= $this->get('Data'); 
			$this->item =  $item; 
			$hoteltranslationsModel = new JHotelReservationModelhoteltranslations();
			$this->translations = $hoteltranslationsModel->getAllTranslations(EMAIL_TEMPLATE_TRANSLATION, $this->item->email_default_id);
				
			JToolBarHelper::title(   'J-Hotel Reservation : '.( $item->email_default_id > 0 ? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_EMAIL',true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::apply('manageemailsdefault.apply');
			JToolBarHelper::save('manageemailsdefault.save');
			JToolBarHelper::cancel('manageemailsdefault.cancel');
								
		}
		parent::display($tpl);
	}
}