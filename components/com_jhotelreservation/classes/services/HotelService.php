<?php 
JTable::addIncludePath('administrator/components/com_jhotelreservation/tables');
JTable::addIncludePath('components/com_jhotelreservation/tables');
class HotelService{
	
	/**
	 * Check if a hotel is available for a period of time
	 * @param unknown_type $hotelId
	 * @param unknown_type $startDate
	 * @param unknown_type $endDate
	 * 
	 * @return true if available, false if not available
	 */
	public static function isHotelAvailable($hotelId, $startDate, $endDate){
		$hotelTable	= 	JTable::getInstance('hotels','Table', array());
		$hotel = $hotelTable->getHotel($hotelId);
	
		if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>strtotime($startDate) ){
			return false;
		}
	
		if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<strtotime($endDate) ){
			return false;
		}

		$ignoredDays = explode(',',$hotel->ignored_dates);

		if(count($ignoredDays)>0){
			foreach($ignoredDays as $ignoredDay){
	
				if( strtotime($startDate) <= strtotime($ignoredDay) && strtotime($ignoredDay) < strtotime($endDate)){
					return false;
				}
			}
		}
	
		return true;
	}
	
	static function getHotelAvailabilyPerDay($hotelId, $startDate, $endDate){
	
		$hotelTable	= 	JTable::getInstance('hotels','Table', array());
		$hotel = $hotelTable->getHotel($hotelId);
		$availability = array();
	
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			$available = true;
	
			if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>$d ){
				$available = false;
			}
	
			if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<$d ){
				$available = false;
			}
	
			$ignoredDays = explode(',',$hotel->ignored_dates);
			if(count($ignoredDays)>0){
				foreach($ignoredDays as $ignoredDay){
	
					if( $d == strtotime($ignoredDay)){
						$available = false;
					}
				}
			}
	
			$availability[$dayString]=$available;
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	
		return $availability;
	}
	
	public static function getHotel($hotelId)
	{
		$db = JFactory::getDBO();
		// Load the data
		$languageTag 	= JRequest::getVar( '_lang' );
		
		if($hotelId==0 || $hotelId==null){
			$hotel = new stdClass();
			$hotel->hotel_name = "";
			$hotel->hotel_id= 0;
			$hotel->pictures	= array();
			
			$hotel->facilities = array();
			$hotel->chanelManager = null;
			$hotel->paymentOptions = "";
			$hotel->currency = "";
			$hotel->hotel_stars = "";
			$hotel->hotel_address = "";
			$hotel->hotel_city = "";
			$hotel->hotel_county = "";
			$hotel->country_name = "";
			$hotel->hotel_phone= "";
			$hotel->hotel_phone= "";
			$hotel->informations= new stdClass();
			$hotel->informations->check_in = "";
			$hotel->informations->check_out = "";
			$hotel->informations->city_tax = "";
				
			return $hotel;
		}
		//$query = ' SELECT * FROM #__hotelreservation_hotels WHERE hotel_id = '.$id.' AND is_available = 1 ORDER BY hotel_name';
		$query = ' SELECT
					h.*,hlt.content as hotelDescription,
					c1.country_name,
					c2.description	AS hotel_currency, c2.currency_symbol AS currency_symbol
					FROM #__hotelreservation_hotels 			h
					LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
					LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
					LEFT JOIN
					(select * from 
						 #__hotelreservation_language_translations 
						 where type = '.HOTEL_TRANSLATION.'
						 and language_tag = "'.$languageTag.'"
					)as hlt on hlt.object_id = h.hotel_id 
					WHERE h.hotel_id = '.$hotelId.' AND h.is_available = 1
					';
		$db->setQuery( $query );
		$hotel = $db->loadObject();

		$hotel->hotel_name = stripslashes($hotel->hotel_name);

		$hotel->pictures	= array();
			
		$query = "  SELECT	*
				FROM #__hotelreservation_hotel_pictures
				WHERE hotel_id = ".$hotelId." AND hotel_picture_enable = 1
				ORDER BY hotel_picture_id
				";
		//dmp($query);
		$db->setQuery( $query );
		$hotel->pictures =  $db->loadObjectList();

		$hotel->facilities = array();
		$query = "  SELECT	hf.*
					FROM #__hotelreservation_hotel_facilities hf
					inner join  #__hotelreservation_hotel_facility_relation hfc on hf.id = hfc.facilityId
					WHERE hfc.hotelId = ".$hotelId."
					ORDER BY hf.name";
		//dmp($query);
		$db->setQuery( $query );
		$hotel->facilities = $db->loadObjectList();

		$hotel->types = array();
		$query = "  SELECT	hf.*
					FROM #__hotelreservation_hotel_types hf
					inner join  #__hotelreservation_hotel_type_relation hfc on hf.id = hfc.typeId
					WHERE hfc.hotelId = ".$hotelId."
					ORDER BY hf.name";
		//dmp($query);
		$db->setQuery( $query );
		$hotel->types = $db->loadObjectList();
		//dmp($hotel->types);

		
		$hotel->chanelManager = null;
		$query = "  SELECT	* from #__hotelreservation_hotel_channel_manager where hotel_id = $hotelId";			
		//dmp($query);
		$db->setQuery( $query );
		$hotel->chanelManager = $db->loadObject();
		
		$hotel->reviewAnwersScore = self::getHotelReviewScore($hotelId);
		$hotel->reviews = self::getHotelReviews($hotelId);

		$informationsTable = JTable::getInstance('ManageHotelInformations','Table',array());
		$hotel->informations =  $informationsTable->getHotelInformations($hotelId);
		$cancellationText ='';
		if($hotel->informations->uvh_agree==1){
			$cancellationText = JText::_('LNG_CANCELATION_UVH',true).' ';
		}

		if(count($hotel->types)==0){
			$type = new stdClass();
			$type->id=0;
			$hotel->types[0]=$type;
		}

		$cancellationText = (isset($hotel->types) && $hotel->types[0]->id == PARK_TYPE_ID) ? "": $cancellationText.str_replace("<<days>>", $hotel->informations->cancellation_days, JText::_('LNG_CANCELLATION_RULE',true)).' ';

		$hotel->informations->cancellation_conditions = $cancellationText.$hotel->informations->cancellation_conditions;
		$informationsTable =JTable::getInstance('ManageHotelInformations',"Table",array());
		$hotel->paymentOptions =  $informationsTable->getHotelPaymentOptions($hotelId);

		return $hotel;
	}
	
	public static function getHotelReviewScore($hotelId){
		$reviewAnswersTable	= JTable::getInstance('ReviewAnswers','Table', array());
		return $reviewAnswersTable->getAverageReviewAnswersScoreByHotel($hotelId);
	}
	
	public static function getHotelReviews($hotelId){
		$reviewAnswersTable	= JTable::getInstance('ReviewAnswers','Table', array());
		return $reviewAnswersTable->getHotelReviews($hotelId);
	}
	
	/**
	 * Get all rooms from specified hotel. It calculates also the price for the room per day.
	 *
	 * @param $hotelId
	 * @param $startDate
	 * @param $endDate
	 * @param $roomIds
	 * @param $adults
	 * @param $children
	 * @return available room
	 */
	public static function getHotelRooms($hotelId, $startDate, $endDate, $roomIds=array(), $adults=2, $children=0, $discountCode = null, $checkAvailability = true,$confirmationId=null){
		$db = JFactory::getDBO();
		$roomFilter ="";
		if(count($roomIds)>0 && $roomIds[0]!=null){
			$roomFilter = " and r.room_id in (";
			foreach( $roomIds as $id )
			{
				$roomFilter .= $id.',';
			}
			$roomFilter = substr($roomFilter,0,-1);
			$roomFilter .= ")";
		}

		$isHotelAvailable = true;
		if(!self::isHotelAvailable($hotelId, $startDate,$endDate) && $checkAvailability){
			$isHotelAvailable = false;
		}
		$languageTag = JRequest::getVar( '_lang');

		$availabilityFilter = "and r.is_available = 1";
		if(!$checkAvailability){
			$availabilityFilter="";
		}
		
		//get hotel rooms
		$query="select *,hlt.content as room_main_description, rr.id as rate_id ,h.reservation_cost_val AS reservation_cost_val, h.reservation_cost_proc AS reservation_cost_proc
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr on r.room_id = rr.room_id
				inner join #__hotelreservation_hotels h	ON h.hotel_id = r.hotel_id
				left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".ROOM_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlt on hlt.object_id = r.room_id 
				where 1  $availabilityFilter and
				#r.front_display=1 and
				r.hotel_id= $hotelId $roomFilter
		        order by r.room_order";
		//echo($query);
		$db->setQuery( $query );
		$rooms =  $db->loadObjectList();

		$number_days = (strtotime($startDate) - strtotime($endDate) ) / ( 60 * 60 * 24) ;
		//dmp($rooms);
		//get hotel rates
		foreach($rooms as $room){
			$query="select * from #__hotelreservation_rooms_rate_prices r
					where rate_id=$room->rate_id and '$startDate'<= date and date<='$endDate'"  ;
			//dmp($query);
			$db->setQuery( $query );
			$roomRateDetails =  $db->loadObjectList();
			$room->roomRateDetails = $roomRateDetails;

			//calculate available number of room
			$room->nrRoomsAvailable = $room->availability;
			$room->lock_for_departure = false;
			//dmp("id: ".$room->room_id);
			//dmp($room->nrRoomsAvailable);
			$room->is_disabled = false;
			$daily = array();
			$totalPrice = 0;
			$currentDayNr =1; 
			$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;


			if(!$isHotelAvailable){
				$room->is_disabled = true;
			}

			//check if arrival date is disabled
			if(count($roomRateDetails)){
				foreach($roomRateDetails as $roomRateDetail){
					if($roomRateDetail->date == $startDate){
						if($roomRateDetail->lock_arrival == 1){
							$room->is_disabled = true;
						}
						$room->max_days = $roomRateDetail->max_days;
						$room->min_days = $roomRateDetail->min_days;
					}

					if($roomRateDetail->date == $endDate){
						if($roomRateDetail->lock_departure == 1){
							$room->is_disabled = true;
							$room->lock_for_departure = true;
						}
					}
				}
			}

			//determine aspects for each day of booking period
			for( $d = strtotime($startDate);$d < strtotime($endDate); ){
				$dayString = date( 'Y-m-d', $d);

				//set default price from rate
				$weekDay = date("N",$d);
				$string_price = "price_".$weekDay;
				$dayPrice = $room->$string_price;
				$childPrice = $room->child_price;
				$extraPersonPrice = $room->extra_pers_price;

				//check if a custom price is set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $dayString){
							$dayPrice = $roomRateDetail->price;
							$extraPersonPrice = $roomRateDetail->extra_pers_price;
							$childPrice = $roomRateDetail->child_price;
						}
					}
				}
				
				if($room->price_type==1){
					$totalAdults = ($adults<=$room->base_adults)?$adults:$room->base_adults;
					$dayPrice = $dayPrice * $totalAdults + $childPrice*$children;
				}
				//add extra person cost - if it is the case
				if($adults > $room->base_adults){
					$dayPrice += ($adults - $room->base_adults) *  $extraPersonPrice;
				}
				//for single use
				//if the price is per person apply single supplement , if is for room apply discount
				if($adults==1 && $children==0){
					if($room->price_type==1){//per person
						$dayPrice = $dayPrice + $room->single_balancing;
					}else{
						$dayPrice = $dayPrice - $room->single_balancing;
					}
				}

				//check if there is a custom price set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						//get room availability - if rate details are set default settings are ignored
						if($roomRateDetail->date == $dayString){
							$room->nrRoomsAvailable = $roomRateDetail->availability;
						}

						//set single use price
						if($roomRateDetail->date == $dayString && $room->price_type==1 && $adults==1 && $children==0){
							$dayPrice = $roomRateDetail->single_use_price;
						}
					}
				}
				
				//apply current discounts
				$query = "  SELECT
								discount_id,
								discount_name,
								discount_datas,
								discount_datae,
								if(price_type = 1 , discount_value * $adults, discount_value) as discount_value,
								percent,
								minimum_number_days,
								maximum_number_days,
								minimum_number_persons,
								check_full_code,
								code,
								price_type
							FROM #__hotelreservation_discounts
							WHERE is_available = 1 and only_on_offers = 0
								AND	FIND_IN_SET( ".$room->room_id.", discount_room_ids  )
								AND '".date( 'Y-m-d', $d)."' BETWEEN discount_datas AND discount_datae
								AND	IF( minimum_number_days > 0, minimum_number_days <= $currentDayNr, 1 )
								AND	IF( minimum_number_persons > 0, minimum_number_persons <= $adults, 1 )
								AND	IF( maximum_number_days> 0, maximum_number_days >= $currentDayNr, 1 )
								AND	IF( minimum_amount> 0, minimum_amount <= $totalPrice, 1 )
							ORDER BY discount_datas
							";

				$db->setQuery( $query );
				$discounts =  $db->loadObjectList();
				$selectedDiscounts = array();
				$discountValue = 0;
				$discountPercent = 0;
				$room->hasDiscounts = count($discounts) > 0;
				//dmp($discounts); 
				
				foreach($discounts as $discount){
					$match = false;
					if(isset($discount->code) && isset($discountCode) && strlen($discount->code)>0){
						if($discount->check_full_code == 1){
							$match = $discountCode==$discount->code;
						}else{
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

				
				if($room->nrRoomsAvailable ==0){
					$room->is_disabled = true;
				}

				$day = array(
						'date'				 => $dayString,
						'price'				 => $dayPrice,
						'price_final'		 => $dayPrice,
						'display_price_final'=> $dayPrice,
						'discounts'			 => $selectedDiscounts,
						'nrRoomsAvailable'   => $room->nrRoomsAvailable
				);

				$totalPrice += $dayPrice;
				$currentDayNr += 1;
				$daily[$dayString]=$day;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}

			//$this->itemRoomsCapacity[$room->room_id ] = array($room->nrRoomsAvailable,	1);

			$room->daily = $daily;

			//average price per room
			$room->room_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
			$room->pers_total_price = JHotelUtil::fmt($totalPrice/($adults+$children),2);
			//dmp($room->room_average_price);
				
			//set pictures
			$query = "  SELECT *
						FROM #__hotelreservation_rooms_pictures
						WHERE room_id = ".$room->room_id." AND room_picture_enable = 1
						ORDER BY room_picture_id
						";
			$db->setQuery( $query );
			$room->pictures =  $db->loadObjectList();
			$room->offer_id = 0;
			$room->adults = $adults;
			$room->children = $children;
		}
		//dmp($rooms);
		//dmp($this->itemRoomsCapacity);
		self::setRoomDisplayPrice($rooms);
		self::checkRoomAvailability($rooms,array(),$hotelId, $startDate, $endDate,$confirmationId);
		
		return $rooms;

	}
	
	/**
	* Get all rooms available. It calculates also the price for the room per day.
	*
	* @param $hotelId
	* @param $startDate
	* @param $endDate
	* @param $roomIds
	* @param $adults
	* @param $children
	* @return available room
	*/
	
	function roomSorter($a, $b){
		$a = $a->room_average_display_price;
		$b = $b->room_average_display_price;
	
		if ($a == $b) {
			return 0;
		}
		 
		return ($a < $b) ? -1 : 1;
	}
	
	public static function getAllRooms($rooms,$startDate,$endDate,$adults=2, $children=0,$orderByPrice = false,$discountCode = null, $checkAvailability = true,$confirmationId=null){
		$db = JFactory::getDBO();

		$number_days = (strtotime($startDate) - strtotime($endDate) ) / ( 60 * 60 * 24) ;
		//get hotel rates
		foreach($rooms as $room){
			$query="select * from #__hotelreservation_rooms_rate_prices r
						where rate_id=$room->rate_id and '$startDate'<= date and date<='$endDate'"  ;
			//dmp($query);
			$db->setQuery( $query );
			$roomRateDetails =  $db->loadObjectList();
			$room->roomRateDetails = $roomRateDetails;
	
			//calculate available number of room
			$room->nrRoomsAvailable = $room->availability;
			$room->lock_for_departure = false;
			//dmp("id: ".$room->room_id);
			//dmp($room->nrRoomsAvailable);
			$room->is_disabled = false;
			$daily = array();
			$totalPrice = 0;
			$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
	
			$isHotelAvailable = true;
			if(!self::isHotelAvailable($room->hotel_id, $startDate,$endDate) && $checkAvailability){
				$isHotelAvailable = false;
			}
			if(!$isHotelAvailable){
				$room->is_disabled = true;
			}
	
			//check if arrival date is disabled
			if(count($roomRateDetails)){
				foreach($roomRateDetails as $roomRateDetail){
					if($roomRateDetail->date == $startDate){
						if($roomRateDetail->lock_arrival == 1){
							$room->is_disabled = true;
						}
						$room->max_days = $roomRateDetail->max_days;
						$room->min_days = $roomRateDetail->min_days;
					}
	
					if($roomRateDetail->date == $endDate){
						if($roomRateDetail->lock_departure == 1){
							$room->is_disabled = true;
							$room->lock_for_departure = true;
						}
					}
				}
			}
	
			//determine aspects for each day of booking period
			for( $d = strtotime($startDate);$d < strtotime($endDate); ){
				$dayString = date( 'Y-m-d', $d);
	
				//set default price from rate
				$weekDay = date("N",$d);
				$string_price = "price_".$weekDay;
				$dayPrice = $room->$string_price;
				$childPrice = $room->child_price;
				$extraPersonPrice = $room->extra_pers_price;
	
				//check if a custom price is set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $dayString){
							$dayPrice = $roomRateDetail->price;
							$extraPersonPrice = $roomRateDetail->extra_pers_price;
							$childPrice = $roomRateDetail->child_price;
						}
					}
				}
	
				if($room->price_type==1){
					$totalAdults = ($adults<=$room->base_adults)?$adults:$room->base_adults;
					$dayPrice = $dayPrice * $totalAdults + $childPrice*$children;
				}
				//add extra person cost - if it is the case
				if($adults > $room->base_adults){
					$dayPrice += ($adults - $room->base_adults) *  $extraPersonPrice;
				}
				//for single use
				//if the price is per person apply single supplement , if is for room apply discount
				if($adults==1 && $children==0){
					if($room->price_type==1){
						//per person
						$dayPrice = $dayPrice + $room->single_balancing;
					}else{
						$dayPrice = $dayPrice - $room->single_balancing;
					}
				}
	
				//check if there is a custom price set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						//get room availability - if rate details are set default settings are ignored
						if($roomRateDetail->date == $dayString){
							$room->nrRoomsAvailable = $roomRateDetail->availability;
						}
	
						//set single use price
						if($roomRateDetail->date == $dayString && $room->price_type==1 && $adults==1 && $children==0){
							$dayPrice = $roomRateDetail->single_use_price;
						}
					}
				}
	
				//apply current discounts
				$query = "  SELECT
								discount_id,
								discount_name,
								discount_datas,
								discount_datae,
								if(price_type = 1 , discount_value * $adults, discount_value) as discount_value,
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
								AND
								FIND_IN_SET( ".$room->room_id.", discount_room_ids  )
								AND
								'".date( 'Y-m-d', $d)."' BETWEEN discount_datas AND discount_datae
								AND
								IF( minimum_number_days > 0, minimum_number_days <= $number_days, 1 )
								AND
								IF( minimum_number_persons > 0, minimum_number_persons <= $adults, 1 )
								ORDER BY discount_datas
								";
	
				$db->setQuery( $query );
				$discounts =  $db->loadObjectList();
				$selectedDiscounts = array();
				$discountValue = 0;
				$discountPercent = 0;
				$room->hasDiscounts = count($discounts) > 0;
				//dmp($discounts);
	
				foreach($discounts as $discount){
					$match = false;
					if(isset($discount->code) && isset($discountCode) && strlen($discount->code)>0){
						if($discount->check_full_code == 1){
							$match = $discountCode==$discount->code;
						}else{
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
	
	
				if($room->nrRoomsAvailable ==0){
					$room->is_disabled = true;
				}
	
				$day = array(
							'date'				 => $dayString,
							'price'				 => $dayPrice,
							'price_final'		 => $dayPrice,
							'display_price_final'=> $dayPrice,
							'discounts'			 => $selectedDiscounts,
							'nrRoomsAvailable'   => $room->nrRoomsAvailable
				);
	
				$totalPrice += $dayPrice;
				$daily[$dayString]=$day;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
	
			//$this->itemRoomsCapacity[$room->room_id ] = array($room->nrRoomsAvailable,	1);
	
			$room->daily = $daily;
	
			//average price per room
			$room->room_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
			$room->pers_total_price = JHotelUtil::fmt($totalPrice/($adults+$children),2);
			//dmp($room->room_average_price);
	
			//set pictures
			$query = "  SELECT *
							FROM #__hotelreservation_rooms_pictures
							WHERE room_id = ".$room->room_id." AND room_picture_enable = 1
							ORDER BY room_picture_id
							";
			$db->setQuery( $query );
			$room->pictures =  $db->loadObjectList();
			$room->offer_id = 0;
			$room->adults = $adults;
			$room->children = $children;
		}
		//dmp($rooms);
		//dmp($this->itemRoomsCapacity);
		self::setRoomDisplayPrice($rooms);
		self::checkRoomListingAvailability($rooms,array(), $startDate, $endDate,$confirmationId);
		
		if($orderByPrice)
			usort($rooms, array("HotelService", "roomSorter"));
		
		return $rooms;
	
	}

	
	public static function getHotelOffers($hotelId, $startDate, $endDate, $offersIds=array(), $adults=2, $children=0, $discountCode = null, $checkAvailability = true,$confirmationId=null){
		$db = JFactory::getDBO();
		$offerFilter ="";
		if(count($offersIds)>0){
			$offerFilter = " and ";

			foreach( $offersIds as $id )
			{
				$values = explode("|",$id);
				$offerFilter .= "(hor.offer_id =". $values[0].' and ';
				$offerFilter .= "hor.room_id =".$values[1].' )';
			}
		}
		//dmp($offerFilter);

		$isHotelAvailable = true;
		if(!self::isHotelAvailable($hotelId, $startDate,$endDate)  && $checkAvailability){
			$isHotelAvailable = false;
		}
		//dmp($startDate);
		//dmp($endDate);
		//dmp($isHotelAvailable);
		
		$availabilityFilter = "and	o.is_available = 1 and r.is_available = 1 ";
		if(!$checkAvailability){
			$availabilityFilter="";
		}
		$languageTag = JRequest::getVar( '_lang');
		
		
		//get hotel rooms
		$query="select r.*,o.*,hlct.content as offer_content,hlit.content as offer_other_info,hlt.content as offer_description,hlost.content as offer_short_description,ot.*, ot.id as rate_id, rr.availability as availability, rr.id as room_rate_id,GROUP_CONCAT(hov.voucher) as vouchers
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr 			on r.room_id = rr.room_id
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
				inner join #__hotelreservation_offers_rates 			ot 		ON ot.offer_id	= hor.offer_id and ot.room_id = hor.room_id
				left join #__hotelreservation_offers_vouchers hov on hov.offerId = o.offer_id
				left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".OFFER_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlt on hlt.object_id = o.offer_id
				left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".OFFER_SHORT_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlost on hlost.object_id = o.offer_id
				left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".OFFER_CONTENT_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlct on hlct.object_id = o.offer_id
				left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".OFFER_INFO_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlit on hlit.object_id = o.offer_id
					
					
				where o.hotel_id= $hotelId $offerFilter
				$availabilityFilter
				and	IF(
				o.offer_datasf <> '0000-00-00'
				AND
				o.offer_dataef <> '0000-00-00',
				DATE(now()) BETWEEN o.offer_datasf  AND o.offer_dataef,
				IF(
				o.offer_datasf <> '0000-00-00',
				DATE(now()) >= o.offer_datasf,
				DATE(now()) <=o.offer_dataef
				)
				)
				group by hor.offer_room_id";
		//echo($query);

		$db->setQuery( $query );
		$offers =  $db->loadObjectList();
		$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
		//dmp($offers);
		//get hotel rates
		if(count($offers)){
			foreach($offers as $offer){
				//get offer custom rate settings
				$query="select * from #__hotelreservation_offers_rate_prices r
					where rate_id=$offer->rate_id and '$startDate'<= date and date<='$endDate'" ;
				$db->setQuery( $query );
				$offerRateDetails =  $db->loadObjectList();
			
				$offer->offerRateDetails = $offerRateDetails;
				//dmp($offerRateDetails);
				//get room custom rate settings
				$query="select * from #__hotelreservation_rooms_rate_prices r
					where rate_id=$offer->room_rate_id and '$startDate'<= date and date<='$endDate'" ;
				//dmp($query);
				$db->setQuery( $query );
				$roomRateDetails =  $db->loadObjectList();
				
				$offer->roomRateDetails = $roomRateDetails;
				//dmp($offer->roomRateDetails);

				//calculate available number of room
				$offer->nrRoomsAvailable = $offer->availability;

				$offer->is_disabled = false;
				$offer->lock_for_departure = false;
				//dmp($offer->vouchers);

				//set voucher as array
				if(isset($offer->vouchers))
					$offer->vouchers = explode(',', $offer->vouchers);
				//dmp($offer->vouchers);
				//check if offer can start on arrival date
				$d = strtotime($startDate);
				$nr_d =  'offer_day_'.date("N", $d);
				if( $offer->{ $nr_d } == 0 ){
					$offer->is_disabled = true;
					//dmp("disable");
				}

				$daily = array();
				$totalPrice = 0;
				$offer_max_nights	= $offer->offer_max_nights;


				if(!$isHotelAvailable){
					$offer->is_disabled = true;
					//dmp("disable");
				}
				//check if arrival date is disabled on arrival date
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $startDate && $roomRateDetail->lock_arrival == 1){
							$offer->is_disabled = true;
							//dmp("disable");
						}

						if($roomRateDetail->date == $endDate){
							if($roomRateDetail->lock_departure == 1){
// 								dmp("disable");
								$offer->is_disabled = true;
								$offer->lock_for_departure = true;
							}
						}
					}
				}
				$dayCounter = 0;
				for( $d = strtotime($startDate);$d < strtotime($endDate); ){
					$dayString = date( 'Y-m-d', $d);
					$dayCounter++;
					//dmp($dayString);
					//set default price from rate
					$weekDay = date("N",$d);
					$string_price = "price_".$weekDay;
					$dayPrice = $offer->$string_price;
					$childPrice = $offer->child_price; 

					$extraPersonPrice = $offer->extra_pers_price;
					//dmp($extraPersonPrice);
					//check if there is a custom price set
					if(count($offerRateDetails)){
						foreach($offerRateDetails as $offerRateDetail){
							if($offerRateDetail->date == $dayString){
								$dayPrice = $offerRateDetail->price;
								$extraPersonPrice = $offerRateDetail->extra_pers_price;
								$childPrice = $offerRateDetail->child_price;
								//	dmp($dayString . ": ". $dayPrice);
							}
						}
					}
					//dmp($extraPersonPrice);

					//dmp($dayPrice);
					//check if we have an extra night
					$isExtraNight = false;
					if( $offer_max_nights <= 0  ){
						$dayPrice = $offer->extra_night_price;
						$isExtraNight = true;
						//dmp("extra price: ".$offer->extra_night_price);
					}
					
					if($offer->price_type==1){
						$totalAdults = ($adults<=$offer->base_adults)?$adults:$offer->base_adults;
						$dayPrice = $dayPrice * $totalAdults+$childPrice * $children;
					}
					//add extra person cost - if it is the case
					if($adults > $offer->base_adults){
						$dayPrice += ($adults - $offer->base_adults) *  $extraPersonPrice;
					}
						
						
					$nrDays = JHotelUtil::getNumberOfDays($startDate, $endDate);
					//dmp($nrDays);
					if( $offer->offer_min_nights > $nrDays ){
						$offer->is_disabled = true;
					}
						
						
					//for single use
					//if the price is per person apply single supplement , if is for room apply discount
					if($adults==1 && $children==0){
						if(!$isExtraNight){
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing;
								//dmp("add balancing: ".$offer->single_balancing." -> ".$dayPrice);
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing;
							}
						}else if($offer->price_type_day==1){
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing/$offer->offer_min_nights;
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing/$offer->offer_min_nights;
							}
						}else if($offer->price_type_day==0){ 
							
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing;
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing;
							}
						}
					}
						
					//check if offer is available on stay period
					if(!(strtotime($offer->offer_datas) <= $d && $d<=strtotime($offer->offer_datae) )){
						$offer->is_disabled = true;
					}

					//get the minimum availability in the selected period
					if(count($roomRateDetails)){
						foreach($roomRateDetails as $roomRateDetail){
							//get room availability - if rate details are set default settings are ignored
							if($roomRateDetail->date == $dayString){
								$offer->nrRoomsAvailable = $roomRateDetail->availability;
							}
						}
					}
						
					if( $offer_max_nights > 0  ){
						if(count($offerRateDetails)){
							foreach($offerRateDetails as $offerRateDetail){
								//set single use price
								if($offerRateDetail->date == $dayString && $offer->price_type==1 && $adults==1 && $children==0){
									$dayPrice = $offerRateDetail->single_use_price;
								}
							}
						}
					}
						
					//apply current discounts
					$query = "  SELECT
								discount_id,
								discount_name,
								discount_datas,
								discount_datae,
								if(price_type = 1 , discount_value * $adults, discount_value) as discount_value,
								percent,
								minimum_number_days,
								minimum_number_persons,
								maximum_number_days,
								check_full_code,
								price_type,
								code
								FROM #__hotelreservation_discounts
								WHERE
								is_available = 1
								AND
								FIND_IN_SET( ".$offer->room_id.", discount_room_ids  )
								AND
								FIND_IN_SET( ".$offer->offer_id.", offer_ids  )
								AND
								'".date( 'Y-m-d', $d)."' BETWEEN discount_datas AND discount_datae
								AND
								IF( minimum_number_days > 0, minimum_number_days <= $number_days, 1 )
								AND
								IF( minimum_number_persons > 0, minimum_number_persons <= $adults, 1 )
								ORDER BY discount_datas
								";
					//dmp($query);
					$db->setQuery( $query );
					$discounts =  $db->loadObjectList();
					$offer->hasDiscounts = count($discounts) > 0;
					//dmp($discounts);
					
					$selectedDiscounts = array();
					$discountValue = 0;
					$discountPercent = 0;
					if(count($discounts)>0){
						foreach($discounts as $discount){
							if($dayCounter<=$discount->maximum_number_days){
								$match = false;
								if(isset($discount->code) && isset($discountCode)){
									if($discount->check_full_code == 1){
										$match = strcasecmp($discountCode,$discount->code) == 0;
									}else{
										$match = stripos($discountCode,$discount->code)===0;
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
						}
					}
						
					if($offer->nrRoomsAvailable ==0){
						$offer->is_disabled = true;
					}
						

					//apply percent
					$dayPrice  = round($dayPrice - $dayPrice * ($discountPercent/100),2);
					//apply value
					$dayPrice = $dayPrice - $discountValue;

					$day = array(
							'date'				  => $dayString,
							'price'				  => $dayPrice,
							'price_final'		  => $dayPrice,
							'display_price_final' => $dayPrice,
							'discounts' 		  => $selectedDiscounts,
							'nrRoomsAvailable'    => $offer->nrRoomsAvailable,
							'isExtraNight'		  => $isExtraNight
					);
						
					$daily[$dayString]=$day;
					$totalPrice += $dayPrice;
					$offer_max_nights--;
					$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
				}
				//dmp($offer->offer_name);
				//dmp($offer->nrRoomsAvailable);

				//$this->itemRoomsCapacity[$offer->room_id ] = array($offer->nrRoomsAvailable, 1);

				$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
				$offer->daily = $daily;

				//average price per offer
				$offer->offer_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
				$offer->pers_total_price = JHotelUtil::fmt($totalPrice/($adults+$children),2);

				if($offer->price_type_day == 1){
					$offer->offer_average_price = $daily[$startDate]["price"];
					$offer->pers_total_price = $daily[$startDate]["price"]/($adults+$children);
					
					foreach($daily as $day){
						if($day["isExtraNight"]){
							$offer->pers_total_price += $day["price"]/($adults+$children);
							$offer->offer_average_price += $day["price"];
						}
					}
					
				}

				//load offers pictures
				$query = "  SELECT *
							FROM #__hotelreservation_offers_pictures
							WHERE offer_id = ".$offer->offer_id." AND offer_picture_enable = 1
							ORDER BY offer_picture_id";
				
				$db->setQuery( $query );
				$offer->pictures =  $db->loadObjectList();
			
				$offer->adults = $adults;
				$offer->children = $children;
			}
		}
		
		self::setOfferDisplayPrice($offers);
		self::checkRoomAvailability($offers,array(),$hotelId, $startDate, $endDate,$confirmationId);
		
		return $offers;
	}
	
	public static function checkRoomAvailability(&$rooms,$items_reserved, $hotel_id, $datas ,$datae,$confirmationId=null){
		//number of reserved rooms for each room type
		$rooms_reserved = BookingService::getNumberOfBookingsPerDay($hotel_id, $datas ,$datae,$confirmationId);
		//dmp($rooms);
		$temporaryReservedRooms = BookingService::getReservedRooms($items_reserved);
		//dmp("T");
		//dmp($temporaryReservedRooms);
		//dmp($confirmationId);
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				//	dmp("NR: ".$room->room_id." ".$room->nrRoomsAvailable);
				//dmp($room->daily);
				foreach($room->daily as $day){
		
					$totalNumberRoomsReserved = 0;
					//dmp($day["data"]);
					if(isset($rooms_reserved[$room->room_id][$day["date"]]))
						$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["date"]];
		
					if(isset($temporaryReservedRooms[$room->room_id])){
						$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
					}
					
					if($day["nrRoomsAvailable"] < $totalNumberRoomsReserved)
					{
						$room->is_disabled = true;
						//exit;
						//dmp($room);
						//dmp("disable");
					}
				}
			}
		}
		//exit;
	}
	
	public static function checkAvailability($hotelId, $startDate, $endDate){
		$rooms = self::getHotelRooms($hotelId, $startDate, $endDate);
		$isAvailable = false;
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				if(!$room->is_disabled){
					$isAvailable = true;
					break;
				}
			}
		}
		
		return $isAvailable;
	}
	
	
	static function setRoomDisplayPrice(&$rooms){
		foreach( $rooms as &$room ){
			$room->room_average_display_price = CurrencyService::convertCurrency($room->room_average_price, "EUR", "EUR");
			foreach( $room->daily as &$daily )
			{
				$daily['display_price_final'] = CurrencyService::convertCurrency($daily['price_final'], "EUR", "EUR");
			}
		}
	}
	
	static function setOfferDisplayPrice(&$offers){
		foreach( $offers as &$offer ){
			$offer->offer_average_display_price = CurrencyService::convertCurrency($offer->offer_average_price, "EUR", "EUR");
			foreach( $offer->daily as &$daily )
			{
				$daily['display_price_final'] = CurrencyService::convertCurrency($daily['price_final'], "EUR", "EUR");
			}
		}
	}
	
	static function getRoomsCalendar($rooms, $nrOfDays, $adults,$children,$month, $year, $bookings,$temporaryReservedRooms, $hotelAvailability){
		$roomsCalendar = array();
	
		$endDay =  date('t', mktime(0, 0, 0,$month, 1, $year));
		//	dmp("D: ".$endDay);
		foreach ($rooms as $room){
			$roomsInfo = array();
			$index = 1;
				
			$roomRateDetails = $room->roomRateDetails;
			//dmp($roomRateDetails);
			$nrDays = 0;
			foreach($room->daily as &$daily )
			{
				$price = $daily["price"];
				$available = true;
				$totalPrice =0;
				$nrDays = $nrOfDays;
	
				if($index<=$endDay){
					$startDate = date('Y-m-d', mktime(0, 0, 0, $month, $index, $year));
						
					if(count($roomRateDetails)){
						foreach($roomRateDetails as $roomRateDetail){
							if($roomRateDetail->date == $startDate){
								if($roomRateDetail->lock_arrival == 1){
									$available = false;
								}
								$room->max_days = $roomRateDetail->max_days;
								$room->min_days = $roomRateDetail->min_days;
							}
						}
					}
						
					$nrDays = $nrDays<$room->min_days ? $room->min_days: $nrDays;
						
					$endDate = date('Y-m-d', mktime(0, 0, 0, $month, $index+$nrDays, $year));
						
					$room->nrRoomsAvailable = $room->availability;
					$room->bookings = 0;
					for( $i = $index; $i<($index+$nrDays);$i++ )
					{
						$day= date('Y-m-d', mktime(0, 0, 0, $month, $i, $year));
	
						//check if hotel is available
						if(!$hotelAvailability[$day]){
							$available = false;
						}
	
						foreach($roomRateDetails as $roomRateDetail){
							if($roomRateDetail->date == $day){
								$room->nrRoomsAvailable = $roomRateDetail->availability;
							}
						}
	
						$totalNumberRoomsReserved = 0;
	
						if(isset($bookings[$room->room_id][$day])){
							$totalNumberRoomsReserved = $bookings[$room->room_id][$day];
						}
						if(isset($temporaryReservedRooms[$room->room_id]) && (strtotime($temporaryReservedRooms["datas"])<= strtotime($day) &&  strtotime($day)<strtotime($temporaryReservedRooms["datae"]) )){
							$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
						}
	
						//calculate maximum number of bookings per stay interval
						if($room->nrRoomsAvailable<=$totalNumberRoomsReserved){
							$available = false;
						}
	
						if(isset($room->daily[$i-1])){
							$price 	= $room->daily[$i-1]['price'];
						}
	
						//echo $p.' <br/>';
						$totalPrice += $price;
					}
						
				}
				//average price per room
				$room->room_average_price = round($totalPrice/$nrDays,2);
				$room->pers_total_price = round($totalPrice/($adults+$children),2);
				if(JRequest::getVar( 'show_price_per_person')==1){
					$price = $room->pers_total_price;
				}else{
					$price = $room->room_average_price;
				}
	
				$roomsInfo[] = array("price" => JHotelUtil::fmt($price,2), "isAvailable" => $available);
				$id= $room->room_id;
				$index++;
			}
			$hotelId = $room->hotel_id;
			$roomsCalendar[$id]= JHotelUtil::getAvailabilityCalendar($hotelId, $month, $year, $roomsInfo, $nrDays, $id);
		}
	
		return $roomsCalendar;
	}
	
	static function getOffersCalendar($offers, $initialNrDays, $adults,$children ,  $month, $year, $bookings, $temporaryReservedRooms, $hotelAvailability){
		$offersCalendar = array();
		$endDay =  date('t', mktime(0, 0, 0,$month, 1, $year));
		//	dmp("D: ".$endDay);
		if($adults==" " || $adults=="")
		$adults=2;
		
		if(count($offers)){
			foreach($offers as $offer){
	
				$roomsInfo = array();
				//dmp($room->daily);
				$index = 1;
	
				$daily = array();
	
				$nrDays = $initialNrDays;
				if($nrDays<$offer->offer_min_nights){
					$nrDays = $offer->offer_min_nights;
				}
				//dmp($offer->offer_name);
					
				//dmp("days: ".$nrDays);
	
				$offerRateDetails = $offer->offerRateDetails;
				$roomRateDetails = $offer->roomRateDetails;
				//dmp($offerRateDetails);
				$firstDayPrice = 0;	
				
				foreach($offer->daily as &$daily ){
					$available = true;
					$totalPrice = 0;
					$offer_max_nights	= $offer->offer_max_nights;
					$extraNightPrice = 0;
						
					if($index<=$endDay){
						$startDate = date('Y-m-d', mktime(0, 0, 0, $month, $index, $year));
						$endDate = date('Y-m-d', mktime(0, 0, 0, $month, $index+$nrDays, $year));
	
						$d = strtotime($startDate);
						$nr_d =  'offer_day_'.date("N", $d);
						if( $offer->{ $nr_d } == 0 ){
							$available = false;
						}
	
						//check if arrival date is disabled
						if(count($roomRateDetails)){
							foreach($roomRateDetails as $roomRateDetail){
								if($roomRateDetail->date == $startDate && $roomRateDetail->lock_arrival == 1){
									$available = false;
								}
							}
						}
						//dmp($startDate);
						//dmp($endDate);
	
						$offer->nrRoomsAvailable = $offer->availability;
						$offer->bookings = 0;
	
						for( $d = strtotime($startDate);$d < strtotime($endDate); ){
							$dayString = date( 'Y-m-d', $d);
								
							//set default price from rate
							$weekDay = date("N",$d);
							$string_price = "price_".$weekDay;
							$dayPrice = $offer->$string_price;
							$childPrice = $offer->child_price;
								
							$extraPersonPrice = $offer->extra_pers_price;
								
							//check if there is a custom price set
							if(count($offerRateDetails)){
								foreach($offerRateDetails as $offerRateDetail){
									if($offerRateDetail->date == $dayString){
										$dayPrice = $offerRateDetail->price;
										$extraPersonPrice = $offerRateDetail->extra_pers_price;
										$childPrice = $offerRateDetail->child_price;
										
									}
								}
							}
							
							$isExtraNight = false;	
							//check if we have an extra night
							if( $offer_max_nights <= 0  ){
								$dayPrice = $offer->extra_night_price;
								$isExtraNight = true;
							}
								
							if($offer->price_type==1){
								$totalAdults = ($adults<=$offer->base_adults)?$adults:$offer->base_adults;
								$dayPrice = $dayPrice * $totalAdults+$childPrice * $children;
							}
								
							//add extra person cost - if it is the case
							if($adults > $offer->base_adults){
								$dayPrice += ($adults - $offer->base_adults) *  $extraPersonPrice;
							}
								
								
							//for single use
							//if the price is per person apply single supplement , if is for room apply discount
							if($adults==1 && $children==0){
								if(!$isExtraNight){
									if($offer->price_type==1){
										$dayPrice = $dayPrice + $offer->single_balancing;
										//dmp("add balancing: ".$offer->single_balancing." -> ".$dayPrice);
									}else{
										$dayPrice = $dayPrice - $offer->single_balancing;
									}
								}else if($offer->price_type_day==1){
									if($offer->price_type==1){
										$dayPrice = $dayPrice + $offer->single_balancing/$offer->offer_min_nights;
									}else{
										$dayPrice = $dayPrice - $offer->single_balancing/$offer->offer_min_nights;
									}
								}
							}
								
							//check if offer is available on stay period
							if(!(strtotime($offer->offer_datas) <= $d && $d<=strtotime($offer->offer_datae) )){
								$available = false;
							}
								
							//get the minimum availability in the selected period
							if(count($roomRateDetails)){
								foreach($roomRateDetails as $roomRateDetail){
									//get room availability - if rate details are set default settings are ignored
									if($roomRateDetail->date == $dayString){
										$offer->nrRoomsAvailable = $roomRateDetail->availability;
									}
								}
							}
								
								
							$totalNumberRoomsReserved = 0;
								
							if(isset($bookings[$offer->room_id][$dayString])){
								$totalNumberRoomsReserved = $bookings[$offer->room_id][$dayString];
							}
							if(isset($temporaryReservedRooms[$offer->room_id]) && (strtotime($temporaryReservedRooms["datas"])<= $d &&  $d<strtotime($temporaryReservedRooms["datae"]) )){
								$totalNumberRoomsReserved += $temporaryReservedRooms[$offer->room_id];
							}
								
							//calculate maximum number of bookings per stay interval
							if($offer->nrRoomsAvailable <= $totalNumberRoomsReserved ){
								$available = false;
							}
								
							if( $offer_max_nights > 0  ){
								if(count($offerRateDetails)){
									foreach($offerRateDetails as $offerRateDetail){
										//set single use price
										if($offerRateDetail->date == $dayString && $adults==1 && $children==0){
											$dayPrice = $offerRateDetail->single_use_price;
										}
									}
								}
							}
	
							//check if hotel is available
							if(!$hotelAvailability[$dayString]){
								$available = false;
							}
								

							if(strtotime($startDate)==$d){
								$firstDayPrice = $dayPrice;
							}
							
							if($isExtraNight){
								$extraNightPrice += $dayPrice;
							}
							
							//dmp("DP: ".$dayPrice);
							$totalPrice += $dayPrice;
							$offer_max_nights--;
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						}
					}
					//dmp("T: ".$totalPrice);
						
					//dmp($offer->nrRoomsAvailable);
					//dmp($offer->bookings);
						
					//average price per offer
					$offer->offer_average_price = round($totalPrice/$nrDays,2);
					$offer->pers_total_price = round($totalPrice/($adults+$children),2);
						
					$price = $offer->offer_average_price;
					if(JRequest::getVar( 'show_price_per_person')==1){
						$price = $offer->pers_total_price;
					}
					
					if($offer->price_type_day == 1){
						$price = $firstDayPrice/($adults+$children);
						$price += $extraNightPrice/($adults+$children);
					}
					
					$roomsInfo[] = array("price" => JHotelUtil::fmt($price,2), "isAvailable" => $available);
					$index++;
				}
	
				//dmp($roomsInfo);
				$id= $offer->offer_id.$offer->room_id;
				$hotelId = $offer->hotel_id;
				$offersCalendar[$id]= JHotelUtil::getAvailabilityCalendar($hotelId, $month, $year, $roomsInfo, $nrDays, $id);
			}
		}
	
		//dmp($offersCalendar);
		
		
		return $offersCalendar;
	}
	
	public static function getCredential($user){
		$db = JFactory::getDBO();
		$query = "  SELECT	* 	FROM #__hotelreservation_hotel_channel_manager where user='$user'";
		//dmp($query);
		$db->setQuery( $query );
		$result = $db->loadObject();
		//dmp($result);
		return $result;
	}
	
	public static function getHotelCurrency($hotel){
		$currency = new stdClass();
		$currency->name = $hotel->hotel_currency;
		$currency->symbol = $hotel->currency_symbol;
		
		return $currency;
	}
	public static function checkRoomListingAvailability(&$rooms,$items_reserved, $datas ,$datae,$confirmationId=null){
		//number of reserved rooms for each room type

		$temporaryReservedRooms = BookingService::getReservedRooms($items_reserved);
		//dmp("T");
		//dmp($temporaryReservedRooms);
		$ingoreNrRooms = !empty($confirmationId)?1:0;
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				$rooms_reserved = BookingService::getNumberOfBookingsPerDay($room->hotel_id, $datas ,$datae,$confirmationId);
				//dmp($rooms);
				//	dmp("NR: ".$room->room_id." ".$room->nrRoomsAvailable);
				//dmp($room->daily);
				foreach($room->daily as $day){
	
					$totalNumberRoomsReserved = 0;
					//dmp($day["data"]);
					if(isset($rooms_reserved[$room->room_id][$day["date"]]))
					$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["date"]];
	
					if(isset($temporaryReservedRooms[$room->room_id])){
						$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
					}
	
					//dmp($totalNumberRoomsReserved);
					//dmp($day["nrRoomsAvailable"]);
					if($day["nrRoomsAvailable"] <= ($totalNumberRoomsReserved - $ingoreNrRooms ))
					{
						$room->is_disabled = true;
						//dmp("disable");
					}
				}
			}
		}
	}
	
}

?>