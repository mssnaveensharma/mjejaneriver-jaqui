<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2015 CMSJunkie. All rights reserved.
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

?>
<div class="table-responsive">
<TABLE width="100%" valign="top" border="0" class="table_info rooms responsive-utilities" cellspacing="0" >
	
		<TR class="tr_header row">
				<TH width="2%">&nbsp;</TH>
				<TH align="left">
					<?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_SPECIAL_OFFERS_PARK',true) : JText::_('LNG_SPECIAL_OFFERS',true)?> 
				</TH>
				<TH class="hidden-xs hidden-phone"  width="10%">
					<?php echo JText::_('LNG_MIN_NIGHTS',true)?>
				</TH>
				<TH class="hidden-xs hidden-phone" width="14%"><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_CAPACITY_PERS_PARK',true) : JText::_('LNG_CAPACITY_PERS',true)?></TH>
				<?php if($this->appSettings->show_children!=0){ ?>
					<TH width="14%" class="hidden-xs hidden-phone"><?php echo JText::_('LNG_CHILDREN')?></TH>
				<?php } ?>
				<TH width="6%" align="right">
				<?php 
				if($this->appSettings->show_price_per_person ==1){ 
					echo JText::_('LNG_PRICE_PER_PERSON',true);
				}else{ 
					echo JText::_('LNG_PRICE',true);
				}
				?>
			    </TH>
				<TH align=right width="15%" class="hidden-phone"></TH> 
				<TH align=right width="15%" >&nbsp;</TH> 
			</tr>
		<?php
$index = 0;
$noOfferFound = true;
foreach( $this->offers as $offer )
{
	$price_per_person = 0;
	if(isset($daily[0]) && isset($daily[0]["discounts"]) && isset($daily[0]["discounts"][0])){
		if(isset( $daily[0]["discounts"][0]->offer_pers_price))
			$price_per_person= $daily[0]["discounts"][0]->offer_pers_price;
	}
	
	if($price_per_person == 0)
		$price_per_person = $offer->price_type;
	
	//dmp($offer->public);
	//when searching with voucher only offer with searched voucher should be displayed
	if($this->userData->voucher!=''){
		//dmp($offer);
		$voucherFound = false;
		if(isset($offer->vouchers) && count($offer->vouchers)){
			foreach ($offer->vouchers as $voucher){
				//dmp($voucher);
				if( strcasecmp($voucher ,$this->userData->voucher)==0){	
					$voucherFound = true;
				}
			}
		}
	 	if(!$voucherFound)
			continue;
	}

	//when searching without voucher, offers with voucher should not be visible
	if(($this->userData->voucher=='' && isset($offer->vouchers) && count($offer->vouchers)>0) && !$offer->public){
		continue;
	}

	$capacityExceeded = false;
	$capacityFullfilled= true;
	
	if(isset($userData->roomGuests) & $this->userData->adults > $offer->max_adults){
		$capacityExceeded = true;
	}else if(!isset($userData->roomGuests) && $offer->max_adults < $this->userData->adults){
		$capacityExceeded = true;
	}
	else if($offer->offer_min_pers > ($this->userData->adults+$this->userData->children)){
		$capacityFullfilled = false;
	}else if(($this->appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($this->userData->reservedItems)]) && $userData->roomGuestsChildren[count($this->userData->reservedItems)] > $offer->base_children)){
		$capacityExceeded = true;
	}
	else if(($this->appSettings->show_children) && !isset($userData->roomGuestsChildren) && ($offer->base_children < $this->userData->children)){
		$capacityExceeded = true;
	} 

	$grand_total = 0;
	foreach( $offer->daily as $daily )
	{
		$p 		= $daily['display_price_final'];
		$grand_total += $p;
	}
	
	$index++;
		
	// dmp($offer);
	?>
	<tr class="row">
		<td align=center>
			<input 
				type	= 'checkbox' 
				name	= 'room_ids[]'
				id		= 'room_ids[]'
				value	= '<?php echo $offer->offer_id.'|'.$offer->room_id?>'
				<?php //echo in_array($offer->offer_id.'|'.$offer->room_id, $this->userData->room_ids) ? " checked "  : " "?>
				onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
				onmouseout			=	"this.style.cursor='default'"
				style				= 	"display:none;"
			>
		</td>
		<td align=left>
			<div class="trigger open">
				<div class="room_expand"></div>
				<?php 		$itemName = $offer->offer_name."(".$offer->room_name.")";
				
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
				&nbsp;|&nbsp;
				<a class="link_more" href="#">&nbsp;<?php echo JText::_('LNG_MORE',true)?> »</a>
			</div>
		</td>
		<td align=center class="hidden-xs hidden-phone">
			<?php if($offer->offer_id > 0){
				 echo $offer->offer_min_nights; 
			 } ?>
		</td>
		<td align=center class="hidden-xs hidden-phone">
			<?php echo $offer->max_adults;?>
		</td>
		<?php if($this->appSettings->show_children!=0){ ?>
			<td align=center class="hidden-xs">
				<?php echo $offer->max_children; ?>
			</td>
		<?php }?>
		<td align=right >
			<?php 
				if(!$offer->is_disabled && !$capacityExceeded){
					if($this->appSettings->show_price_per_person==1){
						echo JHotelUtil::fmt($offer->pers_total_price,2);
					}else{ 
						echo JHotelUtil::fmt($offer->offer_average_display_price,2);
					}
				}
			?>
		</td>
		<td align="center" class="hidden-phone">
			<ul style='margin-top:0px'>
			<?php
			$crt_room_sel  = 1;
			
			$datas = ( $this->userData->year_start.'-'.$this->userData->month_start.'-'.$this->userData->day_start );
			$datae = ( $this->userData->year_end.'-'.$this->userData->month_end.'-'.$this->userData->day_end );
			
			
			$diff = abs(strtotime($datae) - strtotime($datas));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			
			$nrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				 
			
			foreach( $this->userData->reservedItems as $v )
			{
				$room_ex = explode('|' , $v );
				if( $room_ex[0] == $offer->offer_id && $room_ex[1] == $offer->room_id)
				{
				?>
				<li>
					<div style='text-align:center'>
						<?php echo $room_ex[2]?>
						<a href='#dialog_room_selected_<?php echo $room_ex[0]?>_<?php echo $room_ex[1]?>_<?php echo $room_ex[2]?>' name='modal'>
							<!-- img width='12px' height='12px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/img/icon_info.png"?>" -->
						</a>
						<a onclick='javascript:deleteReserveRoom(<?php echo $room_ex[0]?>,<?php echo $room_ex[1]?>,<?php echo $room_ex[2]?>)'>
							<!--img style='cursor:hand;cursor:pointer' width='12px' height='12px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/img/icon_delete.png"?>" -->
						</a>
						<?php
						if( $offer->offer_id == 0 )
						{
						?>
						<a onclick='javascript:editReserveRoom(<?php echo $room_ex[0]?>,<?php echo $room_ex[1]?>,<?php echo $room_ex[2]?>)'>
							<!--img style='cursor:hand;cursor:pointer' width='12px' height='12px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/img/icon_edit.png"?>" -->
						</a>
						<?php
						}
						
						?>
					</div>
				</li>
				<?php
				}
				if( $room_ex[0] == $offer->offer_id  )
					$crt_room_sel++;
			}
			?>
			</ul>
		</td>
		<td colspan=1 align=right style="padding-left:15px;" >
			<?php
				$cheie_offer_room 	= $offer->offer_id."_".$offer->room_id;
				$offer_offer_room 	= $offer->offer_id."|".$offer->room_id;
							
				?>
				<input
					type	=	"hidden" 
					name	=	"items_reserved_tmp_<?php echo $cheie_offer_room?>" 				
					id		=	"items_reserved_tmp_<?php echo $cheie_offer_room?>" 					
					value	=	"<?php echo $offer_offer_room?>" 
				/> 

			
			<?php
			
			if($capacityExceeded){
				echo JText::_('LNG_CAPACITY_EXCEDEED',true);
			}
			if(!$capacityFullfilled){
				echo str_replace("<<min_persons>>",$offer->offer_min_pers, JText::_('LNG_CAPACITY_NOT_MET'));
			}
			else if(!$offer->is_disabled ){
			?>
			<span class="button button-green">
				<button 
					class="reservation"
					id			= 'itemRoomsCapacity_RADIO' 
					name		= 'itemRoomsCapacity_RADIO[]' 
					type		= 'button'
					<?php echo $offer->is_disabled? " disabled " : "" ?>
					<?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
					onclick 	= 	'return bookItem(
										<?php echo $offer->offer_id?>, 
										<?php echo $offer->room_id?>
									);
									'
									
				><?php echo JText::_('LNG_BOOK',true)?></button>
			</span>
			<?php }else{
				$buttonLabel = JText::_('LNG_CHECK_DATES',true);
				if($hideCalendars)
					$buttonLabel = JText::_('LNG_DETAILS',true);
						
				$class="";
				if( $offer->lock_for_departure){
					$buttonLabel = JText::_('LNG_NO_DEPARTURE',true);
					$class = "red";
				}

				?>
				<span class="button button-white trigger open <?php echo $class?>">
					<button
						class="reservation <?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'not-bookable' : ''?>"
						name="check-button"
						value		= "<?php echo $buttonLabel ?>"
						type		= 'button'
						<?php echo count($this->userData->reservedItems)  == $this->userData->rooms ? 'disabled' : ''?>
					><?php echo  $buttonLabel ?>
					</button>
				</span>
			<?php } ?>
			
		</td>
	</tr>
	<tr class="tr_cnt">
		<td class="td_cnt" colspan="9" >
			<div class="cnt">
				<div class="room-description">
					<div class="tabs-container" style="display:none">
							<ul style="display:block">
								<li><a href="#tabs-1_<?php echo $offer->room_id; ?>"><?php echo JText::_('LNG_ROOM_DETAILS',true)?></a></li>
								<li><a href="#tabs-2_<?php echo $offer->room_id; ?>"><?php echo JText::_('LNG_RATE',true)?></a></li>
								<li><a href="#tabs-3_<?php echo $offer->room_id; ?>"><?php echo JText::_('LNG_RATE_RULES',true)?></a></li>
							</ul>
							<div id="tabs-1_<?php echo $offer->room_id; ?>">
								<?php echo $offer->room_details?>
							</div>
							<div id="tabs-2_<?php echo $offer->room_id; ?>">
								<?php echo JText::_('LNG_PRICE_BREAKDOWN_BY_NIGHT',true)?>
								<div class="price_breakdown">
									<table >
									<?php
									$grand_total = 0;
									foreach( $offer->daily as $daily )
									{
										$p 		= $daily['display_price_final'];
										$day	= $daily['date'];
										echo '<tr><td>'.date('D d M', strtotime($day)).'</td><td>'.JHotelUtil::fmt($p,2).' '.$this->userData->currency->symbol.'</td></tr>';
										$grand_total += JHotelUtil::fmt($p,2);	
									}
									?>
										<tr class="price_breakdown_grad_total">
											<td>
												<strong> = <?php echo JText::_('LNG_GRAND_TOTAL',true)?></strong>
											</td>
											<td>
												<?php echo JHotelUtil::fmt($grand_total,2); ?>
										</tr>
									</table>
								</div>
							</div>
							<div id="tabs-3_<?php echo $offer->room_id; ?>">
								<?php echo JText::_('LNG_RATE_RULES_DESCRIPTION',true)?>
										
							</div>
						</div>
					<?php if(!$capacityExceeded){ ?>	
					<div id="calendar-holder-<?php echo ''.$offer->offer_id.''.$offer->room_id?>" class="room-calendar" style="<?php echo $hideCalendars?"display:none":"";?>">	
						<div class="room-loader right"></div>
				
					</div>
					<?php } ?>
					<div class="room_main_description">
						<?php
							echo $offer->offer_description."<br/>";
	 						echo $offer->offer_content."<br/>";
	 						echo $offer->offer_other_info;
						?>
					</div>
				</div>
								
				<div class='picture-container'>
					
					<?php  
					if( isset($offer->pictures) && count($offer->pictures) >0 )
					{
						foreach( $offer->pictures as $picture )
						{
						?>
							<a class="preview" onclick="return false;" title="<?php echo isset($picture->offer_picture_path)?$picture->offer_picture_info:'' ?>" alt="<?php echo isset($picture->offer_picture_info)?$picture->offer_picture_info:'' ?>" href="<?php echo JURI::root().PATH_PICTURES.$picture->offer_picture_path?>">
								<img 
										class="img_picture"
										style="height: 50px"
										src='<?php echo JURI::root() .PATH_PICTURES.$picture->offer_picture_path?>'
										alt="<?php echo isset($picture->offer_picture_path)?$picture->offer_picture_info:'' ?>"
										title="<?php echo isset($picture->offer_picture_path)?$picture->offer_picture_info:'' ?>"
										 />
							</a>
						<?php
						}
					}
					?>
				</div>
			</div>
		</td>
	</tr>
	<?php
	}
	?>
	
</TABLE>
</div>