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
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/logger.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'utils.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'userAccess.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';


//JHtmlBehavior::framework();


if(JHotelUtil::isJoomla3()){
	JHtml::_('jquery.framework', true, true); //load jQuery before other js
}else{
	if(!defined('J_JQUERY_LOADED')) {
		JHotelUtil::includeFile('script', 	'jquery.min.js', 'administrator/components/com_jhotelreservation/assets/js/');
		define('J_JQUERY_LOADED', 1);
	}
}

JHTML::_('script', 		'administrator/components/'.getBookingExtName().'/assets/js/utils.js');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/css/style.css');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/css/general.css');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/css/joomlatabs.css');

//JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/jquery-ui.min.js');

//JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/tooltip.js');
//JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/tooltip.css');
//JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/jquery-ui.css');

//JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/datepicker/css/datepicker.css');
//JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/datepicker/css/layout.css');

//JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/jquery.upload.js');

JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');

//JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/TabPane.css');
//JHTML::_('script', 		'administrator/components/'.getBookingExtName().'/assets/TabPane.js');
JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/js/common.js');

//JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/js/jquery.validate.min.js');
//JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/js/additional-methods.min.js');

JHotelUtil::loadAdminLanguage();
JHotelUtil::loadClasses();

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
		window.onload = function()	{
			jQuery.noConflict();
		};
		var baseUrl="'.(JURI::base().'index.php?option='.getBookingExtName()).'";
');


$controller	= JControllerLegacy::getInstance('JHotelReservation');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

