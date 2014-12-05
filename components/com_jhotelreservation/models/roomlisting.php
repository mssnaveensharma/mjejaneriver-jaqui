<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelRoomListing extends JModelLegacy{
	
	var $searchFilter;
	var $itemHotels;
	var $hotelIds;
	var $searchParams;
	var $appSettings;
	
	function __construct()
	{
		$this->searchFilter = JRequest::getVar('searchkeyword');
		parent::__construct();
		$this->_total = 0;
		$mainframe = JFactory::getApplication();
	
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	
		$this->appSettings = JHotelUtil::getApplicationSettings();
	}
	
	protected function populateState(){
		$app = JFactory::getApplication('site');
		
		// Load state from the request.
		$pk = JRequest::getInt('hotel_id');
		$this->setState('hotel.id', $pk);
		
		$tabId = JRequest::getInt('tabId',1);
		$this->setState('hotel.tabId', $tabId);
		
		
		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
		
		UserDataService::updateUserData();
	}
	
	
	public function getItem($pk = null){
		// Initialise variables.
		$hotel = HotelService::getHotel($this->getState('hotel.id'));
		//dmp($hotel->hotel_currency);
		UserDataService::setCurrency($hotel->hotel_currency, $hotel->currency_symbol);
		
		return $hotel;
	}
	
	function getOffers(){
		$userData = UserDataService::getUserData();
		
		$offers =  HotelService::getHotelOffers($this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		BookingService::setRoomAvailability($offers, $userData->reservedItems, $this->getState('hotel.id'),  $userData->start_date,  $userData->end_date);
		
		return $offers;
	}
		
	function getAllRooms(){
		
		$userData =  $_SESSION['userData'];
		$orderBy = "";
		$this->searchParams = array();
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["keyword"] =  $userData->keyword;
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["adults"] = $userData->adults;
		$this->searchParams["voucher"] = $userData->voucher;
		$this->searchParams["startDate"] = $userData->start_date;
		$this->searchParams["endDate"] = $userData->end_date;
		$this->searchParams["languageTag"] = JRequest::getVar( '_lang');
		$this->searchParams["city"] = JRequest::getVar('city');
		$this->searchParams["showAll"] = JRequest::getVar('showAll');
		$this->searchParams["adults"] = $userData->adults;
		$this->searchParams["children"] = $userData->children;
		$this->searchParams["type"] = JRequest::getVar('jhotelreservation_type');
		
		$orderByPrice = false;
		if($userData->orderBy=='lowest_hotel_price asc' || $userData->orderBy=='starting_price_offers asc')
			$orderByPrice = true;
		
		if(!isset($userData->noDates))
			$userData->noDates = 0;
		$this->searchParams["no-dates"] = $userData->noDates;
		$roomQuery = $this->getSearchQuery($this->searchParams);
		
		$db = JFactory::getDBO();
		$db->setQuery( $roomQuery, $this->getState('limitstart'), $this->getState('limit') );
		
		$rooms =  $db->loadObjectList();
		$rooms = HotelService::getAllRooms($rooms, $userData->start_date, $userData->end_date,$userData->adults,$userData->children,$orderByPrice);
		BookingService::setRoomAvailability($rooms, $userData->reservedItems, $this->getState('hotel.id'),  $userData->start_date,  $userData->end_date);
		
		return $rooms;
	}
	function getSearchQuery($searchParam){
		$languageTag = JRequest::getVar( '_lang');
		
		$availabilityFilter = " and r.is_available = 1 ";
		
		
		$startDate= $searchParam['startDate'];
		$endDate = $searchParam['endDate'];
		$adults =  $searchParam['adults'];
		$children =  $searchParam['children'];
		$typeId =  $searchParam['type'];
		
		
		$endDateS = date("Y-m-d", strtotime("-1 day", strtotime($endDate)));
		$days=$this->getDays($startDate, $endDateS);
		$dayFilter = '';
		foreach($days as $day){
			$dayFilter = $dayFilter." and LOCATE('$day', ignored_dates)=0";
		}
		
		//dmp($dayFilter);
		$whereClause="where 1";
		$whereClauseRoom=" ";
		
		
		$activeHotelsFilter = " and (h.start_date <= '$startDate' or h.start_date='0000-00-00') and (h.end_date >= '$endDate' or h.end_date='0000-00-00')";
		
		
		$voucherFilter = " and  (hov.voucher is null or hov.voucher='' or hof.public=1)";
		if($searchParam['voucher']){
			$voucher =  $searchParam['voucher'];
			$voucherFilter = " and LOWER(hov.voucher) =LOWER('$voucher')";
			$voucherFilter.= " and hof.offer_datas <= '$startDate' and hof.offer_datae >= '$endDate' ";
			$availabilityFilter = " and (min_offer_price is not null) ";
		}
		
		$whereClause=$whereClause;
		if($searchParam['keyword']){
			$keyword = $searchParam['keyword'];
			$whereClause = $whereClause. " and ((h.hotel_name like '%$keyword%') or (h.hotel_city like '%$keyword%') or (h.hotel_county like '%$keyword%')) ";
			//$whereClauseRoom .=" and (hlt.content like '%$keyword%')";
		}

		if($searchParam["orderBy"]){
			$orderBy = " order by ".$searchParam["orderBy"];
		}
		
		if (is_numeric($typeId)) {
			$availabilityFilter .= ' and hatr.accommodationtypeId ='.(int) $typeId;
		}
		
		
		if(isset($searchParam['showAll']) && $searchParam['showAll'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$showFilter='';
			$whereClause='';
		}
		
		if(isset($searchParam['no-dates']) && $searchParam['no-dates'] == 1){
			$availabilityFilter ='';
			$activeHotelsFilter ='';
			$dayFilter='';
			$showFilter='';
		}
		
		//get hotel rooms
			$query="select r.*,h.*,rr.*,hlt.content as room_main_description, rr.id as rate_id ,h.reservation_cost_val AS reservation_cost_val, h.reservation_cost_proc AS reservation_cost_proc,
							count(distinct hcr.confirmation_id) as noBookings,
							hh11.min_offer_price,
							hh11.offer_min_nights,
							hh11.offer_base_adults,
							hh11.offer_price_type,
							hh11.offer_details, 
							c1.country_name, 
							least( hh11.min_offer_price, min_room_price) as lowest_hotel_price,
							currency_symbol
						from #__hotelreservation_rooms r
						inner join #__hotelreservation_rooms_rates rr on r.room_id = rr.room_id
						inner join (select * from #__hotelreservation_hotels h $whereClause $dayFilter $activeHotelsFilter and h.is_available = 1) h ON h.hotel_id = r.hotel_id
						LEFT JOIN #__hotelreservation_countries c1 on h.country_id = c1.country_id
						LEFT JOIN #__hotelreservation_hotel_accommodation_type_relation hatr ON h.hotel_id=hatr.hotelid
						LEFT JOIN #__hotelreservation_hotel_accommodation_types hat ON hat.id=hatr.accommodationtypeId
						LEFT JOIN #__hotelreservation_currencies c2 on h.currency_id = c2.currency_id 
		       		    left join #__hotelreservation_confirmations_rooms as hcr on r.room_id=hcr.room_id  
						left join (
				               select h2.room_id as roomId2, 
				               		offer_min_nights, 
				               		offer_name as min_offer_name,
									base_adults as offer_base_adults, 
									price_type as offer_price_type, 
									min(offer_rate) as min_offer_price,
								    GROUP_CONCAT(offer_name,'||',offer_id,'||',offer_rate,'||',offer_min_nights,'||',base_adults,'||',price_type,'||',price_type_day separator '#') as offer_details
							 	from #__hotelreservation_rooms h2
				             	left join (
				             		select
				             		hof.offer_name,
				             		hof.offer_id,
				             		hof.hotel_id as hotel_id,
				             		hof.offer_min_nights as offer_min_nights, 
				             		ofrr.room_id,
									ofr.base_adults, ofr.price_type,  ofr.price_type_day, 
									if(ofr.price_type_day=1 , 
										if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) ,
										if ( min(orp.price),  min(orp.price), min(least(ofr.price_1, ofr.price_2, ofr.price_3, ofr.price_4, ofr.price_5, ofr.price_6, ofr.price_7))) * hof.offer_min_nights )
										 as offer_rate
								    from #__hotelreservation_offers hof 
				                    left join #__hotelreservation_offers_rooms ofrr on ofrr.offer_id = hof.offer_id 
									left join #__hotelreservation_offers_rates ofr on ofr.offer_id = hof.offer_id and ofr.room_id = ofrr.room_id
									left join #__hotelreservation_offers_rate_prices orp on orp.rate_id = ofr.id
							        left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId  
				  	                where  hof.is_available = 1 and (now() between hof.offer_datasf and hof.offer_dataef) $voucherFilter
				  	                group by hof.offer_id 
				  	                order by offer_rate asc
				  	              ) hh111 on hh111.room_id = h2.room_id
				                group by roomId2
				                order by min_offer_price asc
				            ) hh11 on r.room_id= hh11.roomId2
						left join
							(select * from 
							 #__hotelreservation_language_translations 
							 where type = ".ROOM_TRANSLATION."
							 and language_tag = '$languageTag'
							) as hlt on hlt.object_id = r.room_id 
						where 1 $whereClauseRoom 
						$availabilityFilter
						group by r.room_id,h.hotel_id,r.room_name 
				       $orderBy ";
			return $query;
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
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
	
			$this->_pagination = new JPagination($this->getTotalRooms(), $this->getState('limitstart'), $this->getState('limit') );
			//$this->_pagination->setAdditionalUrlParam('controller','hotels');
			$this->_pagination->setAdditionalUrlParam('filterParams',rawurlencode($this->searchFilter["searchParams"]));
		}
		return $this->_pagination;
	}

	function getTotalRooms(){
	
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$db = JFactory::getDBO();
			$query = $this->getSearchQuery($this->searchParams);
			$db->setQuery($query);
			if($db->query()){
				$this->_total = $db->getNumRows();
			}
			
			
			if(!isset($this->_total)){
				$this->_total = 0;
			}
		}
	
		return $this->_total;
	}
	
}