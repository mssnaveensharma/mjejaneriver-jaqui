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

class JHotelReservationModelManageHotelRatings extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('currency_id',  0, '', 'array');
		//var_dump($array);
	}
		
	function getHotels(){
		$hotelTable = $this->getTable('hotels');
		return $hotelTable->getAllHotels();
	}	
	function getHotel(){
		$hotelId = JRequest::getVar('hotel_id');
		$hotelTable = $this->getTable('hotels');
		$hotelTable->load($hotelId);
		return $hotelTable;
	}
	function getHotelReviews(){
		$hotelId = JRequest::getVar('hotel_id');
		if(!isset($hotelId))
			return null;
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		return $reviewCustomersTable->getHotelReviews($hotelId);
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
	

	function changeState()
	{
		$reviewId = JRequest::getVar('review_id');
		$hotelId = JRequest::getVar('hotel_id');
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$reviewCustomersTable->setPublished($reviewId);
		$reviewCustomersTable->calculateHotelRatingScore($hotelId);
	}
	
	function deleteHotelRating(){
		$confirmationId = JRequest::getVar('confirmation_id',-1);
		
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$review = $reviewCustomersTable->getReviewByCofirmation($confirmationId);
	
		if($review->review_id == 0)
			return;
		$reviewAnswersTable = $this->getTable('reviewanswers');
		$reviewAnswersTable->deleteByReview($review->review_id);
		$this->recalculateReviewScore($review->hotel_id);
		$reviewCustomersTable->delete($review->review_id);
	}
	
	function recalculateReviewScore($hotelId)
	{
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$reviewCustomersTable->calculateHotelRatingScore($hotelId);
	}
	
}
?>