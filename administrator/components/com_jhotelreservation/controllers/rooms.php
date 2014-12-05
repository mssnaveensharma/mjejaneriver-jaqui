<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 CMSJunkie,  All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.controlleradmin' );


/**
 * The Contest List Controller
 *
 */
class JHotelReservationControllerRooms extends JControllerAdmin
{
	
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}
	/**
	 * Display the view
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 */
	public function getModel($name = 'Room', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Removes an room
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('LNG_NO_ROOM_SELECTED',true));
		} else {
			// Get the model.
			$model = $this->getModel();
			
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			
			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} else {
				$this->setMessage(JText::plural('LNG_NR_ROOMS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jhotelreservation&view=rooms');
	}
	
	
	function changeState()
	{
		$model = $this->getModel();
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$cid = $cid[0];
		if ($model->changeState($cid))
		{
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_ROOM_STATE',true);
		}
	
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=rooms');
	}
	
	/**
	 * Change display on front state
	 * 
	 */ 
	function changeFrontState()
	{
		$model = $this->getModel();
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$cid = $cid[0];
		 
		if ($model->changeFrontState($cid))
		{
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_ROOM_STATE',true);
		}
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=rooms');
	}
	
	function room_order()
	{
		$model = $this->getModel("rooms");
		return $model->changeRoomOrder();
	}
}
