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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableReviewQuestions extends JTable
{
	var $review_question_id	= null;
	var $review_question_desc		= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableReviewQuestions(& $db) {

		parent::__construct('#__hotelreservation_review_questions', 'review_question_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	function changequestionorder($questionId, $direction){
		$query = ' update #__hotelreservation_review_questions set review_question_nr=review_question_id+'.$direction.' where review_question_id = '.$questionId;
		$this->_db->setQuery( $query );
		$this->_db->query();
		return $this->_db->getErrorMsg();
	}


}