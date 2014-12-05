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

class JHotelReservationModelManageRoomFeatures extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('feature_id',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}
	function setId($feature_id)
	{
		// Set id and wipe data
		$this->_feature_id	= $feature_id;
		$this->_data		= null;
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
			$query = ' SELECT * FROM #__hotelreservation_room_features';
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query );
			if( $this->_data )
			{
				foreach( $this->_data as $key => $feature )
				{
					$this->_data[$key]->feature_description = "";
				
					$query 		= ' SELECT GROUP_CONCAT(option_name ORDER BY option_name ) AS feature_description FROM #__hotelreservation_room_feature_options WHERE feature_id ='.$feature->feature_id;
					$this->_db->setQuery( $query );
					
					$this->_data[$key]->feature_description	= $this->_db->loadObject( )->feature_description;
				}
			}
		}
		
		return $this->_data;
	}
	
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT * FROM #__hotelreservation_room_features'.
						' WHERE feature_id = '.$this->_feature_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
			
			if( $this->_data )
			{
				$query 		= ' SELECT * FROM #__hotelreservation_room_feature_options WHERE feature_id ='.$this->_feature_id;
				//$this->_db->setQuery( $query );
				$this->_data->option_ids	= $this->_getList( $query );
			}
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->feature_id 			= null;
			$this->_data->feature_name			= null;			
			$this->_data->number_of_options		= 1;			
			$this->_data->is_multiple_selection	= false;
			$this->_data->option_ids			= array();
			$this->_data->option_ids[0]			= new stdClass();
			$this->_data->option_ids[0]->option_id		=0;
			$this->_data->option_ids[0]->option_name	='';
			$this->_data->option_ids[0]->option_price	='';
		}

		return $this->_data;
	}
	
	
	function &getFeatureRoomOptions()
	{
		$query = 	' SELECT * FROM #__hotelreservation_room_feature_options';
		
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );
		return $res;
				
	}
	
	function store($data)
	{	
		// dmp($data );
		// exit;
	// return false;
		$data['number_of_options'] = count($data['option_name']);
		try
		{
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();

			//feature
			$row = $this->getTable();
			if (!$row->bind($data)) 
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
			if( $data['feature_id'] =='')
				$data['feature_id'] = $this->_db->insertid();

			//~feature
			//option
		
						
			$nr 	= 1;
			$opt_ids	= array();
			// dmp($data );
			// exit;
			foreach( $data['option_name'] as $key => $value )
			{
				$row = $this->getTable('ManageRoomFeatureOptions');
	
				// dmp($key);
				$opt 						= new stdClass();
				$opt->option_id 			= isset($data['option_id'][$key]) ? $data['option_id'][$key] : 0;
				$opt->feature_id			= $data['feature_id'];
				$opt->option_name			= $data['option_name'][ $key ];
				$opt->option_price			= $data['option_price'][ $key ];
				 // dmp($opt);
				// continue;
				
				if (!$row->bind($opt)) 
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
				if(isset($data['option_id'][$key]) && $data['option_id'][$key] > 0 )
					$opt_ids[] = $data['option_id'][$key];
				else
					$opt_ids[] = $row->option_id;
				
				if( $nr >= $data['number_of_options'] )
					break;
				
				$nr++;
			}
			
			// dmp($opt_ids);
			// exit;
		
			$query = 	" DELETE FROM #__hotelreservation_room_feature_options 
							WHERE 
								feature_id = ".$data['feature_id']."
								".
								(
								count($opt_ids) > 0 ? " AND option_id NOT IN (".implode(',', $opt_ids).") " : " "
								)."
								
							" ;
							
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
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
			return false;
		}

		return true;
	}
	
	function remove()
	{
		try
		{
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			
			$cids = JRequest::getVar( 'feature_id', array(0), 'post', 'array' );
		
			

			$row = $this->getTable();

			if (count( $cids )) {
				foreach($cids as $cid) 
				{
					if (!$row->delete( $cid )) 
					{
						$this->setError( $row->getErrorMsg() );
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_room_feature_options WHERE feature_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query()) 
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					} 
				}
			}
			
			$query = "COMMIT ";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
			$query = "ROLLBACK";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();

			return false;
		}
		
		return true;

	}




}
?>