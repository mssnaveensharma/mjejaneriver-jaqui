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

class JHotelReservationControllerManageHotelRatings extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');
		$this->registerTask( 'add', 'edit');
		$this->registerTask( 'save', 'save');
		$this->registerTask( 'delete', 'deletehotelrating');
		
	}
	function menuhotelratings(){
		JRequest::setVar('view','managehotelratings');
		JRequest::setVar('layout','ratingsmenu');
		parent::display();
	}

	function managehotelratings(){
		parent::display();
	}
	function deletehotelrating(){
		
	}
 	
	function manageratingquestions(){
		JRequest::setVar('view','manageratingquestions');
		parent::display();
	
	}
	function editratingquestion(){
		JRequest::setVar('view','manageratingquestions');
		JRequest::setVar('layout','edit');
		parent::display();
	}
	
	function saveratingquestion(){
		$ratingQuestions = $this->getModel('manageratingquestions');
		$msg = $ratingQuestions->saveratingquestion();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=managehotelratings.manageratingquestions', $msg );
	}
	
	function deleteratingquestions(){
		$ratingQuestions = $this->getModel('manageratingquestions');
		$msg = $ratingQuestions->deleteratingquestions();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=managehotelratings.manageratingquestions', $msg );
	}
	function changequestionorder(){
		$ratingQuestions = $this->getModel('manageratingquestions');
		$msg = $ratingQuestions->changequestionorder();
		echo $msg;
		exit;
	}
	
	function back(){
		$msg = JText::_( '' ,true);
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
	}

	function changeState(){
		$hotelratings = $this->getModel('managehotelratings');
		$hotelratings->changeState();
		$hotelId = JRequest::getVar('hotel_id');
		$msg = JText::_('LNG_REVIEW_PUBLISH_STATE',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managehotelratings&view=managehotelratings&hotel_id='.$hotelId, $msg );
		parent::display();
	}

}
?>

		