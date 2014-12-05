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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model'); 

class JHotelReservationModelManagePackages extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array 	= JRequest::getVar('package_id',  0, '', 'array');
		$h		= JRequest::getVar('hotel_id',  0, '');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
		$this->setHotelId((int)$h);
	}
	function setId($package_id)
	{
		// Set id and wipe data
		$this->_package_id		= $package_id;
		$this->_data			= null;
		$this->_hotels			= null;
	}

	function setHotelId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	
	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function &getDatas()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = ' SELECT * FROM #__hotelreservation_packages  WHERE hotel_id='.$this->_hotel_id." ORDER BY package_name ";
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query );
			
			foreach( $this->_data as $key => $value )
			{
				if( $this->_data[$key]->is_price_day == false )
					$this->_data[$key]->package_type_price = 1;
				$this->_data[$key]->package_prices = null;
				$this->_data[$key]->package_prices[ $value->package_price_1 ][] = 1;
				$this->_data[$key]->package_prices[ $value->package_price_2 ][] = 2;
				$this->_data[$key]->package_prices[ $value->package_price_3 ][] = 3;
				$this->_data[$key]->package_prices[ $value->package_price_4 ][] = 4;
				$this->_data[$key]->package_prices[ $value->package_price_5 ][] = 5;
				$this->_data[$key]->package_prices[ $value->package_price_6 ][] = 6;
				$this->_data[$key]->package_prices[ $value->package_price_7 ][] = 7;
			}
		}
		
		return $this->_data;
	}
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->_hotels )) 
		{
			$query = ' SELECT 
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						ORDER BY hotel_name, country_name ';
			//$this->_db->setQuery( $query );
			$this->_hotels = $this->_getList( $query );
		}
		return $this->_hotels;
	}
	
	function &getHotel()
	{
		$query = 	' SELECT 	
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels				h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						'.
					' WHERE 
						hotel_id = '.$this->_hotel_id;
		
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT * FROM #__hotelreservation_packages'.
						' WHERE package_id = '.$this->_package_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
					
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->package_id 			= null;
			$this->_data->hotel_id 				= null;
			$this->_data->package_name			= null;		
			$this->_data->package_type_price	= null;		
			$this->_data->package_price			= null;			
			$this->_data->package_price_1		= null;	
			$this->_data->package_price_2		= null;	
			$this->_data->package_price_3		= null;	
			$this->_data->package_price_4		= null;	
			$this->_data->package_price_5		= null;	
			$this->_data->package_price_6		= null;	
			$this->_data->package_price_7		= null;	
			$this->_data->package_price_midweek	= null;				
			$this->_data->package_price_weekend	= null;	
			$this->_data->package_number		= null;			
			$this->_data->package_description	= null;			
			$this->_data->is_available			= null;
			$this->_data->is_price_day			= null;		
			$this->_data->package_datas			= null;		
			$this->_data->package_datae			= null;		
			
		}
		if( $this->_data->is_price_day == false )
			$this->_data->package_type_price = 1;
			
		$this->_data->package_prices = null;
		$this->_data->package_prices[ $this->_data->package_price_1 ][] = 1;
		$this->_data->package_prices[ $this->_data->package_price_2 ][] = 2;
		$this->_data->package_prices[ $this->_data->package_price_3 ][] = 3;
		$this->_data->package_prices[ $this->_data->package_price_4 ][] = 4;
		$this->_data->package_prices[ $this->_data->package_price_5 ][] = 5;
		$this->_data->package_prices[ $this->_data->package_price_6 ][] = 6;
		$this->_data->package_prices[ $this->_data->package_price_7 ][] = 7;
		
		$this->_data->package_datas			= JHotelUtil::convertToFormat($this->_data->package_datas);
		$this->_data->package_datae			= JHotelUtil::convertToFormat($this->_data->package_datae);
		

		if( JRequest::getVar('is_error')=="1" )
		{
			//dmp($this->_data);
			$post = JRequest::get('post');
			if( count($post) > 0 )
			{
				foreach( $post as $key => $value )
				{
					//dmp($key.' >> '.$this->_data->$key.' | '.(property_exists($this->_data,$key)? "1" : "0"));
					if( property_exists($this->_data, $key) )
					{
						if( !is_array( $value ) ) 
							$this->_data->$key = $value;
						else
						{
						
						}
					}
					else
					{
						if( strpos($key, "package_price_day") !== false )
						{
							$this->_data->package_prices = array();
							foreach( $post[ 'package_price_day'] as $keyPos => $valPrice )
							{
								for( $day = 1; $day <=7;$day ++ )
								{
									//dmp( 'price_day_'.$keyPos.'_'.($day) );
									if( isset( $post[ 'day_'.$keyPos.'_'.($day) ] ) )
										$this->_data->package_prices[ $valPrice ][] = $day;
								}
							}
						}
						/*if( strpos($key, "room_number_datas") !== false )
						{
							$key_new = str_replace("room_number_datas_", "", $key);
							if( !isset($this->_data->room_intervals_numbers[ $key_new ]) )
							{
								$nr  							= new stdClass;
								$nr->room_interval_number_id 	= '';
								$nr->room_id				 	= '';
								$nr->nrs				 		= '';
								$nr->nre					 	= '';
								$nr->datas					 	= '';
								$nr->datae					 	= '';
								$nr->datai					 	= '';
								$nr->is_ignore_duplicate 		= false;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->datas = $value;
							
						}
						else if( strpos($key, "room_number_datae") !== false )
						{
							$key_new = str_replace("room_number_datae_", "", $key);
							if( !isset($this->_data->room_intervals_numbers[ $key_new ]) )
							{
								$nr  							= new stdClass;
								$nr->room_interval_number_id 	= '';
								$nr->room_id				 	= '';
								$nr->nrs				 		= '';
								$nr->nre					 	= '';
								$nr->datas					 	= '';
								$nr->datae					 	= '';
								$nr->datai					 	= '';
								$nr->is_ignore_duplicate 		= false;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->datae = $value;
							
						}
						else if( strpos($key, "room_number_datai") !== false )
						{
							$key_new = str_replace("room_number_datai_", "", $key);
							if( !isset($this->_data->room_intervals_numbers[ $key_new ]) )
							{
								$nr  							= new stdClass;
								$nr->room_interval_number_id 	= '';
								$nr->room_id				 	= '';
								$nr->nrs				 		= '';
								$nr->nre					 	= '';
								$nr->datas					 	= '';
								$nr->datae					 	= '';
								$nr->datai					 	= '';
								$nr->is_ignore_duplicate 		= false;
								
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->datai = $value;
							
						}
						else */
					}
				}
				
			}
		}

		return $this->_data;
	}

	function store($data)
	{	
				try
		{
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
				
			$row = $this->getTable();

			$data["package_datas"]=JHotelUtil::convertToMysqlFormat($data["package_datas"]);
			$data["package_datae"]=JHotelUtil::convertToMysqlFormat($data["package_datae"]);
				
			
			// Bind the form fields to the table
			if (!$row->bind($data)) 
			{
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			// Make sure the record is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
			}

			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError( $this->_db->getErrorMsg() );
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			if( $data['package_id'] =='' || $data['package_id'] ==0 || $data['package_id'] ==null )
				$data['package_id'] = $this->_db->insertid();
				
			
			if($data['package_datai']==null || $data['package_datai'] =='NaN-NaN-NaN' )
				$data['package_datai'] ='0000-00-00';
			
			$di 	= explode( ',', $data['package_datai'] ); 
			$arr_di = array();
			// dmp($di);
			// exit;
			foreach( $di as $di_v)
			{ 
				if( $di_v == '0000-00-00' || $di_v =='NaN-NaN-NaN')
					continue;
				$row = $this->getTable('ManagePackagesDatesIgnored');
				$d 						= new stdClass();
				$d->package_date_ignored_id		= 0;
				$d->package_id 			= $data['package_id'];
				$d->package_date_data	= $di_v;
				
				if (!$row->bind($d)) 
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
					
				}
				// Make sure the record is valid
				if (!$row->check()) 
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				if (!$row->store()) 
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				
				$arr_di[] =$this->_db->insertid();
			}
			
			$query = " DELETE FROM #__hotelreservation_packages_date_ignored
						WHERE package_id = '".$data['package_id']."'
						".( count($arr_di)> 0 ? " AND package_date_ignored_id NOT IN (".implode(',', $arr_di).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			// dmp($d);
			
			
			//$this->_db->CommitTrans();
			$query = "COMMIT";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
			//$this->_db->RollbackTrans();
			$query = "ROLLBACK";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			return false;
		}

		return true;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'package_id', array(0), 'post', 'array' );
		
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations							c
					INNER JOIN #__hotelreservation_confirmations_rooms_packages		hp USING( confirmation_id )
					WHERE 
						hp.package_id IN (".implode(',', $cids).") AND c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
						
		$checked_records = $this->_getList( $query );
		if ( count($checked_records) > 0 ) 
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_PACKAGE_REMOVE',true) );
			return false;
		}

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					$msg = JText::_('LNG_ERROR_DELETE_PACKAGE',true);
					return false;
				}
			}
		}
		return true;

	}
	
	function state()
	{
		$query = 	" UPDATE #__hotelreservation_packages SET is_available = IF(is_available, 0, 1) WHERE package_id = ".$this->_package_id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
			
		}
		return true;
	}




}
?>