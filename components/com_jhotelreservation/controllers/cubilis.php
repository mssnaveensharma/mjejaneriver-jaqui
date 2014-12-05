<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 CMSJunkie,  All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT.DS.'classes/cubilis/cubilisxml.php';
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'roomrateprices.php');
/**
 * The Cubilis Controller
 *
 */
class JHotelReservationControllerCubilis extends JControllerLegacy
{
	
	function __construct()
	{
		$this->log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		
		parent::__construct();
	}
	/**
	 * Display the view
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
	}


	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 */
	
	public function getHotelRoomList(){

		$get = JRequest::get( 'get' );
		$post= JRequest::get( 'post' );
	
		if(count($post)==0)
			$post = $get;
		
		$this->log->LogDebug("Cubilis :: call function getHotelRoomList()");
		//$this->log->LogDebug(serialize($post));
		
		$model = $this->getModel("Variables");
		$hotelId= JRequest::getInt("hotelId");
		$model->hotel_id = $hotelId;
		$model->tip_oper=-2;
		//dmp($hotelId);
		$startDate = date('Y-m-d');
		$endDate = date( "Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+10, date("Y")));;
		$rooms = HotelService::getHotelRooms($hotelId, $startDate, $endDate, array(), 2);
		//dmp($rooms);
		//exit;
		$cubilisXml = new CublisXml();
		$xml = $cubilisXml->generateHotelRoomList($hotelId,"gtmd!#","223",$rooms);
		//$xml = $cubilisXml->generateHotelAvailability(1,1,1,$rooms,$startDate,$endDate);
		
		//$reservation = $model->getReservation(2757);
		//dmp($reservation);
		//$xml = $cubilisXml->generateHotelReservations(1,1,1,$reservation);
		
		header("Content-type:text/xml");
		ob_clean();
		echo $xml;
		
	
		//echo($xmlContent);
		exit; 
	} 
	

	public function processRequest(){	
		$post = JRequest::get('post');
		$xml=JRequest::getVar('xml', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$xml = urldecode($xml);
		
		$this->log->LogDebug("Cubilis :: call function processRequest()");
		//$this->log->LogDebug(serialize($post));
		$this->log->LogDebug(serialize($xml));
		//dmp($xml);
		
		$result ="";
		$cubilisXml = new CublisXml();
		if(strrpos($xml,"OTA_HotelRoomListRQ")>0){
			$hotelIdentifier = $cubilisXml->getHotelId($xml);
			//dmp($hotelIdentifier);
			$credentials = HotelService::getCredential($hotelIdentifier);
			$hotelId = $credentials->hotel_id;
			
			$startDate = date('Y-m-d');
			$endDate = date( "Y-m-d",mktime(0, 0, 0, date("m"), date("d")+10, date("Y")));;
			$rooms =HotelService::getHotelRooms($hotelId, $startDate, $endDate, array(), 2);
			$result = $cubilisXml->generateHotelRoomList($hotelId, $credentials->user, $credentials->password, $rooms, $startDate, $endDate);
			echo $result;
		}else if(strrpos($xml,"OTA_HotelAvailNotifRQ")>0){
			$rates = $cubilisXml->parseAvailNotifRequest($xml);
			
			$model = $this->getModel("roomrateprices");
			$model->saveCustomDates($rates);
		}else if(strrpos($xml,"OTA_HotelRoomListRS")>0){	
		}else if(strrpos($xml,"OTA_HotelAvailNotifRQ")>0){	
			
		}else if(strrpos($xml,"OTA_HotelResRQ")>0){
			$this->log->LogDebug("OTA_HotelResRQ");
			$model = $this->getModel("Cubilis");
			$hotelIdentifier = $cubilisXml->getHotelId($xml);
		
			$credentials = HotelService::getCredential($hotelIdentifier);
			$hotelId = $credentials->hotel_id;
			
			$reservations = $model->getNewReservations($hotelId);
			//$this->log->LogDebug(serialize($reservations));
			//dmp($reservations);
			$result = $cubilisXml->generateHotelReservations($hotelId, $credentials->user, $credentials->password,  $reservations);
			$model->setReservationCubilisStatus($reservations);
			$this->log->LogDebug($result);
			echo $result;
		}
		//header("Content-type:text/xml");
		//ob_clean();
		//echo $result;
		//http_response_code(200);
		JFactory::getApplication()->close();
	}
	
}
