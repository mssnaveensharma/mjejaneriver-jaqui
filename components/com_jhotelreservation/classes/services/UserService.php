<?php

class UserService{
	
	public static function isUserLoggedIn($retunUrl=null){
		
		$user = JFactory::getUser();
		if(!$user->id){
			$app = JFactory::getApplication();
			$msg =  JText::_('LNG_UNAUTHORIZED_ACCESS',true);
			if($retunUrl==null)
				$retunUrl = base64_encode(JRoute::_( 'index.php?option='.getBookingExtName().'&view=customeraccount'));
			$app->redirect( JRoute::_( "index.php?option=com_users&view=login&return=".$retunUrl), $msg );
		}
		else
			return true;
	}
	
	public static function getUserByEmail($email){
		$usersTable = JTable::getInstance('Users','Table', array());
		return $usersTable->getUserByEmail($email);
	}
	
	public static function generatePassword($text, $is_cripted = false)
	{
		$password 	=  $text;   
		if( $is_cripted ==false )
			return $password;
		jimport('joomla.user.helper');
		$salt 		= JUserHelper::genRandomPassword(8);  
		$crypt 		= JUserHelper::getCryptedPassword($password, $salt);  
		$password 	= $crypt.":".$salt;  
		return $password;
	}
	
	
}