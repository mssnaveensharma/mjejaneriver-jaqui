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

class JHotelReservationControllerManageCurrencies extends JControllerLegacy
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
		$this->registerTask( 'save', 'save');
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('managecurrencies');
		
		$post = JRequest::get( 'post' );
		$post["currency_symbol"]= JRequest::getVar('currency_symbol', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_currencies', array('description' => $post['description'] ) , 'currency_id', $post['currency_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_CURRENCY_NAME_EXISTENT',true) );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies&task=add', $msg );
		}
		else if ($model->store($post)) 
		{
			$msg = JText::_('LNG_CURRENCY_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );
		} 
		else 
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_CURRENCY',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );	
		}

		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );
	}
	
	public function back(){
		$this->setRedirect('index.php?option='.getBookingExtName());
	}
	function delete()
	{
		$model = $this->getModel('managecurrencies');

		if ($model->remove()) {
			$msg = JText::_('LNG_CURRENCY_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_CURRENCY',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'managecurrencies' );
	
		parent::display(); 
		
	}
	
	function state()
	{
		$model = $this->getModel('managecurrencies');

		if ($model->state()) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_CURRENCY_STATE',true);
		}

	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managecurrencies&view=managecurrencies', $msg );
	}
}