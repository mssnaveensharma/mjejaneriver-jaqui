<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableManageHotelEnvironmentRelation extends JTable
{

		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableManageHotelEnvironmentRelation(& $db) {

		parent::__construct('#__hotelreservation_hotel_environment_relation', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

}