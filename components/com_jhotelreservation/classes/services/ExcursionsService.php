<?php 

class ExcursionsService{
		
	public static function getHotelExcursions($type=-1, $hotelId, $startDate, $endDate, $excursionIds=array(), $nrBooked=array(2), $children=0, $discountCode = null, $checkAvailability = true,$confirmationId=null){
		$db = JFactory::getDBO();
		$excursionFilter ="";
		
		if($hotelId!=-1){
			$excursionFilter =" and e.hotel_id= $hotelId ";
		}
		if(count($excursionIds)>0){
			$excursionFilter .= " and e.id in (";
			foreach( $excursionIds as $id )
			{
				$excursionFilter .= $id.',';
			}
			$excursionFilter = substr($excursionFilter,0,-1);
			$excursionFilter .= ")";
		}
		$availabilityFilter = "";

		if($type!=-1){
			$availabilityFilter = " and e.type = ".$type;
		}
																		
	
		$languageTag = JRequest::getVar( '_lang');
	
		$availabilityFilter .= " and e.is_available = 1";
		if(!$checkAvailability){
			$availabilityFilter="";
		}
		
		//get hotel excursions
		$query="select *,e.id as excursion_id,e.name as excursion_name,e.ignored_dates as excursionIgnoredDates,hlt.content as excursion_main_description,er.name as rate_name, er.id as rate_id ,h.reservation_cost_val AS reservation_cost_val, h.reservation_cost_proc AS reservation_cost_proc
				from #__hotelreservation_excursions e
				inner join #__hotelreservation_excursion_rates er on e.id = er.excursion_id
				inner join #__hotelreservation_hotels h	ON h.hotel_id = e.hotel_id
				left join #__hotelreservation_currencies hc on h.currency_id= hc.currency_id
				left join #__hotelreservation_countries hrc on h.country_id= hrc.country_id
				left join
					(select * from
					 #__hotelreservation_language_translations
					 where type = ".EXCURSION_TRANSLATION."
					 and language_tag = '$languageTag'
					 ) as hlt on hlt.object_id = e.id
				where 1  $availabilityFilter 
				and e.front_display=1 
				$excursionFilter
				order by e.excursion_order";
		//echo($query);
		$db->setQuery( $query );
		$excursions=  $db->loadObjectList();

		$number_days = (strtotime($startDate) - strtotime($endDate) ) / ( 60 * 60 * 24) ;
		//get hotel rates
		$totalPrice = 0;
		$totalExcursionsPrice = 0;
		$excursionIds= array();
		
		foreach($excursions as $excursion){
			$hotelId  = $excursion->hotel_id;
			array_push($excursionIds,$excursion->excursion_id);
			$query="select * from #__hotelreservation_excursion_rate_prices r
			where rate_id=$excursion->rate_id and '$startDate'<= date and date<='$endDate'"  ;
			//dmp($query);
			$db->setQuery( $query );
			$excursionRateDetails =  $db->loadObjectList();
			$excursion->excursionRateDetails = $excursionRateDetails;
				
			//calculate available number of room
			$excursion->nrExcursionsAvailable = $excursion->availability;
			//dmp("id: ".$room->room_id);
			//dmp($room->nrRoomsAvailable);
			$excursion->is_disabled = false;
			$daily = array();
			$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
	
			$isHotelAvailable = true;
			if(!HotelService::isHotelAvailable($hotelId, $startDate,$endDate)  && $checkAvailability){
				$isHotelAvailable = false;
			}
				
			if(!$isHotelAvailable){
				$excursion->is_disabled = true;
			}
			
			$d = strtotime($startDate);
			$currentDayNr =1;
				
			$nr_d =  'excursion_day_'.date("N", $d);
			if( $excursion->{ $nr_d } == 0 ){
				$excursion->is_disabled = true;
			}
	
			//
			$ignoredDates = explode(",",$excursion->excursionIgnoredDates);
			//determine aspects for each day of booking period
			for( $d = strtotime($startDate);$d < strtotime($endDate); ){
				$dayString = date( 'Y-m-d', $d);
				if(array_search($dayString,$ignoredDates))
					$excursion->is_disabled= true;

				//set default price from rate
				$weekDay = date("N",$d);
				$string_price = "price_".$weekDay;
				$dayPrice = $excursion->$string_price;
				$childPrice = $excursion->child_price;
	
				//check if a custom price is set
				if(count($excursionRateDetails)){
					foreach($excursionRateDetails as $excursionRateDetail){
						if($excursionRateDetail->date == $dayString){
							$dayPrice = $excursionRateDetail->price;
							$childPrice = $excursionRateDetail->child_price;
							$excursion->nrExcursionsAvailable = $excursionRateDetail->availability;
						}
					}
				}
	
				$eId = $excursion->excursion_id;
				$nrItemsBooked = 0; 
				if(isset($nrBooked[$eId])){
					$nrItemsBooked = $nrBooked[$eId];
				}
				
				$excursion->nrItemsBooked = $nrItemsBooked;
				//apply current discounts
				$query = "  SELECT
									discount_id,
									discount_name,
									discount_datas,
									discount_datae,
									discount_value as discount_value,
									percent,
									minimum_number_days,
									maximum_number_days,
									minimum_number_persons,
									check_full_code,
									code,
									price_type
								FROM #__hotelreservation_discounts
								WHERE
									is_available = 1 and only_on_offers = 0
									AND FIND_IN_SET( ".$excursion->excursion_id.", excursion_ids  )
									AND	'".date( 'Y-m-d', $d)."' BETWEEN discount_datas AND discount_datae
									AND	IF( minimum_number_days > 0, minimum_number_days <= $currentDayNr, 1 )
									AND	IF( minimum_number_persons > 0, minimum_number_persons <= $nrItemsBooked, 1 )
									AND	IF( maximum_number_days> 0, maximum_number_days >= $currentDayNr, 1 )
									AND	IF( minimum_amount> 0, minimum_amount <= $totalPrice, 1 )
								ORDER BY discount_datas";

				$db->setQuery( $query );
				$discounts =  $db->loadObjectList();
				$selectedDiscounts = array();
				$discountValue = 0;
				$discountPercent = 0;
				$excursion->hasDiscounts = count($discounts) > 0;
				//dmp($discounts);
	
				foreach($discounts as $discount){
					$match = false;
					if(isset($discount->code) && isset($discountCode) && strlen($discount->code)>0){
						if($discount->check_full_code == 1){
							$match = $discountCode==$discount->code;
						}
						else{
							$match = strpos($discountCode,$discount->code)===0;
						}
					}
					if($match || !isset($discount->code) || strlen($discount->code)==0){
						$selectedDiscounts[] = $discount;
						if($discount->percent){
							$discountPercent += $discount->discount_value;
						}else{
							$discountValue += $discount->discount_value;
						}
					}
				}
				//dmp($selectedDiscounts);
					//apply percent
				$dayPrice  = round($dayPrice - $dayPrice * ($discountPercent/100),2);
				//apply value
				$dayPrice = $dayPrice - $discountValue;


				if($excursion->nrExcursionsAvailable ==0){
					$excursion->is_disabled = true;
				}
	
			$day = array(
				'date'				 => $dayString,
				'price'				 => $dayPrice,
				'price_final'		 => $dayPrice,
				'display_price_final'=> $dayPrice,
				'discounts'			 => $selectedDiscounts,
				'nrExcursionsAvailable'   => $excursion->nrExcursionsAvailable
			);
			
			$totalPrice += $dayPrice;
			$currentDayNr += 1;
				
			$totalExcursionsPrice += $dayPrice*$nrItemsBooked;
			$daily[$dayString]=$day;
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		
		$excursion->daily = $daily;
		
		//average price per excursion
		$excursion->excursion_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
		$excursion->pers_total_price = JHotelUtil::fmt($totalPrice/$number_days,2);
		//dmp($excursion->excursion_average_price);

		//set pictures
		$query = "  SELECT *
					FROM #__hotelreservation_excursion_pictures
					WHERE excursion_id = ".$excursion->excursion_id." AND picture_enable = 1
					ORDER BY picture_id
					";
		$db->setQuery( $query );
		$excursion->pictures =  $db->loadObjectList();
	}
	self::setExcursionDisplayPrice($excursions);
	
	if(is_array($nrBooked))
		$excursions = self::applyPackageDiscounts($excursions,$totalExcursionsPrice,implode(",",array_keys($nrBooked)),$startDate);
	$excursions = self::checkExcursionAvailability($excursions,array(),$hotelId, $startDate, $endDate,$confirmationId);
	return $excursions;
	
	}
	public static function applyPackageDiscounts($excursions,$totalPrice, $excursionIds){

		foreach($excursions as $excursion){
			foreach($excursion->daily as $daily){
				$db = JFactory::getDBO();
				$query = "  SELECT
									discount_id,
									discount_name,
									discount_datas,
									discount_datae,
									discount_value as discount_value,
									percent,
									minimum_number_days,
									maximum_number_days,
									minimum_number_persons,
									check_full_code,
									code,
									price_type
									FROM #__hotelreservation_discounts
									WHERE
										is_available = 1 and only_on_offers = 0
									AND FIND_IN_SET( ".$excursion->excursion_id.", excursion_ids  )
									AND	'". $daily['date']."' BETWEEN discount_datas AND discount_datae
									AND	IF( minimum_amount> 0, minimum_amount <= $totalPrice, 1 )
											ORDER BY discount_datas";
			
				$db->setQuery( $query );
				$discounts =  $db->loadObjectList();
				$selectedDiscounts = array();
				$discountValue = 0;
				$discountPercent = 0;
				$excursion->hasDiscounts = count($discounts) > 0;
			
				foreach($discounts as $discount){
					$match = false;
					if(isset($discount->code) && isset($discountCode) && strlen($discount->code)>0){
						if($discount->check_full_code == 1){
							$match = $discountCode==$discount->code;
						}
						else{
							$match = strpos($discountCode,$discount->code)===0;
						}
					}
					if($match || !isset($discount->code) || strlen($discount->code)==0){
						$selectedDiscounts[] = $discount;
						if($discount->percent){
							$discountPercent += $discount->discount_value;
						}else{
							$discountValue += $discount->discount_value;
						}
					}
				}
				//apply percent
				$daily['price'] = round($daily['price'] - $daily['price'] * ($discountPercent/100),2);
				//apply value
				$daily['display_price_final'] =$daily['price_final'] =$daily['price'] = $daily['price'] - $discountValue;
							
				$daily['discounts'] = $selectedDiscounts;
				$excursion->daily[$daily['date']] = $daily;
			}
		}
		return $excursions;
	}
	
	 
	
	public static function checkExcursionAvailability($excursions,$items_reserved, $hotel_id, $datas ,$datae,$confirmationId=null){
		//number of reserved rooms for each room type
		$excursions_reserved = BookingService::getExcursionBookingsPerDay($hotel_id, $datas ,$datae,$confirmationId);
		$temporaryReservedExcursions = BookingService::getReservedRooms($items_reserved);
		//dmp("T");
		//dmp($temporaryReservedExcursions);
		//dmp($confirmationId);
		$ingoreNrExcursions = !empty($confirmationId)?1:0;
		
		if(isset($excursions) && count($excursions)>0){
			foreach($excursions as $excursion){
				//	dmp("NR: ".$room->room_id." ".$room->nrRoomsAvailable);
				//dmp($room->daily);
				foreach($excursion->daily as $day){
	
					$totalNumberExcursionsReserved = 0;
					//dmp($day["data"]);
					if(isset($excursions_reserved[$excursion->excursion_id][$day["date"]]))
						$totalNumberExcursionsReserved = $excursions_reserved[$excursion->excursion_id][$day["date"]];
	
					if(isset($temporaryReservedExcursions[$excursion->excursion_id])){
						$totalNumberExcursionsReserved += $temporaryReservedExcursions[$excursion->excursion_id];
					}
	
					if($day["nrExcursionsAvailable"] <= ($totalNumberExcursionsReserved - $ingoreNrExcursions ))
					{
						$excursion->is_disabled = true;
					}
					else 
						$excursion->capacity = $day["nrExcursionsAvailable"]-($totalNumberExcursionsReserved - $ingoreNrExcursions);
					
				}
			}
		}
		return $excursions;
		//exit;
	}
	
	static function setExcursionDisplayPrice(&$excursions){
		foreach( $excursions as $excursion ){
			$excursion->excursion_average_price = CurrencyService::convertCurrency($excursion->excursion_average_price, "EUR", "EUR");
			foreach( $excursion->daily as $daily )
			{
				$daily['display_price_final'] = CurrencyService::convertCurrency($daily['price_final'], "EUR", "EUR");
			}
		}
	}
	
	static function getExcursionCalendar($excursions, $nrOfDays, $adults,$children,$month, $year, $bookings,$temporaryReservedExcursions, $hotelAvailability){
		$excursionsCalendar = array();
	
		$endDay =  date('t', mktime(0, 0, 0,$month, 1, $year));
		//	dmp("D: ".$endDay);
		foreach ($excursions as $excursion){
			$excursionsInfo = array();
			$index = 1;
	
			$excursionRateDetails = $excursion->excursionRateDetails;
			
			$ignoredDates = explode(",",$excursion->excursionIgnoredDates);
			$nrDays = 0;
			foreach($excursion->daily as &$daily )
			{
				$price = $daily["price"];
				$available = true;
				$totalPrice =0;
				$nrDays = $nrOfDays;
				if($index<=$endDay){

					
					$startDate = date('Y-m-d', mktime(0, 0, 0, $month, $index, $year));
	
					$nrDays = $nrDays<$excursion->min_days ? $excursion->min_days: $nrDays;
	
					$endDate = date('Y-m-d', mktime(0, 0, 0, $month, $index+$nrDays, $year));
	
					$excursion->nrExcursionsAvailable = $excursion->availability;
					$excursion->bookings = 0;
					
					if(array_search($startDate,$ignoredDates))
						$available = false;
					
					$d = strtotime($startDate);
					$nr_d =  'excursion_day_'.date("N", $d);
					if( $excursion->{ $nr_d } == 0 ){
						$available = false;
					}

					for( $i = $index; $i<($index+$nrDays);$i++ )
					{
						$day= date('Y-m-d', mktime(0, 0, 0, $month, $i, $year));
	
						//check if hotel is available
						if($hotelAvailability!=null && !$hotelAvailability[$day]){
							$available = false;
						}
	
						foreach($excursionRateDetails as $excursionRateDetail){
							if($excursionRateDetail->date == $day){
								$excursion->nrExcursionsAvailable = $excursionRateDetail->availability;
							}
						}
	
						$totalNumberExcursionsReserved = 0;
	
						if(isset($bookings[$excursion->excursion_id][$day])){
							$totalNumberExcursionsReserved = $bookings[$excursion->excursion_id][$day];
						}
						if(isset($temporaryReservedExcursions[$excursion->excursion_id]) && (strtotime($temporaryReservedExcursions["datas"])<= strtotime($day) &&  strtotime($day)<strtotime($temporaryReservedExcursions["datae"]) )){
							$totalNumberExcursionsReserved += $temporaryReservedExcursions[$excursion->excursion_id];
						}
	
						//calculate maximum number of bookings per stay interval
						if($excursion->nrExcursionsAvailable<=$totalNumberExcursionsReserved){
							$available = false;
						}
	
						if(isset($excursion->daily[$i-1])){
							$price 	= $excursion->daily[$i-1]['price'];
						}
	
						//echo $p.' <br/>';
						$totalPrice += $price;
					}
	
				}
				//average price per excursion
				$excursion->excursion_average_price = round($totalPrice/$nrDays,2);
				$excursion->pers_total_price = round($totalPrice/$nrDays,2);
				if(JRequest::getVar( 'show_price_per_person')==1){
					$price = $excursion->pers_total_price;
				}else{
					$price = $excursion->excursion_average_price;
				}
	
				$excursionsInfo[] = array("price" => JHotelUtil::fmt($price,2), "isAvailable" => $available);
				$id= $excursion->excursion_id+1000;
				$index++;
			}
			$hotelId = $excursion->hotel_id;
			
			$excursionsCalendar[$id]= JHotelUtil::getAvailabilityCalendar($hotelId, $month, $year, $excursionsInfo, $nrDays, $id);
		}
	
		return $excursionsCalendar;
	}
	
	public static function parseExcursions($excursionItems){
		$excursions = array();
		$excursionIds = array();
		$nrBooked = array();
		foreach($excursionItems as $excursion){
			$excursionId = explode("_",$excursion);
			if(count($excursionId)>1){
				array_push($excursionIds, $excursionId[1]);
				$nrBooked[$excursionId[1]]= $excursionId[2];
			}
		}
		$excursionData = new stdClass();
		$excursionData->excursionIds = $excursionIds;
		
		$excursionData->nrBooked = $nrBooked;

		return $excursionData;
	}
	
	static function getSelectedExcursions($reservedItems, $customPrices, $hotelId, $startDate, $endDate, $roomGuests, $roomGuestsChildren, $discountCode, $checkAvailability = true,$confirmationId=null){
			$selectedExcursions = array();
		if(count($reservedItems)>0){
			$excursionData = ExcursionsService::parseExcursions($reservedItems);
			$selectedExcursions= ExcursionsService::getHotelExcursions(-1,-1, $startDate, $endDate,$excursionData->excursionIds, $excursionData->nrBooked, null , $discountCode, $checkAvailability,$confirmationId);
		}
		ksort($selectedExcursions);
		return $selectedExcursions;
	}
	
	
}


?>