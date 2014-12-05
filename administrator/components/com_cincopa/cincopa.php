<?php
/**
 * @version		$Id: cincopa.php $
 * @copyright	Copyright (C) 2010 Oren Shmulevich. All rights reserved.
 * @license		GNU/GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

define('DS',DIRECTORY_SEPARATOR);

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// import joomla controller library
jimport('joomla.application.component.controller');

// Create the controller
$controller = JControllerLegacy::getInstance('Cincopa');

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
