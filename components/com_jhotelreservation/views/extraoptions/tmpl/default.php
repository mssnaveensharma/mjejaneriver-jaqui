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

<?php // require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationinfo.php'; ?>
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<script type="text/javascript">
	 var currency='<?php echo $this->userData->currency->symbol?>';
	 var extraOptions = [
               <?php 
	               foreach( $this->extraOptions as $extraOption){
	              	 	echo "[".$extraOption->id.", '".$extraOption->name."',".$extraOption->price."],"."\n";
	            	 } 
	           ?>
            	
      ];
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

	function upateExtraOption(){
		var reservationSubtotal=reservationSubtotalI;
		var reservationCost=reservationCostI;
		var reservationTotal=reservationTotalI;

		jQuery("#rooms-info tbody tr").remove();
		jQuery(".extra-checkbox").each(function(){
			if(jQuery(this).is(':checked')){
				
				var extraOptionId = jQuery(this).attr('id');
				extraOptionId = extraOptionId.replace("extraOptionIds",""); 

				var price;
				var name;
				for (var index = 0; index < extraOptions.length; ++index) {
					if(extraOptions[index][0] == extraOptionId){
						name = extraOptions[index][1];
						price = extraOptions[index][2];
					}
				}

				var persons = jQuery("#persons-"+extraOptionId).val();
				var days= jQuery("#days-"+extraOptionId).val();

				if(typeof persons === "undefined")
					persons = 1;
				if(typeof days === "undefined")
					days = 1;
				
				price = parseFloat(price) * parseFloat(persons) * parseFloat(days);
				var row = '<tr id=extra-'+extraOptionId+'><td>'+name+'</td><td class="price">'+currency+' '+price.toFixed(2)+'</td></tr>';
				jQuery('#rooms-info').append(row);
				reservationSubtotal += parseFloat(price);
				
			}
				
		});
		reservationTotal = parseFloat(reservationSubtotal) + parseFloat(reservationCost);
		jQuery("#info-subtotal").html(reservationSubtotal.toFixed(2));
		jQuery("#info-cost").html(reservationCost.toFixed(2));
		jQuery("#info-total").html(reservationTotal.toFixed(2));
			
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
				
			}
		}
	
		form.submit();
		return true;
		
	}
	
	
	function formBack() 
	{
		var form 	= document.forms['userForm'];
		form.task.value	="extraoptions.back";
		form.submit();
	}
	
</script>

<form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userForm" id="userForm" >
	<div class="hotel_reservation">	
			<div CLASS='DIV_BUTTONS'>
				<table width='100%' border= 0 align=center>
					<tr>
						<td align=left>
							<span class="button button-green">
								<button value="checkRates" name="checkRates" type="button" onclick="formBack()">
									<?php echo JText::_('LNG_BACK')?>
								</button>
							</span>
						</td>
						<td align=right>
							<span class="button button-green right">
								<button value="checkRates" name="checkRates" type="button" onclick="return checkContinue();">
								<?php
								
								 echo 
									$this->userData->rooms > count($this->userData->reservedItems) ?
									JText::_('LNG_CONTINUE_ROOM_RATES')
									:
									JText::_('LNG_CONTINUE')
								?>
								</button>
							</span>
						</td>
					</tr>
				</table>
			</div>
			
			<div class="right hidden-phone">
			<?php 
				jimport('joomla.application.module.helper');
				// this is where you want to load your module position
				$modules = JModuleHelper::getModules('reservation-info');
			
				foreach($modules as $module)
				{
					echo JModuleHelper::renderModule($module);
				}
			?>
			</div>
			<?php
			if($this->appSettings->is_enable_extra_options== true  &&  count($this->extraOptions) > 0 )	{
			?> 
				<fieldset>
					<h3><?php echo JText::_('LNG_EXTRAS'); ?> 
					<?php 
						if($this->userData->rooms > 1){
							echo " - ".(isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_SELECT_EXTRA_PARK') : JText::_('LNG_SELECT_EXTRA')) ." ".( count($this->userData->reservedItems)) ;
						}
						?>
					</h3>
					
					<div id="extra-options-container">
						<?php
							foreach( $this->extraOptions as $item ){
								$is_checked = false;
								//dmp($this->_models['variables']->items_reserved);
								$extraOptionKey 	= $this->userData->reservedItems[count($this->userData->reservedItems)-1].'|'.$item->id;
							?>
								<div class="extra-option">
									<div  class='extra-option-image'>
									<?php if(isset($item->image_path) && strlen($item->image_path)>0){ 
										echo "<img src='".JURI::base() .PATH_PICTURES.EXTRA_OPTISON_PICTURE_PATH.$item->image_path."'/>";;
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
												onchange="changeOptionState('options-<?php echo $item->id ?>');upateExtraOption()"
												<?php echo $is_checked ? " checked " : ""?>
												<?php echo $item->mandatory == 1 ? ' onclick="return false" onkeydown="return false" checked="checked" ' : ""?>
											>
									<div class="extra-option-box"> 	
									<strong><?php echo $item->name ?></strong>,	<?php echo $this->userData->currency->symbol?> <?php echo JHotelUtil::fmt($item->price,2) ?><?php echo $item->price_type == 1?",&nbsp;".strtolower(JText::_('LNG_PER_PERSON',true)):"" ?><?php  echo $item->is_per_day == 1 ?",&nbsp;".JText::_('LNG_PER_DAY',true):"" ?><?php  echo $item->is_per_day == 2 ?",&nbsp;".JText::_('LNG_PER_NIGHT',true):"" ?>
									<p><i><?php echo $item->description ?></i></p>
									
									<div class="extras-options" id="options-<?php echo $item->id?>" style="display:none">
										<?php if($item->price_type == 1){?>
											<?php echo JText::_('LNG_NUMBER_OF_PERSONS',true)?>
											<select id="persons-<?php echo $item->id?>" name="extra-option-persons-<?php echo $item->id?>" onchange="upateExtraOption()">
												<?php for($i=1;$i<21;$i++){ ?>
													<option value="<?php echo $i ?>" <?php echo $item->mandatory == 1 && $i== ($this->_models['variables']->guest_adult + $this->_models['variables']->guest_child)? 'selected="selected"':''?>><?php echo $i ?></option>
												<?php } ?>
											</select>
										<?php } ?>
										<br/>
										<?php if($item->is_per_day == 1 || $item->is_per_day == 2){?>
											<?php echo $item->is_per_day ==1 ? JText::_('LNG_NUMBER_OF_DAYS',true) : JText::_('LNG_NUMBER_OF_NIGHTS',true)?>
											<?php 
													$nrDays = UserDataService::getNrDays();
											?>
											<select id="days-<?php echo $item->id?>" name="extra-option-days-<?php echo $item->id?>" onchange="upateExtraOption()"  <?php echo $item->map_per_length_of_stay==1?'onfocus="this.oldvalue=this.value;this.blur();" onchange="this.value=this.oldvalue;"':''?> >
												<?php for($i=1;$i<21;$i++){ ?>
													<option value="<?php echo $i ?>" <?php echo ($item->map_per_length_of_stay ==1 && $i== $nrDays) ? 'selected="selected"':''; ?> ><?php ?><?php echo $i ?></option>
												<?php } ?>
											</select>
										<?php } ?>
										
									</div>
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
				//$cheie_airport = $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current;
				?>
				
				<?php 
				if($this->appSettings->is_enable_screen_airport_transfer){
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
								$this->userData->rooms > count($this->userData->reservedItems)  ?
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
	<input type="hidden" name="task" 	 id="task" 		value="extraoptions.addExtraOptions" />
	<input type="hidden" name="hotel_id" id="hotel_id"	value="<?php echo $this->hotel->hotel_id?>" />
	<input type="hidden" name="current"  id="current"	value="<?php echo JRequest::getVar("current") ?>" />
	<input type="hidden" name="reservedItems"  id="reservedItems" value="<?php echo JRequest::getVar("reservedItems") ?>" />
	<input type="hidden" name="extraOptions"  id="extraOptionss" value="<?php echo JRequest::getVar("extraOptions") ?>" />
</form>
