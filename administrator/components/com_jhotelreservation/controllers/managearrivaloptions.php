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

class JHotelReservationControllerManageArrivalOptions extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');  
		$this->registerTask( 'add', 'edit');
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('managearrivaloptions');

		$post = JRequest::get( 'post' );
		$post['arrival_option_description'] = JRequest::getVar('arrival_option_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
 
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_arrival_options', array('hotel_id' => $post['hotel_id'], 'arrival_option_name' => $post['arrival_option_name'] ) , 'arrival_option_id', $post['arrival_option_id'] ) )
		{
			$msg = JText::_('LNG_ARRIVAL_OPTION_NAME_EXISTENT',true);
			JError::raiseWarning( 500, $msg );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&task=add&hotel_id='.$post['hotel_id'], $msg );
		}
		else if ($model->store($post)) 
		{
			$msg = JText::_('LNG_ARRIVAL_OPTION_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&hotel_id='.$post['hotel_id'], $msg );
		} 
		else 
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_ARRIVAL_OPTIONS',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&hotel_id='.$post['hotel_id'], $msg );	
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0; 
		
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function delete()
	{
		$model = $this->getModel('managearrivaloptions');

		if ($model->remove()) {
			$msg = JText::_('LNG_ARRIVAL_OPTION_HAS_BEEN_DELETED',true);
		} else {
			
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0; 
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'managearrivaloptions' );
	
		parent::display(); 
		
	}
	
	function state()
	{
		$model = $this->getModel('managearrivaloptions');

		if ($model->state()) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_ARRIVAL_OPTION_STATE',true);
		}

		$get = JRequest::get( 'get' );
		if( !isset($get['hotel_id']) )
			$get['hotel_id'] = 0; 
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&hotel_id='.$get['hotel_id'], $msg );
	}
}