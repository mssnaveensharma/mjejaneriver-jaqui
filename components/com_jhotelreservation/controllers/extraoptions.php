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

class JHotelReservationControllerExtraOptions extends JControllerLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function showExtras(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("showExtras");
	
		//add room if does not exist
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotelId");
		
		UserDataService::updateRooms($hotelId, $reservedItems);
		
		$userData = UserDataService::getUserData();
		$userData->hotelId = $hotelId;
		
		
		$appSetting = JHotelUtil::getApplicationSettings();
		if(!isset($userData->currency)){
			$hotel = HotelService::getHotel($hotelId);
			UserDataService::setCurrency($hotel->hotel_currency, $hotel->currency_symbol);
		}
		
		//dump($userData->reservedItems);
		//dump($userData->hotelId);
		//$log->LogDebug(serialize($userData->reservedItems));
		
		$model = $this->getModel("ExtraOptions");
		$extraOptions = $model->getExtraOptions();
		
			
		if(count($extraOptions)>0 && PROFESSIONAL_VERSION==1 && $appSetting->is_enable_extra_options){
			//dump("show extra view");
			JRequest::setVar("view","extraoptions");
			parent::display();
		}else{
			if(count($userData->reservedItems) < $userData->rooms ){
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotel.showHotel&hotel_id='.$userData->hotelId."&reservedItems=".$reservedItems, false));
			}
			else{
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&reservedItems='.$reservedItems, false));
			}
		}
		//exit;
		//$log->LogDebug(serialize($userData->reservedItems));
		//$log->LogDebug("End showExtras");
	}
	
	function addExtraOptions(){
		//$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		//$log->LogDebug("addExtraOptions");
		
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotel_id");
		
		if(!empty($reservedItems)){
			UserDataService::updateRooms($hotelId, $reservedItems);
		}
		
		$data = JRequest::get("post");
		
		$current = $data["current"];
		$extraOptions = array();
		if(isset($data["extraOptionIds"])){
			foreach($data["extraOptionIds"] as $key=>$value){
				$extraOption = explode("|",$value);
	
				if($extraOption[5]>0 || $extraOption[6]>0)
					continue;
				if(isset($data["extra-option-days-".$extraOption[3]])){
					$extraOption[6] = $data["extra-option-days-".$extraOption[3]];
				}
				if(isset($data["extra-option-persons-".$extraOption[3]])){
					$extraOption[5] = $data["extra-option-persons-".$extraOption[3]];
				}
			
				$extraOptions[$key] = implode("|",$extraOption);
			}
		}

		
		if(count($extraOptions)>0){
			UserDataService::addExtraOptions($extraOptions);
		}	
		 
		$userData = UserDataService::getUserData();
		
		if(count($userData->reservedItems) < $userData->rooms ){
		    $extra = implode("#",$extraOptions);
			$extraParam ="";
			if(!empty( $extra)){
				$extraParam = "&extraOptions=".$extra;
			}
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotel.showHotel&hotel_id='.$hotelId."&reservedItems=".$reservedItems.$extraParam, false));
		}
		else{
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&hotel_id='.$hotelId."&reservedItems=".$reservedItems, false));
		}
		
		//$log->LogDebug("End addExtraOptions");
	}
	
	function back(){
		UserDataService::removeLastRoom();
		$userData = UserDataService::getUserData();
		$hotel = HotelService::getHotel($userData->hotelId);
		$link = JHotelUtil::getHotelLink($hotel);
		$this->setRedirect($link);
	}
}