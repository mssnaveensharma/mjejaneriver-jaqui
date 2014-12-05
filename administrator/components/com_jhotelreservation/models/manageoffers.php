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

class JHotelReservationModelManageOffers extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array 	= JRequest::getVar('offer_id',  0, '', 'array');
		$h		= JRequest::getVar('hotel_id',  0, '');
		$this->setHotelId((int)$h);


		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}
	function setId($offer_id)
	{
		// Set id and wipe data
		$this->_offer_id	= $offer_id;
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
			$query = " SELECT ho.*, GROUP_CONCAT(hov.voucher SEPARATOR ', ') as vouchers FROM #__hotelreservation_offers ho 
					   left join #__hotelreservation_offers_vouchers hov on hov.offerId=ho.offer_id
						WHERE ho.hotel_id = ".$this->_hotel_id." group by ho.offer_id ORDER BY ho.offer_order  ";
						
			// dmp($query);
			$this->_data = $this->_getList( $query );
			if(isset($this->_data) && count($this->_data)>0)
			foreach( $this->_data as $key => $value )
			{
				$content_info = "<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>";
				$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_CODE',true)." :</B></TD><TD><B>".$value->offer_code."</B></TD></TR>";
				$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_NAME',true)." :</B></TD><TD><B>".$value->offer_name."</B></TD></TR>";
				if( $value->offer_reservation_cost_val !=0 )
					$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_VALUE',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_val."</B></TD></TR>";
				if( $value->offer_reservation_cost_proc !=0 )
					$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_PERCENT',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_proc." %</B></TD></TR>";
				$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_DESCRIPTION',true)." :</B></TD><TD>".$value->offer_description."</TD></TR>";
				$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_PERIOD',true)." :</B></TD><TD>".$value->offer_datas." <> ".$value->offer_datae."</TD></TR>";
				$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_DISPLAY_ON_FRONT',true)." :</B></TD><TD>".($value->offer_datasf !='0000-00-00'?$value->offer_datasf:'&nbsp;')." <> ".($value->offer_dataef !='0000-00-00'?$value->offer_dataef:'&nbsp;')."</TD></TR>";
/*
				if( $value->offer_datasf !='0000-00-00' )
					$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_DISPLAY_ON_FRONT_START_AT',true)." :</B></TD><TD>".$value->offer_datasf."</TD></TR>";
				if( $value->offer_dataef !='0000-00-00' )
					$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_DISPLAY_ON_FRONT_ENDT_AT',true)." :</B></TD><TD>".$value->offer_dataef."</TD></TR>";
		*/		
				$query = "
							SELECT
								r.room_id,
								r.room_name
								
							FROM #__hotelreservation_rooms	r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
							WHERE ord.offer_id = ".$value->offer_id."
							GROUP BY r.room_id
							ORDER BY room_name
				";
				
				$room = $this->_getList( $query );
				if( isset($room) )
				{
					$old_room_id 	= 0;
					foreach( $room as $value_room )
					{
						$content_info 	.= "<TR><TD nowrap align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD><TD>";
						/*$table_days_org = "<TABLE width=100% cellpadding=0 cellspacing=0  class='table_room_default_prices'>";
						switch( $value_room->type_price_def )
						{
							case 0:
								$table_days_org .="<TR><TD colspan=7  align=center><B>".(JText::_('LNG_TYPE_PRICE',true) .' : '.($value_room->pers_price_def == 1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))).' | '.JText::_('LNG_DEFAULT_PRICE',true).' | '.JText::_('LNG_DAY_BY_DAY',true)."</B></TD></TR>";
								
								for( $day=1;$day<=7;$day++)
								{
									switch( $day )
									{
										case 1:
											$table_days_org .= "<TR><TD nowrap width='14%' align=center>".JText::_('LNG_MON',true)."</TD>";
											break;
										case 2:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_TUE',true)."</TD>";
											break;
										case 3:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_WED',true)."</TD>";
											break;
										case 4:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_THU',true)."</TD>";
											break;
										case 5:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_FRI',true)."</TD>";
											break;
										case 6:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_SAT',true)."</TD>";
											break;
										case 7:
											$table_days_org .= "<TD nowrap width='14%' align=center>".JText::_('LNG_SUN',true)."</TD></TR>";
											break;
									}
								}
								for( $day=1;$day<=7;$day++)
								{
									$field ="room_price_def_$day";
									$table_days_org.= ($day==1?"	<TR>" :"")."
														<TD align=center>".$value_room->$field."</TD>
													".($day==7?"	</TR>" :"");
								}
							break;
							case 1:
								$table_days_org .="<TR><TD align=center><B>".(JText::_('LNG_TYPE_PRICE',true) .' : '.($value_room->pers_price_def == 1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))).' | '.JText::_('LNG_DEFAULT_PRICE',true).' | '.JText::_('LNG_SAME_EVERY_DAY',true)."</B></TD></TR>";
								$table_days_org.= "<TR><TD align=center>".$value_room->room_price_def."</TD></TR>";
								break;
							case 2:
								$table_days_org .="<TR><TD  align=center colspan=2><B>".(JText::_('LNG_TYPE_PRICE',true) .' : '.( $value_room->pers_price_def == 1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))).' | '.JText::_('LNG_DEFAULT_PRICE',true).' | '.JText::_('LNG_midweek_WEEKEND',true)."</B></TD></TR>";
								$table_days_org.= "<TR><TD align=center>".JText::_('LNG_STR_MIDWEEK',true)."</TD>";
								$table_days_org.= "<TD align=center>".JText::_('LNG_STR_WEEKEND',true)."</TD></TR>";
								//$table_days_org.= "<TR><TD align=center>".$value_room->room_price_midweek."</TD>";
								//$table_days_org.= "<TD align=center>".$value_room->room_price_weekend."</TD></TR>";
								break;
						}
						$table_days_org	.='</TABLE>';
						*/
						//$content_info 	.= "<div>$table_days_org<div>";
					
						$query = "
									SELECT
										ord.*
									FROM #__hotelreservation_rooms	r
									INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
									WHERE 
										ord.offer_id = ".$value->offer_id."
										AND
										r.room_id = ".$value_room->room_id."
									ORDER BY room_name
						";
						//dmp($query);
						$room_discount = $this->_getList( $query );
						//dmp($room_discount);
						//exit;
						//if( isset($room_discount) )
						//{
							/*$content_info 	.= "<TABLE border=1 class='table_room_discount_prices' WIDTH='100%' border=0 cellpadding=0 cellspacing=0>";
							foreach( $room_discount as $value_room_discount )
							{
								// dmp($value_room_discount);
								// exit;
								//if( in_array( $value_room_discount->offer_type_discount_type, array('nr_per','nr_day') ) )
								//{
									$content_info 	.= "	<TR>
																<TD>".($value_room_discount->offer_pers_price==1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))."</TD>
																<TD width=30%><B>".$value_room_discount->offer_type_discount_name."</B></TD>
																<TD>".
																	$value_room_discount->offer_type_discount_sign.
																	" ".$value_room_discount->offer_room_discount_nr.
																		" ( ".( $value_room_discount->offer_type_discount_type =='nr_per' ? JText::_('LNG_PERS',true) : JText::_('LNG_DAYS',true)) ." )".
																	" | ".$value_room_discount->offer_room_discount_val.
																	" ".$value_room_discount->offer_room_discount_type.
																"</TD>
															</TR>
													";
								//}
								//else if( in_array( $value_room_discount->offer_type_discount_type, array('price') ) )
								//{
									$content_info 	.= "	<TR>
																<TD>".($value_room_discount->offer_pers_price==1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))."</TD>
																<TD width=30%><B>".$value_room_discount->offer_type_discount_name."</B></TD>
																<TD>".
																	$value_room_discount->offer_room_discount_val.
																	" ".$value_room_discount->offer_room_discount_type.
																"</TD>
															</TR>
													";
									if( $value_room_discount->offer_price_extranights !=0 )
									{
										$content_info	.='<TR><TD  colspan=2>'.JText::_('LNG_PRICE_EXTRA_NIGHTS',true).'</TD><TD>'.$value_room_discount->offer_price_extranights.' &nbsp;'.$value_room_discount->offer_price_type_extranights.'</TD></TR>';
									}
								//}
								//else if( in_array( $value_room_discount->offer_type_discount_type, array('week_day') ) )
								//{
									$value_room_discount->week_vals 		= explode(",", $value_room_discount->week_vals);
									$value_room_discount->week_types 	= explode(",", $value_room_discount->week_types);
									$table_days = "<TABLE width=100% cellpadding=0 cellspacing=0>";
									for( $day=1;$day<=7;$day++)
									{
										switch( $day )
										{
											case 1:
												$table_days .= "<TR><TD rowspan=2>".($value_room_discount->offer_pers_price==1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))."</TD>
																	<TD nowrap width='14%' align=center>".JText::_('LNG_MON',true)."</TD>";
												break;
											case 2:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_TUE',true)."</TD>";
												break;
											case 3:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_WED',true)."</TD>";
												break;
											case 4:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_THU',true)."</TD>";
												break;
											case 5:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_FRI',true)."</TD>";
												break;
											case 6:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_SAT',true)."</TD>";
												break;
											case 7:
												$table_days .= "<TD nowrap width='14%' align=center>".JText::_('LNG_SUN',true)."</TD></TR>";
												break;
										}
									}
									for( $day=1;$day<=7;$day++)
									{
										$table_days.= ($day==1?"	<TR>" :"")."
															<TD align=center>".
																	$value_room_discount->week_vals[$day-1].
																	" ".$value_room_discount->week_types[ $day-1].
															"</TD>
														".($day==7?"	</TR>" :"");
									}
									$table_days	.='</TABLE>';
									$content_info 	.= "	<TR>
																<TD width=30%><B>".$value_room_discount->offer_type_discount_name."</B></TD>
																<TD>$table_days</TD>
															</TR>
													";
									if( $value_room_discount->offer_price_extranights !=0 )
									{
										$content_info	.='<TR><TD>'.JText::_('LNG_PRICE_EXTRA_NIGHTS',true).'</TD><TD>'.$value_room_discount->offer_price_extranights.' &nbsp;'.$value_room_discount->offer_price_type_extranights.'</TD></TR>';
									}
								//}
								//else if(in_array( $value_room_discount->offer_type_discount_type, array('midweek_weekend_day') ) )
								//{
									$content_info 	.= "	<TR>
																<TD>".($value_room_discount->offer_pers_price==1? JText::_('LNG_PERS',true) : JText::_('LNG_ROOM',true))."</TD>
																<TD width=30%><B>".$value_room_discount->offer_type_discount_name."</B></TD>
																<TD>
																	<TABLE width=100% cellpadding=0 cellspacing=0>
																		<TR>
																			<TD align=center width=50%><B>".JText::_('LNG_STR_MIDWEEK',true)."</B></TD>
																			<TD align=center  width=50%><B>".JText::_('LNG_STR_WEEKEND',true)."</B></TD>
																		</TR>
																		<TR>
																			<TD align=center >".
																				$value_room_discount->midweek_val.
																				" ".$value_room_discount->midweek_type.
																			"</TD>
																			<TD align=center >".
																				$value_room_discount->weekend_val.
																				" ".$value_room_discount->weekend_type.
																			"</TD>
																		</TR>
																	</TABLE>
																</TD>
															</TR>
													";
									if( $value_room_discount->offer_price_extranights !=0 )
									{
										$content_info	.='<TR><TD colspan=2>'.JText::_('LNG_PRICE_EXTRA_NIGHTS',true).'</TD><TD>'.$value_room_discount->offer_price_extranights.' &nbsp;'.$value_room_discount->offer_price_type_extranights.'</TD></TR>';
									}
							//	}
							}
							$content_info  .="</TABLE>";
						}*/
						// setup offer packages
			
						
						
					
						
						$old_room_id =$value_room->room_id;

						// $content_info .= "	</TABLE>";
						$content_info .= "<HR></TD></TR>";
					}
				}
				
				$content_info .= "</TABLE>";
				
				
				$this->_data[ $key ]->content_info .= "";//$content_info;
				
				//warning info
				$warning_info = "";
				
				$query = "
							SELECT
								r.room_id,
								r.room_name,
								o.offer_datas,
								o.offer_datae
							FROM #__hotelreservation_rooms				 				r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		ON r.room_id = ord.room_id
							INNER JOIN #__hotelreservation_offers						o		ON o.offer_id = ord.offer_id
							WHERE ord.offer_id = ".$value->offer_id."
							GROUP BY r.room_id
							ORDER BY room_name
				";
				// dmp($query);
				$room = $this->_getList( $query );
				if( isset($room) )
				{
					$old_room_id 	= 0;
					foreach( $room as $value_room )
					{
						$answ = array();
						$is_error_period 		= false;
						$is_error_ignored_days 	= false;
						if( 
							$value_room->offer_datas 	!= '0000-00-00' 
						)
						{	
							$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datas." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_START',true)." )";
						}
						
						if( 
							$value_room->offer_datae 	!= '0000-00-00' 
						)
						
						{
							$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datae." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_END',true)." )";
						}
						
						$nr_a = 1;
						foreach( $answ as $a )
						{
							$warning_info 	.= "<TR>";
							if( $nr_a == 1 )
								$warning_info 	.= "<TD rowspan = ".count($answ)." nowrap align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD>";
							$warning_info 	.= "	<TD width=90%>$a</TD>";
							if( $nr_a == 1 )
								$warning_info 	.= "<TD rowspan = ".count($answ)." nowrap align=center valign=middle width=30%>".
														"<a href='index.php?option=com_jhotelreservation&view=managerooms'>".JText::_('LNG_MORE_DETAILS_ROOM_SETTINGS',true)."</a>".
													"</TD>";
							$warning_info 	.= "</TR>";
							$nr_a ++;
						}
						if( count($answ) > 0 )
						{
							$warning_info .= "<TR><td colspan=3><HR></TD></TR>";
						}
						$old_room_id =$value_room->room_id;
					}
				}
				if( strlen($warning_info) > 0 )
				{
					$warning_info = "	<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>".
											$warning_info.
										"</TABLE>";
				}
				$this->_data[ $key ]->warning_info .= $warning_info;
				//~warning info
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
			$query = 	' SELECT * FROM #__hotelreservation_offers'.
						' WHERE offer_id = '.$this->_offer_id .' AND hotel_id = '.$this->_hotel_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
	
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->hotel_id 			= 0;
			$this->_data->offer_id 			= 0;
			$this->_data->offer_code		= null;
			$this->_data->offer_name		= null;
			$this->_data->state				= null;
			$this->_data->offer_short_description = null;
			$this->_data->offer_description	= null;
			$this->_data->offer_content		= null;
			$this->_data->offer_other_info  = null;
			$this->_data->offer_min_nights	= null;
			$this->_data->offer_max_nights	= null;
			$this->_data->type_price_type_extranights 	= null;
			$this->_data->type_price_extranights 		= null;
			$this->_data->offer_day_1		= null;
			$this->_data->offer_day_2		= null;
			$this->_data->offer_day_3		= null;
			$this->_data->offer_day_4		= null;
			$this->_data->offer_day_5		= null;
			$this->_data->offer_day_6		= null;
			$this->_data->offer_day_7		= null;
			$this->_data->offer_datas		= null;
			$this->_data->offer_datae		= null;
			$this->_data->offer_datasf		= null;
			$this->_data->offer_dataef		= null;
			$this->_data->offer_order		= null;
			$this->_data->offer_reservation_cost_val	= 7.5;
			$this->_data->offer_reservation_cost_proc	= 0;
			$this->_data->offer_commission	= 10;
			$this->_data->themes			= null;
			$this->_data->selectedThemes	= null;
			$this->_data->is_available		= null;
			$this->_data->public			= 0;
			$this->_data->pictures  		= array();
			
			//check temporary files
			$pictures = JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_OFFER_PICTURES.($this->_data->offer_id+0)."/*.*");
			$files = glob( $pictures );
			if(is_array($files) && count($files)>0)
				sort($files);
			$this->_data->pictures			= array();
			if(is_array($files) && count($files)>0)
			foreach( $files as $value )
			{
				$this->_data->pictures[]	= array( 
													'offer_picture_info' 		=> 'add from cache',
													'offer_picture_path' 		=> PATH_OFFER_PICTURES.($this->_data->offer_id+0).'/'.basename($value),
													'offer_picture_enable'		=> 1
												);
			}
		}
		else
		{
			
			$this->_data->pictures  	= array();
			//check temporary files
			$query = "
					SELECT 
						*
					FROM #__hotelreservation_offers_pictures
					WHERE offer_id =".$this->_data->offer_id ."
					ORDER BY offer_picture_id "
				;
			// dmp($query);
			//$this->_db->setQuery( $query );
			$files = $this->_getList( $query );
			if( isset( $files) )
			{			
				foreach( $files as $value )
				{
					$this->_data->pictures[]	= array( 
														'offer_picture_info' 		=> $value->offer_picture_info,
														'offer_picture_path' 		=> $value->offer_picture_path,
														'offer_picture_enable'		=> $value->offer_picture_enable,
													);
				}
			}
		}
		
		$query = " 	SELECT 
						r.room_id,
						r.room_name,
						r.max_adults,
						r.max_children,	
						".
						(
							$this->_offer_id > 0 ?
							"IF( ISNULL(ho.room_id), 0, 1 )"
							:
							"0"
						)
						."						AS is_sel							
					FROM #__hotelreservation_rooms r
					".($this->_offer_id > 0 ?
						"LEFT JOIN (
							 select * from  #__hotelreservation_offers_rooms o_r  where  o_r.offer_id = $this->_offer_id 
						) ho  on r.room_id = ho.room_id
						":"")."
					WHERE 
						r.hotel_id = ".$this->_hotel_id."
						";
		
			
		//dmp($query);
		$this->_data->itemRooms = $this->_getList( $query );
		if( isset( $this->_data->itemRooms) )
		{
			
			foreach( $this->_data->itemRooms as $k => $r )
			{
				
				//dmp($r);
				$query = "SELECT *	FROM  #__hotelreservation_offers_rates d where d.offer_id=$this->_offer_id and d.room_id=$r->room_id";
				//dmp($query);
				$res = $this->_getList( $query );
				if(isset( $res) && count($res)>0 )
				{
					foreach( $res as $d )
					{
						//$d->week_types 	= explode(',', $d->week_types);
						// dmp($d);
						$this->_data->itemRooms[$k]->discounts  = $d;
					}
				}else{
					$discounts = new stdClass();
					$discounts->id =0;
					$discounts->offer_room_price_id = null; 
					$discounts->offer_id = $this->_offer_id; 
					$discounts->room_id = $r->room_id; 
					$discounts->price_1 = null; 
					$discounts->price_2 = null; 
					$discounts->price_3 = null; 
					$discounts->price_4 = null; 
					$discounts->price_5 = null; 
					$discounts->price_6 = null; 
					$discounts->price_7 = null; 
					$discounts->single_balancing = null; 
					$discounts->child_price = null; 
					$discounts->price_type = 1; 
					$discounts->extra_night_price = null; 
					$discounts->extra_pers_price = null;
					$discounts->base_adults = null;
					$discounts->base_children = null;
					$this->_data->itemRooms[$k]->discounts = $discounts;
				}
			}
		}
		
		//clean unnecessary files
		$files = glob( JHotelUtil::makePathFile(JPATH_COMPONENT.PATH_OFFER_PICTURES.$this->_data->offer_id."/"."*.*" ));
		//dmp($files);
		if(is_array($files) && count($files)>0)
		foreach( $files as $pic )
		{
			$is_find = false;
			foreach( $this->_data->pictures as $value )
			{
			
				if( $pic == JHotelUtil::makePathFile(JPATH_COMPONENT.$value['offer_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			//if( $is_find == false )
			//	@unlink( $pic );
		}
		//~clean unnecessary files
		
		//dmp($this->_data->pictures);
		//~check temporary files
		//exit;
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes ORDER BY name';
		$this->_data->themes			=  $this->_getList( $query );
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes_relation where offerId='.$this->_data->offer_id;
		$this->_data->selectedThemes	= $this->_getList( $query );
		
		$query = " SELECT * FROM #__hotelreservation_offers_vouchers where offerId = ".$this->_data->offer_id." ORDER BY voucher";
		$this->_data->vouchers			=  $this->_getList( $query );
		
		
		$this->_data->offer_datas		= JHotelUtil::convertToFormat($this->_data->offer_datas);
		$this->_data->offer_datae		= JHotelUtil::convertToFormat($this->_data->offer_datae);
		$this->_data->offer_datasf		= JHotelUtil::convertToFormat($this->_data->offer_datasf);
		$this->_data->offer_dataef		= JHotelUtil::convertToFormat($this->_data->offer_dataef);
		
		return $this->_data;
	}

	function store($data)
	{	
		// dmp($data);
		// exit;
		
		$data['offer_datas']=JHotelUtil::convertToMysqlFormat($data['offer_datas']); 
		$data['offer_datae']=JHotelUtil::convertToMysqlFormat($data['offer_datae']); 
		$data['offer_datasf']=JHotelUtil::convertToMysqlFormat($data['offer_datasf']); 
		$data['offer_dataef']=JHotelUtil::convertToMysqlFormat($data['offer_dataef']); 

		try
		{
			//$this->_db->BeginTrans();
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		
			$row = $this->getTable();
			
			//dmp($data);
			//exit;
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
			
			if( $data['offer_id'] =='' || $data['offer_id'] ==0 || $data['offer_id'] ==null ){
				$data['offer_id'] = $this->_db->insertid();
			}
			
			$this->_offer_id = $data['offer_id'];
				
			//update vouchers
				
			if($data["processVouchers"]==1){
				$this->_db->setQuery (	" DELETE FROM #__hotelreservation_offers_vouchers
										WHERE offerId = $this->_offer_id");
				if (!$this->_db->query() )
				{
					// dmp($db);
					$ret = false;
					$e = 'INSERT / UPDATE sql STATEMENT error !';
				}
				//dmp($data['vouchers']);
				if(isset($data['vouchers']) && count($data['vouchers'])>0){
					foreach($data['vouchers'] as $key => $value )
					{
						$recordName			= trim($data['vouchers'][ $key ]);
						$this->_db->setQuery( "
										INSERT INTO #__hotelreservation_offers_vouchers
										(
											offerId,
											voucher
										)
										VALUES
										(
											'$this->_offer_id',
											'$recordName'
											
										)
						" );
						dmp($recordName);
						if (!$this->_db->query() )
						{
							// dmp($db);
							$ret = false;
							$e = 'INSERT / UPDATE sql STATEMENT error !';
						}
			
					}
				}
			}
			//end update vouchers		
			//exit;
			
			//prepare photos

			$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
			$files = glob( $path_old."*.*" );
			$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
			
			
			$picture_ids 	= array();
			foreach( $data['pictures'] as $value )
			{
				$row = $this->getTable('ManageOfferPictures');
	
				// dmp($key);
				$pic 						= new stdClass();
				$pic->offer_picture_id		= 0;
				$pic->offer_id 				= $data['offer_id'];
				$pic->offer_picture_info	= $value['offer_picture_info'];
				$pic->offer_picture_path	= $value['offer_picture_path'];
				$pic->offer_picture_enable	= $value['offer_picture_enable'];
				//dmp($pic);
				$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->offer_picture_path) );
			
				if( !is_file($file_tmp) )
					continue;

				if( !is_dir($path_new) )
				{
					if( !@mkdir($path_new) )
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
			
				if( $path_old.basename($pic->offer_picture_path) != $path_new.basename($pic->offer_picture_path) )
				{	
					if(@rename($path_old.basename($pic->offer_picture_path),$path_new.basename($pic->offer_picture_path)) ) 
					{
						
						$pic->offer_picture_path	 = PATH_OFFER_PICTURES.($data['room_id']+0).'/'.basename($pic->offer_picture_path);
						//@unlink($path_old.basename($pic->offer_picture_path));
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
					if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.$value['offer_picture_path']) )
					{
						$is_find = true;
						break;
					}
				}
				//if( $is_find == false )
				//	@unlink( JHotelUtil::makePathFile(JPATH_COMPONENT.$value['offer_picture_path']) );
			}
			
			$query = " DELETE FROM #__hotelreservation_offers_pictures 
						WHERE offer_id = '".$data['offer_id']."'
						".( count($picture_ids)> 0 ? " AND offer_picture_id NOT IN (".implode(',', $picture_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			//~prepare photos
			
			
			//room 
			$offer_room_ids 	= array();
			foreach( $data['rooms'] as $value )
			{
				$row = $this->getTable('ManageOfferRooms');
	
				// dmp($key);
				$offer_room										= new stdClass();
				$offer_room->offer_id 							= $data['offer_id'];
				$offer_room->room_id 							= $value['room_id'];
				
				if (!$row->bind($offer_room)) 
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
				
				$offer_room_ids[] = $this->_db->insertid();
			}
			
			
			$query = " DELETE FROM #__hotelreservation_offers_rooms 
						WHERE offer_id = '".$data['offer_id']."'
						".( count($offer_room_ids)> 0 ? " AND offer_room_id NOT IN (".implode(',', $offer_room_ids).")" : "");
						
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			} 
			//~room packages
			
			
			 dmp($data['rooms']);
			 //exit;
			//room discounts
			$discount_ids 	= array();
			
			foreach( $data['rooms'] as $valueRoom )
			{
				//dmp( $valueRoom);
				if( count( $valueRoom['offer_price'] ) > 0 )
				{
					
					$offer_price = $valueRoom['offer_price'];
					$offer_price['offer_id'] = $data['offer_id'];
					$offer_price['room_id'] = $valueRoom['room_id'];
					$offer_price['id'] =$offer_price['offer_room_rate_id'];
					
					dmp($offer_price);
					//exit;
					$row = $this->getTable('OfferRate');

					//dmp($offer_price);
					//exit;
					if (!$row->bind($offer_price)) 
					{
						dmp($this->_db->getErrorMsg());
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
					// Make sure the record is valid
					if (!$row->check()) 
					{
						dmp($this->_db->getErrorMsg());
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}

					// Store the web link table to the database
					if (!$row->store()) 
					{
						dmp($this->_db->getErrorMsg());
						throw( new Exception($this->_db->getErrorMsg()) );
						$this->setError($this->_db->getErrorMsg());
					}
					
					$discount_ids[] = $this->_db->insertid();
				}
			
			}
			
			//prepare themes
			//dmp($data['themes']);
			$query = " DELETE FROM #__hotelreservation_offers_themes_relation
												WHERE offerId = ".$data['offer_id'];
				
			// dmp($query);
			// exit;
			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			foreach( $data['themes'] as $theme ){
					
				$row = $this->getTable('ManageOffersThemesRelation');
				$themeRelation->offerId= $data['offer_id'];
				$themeRelation->themeId= $theme;
				//dmp($facilityRelation);
				if (!$row->bind($themeRelation))
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
				if (!$row->store(true))
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
					
			}
			
			//store extra options
			$this->storeExtraOptions($this->_offer_id, $data["extra_options_ids"]);
			
			$query = "COMMIT";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
			dmp($ex);
			// exit;
			//$this->_db->RollbackTrans();
			$query = "ROLLBACK";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			return false;
		}
		return true;
	}
	
	
	function storeExtraOptions($offerId,$extraOptionsArray){
		$extraOptions = $this->getExtraOptions();
		foreach($extraOptions as $extraOption){
			if(in_array($extraOption->id, $extraOptionsArray)){
				if(strpos($extraOption->offer_ids, $offerId )===false){
					$extraOption->offer_ids.= ",".$offerId;
				}
			}else{
				if(strpos($extraOption->offer_ids, $offerId)!==false){
					$extraOption->offer_ids = str_replace($offerId, "", $extraOption->offer_ids);
					$extraOption->offer_ids = str_replace(",,", "", $extraOption->offer_ids);
					$extraOption->offer_ids = trim($extraOption->offer_ids, ",");
				}
			}
			
			
			//dmp($extraOption);
			//exit;
			$row =JTable::getInstance('ExtraOption',"JTable");
			if (!$row->bind($extraOption)){
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
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		}
	}
	
	
	public function setHotelMinOfferPrice(){
		$hotelId = $this->_hotel_id;
	
		$query="select rr.base_adults, rr.price_type, least(rr.price_1, rr.price_2, rr.price_3, rr.price_4, rr.price_5, rr.price_6, rr.price_7) as min_rate,
		min(rrp.price) as min_rate_custom
		from #__hotelreservation_offers r
		inner join #__hotelreservation_offers_rates rr on r.offer_id = rr.offer_id
		left join #__hotelreservation_offers_rate_prices rrp on rrp.rate_id = rr.id
		where r.is_available = 1 and r.hotel_id= $hotelId
		group by r.hotel_id";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
		
		
		$price = $result->min_rate;
		if(isset($result->min_rate_custom) && $price>$result->min_rate_custom){
			$price = $result->min_rate_custom;
		}

		if($result->price_type == 0){
			$price = $price / $result->base_adults;
		}

		$query="update #__hotelreservation_hotels set min_offer_price = $price where hotel_id = $hotelId ";

		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			dmp($query);
			dmp("error");
		}
		exit;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'offer_id', array(0), 'post', 'array' );
		
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations					c
					INNER JOIN #__hotelreservation_confirmations_rooms		r USING( confirmation_id )
					WHERE 
						r.offer_id IN (".implode(',', $cids).") 
						AND
						c.hotel_id IN (".$this->_hotel_id.") 
						AND 
						c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
						
		$checked_records = $this->_getList( $query );
		if ( count($checked_records) > 0 ) 
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_OFFER_REMOVE',true) );
			return false;
		}

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					$msg = JText::_( 'LNG_OFFER_ERROR_DELETE' ,true);
					return false;
				}
			}
		}
		return true;

	}
	
	function state()
	{
		$query = 	" UPDATE #__hotelreservation_offers SET is_available = IF(is_available, 0, 1) WHERE offer_id = ".$this->_offer_id ." AND hotel_id = ".$this->_hotel_id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
			
		}
		return true;
	}
	
	function changeFeaturedState()
	{
		$hotelId = JRequest::getVar('hotel_id');
		$query = 	" UPDATE #__hotelreservation_offers SET featured = IF(featured, 0, 1) WHERE offer_id = ".$this->_offer_id;
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	
	function changeTopState()
	{
		$hotelId = JRequest::getVar('hotel_id');
		$query = 	" UPDATE #__hotelreservation_offers SET top = IF(top, 0, 1) WHERE offer_id = ".$this->_offer_id;
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
	

	function getLastOrderNumber($hotelId){
		$query = "select from max(offer_order) as offer_order from  #__hotelreservation_offers WHERE  hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		$offer = $this->_db->loadObject();
		return $offer->offer_order;
	}
	
	function getLastOrder($offerId)
	{
		$offer_id = 0;
		if( isset($offerId) )
			$offer_id = $offerId;
		$increment = 0;
		if( $offer_id > 0 ){
			$query = 	" SELECT * FROM #__hotelreservation_offers  WHERE offer_id = ".$offer_id;
		} else {
			$query = 	" SELECT * FROM #__hotelreservation_offers  ORDER BY offer_order DESC LIMIT 1 ";
			$increment++;
		}
		
		$db 	= JFactory::getDBO();
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
	
		if(!isset($row ))
			return 1;
	
		return ($row->offer_order+$increment);
	}

	function getExtraOptions(){
		$query = "select * from  #__hotelreservation_extra_options WHERE  hotel_id = ". $this->_hotel_id;
		$this->_db->setQuery( $query );
		$extraOptions = $this->_db->loadObjectList();
		return $extraOptions;
	}
}
?>