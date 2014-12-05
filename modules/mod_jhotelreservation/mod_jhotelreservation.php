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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once(JPATH_SITE.'/components/com_jhotelreservation/classes/services/UserDataService.php' );
require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/defines.php';
require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php'; 

// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );

if(JHotelUtil::isJoomla3()){
	JHtml::_('jquery.framework', true, true); //load jQuery before other js
	JHtml::_('behavior.framework');

}else{
	if(!defined('J_JQUERY_LOADED')) {
		JHTML::_('script','components/com_jhotelreservation/assets/js/jquery.min.js');
		JHTML::_('script','components/com_jhotelreservation/assets/js/jquery-ui.min.js');
		define('J_JQUERY_LOADED', 1);
	}
}

jimport( 'joomla.session.session' );
JHTML::_('script', 'administrator/components/com_jhotelreservation/assets/js/utils.js');
JHTML::_('script', 'components/com_jhotelreservation/assets/js/search.js');
JHTML::_('script', 'components/com_jhotelreservation/assets/js/jhotelreservationcalendar.js');

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
		window.onload = function()	{
			jQuery.noConflict();
		};  
	');

JHTML::_('script', 'components/com_jhotelreservation/assets/js/jquery.blockUI.js');
JHTML::_('stylesheet', 	'http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
$title 			= modJHotelReservationHelper::getTitle( $params );
$css_style		= modJHotelReservationHelper::getCSS_Style( $params );
$getHotelItems	= modJHotelReservationHelper::getHotelItems( );
$hotels = modJHotelReservationHelper::getHotelItems();

$language 		= JFactory::getLanguage();

JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/general.css');
JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/bootstrap.css');

if(isset($_SESSION['cssStyle'])){
	JHTML::_('stylesheet', 'modules/mod_jhotelreservation/assets/'.$_SESSION['cssStyle']);
}else{
	JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/'.$css_style);
}

$language = JFactory::getLanguage();
$language_tag 	= $language->getTag();

$x = $language->load('com_jhotelreservation' ,dirname(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jhotelreservation'. DS.'language'), 
		$language_tag,true);


$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

$post			= JRequest::get( 'post' );
$userData =  UserDataService::getUserData();

$startDate = $params->get('start-date');
$endDate = $params->get('end-date');

//create dates & default values
$jhotelreservation_datas = JRequest::getVar('jhotelreservation_datas');
if( strlen($jhotelreservation_datas)==0 )
{
	if(
	JRequest::getVar('year_start') != ''
	&&
	JRequest::getVar('month_start') != ''
	&&
	JRequest::getVar('day_start') != ''
	)
	{
		$jhotelreservation_datas = JRequest::getVar('year_start').'-';
		$jhotelreservation_datas .= strlen(JRequest::getVar('month_start'))>1	? JRequest::getVar('month_start') 	: ("0".JRequest::getVar('month_start'));
		$jhotelreservation_datas .= '-';
		$jhotelreservation_datas .= strlen(JRequest::getVar('day_start'))>1		? JRequest::getVar('day_start') 	: ("0".JRequest::getVar('day_start'));
	}else if(isset($startDate)){
		$jhotelreservation_datas = $params->get('start-date');		
		if(strtotime($jhotelreservation_datas) < strtotime(date("Y-m-d"))){
			$jhotelreservation_datas = date("Y-m-d");
		}
	}
	else if(isset($userData->start_date)){	
		$jhotelreservation_datas = $userData->start_date;
	}else{	
		$jhotelreservation_datas = date('Y-m-d');
	}
}
$jhotelreservation_datas = JHotelUtil::convertToFormat($jhotelreservation_datas);
$jhotelreservation_datae = JRequest::getVar('jhotelreservation_datae');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
if( !isset($jhotelreservation_datae) || strlen($jhotelreservation_datae)==0)
{
	if(
	JRequest::getVar('year_end') != ''
	&&
	JRequest::getVar('month_end') != ''
	&&
	JRequest::getVar('day_end') != ''
	)
	{
		$jhotelreservation_datae = JRequest::getVar('year_end').'-';
		$jhotelreservation_datae .= strlen(JRequest::getVar('month_end'))>1	? JRequest::getVar('month_end') 	: ("0".JRequest::getVar('month_end'));
		$jhotelreservation_datae .= '-';
		$jhotelreservation_datae .= strlen(JRequest::getVar('day_end'))>1	? JRequest::getVar('day_end') 	: ("0".JRequest::getVar('day_end'));
	}else if(isset($endDate) && strlen($endDate)>0){
		
		$jhotelreservation_datae = $params->get('end-date');		
		if(strtotime($jhotelreservation_datae) < strtotime(date("Y-m-d"))){			
			$jhotelreservation_datae = date("Y-m-d");		
		}		
	}
	else if(isset($userData->end_date)){	
		$jhotelreservation_datae = $userData->end_date;
	}else{	
		$jhotelreservation_datae = date('Y-m-d', strtotime( ' + 1 day '));
	}
}
$jhotelreservation_datae = JHotelUtil::convertToFormat($jhotelreservation_datae);
$jhotelreservation_rooms 		= JRequest::getVar('jhotelreservation_rooms');
$jhotelreservation_guest_adult 	= JRequest::getVar('jhotelreservation_guest_adult');
$jhotelreservation_guest_child	= JRequest::getVar('jhotelreservation_guest_child');
$jhotelreservation_hotel_id		= JRequest::getVar('jhotelreservation_hotel_id');

//$getHotelItems= JRequest::getVar('getHotelItems');
// var_dump($getHotelItems);

if( strlen($jhotelreservation_rooms)==0 )
{
	if( JRequest::getVar('rooms') != '' )
		$jhotelreservation_rooms		= JRequest::getVar('rooms');
	else
		$jhotelreservation_rooms		= 1;
}

if( strlen($jhotelreservation_guest_adult)==0 )
{
	if( JRequest::getVar('guest_adult') != '' )
		$jhotelreservation_guest_adult	= JRequest::getVar('guest_adult');
	else 
		$jhotelreservation_guest_adult	= 2;
}

if( strlen($jhotelreservation_guest_child)==0 )
{
	if( JRequest::getVar('guest_child') != '' )
		$jhotelreservation_guest_child		= JRequest::getVar('guest_child');
	else 
		$jhotelreservation_guest_child = 0;
}

if(isset($userData->total_adults))
	$jhotelreservation_guest_adult = $userData->total_adults;
if(isset($userData->total_children))
$jhotelreservation_guest_child = $userData->total_children;

if(isset($userData->rooms))
	$jhotelreservation_rooms = $userData->rooms;



//dmp($params->get('layout-type'));
require( JModuleHelper::getLayoutPath( 'mod_jhotelreservation',  'default-'.$params->get('layout-type', 'default-vertical')));
?>