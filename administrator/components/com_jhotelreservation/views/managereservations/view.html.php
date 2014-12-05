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

if (!checkUserAccess(JFactory::getUser()->id,"manage_reservations")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'variables.php'; 
class JHotelReservationViewManageReservations extends JViewLegacy
{
	function display($tpl = null)
	{
		$post = JRequest::get( 'post' );
		if(count($post)==0)
			$post = JRequest::get( 'get' );
		if( isset( $post['filter_hotel_id'] ) )
			$this->filter_hotel_id =  $post['filter_hotel_id'];
		else
			$this->filter_hotel_id = "";
		
		if( isset( $post['filter_first_name'] ) )
			$this->filter_first_name =  $post['filter_first_name'];
		else
			$this->filter_first_name = "";
			
		if( isset( $post['filter_last_name'] ) )
			$this->filter_last_name =  $post['filter_last_name'];
		else
			$this->filter_last_name = '';
		
		
		if( isset( $post['filter_status_reservation'] ) )
			$this->filter_status_reservation =  $post['filter_status_reservation'];
		else
			$this->filter_status_reservation = 0;
		
		if( isset( $post['filter_room_types'] ) )
			$this->filter_room_types =  $post['filter_room_types'];
		else
			$this->filter_room_types = 0;
			
		if( isset( $post['filter_voucher'] ) )
			$this->filter_voucher =  $post['filter_voucher'];
		else
			$this->filter_voucher = '';
		
		if( 
			JRequest::getString( 'task') !='edit' 
			&& 
			JRequest::getString( 'task') !='add' 
			&& 
			JRequest::getString( 'task') !='info'
			&& 
			JRequest::getString( 'task') !='penalties' 
			&& 
			JRequest::getString( 'task') !='add' 
			&& 
			JRequest::getString( 'task') !='status' 
			&& 
			JRequest::getString( 'task') !='payments' 
			&& 
			JRequest::getString( 'task') !='assign_number_rooms' 
			
		) 
		{
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_MANAGE_RESERVATIONS',true), 'generic.png' );
			//JRequest::setVar( 'hidemainmenu', 1 );  
			JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home',JText::_('LNG_BACK',true), false, false );
			JToolBarHelper::deleteList(JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE',true), 'Delete', JText::_('LNG_DELETE',true), 'Delete button', false, false );
			//JToolBarHelper::editList(); 
			//JToolBarHelper::addNewX(); 
			
				
			$model			= $this->getModel(); 
			$model->checkStatusLate();
			$items			=& $model->getReservations($this->filter_hotel_id, $this->filter_first_name, $this->filter_last_name, $this->filter_status_reservation, $this->filter_room_types, $this->filter_voucher ); 
			$this->items =  $items; 
			
			$itemsStatus	= $this->get('StatusReservation'); 
			$this->itemsStatus =  $itemsStatus; 
			$itemsPayments	= $this->get('PaymentSettings'); 
			$this->itemsPayments =  $itemsPayments; 
			$itemsRoomTypes	= $this->get('RoomTypes');
			$this->itemsRoomTypes =  $itemsRoomTypes; 
			
			$itemPaymentProcessors		= $this->get('PaymentProcessors'); 
			$this->itemPaymentProcessors =  $itemPaymentProcessors; 
			$itemsHotel	= $this->get('Hotels');
			$itemsHotel = checkHotels(JFactory::getUser()->id,$itemsHotel);
			$this->itemsHotel =  $itemsHotel;
			
			//pagination
			$pagination = $this->get('Pagination');
			$this->pagination =  $pagination;
			
			$tpl = "list";
			
			
		}
		else if( JRequest::getString('task') =='info' )
		{
		
			$item					= $this->get('Data');
			$this->item =  $item; 
			
			JToolBarHelper::title(  'J-Hotel Reservation :'. JText::_('LNG_DETAILS_OF_RESERVATION',true).' : '.JHotelUtil::getStringIDConfirmation($item->confirmation_id), 'generic.png' );
		
			JRequest::setVar( 'hidemainmenu', 1 );  
			
			JToolBarHelper::custom( 'backInfo', 'home', 'home', 'Back', false, false );
			JToolBarHelper::custom( 'sendEmail', 'send.png', 'send.png', 'Send', false, false );
			$tpl = "info";
		}
		else if( 
			JRequest::getString('task') =='assign_number_rooms'
		)
		{
			$item						= $this->get('Data'); 
			$this->item =  $item;
		
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_ASSIGN_NUMBER_ROOM',true).' - '.strtolower(JText::_('LNG_RESERVATION',true)).' : '.JHotelUtil::getStringIDConfirmation($item->confirmation_id), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			if( $item->status_reservation_id != CANCELED_ID )
			{
				JToolBarHelper::cancel();
				JToolBarHelper::save(); 
			}
			else
			{
				JToolBarHelper::back(); 
			}
		}
		else if( JRequest::getString('task') =='edit')
		{
		/*
			$itemPaymentProcessors		= $this->get('PaymentProcessors'); 
			$this->itemPaymentProcessors =  $itemPaymentProcessors; 
		
			$modelVariables->load( $post['confirmation_id'], $post['email'], $modelVariables->itemCurrency);
			
			$itemsPayments				= $this->get('PaymentSettings'); 
			$this->itemsPayments =  $itemsPayments; 
			*/

			
			$item		= $this->get('Data'); 
			$this->item =  $item;
		
		
			JToolBarHelper::title(   'J-Hotel Reservation : '.( $item->confirmation_id > 0? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_RESERVATION',true).' : '.JHotelUtil::getStringIDConfirmation($item->confirmation_id), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 ); 
			JToolBarHelper::cancel();
			JToolBarHelper::save(); 
			
			$tpl = "edit";
		}
		else if( JRequest::getString('task') =='penalties' /*|| JRequest::getString('task') =='status'*/ )
		{
			$itemPaymentProcessors		= $this->get('PaymentProcessors'); 
			$this->itemPaymentProcessors =  $itemPaymentProcessors; 
		
			
			$itemsPayments				= $this->get('PaymentSettings'); 
			$this->itemsPayments =  $itemsPayments; 
			
			
			$itemModelVariables		= $this->get('Data'); 
			$this->itemModelVariables =  $itemModelVariables;
		
			JToolBarHelper::cancel();
			JToolBarHelper::apply(); 
	
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_APPLY_PENALTIES',true).' - '.strtolower(JText::_('LNG_RESERVATION',true)).' : '.JHotelUtil::getStringIDConfirmation($itemModelVariables->confirmation_id), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			
		}
		else if( JRequest::getString('task') =='add' )
		{
			/*
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_ADD_RESERVATION',true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );  
			
			JToolBarHelper::custom( 'backInfo', 'home', 'home', 'Back', false, false );
			*/
			//$this->setRedirect( 'index.php?option='.getBookingExtName()."&task=addreservations&view=addreservations", ''); 
		}
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
	
		$user		= JFactory::getUser();
		$isNew		= ($this->item->room_id == 0);
	
		JToolBarHelper::title(JText::_('LNG_ROOM',true)." : ".(!$isNew ? JText::_( "LNG_EDIT',true): JText::_( "LNG_ADD_NEW',true)), 'menu.png');
	
		JToolBarHelper::apply('room.apply');
		JToolBarHelper::save('room.save');
		JToolBarHelper::save2new('room.save2new');
		JToolBarHelper::custom('room.editrateprices', 'stats', 'stats', 'JTOOLBAR_EDIT_RATE_DETAILS',false);
		JToolBarHelper::cancel('room.cancel');
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_ROOM_EDIT');
	}
}