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
		if(jQuery('.trigger_p').length > 0) 
		{
			jQuery('.trigger_p').click(function() 
			{
				if (jQuery(this).hasClass('open')) 
				{
					jQuery(this).removeClass('open');
					jQuery(this).addClass('close');
					jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideDown(100);
					jQuery(this).children('.package_expand').addClass('expanded');
					jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_LESS',true)?> �');
					return false;
				} else {
					jQuery(this).removeClass('close');
					jQuery(this).addClass('open');
					jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(100);
					jQuery(this).children('.package_expand').removeClass('expanded');
					jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_MORE',true)?> �');
					return false;
				}			
			});

			//IE fix
			//jQuery('.trigger_p').parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(100);

		}
		jQuery( "div.tabs-container" ).tabs();
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
		if( $this->_models['variables']->rooms > $this->_models['variables']->getReservedItems('edit') + 1 )
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
	
	function setCheckboxPackage( nr_crt, ch_id )
	{
		var form 	= document.forms['userForm'];
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
		var form 	= document.forms['userForm'];
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
		var form 		= document.forms['userForm'];
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
<form autocomplete="off" action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" id="userForm" >
	<div class="hotel_reservation">
		<table width=100% cellspacing=0 >
			<TR>	
				<TD colspan=5>
					<?php
						$max_package_number = 10;
					?>
				</TD>
			</TR>
		</TABLE>
		<?php
		// dmp($this->_models['variables']->itemPackages);
		if( count($this->_models['variables']->itemPackages) > 0 )
		{
		?>
		<div>
			<table width=100% class="packages" >
				<TR>
					<TD valign=top colspan=6>
						<TABLE width=100% valign=top class="table_info" cellspacing="0">
							<TR class="tr_header">
								<TH width=2%>&nbsp;</TH>
								<TH width=25% ><?php echo JText::_('LNG_PACKAGES',true); ?></TH>
								<TH width=15% align=right><?php echo JText::_('LNG_PRICE',true)?>(<?php echo $this->_models['variables']->currency_selector?>)</TH>
								<TH width=10%>&nbsp;</TH>
							</TR>
							<?php
							//dmp($this->itemPackages);
							$nrCrt 		= 0;
							foreach( $this->_models['variables']->itemPackages as $value )
							{
								$is_checked	= false;
								$cheie_package = $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current.'|'.$value->package_id;
								if( 
									$value->offer_id != $this->_models['variables']->reserve_offer_id
									||
									$value->room_id != $this->_models['variables']->reserve_room_id
									||
									$value->current != $this->_models['variables']->reserve_current
								)
								{
								
									continue;
								}
								else 
								{
									if( isset($this->_models['variables']->itemPackageNumbers[$cheie_package] ) )
										$is_checked = true;
								}
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
										style				= 	'display:none'
									>
								</TD>
								<TD align=left nowrap>
									<div class="trigger_p open">
										<div class="package_expand"></div>
											<?php echo $value->package_name?>
											&nbsp;|&nbsp;
											<a class="link_more" href="#">&nbsp;<?php echo JText::_('LNG_MORE',true)?> �</a>
									</div>
								</TD>
								<TD align=right>
									<?php echo ($value->is_price_day ? "<b><i>" : "").JHotelUtil::fmt($value->display_price_final,2).($value->is_price_day ? "</b></i>" : "")?> <?php echo $value->is_price_day ? "( ".JText::_('LNG_PER_DAY',true)." )  " : ""?>
								</TD>
								<TD nowrap align=right>
									&nbsp;x&nbsp;
									<select id='itemPackageNumbers' name='itemPackageNumbers[]' style='width:60px'
										onchange = 	' 
														var crtValue = this.value;
														crtValue	 = crtValue.split("|");
														setCheckboxPackage( 
																			crtValue[4], 
																			"<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current.'|'?>" + crtValue[3]
																		);
													'
									>
										<?php
										if( !isset($this->_models['variables']->itemPackageNumbers[$cheie_package]) )
											$this->_models['variables']->itemPackageNumbers[$cheie_package] 	
																						= array(
																								$this->_models['variables']->reserve_offer_id,
																								$this->_models['variables']->reserve_room_id,
																								$this->_models['variables']->reserve_current,  
																								$value->package_id,
																								0,
																								0
																							);
										
										for( $i=0; $i<=$max_package_number; $i++ )
										{									
										?>
										<option 
											value='<?php echo $cheie_package.'|'.$i?>'
											<?php echo $this->_models['variables']->itemPackageNumbers[$cheie_package][4] ==$i ? " selected " : ""?>
										>
											<?php echo $i>0?$i : ''?>
										</option>
										<?php
										
										}
										?>
									</select>
								</TD>
							</TR>
							<tr class="tr_cnt">
								<td class="td_cnt" colspan="4" >
									<div class="cnt">
										<div class="package-description" >
											<div 
												id1		="tabs-package" 
												name1	="tabs-package" 
												class	='tabs-container'
												class1	='tabs-container ui-tabs ui-widget ui-widget-content ui-corner-all'
												style="display:block"
											>
												<ul>
													<li><a href="#tabs-package-1_<?php echo $value->package_id; ?>"><?php echo JText::_('LNG_RATE',true)?></a></li>
													<li style="display:block"><a href="#tabs-package-2_<?php echo $value->package_id; ?>"><?php echo JText::_('LNG_RATE_RULES',true)?></a></li>
												</ul>
												<div id="tabs-package-1_<?php echo $value->package_id; ?>" ><?php echo JText::_('LNG_PRICE_PER_PERSON',true)?>
													<div class="price_breakdown">
													<table>
														<?php
														if( $value->is_price_day ==false )
														{
														?>
														<tr class="price_breakdown_grad_total">
															<td nowrap>
																<b> = <?php echo JText::_('LNG_GRAND_TOTAL',true)?></b>
															</td>
															<td>
																<?php echo JHotelUtil::fmt($value->display_price_final,2)?>  (<?php echo $this->_models['variables']->currency_selector?>) (<?php echo  JText::_('LNG_PACKAGE_PRICE',true) ?>)
															</td>
														</tr>
														
														<?php
														}
														else
														{
															$grand_total = 0;
															foreach( $value->daily as $daily )
															{
																$p 		= $daily['display_price_final'];
																$day	= $daily['data'];
																// dmp($daily);
																?>
																<tr>
																	<td>
																		<?php echo date('D d M', strtotime($day))?>
																	</td>
																	<td>
																		<?php echo JHotelUtil::fmt($p,2).' '.$this->_models['variables']->currency_selector?>
																	</td>
																	<td>
																		<input 
																			type='checkbox' 
																			name='package_day[]'
																			id	='package_day_<?php echo $cheie_package?>[]'
																			title	= '<?php echo $p?>'
																			value	= '<?php echo $cheie_package.'|'.$day?>'
																			onclick = 	"
																							calcPackageValue( '<?php echo $cheie_package?>' );	
																						"
																			<?php echo $daily['is_sel']? " checked " : ""?>
																		>
																	</td>
																</tr>
																<?php
																if( $daily['is_sel'] )
																	$grand_total += JHotelUtil::fmt($p,2);	
															}
															?>
															<tr class="price_breakdown_grad_total">
																<td nowrap>
																	<b> = <?php echo JText::_('LNG_GRAND_TOTAL',true)?></b>
																</td>
																<td nowrap>
																	<?php echo '<input type=\'text\' 
																					id	=\'package_grand_total_'.$cheie_package.'\'
																					name=\'package_grand_total_'.$cheie_package.'\'
																					readonly
																					size= 10
																					style = \'border:solid 0px black;text-align:center;\'
																					value=\''.JHotelUtil::fmt($grand_total,2).'\'
																				>'; ?> 
																	<?php echo $this->_models['variables']->currency_selector?>
																</td>
															</tr>
															<?php
														}
														?>
													</table>
													</div>
												</div>
												<div id="tabs-package-2_<?php echo $value->package_id; ?>" >
													<?php echo JText::_('LNG_RATE_RULES_DESCRIPTION',true)?>														
												</div>
											</div>
											<div class="package_main_description">
												<?php echo  $value->package_description ?>
											</div>
										</div>
										<div class='picture-container'>
										</div>
									</div>
								</td>
							</tr>
							<?php
							$nrCrt ++;
							}
							?>
						</TABLE>
					</TD>
				</TR>
			</TABLE>
		</div>
		<?php
		}
			
		if(  JRequest::getVar( 'is_enable_extra_options')== true )
		{
			?>
			<table width="100%" class="arrival-options">
			<?php
			if( count($this->_models['variables']->itemArrivalOptions) > 0 )
			{
			?> 
				<TR>
					<TD valign=top>
						<div class="header_line">
							<strong><?php echo JText::_('LNG_ARRIVAL_OPTIONS',true); ?></strong>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</div>
						<div class='div_arrival_option_info' ><?php echo JText::_('LNG_INFO_ARRIVAL_OPTIONS',true)?></div>
						<TABLE width=100% valign=top class='table_arrival_options'>
							<?php
							// dmp($this->_models['variables']->itemArrivalOptions);
							foreach( $this->_models['variables']->itemArrivalOptions as $value )
							{
								$cheie_arrival_option 	= $value->offer_id.'|'.$value->room_id.'|'.$value->current.'|'.$value->arrival_option_id;
								$is_checked				= false;
								if( 
									$value->offer_id != $this->_models['variables']->reserve_offer_id
									||
									$value->room_id != $this->_models['variables']->reserve_room_id
									||
									$value->current != $this->_models['variables']->reserve_current
								)
								{
									continue;
								}
								else if( 
									isset( $this->_models['variables']->arrival_option_ids[ $cheie_arrival_option ] ) 
									&& 
									$this->_models['variables']->arrival_option_ids[ $cheie_arrival_option ][4] == 1
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
									<?php echo $value->arrival_option_name ?>
								</TD>
								<TD width=15%  align=right>
									<?php echo $value->arrival_option_display_price ?>(<?php echo $this->_models['variables']->currency_selector?>)
								</TD>
							</TR>
							<?php
							}
							?>
						</TABLE>
					</TD>
				</TR>
				<?php
				}
				
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
			</table>
		<?php
		}
		?>
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
	<input type="hidden" name="items_reserved[]" 				id="items_reserved[]" 					value="<?php echo $this->_models['variables']->reserve_offer_id.'|'.$this->_models['variables']->reserve_room_id.'|'.$this->_models['variables']->reserve_current?>" /> 
	<input type="hidden" name="mediaReferer" 					id="mediaReferer" 						value="<?php echo $this->_models['variables']->mediaReferer?>"/>
	<input type="hidden" name="voucher" 						id="voucher" 							value="<?php echo $this->_models['variables']->voucher?>"/>
	
	<?php
	
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
	$this->_models['variables']->displayHiddenValues( 'airport_airline_ids',			array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_type_ids', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_dates', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_hours', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_mins', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_flight_nrs', 	array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_guests', 		array('operation' => 'edit', 'type'=>'array', 'skip_value'=>$skip_key) );

	?>
	
</form>
