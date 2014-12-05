<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->

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

if (!checkUserAccess(JFactory::getUser()->id,"reservations_reports")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}



class JHotelReservationViewReservationsReports extends JViewLegacy
{
	function display($tpl = null)
	{
		
		JHotelReservationHelper::addSubmenu('reports');
		
		$this->setToolbar();
		$function = $this->getLayout();
		if(method_exists($this,$function)) 
			$tpl = $this->$function();
		$this->setLayout('default');
		parent::display($tpl);
	}
	function incomeReport(){
		$this->setReportsToolbar();
		
		$this->itemsRoomTypes = $this->get('RoomTypes');
		$this->initFilterParams();
		$this->includeCharts();
		$this->appSetings = JHotelUtil::getInstance()->getApplicationSettings();
		$this->hotel = HotelService::getHotel($this->hotel_id);
		
		$tpl	="income";
		return $tpl;
	}
	function countriesReport(){
		$this->setReportsToolbar();
		$this->itemsRoomTypes = $this->get('RoomTypes');
		$this->initFilterParams();
		$this->includeCharts();
		$this->appSetings = JHotelUtil::getInstance()->getApplicationSettings();
		$tpl	="countries";
		return $tpl;
	}
	function includeCharts(){
		$doc =JFactory::getDocument();
		$doc->addScript("components/com_jhotelreservation/libraries/charts/jquery.min.js");
		$doc->addScript("components/com_jhotelreservation/libraries/charts/jquery.jqplot.min.js");
		$doc->addStyleSheet('components/com_jhotelreservation/libraries/charts/jquery.jqplot.css' );
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.barRenderer.min.js' );
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.categoryAxisRenderer.min.js' );
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.pointLabels.min.js' );
		
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.highlighter.min.js' );
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.cursor.min.js' );
		$doc->addScript('components/com_jhotelreservation/libraries/charts/plugins/jqplot.dateAxisRenderer.min.js' );		
	}

	function setReportsToolbar(){
		JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_RESERVATIONS_REPORTS',true), 'generic.png' );
		JToolBarHelper::custom( 'reservationsreports.back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_RESERVATIONS_DASHBOARD',true), false, false );
	}
	function setToolbar(){
		JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_('LNG_RESERVATIONS_REPORTS',true), 'generic.png' );
		JRequest::setVar( 'hidemainmenu', 0 );
		JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
	}
	function initFilterParams(){
		
		$post = JRequest::get('post');
		if(isset($post['filter_datas']))
			$post['filter_datas']=JHotelUtil::convertToMysqlFormat($post['filter_datas']);
		if(isset($post['filter_datae']))
			$post['filter_datae']=JHotelUtil::convertToMysqlFormat($post['filter_datae']);
		
		if( isset( $post['hotel_id'] ) )
			$this->hotel_id =  $post['hotel_id'];
		else
		{
			$this->hotel_id = $post['hotel_id'] = 0;
		}
		if( isset( $post['filter_room_types'] ) )
			$this->filter_room_types =  $post['filter_room_types'];
		else
		{
			$this->filter_room_types = $post['filter_room_types'] = 0;
		}
		if( isset( $post['filter_datas'] ) )
			$this->filter_datas =  $post['filter_datas'];
		else
			$this->filter_datas = $post['filter_datas'] = date('Y-m-01');
		
		if( isset( $post['filter_datae'] ) )
			$this->filter_datae =  $post['filter_datae'];
		else
			$this->filter_datae = $post['filter_datae'] = date('Y-m-t');
		
		if( isset( $post['filter_report_type'] ) )
			$this->filter_report_type =  $post['filter_report_type'];
		else
			$this->filter_report_type = "MONTH";

		
		$this->filter_datas = JHotelUtil::convertToFormat($this->filter_datas);
		$this->filter_datae = JHotelUtil::convertToFormat($this->filter_datae);
		
		
		$hotels		= $this->get('Hotels');
		$hotels = checkHotels(JFactory::getUser()->id,$hotels);
		$this->hotels =  $hotels;
		
		return $post;
	}

}