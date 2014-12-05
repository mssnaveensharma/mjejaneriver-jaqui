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
JHTML::_('script','administrator/components/'.getBookingExtName().'/assets/js/dashboard_charts.js');

class JHotelReservationViewJHotelReservation extends JViewLegacy
{
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('LNG_COM_JHOTEL_RESERVATION',true), 'generic.png');
		$this->includeCharts();
		
		if(JRequest::getVar('task') == "menu_airport_transfer"){
			JToolBarHelper::title(JText::_('LNG_AIRPORT_TRANSFER',true), 'generic.png');
			JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
		}	
		parent::display($tpl);
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
	
}