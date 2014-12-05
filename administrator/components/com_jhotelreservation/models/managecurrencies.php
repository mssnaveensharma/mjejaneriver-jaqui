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

class JHotelReservationModelManageCurrencies extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('currency_id',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}
	function setId($currency_id)
	{
		// Set id and wipe data
		$this->_currency_id		= $currency_id;
		$this->_data		= null;
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
			//$query = ' SELECT * FROM #__hotelreservation_currencies';
			$query = 	' SELECT cr.*, IF(ISNULL(ap.currency_id),0,1 ) AS is_default_app FROM #__hotelreservation_currencies cr '.
					' LEFT JOIN #__hotelreservation_applicationsettings ap USING(currency_id)';

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
			$query = 	' SELECT cr.*, IF(ISNULL(ap.currency_id),0,1 ) AS is_default_app FROM #__hotelreservation_currencies cr '.
					' LEFT JOIN #__hotelreservation_applicationsettings ap USING(currency_id)'.
					' WHERE cr.currency_id = '.$this->_currency_id;
				
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}

		if (!$this->_data)
		{
			$this->_data = new stdClass();
			$this->_data->currency_id 			= null;
			$this->_data->description			= null;
			$this->_data->is_default_app		= null;
			$this->_data->currency_symbol		= null;
		}

		$query = 	' SELECT * FROM #__hotelreservation_countries ORDER BY country_name ';
		$this->_data->countries	= $this->_getList($query);
			

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
		$cids = JRequest::getVar( 'currency_id', array(0), 'post', 'array' );
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
		$query = 	" UPDATE #__hotelreservation_currencies SET is_available = IF(is_available, 0, 1) WHERE currency_id = ".$this->_currency_id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
				
		}
		return true;
	}




}
?>