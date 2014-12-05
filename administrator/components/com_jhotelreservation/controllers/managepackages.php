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
JHTML::_('script', 						'administrator/components/'.getBookingExtName().'/assets/datepicker/js/datepicker.js');
JHTML::_('script', 								'administrator/components/'.getBookingExtName().'/assets/datepicker/js/eye.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/datepicker/js/utils.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/datepicker/js/layout.js');


class JHotelReservationControllerManagePackages extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');
		$this->registerTask( 'add', 'edit');
		if( JRequest::getVar('is_error')=="1" && JRequest::getVar('task')=="save" )
		{
			JRequest::setVar( 'view', 'managepackages' );
			$this->display();
		}
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('managepackages');

		$post = JRequest::get( 'post' );
		if( !isset($post['is_price_day']) )
		$post['is_price_day'] = false;
		$post['package_description'] 			= JRequest::getVar('package_description', '', 'post', 'string', JREQUEST_ALLOWRAW);

		$package_prices	= array();
		/*
		 foreach( $post[ 'package_price_day'] as $keyPos => $valPrice )
		{
		for( $day = 1; $day <=7;$day ++ )
		{
		//dmp( 'price_day_'.$keyPos.'_'.($day) );
		if( isset( $post[ 'day_'.$keyPos.'_'.($day) ] ) )
		$package_prices[ $valPrice ][] = $day;
		}
		}
		*/


		$daysWeek = array( "", "MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN" );
		foreach( $package_prices as $keyPret => $valuesDays )
		{
			foreach( $valuesDays as $day )
			{
				$post["package_price_$day"]	 = $keyPret;
				unset( $daysWeek[ $day ] );
			}
		}
		$post['package_prices'] = $package_prices;

		if( JHotelUtil::checkIndexKey( '#__hotelreservation_packages', array( 'hotel_id' => $post['hotel_id'] , 'package_name' => $post['package_name'] ) , 'package_id', $post['package_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_PACKAGE_NAME_EXISTENT',true) );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&task=add&hotel_id='.$post['hotel_id'], $msg );
		}
		/*
		 else if( count($daysWeek) > 1 )
		{
		$info_days = '';
		foreach( $daysWeek as $valDay )
		{
		if( strlen($info_days) > 0 )
		$info_days .=', ';
		$info_days.=JText::_($valDay,true);
		}
		JError::raiseWarning( 500, JText::_('LNG_PRICE_DOESN_T_COVER',true)." ".$info_days );
		}
		else if(
		$post['is_price_day'] == false
		&&
		( !is_numeric($post["package_price"]) ||$post["package_price"] == 0 )
		)
		{
		JError::raiseWarning( 500, JText::_('LNG_PLEASE_INSERT_PACKAGE_PRICE',true) );
		}
		else if(
		$post['is_price_day'] == true
		&&
		(
		( !is_numeric($post["package_price_1"]) ||$post["package_price_1"] == 0 )
		||
		( !is_numeric($post["package_price_2"]) ||$post["package_price_2"] == 0 )
		||
		( !is_numeric($post["package_price_3"]) ||$post["package_price_3"] == 0 )
		||
		( !is_numeric($post["package_price_4"]) ||$post["package_price_4"] == 0 )
		||
		( !is_numeric($post["package_price_5"]) ||$post["package_price_5"] == 0 )
		||
		( !is_numeric($post["package_price_6"]) ||$post["package_price_6"] == 0 )
		||
		( !is_numeric($post["package_price_7"]) ||$post["package_price_7"] == 0 )
		)

		)
		{
		JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_DAY',true) );
		}*/
		else if(
		$post['package_type_price'] == 0
		&&
		(
		( !is_numeric($post["package_price_1"]) ||$post["package_price_1"] == 0 )
		||
		( !is_numeric($post["package_price_2"]) ||$post["package_price_2"] == 0 )
		||
		( !is_numeric($post["package_price_3"]) ||$post["package_price_3"] == 0 )
		||
		( !is_numeric($post["package_price_4"]) ||$post["package_price_4"] == 0 )
		||
		( !is_numeric($post["package_price_5"]) ||$post["package_price_5"] == 0 )
		||
		( !is_numeric($post["package_price_6"]) ||$post["package_price_6"] == 0 )
		||
		( !is_numeric($post["package_price_7"]) ||$post["package_price_7"] == 0 )
		)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_DAY_BY_DAY',true) );
		}
		else if(
		$post['package_type_price'] == 1
		&&
		(!is_numeric($post["package_price"]) ||$post["package_price"] == 0)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_SAME_EVERY_DAY',true) );
		}
		else if(
		$post['package_type_price'] == 2
		&&
		(
		(!is_numeric($post["package_price_midweek"]) ||$post["package_price_midweek"] == 0)
		||
		(!is_numeric($post["package_price_weekend"]) ||$post["package_price_weekend"] == 0)
		)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_MIDDWEEK_WEEKEND',true) );
		}
		else if( strlen($post['package_description'] ) == 0 )
		{
			JError::raiseWarning( 500, JText::_('LNG_PLEASE_INSERT_DESCRIPTION_PACKAGE',true) );
		}
		else if( strlen($post['package_datas'] ) > 0 && strlen($post['package_datae'] ) > 0
		&&
		strtotime($post['package_datas']) > strtotime($post['package_datae'])
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PERIOD_PACKAGE_DATES',true) );
		}
		else if ($model->store($post))
		{
			$msg = JText::_('LNG_PACKAGE_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&hotel_id='.$post['hotel_id'], $msg );
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500,JText::_('LNG_ERROR_SAVING_PACKAGE',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&hotel_id='.$post['hotel_id'], $msg );
		}

		// Check the table in so it can be edited.... we are done with it anyway


	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
		$post['hotel_id'] = 0;
			
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&hotel_id='.$post['hotel_id'], $msg );
	}

	function delete()
	{
		$model = $this->getModel('managepackages');
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
		$post['hotel_id'] = 0;
			
		if ($model->remove()) {
			$msg = JText::_('LNG_PACKAGE_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_PACKAGE',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway

		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&hotel_id='.$post['hotel_id'], $msg );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'managepackages' );

		parent::display();

	}

	function state()
	{
		$model = $this->getModel('managepackages');
		$get = JRequest::get( 'get' );
		if( !isset($get['hotel_id']) )
		$get['hotel_id'] = 0;
			
		if ($model->state()) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_PACKAGE_STATE',true);
		}

		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&hotel_id='.$get['hotel_id'], $msg );
	}
}