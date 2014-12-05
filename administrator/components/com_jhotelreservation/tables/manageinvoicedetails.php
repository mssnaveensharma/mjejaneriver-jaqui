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

class TableManageInvoiceDetails extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableManageInvoiceDetails(& $db) {

		parent::__construct('#__hotelreservation_invoice_details', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getInvoiceDetail($id){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_invoice_details where id=$id";
		// 		dmp($query);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function getInvoiceDetails($invoiceId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_invoice_details where invoiceId=$invoiceId order by arrival";
		// 		dmp($query);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function updateInvoiceDetailStatus($detailId, $status){
		$db =JFactory::getDBO();
		$query = "update #__hotelreservation_invoice_details set status=$status where id=$detailId";
		$db->setQuery($query);
		return $db->query();
	}
}