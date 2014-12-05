<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'manageinvoices.php');

class JHotelReservationModelManageInvoicesFront extends JHotelReservationModelManageInvoices{
	
	function __construct()
	{
		parent::__construct();
	}
}

