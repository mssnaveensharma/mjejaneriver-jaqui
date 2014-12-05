<fieldset class="adminform">
					<legend><?php echo JText::_( 'LNG_WIZARD_OFFER_PACKAGES' ,true); ?></legend>
					<TABLE width=100% class="admintable" align=center border=0 id='table_offer_rooms' name='table_offer_rooms' 	>
						<TR >
							<TD width=15% nowrap><?php echo JText::_( 'LNG_SELECT_ROOM' ,true); ?> :</TD>
							<TD width=40% nowrap align=left valign=top>
								<select class='room_details_id' id='room_details_id' name='room_details_id'  style="width:300px">
									<option value='0'></option>
								<?php
								foreach( $this->item->itemRooms as $value )
								{
									if( $value->is_sel == 0 )
										continue;
								?>
								<option 
									value='<?php echo $value->room_id?>'
									<?php echo $value->is_sel? " selected" : ""?>
								><?php echo $value->room_name?></option>
								<?php
								}
								?>
								</select>
							</TD>
							<TD width=45% nowrap align=left>
								<?php /*
								foreach( $this->item->itemRooms as $value )
								{
								?>
								<div 
									id		='div_info_room_price_<?php echo $value->room_id?>'
									name	='div_info_room_price_<?php echo $value->room_id?>'
									style	='display:none;text-align:left'
								>
									<TABLE>		
										<TR>
										<?php
										switch( $value->type_price )
										{
											case 0:
												?>
												<TD align=center ><?php echo JText::_('LNG_MON',true)?> <br/> <?php echo $value->room_price_1?></TD>
												<TD align=center ><?php echo JText::_('LNG_TUE',true)?> <br/> <?php echo $value->room_price_2?></TD>
												<TD align=center ><?php echo JText::_('LNG_WED',true)?> <br/> <?php echo $value->room_price_3?></TD>
												<TD align=center ><?php echo JText::_('LNG_THU',true)?> <br/> <?php echo $value->room_price_4?></TD>
												<TD align=center ><?php echo JText::_('LNG_FRI',true)?> <br/> <?php echo $value->room_price_5?></TD>
												<TD align=center ><?php echo JText::_('LNG_SAT',true)?> <br/> <?php echo $value->room_price_6?></TD>
												<TD align=center ><?php echo JText::_('LNG_SUN',true)?> <br/> <?php echo $value->room_price_7?></TD>
												<TD align=center align=center>(<?php echo JText::_('LNG_DAY_BY_DAY',true)?>)</TD>
												<?php
												break;
											case 1:
												?>
												<TD align=center ><?php echo $value->room_price?></TD>
												<TD align=center align=center>(<?php echo JText::_('LNG_SAME_EVERY_DAY',true)?>)</TD>
												<?php
												break;
											case 2:
												?>
												<TD align=center ><?php echo JText::_('LNG_STR_MIDWEEK',true)?> : <?php echo $value->room_price_midweek?></TD>
												<TD align=center ><?php echo JText::_('LNG_STR_WEEKEND',true)?> : <?php echo $value->room_price_weekend?></TD>
												<TD align=center align=center>(<?php echo JText::_('LNG_MIDDWEEK_WEEKEND',true)?>)</TD>
												
												<?php
												break;
										} 
										?>
										</tr>
									</table>
								</div>
								<?php
								} */
								?>
							</TD>
						</TR>
					</TABLE>
					<hr/>
					<div style='display:none' class='div_offer_discounts' id='div_offer_discounts' name='div_offer_discounts'>
						
							<?php
							//verificam daca avem discounturi fara always show
							$is_select_discount_show = false;
							//dmp( $this->item->itemRooms);
							
							foreach( $this->item->itemRooms as $valueRoom )
							{
										$is_first = true;
										//move on top all prices
										$id_price 					= 1;
										$id_week_day 				= 2;
										$id_midweek_weekend_day	 	= 3;
										$is_disabled = false;
							?>
							
						    <div id="div_info_offer_price_<?php echo $valueRoom->room_id?>">
								<TABLE width=100% class="admintable" align=center border=0 id='table_offer_type_discounts' name='table_offer_type_discounts' >
									<input type ="hidden" name="offer_room_rate_id_<?php echo $valueRoom->room_id?>" value="<?php echo $valueRoom->discounts->id ?>" />
									<tr>
										<td class="key"><?php echo JText::_('LNG_PRICE_TYPE',true); ?></td>
										<td colspan=3 >
											<div id="price_type" class="offer-price-type">
												<input 
													type		= "radio"
													name		= "price_type_<?php echo $valueRoom->room_id?>"
													id			= "price_type_<?php echo $valueRoom->room_id?>"
													onclick 	= "updateStatus()"
													value		= "1" <?php echo $valueRoom->discounts->price_type==1? "checked" :""?>/>
												<?php echo JText::_( 'LNG_PER_PERSON' ,true); ?>
												&nbsp;
												<input 
													type		= "radio"
													name		= "price_type_<?php echo $valueRoom->room_id?>"
													id			= "price_type_<?php echo $valueRoom->room_id?>"
													onclick 	= "updateStatus()"
													value		= "0"	<?php echo $valueRoom->discounts->price_type==0? "checked" :""?>/>
												<?php echo JText::_( 'LNG_PER_ROOM' ,true); ?>
											</div>
											<div id="price_type_day" class="offer-price-type">
												<input 
													type		= "radio"
													name		= "price_type_day_<?php echo $valueRoom->room_id?>"
													id			= "price_type_day_<?php echo $valueRoom->room_id?>"
													value		= "0" <?php echo isset($valueRoom->discounts->price_type_day) && $valueRoom->discounts->price_type_day==0? "checked" :""?>/>
												<?php echo JText::_('LNG_PER_DAY' ,true); ?>
												&nbsp;
												<input 
													type		= "radio"
													name		= "price_type_day_<?php echo $valueRoom->room_id?>"
													id			= "price_type_day_<?php echo $valueRoom->room_id?>"
													value		= "1" <?php echo isset($valueRoom->discounts->price_type_day) && $valueRoom->discounts->price_type_day==1? "checked" :""?>/>
												<?php echo JText::_('LNG_PER_OFFER' ,true); ?>
												
											</div>
										</td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_( 'LNG_PRICE' ,true); ?></td>
										<td colspan="3">
											<div id="div_offer_room_price">
													<TABLE class='table_type_discounts_ex' cellpadding=0 cellspacing=0  style='text-align:left;border-bottom:solid 1px black'>
														<TR>
															<?php
															for( $day=1;$day<=7;$day++)
															{
															?>
															<TD align="left" style="padding-right:10px">
																
																<?php 
																switch( $day )
																{
																	case 1:
																		echo JText::_('LNG_MON',true);
																		break;
																	case 2:
																		echo JText::_('LNG_TUE',true);
																		break;
																	case 3:
																		echo JText::_('LNG_WED',true);
																		break;
																	case 4:
																		echo JText::_('LNG_THU',true);
																		break;
																	case 5:
																		echo JText::_('LNG_FRI',true);
																		break;
																	case 6:
																		echo JText::_('LNG_SAT',true);
																		break;
																	case 7:
																		echo JText::_('LNG_SUN',true);
																		break;
																}
																?>
															</TD>
															<?php
															}
															?>
														</TR>
														<TR>
															<?php
															// dmp($disc_room);
															for( $day=1;$day<=7;$day++)
															{
																$price_string = "price_".$day;
															?>
															<TD nowrap align=center
																
															>
	
																<input 
																	type	='input' 
																	id		='week_day_<?php echo $valueRoom->room_id?>[]'
																	name	='week_day_<?php echo $valueRoom->room_id?>[]'
																	size	= 8
																	value 	= "<?php echo isset($valueRoom->discounts->$price_string) ? $valueRoom->discounts->$price_string  : ""?>" 
																	class	="offer-price-days<?php echo $valueRoom->room_id?>"
																	<?php echo $is_disabled ? " disabled " : "  "?>
																	autocomplete = "OFF"
																/>
																
															</TD>
															<?php
															}
															?>
														</TR>
												</TABLE>
												
											</div>
											<div id="custom-rates">
													<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&layout=edit&view=offerrateprices&offer_id='.$this->item->offer_id.'&rate_id='.$valueRoom->discounts->id.'&hotel_id='.$this->item->hotel_id); ?>"><?php echo JText::_( 'LNG_OFFER_RATE_PRICES_TEXT' ,true); ?></a>
												</div>
									</tr>	
									<?php if($this->appSettings->show_children!=0){ ?>
									<tr>
										<td class="key"><?php echo JText::_('LNG_CHILD_PRICE',true)?></td>
										<td>
											<input 
													type	='input' 
													id		='child_price_<?php echo $valueRoom->room_id?>'
													name	='child_price_<?php echo $valueRoom->room_id?>'
													value	='<?php echo $valueRoom->discounts->child_price ?>'
													<?php echo $is_disabled ? " disabled " : "  "?>
													autocomplete = "OFF"
												/>
												
										 </td>
									</tr>	 	
									<?php } ?>
									<tr>
										<td class="key">
											<div id="single-supplement-container-<?php echo $valueRoom->room_id?>">
												<?php echo JText::_('LNG_SINGLE_SUPPLEMENT',true)?>
											</div>
											<div id="single-discount-container-<?php echo $valueRoom->room_id?>">
												<?php echo JText::_('LNG_SINGLE_DISCOUNT',true)?>
											</div>
										</td>
										<td>
											<input  
													type	='input' 
													id		='single_balancing_<?php echo $valueRoom->room_id?>'
													name	='single_balancing_<?php echo $valueRoom->room_id?>'
													value	='<?php echo $valueRoom->discounts->single_balancing ?>'
													<?php echo $is_disabled ? " disabled " : "  "?>
													autocomplete = "OFF"
												/>
												
										 </td>
									</tr>		
									<tr>
										<td class="key">
											<?php echo JText::_('LNG_EXTRA_NIGHT_PRICE',true)?>
										</td>
										<td>
											<input 
												type		= "text"
												name		= "extra_night_price_<?php echo $valueRoom->room_id?>"
												id			= "extra_night_price_<?php echo $valueRoom->room_id?>"
												value		= '<?php echo $valueRoom->discounts->extra_night_price ?>'
												size		= 10
												maxlength	= 10
												style		= 'text-align:right'
											/>
										</td>
									</tr>		
									<tr>
										<td class="key">
											<?php echo JText::_('LNG_EXTRA_PERSON_PRICE',true)?>
										</td>
										<td>
											<input 
												type		= "text"
												name		= "extra_pers_price_<?php echo $valueRoom->room_id?>"
												id			= "extra_pers_price_<?php echo $valueRoom->room_id?>"
												value		= '<?php echo $valueRoom->discounts->extra_pers_price ?>'
												size		= 10
												maxlength	= 10	
												style		= 'text-align:right'
											/>
										</td>
									</tr>		
									<tr>
										<td class="key">
											<?php echo JText::_('LNG_BASE_ADULTS',true)?> (<?php echo JText::_('LNG_MAX',true).' '.$valueRoom->max_adults?>)
										</td>
										<td>
											<input 
												type		= "text"
												name		= "base_adults_<?php echo $valueRoom->room_id?>"
												id			= "base_adults_<?php echo $valueRoom->room_id?>"
												value		= '<?php echo $valueRoom->discounts->base_adults ?>'
												size		= 10
												maxlength	= 10	
												style		= 'text-align:right'
											/>
										</td>
										<td rowspan="3" align="left">
											 <div style="border:1px solid #ccc;width:300px;padding:10px;"><?php echo JText::_('LNG_CUSTOM_RATES_NOTICE',true)?></div>
										</td> 
										
									</tr>	
									<?php if($this->appSettings->show_children!=0){ ?>
									<tr style="">
										<td class="key">
											<?php echo JText::_('LNG_BASE_CHILDREN',true)?> (<?php echo JText::_('LNG_MAX',true).' '.$valueRoom->max_children?>)
										</td>
										<td>
											<input 
												type		= "text"
												name		= "base_children_<?php echo $valueRoom->room_id?>"
												id			= "base_children_<?php echo $valueRoom->room_id?>"
												value		= '<?php echo $valueRoom->discounts->base_children ?>'
												size		= 10
												maxlength	= 10	
												style		= 'text-align:right'
											/>
										</td>
									</tr>
									<?php } ?>
											 													
									</TABLE>
										
									</div>
								<?php
									}
								?>
						
					</div>
				</fieldset>