<?php 

class TaxService{
	
	public static function getTaxes($hotelId){
		$db = JFactory::getDBO();
		$query = " SELECT * FROM #__hotelreservation_taxes
					WHERE is_available = 1  AND hotel_id  = $hotelId";		
		$db->setQuery( $query );
		$taxes = $db->loadObjectList();
		
		return $taxes;
	}
	
	function setTaxDisplayPrice(&$taxes){
		foreach ($taxes as &$tax){
			if( $tax->tax_type =='Fixed'){
				$tax->tax_display_value = $this->convertToCurrency($tax->tax_value, $this->itemCurrency->description, $this->currency_selector);
			}
		}
	}
}