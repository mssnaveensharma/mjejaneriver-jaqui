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

class TableHotels extends JTable
{
	var $hotel_id				= null;
	var $hotel_name				= null;
	var $country_id				= null;
	var $hotel_county			= null;
	var $hotel_city				= null;
	var $hotel_website			= null;
	var $hotel_address			= null;
	var $currency_id			= null;
	var $is_available			= null;
	var $hotel_latitude			= null;
	var $hotel_longitude		= null;
	var $hotel_stars			= null;
	var $start_date				= null;
	var $end_date				= null;
	var $ignored_dates			= null;
	var $hotel_rating_score     = null;
	var $featured				= null;
	var $commission				= null;
	var $email					= null;
	var $recommended			= null;
	var $reservation_cost_val	= null;
	var $reservation_cost_proc	= null;
	var $hotel_phone			= null;
	var $hotel_number			= null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Tablehotels(& $db) {

		parent::__construct('#__hotelreservation_hotels', 'hotel_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	function getAllHotels(){
		$query = ' SELECT
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels h
						LEFT JOIN #__hotelreservation_countries	c USING ( country_id)
						ORDER BY hotel_name, country_name ';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getHotel($hotelId){
		$query = "SELECT * FROM #__hotelreservation_hotels h
 				  LEFT JOIN #__hotelreservation_countries c on h.country_id=c.country_id
				  where  h.hotel_id=".$hotelId;
// 		dmp($query);						
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	
	function getAllHotelsWithoutMonthlyInvoice($startDate, $endDate){
		$query = "select hotel_id, commission, country_id, reservation_cost_val
						FROM #__hotelreservation_hotels h where hotel_id not in 
						(select hotelId from #__hotelreservation_invoices hi 
						where hi.date>='$startDate' and hi.date<='$endDate' )
						order by h.hotel_id";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * 
	 * @param unknown_type $searchParam
	 * @param  $searchType = 0 - search for all info including the prices based on search params
	 * @param  $searchType = 0 - search only for hotel data excluding the prices, reviews
	 * @return string
	 */
	function getHotelSearchQuery($searchParam, $searchType=0){
		$facilityFilter="";
		$typesFilter="";
		$accommodationTypeFilter="";
		$enviromentFilter="";
		$regionFilter="";
		$themesFilter="";
		$orderBy="";
		$voucherFilter = "";

		if($searchParam['facilities']){
			$options = explode(",",trim($searchParam['facilities']));
			foreach ($options as $option){
				$facilityFilter = $facilityFilter. " and FIND_IN_SET('".$option."',facilities) ";
			}
		}
			
		if($searchParam['types']){
			$options = explode(",",trim($searchParam['types']));
			foreach ($options as $option){
				$typesFilter = $typesFilter. " and FIND_IN_SET('".$option."',types) ";
			}
		}
		if($searchParam['accommodationTypes']){
			$options = explode(",",trim($searchParam['accommodationTypes']));
			
			foreach ($options as $option){
				$accommodationTypeFilter = $accommodationTypeFilter. " and FIND_IN_SET('".$option."',accommodationTypes) ";
			}
		}
		if($searchParam['enviroments']){
			$options = explode(",",trim($searchParam['enviroments']));
			foreach ($options as $option){
				$enviromentFilter = $enviromentFilter. " and FIND_IN_SET('".$option."',enviroments) ";
			}
		}
		if($searchParam['regions']){
			$options = explode(",",trim($searchParam['regions']));
			foreach ($options as $option){
				$regionFilter = $regionFilter. " and FIND_IN_SET('".$option."',regions) ";
			}
		}

		
		if($searchParam['themes']){
			$options = explode(",",trim($searchParam['themes']));
			$themesFilter = " ";
			foreach ($options as $option){
				$themesFilter = $themesFilter. " and FIND_IN_SET('".$option."',themes) ";
			}
		}

		$roomFilter='';
		/*$roomFilter=strlen($regionFilter)>0?'':'where 1';
		if($searchParam['adults']){
			$adults =  $searchParam['adults'];
			$roomFilter = $roomFilter." and hr.room_capacity >= $adults";
		}*/
		
	
		$cityFilter ="";
		if(!empty($searchParam['city'])){
			$cityFilter = " and h.hotel_city = '".$searchParam['city']."'";
		}
		

		$startDate= $searchParam['startDate'];
		$endDate = $searchParam['endDate'];
		$endDate = date("Y-m-d", strtotime("-1 day", strtotime($endDate)));
		$days=$this->getDays($startDate, $endDate);
		$dayFilter = '';
		foreach($days as $day){
			$dayFilter = $dayFilter." and LOCATE('$day', ignored_dates)=0";
		}
		
		//dmp($dayFilter);
		
		$activeHotelsFilter = " and (h.start_date <= '$startDate' or h.start_date='0000-00-00')and (h.end_date >= '$endDate' or h.end_date='0000-00-00')";
		
		$availabilityFilter = " and (room_available=1 or offer_available=1)";
		
		$voucherFilter = " and  (hov.voucher is null or hov.voucher='' or hof.public=1)";
		if($searchParam['voucher']){
			$voucher =  $searchParam['voucher'];
			$voucherFilter = " and LOWER(hov.voucher) =LOWER('$voucher')";
			$voucherFilter.= " and hof.offer_datas <= '$startDate' and hof.offer_datae >= '$endDate' ";
			$availabilityFilter = " and (min_offer_price is not null) ";
		}
		
		
		$regionWhereFilter="";
		$accomodationTypeWhereFilter ="";
		$offerThemesWhereFilter ="";
		$whereClause="";
		if($searchParam['keyword']){
			$keyword = $searchParam['keyword'];
			$whereClause = $whereClause. " and ((h.hotel_name like '%$keyword%') or (h.hotel_city like '%$keyword%') or (h.hotel_county like '%$keyword%')) ";
			
			if($searchParam['searchType']==JText::_("LNG_CITY")){
				$whereClause = "and h.hotel_city like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_PROVINCE_AND_REGION")){
				$whereClause = "";
				$regionWhereFilter=" and hr.name like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_HOTELS")){
				$whereClause = "and h.hotel_city like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_ACCOMMODATION_TYPES")){
				$whereClause = "";
				$accomodationTypeWhereFilter =" and hat.name like '%$keyword%'";
			}
			
			if($searchParam['searchType']==JText::_("LNG_THEMES")){
				$whereClause = "";
				$offerThemesWhereFilter = " and hr.name like '%$keyword%'";
			}
			
		}
		
		if($searchParam["orderBy"]){
			$orderBy = " order by ".$searchParam["orderBy"];
		}
		
		$showFilter = " having ((hh10.room_min_rate is not null) or min_offer_price is not null) ";
		
		$roomRateDateFilter = " and hrrp.date between '$startDate' and '$endDate'";
		$offerRateDateFilter= " and orp.date between '$startDate' and '$endDate'";
		
		if(isset($searchParam['showAll']) && $searchParam['showAll'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$showFilter='';
			$whereClause='';
			$offerRateDateFilter="";
			$roomRateDateFilter="";
		}
		
		if(isset($searchParam['no-dates']) && $searchParam['no-dates'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$showFilter='';
			$offerRateDateFilter="";
			$roomRateDateFilter="";
		}
		
		$distanceSelect="";
		$havingDistance="";
	
		if(isset($searchParam['nearByHotels'])){
			$whereClause="";
			$regionWhereFilter="";
			$latitude = $searchParam['latitude'];
			$longitude = $searchParam['longitude'];
			$distance = $searchParam['distance'];
			$distanceSelect = "3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( h.hotel_latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs(h.hotel_latitude) *  pi()/180) * POWER(SIN(($longitude - h.hotel_longitude) *  pi()/180 / 2), 2) )) as distance ,";
			$havingDistance = " and distance < $distance  ";
			
			
			$orderBy = "ORDER BY distance asc";
		}
		$excludeFilter = "";
		if(!empty($searchParam["excludedIds"])){
			$excludeFilter =" and h.hotel_id not in (".$searchParam["excludedIds"].") ";
		}
		$languageTag = $searchParam['languageTag'];
		//dmp($whereClause);
		$query = "
					select 
					h.*,
					hotelId,
					hh10.room_min_rate as min_room_price,
					hh11.min_offer_price,
					hh11.offer_min_nights,
					hh11.offer_base_adults,
					hh11.offer_price_type,
					IF(hh11.min_offer_price IS NULL OR hh10.room_min_rate IS NULL, COALESCE(hh11.min_offer_price, hh10.room_min_rate), LEAST(hh11.min_offer_price,hh10.room_min_rate))  as lowest_hotel_price, 
					
				$distanceSelect
				";
		if($searchType == 0){
			$query .= "	
					currency_symbol,
					room_details,
					offer_details,
					count(distinct hc.confirmation_id) as noBookings,
					noReviews,
					c1.country_name,
					c2.description	AS hotel_currency,
					hotel_picture_path,hotel_picture_info,hotel_pictures_count,
					hlt.content as hotel_description,
					";
		}	
		
		$query .="	
				hotelId1, facilities, types, accommodationTypes, enviroments, regions, themes, room_available, offer_available from (
				 	select hotelId1, facilities, types, accommodationTypes, enviroments, regions, themes, room_available, offer_available from (
						select hotelId1, facilities, types, accommodationTypes, enviroments, regions, room_available, max(hof.is_available) as offer_available, GROUP_CONCAT(hotr.themeId) as themes from ( 
						  select hotelId1, facilities, types, accommodationTypes, enviroments, regions, max(hr.front_display) as room_available from (
						     select hotelId1, facilities, types, accommodationTypes, enviroments,  GROUP_CONCAT(rl.regionId) as regions from (
							    select hotelId1, facilities, types, accommodationTypes, GROUP_CONCAT(er.environmentId) as enviroments from (
							        Select hotelId1, facilities, types, GROUP_CONCAT(atr.accommodationTypeId) as accommodationTypes from(
							            Select hotelId1, facilities,GROUP_CONCAT(tr.typeId) as types  from (
							                SELECT h.hotel_id as hotelId1, GROUP_CONCAT(fr.facilityId) as facilities FROM 
							                     #__hotelreservation_hotels as h
							                    left join #__hotelreservation_hotel_facility_relation as fr on h.hotel_id=fr.hotelId where 1 $whereClause $dayFilter $activeHotelsFilter and h.is_available = 1  group by hotelId1 
							           	 	) as hh1 
						                      left join #__hotelreservation_hotel_type_relation as tr on hotelId1=tr.hotelId 
						                      left join #__hotelreservation_hotel_types ht on ht.id=tr.typeId 
						                      where 1 $facilityFilter 
						                      group by hotelId1
							        	) as hh2     
						                  left join #__hotelreservation_hotel_accommodation_type_relation as atr on hotelId1=atr.hotelId 
						                  where 1 $typesFilter  
						                  group by hotelId1
							    	) as hh3
							        left join #__hotelreservation_hotel_environment_relation as er on hotelId1=er.hotelId 
							        where 1 $accommodationTypeFilter   
							        group by hotelId1
							) as hh4
							    left  join #__hotelreservation_hotel_region_relation as rl on hotelId1=rl.hotelId 
							    left  join #__hotelreservation_hotel_regions as hr on hr.id = rl.regionId 
							    where 1 $enviromentFilter $regionWhereFilter
							    group by hotelId1
						)as hh5 
							 	left join #__hotelreservation_rooms as hr on hotelId1=hr.hotel_id where 1 $regionFilter  group by hotelId1
					) as hh6 left join #__hotelreservation_offers hof on hotelId1=hof.hotel_id
						 left join #__hotelreservation_offers_themes_relation hotr on hotr.offerId=hof.offer_id 
						 left join #__hotelreservation_offers_themes hot on hot.id = hotr.themeId
						 ".(
						 !empty($searchParam['voucher'])?
						 "left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId  
						 where 1 $voucherFilter
						 ":"")."
						 group by hotelId1
			   		  )as hh7 
				) as hh8 "; 
		 
		if($searchType == 0){
			$query .=" left join
					( select h1.hotel_id as hotelId,  count(hrt.review_id) as noReviews from
						#__hotelreservation_hotels h1
						left join #__hotelreservation_review_customers as hrt on h1.hotel_id=hrt.hotel_id  
						 group by h1.hotel_id
					) as hh9 on hh8.hotelId1 = hh9.hotelId
				";
		}else{
			$query .=" left join
					( select h1.hotel_id as hotelId from #__hotelreservation_hotels h1
						 group by h1.hotel_id
					) as hh9 on hh8.hotelId1 = hh9.hotelId
				";		
		}
		
		$query .=" inner join #__hotelreservation_hotels h on hh8.hotelId1=h.hotel_id ";
		
		if($searchType == 0){
			$query .=" LEFT JOIN #__hotelreservation_countries c1 USING (country_id) ";
			$query .=" LEFT JOIN #__hotelreservation_currencies c2 USING (currency_id) ";
		}
		
			$query .="
				left join (
	                select h.hotel_id as hotelId3,
	                min(room_rate) as room_min_rate,
	                GROUP_CONCAT(room_name,'|',room_id,'|',room_rate) as room_details 
	                from #__hotelreservation_hotels h
	                left join (
	                	select  
	                	hr.room_name,
	                	hr.room_id,
	                	hr.hotel_id,
	                	if(min(hrrp.price), min(hrrp.price), min(least(hrr.price_1, hrr.price_2, hrr.price_3, hrr.price_4, hrr.price_5, hrr.price_6, hrr.price_7))) as room_rate
	               		from #__hotelreservation_rooms as hr 
                    	left join #__hotelreservation_rooms_rates as hrr on hr.room_id=hrr.room_id
                    	left join #__hotelreservation_rooms_rate_prices as hrrp on hrr.id=hrrp.rate_id $roomRateDateFilter
                    	where hr.front_display = 1 and hr.is_available=1
                    	group by hr.room_id
                    ) hh110 on hh110.hotel_id=h.hotel_id
                    group by hotelId3
                    
	             ) as hh10 on h.hotel_id=hh10.hotelId3";
	
		if($searchType == 0){
			$query .="  left join #__hotelreservation_confirmations as hc on h.hotel_id=hc.hotel_id";
		}
		
        $query .="
			left join (
               select h2.hotel_id as hotelId2, 
               		offer_min_nights, 
               		GROUP_CONCAT(offer_name,'||',offer_id,'||',offer_rate,'||',offer_min_nights,'||',base_adults,'||',price_type,'||',price_type_day,'||',offer_room_id,'||',offer_content separator '#') as offer_details,
               		offer_name as min_offer_name,
					base_adults as offer_base_adults, 
					price_type as offer_price_type, 
					min(offer_rate) as min_offer_price
				   
			 	from #__hotelreservation_hotels h2
             	inner join (
             		select
             		hof.offer_name,
             		hof.offer_content,
             		hof.offer_id,
             		hof.hotel_id as hotel_id,
             		hof.offer_min_nights as offer_min_nights, 
					ofr.base_adults, ofr.price_type,  ofr.price_type_day, ofrr.room_id as offer_room_id, 
					if(ofr.price_type_day=1 , 
						if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) ,
						if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) * hof.offer_min_nights )
						 as offer_rate
				    from #__hotelreservation_offers hof 
                    left join #__hotelreservation_offers_rooms ofrr on ofrr.offer_id = hof.offer_id 
					left join #__hotelreservation_offers_rates ofr on ofr.offer_id = hof.offer_id and ofr.room_id = ofrr.room_id
					left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id $offerRateDateFilter
			        left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId  
  	                where  hof.is_available = 1 and (now() between hof.offer_datasf and hof.offer_dataef) $voucherFilter
  	                group by hof.offer_id 
  	                order by offer_rate asc
  	              ) hh111 on hh111.hotel_id = h2.hotel_id
                group by hotelId2
                order by min_offer_price asc
            )  as hh11 on h.hotel_id= hh11.hotelId2";
		
		
	    if($searchType == 0){     
		       $query .=" left join (
					    SELECT h3.hotel_id AS hotelId12, min(hp.hotel_picture_id), hotel_picture_path, hotel_picture_info, count(hp.hotel_picture_id) as hotel_pictures_count
					   FROM #__hotelreservation_hotels h3
					   LEFT JOIN #__hotelreservation_hotel_pictures AS hp ON hp.hotel_id = h3.hotel_id
					   group by h3.hotel_id
					)as hh12 on hh12.hotelId12 = h.hotel_id
					";
		      
				$query .="	left join (
						select * from 
						 #__hotelreservation_language_translations 
						 where type = ".HOTEL_TRANSLATION."
						 and language_tag = '$languageTag'
						)as hlt on hlt.object_id = h.hotel_id 
						";
	        }
			$query .=" WHERE h.is_available = 1 $availabilityFilter $themesFilter $cityFilter $excludeFilter group by h.hotel_id  $showFilter $havingDistance";

		if($searchType == 0){
			$query .= " $orderBy ";
		}
		
		return $query;
	}

	function getHotels($searchParam, $limitstart=0, $limit=0){
		$db =JFactory::getDBO();

		$query = $this->getHotelSearchQuery($searchParam);
		//echo $query;
		$first  = time();
		
		$db->setQuery("SET OPTION SQL_BIG_SELECTS=1 ");
		//$db->query();
		
		$db->setQuery("SET SESSION group_concat_max_len = 1000000 ");
		$db->query();
		
		
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		//print_r($result);
		//dmp($this->_db->getErrorMsg());
		//echo $this -> getElapsedTime($first);
		
		return $result;
	}

	function getTotalHotels($searchParam){
		$result = 0;
		$db =JFactory::getDBO();

		$query = $this->getHotelSearchQuery($searchParam,0);
		$db->setQuery($query);
		
		$result = $db->loadObjectList();
		
		//dmp($this->_db->getErrorMsg());
		return $result;
	}

	function getFilteredHotels($filterParams=array(), $limitstart=0, $limit=0){
		$whereCond=' where 1 ';

		if(isset($filterParams))
		foreach($filterParams as $key=>$value){
			$whereCond .= "and $key= $value ";
		}
		$query = "	SELECT
					h.*,
					c1.country_name,
					c2.description	AS hotel_currency
				FROM #__hotelreservation_hotels 			h
				LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
				LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
		$whereCond
				ORDER BY h.hotel_name
				";
		$db =JFactory::getDBO();
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	function getFilteredHotelsTotal($filterParams=array()){
		$whereCond=' where 1 ';
		foreach($filterParams as $key=>$value){
			$whereCond .= "and $key= $value ";
		}
		$query = "	SELECT
						h.*,
						c1.country_name,
						c2.description	AS hotel_currency
					FROM #__hotelreservation_hotels 			h
					LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
					LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
		$whereCond
					ORDER BY h.hotel_name
					";
		$db =JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		return $db->getNumRows();
	}

	function getDays($sStartDate, $sEndDate){
		// Firstly, format the provided dates.
		// This function works best with YYYY-MM-DD
		// but other date formats will work thanks
		// to strtotime().
		$sStartDate = date("Y-m-d", strtotime($sStartDate));
		$sEndDate = date("Y-m-d", strtotime($sEndDate));
	
		// Start the variable off with the start date
		$aDays[] = $sStartDate;
	
		// Set a 'temp' variable, sCurrentDate, with
		// the start date - before beginning the loop
		$sCurrentDate = $sStartDate;
	
		// While the current date is less than the end date
		while($sCurrentDate < $sEndDate){
			// Add a day to the current date
			$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
	
			// Add this new day to the aDays array
			$aDays[] = $sCurrentDate;
		}
	
		// Once the loop has finished, return the
		// array of days.
		return $aDays;
	}
	
	function getElapsedTime($eventTime)
	{
		$totaldelay = time() - $eventTime;
		if($totaldelay <= 0)
		{
			return ' < 1 sec';
		}
		else
		{
			if($days=floor($totaldelay/86400))
			{
				$totaldelay = $totaldelay % 86400;
				return $days.' days ago.';
			}
			if($hours=floor($totaldelay/3600))
			{
				$totaldelay = $totaldelay % 3600;
				return $hours.' hours ago.';
			}
			if($minutes=floor($totaldelay/60))
			{
				$totaldelay = $totaldelay % 60;
				return $minutes.' minutes ago.';
			}
			if($seconds=floor($totaldelay/1))
			{
				$totaldelay = $totaldelay % 1;
				return $seconds.' seconds ago.';
			}
		}
	}
	
	
	function getHotelCitiesSuggestions($keword,$limit){
		$query = "select hotel_city as label, hotel_city as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_CITY")."' as category 
					FROM #__hotelreservation_hotels h 
					where hotel_city like '%$keword%' and h.is_available=1
					group by h.hotel_city
					order by nr_hotels desc, h.hotel_city";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelProvinceSuggestions($keword,$limit){
		$query = "select hotel_county as label, hotel_county as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_PROVINCE_AND_REGION")."' as category
		FROM #__hotelreservation_hotels h
		where hotel_county like '%$keword%' and h.is_available=1
		group by h.hotel_county
		order by nr_hotels desc, h.hotel_county";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelRegionSuggestions($keword,$limit){
		$query = "select hr.name as label, hr.name as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_PROVINCE_AND_REGION")."' as category
		FROM #__hotelreservation_hotels h
		inner join #__hotelreservation_hotel_region_relation as rl on h.hotel_id=rl.hotelId 
		inner join #__hotelreservation_hotel_regions as hr on hr.id = rl.regionId 
		where hr.name like '%$keword%' and h.is_available=1
		group by hr.id
		order by nr_hotels desc, hr.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelsSuggestions($keword,$limit){
		$query = "select hotel_name as label, hotel_id as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_HOTELS")."' as category
		FROM #__hotelreservation_hotels h
		#inner join  #__hotelreservation_offers hof on hof.hotel_id = h.hotel_id and hof.is_available =1   
		
		where hotel_name like '%$keword%' and h.is_available=1
		group by h.hotel_name
		order by nr_hotels desc, h.hotel_name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelAccomodationTypeSuggestions($keword,$limit){
		$query = "select hat.name as label, hat.id as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_ACCOMMODATION_TYPES")."' as category
		FROM #__hotelreservation_hotels h
		
		left join #__hotelreservation_hotel_accommodation_type_relation as atr on h.hotel_id=atr.hotelId 
		left join #__hotelreservation_hotel_accommodation_types as hat on hat.id = atr.accommodationtypeId
		where hat.name like '%$keword%' and h.is_available=1
		group by hat.name
		order by nr_hotels desc, hat.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getHotelOfferThemesSuggestions($keword,$limit){
		$query = "select hot.name as label, hot.id as value, count(h.hotel_id) as nr_hotels, '".JText::_("LNG_THEMES")."' as category
		FROM #__hotelreservation_hotels h
		left join #__hotelreservation_offers ho on h.hotel_id=ho.hotel_id
		left join #__hotelreservation_offers_themes_relation hotr on hotr.offerId=ho.offer_id 
		left join #__hotelreservation_offers_themes hot on hot.id = hotr.themeId
		where hot.name like '%$keword%' and h.is_available=1
		group by hot.name
		order by nr_hotels desc, hot.name";
		//dmp($query);
		$this->_db->setQuery( $query,0,$limit);
		return $this->_db->loadObjectList();
	}
	
	function getNearByHotels($latitude, $longitude, $distance, $excludedIds, $limit){
		
		$excludedIdsFilter = "and h.hotel_id no in (". implode(",", $excludedIds).")";
		
		$query = " SELECT h.*,hotel_picture_path, min(hp.hotel_picture_id),
				3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( h.hotel_latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs(h.hotel_latitude) *  pi()/180) * POWER(SIN(($longitude - h.hotel_longitude) *  pi()/180 / 2), 2) )) as distance
				FROM #__hotelreservation_hotels h
				left join #__hotelreservation_hotel_pictures hp on h.hotel_id=hp.hotel_id
				inner join (
				select h.hotel_id as hotelId
				from #__hotelreservation_hotels h
				left join #__hotelreservation_offers hof on hof.hotel_Id = h.hotel_id
				left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId
				where hof.is_available =1 and (hov.voucher is null or hov.voucher='' or hof.public=1)
				group by hotelId
				) as hh1 on h.hotel_id=hh1.hotelId
				where h.is_available =1 
				group by h.hotel_id
				having distance < $distance ORDER BY distance asc ";
		
		//echo $query;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
	}
}
