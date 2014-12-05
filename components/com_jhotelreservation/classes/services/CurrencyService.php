<?php 
class CurrencyService{
	
	/*
	 * Convert from source currency to dest currency using google converter
	*/
	public static function getAllCurrencies(){
		$db = JFactory::getDBO();
		$query = ' SELECT
					h.*
					FROM #__hotelreservation_currencies h
					ORDER BY description asc';
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
	public static function convertCurrency($amount, $from_Currency, $to_Currency) {
		
		if(strcmp($from_Currency, $to_Currency) == 0 || strlen($from_Currency)==0 )
			return $amount;
	
		$amount = urlencode($amount);
	
		$from_Currency = urlencode($from_Currency);
	
		$to_Currency = urlencode($to_Currency);
	
			
	
		$url = "http://www.google.com/ig/calculator?q=$amount$from_Currency=?$to_Currency";
	
		$ch = curl_init();
	
		$timeout = 0;
	
		curl_setopt ($ch, CURLOPT_URL, $url);
	
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt ($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
		$rawdata = curl_exec($ch);
	
		curl_close($ch);
	
	
		$data = explode('"', $rawdata);
	
		$data = explode(' ', $data[3]);
	
		$var = $data[0];
	
		return round($var,2);
	
	}
}

?>