<?php

class BookingService{


	public static function getNumberOfBookings($hoteId, $startDate, $endDate){
		$reserved_rooms = array();
		$db = JFactory::getDBO();
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
				
			$query = "select room_id,count(hcr.confirmation_room_id) as reserved_rooms from #__hotelreservation_confirmations hc
					inner join #__hotelreservation_confirmations_rooms hcr on hc.confirmation_id = hcr.confirmation_id
					where '".(date('Y-m-d', $d))."' between hc.start_date and hc.end_date and hc.hotel_id = $hoteId
					group by hcr.room_id";

			$db->setQuery( $query );
			$reservationInfos =  $db->loadObjectList();
			
			if(count($reservationInfos) > 0){
				foreach($reservationInfos as $reservationInfo){
					if(isset($reservationInfo->reserved_rooms)){
						if(!isset($reserved_rooms[$reservationInfo->reserved_rooms])
								|| $reserved_rooms[$reservationInfo->reserved_rooms] < $reservationInfo->reserved_rooms){
							$reserved_rooms[$reservationInfo->room_id] = $reservationInfo->reserved_rooms;
						}
					}
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}

		return $reserved_rooms;
	}


	public static function getNumberOfBookingsPerDay($hoteId, $startDate, $endDate, $reservationId=null){
		$reservedRooms = array();
		$bookedRooms = array();
		$db = JFactory::getDBO();
		$reservationFilter = "";
		if(isset($reservationId) && $reservationId>0){
			//exclude the current reservation 
			$reservationFilter = "and c.confirmation_id <> $reservationId";
			//build count of rooms added to reservation 
			$reservedItems = UserDataService::getUserData()->reservedItems;
			$bookedRooms = self::getReservationBookedRooms($reservedItems);
		}
		
		$query = "select hcr.room_id, c.start_date, c.end_date
					from #__hotelreservation_confirmations_rooms hcr
					left join #__hotelreservation_confirmations c on c.confirmation_id= hcr.confirmation_id
					where (c.start_date <'$endDate' and c.end_date >='$startDate') 
						and c.reservation_status <> ".CANCELED_ID." 
						$reservationFilter
						and c.hotel_id = $hoteId";
		//echo $query;
		$db->setQuery( $query );
		$reservationInfos =  $db->loadObjectList();
		//dmp($bookedRooms);
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			foreach($reservationInfos as $reservationInfo){
				if( strtotime($reservationInfo->start_date)== $d || ($d<strtotime($reservationInfo->end_date) && $d>strtotime($reservationInfo->start_date)) ){
					if(!isset($reservedRooms[$reservationInfo->room_id]) || !isset($reservedRooms[$reservationInfo->room_id][$dayString]) ){
						$reservedRooms[$reservationInfo->room_id][$dayString] = 0;
					}
					$reservedRooms[$reservationInfo->room_id][$dayString] = $reservedRooms[$reservationInfo->room_id][$dayString] +1;
				}
			}	
			//add to the total booked the number of added rooms
			if(count($bookedRooms)){
				foreach ($bookedRooms as $key=>$bookedRoom){
					if(!isset($reservedRooms[$key][$dayString]))
						$reservedRooms[$key][$dayString] = 0; 
					$reservedRooms[$key][$dayString] += $bookedRoom;
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		//dmp($reservedRooms);exit;
		return $reservedRooms;
	}
	
	static function getReservationBookedRooms($reservedItems){
		$bookedRooms = array();
		if(isset($reservedItems))
		foreach($reservedItems as $reservedItem){
			$values = explode("|",$reservedItem);
			if(!isset($bookedRooms[$values[1]]))
				$bookedRooms[$values[1]] = 0;
			$bookedRooms[$values[1]]+=1;
		}
		
		return $bookedRooms;
	}
	
	public static function getExcursionBookingsPerDay($hoteId, $startDate, $endDate, $reservationId=null){
		$reservedExcursions = array();
		$db = JFactory::getDBO();
		$reservationFilter = "";
		if(isset($reservationId)){
			$reservationFilter = "and c.confirmation_id <> $reservationId";
		}
	
		$query = "select hce.confirmation_excursion_id, hce.excursion_id,hce.nr_booked, c.start_date, c.end_date
				  from #__hotelreservation_confirmations_excursions hce
				  left join #__hotelreservation_confirmations c on c.confirmation_id= hce.confirmation_id
				  where (c.start_date <'$endDate' and c.end_date >='$startDate')
		  		  and c.reservation_status <> ".CANCELED_ID."
				  $reservationFilter";
		$db->setQuery( $query );
		$reservationInfos =  $db->loadObjectList();

		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			foreach($reservationInfos as $reservationInfo){
				if( strtotime($reservationInfo->start_date)== $d || ($d<strtotime($reservationInfo->end_date) && $d>strtotime($reservationInfo->start_date)) ){
					if(!isset($reservedExcursions[$reservationInfo->excursion_id]) || !isset($reservedExcursions[$reservationInfo->excursion_id][$dayString]) ){
						$reservedExcursions[$reservationInfo->excursion_id][$dayString] = 0;
					}
					$reservedExcursions[$reservationInfo->excursion_id][$dayString] = $reservedExcursions[$reservationInfo->excursion_id][$dayString] +$reservationInfo->nr_booked;
				}
			}
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
		
	
		return $reservedExcursions;
		}

	
	public static function checkReservationAvailability($reservationDetails){
		$result = true;

		$hotelId = $reservationDetails->reservationData->userData->hotelId; 
		$startDate = $reservationDetails->reservationData->userData->start_date;
		$endDate = $reservationDetails->reservationData->userData->end_date;
		$confirmationId = $reservationDetails->reservationData->userData->confirmation_id;
		
		$bookings = self::getNumberOfBookingsPerDay($hotelId,$startDate,$endDate, $endDate, $confirmationId);
		$rooms = $reservationDetails->rooms;
		
		foreach($rooms as $room){
			$available = $room->days[$dayString]["available"];
			$nrRooms = $room->days[$dayString]["nrRooms"];
			$nrBookings = isset($bookings[$room->room_id][$dayString])?$bookings[$room->room_id][$dayString]:0;
			$info[$room->room_id] = array($nrRooms, $nrBookings, $available);
		}
		
		return $result;
	}
	

	public static function setRoomAvailability(&$rooms, $items_reserved, $hotel_id, $startDate ,$endDate, $confirmationId=0){
		//number of reserved rooms for each room type
		$app = JFactory::getApplication();
		$rooms_reserved = self::getNumberOfBookingsPerDay($hotel_id, $startDate ,$endDate, $confirmationId);
		$temporaryReservedRooms = self::getReservedRooms($items_reserved);
		//dmp("T");
		//dmp($temporaryReservedRooms);

		
		foreach($rooms as $room){
			//	dmp("NR: ".$room->room_id." ".$room->nrRoomsAvailable);
			//dmp($room->daily);
			foreach($room->daily as $day){
	
				$totalNumberRoomsReserved = 0;
				//dmp($day["data"]);
				if(isset($rooms_reserved[$room->room_id][$day["date"]])){
					$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["date"]];
				}
				
				if(isset($temporaryReservedRooms[$room->room_id])){
					$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
				}
				// No remember me for admin
				if (!$app->isAdmin()) {
					$totalNumberRoomsReserved+=1;
				}
				if(intval($day["nrRoomsAvailable"]) < $totalNumberRoomsReserved )
				{
					$room->is_disabled = true;
				}
			}
		}
	}
	
	public static function getReservedRooms($reservedItems){
		$result = array();
		if(is_array($reservedItems) && count($reservedItems)){
			foreach($reservedItems as $item){
				$value = explode("|",$item);
				if(isset($result[$value[1]]))
					$result[$value[1]] =$result[$value[1]]+1;
				else
					$result[$value[1]] =1 ;
			}
		}
		return $result;
	}
	
	
	public static function checkReservationPendingPayments(){
		$reservations = self::getExpiredReservationPayments();
		//dmp($reservations);
		foreach($reservations as $reservation){
			self::cancelReservation($reservation->confirmation_id, $reservation->confirmation_payment_id, true);
			EmailService::sendReservationFailureEmail($reservation);
		}
	}
	
	public static function getExpiredReservationPayments(){
		$db = JFactory::getDBO();
		
		$query = " 	SELECT
					c.confirmation_id,
					c.email,
					hp.type AS type,
					cp.confirmation_payment_id,
					c.start_date,
					c.end_date,
					c.first_name,
					c.last_name
					FROM #__hotelreservation_confirmations c
					INNER JOIN #__hotelreservation_confirmations_payments	cp USING( confirmation_id )
					INNER JOIN #__hotelreservation_payment_processors		hp on hp.type = cp.processor_type
					WHERE
					( hp.type = '".PROCESSOR_PAYPAL."' OR hp.type = '".PROCESSOR_BUCKAROO."' OR hp.type = '".PROCESSOR_4B."' )
					AND
					cp.payment_status ='".PAYMENT_STATUS_PENDING."'
					AND
					IF( hp.timeout > 0, TIMESTAMPDIFF(MINUTE, cp.created, now() ) > hp.timeout, 0 )
					GROUP BY c.confirmation_id
				";
		
		$db->setQuery( $query );
		$reservations =  $db->loadObjectList();
		
		return $reservations;
	}
	
	static function cancelReservation($confirmation_id, $confirmation_payment_id=0, $cancelPayment= false){
		$db =JFactory::getDBO();
	
		try{
			$query = " update #__hotelreservation_confirmations set reservation_status=".CANCELED_ID." WHERE confirmation_id = ".$confirmation_id ;
			
			$db->setQuery( $query );
			if (!$db->query())
			{
				throw( new Exception($db->getErrorMsg()));
			}
				
			if($cancelPayment){
				$query = " 	UPDATE #__hotelreservation_confirmations_payments set payment_status = '".PAYMENT_STATUS_FAILURE."' WHERE confirmation_payment_id = $confirmation_payment_id";
				$db->setQuery( $query );
				if (!$db->query())
				{
					throw(new Exception($db->getErrorMsg()));
				}
			}
			return true;
			
		}catch( Exception $ex )
		{
			return false;
		}
	}
}