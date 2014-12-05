<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelExcursionsListing extends JModelLegacy{
	
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
		
		$redirect= JRequest::getVar("excursionRedirect",0);
		if($redirect==0)
			UserDataService::initializeExcursions();
	}
	
	public function getItem($pk = null){
		// Initialise variables.
		$hotel = HotelService::getHotel($this->getState('hotel.id'));
		//dmp($hotel->hotel_currency);
		UserDataService::setCurrency($hotel->hotel_currency, $hotel->currency_symbol);
		
		return $hotel;
	}
		
	function getAllExcursions(){
		
		$userData =  $_SESSION['userData'];
		$orderBy = "";
		$this->searchParams = array();
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["keyword"] =  $userData->keyword;
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["adults"] = $userData->adults;
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
		
		
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_EXCURSIONS,-1, $userData->start_date, $userData->end_date,null,null,$userData->adults,$userData->children);
		$this->_total = count($excursions);
		if(count($excursions))
			UserDataService::setCurrency($excursions[0]->country_currency_short, $excursions[0]->country_currency);
		return $excursions;
	}
	
	function getAllCourses(){
		$userData =  $_SESSION['userData'];
		$orderBy = "";
		$this->searchParams = array();
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["keyword"] =  $userData->keyword;
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["adults"] = $userData->adults;
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
	
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_COURSES,-1, $userData->start_date, $userData->end_date,null,null,$userData->adults,$userData->children);
		$this->_total = count($excursions);
		return $excursions;
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
	
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
			//$this->_pagination->setAdditionalUrlParam('controller','hotels');
			$this->_pagination->setAdditionalUrlParam('filterParams',rawurlencode($this->searchFilter["searchParams"]));
		}
		return $this->_pagination;
	}

	
	
}