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

class JHotelReservationControllerManageRoomFeatures extends JControllerLegacy
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
		$model = $this->getModel('manageroomfeatures');

		$post = JRequest::get( 'post' );
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_room_features', array('feature_name' => $post['feature_name'] ) , 'feature_id', $post['feature_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_FEATURE_ROOM_NAME_EXISTENT',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures&task=add', $msg );
		}
		else if ($model->store($post))
		{
			$msg = JText::_('LNG_FEATURE_ROOM_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures', $msg );
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_FEATURE_ROOM',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures', $msg );
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
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures', $msg );
	}

	function delete()
	{
		$model = $this->getModel('manageroomfeatures');

		if ($model->remove()) {
			$msg = JText::_('LNG_FEATURE_ROOM_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_FEATURE_ROOM',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway

		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures', $msg );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'manageroomfeatures' );

		parent::display();

	}

}