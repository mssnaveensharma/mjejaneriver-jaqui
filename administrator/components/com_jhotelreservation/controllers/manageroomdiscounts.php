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


class JHotelReservationControllerManageRoomDiscounts extends JControllerLegacy
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
		$model = $this->getModel('manageroomdiscounts');

		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
		$post['hotel_id'] = 0;

		$post['discount_room_ids']	= implode(',', $post['discount_room_ids']);
		$post['offer_ids']	= implode(',', $post['offer_ids']);
		$post['excursion_ids']	= implode(',', $post['excursion_ids']);
		
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_discounts', array('hotel_id' => $post['hotel_id'] , 'discount_name' => $post['discount_name'] ) , 'discount_id', $post['discount_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_DISCOUNT_PERIOD_INTERSECT',true) );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomdiscounts&view=manageroomdiscounts&task=add&hotel_id='.$post['hotel_id'], '' );
		}
		else if ($model->store($post))
		{
			$msg = JText::_('LNG_DISCOUNT_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomdiscounts&view=manageroomdiscounts&hotel_id='.$post['hotel_id'], $msg );
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_DISCOUNT',true) );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomdiscounts&view=manageroomdiscounts&hotel_id='.$post['hotel_id'], '' );
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
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomdiscounts&view=manageroomdiscounts&hotel_id='.$post['hotel_id'], $msg );
	}

	function delete()
	{
		$model = $this->getModel('manageroomdiscounts');
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
		$post['hotel_id'] = 0;

		if ($model->remove()) {
			$msg = JText::_('LNG_DISCOUNT_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_DISCOUNT',true);
		}
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&view=manageroomdiscounts&hotel_id='.$post['hotel_id'], $msg );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'manageroomdiscounts' );

		parent::display();

	}

	function state()
	{
		$model = $this->getModel('manageroomdiscounts');
		$get = JRequest::get( 'get' );
		if( !isset($get['hotel_id']) )
		$get['hotel_id'] = 0;
		// dmp($post);
		// exit;
		if ($model->state()) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_DISCOUNT_STATE',true);
		}


		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomdiscounts&view=manageroomdiscounts&hotel_id='.$get['hotel_id'], $msg );
	}
	
	function updateOffers()
	{

		$roomIds = $_POST['roomIDs'];
		$offerIds = $_POST['offerIDs'];

		$model = $this->getModel('manageroomdiscounts');
		$buff =  $model->getHTMLContentOffers($roomIds,$offerIds);
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="0"  content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
}