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

class JHotelReservationControllerManageInvoices extends JControllerLegacy
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
		$this->registerTask( 'backToInvoice', 'cancel');
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	}


	function createMontlyInvoices(){
		$model = $this->getModel('manageinvoices');
		$model->createMonthlyInvoices();
		exit;
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('manageinvoices');

		$post = JRequest::get( 'post' );

		if ($model->store($post))
		{
			$msg = JText::_('LNG_INVOICE_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageinvoices&view=manageinvoices&hotel_id='.$post['hotel_id'], $msg );
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_INVOICE',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageinvoices&view=manageinvoices&hotel_id='.$post['hotel_id'], '' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
	}

	function send(){
		$model = $this->getModel('manageinvoices');
		
		$post = JRequest::get( 'post' );
		
		if ($model->sendInvoice($post))
		{
			$msg = JText::_('LNG_INVOICE_ISSUED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageinvoices&view=manageinvoices&hotel_id='.$post['hotel_id'], $msg );
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_ISSUE_INVOICE',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageinvoices&view=manageinvoices&hotel_id='.$post['hotel_id'], '' );
		}
	}
	
	function issueInvoices(){
		$model = $this->getModel('manageinvoices');
		$model->issueInvoices();
		exit;
	}
	
	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		if(JRequest::getVar('task')=='cancel')
			$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageinvoices&view=manageinvoices&hotel_id='.$post['hotel_id'], $msg );
	}

	
	function edit()
	{
		JRequest::setVar( 'view', 'manageinvoices' );

		parent::display();
	}
}