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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model'); 
require_once "hoteltranslations.php";

class JHotelReservationModelManageEmailsDefault extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array 	= JRequest::getVar('email_default_id',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}
	function setId($email_default_id)
	{
		// Set id and wipe data
		$this->_email_default_id		= $email_default_id;
		$this->_data			= null;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	 
	
	function &getDatas()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = ' SELECT * FROM #__hotelreservation_emails_default  ORDER BY email_default_name ';
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT * FROM #__hotelreservation_emails_default'.
						' WHERE email_default_id = '.$this->_email_default_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
					
		}
		
		if ( !$this->_data ) 
		{
			$this->_data = new stdClass();
			$this->_data->email_default_id 		= null;
			$this->_data->email_name			= null;
			$this->_data->email_subject			= null;
			
			$this->_data->email_type			= null;			
			$this->_data->email_content			= null;			
			$this->_data->is_default			= null;
			
		}

		return $this->_data;
	}

	function store($data)
	{	
		$row = $this->getTable();

		// Bind the form fields to the table
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		$this->_default_email_id = $row->email_default_id;
		
		return true;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'email_default_id', array(0), 'post', 'array' );
		
		

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;

	}
	
	function state()
	{
		$query = 	' SELECT * FROM #__hotelreservation_emails_default'.
					' WHERE email_default_id = '.$this->_email_default_id;
		
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$query = 	" UPDATE #__hotelreservation_emails_default SET is_default = IF(email_default_id = ".$this->_email_default_id.", 1, 0) 
						WHERE email_type = '".$item->email_type."'
						";
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;
	}
	
	function saveEmailContent($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationModelHotelTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(EMAIL_TEMPLATE_TRANSLATION,$data['default_email_id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['email_content_'.$_lng]) && strlen($data['email_content_'.$_lng])>0){
					$offerDescription = JRequest::getVar( 'email_content_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(EMAIL_TEMPLATE_TRANSLATION,$data['default_email_id'],$_lng,$offerDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}




}
?>