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

class JHotelReservationModelManageHotelUsers extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$this->userId 	= JRequest::getVar('user_id');
		$this->hotel_id = JRequest::getVar('hotel_id');
				
		$mainframe = JFactory::getApplication();
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->_total = 0;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	 
	function &getHotelId()
	{
		return $this->hotel_id;
	}
	
	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function &getDatas()
	{
		// Load the data
		$table = $this->getTable();
		if($this->hotel_id>0)
			$users =  $table->getHotelUsers($this->hotel_id, $this->getState('limitstart'), $this->getState('limit'));
		else 
			$users=null;
		return $users;
	}
	
	function getTotalUsers(){
		$table = $this->getTable();
		$this->_total = $table->getTotalUsers($this->hotel_id);
		//dmp($this->_total);
		return $this->_total;
	}
	
	function getFilterParams(){
		return null;
	}
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
	
			$this->_pagination = new JPagination($this->getTotalUsers(), $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('controller','managehotelusers');
			$this->_pagination->setAdditionalUrlParam('view','managehotelusers');
			$this->_pagination->setAdditionalUrlParam('task','viewhotelusers');
		}
		return $this->_pagination;
	}
	
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->_hotels )) 
		{
			$query = ' SELECT 
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						ORDER BY hotel_name, country_name ';
			//$this->_db->setQuery( $query );
			$this->_hotels = $this->_getList( $query );
		}
		return $this->_hotels;
	}
	
	function &getHotel()
	{
		$query = 	' SELECT 	
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels				h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						'.
					' WHERE 
						hotel_id = '.$this->hotel_id;
		
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}
	
	
	function &getData()
	{
		$table = $this->getTable();
		if(isset($this->userId) & $this->userId!="")
			$this->_data = $table->getUserById($this->userId);
		else $this->_data=null;
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
		return true;
	}
	
	function remove()
	{
		$table = $this->getTable();
		return $table->deleteUser($this->userId,$this->hotel_id);
	}
	
	
	function exportListAsCSV(){
		$csv_output = '';
		
		$table = $this->getTable();
		$users =  $table->getUsers();
		
		foreach($users as $user){
			$csv_output .= $user->first_name.', '. $user->last_name.', '. $user->address.', '. $user->city.', '. $user->state_name.', '. $user->country.', '. $user->postal_code.', '. $user->tel.', '. $user->email;
			$csv_output .= "\n";
		}
		
		$fileName = "user_list.csv";

		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$fileName.".csv");

		print $csv_output;
	}
}
?>