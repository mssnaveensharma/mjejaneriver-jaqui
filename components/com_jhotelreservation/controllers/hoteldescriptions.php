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
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'hoteltranslations.php';

class JHotelReservationControllerHotelDescriptions extends JHotelReservationController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		$view	= $this->getView('hoteldescriptions');
		$model 	= $this->getModel('hoteldescriptions');
		
		//add translation model
		$hotelTranslations = $this->getModel ( 'hoteltranslations' );
		$view->setModel($hotelTranslations);
		$view->setModel( $model, true );
		$view->display();
		parent::__construct();
	}

}