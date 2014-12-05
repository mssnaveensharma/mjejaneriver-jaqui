<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelHotels extends JModelLegacy{
	
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
	
	function getHotels(){
		$userData =  $_SESSION['userData'];
		//dmp($userData);
		$this->createSeachFilter($userData->filterParams, $userData->orderBy);
		$this->searchParams = $this->createDBSearchParams($this->searchFilter["filterCategories"]);
		$this->searchParams["keyword"] =  $userData->keyword;
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["adults"] = $userData->adults;
		$this->searchParams["voucher"] = $userData->voucher;
		$this->searchParams["startDate"] = $userData->start_date;
		$this->searchParams["endDate"] = $userData->end_date;
		$this->searchParams["languageTag"] = JRequest::getVar( '_lang');
		$this->searchParams["city"] = JRequest::getVar('city');
		$this->searchParams["showAll"] = JRequest::getVar('showAll');
		$this->searchParams["searchType"] = JRequest::getVar('searchType');
		$this->searchParams["searchId"] = JRequest::getVar('searchId');
		
		if($this->searchParams["searchType"]==JText::_("LNG_HOTELS")){
			$hotel = $hotel = HotelService::getHotel($this->searchParams["searchId"]);
			$link = JHotelUtil::getHotelLink($hotel);
			
			$app = JFactory::getApplication();
			$app->redirect($link);
			
		}
		
		if(!isset($userData->noDates))
			$userData->noDates = 0;
		$this->searchParams["no-dates"] = $userData->noDates;
		
		$hotelTable =$this->getTable('hotels');
		$hotels =  $hotelTable->getHotels($this->searchParams, $this->getState('limitstart'), $this->getState('limit'));
		
		
		$userData->searchFilter =  $this->searchFilter;
		$_SESSION['userData'] = $userData;
		
		
		$nearByHotels = array();
		if(count($hotels)<=3){ 
			$nearByHotels = $this->getNearByHotels($this->searchParams["keyword"], $this->searchParams, $hotels);
			if(!empty($nearByHotels)){
				foreach($nearByHotels as &$hotel){
					$hotel->nearBy =1;
				}
				$hotels = array_merge($hotels,$nearByHotels);
			}
		}
		
		$hotels = $this->prepareHotelOffers($hotels);
		
		$this->itemHotels = $hotels;
		//dmp($userData->orderBy);
		if(isset($userData->voucher) && $userData->voucher!='' && $userData->orderBy=='' && count($this->itemHotels)>0){
			//dmp("shuffle");
			shuffle($this->itemHotels);
		}
		
		return $this->itemHotels;
	}
	
	function getNearByHotels($locationName, $searchParams, $hotels){
		
		$location = JHotelUtil::getInstance()->getCoordinates($locationName);
		
		if(empty($location))
			return null;
		
		$excludedIds=array();
		foreach($hotels as $hotel){
			$excludedIds[]=$hotel->hotel_id;
		}
		
		$searchParams["nearByHotels"] = 1;
		$searchParams["latitude"]= $location["latitude"];
		$searchParams["longitude"]= $location["longitude"];
		$searchParams["distance"] = 100;
		if(!empty($excludedIds)){
			$searchParams["excludedIds"] = implode(",",$excludedIds);
		}
		$hotelTable =$this->getTable('hotels');
		$hotels =  $hotelTable->getHotels($searchParams, 0, 5);
		
		return $hotels;
		
	}
	
	
	function prepareHotelOffers($hotels){
		foreach($hotels as $hotel){
			if(!empty($hotel->room_details)){
				$hotel->rooms = explode(",",$hotel->room_details);
				foreach($hotel->rooms as &$room){
					$room = explode("|",$room);
				}
			}
				
			//dmp($hotel->offer_details);
			if(!empty($hotel->offer_details)){
				$hotel->offers = explode("#",$hotel->offer_details);
				foreach($hotel->offers as &$offer){					
					$offer = explode("||",$offer);
					if(count($offer)<5)
						continue;
						
					//dmp($offer);
					$offer["price"] = $offer[2];
					if($offer[5] == 1){
						$offer["price"] = $offer[2];
					}else{
						if($offer[4]!=0)
							$offer["price"] = $offer[2] / $offer[4];
					}
					if($offer[6] == 1){
						$offer["price"] = $offer[2];
					}
				}
		
			}
		}
		
		return $hotels;
	}
	
	function getSearchFilter(){
		return $this->searchFilter["searchParams"];
	}
	
	
	function getTotalHotels(){
		
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$categoryId= JRequest::getVar('categoryId');
			$hotelTable =$this->getTable('hotels');
			$hotels = $hotelTable->getTotalHotels($this->searchParams);
			
			$this->_total = count($hotels);
			$this->processSearchResults($hotels, $this->searchFilter["filterCategories"], true);
			if(!isset($this->_total)){
				$this->_total = 0; 
			}
			//dmp($this->_total);
		}
		
		return $this->_total;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $searchResults
	 * @param unknown_type $params
	 * @return string
	 */
	function processSearchResults($searchResults, &$params, $countCategories = false){
		$hotelIds='';
		if(!is_array($searchResults) || count($searchResults)==0) 
			return null;
		foreach($searchResults as $searchResult){
			foreach($params as $key=>$param){
				if($countCategories){
					$results = explode(",",$searchResult->$param["dbIdent"]);
					$found = array();
					foreach($results as $result){
						if(!isset($found[$result])){
							foreach($param["items"] as $prm){
								if($prm->id == intval($result)){
									if(isset($prm->count)){
										$prm->count = $prm->count+1;
									}else{
										$prm->count = 1;
									}
									
									$found[$result] = true;
								}
							}
						}
					}
				}
			}
			$hotelIds = $hotelIds.$searchResult->hotelId1.',';
				
		}
		$hotelIds =substr($hotelIds, 0, -1);
		return $hotelIds;
		//exit();
	}
	
	/**
	 *
	 * Create the list of search ids for each filter category
	 * @param unknown_type $params
	 * @return string
	 */
	function createDBSearchParams($params){
		foreach($params as $key=>$value){
			$list =' ';
			foreach($value["items"] as $prm){
				if(isset($prm->selected)){
					$list = $list.$prm->id.",";
				}
			}
			$list =substr($list, 0, -1);
			$params[$key] = $list;
		}
		//dmp($params);
		return $params;
	}
	
	/**
	 * Create search filter along with filter categories
	 *
	 * @param unknown_type $params
	 */
	function createSeachFilter($params, $orderBy){
		$this->searchFilter					= array();
		$this->searchFilter["searchParams"] = $params;
		
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_facilities ORDER BY name';
		$facilities = $this->_getList( $query );
		$this->setSearchedParams("facilityId",$facilities, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_types ORDER BY name';
		$types = $this->_getList( $query );
		$this->setSearchedParams("typeId",$types, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_accommodation_types ORDER BY name';
		$accommodationTypes = $this->_getList( $query );
		$this->setSearchedParams("accommodationTypeId",$accommodationTypes, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_environments ORDER BY name';
		$environments = $this->_getList( $query );
		$this->setSearchedParams("enviromentId",$environments, $params);
			
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_regions ORDER BY name';
		$regions = $this->_getList( $query );
		$this->setSearchedParams("regionId",$regions, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes ORDER BY name';
		$themes = $this->_getList( $query );
		$this->setSearchedParams("themeId",$themes, $params);
		
		$filterCategories =  array();
		$filterCategories["facilities"] = array ("dbIdent"=>"facilities","name"=>JText::_('LNG_FACILITIES'), "items"=>$facilities);
		$filterCategories["types"] = array ("dbIdent"=>"types","name"=>JText::_('LNG_TYPES'), "items"=>$types);
		$filterCategories["accommodationTypes"] = array ("dbIdent"=>"accommodationTypes","name"=>JText::_('LNG_ACCOMMODATION_TYPES'), "items"=>$accommodationTypes);
		$filterCategories["enviroments"] = array ("dbIdent"=>"enviroments","name"=>JText::_('LNG_ENVIROMENTS'), "items"=>$environments);
		$filterCategories["regions"] = array ("dbIdent"=>"regions","name"=>JText::_('LNG_REGIONS'), "items"=>$regions);
		$filterCategories["themes"] = array ("dbIdent"=>"themes","name"=>JText::_('LNG_THEMES'), "items"=>$themes);
		
		$this->searchFilter["filterCategories"] = $filterCategories;
	
		$this->searchFilter["orderBy"] = $orderBy;
	}
	
	/**
	 *
	 * Select existing params
	 * @param unknown_type $params
	 * @param unknown_type $selectedParams
	 */
	function setSearchedParams($identifier, &$params, $selectedParams){
		//dmp($params);
		foreach( $params as &$param ){
			$param->countResults = 0;
			$param->identifier = $identifier;
		}
		if(isset($selectedParams)){
			$selectedParamsArray = explode("&", $selectedParams);
			foreach($selectedParamsArray as $selectedParam){
				if(isset($selectedParam) && strlen($selectedParam)>0){
					$selectedParamArray = explode("=", $selectedParam);
					//dmp($selectedParamArray);
					$paramId = $selectedParamArray[0];
					$paramValue = $selectedParamArray[1];
					foreach( $params as &$param ){
						$param->countResults = 0;
						$param->identifier = $identifier;
						if($identifier == $paramId && ($param->id == intval($paramValue))){
							$param->selected =1 ;			
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * 
	 * Enter description here ...
	 * @return JPagination
	 */
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$total = $this->getTotalHotels();
			$this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit') );
			//$this->_pagination->setAdditionalUrlParam('controller','hotels');
			$this->_pagination->setAdditionalUrlParam('filterParams',rawurlencode($this->searchFilter["searchParams"]));
		}
		return $this->_pagination;
	}
	
	function getSuggestionsList($keyword){
		
		 $hotelTable =$this->getTable('hotels');
		 $limit = 5;
		 $cities = $hotelTable->getHotelCitiesSuggestions($keyword, $limit);
		 $provinces = $hotelTable->getHotelProvinceSuggestions($keyword, $limit);
		 $regions = $hotelTable->getHotelRegionSuggestions($keyword, $limit);
		 $hotels = $hotelTable->getHotelsSuggestions($keyword, $limit);
		 $accomodationTypes = $hotelTable->getHotelAccomodationTypeSuggestions($keyword, $limit);
		 $offerThemes = $hotelTable->getHotelOfferThemesSuggestions($keyword, $limit);
		 
		 $suggestionList = array();
		 if(!empty($cities))
			 $suggestionList = array_merge($suggestionList,$cities);
		 if(!empty($provinces))
			 $suggestionList = array_merge($suggestionList,$provinces);
		 if(!empty($regions))
		 	$suggestionList = array_merge($suggestionList,$regions);
		 if(!empty($hotels))
			$suggestionList = array_merge($suggestionList,$hotels);
		 if(!empty($accomodationTypes))
		 	$suggestionList = array_merge($suggestionList,$accomodationTypes);
		 if(!empty($offerThemes))
		 	$suggestionList = array_merge($suggestionList,$offerThemes);
		 
		//$suggestionList = '[{"id":"Grus grus","label":"Common Crane","value":"Common Crane","category":"cat1"},{"id":"Tringa totanus","label":"Common Redshank","value":"Common Redshank"},{"id":"Sterna sandvicensis","label":"Sandwich Tern","value":"Sandwich Tern"},{"id":"Caprimulgus europaeus","label":"European Nightjar","value":"European Nightjar"},{"id":"Upupa epops","label":"Eurasian Hoopoe","value":"Eurasian Hoopoe"},{"id":"Jynx torquilla","label":"Eurasian Wryneck","value":"Eurasian Wryneck"},{"id":"Picus viridis","label":"European Green Woodpecker","value":"European Green Woodpecker"},{"id":"Saxicola rubicola","label":"European Stonechat","value":"European Stonechat"},{"id":"Emberiza hortulana","label":"Ortolan Bunting","value":"Ortolan Bunting"},{"id":"Phalacrocorax carbo","label":"Great Cormorant","value":"Great Cormorant"},{"id":"Ficedula hypoleuca","label":"Eurasian Pied Flycatcher","value":"Eurasian Pied Flycatcher"},{"id":"Sitta europaea","label":"Eurasian Nuthatch","value":"Eurasian Nuthatch"}]'; 
		//dmp($suggestionList);
		$suggestionList = json_encode($suggestionList);
		
		return $suggestionList;
	}
	
}

