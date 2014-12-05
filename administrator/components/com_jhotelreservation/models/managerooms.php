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

class JHotelReservationModelManageRooms extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array 	= JRequest::getVar('room_id',  0, '', 'array');
		$h		= JRequest::getVar('hotel_id',  0, '');
		
		if(isset($array[0])) $this->setId((int)$array[0]);
		$this->setHotelId((int)$h);
	}
	function setId($room_id)
	{
		// Set id and wipe data
		$this->_room_id		= $room_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}
	
	function setHotelId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	 
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	function &getDatas()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = ' SELECT * FROM #__hotelreservation_rooms WHERE hotel_id='.$this->_hotel_id." ORDER BY room_order ";
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query );

			foreach( $this->_data as $key => $value )
			{
				$this->_data[$key]->room_prices = null;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_1 ][] = 1;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_2 ][] = 2;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_3 ][] = 3;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_4 ][] = 4;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_5 ][] = 5;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_6 ][] = 6;
				$this->_data[$key]->room_prices[ $this->_data[$key]->room_price_7 ][] = 7;

				$query = ' SELECT * FROM #__hotelreservation_rooms_seasons WHERE room_id = "'.$value->room_id.'"
								ORDER BY room_season_datas, room_season_name';
				//$this->_db->setQuery( $query );
				$this->_data[ $key ]->seasons = $this->_getList( $query );

			}
		}
		//dmp($this );
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
		
			$query = 	' SELECT * FROM #__hotelreservation_rooms'.
						' WHERE 
							room_id = '.$this->_room_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
			if( $this->_data )
				$this->_data->option_ids  = explode(',', $this->_data->option_ids);
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->room_id 			= null;
			$this->_data->hotel_id 			= null;
			$this->_data->room_name			= null;			
			$this->_data->type_price				= null;		
			$this->_data->room_price_1				= null;			
			$this->_data->room_price_2				= null;			
			$this->_data->room_price_3				= null;			
			$this->_data->room_price_4				= null;			
			$this->_data->room_price_5				= null;			
			$this->_data->room_price_6				= null;			
			$this->_data->room_price_7				= null;			
			$this->_data->room_price				= null;				
			$this->_data->room_price_midweek		= null;				
			$this->_data->room_price_weekend		= null;				
			$this->_data->room_capacity		= null;
			$this->_data->number_of_rooms	= null;			
			$this->_data->room_description	= null;			
			$this->_data->room_short_description	= null;
			$this->_data->room_main_description		= null;
			$this->_data->room_details				= null;
			$this->_data->is_available		= null;
			// $this->_data->room_datas		= date('Y-m-d');
			// $this->_data->room_datae		= date('Y-m-d');
			$this->_data->option_ids		= null;
			$this->_data->pers_price		= null;
			$this->_data->front_display		= null;
			$this->_data->single_discount		= null;
			$this->_data->single_supplement		= null;
			$this->_data->has_breakfast		= null;
			//check temporary files
			$pictures = JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_ROOM_PICTURES.($this->_data->room_id+0)."/*.*");
			$files = glob( $pictures );
			if(is_array($files) && count($files)>0)
				sort($files);
			//~check temporary files
			$this->_data->pictures			= array();
			$this->_data->dates_room		= array();
			$this->_data->seasons			= array();
		
			if(is_array($files) && count($files)>0)
			foreach( $files as $value )
			{
				$this->_data->pictures[]	= array( 
													'room_picture_info' 		=> 'add from cache',
													'room_picture_path' 		=> PATH_ROOM_PICTURES.($this->_data->room_id+0).'/'.basename($value),
													'room_picture_enable'		=> 1
												);
			}
			
			$obj = new stdClass;
			$obj->nrs		= '';
			$obj->nre		= '';
			$obj->datas		= '';
			$obj->datae		= '';
			$obj->datai		= '';
			$obj->is_ignore_duplicate = true;
			$this->_data->room_intervals_numbers[] = $obj;
			//clean all temporary seasons
			if( JRequest::getVar('is_error')=="0" || JRequest::getVar('is_error') == null )
			{
				$query = " DELETE FROM #__hotelreservation_rooms_seasons WHERE room_id =0 "	;
				$this->_db->setQuery( $query );
				$this->_db->query();
				
				$query = " DELETE FROM #__hotelreservation_rooms_seasons_date_ignored WHERE room_id =0 "	;
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
			//clean all temporary seasons
			
		}		
		else
		{
			$query = "
					SELECT 
						*
					FROM #__hotelreservation_rooms_seasons
					WHERE room_id ='".$this->_data->room_id ."'
					ORDER BY room_season_datas, room_season_name "
				;
			// dmp($query);
			//$this->_db->setQuery( $query );
			$this->_data->seasons 	= $this->_getList( $query );

			$query = "
					SELECT 
						*
					FROM #__hotelreservation_rooms_intervals_numbers
					WHERE room_id =".$this->_data->room_id ."
					ORDER BY nrs, nre "
				;
			// dmp($query);
			//$this->_db->setQuery( $query );
			$this->_data->room_intervals_numbers 	= $this->_getList( $query );
			if( count($this->_data->room_intervals_numbers)  == 0 )
			{
				$obj = new stdClass;
				$obj->nrs		= '';
				$obj->nre		= '';
				$obj->datas		= '';
				$obj->datae		= '';
				$obj->datai		= '';
				$obj->is_ignore_duplicate = true;
				$this->_data->room_intervals_numbers[] = $obj;
															
			}
			
			//check temporary files
			$query = "
					SELECT 
						*
					FROM #__hotelreservation_rooms_pictures
					WHERE room_id =".$this->_data->room_id ."
					ORDER BY room_picture_id "
				;
			// dmp($query);
			//$this->_db->setQuery( $query );
			$files = $this->_getList( $query );
			$this->_data->pictures			= array();
			foreach( $files as $value )
			{
				$this->_data->pictures[]	= array( 
													'room_picture_info' 		=> $value->room_picture_info,
													'room_picture_path' 		=> $value->room_picture_path,
													'room_picture_enable'		=> $value->room_picture_enable,
												);
			}
		}
				
		
		//prepare price days
		$this->_data->room_prices = null;
		$this->_data->room_prices[ $this->_data->room_price_1 ][] = 1;
		$this->_data->room_prices[ $this->_data->room_price_2 ][] = 2;
		$this->_data->room_prices[ $this->_data->room_price_3 ][] = 3;
		$this->_data->room_prices[ $this->_data->room_price_4 ][] = 4;
		$this->_data->room_prices[ $this->_data->room_price_5 ][] = 5;
		$this->_data->room_prices[ $this->_data->room_price_6 ][] = 6;
		$this->_data->room_prices[ $this->_data->room_price_7 ][] = 7;
		// dmp($this->_data->room_prices);
		//~prepare price days
		
		if( JRequest::getVar('is_error')=="1" )
		{
		
			//dmp($this->_data);
			$post = JRequest::get('post');
			if( count($post) > 0 )
			{
				$this->_data->option_ids 				= array();
				$this->_data->room_intervals_numbers	= array();
				$this->_data->pictures					= array();

				$query = "
						SELECT 
							*
						FROM #__hotelreservation_rooms_seasons
						WHERE room_id ='".$this->_data->room_id ."'
						ORDER BY room_season_datas, room_season_name "
					;
				// dmp($query);
				//$this->_db->setQuery( $query );
				$this->_data->seasons 	= $this->_getList( $query );

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
						if( strpos($key, "option_ids") !== false )
						{
							if( is_array($value ) )
							{
								foreach( $value as $v )
									$this->_data->option_ids[] = $v;
							}
							else
								$this->_data->option_ids[] = $value[0];
						}
						else if( strpos($key, "room_picture_info") !== false )
						{
							foreach( $value as $k => $v )
							{
								if(!isset( $this->_data->pictures[ $k ] ) )
								{
									$this->_data->pictures[ $k ]	= array( 
																				'room_picture_info' 		=> '',
																				'room_picture_path' 		=> '',
																				'room_picture_enable'		=> ''
																	);
								}
								$this->_data->pictures[ $k ]['room_picture_info'] = $v;
							}
						}
						else if( strpos($key, "room_picture_enable") !== false )
						{
							foreach( $value as $k => $v )
							{
								if(!isset( $this->_data->pictures[ $k ] ) )
								{
									$this->_data->pictures[ $k ]	= array( 
																				'room_picture_info' 		=> '',
																				'room_picture_path' 		=> '',
																				'room_picture_enable'		=> ''
																	);
								}
								$this->_data->pictures[ $k ]['room_picture_enable'] = $v;
							}
						}
						else if( strpos($key, "room_picture_path") !== false )
						{
							foreach( $value as $k => $v )
							{
								if(!isset( $this->_data->pictures[ $k ] ) )
								{
									$this->_data->pictures[ $k ]	= array( 
																				'room_picture_info' 		=> '',
																				'room_picture_path' 		=> '',
																				'room_picture_enable'		=> ''
																	);
								}
								$this->_data->pictures[ $k ]['room_picture_path'] = $v;
							}
						}
						else if( strpos($key, "room_number_start") !== false )
						{
							$key_new = str_replace("room_number_start_", "", $key);
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
								$nr->is_ignore_duplicate 		= true;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->nrs = $value;
							
						}
						else if( strpos($key, "room_number_stop") !== false )
						{
							$key_new = str_replace("room_number_stop_", "", $key);
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
								$nr->is_ignore_duplicate 		= true;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->nre = $value;
							
						}
						else if( strpos($key, "is_ignore_duplicate") !== false )
						{
							$key_new = str_replace("is_ignore_duplicate_", "", $key);
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
								$nr->is_ignore_duplicate 		= true;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->is_ignore_duplicate = $value;
							
						}
						else if( strpos($key, "room_number_datas") !== false )
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
								$nr->is_ignore_duplicate 		= true;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->datas = JHotelUtil::convertToFormat($value);
							
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
								$nr->is_ignore_duplicate 		= true;
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							//convert date format
						
								
							$this->_data->room_intervals_numbers[ $key_new ]->datae = JHotelUtil::convertToFormat($value);
							
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
								$nr->is_ignore_duplicate 		= true;
								
								$this->_data->room_intervals_numbers[ $key_new] = $nr;
							}
							
							$this->_data->room_intervals_numbers[ $key_new ]->datai = $value;
							
						}
						else if( strpos($key, "price_day") !== false )
						{
							$this->_data->room_prices = array();
							foreach( $post[ 'price_day'] as $keyPos => $valPrice )
							{
								for( $day = 1; $day <=7;$day ++ )
								{
									//dmp( 'price_day_'.$keyPos.'_'.($day) );
									if( isset( $post[ 'day_'.$keyPos.'_'.($day) ] ) )
										$this->_data->room_prices[ $valPrice ][] = $day;
								}
							}
						}
					}
				}
				
			}
		}
		
		//clean unnecessary files
		$files = glob( JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_ROOM_PICTURES.$this->_data->room_id."/"."*.*" ));
		if(is_array($files) && count($files)>0)
		foreach( $files as $pic )
		{
			$is_find = false;
			foreach( $this->_data->pictures as $value )
			{
			
				if( $pic == JHotelUtil::makePathFile(JPATH_COMPONENT.$value['room_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			if( $is_find == false )
				@unlink( $pic );
		}
		//~clean unnecessary files
		//dmp($this->_data->option_ids);
		//dmp($this->_data);
		return $this->_data;
	}

	
	function &getFeatureOptionsRoom()
	{
		$query = 	' 
						SELECT 
								hrf.feature_id,
								hrf.feature_name,
								hrf.is_multiple_selection,
								hrf.number_of_options,
								hrfo.option_id ,
								hrfo.option_name 
							FROM #__hotelreservation_room_features hrf
							LEFT JOIN #__hotelreservation_room_feature_options hrfo	USING(feature_id)
					';
		
		//$this->_db->setQuery( $query );
		$feature_options = $this->_getList( $query );
		
		$data	= array();
		foreach( $feature_options as $key => $value )
		{
			if( $value->number_of_options ==0 )
				continue;
			if( !isset($data[ $value->feature_id ]) )
				$data[ $value->feature_id ] = new stdClass;
			$data[$value->feature_id]->feature_name	= $value->feature_name;
			if( !isset($data[$value->feature_id]->options))
				$data[$value->feature_id]->options = array();
			
			if( count( $data[ $value->feature_id ]->options ) >= $value->number_of_options )
				continue;
			
			$data[$value->feature_id]->options[]				= array( 'option_id'=>$value->option_id, 'option_name'=>$value->option_name) ;
			$data[$value->feature_id]->is_multiple_selection	= $value->is_multiple_selection;
			
		}
		return $data;
	}
	
	function store($data)
	{	
		// dmp($data);
		// exit;
		if(!isset($data["has_breakfast"])){
			$data["has_breakfast"] = 0;
		}
		if(isset($data['start_date']))
			$data['start_date']= JHotelUtil::convertToMysqlFormat($data['start_date']);
		if(isset($data['end_date']))
			$data['end_date']= JHotelUtil::convertToMysqlFormat($data['end_date']);
		try
		{
			//$this->_db->BeginTrans();
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();

			$row = $this->getTable();
			if(count($data['option_ids']) > 0 )
				$data['option_ids'] = implode(',', $data['option_ids'] );
			else
				$data['option_ids'] = '';

			// Bind the form fields to the table
			if (!$row->bind($data)) 
			{
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}
			// Make sure the record is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}

			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError( $this->_db->getErrorMsg() );
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}
			
			
			//prepare photos
			$path_old = JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_ROOM_PICTURES.($data['room_id']+0)."/");
			$files = glob( $path_old."*.*" );
			
			if( $data['room_id'] =='' || $data['room_id'] ==0 || $data['room_id'] ==null )
			{
				$data['room_id'] = $this->_db->insertid();
				
				//update all temporary seasons
				$query = " UPDATE #__hotelreservation_rooms_seasons SET room_id  = ".$data['room_id']." WHERE room_id =0 "	;
				$this->_db->setQuery( $query );
				$this->_db->query();
				//~update all temporary seasons
				
			}	
				
			$this->_room_id = $data['room_id'];
			
			$path_new = JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_ROOM_PICTURES.($data['room_id']+0)."/");
		
			
			$picture_ids 	= array();
			foreach( $data['pictures'] as $value )
			{
				$row = $this->getTable('ManageRoomPictures');
	
				// dmp($key);
				$pic 						= new stdClass();
				$pic->room_picture_id		= 0;
				$pic->room_id 				= $data['room_id'];
				$pic->room_picture_info		= $value['room_picture_info'];
				$pic->room_picture_path		= $value['room_picture_path'];
				$pic->room_picture_enable	= $value['room_picture_enable'];
				//dmp($pic);
				$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->room_picture_path) );
			
				if( !is_file($file_tmp) )
					continue;
				
				if( !is_dir($path_new) )
				{
					if( !@mkdir($path_new) )
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
				
					// dmp(($path_old.basename($pic->room_picture_path).",".$path_new.basename($pic->room_picture_path)));
					// exit;
				if( $path_old.basename($pic->room_picture_path) != $path_new.basename($pic->room_picture_path) )
				{	
					if(@rename($path_old.basename($pic->room_picture_path),$path_new.basename($pic->room_picture_path)) ) 
					{
						
						$pic->room_picture_path	 = PATH_ROOM_PICTURES.($data['room_id']+0).'/'.basename($pic->room_picture_path);
						//@unlink($path_old.basename($pic->room_picture_path));
					}
					else
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
				
				if (!$row->bind($pic)) 
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

				// Store the web link table to the database
				if (!$row->store()) 
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				
				$picture_ids[] = $this->_db->insertid();
				
				
			}
		
			$files = glob( $path_new."*.*" );
			
			foreach( $files as $pic )
			{
				$is_find = false;
				foreach( $data['pictures'] as $value )
				{
					if( $pic == JHotelUtil::makePathFile(JPATH_COMPONENT.$value['room_picture_path']) )
					{
						$is_find = true;
						break;
					}
				}
				if( $is_find == false )
					@unlink( JHotelUtil::makePathFile(JPATH_COMPONENT.$value['room_picture_path']) );
			}
			
			$query = " DELETE FROM #__hotelreservation_rooms_pictures 
						WHERE room_id = '".$data['room_id']."'
						".( count($picture_ids)> 0 ? " AND room_picture_id NOT IN (".implode(',', $picture_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			//~prepare photos
			
			
			//prepare intervals numbers
			$room_interval_number_ids			= array();
			$room_number_ids					= array();
			$room_number_date_ignored_ids		= array();

			$c = count($data['room_intervals_numbers']['nrs']);
			for( $i=0;$i<$c;$i++)
			{
				$row = $this->getTable('ManageRoomIntervalsNumbers');
				$d 						= new stdClass();
				$d->room_date_id		= 0;
				$d->room_id 			= $data['room_id'];
				$d->nrs					= $data['room_intervals_numbers']['nrs'][$i];
				$d->nre					= $data['room_intervals_numbers']['nre'][$i];
				$d->is_ignore_duplicate	= $data['room_intervals_numbers']['is_ignore_duplicate'][$i];
				$d->datas				= JHotelUtil::convertToMysqlFormat($data['room_intervals_numbers']['datas'][$i]);
				$d->datae				= JHotelUtil::convertToMysqlFormat($data['room_intervals_numbers']['datae'][$i]);
				$d->datai				= $data['room_intervals_numbers']['datai'][$i];
				
				if($d->datai==null || $d->datai =='NaN-NaN-NaN' )
					$d->datai ='0000-00-00';
				
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
				
				
				$d->room_interval_number_id = $this->_db->insertid();
				$room_interval_number_ids[] = $d->room_interval_number_id;
				//prepare intervals numbers explicit
				for( $k = $d->nrs; $k<=$d->nre;$k++ )
				{
					$row = $this->getTable('ManageRoomNumbers');
					$d_ex 								= new stdClass();
					$d_ex->room_id						= $d->room_id;
					$d_ex->room_number_number			= $k;
					$d_ex->type_price					= $data['type_price'];
					$d_ex->room_number_price_1			= $data['room_price_1'];
					$d_ex->room_number_price_2			= $data['room_price_2'];
					$d_ex->room_number_price_3			= $data['room_price_3'];
					$d_ex->room_number_price_4			= $data['room_price_4'];
					$d_ex->room_number_price_5			= $data['room_price_5'];
					$d_ex->room_number_price_6			= $data['room_price_6'];
					$d_ex->room_number_price_7			= $data['room_price_7'];
					$d_ex->room_number_price			= $data['room_price'];
					$d_ex->room_number_midweek			= $data['room_price_midweek'];
					$d_ex->room_price_weekend			= $data['room_price_weekend'];
					
					$d_ex->room_number_datas			= JHotelUtil::convertToMysqlFormat($d->datas);
					$d_ex->room_number_datae			= JHotelUtil::convertToMysqlFormat($d->datae);
					$d_ex->room_number_datai			= $d->datai;
					
					
					if (!$row->bind($d_ex)) 
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
					$d->room_number_id = $this->_db->insertid();

					$room_number_ids[] = $d->room_number_id;
					
					
					
					$di 	= explode( ',', $d->datai );
					$arr_di = array();
					foreach( $di as $di_v)
					{
						if($di_v =='NaN-NaN-NaN' || $di_v =='' || $di_v =='0000-00-00' )
							continue;
						$row = $this->getTable('ManageRoomNumbersDatesIgnored');
						$d_ex 								= new stdClass();
						$d_ex->room_id						= $d->room_id;
						$d_ex->room_interval_number_id		= $d->room_interval_number_id;
						$d_ex->room_number_id				= $d->room_number_id;
						$d_ex->room_number_date_number		= $k;
						$d_ex->room_number_date_data		= $di_v;
						
						if (!$row->bind($d_ex)) 
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
						
						$room_number_date_ignored_ids[] = $this->_db->insertid();
						
						$arr_di[] = $di_v;
					}
					
				}
				
				//~prepare intervals numbers explicit
				
			}
			
			$query = " DELETE FROM #__hotelreservation_rooms_intervals_numbers
						WHERE room_id = '".$data['room_id']."'
						".( count($room_interval_number_ids)> 0 ? " AND room_interval_number_id NOT IN (".implode(',', $room_interval_number_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			$query = " DELETE FROM #__hotelreservation_rooms_numbers
						WHERE room_id = '".$data['room_id']."'
						".( count($room_number_ids)> 0 ? " AND room_number_id NOT IN (".implode(',', $room_number_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			
			
			$query = " DELETE FROM #__hotelreservation_rooms_numbers_date_ignored
						WHERE room_id = '".$data['room_id']."'
						".( count($room_number_date_ignored_ids)> 0 ? " AND room_number_date_ignored_id NOT IN (".implode(',', $room_number_date_ignored_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			//~prepare intervals numbers
		
			//$this->_db->CommitTrans();
			$query = "COMMIT";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
		// dmp($ex);
		// exit;
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
		$cids = JRequest::getVar( 'room_id', array(0), 'post', 'array' );
		
		try
		{
		
			$query = " 	SELECT  
							*  
						FROM #__hotelreservation_confirmations				c
						INNER JOIN #__hotelreservation_confirmations_rooms	hr USING( confirmation_id )
						WHERE 
							hr.room_id IN (".implode(',', $cids).") AND c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
						";
							
			$checked_records = $this->_getList( $query );
			if ( count($checked_records) > 0 ) 
			{
				JError::raiseWarning( 500, JText::_('LNG_SKIP_ROOM_REMOVE',true) );
				return false;
			}
			
			
			$query = "START TRANSACTION";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			if (count( $cids )) 
			{
				foreach($cids as $cid) 
				{
					$query = 	" DELETE FROM  #__hotelreservation_rooms WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM  #__hotelreservation_rooms_intervals_numbers WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE  FROM  #__hotelreservation_rooms_numbers WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM  #__hotelreservation_rooms_pictures WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM  #__hotelreservation_rooms_numbers_date_ignored WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					$query = 	" DELETE FROM  #__hotelreservation_rooms_seasons WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}

					$query = 	" DELETE FROM  #__hotelreservation_rooms_seasons_date_ignored WHERE room_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}

					if( is_dir(JPATH_COMPONENT.PATH_ROOM_PICTURES.$cid) )
					{
						$files = glob( JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_ROOM_PICTURES.$cid."/"."*.*" ));
						foreach( $files as $pic )
						{
							if( !@unlink($pic) )
								throw( new Exception($this->_db->getErrorMsg()) );
						}
						if( !@rmdir(JPATH_COMPONENT.PATH_ROOM_PICTURES.$cid) )
							throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
			}
			
			$query = "COMMIT";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
			// dmp($ex);
			// exit;
			$query = "ROLLBACK";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			$msg = JText::_('LNG_ERROR_DELETE_ROOM',true);
			return false;
		}
		return true;

	}
	
	function state()
	{
		$query = 	" UPDATE #__hotelreservation_rooms SET is_available = IF(is_available, 0, 1) WHERE room_id = ".$this->_room_id ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
			
		}
		return true;
	}


	function checkNumbers( $hotel_id,  $room_id, $numbers, &$msg )
	{
		if( count($numbers) == 0 )
			return true;
		$query 			= 	" 	SELECT 
									hr.room_name  
								FROM #__hotelreservation_rooms_numbers 			hrn
								INNER JOIN #__hotelreservation_rooms			hr		USING(room_id)
								WHERE 
									hrn.room_id <> '".$room_id ."'
									AND
									hr.hotel_id = '".$hotel_id ."'
									AND 
									hrn.room_number_number IN (".implode(',', $numbers).")
								LIMIT 1	
								";
		// dmp($query);
		// exit;
		$row_check		= $this->_getList( $query );
		if( count($row_check) > 0 )
		{
			$room_info = '';
			foreach( $row_check	as $value )
			{
				$room_info = '&nbsp;('.$value->room_name.' )';
			}
			$msg = JText::_('LNG_HAS_INTERSECT_INTERVALS_DIFFERENT_ROOM',true).$room_info;
			return false;
		}
		//dmp($interval_rows);
		//exit;
		return true;
	}
	
	function checkDuplicateNumbers( $numbers, &$msg )
	{
		foreach( $numbers as $k => $value )
		{
			$arr_tmp = $numbers;
			unset($arr_tmp[$k] );
			if( in_array($value, $arr_tmp) )
			{
				$msg = JText::_('LNG_HAS_INTERSECT_INTERVALS_SAME_ROOM',true).' '.$value->room_name;
				return true;
			}
		}
		return false;
	}
	
	function validNumbers( $room_intervals_numbers, &$msg )
	{
		$c = count($room_intervals_numbers['nrs']);
		
		if( $c==1 && $room_intervals_numbers['nrs'][0] =="")
		{
			$msg = JText::_('LNG_ERROR_FILL_NUMBERS',true);
			return false;
		}
		for( $i=0;$i<$c;$i++)
		{
			if( $room_intervals_numbers['nrs'][$i] > $room_intervals_numbers['nre'][$i] )
			{
				$msg = JText::_('LNG_ERROR_FILL_NUMBERS',true);
				return false;
			}
			
			else if( 
					$room_intervals_numbers['datas'][$i] == '0000-00-00'
					&&
					$room_intervals_numbers['datas'][$i] == ''
					&&
					!checkdate(
									date('m', strtotime($room_intervals_numbers['datas'][$i]) ), 
									date('d', strtotime($room_intervals_numbers['datas'][$i]) ), 
									date('Y', strtotime($room_intervals_numbers['datas'][$i]) )
								) 
					
			) 
			{
			
				$msg = JText::_('LNG_ERROR_FILL_NUMBERS',true);
				return false;
			}
			else if( 
					$room_intervals_numbers['datae'][$i] == '0000-00-00'
					&&
					$room_intervals_numbers['datae'][$i] == ''
					&&
					!checkdate(
									date('m', strtotime($room_intervals_numbers['datae'][$i]) ), 
									date('d', strtotime($room_intervals_numbers['datae'][$i]) ), 
									date('Y', strtotime($room_intervals_numbers['datae'][$i]) )
								) 
					
			) 
			{
			
				$msg = JText::_('LNG_ERROR_FILL_NUMBERS',true);
				return false;
			}
			if( strtotime($room_intervals_numbers['datas'][$i]) > strtotime($room_intervals_numbers['datae'][$i]) 
				&& 
				( $room_intervals_numbers['datas'][$i] != '' && $room_intervals_numbers['datas'][$i] != '0000-00-00' )
				&& 
				( $room_intervals_numbers['datae'][$i] != '' && $room_intervals_numbers['datae'][$i] != '0000-00-00' )
			)
			{
				$msg = JText::_('LNG_ERROR_FILL_NUMBERS',true);
				return false;
			}
			
		}
		return true;
	}

	function checkNumbersUnAssigned2Reservation( $hotel_id, $room_id, $numbers, &$msg )
	{
		
		$query = "
				SELECT 
					hrn.*
				FROM #__hotelreservation_rooms_numbers		hrn
				INNER JOIN #__hotelreservation_rooms		hr		USING(room_id)
				WHERE 
					hr.hotel_id =".$hotel_id ." 
					AND 
					hrn.room_id =".$room_id ."
				ORDER BY hrn.room_number_number "
			;
		// dmp($query);
		//$this->_db->setQuery( $query );
		$arr_old_numbers = array();
		$rows 	= $this->_getList( $query );
		
		if(count($rows)>0)
		foreach( $rows as $value )
		{
			if( in_array($value->room_number_number, $numbers) )
				continue;
			$arr_old_numbers[] = $value->room_number_number;
		}
		if( count($arr_old_numbers)==0 )
			return true;
			
		$query 			= 	" 	SELECT 
									hcrnd.*  
								FROM #__hotelreservation_confirmations_rooms_numbers_dates 	hcrnd
								INNER JOIN #__hotelreservation_confirmations_rooms			hcr		USING(room_id)
								WHERE 
									hcr.hotel_id	= '".$hotel_id ."'
									AND
									hcrnd.room_id = '".$room_id ."'
									AND 
									hcrnd.room_number_number IN (".implode(',', $arr_old_numbers).")";
		$row_check		= $this->_getList( $query );
		if( count($row_check) > 0 )
		{
			$msg = JText::_('LNG_NUMBERS_ALLREADY_RESERVED',true);
			return false;
		}
		
		return true;
	}
	
	function checkDateIgnoredAssigned2Reservation( $hotel_id, $room_id, $datei, &$msg )
	{
		//dmp($datei);
		
		foreach( $datei as $key => $value )
		{
			if( strlen($value) == 0 )
				continue;
			$v = explode(',', $value);
			$query = "
				SELECT 
					hcrnd.*
				FROM #__hotelreservation_confirmations_rooms_numbers_dates	hcrnd
				INNER JOIN #__hotelreservation_confirmations_rooms			hcr		USING(room_id)
				WHERE 
					hcr.hotel_id	= '".$hotel_id ."'
					AND
					hcrnd.room_id =".$room_id ." 
					AND 
					hcrnd.room_number_data IN ('".implode("','", $v)."')
					AND
					hcrnd.room_number_number = $key
				ORDER BY hcrnd.room_number_number "
			;
			//dmp($query);
			$row_check		= $this->_getList( $query );
		
			if( count($row_check) > 0 )
			{
				$msg = JText::_('LNG_DATE_ALLREADY_RESERVED',true);
				return false;
			}
			
		}
		return true;
	}

}
?>