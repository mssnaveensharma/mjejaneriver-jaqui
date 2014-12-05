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
//JHTML::_('stylesheet', 						'administrator/components/'.getBookingExtName().'/assets/datepicker/css/datepickerstyle.css');
JHTML::_('script', 						'administrator/components/'.getBookingExtName().'/assets/datepicker/js/datepicker.js');
JHTML::_('script', 								'administrator/components/'.getBookingExtName().'/assets/datepicker/js/eye.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/datepicker/js/utils.js');
JHTML::_('script', 							'administrator/components/'.getBookingExtName().'/assets/datepicker/js/layout.js');
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'managerooms'.DS.'view.html.php' );



class JHotelReservationControllerManageRooms extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');  
		$this->registerTask( 'add', 'edit');  
		$this->registerTask( 'apply', 'save');
		$post 		= JRequest::get( 'post' );
		
		if( JRequest::getVar('is_error')=="1" && JRequest::getVar('task')=="save" )
		{
			JRequest::setVar( 'view', 'managerooms' ); 
			//$this->display();
		}
	}

	 
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('managerooms');

		$post 		= JRequest::get( 'post' );

		$post['room_details'] 			= JRequest::getVar('room_details', '', 'post', 'string', JREQUEST_ALLOWRAW); 
		$post['room_short_description'] = JRequest::getVar('room_short_description', '', 'post', 'string', JREQUEST_ALLOWRAW); 
		$post['room_main_description'] 	= JRequest::getVar('room_main_description', '', 'post', 'string', JREQUEST_ALLOWRAW); 
		
		$option_ids 	= array();
		$pictures					= array();

		$room_intervals_numbers['nrs']		= array();
		$room_intervals_numbers['nre']		= array();
		$room_intervals_numbers['is_ignore_duplicate']	= array();
		$room_intervals_numbers['datas']	= array();
		$room_intervals_numbers['datae']	= array();
		$room_intervals_numbers['datai']	= array();
		foreach( $post as $key => $value )
		{
			if( strpos( $key, 'option_ids' ) !== false )
			{
				foreach( $value as $v )
					$option_ids[] = $v;
			}
			else if( 
				strpos( $key, 'room_picture_info' ) !== false 
				||
				strpos( $key, 'room_picture_path' ) !== false
				||
				strpos( $key, 'room_picture_enable' ) !== false				
			)
			{
				foreach( $value as $k => $v )
				{
					if( !isset($pictures[$k]) )
						$pictures[$k] = array('room_picture_info'=>'', 'room_picture_path'=>'','room_picture_enable'=>1);
						
						
					$pictures[$k][$key] = $v;
				}
			}
			else if( strpos( $key, 'room_number_start_' ) !== false )
			{
				$room_intervals_numbers['nrs'][] = $value;
			}
			else if( strpos( $key, 'room_number_stop_' ) !== false )
			{
				$room_intervals_numbers['nre'][] = $value;
			} 
			else if( strpos( $key, 'is_ignore_duplicate_' ) !== false )
			{
				$room_intervals_numbers['is_ignore_duplicate'][] = $value;
				/*
				foreach( $value as $v )
				{
					if( $v != '' && is_numeric($v) )
						$room_intervals_numbers['nre'][] = $v+0;
				}
				*/
			} 
			else if( strpos( $key, 'room_number_datas_' ) !== false )
			{
				// foreach( $value as $v )
				// {
					// if( $v != '' && is_numeric($v) )
						// $room_intervals_numbers['datas'][] = $v+0;
				// }
				$room_intervals_numbers['datas'][] = $value;
			} 
			else if( strpos( $key, 'room_number_datae_' ) !== false )
			{
				// foreach( $value as $v )
				// {
					// if( $v != '' && is_numeric($v) )
						// $room_intervals_numbers['datae'][] = $v+0;
				// }
				$room_intervals_numbers['datae'][] = $value;
			} 
			else if( strpos( $key, 'room_number_datai_' ) !== false )
			{
				// foreach( $value as $v )
				// {
					// if( $v != '' && is_numeric($v) )
						// $room_intervals_numbers['datai'][] = $v+0;
				// }
				$room_intervals_numbers['datai'][] = $value;
			} 
		}
				
		
		//clean all necessary 
		$c 	= count($room_intervals_numbers['nrs']);
		foreach( $room_intervals_numbers['nrs'] as $i => $valTmp )
		{
			if( 
				$room_intervals_numbers['nrs'][$i] ==''
				&&						
				$room_intervals_numbers['nre'][$i] == ''
			)
			{
				unset( $room_intervals_numbers['nrs'][$i]);
				unset( $room_intervals_numbers['nre'][$i]);
				unset( $room_intervals_numbers['is_ignore_duplicate'][$i]);
				unset( $room_intervals_numbers['datas'][$i]);
				unset( $room_intervals_numbers['datae'][$i]);
				unset( $room_intervals_numbers['datai'][$i]);
				
				
				continue;
			}
		}
		//clean all necessary 
		
		//dmp($room_intervals_numbers);
		$numbers 		= array();
		$numbers_2_check= array();
		$datai	 		= array();
		$room_prices	= array();
		if( 
			count($room_intervals_numbers['nrs'])  > 0 
		)
		{
			$c = count($room_intervals_numbers['nrs']);
			for( $i=0;$i<$c;$i++)
			{
				if( $room_intervals_numbers['nrs'][$i] > $room_intervals_numbers['nre'][$i] )
				{
					continue;
				}
				
				for( $d=$room_intervals_numbers['nrs'][$i];$d<=$room_intervals_numbers['nre'][$i]; $d++)
				{
					$numbers[] 		= $d;
					if( $room_intervals_numbers['is_ignore_duplicate'][$i] == 0 )
						$numbers_2_check[] = $d;
					$datai[ $d ]	= $room_intervals_numbers['datai'][$i] ;
				}
			}
		} 
		
		$daysWeek = array( "", "LNG_MON", "LNG_TUE", "LNG_WED", "LNG_THU", "LNG_FRI", "LNG_SAT", "LNG_SUN" );

		foreach( $room_prices as $keyPret => $valuesDays )
		{
			foreach( $valuesDays as $day )
			{
				$post["room_price_$day"]	 = $keyPret;
				unset( $daysWeek[ $day ] );
			}
		}
		// dmp($numbers_2_check);
		// exit;
		// dmp($room_intervals_numbers['nrs']);
		// dmp($room_intervals_numbers['nre']);
		$post['option_ids'] = $option_ids;
		$post['pictures'] 				= $pictures;
		$post['room_prices'] 			= $room_prices;
		$post['room_intervals_numbers'] = $room_intervals_numbers;
		$post['numbers'] 				= $numbers; 
		$post['numbers_2_check'] 		= $numbers_2_check; 
		$post['datai'] 					= $datai; 
		$post['room_order']				= $this->getLastRoom($post);

		
		
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_rooms', array('room_name' => $post['room_name'] , 'hotel_id' => $post['hotel_id'] ) , 'room_id', $post['room_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_ROOM_NAME_EXISTENT',true) );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', $msg );
		}

		else if(
			$post['type_price'] == 0
			&&
			(
				( !is_numeric($post["room_price_1"]) ||$post["room_price_1"] == 0 )
				||			
				( !is_numeric($post["room_price_2"]) ||$post["room_price_2"] == 0 )
				||			
				( !is_numeric($post["room_price_3"]) ||$post["room_price_3"] == 0 )
				||			
				( !is_numeric($post["room_price_4"]) ||$post["room_price_4"] == 0 )
				||			
				( !is_numeric($post["room_price_5"]) ||$post["room_price_5"] == 0 )
				||			
				( !is_numeric($post["room_price_6"]) ||$post["room_price_6"] == 0 )
				||			
				( !is_numeric($post["room_price_7"]) ||$post["room_price_7"] == 0 )
			)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_DAY_BY_DAY',true) );
		}
		else if(
			$post['type_price'] == 1
			&&
			(!is_numeric($post["room_price"]) ||$post["room_price"] == 0)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_SAME_EVERY_DAY',true) );
		}
		else if(
			$post['type_price'] == 2
			&&
			(
				(!is_numeric($post["room_price_midweek"]) ||$post["room_price_midweek"] == 0)
				||
				(!is_numeric($post["room_price_weekend"]) ||$post["room_price_weekend"] == 0)
			)
		)
		{
			JError::raiseWarning( 500, JText::_('LNG_ERROR_PRICE_MIDDWEEK_WEEKEND',true) );
		}
		else if( !$model->validNumbers($post['room_intervals_numbers'], $msg )  )
		{
			JError::raiseWarning( 500, $msg );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&task=add&is_error_save=1&room_id[]='.$post['room_id'], '');
		}		
		else if( count($post['numbers']) != $post['number_of_rooms'] )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_ERROR_COUNT_NR_ROOM_VS_NUMBERS',true) );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', '' );
		}
		else if( $model->checkDuplicateNumbers($post['numbers'], $msg )  )
		{
			JError::raiseWarning( 500, $msg );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', '' );
		}	
		else if( JHotelUtil::checkIndexKey( '#__hotelreservation_rooms', array('hotel_id' => $post['hotel_id'] , 'room_name' => $post['room_name']) , 'room_id', $post['room_id'] ) )
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_ROOM_NAME_EXISTENT',true) );
			// $this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$post['hotel_id'], $msg );
		}
		else if( !$model->checkNumbers( $post['hotel_id'],  $post['room_id'], $post['numbers_2_check'] , $msg ) )
		{
			JError::raiseWarning( 500, $msg );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', '' );
		} 
		else if($post['room_id']!="" && !$model->checkNumbersUnAssigned2Reservation( $post['hotel_id'], $post['room_id'], $post['numbers'] , $msg ) )
		{
			JError::raiseWarning( 500, $msg );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', '' );
		}
		/*else if( !$model->checkDateIgnoredAssigned2Reservation( $post['hotel_id'], $post['room_id'], $post['datai'] , $msg ) )
		{
			JError::raiseWarning( 500, $msg );
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms', '' );
		} */
		/*
		else if(1)
		{
			$msg = 'DSC';
			JError::raiseWarning( 500, $msg );
		}*/
		else if ($model->store($post)) 
		{
			$app =JFactory::getApplication();
			$app->enqueueMessage(JText::_('LNG_ROOM_SAVED',true));
			$post["room_id"] = $model->_room_id;
			$this->saveRoomDescriptions($post);
			if(JRequest::getVar('task')=='apply'){
				$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&task=edit&hotel_id='.$post['hotel_id'].'&room_id[]='.$model->_room_id, $msg );
			}
			else
				$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$post['hotel_id'], $msg );
		} 
		else 
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_ROOM',true));
			//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$post['hotel_id'], '' );	
		}

	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;

		$db = JFactory::getDBO();
		$query = " DELETE FROM #__hotelreservation_rooms_seasons WHERE room_id =0 "	;
		$db->setQuery( $query );
		$db->query();
		
		$query = " DELETE FROM #__hotelreservation_rooms_seasons_date_ignored WHERE room_id =0 "	;
		$db->setQuery( $query );
		$db->query();
			
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function delete()
	{
		$model = $this->getModel('managerooms');
		$post = JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
		if ($model->remove()) {
			$msg = JText::_('LNG_ROOM_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_ROOM',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$post['hotel_id'], $msg );
	}
	
	
	function edit()
	{
		$model = $this->getModel ( 'managerooms' );
		$view  = $this->getView  ('managerooms');
		$view->setModel( $model, true );  // true is for the default model;
		
		$hotelTranslations = $this->getModel ( 'hoteltranslations' );
		$view->setModel($hotelTranslations);
		$view->display();
	}
	
	function state()
	{
		$model = $this->getModel('managerooms');
		$get = JRequest::get( 'get' );
		if( !isset($get['hotel_id']) )
			$get['hotel_id'] = 0;
		if ($model->state()) 
		{
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_ROOM_STATE',true);
		}

	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managerooms&view=managerooms&hotel_id='.$get['hotel_id'], $msg );
	}
	
	function status_season()
	{
		$room_id 						= 0;
		$room_season_id 				= 0;
		$is_available					= -1;
		
		foreach( $_GET as $key => $value )
		{
			if( isset( $$key ) )
				$$key = $value;
		}
		
			
		$ret			= true;
		$e				= '';
		$p				= -1;
		$m				= '';
		$i				= '';
	
		if( $is_available ==-1  || $room_season_id==0)
		{
			$ret = false;
			$e = 'Invalid params';
		}
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$db->setQuery( " 
							UPDATE #__hotelreservation_rooms_seasons
							SET 
								is_available = $is_available 
							WHERE room_season_id = '$room_season_id'
						" );
		
			if (!$db->query() ) 
			{
				//dmp($db);
				$ret = false;
				$e = 'UPDATE sql STATEMENT error !';
			}
		}
		
		//retrieve seasons
		$info_buff 	= $ret ? $this->getHTMLContentRoomInfoSeasons($room_id) : '';
		//dmp($buff);
		//~retrieve seasons
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" e="'.$e.'" info="'.$i.'" mesage="'.$m.'" p="'.$room_season_id.'" content_info_seasons="'.$info_buff.'"  />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	
	
	function getHTMLContentRoomSeasons($room_id)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_jhotelreservation/views/managerooms/view.html.php';
		include_once( $path);
		
		$view = $this->getView('managerooms');
		$db = JFactory::getDBO();
		$db->setQuery( " 
						SELECT 
							*
						FROM #__hotelreservation_rooms_seasons
						WHERE 
							room_id = '$room_id'	
						ORDER BY room_season_datas, room_season_name
						" );
		$seasons 	= $db->loadObjectList();
		// dmp(count($rows));
		$buff = $view->displayRoomSeasons($seasons);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	
	function getHTMLContentRoomInfoSeasons($room_id)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_jhotelreservation/views/managerooms/view.html.php';
		include_once( $path);
		
		$view = $this->getView('managerooms');
		$db = JFactory::getDBO();
		$db->setQuery( " 
						SELECT 
							*
						FROM #__hotelreservation_rooms_seasons
						WHERE 
							room_id = '$room_id'	
						ORDER BY room_season_datas, room_season_name
						" );
		$seasons 	= $db->loadObjectList();
		// dmp(count($rows));
		$buff = $view->displayRoomInfoSeasons($seasons);
		//var_dump($buff);
		return htmlspecialchars($buff);
	}
	
	
	function delete_season()
	{
		$room_id 						= 0;
		$room_season_id 				= 0;
		
		foreach( $_GET as $key => $value )
		{
			if( isset( $$key ) )
				$$key = $value;
		}
		
			
		$ret			= true;
		$e				= '';
		$p				= -1;
		$m				= '';
		$i				= '';
	
		if( $room_season_id==0)
		{
			$ret = false;
			$e = 'Invalid params';
		}
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$db->setQuery( " 
							DELETE FROM #__hotelreservation_rooms_seasons
							WHERE room_season_id = '$room_season_id'
						" );
		
			if (!$db->query() ) 
			{
				//dmp($db);
				$ret = false;
				$e = 'UPDATE sql STATEMENT error !';
			} 
			
		}
	
		$buff 		= $ret ? $this->getHTMLContentRoomSeasons($room_id) : '';
		$info_buff 	= $ret ? $this->getHTMLContentRoomInfoSeasons($room_id) : '';
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" e="'.$e.'" info="'.$i.'" mesage="'.$m.'" p="'.$room_season_id.'" content_seasons="'.$buff.'" content_info_seasons="'.$info_buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
	
	function alter_season()
	{
		$room_id 						= 0;
		$room_season_id 				= 0;
		$room_season_name 				= '';
		$room_season_datas 				= '';
		$room_season_datae 				= '';
		$room_season_datai 				= '';
		$room_season_type_price 		= '';
		$room_season_pers_price 		= '';
		$room_season_price_1 			= '';
		$room_season_price_2 			= '';
		$room_season_price_3 			= '';
		$room_season_price_4 			= '';
		$room_season_price_5 			= '';
		$room_season_price_6 			= '';
		$room_season_price_7 			= '';
		$room_season_price	 			= '';
		$room_season_price_midweek	 	= '';
		$room_season_price_weekend	 	= '';
		
		foreach( $_GET as $key => $value )
		{
			if( isset( $$key ) )
				$$key = $value;
		}
		
			
		$ret	= true;
		$e		= '';
		$p		= -1;
		$m		= '';
		$i		= '';
		
		if( $room_season_name =='' )
		{
			$ret = false;
			$e = 'Invalid params';
			$i = 'Invalid params !';
		}
		else if(
			$room_season_datas!='' && $room_season_datae!='' && strtotime( $room_season_datas ) > strtotime( $room_season_datae )
		)
		{
			$ret 	= false;
			$i 		=  JText::_('LNG_DATA_START_DATA_STOP',true);
		}
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$db->setQuery( " 
							SELECT 
								*
							FROM #__hotelreservation_rooms_seasons
							WHERE 
								room_id = '$room_id'
								AND
								room_season_name = '$room_season_name'
								AND
								room_season_id <> '$room_season_id'
						" );
			$rows 			= $db->loadObjectList();
			// dmp(count($rows));
			if( count($rows) > 0 )
			{
				$ret 	= false;
				$i		= JText::_('LNG_SEASON_NAME_EXISTENT',true);
				
			}
		
			if( $ret == true )
			{
				$room_season_datas_tmp = $room_season_datas;
				if( $room_season_datas_tmp == '')
					$room_season_datas_tmp = '0000-00-00';
				$room_season_datae_tmp = $room_season_datae;
				if( $room_season_datae_tmp == '')
					$room_season_datae_tmp = '9999-12-31';
				
				$db->setQuery( " 
								SELECT 
									*
								FROM #__hotelreservation_rooms_seasons
								WHERE 
									room_id = '$room_id'
									AND
									room_season_id <> '$room_season_id'
									
									#return (r1start == r2start) || (r1start > r2start ? r1start <= r2end : r2start <= r1end);

									
									AND
									(
										
										(
											'$room_season_datas_tmp' = room_season_datas
										)
										OR
										(
										
											IF(
												'$room_season_datas_tmp' > room_season_datas,
												if( room_season_datae <> '0000-00-00', '$room_season_datas_tmp' <= room_season_datae, 1 ),
												room_season_datas <= '$room_season_datae_tmp'
											)
										)
									)	
							" );
				//dmp($db);
				// exit;
				$rows 			= $db->loadObjectList();
				if( count($rows) > 0 )
				{
					$ret 	= false;
					$i		= JText::_('LNG_SEASON_INTERSECT_PERIOD',true);
				}
			}
			// dmp($room_season_type_price);
			
			if( $ret == true )
			{
				switch( $room_season_type_price )
				{
					case 0:
						if( 
							( !is_numeric($room_season_price_1) || $room_season_price_1==0 || strlen($room_season_price_1) == 0 )
							||
							( !is_numeric($room_season_price_2) || $room_season_price_2==0 || strlen($room_season_price_2) == 0 )
							||
							( !is_numeric($room_season_price_3) || $room_season_price_3==0 || strlen($room_season_price_3) == 0 )
							||
							( !is_numeric($room_season_price_4) || $room_season_price_4==0 || strlen($room_season_price_4) == 0 )
							||
							( !is_numeric($room_season_price_5) || $room_season_price_5==0 || strlen($room_season_price_5) == 0 )
							||
							( !is_numeric($room_season_price_6) || $room_season_price_6==0 || strlen($room_season_price_6) == 0 )
							||
							( !is_numeric($room_season_price_7) || $room_season_price_7==0 || strlen($room_season_price_7) == 0 )
						)
						{
							$ret 	= false;
							$i		= JText::_('LNG_SEASON_PRICE_INCORECT',true);
						}
						break;
					case 1:
						if( 
							!is_numeric($room_season_price) || $room_season_price==0 || strlen($room_season_price) == 0
						)
						{
							$ret 	= false;
							$i		= JText::_('LNG_SEASON_PRICE_INCORECT',true);
						}
						break;
					case 2:
						if( 
							(!is_numeric($room_season_price_midweek) || $room_season_price_midweek==0 || strlen($room_season_price_midweek) == 0)
							||
							(!is_numeric($room_season_price_weekend) || $room_season_price_weekend==0 || strlen($room_season_price_weekend) == 0)
						)
						{
							$ret 	= false;
							$i		= JText::_('LNG_SEASON_PRICE_INCORECT',true);
						}
						break;
				}
			}
			
			$query = "START TRANSACTION";
			$db->setQuery($query);
			$db->queryBatch();
			if( $ret == true )
			{
				$db->setQuery( " 
								INSERT INTO #__hotelreservation_rooms_seasons
								(
									room_season_id,
									room_id,
									room_season_name,
									room_season_datas,
									room_season_datae,
									room_season_datai,
									room_season_pers_price,
									room_season_type_price,
									room_season_price_1,
									room_season_price_2,
									room_season_price_3,
									room_season_price_4,
									room_season_price_5,
									room_season_price_6,
									room_season_price_7,
									room_season_price,
									room_season_price_midweek,
									room_season_price_weekend
								)
								VALUES
								(
									'$room_season_id',
									'$room_id',
									'$room_season_name',
									'$room_season_datas',
									'$room_season_datae',
									'$room_season_datai',
									'$room_season_pers_price',
									'$room_season_type_price',
									'$room_season_price_1',
									'$room_season_price_2',
									'$room_season_price_3',
									'$room_season_price_4',
									'$room_season_price_5',
									'$room_season_price_6',
									'$room_season_price_7',
									'$room_season_price',
									'$room_season_price_midweek',
									'$room_season_price_weekend'
								)
								ON DUPLICATE KEY UPDATE
									room_id 					= '$room_id',
									room_season_name			= '$room_season_name',
									room_season_datas			= '$room_season_datas',
									room_season_datae			= '$room_season_datae',
									room_season_datai			= '$room_season_datai',
									room_season_pers_price		= '$room_season_pers_price',
									room_season_type_price		= '$room_season_type_price',
									room_season_price_1			= '$room_season_price_1',
									room_season_price_2			= '$room_season_price_2',
									room_season_price_3			= '$room_season_price_3',
									room_season_price_4			= '$room_season_price_4',
									room_season_price_5			= '$room_season_price_5',
									room_season_price_6			= '$room_season_price_6',
									room_season_price_7			= '$room_season_price_7',
									room_season_price			= '$room_season_price',
									room_season_price_midweek	= '$room_season_price_midweek',
									room_season_price_weekend	= '$room_season_price_weekend'
								
								" );
				// dmp($db);
				if (!$db->query() ) 
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				} 
				else if( $room_season_id == 0)
					$room_season_id = $db->insertid();
			}
			
			if( $ret == true )
			{
				$query = " DELETE FROM #__hotelreservation_rooms_seasons_date_ignored WHERE room_season_id = $room_season_id ";
				$db->setQuery($query);
				if (!$db->query() ) 
				{
					//dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				} 
				else
				{
					$ex = explode(",", $room_season_datai );
					
					foreach( $ex as $v )
					{
						if( $v =='NaN-NaN-NaN' || $v =='0000-00-00' || $v =='' )	
							continue;
						$query = " 	INSERT INTO #__hotelreservation_rooms_seasons_date_ignored
									(
										room_id,
										room_season_id,
										room_season_data
									)
									VALUES
									(
										'$room_id',
										'$room_season_id',
										'$v'
									)
						";
						$db->setQuery($query);
						if (!$db->query($query) ) 
						{
							//dmp($db);
							$ret = false;
							$e = 'INSERT / UPDATE sql STATEMENT error !';
							break;
						}
					}
				}
			}
			
			
			if( $ret == true )
			{
				$query = "COMMIT";
				$db->setQuery($query);
				$db->queryBatch();
			}	
			else
			{
				$query = "ROLLBACK";
				$db->setQuery($query);
				$db->queryBatch();
			}
							
			
		}
	
		$buff 		= $ret ? $this->getHTMLContentRoomSeasons($room_id) : '';
		$info_buff 	= $ret ? $this->getHTMLContentRoomInfoSeasons($room_id) : '';
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="'.($ret ? "0" : "1").'" e="'.$e.'" info="'.$i.'" mesage="'.$m.'" p="'.$room_season_id.'" content_seasons="'.$buff.'" content_info_seasons="'.$info_buff.'"  />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	} 
	
	function room_order()
	{
		$tip_order 	= '';
		$room_id 	= 0;
		if( isset( $_GET['tip_order'] ) )
			$tip_order = $_GET['tip_order'];
		if( isset( $_GET['room_id'] ) )
			$room_id = $_GET['room_id'];
		if( isset( $_GET['hotel_id'] ) )
			$hotel_id = $_GET['hotel_id'];

		$ret	= true;
		$up	 	= false;
		$down	= false;
		$e		= '';
		$p		= -1;
		$id_alter = 0;
		if( $tip_order == '' || $room_id == 0  || $room_id =='' )
		{
			$ret = false;
			$e = 'Invalid params';
		}
		if( $ret == true )
		{
			$db = JFactory::getDBO();

			$db->setQuery( " 
							(
								SELECT 
									*,
									'up'	AS type_order
								FROM #__hotelreservation_rooms 
								WHERE 
									room_order <= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
									AND
									room_id <> $room_id
									AND
									hotel_id = $hotel_id
								ORDER BY room_order DESC
								LIMIT 1
							)
							UNION ALL
							(
								SELECT 
									*,
									'crt'	AS type_order
								FROM #__hotelreservation_rooms 
								WHERE 
									room_order = ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
									AND 
									hotel_id = $hotel_id
								ORDER BY room_order
								LIMIT 1
							)
							UNION ALL
							(
								SELECT 
									*,
									'down'	AS type_order
								FROM #__hotelreservation_rooms 
								WHERE 
									room_order >= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
									AND
									room_id <> $room_id
									AND 
									hotel_id = $hotel_id
								ORDER BY room_order
								LIMIT 1
							)
							
							" );
			// dmp($db);
			// exit;
			$rows 			= $db->loadObjectList();
			$row_up			= null;
			$row_crt		= null;
			$row_down		= null;
			foreach( $rows as $value )
			{
				switch( $value->type_order )
				{
					case 'up':
						$row_up = $value;
						break;
					case 'crt':
						$row_crt = $value;
						break;
					case 'down':
						$row_down = $value;
						break;	
				}
			}
			$db->setQuery( " START TRANSACTION ");
			if (!$db->query() ) 
			{
				$ret = false;
				$e = ' sql STATEMENT error !';
			} 
			switch( $tip_order )
			{
				case 'up':
				{
					if( $row_up != null && $row_crt != null )
					{
						$id_alter = $row_up->room_id;
						$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_up->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_crt->room_id );
						if (!$db->query() ) 
						{
							$ret = false;
							$e = 'UPDATE sql STATEMENT error !';
						} 
						$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_crt->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_up->room_id );
						if (!$db->query() ) 
						{
							$ret = false;
							$e = 'UPDATE sql STATEMENT error !';
						} 
						
					}
					else
						$ret = false;
					
					break;
				}
				case 'down':
				{
					if( $row_down != null && $row_crt != null )
					
					{
						$id_alter = $row_down->room_id;
						$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_down->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_crt->room_id );
						if (!$db->query() ) 
						{
							$ret = false;
							$e = 'UPDATE sql STATEMENT error !';
						} 
						
						$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_crt->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_down->room_id );
						if (!$db->query() ) 
						{
							$ret = false;
							$e = 'UPDATE sql STATEMENT error !';
						} 
						
					}
					else
						$ret = false;
					break;
				}

			}
			if( $ret == true )
			{
				$db->setQuery( " COMMIT ");
				if (!$db->query() ) 
				{
					$ret = false;
					$e = ' sql STATEMENT error !';
				} 
				//check results
				$db->setQuery( " 
								(
									SELECT 
										*,
										'up'	AS type_order
									FROM #__hotelreservation_rooms 
									WHERE 
										room_order <= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id ) 
										AND
										room_id <> $room_id
										AND 
										hotel_id = $hotel_id
									LIMIT 1
								)
								UNION ALL
								(
									SELECT 
										*,
										'crt'	AS type_order
									FROM #__hotelreservation_rooms 
									WHERE 
										room_order = ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id  ) 
										AND  
										hotel_id = $hotel_id 
									LIMIT 1
								)
								UNION ALL
								(
									SELECT 
										*,
										'down'	AS type_order
									FROM #__hotelreservation_rooms 
									WHERE 
										room_order >= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id  ) 
										AND
										room_id <> $room_id
										AND 
										hotel_id = $hotel_id
									LIMIT 1
								)
								
								" );
				// dmp($db);
				$rows 			= $db->loadObjectList();
				$row_up			= null;
				$row_crt		= null;
				$row_down		= null;
				foreach( $rows as $value )
				{
					switch( $value->type_order )
					{
						case 'up':
							$up = true;
							break;
						case 'crt':
							break;
						case 'down':
							$down = true;
							break;	
					}
				}
				//check results
			}
			else
			{
				$db->setQuery( " ROLLBACK ");
				if (!$db->query() ) 
				{
					$ret = false;
					$e = ' sql STATEMENT error !';
				} 
			}
		
		}

		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_order>';
		echo '<answer up="'.($up? "1" : "0").'" down="'.($down? "1" : "0").'" error="'.($ret ? "0" : "1").'" info="'.$e.'" "p="'.$id_alter.'" />';
		echo '</room_order>';
		echo '</xml>';
		exit;
	}
	
	
	function getLastRoom($post)
	{
		$room_id = 0;
		if( isset($post['room_id']) )
			$room_id = $post['room_id'];
		if( $room_id > 0 )
			$query = 	" SELECT * FROM #__hotelreservation_rooms  WHERE room_id = ".$room_id;
		else
			$query = 	" SELECT * FROM #__hotelreservation_rooms  ORDER BY room_order DESC ";
		// dmp($query);
		// exit;
		$db = JFactory::getDBO();
		
		$db->setQuery( $query );
		$row = $db->loadObject();
		
		if( $row == null )
			return 1;
		
		return $row->room_order + ( $room_id > 0? 0 : 1);
	}
	
	function saveRoomDescriptions($post){

		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = $this->getModel ( 'hoteltranslations' );
			$modelHotelTranslations->deleteTranslationsForObject(ROOM_TRANSLATION,$post['room_id']);
			foreach( $dirs  as $_lng ){
				if(isset($post['room_main_description_'.$_lng]) && strlen($post['room_main_description_'.$_lng])>0){
					$roomDescription = 		JRequest::getVar( 'room_main_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(ROOM_TRANSLATION,$post['room_id'],$_lng,$roomDescription);
				}
				
			}
		}
		catch(Exception $e){
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}
	
}