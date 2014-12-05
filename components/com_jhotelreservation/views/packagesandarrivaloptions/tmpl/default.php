<?php // no direct access
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

defined('_JEXEC') or die('Restricted access'); 

?>

<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationinfo.php'; ?>
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".extras-options").each(function(){ 
			//jQuery(this).hide();
		});
		
		jQuery(".extra-checkbox").each(function(){ 
			//jQuery(this).prop('checked', false);
		});
	});
	
	function showWindowModalContinue()
	{
		//jQuery('#mask').fadeIn(1000);	
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
			
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(1000);	
		jQuery('#mask').fadeTo("slow",0.8);	
	}


	function changeOptionState(id){
		jQuery("#"+id).toggle();
	}

	
	
	function checkContinue()
	{
		var is_ok	= false;
		var form 	= document.forms['userForm'];
		if( form.elements['is_airport_transfers'] )
		{
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
				else if( form.elements['airport_airline_id'].value =='0' )
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
				
				
				//make variables for airport transfers
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_type_ids[]',
					name		: 'airport_transfer_type_ids[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_type_id'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_dates[]',
					name		: 'airport_transfer_dates[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_date'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_airline_ids[]',
					name		: 'airport_airline_ids[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_airline_id'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_time_hours[]',
					name		: 'airport_transfer_time_hours[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_time_hour'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_time_mins[]',
					name		: 'airport_transfer_time_mins[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_time_min'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_flight_nrs[]',
					name		: 'airport_transfer_flight_nrs[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_flight_nr'].value
				}).appendTo(jQuery('#userForm'));
				
				jQuery('<input>').attr({
					type		: 'hidden',
					id			: 'airport_transfer_guests[]',
					name		: 'airport_transfer_guests[]',
					value		:  '<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>|' + form.elements['airport_transfer_guest'].value
				}).appendTo(jQuery('#userForm'));

				//~make variables for airport transfers
			}
		}
	
		<?php
		
		if( $this->_models['variables']->rooms > $this->_models['variables']->getReservedItems() )
		{
		?>
			form.elements['tip_oper'].value = "-1";
			form.elements['task'].value = "checkAvalability";
		<?php
		}
		else
		{
		?>
			form.elements['tip_oper'].value = '4';
		<?php
		}
		?>
		//showWindowModalContinue();
		form.submit();
		return true;
		
	}
	
	function formBack() 
	{
		var form 	= document.forms['userForm'];
		//userForm.tip_oper.value = "<?php echo JRequest::getVar( 'tip_oper')-1?>;
		form.elements['task'].value = "checkAvalability";
		
		form.elements['tip_oper'].value ="-1";
		
		deleteReservedItems();

		//clean all necesary
		//~clean all necesary
		//showWindowModalContinue();
		form.submit();
	}
	
</script>
<form autocomplete="off" action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" id="userForm" >
	<div class="hotel_reservation">	
		<div CLASS='DIV_BUTTONS'>
				<table width='100%' border= 0 align=center>
					<tr>
						<td align=left>
						
							<span class="button button-green">
								<button value="checkRates" name="checkRates" type="button" onclick="formBack()">
									<?php echo JText::_('LNG_BACK',true)?>
								</button>
							</span>
						</td>
						<td align=right>
							<span class="button button-green right">
								<button value="checkRates" name="checkRates" type="button" onclick="return checkContinue();">
								<?php echo 
									$this->_models['variables']->rooms > $this->_models['variables']->getReservedItems('edit') + 1 ?
									JText::_('LNG_CONTINUE_ROOM_RATES',true)
									:
									JText::_('LNG_CONTINUE',true)
								?>
								</button>
							</span>
						</td>
					</tr>
				</table>
			</div>
			<?php
			if(JRequest::getVar( 'is_enable_extra_options')== true  &&  count($this->_models['variables']->extraOptions) > 0 )	{
			?> 
				<fieldset>
					<h3><?php echo JText::_('LNG_EXTRAS',true); ?> 
					<?php 
						if($this->_models['variables']->rooms > 1){
							echo " - ".(isset($this->_models['variables']->itemHotelSelected->types) & $this->_models['variables']->itemHotelSelected->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_SELECT_EXTRA_PARK',true) : JText::_('LNG_SELECT_EXTRA',true)) ." ".( $this->_models['variables']->getReservedItems()) ;
						}
						?>
					</h3>
					
					<div id="extra-options-container">
						<?php
							foreach( $this->_models['variables']->extraOptions as $item ){
								$is_checked = false;
								//dmp($this->_models['variables']->items_reserved);
								$extraOptionKey 	= $this->_models['variables']->items_reserved[$this->_models['variables']->getReservedItems()-1].'|'.$item->id;
								//dmp($extraOptionKey);
								if(isset( $this->_models['variables']->extraOptionIds[ $extraOptionKey ] )
										& $this->_models['variables']->extraOptionIds[ $extraOptionKey ][4] == 1
										|| $item->mandatory == 1){
									$is_checked = true;
								}
							?>
								<div class="extra-option">
									<div  class='extra-option-image'>
									<?php if(isset($item->image_path) && strlen($item->image_path)>0){ 
										echo "<img src='".JURI::base() ."administrator/components/".getBookingExtName().EXTRA_OPTISON_PICTURE_PATH.$item->image_path."'/>";
									}else{
										echo "<img src='".JURI::base() ."components/com_jhotelreservation/img/no_image.jpg'/>";
									}
									?>
									</div>
										<input 
												type	='checkbox'
												name	='extraOptionIds[]'
												id		='extraOptionIds<?php echo $item->id ?>'
												value	= '<?php echo $extraOptionKey?>|1|0|0'
												class="extra-checkbox"
												onchange="changeOptionState('options-<?php echo $item->id ?>');"
												<?php echo $is_checked ? " checked " : ""?>
												<?php echo $item->mandatory == 1 ? ' onclick="return false" onkeydown="return false" checked="checked" ' : ""?>
											>
										
									<strong><?php echo $item->name ?></strong>,	<?php echo $this->_models['variables']->itemCurrency->currency_symbol?> <?php echo JHotelUtil::fmt($item->price,2) ?><?php echo $item->price_type == 1?",&nbsp;".strtolower(JText::_('LNG_PER_PERSON",true)):"" ?><?php  echo $item->is_per_day == 1 ?",&nbsp;".JText::_('LNG_PER_DAY",true):"" ?><?php  echo $item->is_per_day == 2 ?',&nbsp;".JText::_("LNG_PER_NIGHT',true):"" ?>
									<p><i><?php echo $item->description ?></i></p>
									
									<div class="extras-options" id="options-<?php echo $item->id?>" style="display:none">
										<?php if($item->price_type == 1){?>
											<?php echo JText::_('LNG_NUMBER_OF_PERSONS',true)?>
											<select name="extra-option-persons-<?php echo $item->id?>">
												<?php for($i=1;$i<21;$i++){ ?>
													<option value="<?php echo $i ?>" <?php echo $item->mandatory == 1 && $i== ($this->_models['variables']->guest_adult + $this->_models['variables']->guest_child)? 'selected="selected"':''?>><?php echo $i ?></option>
												<?php } ?>
											</select>
										<?php } ?>
										
										<?php if($item->is_per_day == 1 || $item->is_per_day == 2){?>
											<?php echo $item->is_per_day ==1 ? JText::_('LNG_NUMBER_OF_DAYS',true) : JText::_('LNG_NUMBER_OF_NIGHTS',true)?>
											<select name="extra-option-days-<?php echo $item->id?>">
												<?php for($i=1;$i<21;$i++){ ?>
													<option value="<?php echo $i ?>"><?php ?><?php echo $i ?></option>
												<?php } ?>
											</select>
										<?php } ?>
										
									</div>
									<div class="clear"> </div>
								</div>
							<?php
							}
							?>
					</div>
				<?php
				}
				?>
				</fieldset>
						
				<?php
				$cheie_airport = $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current;
				?>
				
				<?php 
				if($this->_models['variables']->itemAppSettings->is_enable_screen_airport_transfer){
				?>
				<TR>
					<TD valign=top>
						<BR>
						<div class="header_line">
							<strong><?php echo JText::_('LNG_AIRPORT_TRANSFER',true); ?></strong>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</div>
						<input 
							type	='checkbox'
							name	='is_airport_transfers'
							id		='is_airport_transfers'
							<?php 
								if( isset( $this->_models['variables']->airport_transfer_type_ids[ $cheie_airport ] ) & $this->_models['variables']->airport_transfer_type_ids[ $cheie_airport ][3] >  0  )
									echo " checked ";
							?>
							onclick	= "
										if( this.checked == false )
										{
											jQuery('#airport_transfer_type_id').val( '0' );
											jQuery('#airport_transfer_time_hour').val('-1');
											jQuery('#airport_transfer_time_min').val('-1');
											jQuery('#airport_transfer_flight_nr').val('');
											jQuery('#airport_transfer_guest').val('');
											jQuery('#airport_transfer_date').val('');
											jQuery('#airport_airline_id').val('0');
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
							<?php echo (isset( $this->_models['variables']->airport_transfer_type_ids[ $cheie_airport ] ) & $this->_models['variables']->airport_transfer_type_ids[ $cheie_airport ][3] >  0 ) ? "" : " style='display:none' " ?>
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
											style	= 'width:500px'
											onchange= 	"
															if(this.value == 0 )
															{
																jQuery('#div_airport_transfer_type_price').html( '' );
															}
															else
															{
																<?php
																
																foreach( $this->_models['variables']->itemAirportTransferTypes as $valueAirportTransferType )
																{
																	//$cheie_arrival_option 	= $valueAirportTransferType->offer_id.'|'.$valueAirportTransferType->room_id.'|'.$valueAirportTransferType->current;
																	?>
																	if( this.value == '<?php echo $valueAirportTransferType->airport_transfer_type_id?>')
																	{
																		<?php
																		$pr = 	$this->_models['variables']->currency_selector.'&nbsp;'.$valueAirportTransferType->airport_transfer_type_display_price.
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
												<?php /*echo 0 == $this->_models['variables']->airport_transfer_type_id ? " selected " : ""*/ ?>
											>
											</option>
											
											<?php
											foreach( $this->_models['variables']->itemAirportTransferTypes as $valueAirportTransferType )
											{
												if( 
													$valueAirportTransferType->offer_id != $this->_models['variables']->reserve_offer_id
													||
													$valueAirportTransferType->room_id != $this->_models['variables']->reserve_room_id
													||
													$valueAirportTransferType->current != $this->_models['variables']->reserve_current
												)
												{
													continue;
												}
												
												$cheie_arrival_option 	= $valueAirportTransferType->offer_id.'|'.$valueAirportTransferType->room_id.'|'.$valueAirportTransferType->current;
											?>
											<option
												<?php echo isset( $this->_models['variables']->airport_transfer_type_ids[$cheie_arrival_option]) && $valueAirportTransferType->airport_transfer_type_id == $this->_models['variables']->airport_transfer_type_ids[$cheie_arrival_option][3]? " selected " : "" ?>
												value ='<?php echo $valueAirportTransferType->airport_transfer_type_id ?>'
											>
												<?php echo $valueAirportTransferType->airport_transfer_type_name?> 
												( <?php echo $this->_models['variables']->currency_selector.'&nbsp;'.$valueAirportTransferType->airport_transfer_type_display_price?> )
												<?php echo ($valueAirportTransferType->airport_transfer_type_vat !=0 ? (" + ".$valueAirportTransferType->airport_transfer_type_vat." %".JText::_('LNG_VAT',true)) : "&nbsp;")?>
												<?php echo " | ".$valueAirportTransferType->airport_transfer_type_description?> 	
											</option>
											<?php
											}									
											?>
										</select>
										<?php
										//dmp($this->_models['variables']->itemAirportTransferTypes);
										?>
									</TD>
								</TR>
								<TR>
									<TD nowrap>
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
										echo JHTML::calendar(
																isset( $this->_models['variables']->airport_transfer_dates[ $cheie_airport ] ) ? 
																	$this->_models['variables']->airport_transfer_dates[ $cheie_airport ][3]
																	:
																	""
																,
																'airport_transfer_date',
																'airport_transfer_date',
																'%Y-%m-%d', 
																array('style'=>'text-align:center;width:80px')
															);
									?>
									</TD>
								</TR>
								<TR>
									<TD  nowrap>
										<?php echo JText::_('LNG_AIRLINE',true)?>
										<span class="mand">*</span>
									</TD>
									<TD width=40%>
										<select
											id		= 'airport_airline_id'
											name	= 'airport_airline_id'
											style	= 'width:230px'
										>
											<option
												value = '0'
												<?php /* echo 0== $this->_models['variables']->airline_id? " selected " : ""  */?>
											></option>
											<?php
											foreach( $this->_models['variables']->itemArrivalAirlines as $valueAirlines )
											{
											?>
											<option
												value ='<?php echo $valueAirlines->airline_id ?>'
												<?php /*echo $valueAirlines->airline_id == $this->_models['variables']->airline_id? " selected " : ""*/ ?>
												<?php echo 
													isset( $this->_models['variables']->airport_airline_ids[ $cheie_airport ] ) 
													&& 
													$this->_models['variables']->airport_airline_ids[ $cheie_airport ][3] == $valueAirlines->airline_id ?
													" selected "
													:
													""
												?>
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
											<option value='-1' <?php /* echo $this->_models['variables']->airport_transfer_time_hour==-1? " selected" : "" */?>></option>
											<?php
											for($i=0;$i<=23;$i++ )
											{
											?>
											<option 
												<?php echo 
													isset( $this->_models['variables']->airport_transfer_time_hours[ $cheie_airport ] ) 
													&& 
													$this->_models['variables']->airport_transfer_time_hours[ $cheie_airport ][3] == $i ?
													" selected "
													:
													""
												?>
											>
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
											<option value='-1' <?php /* echo $this->_models['variables']->airport_transfer_time_min==-1? " selected" : ""*/?>></option>
											<?php
											for($i=0;$i<=59;$i++ )
											{
											?>
											<option 
												<?php echo 
													isset( $this->_models['variables']->airport_transfer_time_mins[ $cheie_airport ] ) 
													&& 
													$this->_models['variables']->airport_transfer_time_mins[ $cheie_airport ][3] == $i ?
													" selected "
													:
													""
												?>
											>
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
											value	='<?php echo 
														isset( $this->_models['variables']->airport_transfer_flight_nrs[ $cheie_airport ] ) 
														&& 
														$this->_models['variables']->airport_transfer_flight_nrs[ $cheie_airport ][3] ?
														$this->_models['variables']->airport_transfer_flight_nrs[ $cheie_airport ][3]
														:
														""
														?>'
											
										>
										<?php echo JText::_('LNG_FLIGHT_NR_SAMPLE',true)?>
									</TD>
									<TD nowrap>
										<?php echo JText::_('LNG_GUEST',true)?> :
										<span class="mand">*</span>
									</TD>
									<TD width=40%>
										<input 
											type	='text'
											name	='airport_transfer_guest'
											id		='airport_transfer_guest'
											value	='<?php echo 
														isset( $this->_models['variables']->airport_transfer_guests[ $cheie_airport ] ) 
														&& 
														$this->_models['variables']->airport_transfer_guests[ $cheie_airport ][3] ?
														$this->_models['variables']->airport_transfer_guests[ $cheie_airport ][3]
														:
														""
														?>'
											
										>
									</TD>
								</TR>
							</TABLE>
						</div>
					</TD>
				</TR>
				<?php } ?>
			
		<BR>
		<div CLASS='DIV_BUTTONS'>
			<table width='100%' border= 0 align=center>
				<tr>
					<td align=left>
					
						<span class="button button-green">
							<button value="checkRates" name="checkRates" type="button" onclick="formBack()">
								<?php echo JText::_('LNG_BACK',true)?>
							</button>
						</span>
					</td>
					<td align=right>
						<span class="button button-green right">
							<button value="checkRates" name="checkRates" type="button" onclick="return checkContinue();">
							<?php echo 
								$this->_models['variables']->rooms > $this->_models['variables']->getReservedItems('edit') + 1 ?
								JText::_('LNG_CONTINUE_ROOM_RATES',true)
								:
								JText::_('LNG_CONTINUE',true)
							?>
							</button>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div> 
	<div id="mask"></div>
	<input type="hidden" name="option" 							id="option" 							value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" 							id="task" 								value="" />
	<input type="hidden" name="tip_oper" 						id="tip_oper" 							value="<?php echo JRequest::getVar( 'tip_oper') ?>" />
	<input type="hidden" name="tmp" 							id="tmp" 								value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="controller" 						id="controller" 						value="" />
	<input type="hidden" name="view" 							id="view" 								value="JHotelReservation" /> 
	<input type="hidden" name="_lang" 							id="_lang" 								value="<?php echo JRequest::getVar('_lang') ?>" />
																			
	<input type="hidden" name="year_start" 						id="year_start" 						value="<?php echo $this->_models['variables']->year_start?>" 									/> 
	<input type="hidden" name="month_start" 					id="month_start" 						value="<?php echo $this->_models['variables']->month_start?>" 									/> 
	<input type="hidden" name="day_start" 						id="day_start" 							value="<?php echo $this->_models['variables']->day_start?>" 									/> 
	<input type="hidden" name="year_end" 						id="year_end" 							value="<?php echo $this->_models['variables']->year_end?>" 										/> 
	<input type="hidden" name="month_end" 						id="month_end" 							value="<?php echo $this->_models['variables']->month_end?>" 									/> 
	<input type="hidden" name="day_end" 						id="day_end" 							value="<?php echo $this->_models['variables']->day_end?>" 										/> 
	<input type="hidden" name="rooms" 							id="rooms" 								value="<?php echo $this->_models['variables']->rooms?>" 										/> 
	<input type="hidden" name="guest_adult" 					id="guest_adult" 						value="<?php echo $this->_models['variables']->guest_adult?>" 									/> 
	<input type="hidden" name="guest_child" 					id="guest_child" 						value="<?php echo $this->_models['variables']->guest_child?>" 									/> 
	<input type="hidden" name="coupon_code"						id="coupon_code"						value="<?php echo $this->_models['variables']->coupon_code?>" 									/> 
	<input type="hidden" name="room_available_ids"				id="room_available_ids"					value="<?php echo implode(',' , $this->_models['variables']->room_available_ids)?>" 			/> 
	<input type="hidden" name="currency_selector"				id="currency_selector"					value="<?php echo $this->_models['variables']->currency_selector?>" 							/> 
	
	<input type="hidden" name="reserve_offer_id" 				id="reserve_offer_id" 					value=""	/>	
	
	<input type="hidden" name="hotel_id" 						id="hotel_id"							value="<?php echo $this->_models['variables']->hotel_id?>" /> 
	<input type="hidden" name="reserve_room_id" 				id="reserve_room_id" 					value="" 	/>
	<input type="hidden" name="reserve_current" 				id="reserve_current" 					value="<?php echo $this->_models['variables']->reserve_current ?>" 	/>
	<input type="hidden" name="max_package_number" 				id="max_package_number" 				value="<?php echo $this->max_package_number ?>" />

	<input type="hidden" name="itemRoomsCapacity"				id="itemRoomsCapacity"					value="<?php echo $this->_models['variables']->getStringRoomsCapacity($this->_models['variables']->itemRoomsCapacity)?>" 	/> 

	<input type="hidden" name="option_ids"						id="option_ids"							value="<?php echo implode(',' , $this->_models['variables']->option_ids)?>" 	/> 
	<input type="hidden" name="room_ids"						id="room_ids"							value="<?php echo implode(',' , $this->_models['variables']->room_ids)?>" 	/> 
	<input type="hidden" name="max_package_number" 				id="max_package_number" 				value="<?php echo $this->max_package_number?>" />
	<!-- input type="hidden" name="items_reserved[]" 			id="items_reserved[]" 					value="<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>" /--> 
	<input type="hidden" name="mediaReferer" 					id="mediaReferer" 						value="<?php echo $this->_models['variables']->mediaReferer?>"/>
	<input type="hidden" name="voucher" 						id="voucher" 							value="<?php echo $this->_models['variables']->voucher?>"/>
	
	<?php
	/*
	$is_edit  = false;
	foreach( $this->_models['variables']->items_reserved as $v)
	{
		if($v == $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current)
			$is_edit  = true;
	?>
	<input type="hidden" name="items_reserved[]" 				id="items_reserved[]" 					value="<?php echo $v?>" /> 
	<?php
	}
	
	if( $is_edit ==false )
	{
	?>
	<input type="hidden" name="items_reserved[]" 		id="items_reserved[]" 		value="<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>" /> 
	<?php
	}
	*/
	
	$skip_key = array( 
						$this->_models['variables']->reserve_offer_id, 
						$this->_models['variables']->reserve_room_id,
						$this->_models['variables']->reserve_current
					);
	$this->_models['variables']->displayHiddenValues( 'items_reserved', 				array('operation' => 'edit', 'type'=>'value', 'skip_value'=>$skip_key)  	);
	$this->_models['variables']->displayHiddenValues( 'package_ids', 					array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key)  	);
	$this->_models['variables']->displayHiddenValues( 'package_day', 					array('operation' => 'edit', 'type'=>'multiarray', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'itemPackageNumbers',				array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'arrival_option_ids', 			array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'extraOptionIds', 				array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_airline_ids',			array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_type_ids', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_dates', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_hours', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_mins', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_flight_nrs', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_guests', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );

	?>
	
</form>
