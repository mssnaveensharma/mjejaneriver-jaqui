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

class JHotelReservationModelManageRatingQuestions extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		//var_dump($array);
	}
		
	public function getReviewQuestions()
	
	{
		// Get all review questions
		$query = 	' SELECT * FROM #__hotelreservation_review_questions order by review_question_nr';
		$this->_db->setQuery( $query );
		return  $this->_db->loadObjectList();
	}
	
	public function getReviewQuestion()
	
	{
		// Get all review question	
		$questionId = JRequest::getVar('review_question_id');
		if(isset($questionId[0])){
			$table = $this->getTable('ReviewQuestions','Table');
			$table->load($questionId[0]);
			return $table;
		}
		return null;
	}
	public function saveratingquestion(){
		$post = JRequest::get('post');
		$table = $this->getTable('ReviewQuestions', 'Table');
		$table->bind($post);
		if(!$table->store()){
				return $row->getErrorMsg();
		}
		else 
			return JText::_('LNG_RATING_QUESTION_SAVED',true);
		
	}
	function deleteratingquestions(){
		$cids = JRequest::getVar( 'review_question_id', array(0), 'post', 'array' );
		$post = JRequest::get('post');
		
		$row = $this->getTable('ReviewQuestions', 'Table');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					return $row->getErrorMsg();
				}
			}
		}
		return JText::_('LNG_RATING_QUESTION_DELETED',true);
	}
	function changequestionorder(){
		$questionId = JRequest::getVar( 'review_question_id');
		$tipOrder = JRequest::getVar( 'tip_order');
		if($tipOrder =="up")
			$direction = -1; 
		else if ($tipOrder =="down")
			$direction = 1;
		$row = $this->getTable('ReviewQuestions', 'Table');
		return $row->changequestionorder($questionId,$direction);
	}
	
}
?>