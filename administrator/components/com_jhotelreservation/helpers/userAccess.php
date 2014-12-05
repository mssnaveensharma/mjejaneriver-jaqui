<?php
/**
 * @copyright	Copyright (C) 2009-2011 CMSJunkie - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

function checkUserAccess($userId,$permissionCode){

	//check if user is super user	
	if (isSuperUser($userId))
		return true;

	$db = JFactory::getDBO();
	$query = "SELECT e.code 
			  FROM  #__users a,
					#__hotelreservation_user_group_mapping b,
					#__hotelreservation_group_role_mapping c,
					#__hotelreservation_role_permission_mapping d,
					#__hotelreservation_permissions e
				WHERE a.id=b.user_id
					and b.group_id = c.group_id
					and c.role_id = d.role_id
					and d.permission_id = e.id
					and a.id=".$userId;
	$db->setQuery( $query );
	$userPermissions = $db->loadColumn();
	if(is_array($userPermissions) && in_array($permissionCode,$userPermissions)>0)
		return true;
	else return false;	
}

function isSuperUser($userId){
	$user	= JFactory::getUser();
	$isroot	= $user->get('isRoot');
	return $isroot;
}

function isManager(){
	$user =& JFactory::getUser();
	$groups = isset($user->groups) ? $user->groups : array();
	return in_array(6,$groups);
}

function checkHotels($userId,$hotels){
	
	if (isSuperUser($userId))
		return $hotels;

	
	$db = JFactory::getDBO();
	$query = "SELECT b.hotel_id
				  FROM  #__users a,
						#__hotelreservation_user_hotel_mapping b
					WHERE a.id=b.user_id
						  and a.id=".$userId;
	$db->setQuery( $query );
	$userHotels = $db->loadColumn();

	if (count($userHotels)==0) return null;

	for($i=0,$a=count($hotels);$i<$a;$i++){
		$hotel = $hotels[$i];
		if(!in_array($hotel->hotel_id,$userHotels)>0){
			unset($hotels[$i]);
		}

	}
	return $hotels;
}
function showRestricted(){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
?>

