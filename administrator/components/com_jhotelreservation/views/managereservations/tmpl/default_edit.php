<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
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

	$percent 		= 0;
	$explication	= '';
	$payment_id		= 0;
	$skip_fields	= array(
								'state',
								'confirmation_id',
								'itemPackageNumbers',
								'itemRoomsCapacity',
								'itemArrivalOptions',
								'itemAirportTransferTypes',
								'itemAirlines',
								'option_ids',
								'package_ids',
								'package_day',
								'payments',
								'arrival_options_ids',
								'airline_id',
								'airport_transfer_type_id',
								'airport_transfer_date',
								'airport_transfer_time_hour',
								'airport_transfer_time_min',
								'airport_transfer_flight_nr',
								'airport_transfer_guest',
								'arrival_option_ids',
							);
	
	//dmp($this);
	/*if(count($this->item->room_ids) > 0 )
		$this->item->room_ids = explode(',', $this->item->room_ids);
	else
		$this->item->room_ids = array();
	
	if(count($this->item->option_ids) > 0 )
		$this->item->option_ids = explode(',', $this->item->option_ids);
	else
		$this->item->option_ids = array();
	
	if(count($this->item->package_ids) > 0 )
		$this->item->package_ids = explode(',', $this->item->package_ids);
	else
		$this->item->package_ids = array();

	if(count($this->item->arrival_option_ids) > 0 )
		$this->item->arrival_option_ids = explode(',', $this->item->arrival_option_ids);
	else
		$this->item->arrival_option_ids = array();
		*/

?>
<form autocomplete='off' action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option"				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" id ="task" name="task" 				value="" />
	<input type="hidden" name="payment_id" 			value="<?php echo $payment_id?>" />
	<input type="hidden" name="is_enable_payment" 	value="<?php echo $this->item->is_enable_payment?>" />
	<input type="hidden" name="is_penalty" 			value="1" />
	<input type="hidden" name="hotel_id" 			value="<?php echo $this->item->hotel_id ?>" />
	<input type="hidden" name="email" 				value="<?php echo $this->item->email ?>" />
	<input type="hidden" name="total" 				value="<?php echo $this->item->total ?>" />
	<input type="hidden" name="total_payed" 		value="<?php echo $this->item->total_payed ?>" />
	<input type="hidden" name="confirmation_id" 	value="<?php echo $this->item->confirmation_id ?>" />
	<input type="hidden" name="controller" 			value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<input type="hidden" name="view"			    value="managereservations" />
	<input type="hidden" id="guest_adult" name="guest_adult"			value="<?php echo $this->item->guest_adult ?>" />
	<input type="hidden" id="room_guests" name="room_guests"			value="<?php echo implode(",",$this->item->room_guests)?>" />
	<?php
		$this->item->displayHiddenValues( 'items_reserved', 				array('type'=>'value'));
		/*$this->item->displayHiddenValues( 'package_ids', 					array('type'=>'array'));
		$this->item->displayHiddenValues( 'package_day', 					array('type'=>'multiarray'));
		$this->item->displayHiddenValues( 'itemPackageNumbers',				array('type'=>'array'));
		$this->item->displayHiddenValues( 'arrival_option_ids', 			array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_airline_ids',			array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_type_ids', 		array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_dates', 		array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_time_hours', 	array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_time_mins', 	array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_flight_nrs', 	array('type'=>'array'));
		$this->item->displayHiddenValues( 'airport_transfer_guests', 		array('type'=>'array'));*/
		
	?>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_CONTACT',true); ?></legend>
			
			<TABLE class="admintable" align="left" border=0 >
				<TR>
					<TD width=10% nowrap><B><?php echo JText::_('LNG_COMPANY_NAME',true)?> :</B></TD>
					<TD nowrap width=90% align=left>
						<input type='text' id='company_name' name='company_name'
							value='<?php echo isset($this->item->company_name)?$this->item->company_name:'' ?>'
						>
					</TD>
				</TR>
				<TR>
					<TD width=10% nowrap><B><?php echo JText::_('LNG_FIRST_NAME',true)?> :</B></TD>
					<TD nowrap width=90% align=left>
						<input type='text' id='first_name' name='first_name'
							value='<?php echo $this->item->first_name?>'
						>
					</TD>
				</TR>
				<TR>
					<TD width=10% nowrap><B><?php echo JText::_('LNG_LAST_NAME',true)?> :</B></TD>
					<TD nowrap width=90% align=left>
						<input type='text' id='last_name' name='last_name'
							value='<?php echo $this->item->last_name?>'
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_BILLING_ADDRESS',true);?><span class="mand">*</span>
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'address'
							id				= 'address'
							autocomplete	= 'off'
							size			= 50
							value			= "<?php echo $this->item->address?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_POSTAL_CODE',true);?>
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'postal_code'
							id				= 'postal_code'
							autocomplete	= 'off'
							size			= 50
							value			= "<?php echo $this->item->postal_code?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_CITY',true);?>
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'city'
							id				= 'city'
							autocomplete	= 'off'
							size			= 50
							value			= "<?php echo $this->item->city?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_STATE',true);?>
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'state_name'
							id				= 'state_name'
							autocomplete	= 'off'
							size			= 50
							value			= "<?php echo $this->item->state_name?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_COUNTRY',true);?> 
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'country'
							id				= 'country'
							autocomplete	= 'off'
							size			= 30
							value			= "<?php echo $this->item->country?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_TELEPHONE_NUMBER',true);?>
					</TD>
					<TD colspan=2 align=left>
						<input 
							type 			= 'text'
							name			= 'tel'
							id				= 'tel'
							autocomplete	= 'off'
							size			= 50
							value			= "<?php echo $this->item->tel?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_EMAIL',true);?>
					</TD>
					<TD align=left>
						<input 
							type 			= 'text'
							name			= 'email'
							id				= 'email'
							autocomplete	= 'off'
							size			= 50
							readonly
							value			= "<?php echo $this->item->email?>"
						>
					</TD>
				</TR>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_EXTRA_INFO',true);?> 
					</TD>
					<TD colspan=2 align=left>
						<textarea name='details' id='details' autocomplete	= 'off' rows="3" cols="38" ><?php echo $this->item->details?></textarea>
					</TD>
				</tr>
			</table>
			
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_GUEST_INFORMATIONS',true); ?></legend>
			
			<TABLE class="admintable" align="left" border="0">
				<?php  
					$index = 0;
					for($t=1; $t<=count($this->item->items_reserved); $t++){
						
					?>
				<tr><td colspan="2"><strong><?php echo JText::_('LNG_ROOM',true).' '.$t ?></strong></td></tr>
				<tr><td><?php echo JText::_('LNG_GUESTS',true) ?> </td><td> <input type="text" name="guest_number" value="<?php echo $this->item->room_guests[$t-1]?>"/> <input type="button" onclick="updateGuestNumber()" value="Update"/> </td></tr>
					
				<?php 
				$index++;
				$value = $index+$this->item->room_guests[$t-1];
				//dmp($this->item->room_guests[$t-1]);
				//dmp($value);
				for($i=$index;$i<$value;$i++)
				{
					$index= $i;
					if( !isset($this->item->guest_first_name[$i-1]) )
						$this->item->guest_first_name[$i-1] = '';
					if( !isset($this->item->guest_last_name[$i-1]) )
						$this->item->guest_last_name[$i-1] = '';
					if( !isset($this->item->guest_identification_number[$i-1]) )
						$this->item->guest_identification_number[$i-1] = '';
				?>
				<tr style='background-color:##CCCCCC'>
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_GUEST_DETAILS',true);?> <span class="mand">*</span>
					</TD>
					<TD  align=left>
					 <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME',true);?></label>: 
						<input class="req-field" 
							type 			= 'text'
							name			= 'guest_first_name[]'
							id				= 'guest_first_name'
							
							size			= 25
							value			= "<?php echo $this->item->guest_first_name[$i-1]?>">
						
					<td>
						<label for="guest_last_name"><?php echo JText::_('LNG_LAST_NAME',true);?></label>: 
						<input  class="req-field"
							type 			= 'text'
							name			= 'guest_last_name[]'
							id				= 'guest_last_name'
							
							size			= 25
							value			= "<?php echo $this->item->guest_last_name[$i-1]?>">
					</TD>
				
					<td><label for="guest_identification_number"><?php echo JText::_('LNG_PASSPORT_NATIONAL_ID',true);?></label>: 
						<input class="req-field"
							type 			= 'text'
							name			= 'guest_identification_number[]'
							id				= 'guest_identification_number'
							
							size			= 25
							value			= "<?php echo $this->item->guest_identification_number[$i-1]?>">
					</td>
				</tr>
				<?php
				}
				}	
				?>
			</table>
			
	</fieldset>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_RESERVATION_DETAILS',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0 width=100% cellpadding=0 cellspacing=0>
		
		
			<tr>
				<td width=10% ><?php echo JText::_('LNG_ARIVAL',true)?></td>
				<td width=90% colspan="4" nowrap="nowrap" >
					<?php
					
						$datas			= date( "Y-m-d", mktime(0, 0, 0, $this->item->month_start, $this->item->day_start, $this->item->year_start ));
						$datae			= date( "Y-m-d", mktime(0, 0, 0, $this->item->month_end, $this->item->day_end, $this->item->year_end ));
						
						$datas =  JHotelUtil::convertToFormat($datas);
						$datae =  JHotelUtil::convertToFormat($datae);
						echo JHTML::calendar(
												$datas,'datas','datas',$this->item->itemAppSettings->calendarFormat, 
												array(
														'class'		=>'date_hotelreservation keyObserver inner-shadow', 
													)
											);
					?> 
				</td>
			</tr>
			<tr>
				<td width=10% ><?php echo JText::_('LNG_DEPARTURE',true)?></td>
				<td colspan=4 nowrap>
					<?php
						echo JHTML::calendar($datae,'datae','datae',$this->item->itemAppSettings->calendarFormat, array('class'=>'date_hotelreservation keyObserver inner-shadow'));

					?>
				</td>
			</tr>
		
		
			<TR><TD colspan=2><HR></TD></TR>
			
			<TR>
				
				<TD nowrap ALIGN=LEFT colspan="2">
					<?php 
						//dmp($this->item->items_reserved);
						for($t=1; $t<=count($this->item->items_reserved); $t++){ 
						echo "<br/>";	
						echo JText::_('LNG_ROOM_TYPE",true).' '.JText::_('LNG_FOR',true).' '.JText::_("LNG_ROOM',true).' '.$t; 
						echo "<br/>";
						echo "<br/>";
					?>
					
					<TABLE width="100%" valign="top" border="0" class="table_info rooms" cellspacing="0" >
							<?php
							$index = 0;
							$showRoomHeader = true;
							$noOfferFound = true;
							dmp($this->item->roomsAvailable);
							dmp($this->item->offersAvailable);
							foreach( $this->item->roomsAvailable as $value )
							{
// 								dmp($value);
								$daily = $value->daily;
								$price_per_person = 0;
								if(isset($daily[0]) && isset($daily[0]["discounts"]) && isset($daily[0]["discounts"][0])){
									if(isset( $daily[0]["discounts"][0]->offer_pers_price))
										$price_per_person= $daily[0]["discounts"][0]->offer_pers_price;
								}
								
								$room_capacity = $this->item->room_guests[$t-1];
								$hide_room = false;
								if($value->room_capacity< $room_capacity)
									$hide_room = true;
								
								if($price_per_person == 0)
									$price_per_person = $value->pers_price;
								
								
								//when searching with voucher only offer with searched voucher should be displayed
								if($this->item->voucher!=''){
									//dmp($value);
									$voucherFound = false;
									if(isset($value->vouchers) && count($value->vouchers)){
										foreach ($value->vouchers as $voucher){
											if( strcasecmp($voucher->voucher ,$this->item->voucher)==0){	
												$voucherFound = true;
											}
										}
									}
								 	if(!$voucherFound)
										continue;
								}

								//when searching without voucher, offers with voucher should not be visible
								if($this->item->voucher=='' && isset($value->vouchers) && count($value->vouchers)>0){
									continue;
								}
								
								if(!$value->front_display && $value->offer_id == 0){
									continue;
								}
								
								$noOfferFound = false;
								
								$grand_total = 0;
								foreach( $value->daily as $daily )
								{
									$p 		= $daily['display_price_final'];
									$grand_total += JHotelUtil::fmt($p,2);
								}
								
																
								if( $value->offer_id == 0 && $showRoomHeader){
									$showRoomHeader = false;
									?>
									<TR class="tr_header">
										<TH width=2%>&nbsp;</TH>
										<TH align="left"><?php echo JText::_('LNG_ROOMS',true)?> </TH>
										<TH width="10%">
											<?php if($value->offer_id > 0){?>
												<?php echo JText::_('LNG_MIN_NIGHTS',true)?>
											<?php } ?>
										</TH>
										
										<TH width=10%><?php echo JText::_('LNG_CAPACITY_PERS',true)?></TH>
										<TH width=10% align=right>
										<?php 
										if(JRequest::getVar( 'show_price_per_person')==1){ 
											echo JText::_('LNG_PRICE_PER_PERSON',true);
										}else{ 
											echo JText::_('LNG_PRICE',true);
										}
										?>
									    </TH>
										<TH align=right width="15%" ></TH> 
										<TH align=right width="15%" >&nbsp;</TH> 
									</tr>
									<?php 
								}else if($index ==0 ){
									?>
									<TR class="tr_header">
										<TH width="2%">&nbsp;</TH>
										<TH align="left">
											<?php echo JText::_('LNG_SPECIAL_OFFERS',true)?> 
										</TH>
										<TH width="10%">
											<?php if($value->offer_id > 0){?>
												<?php echo JText::_('LNG_MIN_NIGHTS',true)?>
											<?php } ?>
										</TH>
										<TH width="10%"><?php echo JText::_('LNG_CAPACITY_PERS',true)?></TH>
										<TH width="10%" align="right">
										<?php 
										if(JRequest::getVar( 'show_price_per_person')==1){ 
											echo JText::_('LNG_PRICE_PER_PERSON',true);
										}else{ 
											echo JText::_('LNG_PRICE',true);
										}
										?>
									    </TH>
										<TH align=right width="15%" ></TH> 
										<TH align=right width="15%" >&nbsp;</TH> 
									</tr>
									<?php 
																		
								}
									
								
							// dmp($value);
							?>
							<tr <?php echo $hide_room ?"style='display:none'":""; ?>>
								<td align=center>
									<input 
										type	= 'checkbox' 
										name	= 'room_ids[]'
										id		= 'room_ids[]'
										class	= "room_ids_<?php echo $t ?> "
										value	= '<?php echo $value->offer_id.'|'.$value->room_id?>'
										<?php echo in_array($value->offer_id.'|'.$value->room_id.'|'.$t, $this->item->items_reserved) ? " checked "  : " "?>
										onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
										onmouseout			=	"this.style.cursor='default'"
										style				= 	"display:block;"
									>
								</td>
								<td align=left nowrap   nowrap >
									<div class="trigger open">
										<div class="room_expand"></div>
										<?php echo
											$itemName ='';
											if($value->offer_id > 0){
												$itemName = $value->offer_name;
											}else{
												$itemName = $value->room_name;
											}
										?>
										<a href="#">
										<?php 
										if( strlen($itemName) > MAX_LENGTH_ROOM_NAME )	
										{
											
											?>
											<span 
												title			=	"<?php echo $itemName?>" 
												style			=	'cursor:hand;cursor:pointer'
												onmouseover		= 	"
																		var text = jQuery(this).attr('title');
																		var posi = jQuery(this).position();
																		var top  = posi.top;
																		var left = posi.left+5;
																		var wid	 = jQuery(this).width();
																		
																		jQuery(this).attr('title','');
																		jQuery(this).parent().append('<div class=\'poptext\'>'+text.replace('|','<br />')+'</div>');
																		jQuery('.poptext').attr('css','TOOLTIP_ROOM_NAME');
																		jQuery('.poptext').css(
																							{
																								'left':(left+wid)+'px',
																								'top'			:(top-jQuery('.poptext').height())+'px',
																								'display'		:'none',
																								'position'		:'absolute',
																								'z-index'		:'1000',
																								'padding'		:'5px',
																								'background-color': '#fff'
																							});
																		jQuery('.poptext').fadeIn('slow');
																	"
												onmouseout		= 	"
																		var title = jQuery(this).parent().find('.poptext').html();
																		jQuery(this).attr('title',title.replace('<br />','|'));
																		jQuery(this).parent().find('.poptext').fadeOut('slow');
																		jQuery(this).parent().find('.poptext').remove();
																		
																	"
											><?php echo substr($itemName, 0,MAX_LENGTH_ROOM_NAME); ?>...</span>
											<?php
										}
										else
											echo $itemName;
										?>
										</a>
										<?php 
											if($value->has_breakfast && $value->offer_id == 0){
												echo "(".JText::_('LNG_BREAKFAST_INCLUDED',true).")";
											}
										?>
										&nbsp;|&nbsp;
										<a style="display:none" class="link_more" href="#">&nbsp;<?php echo JText::_('LNG_MORE',true)?> »</a>
									</div>
								</td>
								<td align=center >
									<?php if($value->offer_id > 0){
										 echo $value->offer_min_nights; 
									 } ?>
								</td>
								<td align=center >
									<?php echo $value->room_capacity?>
									<input type='hidden' id='room_capacity_<?php echo $value->room_id?>' name='room_capacity_<?php echo $value->room_id?>' value='<?php echo $value->room_capacity ?>'>
								</td>
								<td align=right >
									<?php 
										if(!$value->is_disabled){
											if(JRequest::getVar( 'show_price_per_person')==1){
												$divider = $this->item->guest_adult > $value->room_capacity? $value->room_capacity:$this->item->guest_adult;
												echo JHotelUtil::fmt($grand_total / $divider,2);
											}else{ 
												echo JHotelUtil::fmt($value->room_average_display_price,2);
											}
										}
									?>
								</td>
								
								<td colspan=1 align=right style="padding-left:15px;" >
									<?php
									$is_checked = false;
									if( !isset($this->item->itemRoomsCapacity[$value->room_id]) )
										$this->item->itemRoomsCapacity[$value->room_id] = array(0,0);
									else
										$is_checked = $this->item->itemRoomsCapacity[$value->room_id][1]>0?true:false;
									?>
									
									<?php
									if( $value->offer_id  > 0 )
									{
										$cheie_offer_room 	= $value->offer_id."_".$value->room_id;
										$value_offer_room 	= $value->offer_id."|".$value->room_id;
													
										?>
										<input
											type	=	"hidden" 
											name	=	"items_reserved_tmp_<?php echo $cheie_offer_room?>" 				
											id		=	"items_reserved_tmp_<?php echo $cheie_offer_room?>" 					
											value	=	"<?php echo $value_offer_room?>|?" 
										/> 
		
										<?php
										foreach( $value->offer_detalii as $keyOffer => $valueOffer )
										{
											if( $keyOffer =='packages' )
											{
												foreach( $valueOffer as $vPackage )
												{
													//dmp($vPackage);
													?>
													<input 
														type	=	'hidden'
														name	=	'package_ids_tmp_<?php echo $cheie_offer_room?>' 
														id		=	'package_ids_tmp_<?php echo $cheie_offer_room?>' 
														value	=	'<?php echo $value_offer_room?>|?|<?php echo $vPackage->package_id?>'
													/>
													<input 
														type	=	'hidden'
														name	=	'itemPackageNumbers_tmp_<?php echo $cheie_offer_room?>' 
														id		=	'itemPackageNumbers_tmp_<?php echo $cheie_offer_room?>' 
														value	=	'<?php echo $value_offer_room?>|?|<?php echo $vPackage->package_id?>|<?php echo $value->room_capacity?>'
													/>
													<?php
													foreach( $vPackage->days as $d )
													{
													?>
														<input 
															type	=	'hidden'
															name	=	'package_day_tmp_<?php echo $cheie_offer_room?>' 
															id		=	'package_day_tmp_<?php echo $cheie_offer_room?>' 
															value	=	'<?php echo $value_offer_room?>|?|<?php echo $d?>'
														/>
													<?php
													}
												}
											}
											else if( $keyOffer =='arrival_options' )
											{
												foreach( $valueOffer as $vArrivalOption )
												{
												?>
												<input 
													type	=	'hidden'
													name	=	'arrival_option_ids_tmp_<?php echo $cheie_offer_room?>' 
													id		=	'arrival_option_ids_tmp_<?php echo $cheie_offer_room?>' 
													value	=	'<?php echo $value_offer_room?>|?|<?php echo $vArrivalOption->arrival_option_id?>|1'
												/>	
												<?php
												}
											
											}
										}
									}
									//echo $value->is_disabled;
									
									?>
									
									
									<input 
										id			= 'itemRoomsCapacity_RADIO' 
										name		= 'itemRoomsCapacity_RADIO_<?php echo $t?>[]' 
										type		= 'radio'
										<?php echo in_array($value->offer_id.'|'.$value->room_id.'|'.$t, $this->item->items_reserved) ? " checked "  : " "?>
										<?php echo ($value->is_disabled && !(in_array($value->offer_id.'|'.$value->room_id.'|'.$t, $this->item->items_reserved)))? 'disabled="disabled"':''; ?>
										onclick 	= 	'  
															var crtSelected		= jQuery("#itemRoomsCapacity_<?php echo $value->room_id?>").val();
															crtSelected	 		= crtSelected.split("|");
															var crtStatus 		= this.checked? 1 + parseInt(crtSelected[2]) : 0  + parseInt(crtSelected[2]) ;
															jQuery("#itemRoomsCapacity_<?php echo $value->room_id?>").val("<?php echo $value->room_id.'|'.$this->item->itemRoomsCapacity[$value->room_id][0].'|'?>"+crtStatus);
															var crtValue 	= "<?php echo $value->offer_id.'|'.$value->room_id.'|'.$this->item->itemRoomsCapacity[$value->room_id][0]?>";
															crtValue	 	= crtValue.split("|");
															setCheckboxRooms( 1, crtValue[0], crtValue[1],<?php echo $t ?>);
															return checkReserve(
																					<?php echo $value->offer_id?>, 
																					<?php echo $value->room_id?>, 
																					<?php echo $t ?>
																				);
														'
														
									/>
									
									
									<select 
										id		='itemRoomsCapacity_<?php echo $value->room_id?>' 
										name	='itemRoomsCapacity[]' 
										style	='width:60px;display:none'
										>
										
										<option 
											value='<?php echo $value->room_id.'|'.$this->item->itemRoomsCapacity[$value->room_id][0].'|1'?>'
											selected
										>
											1</option>
										
									</select>
									
								</td>
							</tr>
							
							
							<!--tr class="tr_cnt">
								<td class="td_cnt" colspan="7" >
									<div class="cnt">
										<div class="room-options">
											<table width=100% class="packages" >
												<TR>
													<TD valign=top colspan=6>
														<TABLE width=100% valign=top class="table_info" cellspacing="0">
															<TR class="tr_header">
																<TH width=2%>&nbsp;</TH>
																<TH width=25% ><?php echo JText::_('LNG_PACKAGES',true); ?></TH>
																<TH width=15% align=right><?php echo JText::_('LNG_PRICE',true)?>(<?php echo $this->item->currency_selector?>)</TH>
																<TH width=10%>&nbsp;</TH>
															</TR>
															<?php
															//dmp($this->itemPackages);
															$nrCrt 		= 0;
															foreach( $this->item->itemPackages as $package )
															{
																$is_checked	= false;
																$cheie_package = $value->offer_id.'|'.$value->room_id.'|'.$t.'|'.$package->package_id;
																
																	if( isset($this->item->itemPackageNumbers[$cheie_package] ) )
																		$is_checked = true;
																
															?>
															<TR>
																<TD align=center>
																	<input 
																		type	= 'checkbox' 
																		name	= 'package_ids[]'
																		id		= 'package_ids[]'
																		value	= '<?php echo $cheie_package?>'
																		<?php echo $is_checked? ' checked '  : '';?>
																		onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
																		onmouseout			=	"this.style.cursor='default'"
																		style				= 	'display:block'
																	>
																</TD>
																<TD align=right>
																	<?php echo ($package->is_price_day ? "<b><i>" : "").JHotelUtil::fmt($package->display_price_final,2).($package->is_price_day ? "</b></i>" : "")?> <?php echo $package->is_price_day ? "( ".JText::_('LNG_PER_DAY',true)." )  " : ""?>
																</TD>
																<TD nowrap align=right>
																	&nbsp;x&nbsp;
																	<select id='itemPackageNumbers' name='itemPackageNumbers[]' style='width:60px'
																		onchange = 	' 
																						var crtValue = this.value;
																						crtValue	 = crtValue.split("|");
																						setCheckboxPackage( 
																											crtValue[4], 
																											"<?php echo $this->item->reserve_offer_id.'|'.$this->item->reserve_room_id.'|'.$t.'|'?>" + crtValue[3]
																										);
																					'
																	>
																		<?php
																		if( !isset($this->item->itemPackageNumbers[$cheie_package]) )
																			$this->item->itemPackageNumbers[$cheie_package] 	
																														= array(
																																$this->item->reserve_offer_id,
																																$this->item->reserve_room_id,
																																$t,  
																																$package->package_id,
																																0,
																																0
																															);
																		
																		$max_package_number = $value->room_capacity;
																		for( $i=0; $i<=$max_package_number; $i++ )
																		{									
																		?>
																		<option 
																			value='<?php echo $i>0?($cheie_package.'|'.$i):''?>'
																			<?php echo $this->item->itemPackageNumbers[$cheie_package][4] ==$i ? " selected " : ""?>
																		>
																			<?php echo $i>0?$i : ''?>
																		</option>
																		<?php
																		
																		}
																		?>
																	</select>
																</TD>
															</TR>
															
															<?php
															$nrCrt ++;
															}
															?>
														</TABLE>
													</TD>
												</TR>
											</TABLE>
										</div>
										<div id="arrival options">
											
											<?php
											//dmp($this->item->itemArrivalOptions);
											if( count($this->item->itemArrivalOptions) > 0 )
											{
											?> 
												<table width="100%" class="arrival-options">
												<TR>
													<TD valign=top>
														<div class="header_line">
															<strong><?php echo JText::_('LNG_ARRIVAL_OPTIONS',true); ?></strong>
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														</div>
														<div class='div_arrival_option_info' ><?php echo JText::_('LNG_INFO_ARRIVAL_OPTIONS',true)?></div>
														<TABLE width=100% valign=top class='table_arrival_options'>
															<?php
															// dmp($this->item->itemArrivalOptions);
															foreach( $this->item->itemArrivalOptions as $arrivalOption )
															{
																$cheie_arrival_option 	= $value->offer_id.'|'.$value->room_id.'|'.$index.'|'.$arrivalOption->arrival_option_id;
																$is_checked				= false;
																
																if( 
																	isset( $this->item->arrival_option_ids[ $cheie_arrival_option ] ) 
																	&& 
																	$this->item->arrival_option_ids[ $cheie_arrival_option ][4] == 1
																)
																{
																	$is_checked = true;
																}
								
															?>
															<tr>
																<TD width=5%  align=right>
																	<input 
																		type	='checkbox'
																		name	='arrival_option_ids[]'
																		id		='arrival_option_ids[]'
																		value	= '<?php echo $cheie_arrival_option?>|1'
																		<?php echo $is_checked ? " checked " : ""?>
																	>
																</TD>
																<TD width=80%  align=left>
																	<?php echo $arrivalOption->arrival_option_name ?>
																</TD>
																<TD width=15%  align=right>
																	<?php echo $arrivalOption->arrival_option_display_price ?>(<?php echo $this->item->currency_selector?>)
																</TD>
															</TR>
															<?php
															}
															?>
														</TABLE>
													</TD>
												</TR>
											</table>
											<?php } ?>
										</div>
									</div>
								</td>
							</tr-->
							<?php
								$index++;
							}
							?>
						</TABLE>
					<?php } ?>
				</TD>
			</TR>
			<TR><TD colspan=2><HR></TD></TR>
			<tr>
			<td>
			
				</td>
			</TR>
		</TABLE>
	</fieldset>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_AIRPORT_TRANSFER',true); ?></legend>
		<TABLE width=100%>
			<TR>
				<TD valign=top>
					<input 
						type	='checkbox'
						name	='is_airport_transfers'
						id		='is_airport_transfers'
						<?php echo $this->item->airport_transfer_type_id> 0 ? " checked " : ""?>
						onclick	= "
									if( this.checked == false )
									{
										jQuery('#airport_transfer_type_id').val( '0' );
										jQuery('#airport_transfer_time_hour').val('-1');
										jQuery('#airport_transfer_time_min').val('-1');
										jQuery('#airport_transfer_flight_nr').val('');
										jQuery('#airport_transfer_guest').val('');
										jQuery('#airport_transfer_date').val('');
										jQuery('#airline_id').val('0');
										jQuery('#div_airport_transfer').hide(100);
										jQuery('#div_airport_transfer_type_price').html( '' );
									}
									else
									{
										jQuery('#div_airport_transfer').show(100);
									}
						"
						<?php echo JText::_('LNG_AIRPORT_TRANSFER_INFO',true)?>
					>
					<div id='div_airport_transfer' 
						<?php echo $this->item->airport_transfer_type_id == 0 ? " style='display:none' " : ""?>
					>
					<TABLE width=100% valign=top class='table_arrival_options'>
						<TR>
							<TD colspan=4 align=left  style="padding-top:10px;padding-bottom:10px;">	
								-<?php echo JText::_('LNG_FIELDS_MARKED_WITH',true);?>
								<span class="mand">*</span>
								<?php echo JText::_('LNG_ARE_MANDATORY',true);?>-
							</TD>
						</tr>
						<tr>
							<TD width=20% nowrap align=left>
								<?php echo JText::_('LNG_TRANSFER_TYPE',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=80%  colspan=3 align=left>
								<select
									name	= 'airport_transfer_type_id'
									id		= 'airport_transfer_type_id'
									style	= 'width:600px'
									onchange= 	"
													if(this.value == 0 )
													{
														jQuery('#div_airport_transfer_type_price').html( '' );
													}
													else
													{
														<?php
														foreach( $this->item->itemAirportTransferTypes as $valueAirportTransferType )
														{
															?>
															if( this.value == '<?php echo $valueAirportTransferType->airport_transfer_type_id?>')
															{
																<?php
																$pr = 	$valueAirportTransferType->airport_transfer_type_price.
																		($valueAirportTransferType->airport_transfer_type_vat !=0 ? (" + ".$valueAirportTransferType->airport_transfer_type_vat." %".JText::_('LNG_VAT',true)) : "");
										
																?>																
																jQuery('#div_airport_transfer_type_price').html( '<?php echo $pr?>' );
															}
															<?php
														}
														?>
													}
												"
								>
									<option
										value = '0'
										<?php echo 0 == $this->item->airport_transfer_type_id ? " selected " : "" ?>
									>
									</option>
									
									<?php
									foreach( $this->item->itemAirportTransferTypes as $valueAirportTransferType )
									{
									?>
									<option
										<?php echo $valueAirportTransferType->airport_transfer_type_id == $this->item->airport_transfer_type_id? " selected " : "" ?>
										value ='<?php echo $valueAirportTransferType->airport_transfer_type_id ?>'
									>
										<?php echo $valueAirportTransferType->airport_transfer_type_name?> 
										( <?php echo $valueAirportTransferType->airport_transfer_type_price?> )
										<?php echo ($valueAirportTransferType->airport_transfer_type_vat !=0 ? (" + ".$valueAirportTransferType->airport_transfer_type_vat." %".JText::_('LNG_VAT',true)) : "&nbsp;")?>
										<?php echo " | ".$valueAirportTransferType->airport_transfer_type_description?> 	
									</option>
									<?php
									}									
									?>
								</select>
							</TD>
						</TR>
						<TR>
							<TD>
								<?php echo JText::_('LNG_PRICE',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<div id='div_airport_transfer_type_price' name='div_airport_transfer_type_price'></div>
							</TD>
							<TD nowrap>
								<?php echo JText::_('LNG_DATE',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<?php
								echo JHTML::calendar($this->item->airport_transfer_date,'airport_transfer_date','airport_transfer_date','%Y-%m-%d', array('style'=>'text-align:center;width:80px'));

							?>

							</TD>
						</TR>
						<TR>
							<TD nowrap>
								<?php echo JText::_('LNG_AIRLINE',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<select
									id		= 'airline_id'
									name	= 'airline_id'
									style	= 'width:230px'
								>
									<option
										value = '0'
										<?php echo 0== $this->item->airline_id? " selected " : "" ?>
									></option>
									<?php
									if(isset($this->item->itemAirlines))
									foreach( $this->item->itemAirlines as $valueAirlines )
									{
									?>
									<option
										value ='<?php echo $valueAirlines->airline_id ?>'
										<?php echo $valueAirlines->airline_id == $this->item->airline_id? " selected " : "" ?>
									>
										<?php echo $valueAirlines->airline_name?> 
									</option>
									<?php
									}
									?>
								</select>
							</TD>
							<TD nowrap>
								<?php echo JText::_('LNG_TIME',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<select
									id		='airport_transfer_time_hour'
									name	='airport_transfer_time_hour'
								>
									<option value='-1' <?php echo $this->item->airport_transfer_time_hour==-1? " selected" : ""?>></option>
									<?php
									for($i=0;$i<=23;$i++ )
									{
									?>
									<option <?php echo $this->item->airport_transfer_time_hour==$i? " selected" : ""?>>
										<?php echo $i>9? $i : "0$i"?>
									</option>
									<?php
									}
									?>
								</select>
								:
								<select
									id		='airport_transfer_time_min'
									name	='airport_transfer_time_min'
								>
									<option value='-1' <?php echo $this->item->airport_transfer_time_min==-1? " selected" : ""?>></option>
									<?php
									for($i=0;$i<=59;$i++ )
									{
									?>
									<option <?php echo $this->item->airport_transfer_time_min==$i? " selected" : ""?>>
										<?php echo $i>9? $i : "0$i"?>
									</option>
									<?php
									}
									?>
								</select>
							</TD>
						</TR>
						<TR>
							<TD nowrap>
								<?php echo JText::_('LNG_FLIGHT_NR',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<input 
									type	='text'
									name	='airport_transfer_flight_nr'
									id		='airport_transfer_flight_nr'
									value	='<?php echo $this->item->airport_transfer_flight_nr?>'
									
								>
								<?php echo JText::_('LNG_FLIGHT_NR_SAMPLE',true)?>
							</TD>
							<TD>
								<?php echo JText::_('LNG_GUEST',true)?> :
								<span class="mand">*</span>
							</TD>
							<TD width=40%>
								<input 
									type	='text'
									name	='airport_transfer_guest'
									id		='airport_transfer_guest'
									value	='<?php echo $this->item->airport_transfer_guest?>'
									
								>
							</TD>
						</TR>
					</TABLE>
				</TD>
			</TR>
		</table>
	</fieldset>

	<?php echo JHTML::_( 'form.token' ); ?> 
	<script>
		<?php
		if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
		{
		?>
		function submitbutton(pressbutton) 
		<?php
		}
		else
		{
		?>
		Joomla.submitbutton = function(pressbutton) 
		<?php
		}
		?>
		{
			var form 	= document.forms['adminForm'];
			if( pressbutton == 'cancel' )
			{
				form.submit();
				return true;
			}
			var is_ok	= false;
			if( form.elements['is_airport_transfers'].checked == false )
			{
				
			}
			else
			{
				if( form.elements['airport_transfer_type_id'].value =='0' )
				{
					alert("<?php echo JText::_('LNG_PLEASE_SELECT_AIRPORT_TRANSFER_TYPE',true);?>");
					return false
				}
				else if( !validateField( form.elements['airport_transfer_date'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_DATE',true);?>" ) )
				{
					return false;
				}
				else if( form.elements['airline_id'].value =='0' )
				{
					alert("<?php echo JText::_('LNG_PLEASE_SELECT_AIRPORT_AIRLINE',true);?>");
					return false
				}
				else if( form.elements['airport_transfer_time_hour'].value =='-1' )
				{
					alert("<?php echo JText::_('LNG_PLEASE_SELECT_AIRPORT_TRANSFER_HOUR',true);?>");
					return false
				}
				else if( form.elements['airport_transfer_time_min'].value =='-1' )
				{
					alert("<?php echo JText::_('LNG_PLEASE_SELECT_AIRPORT_TRANSFER_MIN',true);?>");
					return false
				}
				else if( !validateField( form.elements['airport_transfer_flight_nr'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_FLIGHT_NR',true);?>" ) )
				{
					return false;
				}
				else if( !validateField( form.elements['airport_transfer_guest'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_GUEST',true);?>" ) )
				{
					return false;
				}
			}
			form.elements['task'].value					= "save";
			form.submit();
			return true;
		}


		jQuery(document).ready(function(){
			if(jQuery('.trigger').length > 0) 
			{
				jQuery('.trigger').click(function() 
				{
					if (jQuery(this).hasClass('open')) 
					{
						jQuery(this).removeClass('open');
						jQuery(this).addClass('close');
						jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideDown(100);
						jQuery(this).children('.room_expand').addClass('expanded');
						jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_LESS',true)?> »');
						return false;
					} else {
						jQuery(this).removeClass('close');
						jQuery(this).addClass('open');
						jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(100);
						jQuery(this).children('.room_expand').removeClass('expanded');
						jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_MORE',true)?> »');
						return false;
					}			
				});
			}
		});		

		function updateGuestNumber(){
			var value="";
			jQuery("input[name=guest_number]").each(function(){
				
				value+=this.value+",";
			});
			value = value.slice(0, -1);
			//console.log(value);
			jQuery("#room_guests").val(value);
			jQuery("#task").val("edit");
			jQuery("#adminForm").submit();
		}

		function checkReserve( offer_id, room_id, current )
		{
			
			var crtValue 	= offer_id+"|"+room_id+"|"+current;
			
			jQuery('<input>').attr({
				type		: 'hidden',
				id			: 'items_reserved',
				class		: 'item_reserved'+current,
				name		: 'items_reserved[]',
				value		:  crtValue
			}).appendTo(jQuery('#adminForm'));

			
		}

		function setCheckboxRooms( nr_crt, offer_id, room_id, current )
		{

		
			var elements = jQuery('.room_ids_'+current);
			
			var form 	= document.forms['adminForm'];
			//alert("current: "+current);
			var len = form.elements["room_ids[]"].length;
			//alert(len);
			for( i = 0; i < len; i++ )
			{
				var elementName = "itemRoomsCapacity_RADIO_"+current+"[]";
				if(form.elements[elementName][i]){
					if(!form.elements[elementName][i].checked){
						var val = form.elements["room_ids[]"][i].value+"|"+current;
						jQuery('input[value=\"'+val+'\"]').remove();
						elements[i].checked = false;
					}
					else{
						elements[i].checked = true;
					}
				}
			}
			//alert(9);
		}

		function setCheckboxPackage( nr_crt, ch_id )
		{
			var form 	= document.forms['adminForm'];
			if( form.elements["package_ids[]"].type =="checkbox" )
			{
				if( form.elements["package_ids[]"].value == ch_id )
				{
					form.elements["package_ids[]"].checked = (nr_crt>0? true : false);
				}
			}
			else 
			{
				var len = form.elements["package_ids[]"].length;
				
				for( i = 0; i < len; i++ )
				{
					if( form.elements["package_ids[]"][i].type =="checkbox" )
					{
						if( form.elements["package_ids[]"][i].value != ch_id )
							continue;
						
						form.elements["package_ids[]"][i].checked = (nr_crt>0? true : false);
						break;
					}
				}
			}
		
			alterDailyPackage( ch_id,(nr_crt>0? true : false) );
		}
		//###########################################
		
		function calcPackageValue(id)
		{
			var form 	= document.forms['adminForm'];
			var v 		= 0.00;
			if( form.elements['package_day_' + id+"[]"] )
			{
				if( form.elements['package_day_' + id+"[]"].type =="checkbox")
				{
					if( form.elements['package_day_' + id+"[]"].checked )
						v = v + parseFloat(form.elements['package_day_' + id+"[]"].title);
				}
				else 
				{
					var len = form.elements['package_day_' + id + "[]"].length;
					for( i = 0; i < len; i++ )
					{	
						if( form.elements['package_day_' + id + "[]"][i].checked )
						{
							v = v + parseFloat(form.elements['package_day_' + id+"[]"][i].title);
						}
					}
				}
				var formated_value = v.toFixed( 2 );
				if( form.elements['package_grand_total_'+id ] )
					form.elements['package_grand_total_'+id ].value = formated_value;
			}
			//alert(9);
		}
		
		function alterDailyPackage( id, isChecked )
		{
			var form 		= document.forms['adminForm'];
			var v 			= 0.00;
			
			if( form.elements['package_day_' + id+"[]"] )
			{
				if( form.elements['package_day_' + id+"[]"].type =="checkbox")
				{
					form.elements['package_day_' + id+"[]"].checked = isChecked ;
					if( form.elements['package_day_' + id+"[]"].checked )
						v = v + parseFloat(form.elements['package_day_' + id+"[]"].title);
				}
				else 
				{
					var len = form.elements['package_day_' + id + "[]"].length;
					for( i = 0; i < len; i++ )
					{	
						form.elements['package_day_' + id + "[]"][i].checked = isChecked ;
						if( form.elements['package_day_' + id + "[]"][i].checked )
						{
							v = v + parseFloat(form.elements['package_day_' + id+"[]"][i].title);
						}
					}
				}
				var formated_value = v.toFixed( 2 );
				if( form.elements['package_grand_total_'+id ] )
					form.elements['package_grand_total_'+id ].value = formated_value;
			}
			//alert(9);
		}
		</script>
</form>


