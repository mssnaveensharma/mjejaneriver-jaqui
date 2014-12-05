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

class JHotelReservationControllerManageHotelUsers extends JControllerLegacy
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
		$this->registerTask( 'exportListAsCSV', 'exportListAsCSV');
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('managehotelusers');

		$post = JRequest::get( 'post' );
		if ($model->store($post)) 
		{
			$msg = JText::_('LNG_USER_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managehotelusers&view=managehotelusers&hotel_id='.$post['hotel_id'], $msg );
		} 
		else 
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_USER',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managehotelusers&view=managehotelusers&hotel_id='.$post['hotel_id'], '' );	
		}

		// Check the table in so it can be edited.... we are done with it anyway
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managehotelusers&view=managehotelusers&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function delete()
	{
		$model = $this->getModel('managehotelusers');
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
			
		if ($model->remove()) {
			$msg = JText::_('LNG_USER_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_USER',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managehotelusers&view=managehotelusers&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'managehotelusers' );
		parent::display(); 
	}
	
	function exportListAsCSV(){
		$model = $this->getModel('managehotelusers');
		$model->exportListAsCSV();
		die(); // no need to send anything else
		
		parent::display($tpl);
	}
}