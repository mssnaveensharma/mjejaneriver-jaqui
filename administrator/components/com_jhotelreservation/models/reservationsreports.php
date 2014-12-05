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

class JHotelReservationModelReservationsReports extends JModelLegacy
{ 
	var $dataRooms 					= null;
	var $dataRoomsConfirmations		= null;
	var $paymentProcessorsResults	= null;
	var $hotels						= null;
	function __construct()
	{
		parent::__construct();
	}
	
	function getRoomTypes()
	{
		// Load the data
		$query = ' SELECT * FROM #__hotelreservation_rooms WHERE is_available = 1 AND hotel_id ="'.JRequest::getString('hotel_id') .'"';
		//echo $query;
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
	}
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->hotels )) 
		{
			$query = ' SELECT 
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						ORDER BY hotel_name, country_name ';
			//$this->_db->setQuery( $query );
			$this->hotels = $this->_getList( $query );
		}
		return $this->hotels;
	}

	function getJsonIncomeData(){
		$row = $this->getTable('confirmations');
		$dateStart = JRequest::getVar('dateStart');
		$dateEnd = JRequest::getVar('dateEnd');
		$roomTypeId = JRequest::getVar('roomTypeId');
		$hotelId = JRequest::getVar('hotelId');
		$reportType = JRequest::getVar('reportType');
		
		if(isset($dateStart) && isset($dateEnd)){
			$reportData = $row->getReservationsIncome($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd);
			$processArray = array();
			foreach ($reportData as $data){
				if($data->groupUnit!=null)
					array_push($processArray,array($data->groupUnit, (int)$data->reservationTotal));
			}
			if(count($reportData)==0){
				array_push($processArray,array('Not found', '0'));
			}
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}
	function getJsonCountriesData(){
		$row = $this->getTable('confirmations');
		$dateStart = JRequest::getVar('dateStart');
		$dateEnd = JRequest::getVar('dateEnd');
		$roomTypeId = JRequest::getVar('roomTypeId');
		$hotelId = JRequest::getVar('hotelId');
		$reportType = JRequest::getVar('reportType');
	
		if(isset($dateStart) && isset($dateEnd)){
			$reportData = $row->getReservationsCountries($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd);
			$processArray = array();
			foreach ($reportData as $data){
				if($data->country!=null && $data->country!="")
					array_push($processArray,array($data->country, (int)$data->countryCount));
			}

			if(count($reportData)==0){
				array_push($processArray,array('Not found', '0'));
			}		
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}
	
	function getJsonReservationData(){
		$row = $this->getTable('confirmations');
		$dayLag = JRequest::getVar('daysLag');
		$reportType = JRequest::getVar('reportType');
		
		switch($dayLag){
			case 7:
			case 30:
				$reportType = "DAY";
				break;
			case 90:
			case 180:
			case 365:
				$reportType = "MONTH";
				break;
			case 730:
			case 1095:
				$reportType = "YEAR";
				break;
			default: 
				$reportType = "DAY";
		}
	
		if(isset($dayLag)){
			$reportData = $row->getReservationsReport($reportType,$dayLag);
			$processArray = array();
			foreach ($reportData as $data){
				array_push($processArray,array($data->groupUnit, (int)$data->reservationTotal));
			}
			if(count($reportData)==0){
				$processArray=null;
			}
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}
	
}
