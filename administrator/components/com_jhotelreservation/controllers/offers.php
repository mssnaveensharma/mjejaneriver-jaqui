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
JHTML::_('script', 						'administrator/components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/js/offers.js');

class JHotelReservationControllerOffers extends JControllerLegacy
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
		if( JRequest::getVar('is_error')=="1" && JRequest::getVar('task')=="save")
		{
			JRequest::setVar( 'view', 'offers' ); 
			//$this->display();
		}
		if(JRequest::getVar('task')!="back")
			JRequest::setVar( 'view', 'offers' );
		
		$this->registerTask( 'apply', 'save');
		$this->registerTask( 'saveAsNew', 'save');
	}

	function delete()
	{
		$model = $this->getModel('offers');
			
		if ($model->remove()) {
			$msg = JText::_( 'LNG_OFFER_HAS_BEEN_DELETED' ,true);
		} else {
			
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers&hotel_id='.$model->getState('filter.hotel_id'), $msg );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'offers' );
		parent::display(); 
	}
	
	function state()
	{
		
		$model = $this->getModel('offers');
		if ($model->state()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_( 'LNG_ERROR_CHANGE_OFFER_STATE' ,true);
		}

	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers&hotel_id='.$model->getState('filter.hotel_id'), $msg );
	}
	
	
	function changeFeaturedState(){
		$model = $this->getModel('offers');
	
		if ($model->changeFeaturedState()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
		}
		
		$this->setMessage($msg);
		
		//JRequest::setVar( 'view', 'offers' );
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers&hotel_id='.$model->getState('filter.hotel_id'), $msg );
	}
	
	function changeTopState(){
		$model = $this->getModel('offers');
	
		if ($model->changeTopState()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
		}
	
		$this->setMessage($msg);
	
		//JRequest::setVar( 'view', 'offers' );
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers&hotel_id='.$model->getState('filter.hotel_id'), $msg );
	}
	
	function offer_order()
	{
		$model = $this->getModel('offers');
	    return $model->changeOfferOrder();
	}
	
	function getOfferContent(){
		$model = $this->getModel('offers');
		$content= $model->getOfferContent();
		echo $content; 
		exit; 
	}
	
	function getWarningContent(){
		$model = $this->getModel('offers');
		$content= $model->getWarningContent();
		echo $content;
		exit;
	}
	
}