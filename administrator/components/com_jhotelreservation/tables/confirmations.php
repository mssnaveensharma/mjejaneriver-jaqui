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

class TableConfirmations extends JTable
{
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableConfirmations(& $db) {

		parent::__construct('#__hotelreservation_confirmations', 'confirmation_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getReservationData($confirmationId){
		$db =JFactory::getDBO();
		$query = 	" SELECT
						c.*,
						r.room_id as reserve_room_id,IF(c.hotel_id=0,ce.hotel_id,c.hotel_id) as hotel_id,
						GROUP_CONCAT( DISTINCT CONCAT(r.adults, '|', r.current) ORDER BY r.current) as total_adults,
						GROUP_CONCAT( DISTINCT CONCAT(r.children, '|', r.current) ORDER BY r.current) as children,
						GROUP_CONCAT( DISTINCT CONCAT(r.juniors, '|', r.current) ORDER BY r.current) as juniors,
						GROUP_CONCAT( DISTINCT CONCAT(r.babies, '|', r.current) ORDER BY r.current) as babies,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id, '|', r.current) ORDER BY r.current )	AS items_reserved,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id) ORDER BY r.current )	AS room_ids,
						GROUP_CONCAT( DISTINCT CONCAT(r.offer_id, '|', r.room_id, '|', r.current,'|',crp.date,'|',crp.price ) ORDER BY r.current ) as room_prices,
						GROUP_CONCAT( DISTINCT fo.option_id) AS option_ids,
						GROUP_CONCAT( DISTINCT CONCAT(cg.first_name, '|', cg.last_name, '|', cg.identification_number) ORDER BY cg.id) AS guestDetails,
						GROUP_CONCAT( DISTINCT CONCAT(eo.offer_id, '|', eo.room_id, '|', eo.current, '|', eo.extra_option_id, '|', 1,'|', eo.extra_option_persons,'|', eo.extra_option_days) ORDER BY r.current )		AS extraOptionIds,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_type_id ) ORDER BY r.current )		AS airport_transfer_type_ids,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airline_id ) ORDER BY r.current)						AS airport_airline_ids,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_flight_nr ) ORDER BY r.current)		AS airport_transfer_flight_nrs,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_date ) ORDER BY r.current)			AS airport_transfer_dates,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_time_hour ) ORDER BY r.current)		AS airport_transfer_time_hours,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_time_min ) ORDER BY r.current)		AS airport_transfer_time_mins,
						GROUP_CONCAT( DISTINCT CONCAT(at.offer_id, '|', at.room_id, '|', at.current, '|', at.airport_transfer_guest ) ORDER BY r.current)			AS airport_transfer_guests,
						GROUP_CONCAT( DISTINCT CONCAT(ce.hotel_id,'_',ce.excursion_id, '_', ce.nr_booked) ORDER BY ce.confirmation_excursion_id)			AS excursions,
				
						cp.amount as totalPaid,		
						s.status_reservation_name				AS status_reservation_name,
						s.order									AS status_order,
						s.is_modif								AS status_is_modif,
						cp.payment_method					
						FROM #__hotelreservation_confirmations c
						LEFT JOIN #__hotelreservation_confirmations_rooms 						r	ON ( r.confirmation_id 			= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_room_prices					crp	ON ( r.confirmation_room_id 	= crp.confirmation_room_id )
						LEFT JOIN #__hotelreservation_confirmations_feature_options 			fo	ON ( fo.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_status_reservation 						s	ON ( c.reservation_status	 	= s.status_reservation_id )
						LEFT JOIN #__hotelreservation_confirmations_extra_options 				eo	ON ( eo.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_rooms_airport_transfer 		at	ON ( at.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_guests				 		cg	ON ( cg.confirmation_id 		= c.confirmation_id )
						LEFT JOIN #__hotelreservation_confirmations_payments			 		cp	ON ( cp.confirmation_id 		= c.confirmation_id  and cp.payment_status =".PAYMENT_STATUS_PAID.")
						LEFT JOIN #__hotelreservation_confirmations_excursions  				ce	ON ( ce.confirmation_id 		= c.confirmation_id )
						".
						" WHERE c.confirmation_id = $confirmationId
						GROUP BY c.confirmation_id
						";
		//var_dump($query);
		$db->setQuery( $query );
		$data 	=$db->loadObject();
		//dmp($data);
		return $data;
	}
	

	function getHotelMonthlyReservations($hotelId,$startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "select * 
				 from #__hotelreservation_confirmations hc
				 inner join #__hotelreservation_confirmations_rooms hcr  on hcr.confirmation_id = hc.confirmation_id
				 left join  #__hotelreservation_offers ho on ho.offer_id = hcr.offer_id 
				 where hc.hotel_id=$hotelId and hc.end_date>='$startDate' and hc.end_date<='$endDate' and hc.reservation_status <> ".CANCELED_ID."
				 group by hc.confirmation_id
		";
		//dmp($query);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getByUserId($userId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_confirmations where user_id=$userId";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getClientReservations($userId){
		$db =JFactory::getDBO();
		$query = "		SELECT 
							c.*,  
							s.status_reservation_name,
							s.is_modif,
							GROUP_CONCAT( DISTINCT r.room_id) AS room_ids,
							pp.name as payment_processor,
							p.payment_status, 
							p.confirmation_payment_id,
							h.hotel_name AS hotel_name
						FROM #__hotelreservation_confirmations c 
						INNER JOIN #__hotelreservation_status_reservation				s	on (c.reservation_status=s.status_reservation_id)
						INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
						LEFT JOIN #__hotelreservation_confirmations_payments			p	USING(confirmation_id) 
						LEFT JOIN #__hotelreservation_hotels h	ON ( h.hotel_id = r.hotel_id )		
						LEFT JOIN #__hotelreservation_payment_processors pp	ON ( pp.type = p.processor_type )
						WHERE c.user_id=$userId
						GROUP BY c.confirmation_id 
						ORDER BY c.confirmation_id DESC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	function getReservationsIncome($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd){
		$whereCond = "";
		if(isset($roomTypeId) && $roomTypeId>0){
			$whereCond.=" and r.room_id = $roomTypeId";
		}
		$dateStart =  date_format(new DateTime($dateStart),'Y-m-d');
		$dateEnd =  date_format(new DateTime($dateEnd),'Y-m-d');
		
		$db =JFactory::getDBO();
		$query = "		SELECT 
							sum(p.amount) reservationTotal,
							(CASE '$reportType' WHEN 'DAY' then  p.payment_date
				                        WHEN 'WEEK' then  concat('W',Week(p.payment_date))
				                        WHEN 'MONTH' then concat(Month(p.payment_date),'-','01','-',Year(p.payment_date))
				                        WHEN 'YEAR' then  concat(Year(p.payment_date))
				            END) as groupUnit
						FROM #__hotelreservation_confirmations c 
						INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
						LEFT JOIN 
						(
							SELECT 
								p.*
							from #__hotelreservation_confirmations_payments	p	
							where p.payment_status = ".PAYMENT_STATUS_PAID." 
							and p.payment_date is not null
                            and p.payment_date between '$dateStart' and '$dateEnd'
						) 																p 	USING(confirmation_id)
						LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= c.hotel_id )	
						WHERE h.hotel_id= $hotelId
						$whereCond
						GROUP BY groupUnit
						ORDER BY groupUnit asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getReservationsCountries($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd){
		$whereCond = "";
		$dateStart =  date_format(new DateTime($dateStart),'Y-m-d');
		$dateEnd =  date_format(new DateTime($dateEnd),'Y-m-d');
						
		if(isset($roomTypeId) && $roomTypeId>0){
			$whereCond.=" and r.room_id = $roomTypeId";
		}
		
		$db =JFactory::getDBO();
		$query = "			SELECT
								c.country,
								count(*) as countryCount
							FROM #__hotelreservation_confirmations c 
							INNER JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
							LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= r.hotel_id )	
							WHERE c.hotel_id= '$hotelId'	
							and c.start_date between '$dateStart' and '$dateEnd'
							$whereCond
							GROUP BY c.country
							ORDER BY c.country asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getReservationsReport($reportType,$dayLag){
		$whereCond = "";
		$dayDiff = "-".$dayLag.' day';
		$dateStart =  date('Y-m-d',(strtotime ($dayDiff)));
		$dateEnd =  date_format(new DateTime('NOW'),'Y-m-d');
		
		$db =JFactory::getDBO();
		$query = "			SELECT
								count(c.confirmation_id) reservationTotal,
								(CASE '$reportType' WHEN 'DAY' then concat(Month(c.created),'-',Day(c.created),'-',Year(c.created))
				                        WHEN 'WEEK' then  concat('W',Week(c.created))
				                        WHEN 'MONTH' then concat(Month(c.created),'-','01','-',Year(c.created))
				                        WHEN 'YEAR' then  concat(Year(c.created))
				            	END) as groupUnit
								FROM #__hotelreservation_confirmations c 
								left JOIN #__hotelreservation_confirmations_rooms				r	USING(confirmation_id)
								left JOIN #__hotelreservation_confirmations_excursions			e	USING(confirmation_id)
								LEFT JOIN #__hotelreservation_hotels			 				h	ON ( h.hotel_id 					= r.hotel_id )	
								where c.start_date between '$dateStart' and '$dateEnd'
								$whereCond
								GROUP BY groupUnit
								ORDER BY groupUnit asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	
	function getReviewsToSend(){
		$db =JFactory::getDBO();
		$query = "			 SELECT
								c.*,DATEDIFF( CURDATE(),date(c.start_date)) daysAfterCheckout
								FROM #__hotelreservation_confirmations c 
								INNER JOIN #__hotelreservation_status_reservation s	on c.reservation_status = s.status_reservation_id
								WHERE c.reservation_status !=2
			                	and c.review_email_date is null
			                	and DATEDIFF( CURDATE(),date(c.end_date))>0
			                	
							GROUP BY c.confirmation_id 
							ORDER BY c.confirmation_id DESC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function setStatus($reservationId, $status){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_confirmations SET reservation_status = $status  WHERE confirmation_id = ".$reservationId ;
		$db->setQuery($query);
		return $db->query();
	}
	
	function updateCancelationComments($reservationId, $cancellationNotes){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_confirmations SET cancellation_notes = '$cancellationNotes'  WHERE confirmation_id = ".$reservationId ;
		$db->setQuery($query);
		return $db->query();
	}
	
	

	function getCubilisReservations($hotelId, $limit){
		$db =JFactory::getDBO();
		$query = "select * from  #__hotelreservation_confirmations where cubilis_status <> ".CUBILIS_RESERVATION_SENT." and hotel_id = $hotelId";
		$db->setQuery($query,0,$limit);
		$reservations =  $db->loadObjectList();
		
		if(!empty($reservations)){
			foreach($reservations as &$reservation){
				$query = "select * from #__hotelreservation_confirmations_rooms where confirmation_id = ".$reservation->confirmation_id;
				$db->setQuery($query);
				$reservation->rooms = $db->loadObjectList();
				foreach($reservation->rooms as &$room){
					$query = "select * from #__hotelreservation_confirmations_room_prices where confirmation_room_id = ".$room->confirmation_room_id;
					$db->setQuery($query);
					$room->prices = $db->loadObjectList();
				}
			}
		}
		
		return $reservations;
	}
	
	function setReservationCubilisStatus($reservations){
		$db =JFactory::getDBO();
		$result = true;
		
		if(is_array($reservations) && count($reservations)){
			$reservationIds="(";
			foreach($reservations as $reservation){
				$reservationIds .= $reservation->confirmation_id.",";
			}
			$reservationIds =substr($reservationIds, 0, -1);
			$reservationIds.=")";
			
			$query = " UPDATE #__hotelreservation_confirmations SET cubilis_status = ".CUBILIS_RESERVATION_SENT."  WHERE confirmation_id in ".$reservationIds ;
			$db->setQuery($query);
			$result =  $db->query();
		}
		
		return $result;
	}

	
	function getReservationList($startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "select c.*, h.hotel_id, h.hotel_name, h.email as hotel_email, count(cr.room_name) as number_rooms,  GROUP_CONCAT(of.offer_name) as offer_names , GROUP_CONCAT(cr.room_name) as room_names 
				from  #__hotelreservation_confirmations c
				left join #__hotelreservation_confirmations_rooms cr on cr.confirmation_id = c.confirmation_id
				left join #__hotelreservation_offers of on cr.offer_id = of.offer_id 
				left join #__hotelreservation_hotels h on h.hotel_id = c.hotel_id 
				where c.start_date between '$startDate' and '$endDate'  and c.hotel_id =1  and c.reservation_status <> ".CANCELED_ID."
				group by c.confirmation_id
				order by c.hotel_id ";
		//dmp($query);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
}