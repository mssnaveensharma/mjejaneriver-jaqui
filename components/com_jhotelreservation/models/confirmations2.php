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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport('joomla.user.helper');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelConfirmations extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	function store($data, $is_transaction = true, $is_frontend = true, &$answer_processor = '' )
	{	
		 //dmp($data->items_reserved);
		// exit;
		$rspProcessorPayment			= array();
		$max_records					= 1;
		$confirmation_id				= $data->confirmation_id;
		
		$is_edit 						= $data->confirmation_id > 0;
		$arr_answer						= array(0, '');
		try
		{
			$db =JFactory::getDBO();
			if( $is_transaction )
			{
				$db->setQuery("START TRANSACTION");
				$db->query();
			}
		
			
			$i_max = 11;
			for( $i = 0; $i <= $i_max; $i ++ )
			{
							
				$rowTable			= null;
				$rowTableManage		= null;
				$obj_ids	 		= null;
				
				switch( $i )
				{
					case 0:
					case 11:
						$max_records 					=	1;
						break;
					case 1:
						$obj_ids	 					= 	$data->items_reserved;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRooms');
						///continue;
						break;
					case 2:
						$obj_ids	 					= 	$data->itemRoomsSelected;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRoomsNumbersDates');
						break;
					case 3:
						$obj_ids	 					= 	$data->itemRoomsSelected;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRoomsNumbersDatesDiscounts');
						break;
					case 4:
						$obj_ids	 					= 	$data->package_ids;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRoomsPackages');
						break;
					case 5:
						$obj_ids	 					= 	$data->itemPackages;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRoomsPackagesDates');
						break;
					case 6:
						$obj_ids	 					= 	$data->option_ids;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsFeatureOptions');
						break;
					case 7:
						$obj_ids	 					= 	$data->extraOptionIds;
						//dmp($data->extraOptionIds);
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsExtraOptions');
						break;
					case 8:
						$obj_ids	 					= 	$data->itemAirportTransferTypes;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsRoomsAirportTransfer');
						break;
					case 9:
						$obj_ids	 					= 	$data->itemTaxes;
						$max_records 					= 	count($obj_ids);
						$rowTable						= 	 $this->getTable('ConfirmationsTaxes');
						break;
					case 10:
						$max_records 					=	1;
						$rowTable						= 	 $this->getTable('ConfirmationsPayments');
						break;

				}

				if( $i > 0 && $is_edit && $rowTable != null ) //delete all records for new records
				{
					if($is_edit && $i != 10 && $i != 11)
					{
						$rowTable->setKey('confirmation_id');
						
						if (!$rowTable->delete($data->confirmation_id) ) 
						{
							throw( new Exception($this->_db->getErrorMsg()) ); 
							
						}
					}
				}
			
				for( $j = 0; $j < $max_records ; $j ++ )
				{
					$max_subrecords					= 1;
					$j_key 							= $j;
					if( $i==2 || $i==3 || $i==5 || $i==7 || $i == 8 )
					{
						if(  $i==5 )
						{
							$j_tmp = 0;
							foreach( $obj_ids as $x => $y )
							{
								if($j_tmp == $j )
								{
									$max_subrecords	= count($obj_ids[$x]->daily);
									$j_key			= $x;
									break;
								}
								$j_tmp ++;
							}
						}
						else if(  $i==7 || $i==8 )
						{
							$j_tmp = 0;
							//dmp($j);
							// exit;
							$max_subrecords					= 1;
							foreach( $obj_ids as $x => $y )
							{
								if($j_tmp == $j )
								{
									$j_key			= $x;
									break;
								}
								$j_tmp ++;
							}
						}
						else
						{
							$max_subrecords					= count($obj_ids[$j_key]->daily);
						}
					}
					
					for( $k = 0; $k < $max_subrecords ; $k ++ )
					{
						$max_subsubrecords				= 1;
						$k_key							= $k;
						if( $i==2 || $i==3 )
						{
							$max_subsubrecords				= isset($data->itemRoomsCapacity[ $obj_ids[ $j_key ]->room_id ][1] )? 
																$data->itemRoomsCapacity[ $obj_ids[ $j_key ]->room_id ][1] 
																: 
																"0";

						}
			
						for( $l = 0; $l < $max_subsubrecords ; $l ++ )
						{	
							$l_key								= $l;
							$max_subsubsubrecords				= 1;
							if( $i==2 )
							{
								$max_subsubsubrecords				= isset($obj_ids[$j_key]->daily[ $k_key ]['numbers']) ? count($obj_ids[$j_key]->daily[ $k_key ]['numbers'] ) : 0;
							}
							else if( $i==3 )
							{
								$max_subsubsubrecords				= isset($obj_ids[$j_key]->daily[ $k_key ]['numbers'][$l_key]['discounts']) ? count($obj_ids[$j_key]->daily[ $k_key ]['numbers'][$l_key]['discounts'] ) : 0;
							}
							for( $m = 0; $m < $max_subsubsubrecords ; $m ++ )
							{
								$m_key				= $m;
								$obj				= new stdClass();
								
								$obj->datas			= date( "Y-m-d", strtotime($data->year_start."-".$data->month_start."-".$data->day_start)	);
								$obj->datae			= date( "Y-m-d", strtotime($data->year_end."-".$data->month_end."-".$data->day_end)			);
								
							
								if( $data->hotel_id == 0 )
									throw( new Exception(" Invalid ID Hotel, please contact administrator !") ); 
								switch( $i )
								{
									case 0:
										$rowTable						= 	 $this->getTable('Confirmations');
										$obj->confirmation_id			=	$confirmation_id;
										$obj->hotel_id					=	$data->hotel_id;
										$obj->data						= 	date('Y-m-d H:i:s')	;
										$obj->guest_adult				= 	$data->guest_adult;
										$obj->guest_child				= 	$data->guest_child;
										$obj->rooms						= 	$data->rooms;
										$obj->coupon_code				=	$data->coupon_code;
										
										$obj->guest_type				=	$data->guest_type;
										$obj->first_name				=	$data->first_name;
										$obj->last_name					=	$data->last_name;
										$obj->details					=	$data->details;
										$obj->address					=	$data->address;
										$obj->postal_code				=	$data->postal_code;
										$obj->city						=	$data->city;
										$obj->state_name				=	$data->state_name;
										$obj->country					=	$data->country;
										$obj->tel						=	$data->tel;
										$obj->email						=	$data->email;
										$obj->conf_email				=	$data->conf_email;
										$obj->card_type_name			=	$data->card_type_name;
										$obj->card_name					=	$data->card_name;
										$obj->card_number				=	$data->card_number;
										$obj->card_expiration_month		=	$data->card_expiration_month;
										$obj->email_confirmation		= 	$data->Confirmation;
										$obj->reservation_status		=	$data->reservation_status;	
										$obj->is_enable_payment			= 	$data->is_enable_payment;
										$obj->total						= 	$data->total;
										$obj->total_cost				= 	$data->total_cost;
										$obj->total_payed				= 	$data->total_payed;
										$obj->confirmation_payment_status = $data->confirmation_payment_status;
										$obj->key_control_reservation	= $data->key_control_reservation;
										$obj->media_referer				= $data->mediaReferer;
										$obj->voucher					= $data->voucher;
										$obj->company_name				= $data->company_name;
										$obj->discount_code				= $data->discount_code;
										
										// dmp($obj);
										// exit;
										break;
									case 1:
										$rowTable						= 	 $this->getTable('ConfirmationsRooms');
										$rowTableManage					= 	 $this->getTable( "ManageRooms" );
										$ex_room = explode( "|", $obj_ids[$j_key]);
										$rowTableManage->load( $ex_room[1] );
										$obj->confirmation_id			=	$confirmation_id;
										$obj->hotel_id					=	$rowTableManage->hotel_id;
										$obj->offer_id					=	$ex_room[0];
										$obj->room_id					=	$rowTableManage->room_id;
										$obj->current					=	$ex_room[2];
										$obj->room_name					=	$rowTableManage->room_name;
										$obj->room_price_1				=	$rowTableManage->room_price_1;
										$obj->room_price_2				=	$rowTableManage->room_price_2;
										$obj->room_price_3				=	$rowTableManage->room_price_3;
										$obj->room_price_4				=	$rowTableManage->room_price_4;
										$obj->room_price_5				=	$rowTableManage->room_price_5;
										$obj->room_price_6				=	$rowTableManage->room_price_6;
										$obj->room_price_7				=	$rowTableManage->room_price_7;
										$obj->nr_guests					= 	$data->room_guests[$obj->current-1];
										//dmp($data->itemRoomsCapacity);
										//dmp($rowTableManage->room_id);
										if( 
											count($data->itemRoomsCapacity) == 0 
											|| 
											!isset($data->itemRoomsCapacity[$rowTableManage->room_id])
											||
											$data->itemRoomsCapacity[$rowTableManage->room_id][1] == 0
										)
										{
											throw( new Exception(" Count ROOMs ERROR !") ); 
										}
										else
										{
											$obj->rooms				= 	$data->itemRoomsCapacity[$rowTableManage->room_id][1];
										}
										
										break;
										
									case 2:
										$rowTable						= 	 $this->getTable('ConfirmationsRoomsNumbersDates');
										
										$obj->confirmation_id			=	$confirmation_id;
										$obj->offer_id					=	$obj_ids[$j_key]->offer_id;
										$obj->room_id					=	$obj_ids[$j_key]->room_id;
										$obj->current					=	$obj_ids[$j_key]->current;
										
										$obj->room_number_id			=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['id'];
										$obj->room_number_number		= 	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['nr'];
										$obj->room_number_data			= 	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['data'];
										$obj->room_number_price			= 	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['price'];
										
										break;
									case 3:
										$rowTable						= 	 $this->getTable('ConfirmationsRoomsNumbersDatesDiscounts');
										
										$obj->confirmation_id					=	$confirmation_id;
										$obj->offer_id							=	$obj_ids[$j_key]->offer_id;
										$obj->room_id							=	$obj_ids[$j_key]->room_id;
										$obj->current							=	$j_key;
										
										$obj->confirmation_room_number_date_id	=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['confirmation_room_number_date_id'];
																		
										if( $obj_ids[$j]->offer_id == 0 )
										{
											$obj->discount_id			=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->discount_id;
											$obj->discount_name				=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->discount_name;
											$obj->discount_datas			=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->discount_datas;
											$obj->discount_datae			=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->discount_datae;
											$obj->discount_value			=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->discount_value;
										}
										else
										{
// 											dmp($obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts']);
											$obj->offer_discount_id	=	$obj_ids[$j_key]->daily[$k_key]['numbers'][$l_key]['discounts'][$m_key]->offer_room_price_id;
											// $obj->discount_name				=	$obj_ids[$j]->daily[$k]['numbers'][$l]['discounts'][$m]->discount_name;
											// $obj->discount_datas			=	$obj_ids[$j]->daily[$k]['numbers'][$l]['discounts'][$m]->discount_datas;
											// $obj->discount_datae			=	$obj_ids[$j]->daily[$k]['numbers'][$l]['discounts'][$m]->discount_datae;
											// $obj->discount_value			=	$obj_ids[$j]->daily[$k]['numbers'][$l]['discounts'][$m]->discount_value;
										}
										break;
									case 4:
										$rowTable						= 	 $this->getTable('ConfirmationsRoomsPackages');
										$rowTableManage					= 	 $this->getTable( "ManagePackages" );
										
										$rowTableManage->load( $obj_ids[$j_key][3] );
										
										if( $rowTableManage->package_id + 0 == 0 )
											continue;
										//dmp($rowTableManage->package_id);	
										
										$obj->confirmation_id			=	$confirmation_id;
										$obj->hotel_id					=	$data->hotel_id;
										$obj->offer_id					=	$obj_ids[$j_key][0];
										$obj->room_id					=	$obj_ids[$j_key][1];
										$obj->current					=	$obj_ids[$j_key][2];
																				
										$obj->package_id				=	$rowTableManage->package_id;
										$obj->package_name				=	$rowTableManage->package_name;
										$obj->package_price				=	$rowTableManage->package_price;
										$obj->is_price_day				=	$rowTableManage->is_price_day;

										$ch_package = $obj_ids[$j_key][0].'|'.$obj_ids[$j_key][1].'|'.$obj_ids[$j_key][2].'|'.$obj_ids[$j_key][3];
										//dmp($data->itemPackageNumbers);
										//dmp($ch_package);
										
										if( 
											count($data->itemPackageNumbers) == 0 
											|| 
											!isset($data->itemPackageNumbers[$ch_package])
											||
											$data->itemPackageNumbers[$ch_package][4] == 0
										)
										{
											throw( new Exception(" Count PACKAGEs ERROR !") ); 
										}
										else
										{
											$obj->package_number		= 	$data->itemPackageNumbers[$ch_package][4];
										}

										// dmp($data->itemPackageNumbers);
										
										
										break;
									case 5:
										
										$rowTable						= 	 $this->getTable('ConfirmationsRoomsPackagesDates');
										$rowTableManage					= 	 $this->getTable( "ManagePackages" );
										$ex_package = explode( "|", $j_key);
										$rowTableManage->load( $ex_package[3] );



										if( $rowTableManage->package_id + 0 == 0  
											|| 
											( $obj_ids[$j_key]->daily[$k_key]['is_sel'] == false && $obj_ids[$j_key]->is_price_day ==true )
										)
											continue;
										
										$ch_package = $ex_package[0].'|'.$ex_package[1].'|'.$ex_package[2].'|'.$ex_package[3];

										$obj->confirmation_id			=	$confirmation_id;
										$obj->offer_id					=	$ex_package[0];
										$obj->room_id					=	$ex_package[1];
										$obj->current					=	$ex_package[2];
										$obj->package_id				=	$rowTableManage->package_id;
										$obj->package_data				=	$obj_ids[$j_key]->daily[$k_key]['data'];
										$obj->package_price				=	$obj_ids[$j_key]->daily[$k_key]['price_final'];
										
										if( 
											count($data->itemPackageNumbers) == 0 
											|| 
											!isset($data->itemPackageNumbers[$ch_package])
											||
											$data->itemPackageNumbers[$ch_package][4] == 0
										)
										{
											// dmp($data->package_ids);
											throw( new Exception(" Count PACKAGEs Data ERROR !") ); 
										}
										else
										{
											$obj->package_number		= 	$data->itemPackageNumbers[$ch_package][4];
										}

										// dmp($data->itemPackageNumbers);
										
										break;
									case 6:
										
										if($obj_ids[$j_key]=='')
											continue;
										$rowTable						= 	 $this->getTable('ConfirmationsFeatureOptions');
										$rowTableManage					= 	 $this->getTable( "ManageRoomFeatureOptions" );
									
									
										$rowTableManage->load( $obj_ids[$j_key] );
										
										if( $rowTableManage->option_id + 0 == 0 )
											continue;
										$obj->confirmation_id			=	$confirmation_id;
										$obj->option_id					=	$rowTableManage->option_id;
										$obj->option_name				=	$rowTableManage->option_name;
										$obj->option_price				=	$rowTableManage->option_price;
										
										break;
									case 7:
										
										$rowTable						= 	 $this->getTable('ConfirmationsExtraOptions');
										$rowTableManage					= 	JTable::getInstance("ExtraOption","JTable");
									
										$ex_extra_options = $obj_ids[$j_key];
										//$ex_extra_options = explode( "|", $j_key);
										$rowTableManage->load( $ex_extra_options[3] );
										
										if( $rowTableManage->id + 0 == 0 )
											continue;
										$obj->confirmation_id			=	$confirmation_id;
										$obj->hotel_id					=	$data->hotel_id;
										$obj->offer_id					=	$ex_extra_options[0];
										$obj->room_id					=	$ex_extra_options[1];
										$obj->current					=	$ex_extra_options[2];
										$obj->extra_option_id			=	$rowTableManage->id;
										$obj->extra_option_name			=	$rowTableManage->name;
										$obj->extra_option_price		=	$rowTableManage->price;
										$obj->extra_option_price_type	=	$rowTableManage->price_type;
										$obj->extra_option_is_per_day	=	$rowTableManage->is_per_day;
										$obj->extra_option_mandatory	=	$rowTableManage->mandatory;
										$obj->extra_option_persons		=	$ex_extra_options[5];
										$obj->extra_option_days			=	$ex_extra_options[6];
										
										//dmp($obj);
										//dmp($ex_extra_options);
										
										break;
									case 8:
										$ex_airport_transfer 	= explode("|", $j_key);
										$ch_airport 			= $ex_airport_transfer[0].'|'.$ex_airport_transfer[1].'|'.$ex_airport_transfer[2];
										
										
										$rowTable						= 	 $this->getTable('ConfirmationsRoomsAirportTransfer');
										
										$rowTableManage					= 	 $this->getTable( "ManageAirportTransferTypes" );
										$rowTableManage->load( $ex_airport_transfer[3] );
																			
										if( 
											$rowTableManage->airport_transfer_type_id + 0 == 0 
											||
											!isset( $data->airport_airline_ids[$ch_airport] )
											||
											!isset( $data->airport_transfer_dates[$ch_airport] )
											||
											!isset( $data->airport_transfer_time_hours[$ch_airport] )
											||
											!isset( $data->airport_transfer_time_mins[$ch_airport] )
											||
											!isset( $data->airport_transfer_flight_nrs[$ch_airport] )
											||
											!isset( $data->airport_transfer_guests[$ch_airport] )
										)
										{
											continue;
										}

										$obj->confirmation_id				=	$confirmation_id;
										$obj->hotel_id						=	$data->hotel_id;
										$obj->offer_id						=	$ex_airport_transfer[0];
										$obj->room_id						=	$ex_airport_transfer[1];
										$obj->current						=	$ex_airport_transfer[2];
										$obj->airport_transfer_type_id		=	$rowTableManage->airport_transfer_type_id;
										$obj->airport_transfer_type_name	=	$rowTableManage->airport_transfer_type_name;
										$obj->airport_transfer_type_price	=	$rowTableManage->airport_transfer_type_price;
										$obj->airport_transfer_type_vat		=	$rowTableManage->airport_transfer_type_vat;
										
										$rowTableManage					= 	 $this->getTable( "ManageAirlines" );
										$rowTableManage->load( $data->airport_airline_ids[ $ch_airport ][3] );
										
										
										if( $rowTableManage->airline_id + 0 == 0 )
											continue;
										$obj->airline_id					=	$rowTableManage->airline_id;
										$obj->airline_name					=	$rowTableManage->airline_name;
										
										$obj->airport_transfer_flight_nr		=	$data->airport_transfer_flight_nrs[$ch_airport][3];
										$obj->airport_transfer_date				=	$data->airport_transfer_dates[$ch_airport][3];
										$obj->airport_transfer_time_hour		=	$data->airport_transfer_time_hours[$ch_airport][3];
										$obj->airport_transfer_time_min			=	$data->airport_transfer_time_mins[$ch_airport][3];
										$obj->airport_transfer_guest			=	$data->airport_transfer_guests[$ch_airport][3];
										
										break;
										
									case 9:
										
										$rowTable						= 	 $this->getTable('ConfirmationsTaxes');
										$rowTableManage					= 	 $this->getTable( "ManageTaxes" );
									
										$rowTableManage->load( $obj_ids[$j]->tax_id );
										
										if( $rowTableManage->tax_id +0== 0 )
											continue;
										
										$obj->confirmation_id			=	$confirmation_id;
										$obj->tax_id					=	$rowTableManage->tax_id;
										$obj->tax_name					=	$rowTableManage->tax_name;
										$obj->hotel_id					=	$data->hotel_id;
										$obj->tax_type					=	$rowTableManage->tax_type;
										$obj->tax_value					=	$rowTableManage->tax_value;
										
										break;
									case 10:
									
										if($is_edit)
										{
											//$i_max = 10;
											continue;
										}
										//dmp($data->payment_processor_sel_id);
										if( $data->need_preauthorized == false || $data->is_enable_payment == false || $data->payment_processor_sel_id <= 0)
										{
											continue;
										}
										
										if( $data->payment_processor_sel_type == PROCESSOR_PAYFLOW)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(PREAUTHORIZATION_PAYMENT_ID) );
											//dmp($rowTablePayment);
											if( $rowTablePayment->payment_id > 0 )
											{
											
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;	
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;	
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;	

												$obj->payment_status				= PAYMENT_STATUS_BLOCK;	
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;	
													$obj->payment_value				= $rowTablePayment->payment_value;	
												}
												
												$obj->tip							= 'card';	
												$obj->TRXTYPE						= 'A';	
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';	
												$obj->DOREAUTHORIZATION				= '';	
												$obj->CAPTURECOMPLETE				= '';	
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_AUTHORIZE)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(PREAUTHORIZATION_PAYMENT_ID) );
										
											if( $rowTablePayment->payment_id > 0 )
											{
													
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;
										
												$obj->payment_status				= PAYMENT_STATUS_BLOCK;
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;
													$obj->payment_value				= $rowTablePayment->payment_value;
												}
										
												$obj->tip							= 'card';
												$obj->TRXTYPE						= 'A';
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';
												$obj->DOREAUTHORIZATION				= '';
												$obj->CAPTURECOMPLETE				= '';
											}
										}

										else if( $data->payment_processor_sel_type == PROCESSOR_PAYPAL_EXPRESS)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(PAYPAL_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;	
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;	
												$obj->payment_status				= PAYMENT_STATUS_PENDING;	
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;	
													$obj->payment_value				= $rowTablePayment->payment_value;	
												}
												$obj->tip							= 'website';	
												$obj->TRXTYPE						= '';	
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';	
												$obj->DOREAUTHORIZATION				= '';	
												$obj->CAPTURECOMPLETE				= '';	
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_BANK_ORDER)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(BANK_ORDER_ID) );
											
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;	
												$obj->payment_status				= PAYMENT_STATUS_WAITING;	
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;	
													$obj->payment_value				= $rowTablePayment->payment_value;	
												}
												$obj->tip							= 'bank';	
												$obj->TRXTYPE						= '';	
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';	
												$obj->DOREAUTHORIZATION				= '';	
												$obj->CAPTURECOMPLETE				= '';	
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_CASH)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(CASH_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;	
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;	
												$obj->payment_status				= PAYMENT_STATUS_NOTPAYED;	
												$obj->payment_percent				= $rowTablePayment->payment_percent;	
												$obj->payment_value					= $rowTablePayment->payment_value;	
												$obj->tip							= 'cash';	
												$obj->TRXTYPE						= '';	
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';	
												$obj->DOREAUTHORIZATION				= '';	
												$obj->CAPTURECOMPLETE				= '';	
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_MPESA)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(MPESA_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;	
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_tel					= $data->payment_tel;	
												$obj->payment_code					= $data->payment_code;	
												$obj->payment_explication			= $rowTablePayment->payment_name;	
												$obj->payment_status				= PAYMENT_STATUS_WAITING;	
												$obj->payment_percent				= $rowTablePayment->payment_percent;	
												$obj->payment_value					= $rowTablePayment->payment_value;	
												$obj->tip							= 'phone';	
												$obj->TRXTYPE						= 'X';	//set X for try payment
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';	
												$obj->DOREAUTHORIZATION				= '';	
												$obj->CAPTURECOMPLETE				= '';	
												
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_IDEAL_OMNIKASSA)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(IDEAL_OMNIKASSA_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;
												$obj->payment_status				= PAYMENT_STATUS_PENDING;
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;
													$obj->payment_value				= $rowTablePayment->payment_value;
												}
												$obj->tip							= 'website';
												$obj->TRXTYPE						= '';
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';
												$obj->DOREAUTHORIZATION				= '';
												$obj->CAPTURECOMPLETE				= '';
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_BUCKAROO)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(IDEAL_OMNIKASSA_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;
												$obj->payment_status				= PAYMENT_STATUS_PENDING;
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;
													$obj->payment_value				= $rowTablePayment->payment_value;
												}
												$obj->tip							= 'website';
												$obj->TRXTYPE						= '';
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';
												$obj->DOREAUTHORIZATION				= '';
												$obj->CAPTURECOMPLETE				= '';
											}
										}
										else if( $data->payment_processor_sel_type == PROCESSOR_4B)
										{
											$rowTable					= 	 $this->getTable('ConfirmationsPayments');
											$rowTablePayment			= 	 $this->getTable( "PaymentSettings" );
											$rowTablePayment->load( $data->getIDPaymentSettings(P4B_ID) );
											if( $rowTablePayment->payment_id > 0 )
											{
												$obj->confirmation_id				= $confirmation_id;
												$obj->payment_id					= $rowTablePayment->payment_id;
												$obj->payment_type_id				= $rowTablePayment->payment_type_id;
												$obj->paymentprocessor_id			= $data->payment_processor_sel_id;
												$obj->data							= date('Y-m-d h:i:s')	;
												$obj->payment_explication			= $rowTablePayment->payment_name;
												$obj->payment_status				= PAYMENT_STATUS_PENDING;
												if($data->confirmation_id == 0 && $data->total_cost > 0 )
												{
													$obj->payment_value				= $data->total_cost;
													$obj->payment_percent			= 0;
												}
												else
												{
													$obj->payment_percent			= $rowTablePayment->payment_percent;
													$obj->payment_value				= $rowTablePayment->payment_value;
												}
												$obj->tip							= 'website';
												$obj->TRXTYPE						= '';
												$obj->AMT							= 0.00;
												$obj->ORIGID						= '';
												$obj->DOREAUTHORIZATION				= '';
												$obj->CAPTURECOMPLETE				= '';
											}
										}
										
										
										else
											continue;

											
										//$i_max =11 ;
										//else
										//	continue;
										break;
									case 11:
										//dmp($obj);
										//exit;
										
										// throw( new Exception(JText::_('LNG_ERR_PAYMENT',true)) ); 
										$curency_tmp 					= 	$data->getCurrencyName( $data->itemAppSettings->currency_id );
										$obj->currency_id				= 	$curency_tmp->currency_id;
										$obj->currency_name				= 	$curency_tmp->description;

										//if( $is_edit ==false  )	//daca avem edit nu mai facem payment
										{
											$query = 	'	 	
															SELECT 
																ps.* ,
																proc.paymentprocessor_type,
																proc.paymentprocessor_username,
																proc.paymentprocessor_address
															FROM #__hotelreservation_confirmations_payments 	ps
															INNER JOIN #__hotelreservation_paymentprocessors 	proc	USING(paymentprocessor_id)
															WHERE 
																ps.confirmation_id = '.$confirmation_id.' 
																	'.($data->is_enable_payment? " AND ( ps.TRXTYPE <> ''  /*OR ps.tip='card' */) " : " AND ps.payment_status <> '".PAYMENT_STATUS_PAYED."'").' 
															ORDER BY ps.confirmations_payments_id 
														';

											$ps 			= 	$this->_getList( $query );
											

											// dmp($ps);
											// dmp($query);
											$rowTable		= 	 $this->getTable('ConfirmationsPayments');
											//exit;
											if( count($ps) > 0 )
											{
												foreach( $ps as $row )
												{
													if( $row->AMT == 0.00 )
													{
														$value = $data->total_init ;

														if( $row->payment_percent > 0 )
															$value = JHotelUtil::my_round($value*$row->payment_percent/100,2);
														else
															$value = JHotelUtil::my_round($row->payment_value,2);

														$row->AMT = $value;
													}
													$arr_answer= array(0, '');

													// dmp($data->is_enable_payment);
													// dmp($row);
													// exit;
													$sufix_language = 'PAYFLOW';
													if( $data->is_enable_payment )
													{		
														switch( $row->paymentprocessor_type )
														{
															case PROCESSOR_PAYFLOW:
																$sufix_language = 'PAYFLOW';
																$rspProcessorPayment		= array(
																										'responce'				=> array(), 
																										'answer'				=>'', 
																										'code'					=>'', 
																										'paymentprocessor_id'	=>$row->paymentprocessor_id
																									);
																									// dmp($row);
																									// exit;
																$arr_answer = $data->payflow(	
																							$obj->currency_name,
																							$row->AMT, 
																							$row->TRXTYPE,
																							$row->ORIGID,
																							//in_array($data->reservation_status, array(CANCELED_ID, CHECKEDOUT_ID)) ? $row->payflow_id : '',
																							$row->DOREAUTHORIZATION,
																							$row->CAPTURECOMPLETE,
																							$rspProcessorPayment
																						);
																// dmp($arr_answer);
																// exit;
																break;
															case PROCESSOR_AUTHORIZE:
																$sufix_language = 'AUTHORIZE';
																
																$customer = (object)array();
																$customer->first_name = $data->first_name;
																$customer->last_name = $data->last_name;
																$customer->address = $data->address;
																$customer->city = $data->city	;
																$customer->state = $data->state_name;
																$customer->zip = $data->postal_code;
																$customer->country = $data->country;
																$customer->phone = $data->tel;
																$customer->email = $data->email;

																$data_1 =  $data->year_start.'-'.$data->month_start.'-'.$data->day_start;
																$datas = JHotelUtil::getDateGeneralFormat($data_1);
																$data_2 = $data->year_end.'-'.$data->month_end.'-'.$data->day_end;
																$datae = JHotelUtil::getDateGeneralFormat($data_2);
																
																$order = array(
																            'description' => 'Hotel Reservation at '.$data->itemHotelSelected->hotel_name.'('.$datas.'-'.$datae.')',
																            'invoice_num' => $confirmation_id
																);
																
														        $creditCard = array(
														            'exp_date' => $data->card_expiration_month."".substr($data->card_expiration_year,-2),
														            'card_num' => $data->card_number,
														       		'amount' => $row->AMT														            );

																$authorize = new authorize();
																$arr_answer = $authorize->chargeCreditCard($creditCard,$order,$customer);
																//print_r($arr_answer);
																break;
															case PROCESSOR_PAYPAL_EXPRESS:
																break;
															case PROCESSOR_IDEAL_OMNIKASSA:
																break;
															case PROCESSOR_4B:
																break;
															case PROCESSOR_MPESA:
																$sufix_language = 'MPESA';
																$rspProcessorPayment		= array(
																										'responce'				=> array(), 
																										'answer'				=>'', 
																										'code'					=>'', 
																										'txn'					=>array(),
																										'paymentprocessor_id'	=>$row->paymentprocessor_id
																									);
																									// dmp($row);
																									// exit;
																$arr_answer = $data->pesapi(	
																							$data->payment_name,
																							$data->payment_tel,
																							$row->AMT, 
																							$data->payment_code,
																							$rspProcessorPayment
																						);
																// exit;
																
																break;
															default:
																break;
														}
													}
													else
													{
														$arr_answer= array(0, '');
													}
													// dmp($arr_answer);
													// exit;
													
													if( $arr_answer[0] > 0  )
													{
														
														
														//throw( new Exception(PAYMENT_STATUS_PAYED." ERROR !") ); 
														//exit;



														$rspProcessorPayment['answer'] 	= $arr_answer[1];
														$rspProcessorPayment['code'] 	= $arr_answer[0];
														
														$err_processors 	= 	JText::_('LNG_ERR_PAYMENT',true);
														$reasonCode = "";
														
														if( $sufix_language=='MPESA')
														{
															/*
															if( $arr_answer[0]==6 )
															{
																$err_processors 	.= prepareErrorMsgProcessor($arr_answer[1]);
																throw( new Exception($err_processors) ); 
															}
															*/
														}
														else if($sufix_language=='AUTHORIZE'){
															$reasonCode = " Reason:".$arr_answer[1];
														}
														$err_processors 	.= prepareErrorMsgProcessor(JText::_("LNG_".$sufix_language."_".$arr_answer[0],true)." ".$reasonCode);
														
  														throw( new Exception($err_processors) ); 
													}
													else
														$this->logger_processor($rspProcessorPayment);
		
													
													$obj->confirmation_id				= $row->confirmation_id;
													$obj->confirmations_payments_id		= $row->confirmations_payments_id;
													$obj->payment_id					= $row->payment_id;
													$obj->payment_type_id				= $row->payment_type_id;
													$obj->payment_tel					= $row->payment_tel;
													$obj->payment_code					= $row->payment_code;
													$obj->data							= $row->data;
													$obj->payment_explication			= $row->payment_explication;
													$obj->tip							= $row->tip;
													if( $data->is_enable_payment  )
													{
														if( $data->payment_processor_sel_type  == PROCESSOR_PAYFLOW )
														{
															$obj->payment_status			= 	
																								($arr_answer[0]>0?
																									PAYMENT_STATUS_REJECTED
																									:
																									(
																										
																										$is_edit == false && 0 ?
																											PAYMENT_STATUS_BLOCK
																											:
																											(
																											$row->TRXTYPE=='S' || ( $row->TRXTYPE!='V' && $data->confirmation_payment_status == PAYMENT_STATUS_PAYED ) ? 
																												PAYMENT_STATUS_PAYED
																												:
																												(
																													$row->TRXTYPE=='A' ? 
																														PAYMENT_STATUS_BLOCK 
																														: 
																														( $row->TRXTYPE=='V' ? PAYMENT_STATUS_RELEASED : PAYMENT_STATUS_PAYED )
																												)
																											)
																									)
																								);
														}
														else if( $data->payment_processor_sel_type  == PROCESSOR_PAYPAL_EXPRESS )
														{
															$obj->payment_status			= PAYMENT_STATUS_PENDING;
														}
														else if( $data->payment_processor_sel_type  == PROCESSOR_AUTHORIZE )
														{
															$obj->payment_status = ($arr_answer[0]>0? PAYMENT_STATUS_REJECTED:PAYMENT_STATUS_PAYED);
														}
														else if( $data->payment_processor_sel_type  == PROCESSOR_IDEAL_OMNIKASSA )
														{
															$obj->payment_status			= PAYMENT_STATUS_PENDING;
														}
														else if( $data->payment_processor_sel_type  == PROCESSOR_BANK_ORDER )
														{
															$obj->payment_status			= PAYMENT_STATUS_WAITING;
														}
														else if( $data->payment_processor_sel_type  == PROCESSOR_MPESA )
														{
															$obj->payment_status			= 	
																								($arr_answer[0]>0?
																									PAYMENT_STATUS_REJECTED
																									:
																									PAYMENT_STATUS_PAYED
																								);
														}
													}
													else
													{
														$obj->payment_status			= $data->confirmation_payment_status == PAYMENT_STATUS_PAYED ? PAYMENT_STATUS_PAYED : PAYMENT_STATUS_NOTPAYED;
													}
													
													$obj->payment_percent				= $row->payment_percent;	
													$obj->payment_value					= $row->payment_value;	
													$obj->payment_answer				= $arr_answer[1];
													//$obj->payment_code					= $arr_answer[1];
													$obj->TRXTYPE						= '';
													$obj->AMT							= $row->AMT;
													if( $data->is_enable_payment  )
													{
														if( $data->payment_processor_sel_type  == PROCESSOR_PAYFLOW )
														{
															$obj->ORIGID						= ($row->TRXTYPE=='S' || $data->confirmation_payment_status == PAYMENT_STATUS_PAYED ? '' : $data->ID_PAYMENT_PAYFLOW);
														}
														if( $data->payment_processor_sel_type  == PROCESSOR_MPESA )
														{
															$obj->ORIGID						= ($row->TRXTYPE=='' || $data->confirmation_payment_status == PAYMENT_STATUS_PAYED ? '' : $data->ID_PAYMENT_MPESA);
															$obj->payment_value					= $row->AMT;
															$obj->payment_percent				= 0;					
														}
													}
													$obj->DOREAUTHORIZATION				= '';
													$obj->CAPTURECOMPLETE				= '';
													
													if (!$rowTable->bind($obj)) 
													{
														throw( new Exception(JText::_('LNG_ERR_PAYMENT',true)) ); 
														//throw( new Exception($this->_db->getErrorMsg()) ); 
													}
													
													if (!$rowTable->check()) 
													{
														throw( new Exception(JText::_('LNG_ERR_PAYMENT',true)) ); 
														//throw( new Exception($this->_db->getErrorMsg()) ); 
													}

													if (!$rowTable->store()) 
													{
														throw( new Exception(JText::_('LNG_ERR_PAYMENT',true)) ); 
														//throw( new Exception($this->_db->getErrorMsg()) ); 
													}
												}
											}
										}

										$data->confirmation_id			=	$confirmation_id;
										$data->writeAllInfos();
										
										$obj->confirmation_id			=	$confirmation_id;
										$rowTable						= 	 $this->getTable('Confirmations');
										$obj->data						= 	date('Y-m-d H:i:s')	;
										$obj->guest_adult				= 	$data->guest_adult;
										$obj->guest_child				= 	$data->guest_child;
										$obj->rooms						= 	$data->rooms;
										$obj->coupon_code				=	$data->coupon_code;
										
										$obj->first_name				=	$data->first_name;
										$obj->last_name					=	$data->last_name;
										$obj->details					=	$data->details;
										$obj->address					=	$data->address;
										$obj->postal_code				=	$data->postal_code;
										$obj->city						=	$data->city;
										$obj->tel						=	$data->tel;
										$obj->email						=	$data->email;
										$obj->conf_email				=	$data->conf_email;
										$obj->email_confirmation		= 	$data->Confirmation;
										$obj->reservation_status			=	$data->reservation_status;
										$obj->confirmation_payment_status	=	$data->confirmation_payment_status;		
										$obj->total						= 	$data->total;
										$obj->total_cost				= 	$data->total_cost;
										$obj->total_payed				= 	$data->total_payed;
										$obj->confirmation_payment_status = (round($data->total,3) == round($data->total_payed,3) ? PAYMENT_STATUS_PAYED : PAYMENT_STATUS_NOTPAYED);
										
										break;	
								}
								if( !isset($obj->confirmation_id) )
									continue;
								
								if (!$rowTable->bind($obj)) 
								{
									throw( new Exception($this->_db->getErrorMsg()) ); 
								}
								
								
								if (!$rowTable->check()) 
								{
									throw( new Exception($this->_db->getErrorMsg()) ); 
								}

								if (!$rowTable->store()) 
								{
									throw( new Exception($this->_db->getErrorMsg()) ); 
								}
								
								if( $i == 0 && $is_edit ==false )
								{
									$confirmation_id = $rowTable->confirmation_id;
								}
								else if( $i==2) //luam id-ul 
								{
									$obj_ids[$j]->daily[$k]['numbers'][$l]['confirmation_room_number_date_id'] =  $rowTable->confirmation_room_number_date_id;
								}
							}
						}
					}
				}
			}
			$this->storeGuestDetails($confirmation_id, $data);
		}
		catch( Exception $ex )
		{
			//dmp($ex);
			$answer_processor = $arr_answer[0];
			if( $is_transaction )
			{
				$db->setQuery(" ROLLBACK ");
				$db->query();
			}
			//exit;
			$this->logger_processor($rspProcessorPayment);
			if($is_frontend)
				JError::raiseWarning( 500, $ex->getMessage() ); 
			return false;
		}
		
		if( $is_transaction )
		{
			// $db->setQuery(" ROLLBACK ");
			$db->setQuery(" COMMIT ");
			$db->query();
		}
	
		return true;
	}
	
	function storeGuestDetails($confirmation_id, $data){
		
		if(isset($data->guest_first_name)){		
			$db =JFactory::getDBO();
			$query="delete from #__hotelreservation_confirmations_guests where confirmation_id=".$confirmation_id;
			$db->setQuery($query);
			if($db->query()){
				for($i=0;$i<count($data->guest_first_name);$i++){
					$rowTable	= 	 $this->getTable('ConfirmationsGuests');			
					$obj = new stdClass();
					$obj->confirmation_id = $confirmation_id;
					$obj->first_name = $data->guest_first_name[$i];
					$obj->last_name = $data->guest_last_name[$i];
					$obj->identification_number = $data->guest_identification_number[$i];
					
					if (!$rowTable->bind($obj))
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
	
					if (!$rowTable->check())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					if (!$rowTable->store())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
			}
		}
	}
	
	function logger_processor($rspProcessorPayment)
	{
		// dmp($rspProcessorPayment);
		if( count($rspProcessorPayment) > 0 )
		{
			$p_id 	= 0;
			$t	 	= '';
			$r	 	= '';
			$a		= '';
			$c		= '';
			$field  = null;
			foreach( $rspProcessorPayment as $key => $value )
			{
				switch( $key )
				{
					case 'paymentprocessor_id':
						$p_id = $value;
						break;
					case 'txn':
					case 'responce':
						if(!is_array($value) )
							$key=='txt'? $t = $value : $r = $value;
						else
						{
							foreach( $value as $k => $v )
							{
								if( $key=='txt') 
								{
									if( strlen($t) > 0 )
										$t .='\r\n';
									$t.= $k.' => '.$v;
								}
								else
								{
									if( strlen($r) > 0 )
										$r .='\r\n';
									$r.= $k.' => '.$v;
								}
							}
						}
						break;
					case 'answer':
						$a = $value;
						break;
					case 'code':
						$c = $value;
						break;
					default:
						continue;
				}
				
			}
			
			$sql_logg = " INSERT INTO #__hotelreservation_paymentprocessors_results
						(
							dataCrt,
							paymentprocessor_id,
							txn,
							responce,
							answer,
							code
						)
						VALUES
						(
							now(),
							$p_id,
							'".urlencode($t)."',
							'".urlencode($r)."',
							'".urlencode($a)."',
							'".urlencode($c)."'
						)
						";
			// dmp($sql_logg);
			$this->_db->setQuery( $sql_logg );
			$this->_db->query();
			//dmp($this->_db->getErrorMsg());
		}
	}
}