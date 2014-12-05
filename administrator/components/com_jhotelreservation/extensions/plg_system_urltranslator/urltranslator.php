<?php
/**
 * @version		$Id: remember.php 22249 2011-10-16 17:19:28Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * URL Translator
 *
 * @package		Joomla.Plugin
 * @subpackage	System.remember
 */
class plgSystemUrlTranslator extends JPlugin
{
	var $excludingVouchers = array("viva","nusport","grazia","libelle","margriet","flair","panorama","autoweek","revu","story","topdeal","zonnebloem");
	var $regionItemsIds = array("drenthe"=>"185","groningen"=>"186","friesland"=>"187","overijssel"=>"188","gelderland"=>"189","noord holland"=>"191","utrecht"=>"192","limburg"=>"195","zuid holland"=>"196","zeeland"=>"197","noord brabant"=>"198","duitsland"=>"221");

	function onAfterRoute()
	{
		$app = JFactory::getApplication();
		
		// No remember me for admin
		if ($app->isAdmin()) {
			return;		
		}
		
		// Get the full current URI.
		$uri = JURI::getInstance();
		$current = $uri->toString( array('path'));
		
		$pieces = explode("/", $current);
		$keyword= array_pop($pieces); 
		$keywordCat = array_pop($pieces);
		//var_dump($keywordCat);
		
		if(!isset($keyword) || $keyword=='')
			return;
	
		$params = JRequest::get('GET');
		//var_dump($keyword);
		if(strpos($keyword,'hotels-')=== 0) {
			//var_dump("found_region");
			$params = $this->getHotelRegionParams($keyword, $params);
		}else if( strpos($keyword,'hotel-') === 0) {
			//var_dump("found_hotel");
			$params = $this->getHotelParams($keyword, $params);
		}else if(strpos($keyword,'hotelarrangement-')===0) {
			//var_dump("found_offer"); 
			$params = $this->getHotelOfferParams($keyword, $params);
		}else if(strpos($keywordCat,'hotelarrangement')===0) {
			$params = $this->getHotelCityOffersParams($keyword, $params);
		}else if(strpos($keyword,'type-')===0) {
			//var_dump("found_offer");
			$params = $this->getHotelTypeParams($keyword, $params);
		}else if(strpos($keyword,'theme-')===0) {
			//var_dump("found_offer");
			$params = $this->getHotelThemeParams($keyword, $params);
		}else {
			//var_dump($keyword);
			$params = $this->getVoucherParams($keyword, $params);
			if(empty($params["voucher"])){
				$params = $this->getHotelCityParams($keyword, $params);
			}
		}
		//var_dump($params);
		//exit;
		JRequest::set($params,'get',true);
	}
	
	function getHotelParams($keyword, $params){
		$keyword =  preg_replace("/hotel-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "
				SELECT * from #__hotelreservation_hotels h
					inner join ( select *, REPLACE(hotel_name,'-',' ') as hotelName FROM `#__hotelreservation_hotels`) h1 on h.hotel_id = h1.hotel_id
					WHERE h1.hotelName = '".$keyword."' ";
				
		//var_dump($query);
		$db->setQuery($query, 0, 1);
		$hotel = $db->loadObject();
		//var_dump($hotel);
		if(isset($hotel)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "hotel.showHotel";
			$params["view"] = "hotel";
			$params["hotel_id"] = $hotel->hotel_id;
			$params["tip_oper"] = "-1";
			$params["init_hotel"] = "1";
			$params["Itemid"] = "";
		}
		//var_dump($params);
		//exit;
		return $params;
	}
	
	
	function getHotelCityParams($keyword, $params){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotels`  WHERE REPLACE(hotel_city,'-',' ') = '$keyword'";
		$db->setQuery($query);
		$hotels = $db->loadObjectList();
		
		if(!empty($hotels)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "hotels.searchHotels";
			$params["view"] = "hotels";
			$params["showAll"] = "1";
			$params["city"] = $keyword;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	
	function getHotelRegionParams($keyword, $params){	
		$keyword =  preg_replace("/hotels-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotel_regions`  WHERE REPLACE(name,'-',' ') = '$keyword'";
		
 		//var_dump($query);
		$db->setQuery($query, 0, 1);

		$region = $db->loadObject();
		//var_dump($region);
		if(isset($region)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "search.searchHotels";
			$params["filterParams"] = "regionId=".$region->id;
			$params["tip_oper"] = "-2";
			$params["showAll"] = "1";
			$params["Itemid"] = $this->regionItemsIds[$keyword];
		}		
		return $params;
	}
	
	function getHotelThemeParams($keyword, $params){
		$keyword =  preg_replace("/theme-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_offers_themes`  WHERE REPLACE(name,'-',' ') = '$keyword'";
	
	
		//var_dump($query);
		$db->setQuery($query, 0, 1);
	
		$theme = $db->loadObject();
		//var_dump($theme);
		if(isset($theme)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "search.searchHotels";
			$params["filterParams"] = "themeId=".$theme->id;
			$params["showAll"] = "1";
			$params["view"] = "hotels";
			$params["Itemid"] = ""; 
		}
		return $params;
	}
	
	function getHotelTypeParams($keyword, $params){
		$keyword =  preg_replace("/type-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__hotelreservation_hotel_types`  WHERE REPLACE(name,'-',' ') = '$keyword'";
	
	
		//var_dump($query);
		$db->setQuery($query, 0, 1);
	
		$type = $db->loadObject();
		//var_dump($type);
		if(isset($type)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "search.searchHotels";
			$params["filterParams"] = "typeId=".$type->id;
			$params["tip_oper"] = "-2";
			$params["showAll"] = "1";
			$params["view"] = "hotels";
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getVoucherParams($keyword, $params){
		
		foreach($this->excludingVouchers as $voucher){
			if(strcasecmp($voucher, $keyword) == 0)
				return;
		}
		
		$keyword =  str_replace("-", " ", $keyword);
		$db = JFactory::getDBO();
		$query = "
				select * from #__hotelreservation_offers of
					inner join #__hotelreservation_offers_vouchers hov on of.offer_id = hov.offerId  
					where REPLACE(hov.voucher,'-',' ') = '$keyword' 
					group by of.offer_id";
	
		//var_dump($query);
		$db->setQuery($query, 0, 1);
		$voucher = $db->loadObject();
		//var_dump($voucher);
		if(isset($voucher)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.searchOffers";
			$params["voucher"] = $voucher->voucher;
			$params["Itemid"] = "";
		}
		//var_dump($params);
		return $params;
	}
	
	function getHotelOfferParams2($keyword, $params){

		$keyword =  preg_replace("/hotelarrangement-/", "", $keyword,1);
		$keyword =  str_replace("-", " ", $keyword);

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__hotelreservation_offers of  
						inner join ( select *, REPLACE(offer_name,'-',' ') as offerName FROM #__hotelreservation_offers) of1 on of.offer_id = of1.offer_id
						WHERE of1.offerName = '".$keyword."' ";
		//var_dump($query);
		$db->setQuery($query, 0, 1);
		
		$offer = $db->loadObject();
		//var_dump($offer);
		if(isset($offer)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.displayOffer";
			$params["offerId"] = $offer->offer_id;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getHotelOfferParams($keyword, $params){

		$keywords =  explode("-",$keyword);
		$keyword =  end($keywords);

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__hotelreservation_offers where offer_id = $keyword";
	
		//var_dump($query);
		$db->setQuery($query, 0, 1);
		
		$offer = $db->loadObject();
		//var_dump($offer);
		
		if(isset($offer)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.displayOffer";
			$params["offerId"] = $offer->offer_id;
			$params["Itemid"] = "";
		}
		return $params;
	}
	
	function getHotelCityOffersParams($keyword, $params){
	
		$keyword =  str_replace("-", " ", $keyword);
		
		$db = JFactory::getDBO();
		$query = "SELECT * 
					FROM #__hotelreservation_offers of
					left join #__hotelreservation_hotels h on h.hotel_id = of.hotel_id
					WHERE REPLACE(h.hotel_city,'-',' ') = '$keyword'";
		$db->setQuery($query);
		$offers = $db->loadObjectList();
		
		if(!empty($offers)){
			$params["option"] = "com_jhotelreservation";
			$params["task"] = "offers.searchOffers";
			$params["city"] = "$keyword";
			$params["view"] = "listOffers";
			$params["Itemid"] = "";
		}
		return $params;
	}
}
