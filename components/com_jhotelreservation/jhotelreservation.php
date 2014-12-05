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
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/logger.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'utils.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'userAccess.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'hoteltranslations.php';
require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'confirmation.php';


$appSettings =JHotelUtil::getInstance()->getApplicationSettings();
JRequest::setVar('show_price_per_person', $appSettings->show_price_per_person);

JHTML::_('script', 'administrator/components/'.getBookingExtName().'/assets/js/utils.js');

if(JHotelUtil::isJoomla3()){
	JHtml::_('jquery.framework', true, true);
	JHtml::_('behavior.framework');
	define('J_JQUERY_LOADED', 1);
}else{
	if(!defined('J_JQUERY_LOADED')) {
		JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/jquery.min.js');
		define('J_JQUERY_LOADED', 1);
	}
}
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/css/bootstrap.css');
JHTML::_('script',	'components/'.getBookingExtName().'/assets/js/jquery-ui.min.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');

JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/css/jquery-ui.css');
JHTML::_('script',		'administrator/components/'.getBookingExtName().'/assets/js/image.js');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/css/tabs.css');
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/css/general.css');

$doc =JFactory::getDocument();
$doc->addScriptDeclaration('
		window.onload = function()	{
		jQuery.noConflict();
		};
		var baseUrl="'.(JRoute::_('index.php?option=com_jhotelreservation')).'";
');

if( isset($_SESSION['cssStyleComp'] ) ){
	JHTML::_('stylesheet', $_SESSION['cssStyleComp'], 	'components/'.getBookingExtName().'/assets/css/');
}else if( isset($appSettings->css_style) ){
	JHTML::_('stylesheet', $appSettings->css_style, 	'components/'.getBookingExtName().'/assets/css/');
}else{
	JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/css/style.css');
}

//setting menu item Id
$session = JFactory::getSession();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$activeMenu = $app->getMenu()->getActive();

if (!empty($activeMenu) && $activeMenu != $menu->getDefault()) {
	$menuId = $activeMenu->id;
	$session->set('menuId', $menuId);
}

$menuId = $session->get('menuId');
if(!empty($menuId)){
	JFactory::getApplication()->getMenu()->setActive($menuId);
}

$task = JRequest::getCmd('task');
$task = trim($task);
$view = JRequest::getCmd('view');
$view = trim($view);
if(empty($task) && empty($view)){
	return;
}
//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
//$log->LogDebug("-------Start execution--------task: ".$task."--------view:".$view);

JHotelUtil::loadSiteLanguage();
JHotelUtil::loadClasses();
if($task!="hotel.getRoomCalendars" && $task!="hotel.checkReservationPendingPayments")
	UserDataService::initializeUserData();


if( strpos($_SERVER['REQUEST_URI'],"buckarooautomaticresponse") ){
	$task = "paymentoptions.processAutomaticResponse";
	JRequest::setVar( 'task', $task);
	JRequest::setVar( 'processor', "buckaroo");
}

//$log->LogDebug($task);
$controller	= JControllerLegacy::getInstance('JHotelReservation');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

