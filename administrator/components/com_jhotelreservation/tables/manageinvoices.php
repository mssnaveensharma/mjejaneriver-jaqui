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

class TableManageInvoices extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableManageInvoices(& $db) {

		parent::__construct('#__hotelreservation_invoices', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getInvoice($invoiceId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_invoices where id= $invoiceId";
		$db->setQuery($query);
		return $db->loadObject();
	}

	function getHotelInvoices($hotelId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_invoices where hotelId=$hotelId order by id desc";
		//dmp($query);
		$db->setQuery($query);
		$result =  $db->loadObjectList();
		//dmp($result);
		return $result;
	}

	function updateState($invoiceId, $name, $agreed){
		$db =JFactory::getDBO();
		$query = "update #__hotelreservation_invoices set approvalName='$name', agreed=$agreed where id=$invoiceId";
		//dmp($query);
		//exit;
		$db->setQuery($query);
		return $db->query();
	}
	
	function getOpenInvoices(){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_invoices where status=0";
		$db->setQuery($query);
		return $db->loadObjectList();
	}


}