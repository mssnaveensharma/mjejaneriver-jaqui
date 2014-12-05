<?php 
/**
* @copyright	Copyright (C) 2008-2012 CMSJunkie. All rights reserved.
* 
*/
?>
<script>

jQuery(document).ready(function() {	

	//select all the a tag with name equal to modal
	jQuery('a[name=modal]').click(function(e) {
		//Cancel the link behavior
		e.preventDefault();
		//Get the A tag
		var id = jQuery(this).attr('href');
	
		//Get the screen height and width
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
			
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(1000);	
		jQuery('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
		//Set the popup window to center
		// jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
		// jQuery(id).css('left', winW/2-jQuery(id).width()/2);
		jQuery(id).css('top',  f_scrollTop() + 20);
		jQuery(id).css('left', winW/2-jQuery(id).width()/2);

		//transition effect
		jQuery(id).fadeIn(2000); 
	
	});
	
	//if close button is clicked
	jQuery('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#mask').hide();
		jQuery('.window').hide();
	});		
	
	//if mask is clicked
	jQuery('#mask').click(function () {
		jQuery(this).hide();
		jQuery('.window').hide();
	});	

	function f_clientWidth() {
		return f_filterResults (
			window.innerWidth ? window.innerWidth : 0,
			document.documentElement ? document.documentElement.clientWidth : 0,
			document.body ? document.body.clientWidth : 0
		);
	}
	function f_clientHeight() {
		return f_filterResults (
			window.innerHeight ? window.innerHeight : 0,
			document.documentElement ? document.documentElement.clientHeight : 0,
			document.body ? document.body.clientHeight : 0
		);
	}
	function f_scrollLeft() {
		return f_filterResults (
			window.pageXOffset ? window.pageXOffset : 0,
			document.documentElement ? document.documentElement.scrollLeft : 0,
			document.body ? document.body.scrollLeft : 0
		);
	}


	function f_scrollTop() {
		return f_filterResults (
			window.pageYOffset ? window.pageYOffset : 0,
			document.documentElement ? document.documentElement.scrollTop : 0,
			document.body ? document.body.scrollTop : 0
		);
	}	
	function f_filterResults(n_win, n_docel, n_body) {
		var n_result = n_win ? n_win : 0;
		if (n_docel && (!n_result || (n_result > n_docel)))
			n_result = n_docel;
		return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
	}

	
});



</script>
<form autocomplete='off' action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_SEARCH_RESERVATION',true); ?></legend>
			<center>
				<TABLE width="500px" border="0" cellpadding="5" align="right">
					<TR>
						<TD width=10% nowrap><?php echo JText::_('LNG_HOTEL',true)?> :</TD>
						<TD colspan=3>
							<select name='filter_hotel_id' id='filter_hotel_id' style='width:520px'>
								<option value='0' <?php echo $this->filter_hotel_id==0? " selected " : ""?> ></option>
								<?php
								
								for($i = 0; $i <  count( $this->itemsHotel ); $i++)
								{
									$hotel = $this->itemsHotel[$i]; 
									if(isset($hotel)){
										echo "<option value='$hotel->hotel_id'". ($this->filter_hotel_id==$hotel->hotel_id? 'selected' : '').">";
										echo stripslashes("$hotel->hotel_name($hotel->hotel_city)");
										echo "</option>";
									}
								}
								?>
								
								</select>
						</TD>
					</TR>
					
					<TR>
						<TD width=10% nowrap><?php echo JText::_('LNG_FIRST_NAME',true)?> :</TD>
						<TD>
							<input 
								type		='text'
								name		= 'filter_first_name'
								id			= 'filter_first_name'
								size		='48'
								maxlength	='100'		
								value		='<?php echo $this->filter_first_name?>'								
							>
						</TD>
						<TD width=10% nowrap><?php echo JText::_('LNG_LAST_NAME',true)?> :</TD>
						<TD>
							<input 
								type		='text'
								name		= 'filter_last_name'
								id			= 'filter_last_name'
								size		='48'
								maxlength	='100'			
								value		='<?php echo $this->filter_last_name?>'
							>
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap><?php echo JText::_('LNG_STATUS',true)?> :</TD>
						<TD>
							<select name='filter_status_reservation' id='filter_status_reservation' style='width:220px'>
								<option value='0' <?php echo $this->filter_status_reservation==0? " selected " : ""?> ></option>
							<?php
							
							for($i = 0; $i <  count( $this->itemsStatus ); $i++)
							{
								$status = $this->itemsStatus[$i]; 
								?>
								<option value='<?php echo $status->status_reservation_id?>' <?php echo $this->filter_status_reservation==$status->status_reservation_id? " selected " : ""?>
									style = 'background-color:<?php echo $status->bkcolor?>;color:<?php echo $status->color?>'
								>
									<?php echo $status->status_reservation_name?>
								</option>
								<?php
							}
							
							?>
						<option value='<?php echo PAYMENT_STATUS_WAITING ?>' <?php echo strcmp($this->filter_status_reservation,PAYMENT_STATUS_WAITING)==0? " selected " : ""?>
									style = 'background-color:#000;color:#FFF;'
								>
									Waiting
								</option>
								<option value='<?php echo PAYMENT_STATUS_PENDING ?>' <?php echo strcmp($this->filter_status_reservation,PAYMENT_STATUS_PENDING)==0? " selected " : ""?>
									style = 'background-color:#000;color:#FFF;'
								>
									Pending
								</option>
							</select>
						
						</TD>
						<TD width=10% nowrap><?php echo JText::_('LNG_ROOM_TYPE',true)?> :</TD>
						<TD>
							<select name='filter_room_types' id='filter_room_types' style='width:220px'>
								<option value='0' <?php echo $this->filter_room_types==0? " selected " : ""?> ></option>
							<?php
							
							for($i = 0; $i <  count( $this->itemsRoomTypes ); $i++)
							{
								$room = $this->itemsRoomTypes[$i]; 
								?>
								<option value='<?php echo $room->room_id?>' <?php echo $this->filter_room_types==$room->room_id? " selected " : ""?>>
									<?php echo $room->room_name?>
								</option>
								<?php
							}
							?>
							</select>
						</TD>
					</TR>
					<tr>
						<TD width=10% nowrap><?php echo JText::_('LNG_VOUCHER',true)?> :</TD>
						<TD>
							<input 
								type		='text'
								name		= 'filter_voucher'
								id			= 'filter_voucher'
								size		='48'
								maxlength	='100'			
								value		='<?php echo $this->filter_voucher?>'
							>
						</TD>
						
					</tr>
					<TR>
						<TD colspan=4 align="right">
							<input 
								type		='submit'
								value		='<?php echo JText::_('LNG_SEARCH_RESERVATION',true)?>'
								style		='width:180px;font-size:12px'
								onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	="this.style.cursor='default'"
								onclick		=" adminForm.view.value = '<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>';"
							>
						</TD>
						
					</TR>
					
				</TABLE>
			</center>
		</fieldset>
		<fieldset class="adminform">
				<div id="boxes">
					<TABLE class="adminlist" width=100%>
						<thead>	
							<th width='1%'>#</th>
							<th colspan=2 width='3%' align=center><B><?php echo JText::_('LNG_ID_RESERVATION',true)?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true)?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_HOTEL',true)?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_VOUCHER',true)?></B></th>
							<th width='20%' align=center><B><?php echo JText::_('LNG_PERIOD',true)?></B></th>
							<th width='38%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true)?></B></th>
							<th width='2%' align=center><B><?php echo JText::_('LNG_NUMBERS',true)?></B></th>
							<th width='8%' align=center><B><?php echo JText::_('LNG_STATUS',true)?></B></th>
							<th width='2%' align=center><B><?php echo JText::_('LNG_PENALTIES',true)?></B></th>
							<th width='2%' align=center><B><?php echo JText::_('LNG_PAYMENT',true)?></B></th>
						</thead>
						<tbody>
						<?php
						$nrcrt = 1;
						//if(0)
						//$is_create_hidden_fields = false;
						for($i = 0; $i <  count( $this->items ); $i++)
						{
							$reservation = $this->items[$i]; 
							//dmp($reservation);
							//print_r($reservation);
							$date_parts1=explode("-", $reservation->datas);   
							$date_parts2=explode("-", date('Y-m-d'));   
							//gregoriantojd() Converts a Gregorian date to Julian Day Count   
							$start_date		=	gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);   
							$end_date		=	gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);   
							$day_dif 		= 	$start_date - $end_date;
							
							$percent 		= 0;
							$value	 		= 0;
							$explication	= '';
							$days			= 0;
							$is_days		= false;
							$max_days		= 0;
							$is_max_days	= false;
							
							//dmp($this->itemsPayments);
							$payment_id_DONE 		= 0;
							$payment_id_CANCEL 		= 0;
							$payment_id_CHECKOUT 	= 0;
							foreach( $this->itemsPayments as $val )
							{
								if( $val->hotel_id != $reservation->hotel_id )	
									continue;
								if( $val->payment_type_id == CANCELED_ID )
								{
									$percent			= $val->payment_percent;
									$value				= $val->payment_value;
									$explication 		= $val->payment_name;
									$payment_id_CANCEL 	= $val->payment_id;
									if( $val->is_check_days )
										$days = $val->payment_days;		
									$is_days		= $val->is_check_days;
									//break;
								}
								else if( $val->payment_type_id == BANK_ORDER_ID ||  $val->payment_type_id == BUCKAROO_ID)
								{
									//exit;
									// $explication 	= $val->payment_name;
									$is_max_days	= $val->is_check_days;
									if( $val->is_check_days )
									{
										foreach( $this->itemPaymentProcessors as $process )
										{
											if( $process->paymentprocessor_type == PROCESSOR_BANK_ORDER )
											{
												$max_days = $process->paymentprocessor_timeout_days;		
												break;
											}
											break;
										}
									}
									//break;
								}
								else if( $val->payment_type_id == DONE_PAYMENT_ID )
								{
									$payment_id_DONE = $val->payment_id;
									$payment_id_CHECKOUT = $val->payment_id;
								}
							}
							
					
							if(strlen($reservation->room_ids) > 0 )
								$reservation->room_ids = explode(',', $reservation->room_ids);
							else
								$reservation->room_ids = array();
							
							if(strlen($reservation->option_ids) > 0 )
								$reservation->option_ids = explode(',', $reservation->option_ids);
							else
								$reservation->option_ids = array();
							
							if(strlen($reservation->package_ids) > 0 )
								$reservation->package_ids = explode(',', $reservation->package_ids);
							else
								$reservation->package_ids = array();
							
							$is_disable_status = false;
							
							$is_pending 			= false;
							$is_waiting 			= false;
							
							$is_part_payment		= false;	
							$payment_value			= 0; 
							
							$is_website_paymemt 	= false;
							$is_bank_order		 	= false;
							$is_phone_order		 	= false;
							
							$id_bank_order			= 0;

							//dmp($reservation->total);
							
							foreach( $reservation->itemPayments as $payment )
							{
								// dmp($payment);
								if(in_array($payment->payment_type_id, array(CANCELED_ID, CHECKEDOUT_ID) )  )
								{
									$is_disable_status = true;
								}
								else if(in_array($payment->payment_type_id, array(PREAUTHORIZATION_PAYMENT_ID) )  )
								{
									$reservation->is_modif = false;
								}
								
								if( $payment->payment_status==PAYMENT_STATUS_WAITING )
								{
									$is_waiting 			= true;
									if( JHotelUtil::my_round($payment->payment_value,2) != $reservation->total && $payment->payment_percent < 100 )
									{
										$is_part_payment 	= true;
										$payment_value		= $payment->payment_value;
									}
								}
								else if( $payment->payment_status==PAYMENT_STATUS_PENDING)
								{
									$is_pending 			= true;
									if( JHotelUtil::my_round($payment->payment_value,2) != $reservation->total && $payment->payment_percent < 100 )
									{
										$is_part_payment 	= true;
										$payment_value		= $payment->payment_value;
									}
									//break;
								}
								
								if( $payment->paymentprocessor_type ==  PROCESSOR_PAYPAL_EXPRESS )
								{
									$is_website_paymemt = true;
								}
								else if( $payment->paymentprocessor_type ==  PROCESSOR_BANK_ORDER )
								{
									$is_bank_order = true;
									$id_bank_order	= $payment->paymentprocessor_id;
								}
								else if( $payment->paymentprocessor_type ==  PROCESSOR_MPESA )
								{
									$is_phone_order = true;
								}
								
							}
							
						?>
						<TR class="row<?php echo $i%2 ?>"
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
							<?php echo strtotime(date('Y-m-d')) >= strtotime($reservation->datae) && $reservation->status_reservation_id == CHECKEDIN_ID ? "style='color:#FF0000'" : ""?>
						>
							<TD align=center><B><?php echo ($this->pagination->limitstart + $nrcrt++)?></B></TD>
							<TD align=center nowrap>
								<input 
									type	="radio" 
									name	="boxchecked"  
									id		="boxchecked" 
									value	="<?php echo $reservation->confirmation_id?>" 
									onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	=	"this.style.cursor='default'"
									onclick="
												adminForm.confirmation_id.value = '<?php echo $reservation->confirmation_id?>'
											" 
									style = "<?php /*echo $reservation->is_modif == 0  || $is_pending == true || $is_waiting == true ? "display:none" : ""*/?>"
									/>
							</TD>
							<TD align=center>
								<div>
									<a href='#dialog_<?php echo $reservation->confirmation_id?>' name='modal'>
										<B><?php echo JHotelUtil::getStringIDConfirmation($reservation->confirmation_id);?></b>
									</a>
								</div>
								<div id='dialog_<?php echo $reservation->confirmation_id?>' class='window'>
									<div class='info'>
										<SPAN class='title_ID'>
											<?php echo JText::_('LNG_RESERVATION',true).' : '.JHotelUtil::getStringIDConfirmation($reservation->confirmation_id)?>
										</SPAN>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' style='cursor:hand;cursor:pointer' value='Close it' class='close'/>
										<?php echo $reservation->email_confirmation?>
									</div>
								</div>
							</TD>
							<TD align=left>
								<?php
								if( $reservation->is_modif == 1 && $is_pending == false && $is_waiting == false   )
								{
								?>
								<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task=edit&confirmation_id[]='. $reservation->confirmation_id )?>'
									title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true)?>"
									<?php echo strtotime(date('Y-m-d')) >= strtotime($reservation->datae) && $reservation->status_reservation_id == CHECKEDIN_ID ? "style='color:#FF0000'" : ""?>
								>
									<B><?php echo $reservation->first_name.' '.$reservation->last_name?></B>
								</A>
								<?php
								}
								else
								{
								?>
								<B><?php echo $reservation->first_name.' '.$reservation->last_name?></B>
								<?php
								}
								?>
							</TD>
							<TD align=center>
								<?php echo stripslashes($reservation->hotel_name)?>
							</TD>
							<TD align=center>
								<?php echo stripslashes($reservation->voucher)?>
							</TD>
							<TD align=center>
								<?php echo JHotelUtil::getDateGeneralFormat($reservation->datas)?>
								<>
								<?php echo JHotelUtil::getDateGeneralFormat($reservation->datae)?>
							</TD>
							<TD align=center>
								<a
									<?php echo strtotime(date('Y-m-d')) >= strtotime($reservation->datae) && $reservation->status_reservation_id == CHECKEDIN_ID ? "style='color:#FF0000'" : ""?>
									href	='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task=info&confirmation_id[]='. $reservation->confirmation_id )?>'
								>
								<?php echo JText::_('LNG_ADULT_S',true)?> : <?php echo $reservation->total_adult?>
								,
								<?php echo JText::_('LNG_CHILD_S',true)?> : <?php echo $reservation->guest_child?>
								,
								<?php echo JText::_('LNG_ROOMS',true)?> : <?php echo $reservation->rooms?> 
								....
								</a>
							</TD>
							<TD align=center>
								<?php
								if( $is_pending == true || $is_waiting == true  || $is_disable_status == true )
								{
									echo "&nbsp;";
								}
								else
								{
									
									if( 
										$reservation->status_reservation_id == CHECKEDOUT_ID
										//||
										// $reservation->payment_penalty_percent != 0 
										// || 
										//$reservation->confirmation_payment_status == PAYMENT_STATUS_PAYED 
									)
									{
										echo "&nbsp;";
									}
									else
									{
									?>
									<a href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task=assign_number_rooms&confirmation_id[]='. $reservation->confirmation_id )?>'>
									<img border= 1 
										width=16px
										height=16px
										src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/number_room.gif" ?>" 
									/>
									</a>
									<?php
									}
								}
								?>
							</TD>
							<TD align=center 
								nowrap
								style		= 	"background-color:<?php echo $reservation->bkcolor?>;color:<?php echo $reservation->color?>"
							>
								<?php
								if( $is_pending == true || $is_waiting == true  )
								{
									if( $is_waiting )
									{
										$dif 		= strtotime('now') - strtotime($reservation->data);
										$days		= floor($dif / (60*60*24));//round(($dif % 604800) / 86400);
										$hours 		= gmdate('H', $dif);
										$minutes 	= date('i', $dif);
										$sec 		= date('s', $dif);
										
										echo "<B>".strtoupper(PAYMENT_STATUS_WAITING)."</B>";
										echo "<BR>".$reservation->data;
										//echo "<BR>".round((strtotime('now') - strtotime($reservation->data)) / 60,2)." ".JText::_('LNG_MINUTES',true);
										//echo "<BR>".($day > 0 		? $day : "");
										//echo "<BR>".($hours > 0 	? $hours : "");
										if( strlen($hours)==1)
											$hours = "0$hours";
										if( strlen($minutes)==1)
											$minutes = "0$minutes";
										if( strlen($sec)==1)
											$sec = "0$sec";

										echo "<BR>".$days."&nbsp;".$hours.":".$minutes.":".$sec."&nbsp;";
										echo JText::_('LNG_TIME_LEFT',true);
										if( $is_part_payment )
										{
										?>
										<select 
											name	='confirmation_payment_status_<?php echo $reservation->confirmation_id?>' 
											id		='confirmation_payment_status_<?php echo $reservation->confirmation_id?>' 
											style	='width:90px'
											
											onchange	= 	'
																if( this.value == "<?php echo $reservation->confirmation_payment_status?>" )
																	return;
																if( this.value == "<?php echo PAYMENT_STATUS_PAYED?>" )
																{
																	if( confirm("<?php echo JText::_('LNG_DO_YOU_WANT_TO_MARK_THIS_PAYMENT_AS_PAYED',true)?>',true) )
																	{
																		document.adminForm.confirmation_payment_status.value 			= "<?php echo PAYMENT_STATUS_PAYED?>";
																		document.adminForm.payment_id.value								= "<?php echo $payment_id_DONE ?>";
																		document.adminForm.payment_type_id.value						= "<?php echo DONE_PAYMENT_ID?>";
																	}
																	else
																	{
																		this.value = "<?php echo $reservation->confirmation_payment_status?>"
																		return false;
																	}
																}
																<?php
																if( $is_waiting == true  )
																{
																?>
																//alert(0);
																document.adminForm.status_reservation_id.value				= "<?php echo RESERVED_ID?>";
																document.adminForm.change_confirmation_payment_status.value	= "2";
																document.adminForm.payment_explication.value				= "Manual";
																document.adminForm.payment_processor_sel_id.value			= "<?php echo $id_bank_order?>";
																document.adminForm.payment_processor_sel_type.value			= "<?php echo PROCESSOR_BANK_ORDER?>";
																<?php
																}
																else
																{
																?>
																document.adminForm.status_reservation_id.value				= "<?php echo $reservation->status_reservation_id?>";
																document.adminForm.change_confirmation_payment_status.value	= "1";
																<?php
																}
																?>
																// return false;
																document.adminForm.payment_value.value						= "<?php echo $payment_value?>";
																document.adminForm.is_part_payment.value					= "1";
																document.adminForm.task.value								= "save";
																document.adminForm.view.value								= "managereservations";
																document.adminForm.confirmation_id.value					= "<?php echo $reservation->confirmation_id?>";
																document.adminForm.total.value								= "<?php echo $reservation->total?>";
																document.adminForm.hotel_id.value							= "<?php echo $reservation->hotel_id?>";
																document.adminForm.email.value								= "<?php echo $reservation->email?>";
																document.adminForm.total_payed.value						= "<?php echo $reservation->total_payed?>";
																document.adminForm.submit();
																
															'
										>
											<?php 
											if(  $reservation->confirmation_payment_status != PAYMENT_STATUS_PAYED )
											{
											?>
											<option 
												value		= '<?php echo PAYMENT_STATUS_NOTPAYED?>' 
												<?php echo $reservation->confirmation_payment_status==PAYMENT_STATUS_NOTPAYED? " selected " : ""?>
											>
												<?php echo JText::_('LNG_NOT_PAYED',true)?>
											</option>
											<?php
											}
											?>
											<option 
												value		= '<?php echo PAYMENT_STATUS_PAYED?>' 
												<?php echo $reservation->confirmation_payment_status==PAYMENT_STATUS_PAYED? " selected " : ""?>
											>
												<?php echo JText::_('LNG_PAYED',true)?>
											</option>
											
										</select>
										<?php
										}
									}
									else if( $is_pending )
									{
										$dif 	= strtotime('now') - strtotime($reservation->data);
										
										$day 		= round(($dif % 604800) / 86400);
										$hours 		= round((($dif % 604800) % 86400) / 3600);
										$minutes 	= round(((($dif % 604800) % 86400) % 3600) / 60);
										$sec 		= round((((($dif % 604800) % 86400) % 3600) % 60));
										echo "<B>".strtoupper(PAYMENT_STATUS_PENDING)."</B>";
										echo "<BR>".$reservation->data;
										//echo "<BR>".round((strtotime('now') - strtotime($reservation->data)) / 60,2)." ".JText::_('LNG_MINUTES',true);
										//echo "<BR>".($day > 0 		? $day : "");
										//echo "<BR>".($hours > 0 	? $hours : "");
										if( strlen($hours)==1)
											$hours = "0$hours";
										if( strlen($minutes)==1)
											$minutes = "0$minutes";
										if( strlen($sec)==1)
											$sec = "0$sec";

										echo "<BR>".$hours.":".$minutes.":".$sec."&nbsp;";
										echo JText::_('LNG_TIME_LEFT',true);
									}
									
								}
								else
								{
									$is_accept_card 					= false;
									$payment_card_processor_sel_type 	= '';
									$payment_card_processor_sel_id 		= 0;
									$payment_cash_processor_sel_type 	= '';
									$payment_cash_processor_sel_id 		= 0;
									//print_r($reservation);
									if(count($reservation->itemPayments)>0){
										$payment_card_processor_sel_type 	= $reservation->itemPayments[count($reservation->itemPayments)-1]->paymentprocessor_type;
										$payment_card_processor_sel_id 		=$reservation->itemPayments[count($reservation->itemPayments)-1]->paymentprocessor_id;
									}
									if( $payment_card_processor_sel_type == PROCESSOR_PAYFLOW ||$payment_card_processor_sel_type == PROCESSOR_AUTHORIZE)
									{
										$is_accept_card 					= true;
									}
									else if( $payment_card_processor_sel_type == PROCESSOR_CASH)
									{
										$payment_cash_processor_sel_type 	=$payment_card_processor_sel_type;
										$payment_cash_processor_sel_id 		=$payment_card_processor_sel_id;
									}
									?> <?php //echo $payment_card_processor_sel_id?>
									<select 
										name	='status_reservation_id_<?php echo $reservation->confirmation_id?>' 
										id		='status_reservation_id_<?php echo $reservation->confirmation_id?>'
										style1	='width:170px'
										<?php  $is_disable_status? " disabled " : ""?>
										onchange	= 	'
															if( this.value == "<?php echo $reservation->status_reservation_id?>" )
																return;
															if( this.value == "<?php echo CANCELED_ID?>" )
															{
																if( confirm("<?php echo JText::_('LNG_DO_YOU_WANT_TO_CANCEL_THIS_RESERVATION',true)?>',true) )
																{
																	document.adminForm.tip_confirmation_payment_status.value 		= "cash";
																	document.adminForm.payment_type_id.value						= "<?php echo CANCELED_ID?>";
																	document.adminForm.payment_id.value 							= "<?php echo $payment_id_CANCEL?>";
																		
																	if( 
																		<?php echo ($is_days==false || $day_dif <= $days || $percent ==0 )? '1' : '0'?>
																	)
																	{
																		<?php
																		if( $reservation->is_enable_payment  && $is_accept_card )	
																		{
																		?>
																		if( confirm("<?php echo JText::_('LNG_USE_CREDIT_CARD',true)?>',true))
																		{
																			document.adminForm.tip_confirmation_payment_status.value 		= "card";
																			document.adminForm.payment_processor_sel_type.value				= "<?php echo $payment_card_processor_sel_type?>"
																			document.adminForm.payment_processor_sel_id.value				= "<?php echo $payment_card_processor_sel_id?>"
																			
																		}
																		<?php
																		}
																		?>
																		
																		document.adminForm.payment_percent.value 			= "<?php echo $percent?>";
																		document.adminForm.payment_explication.value 		= "<?php echo $explication?>";
																		document.adminForm.payment_percent.value 			= prompt( "<?php echo JText::_('LNG_CANCELATION_FEE_PERCENT",true)?> (<?php echo JText::_('LNG_DEFAULT',true)?> <?php echo $percent?> %) ',"<?php echo $percent?>" );
																		
																		
																		myRegExp = /[0-9]+/g;
																		myNumber = new String(document.adminForm.payment_percent.value);

																		if(myRegExp.test(myNumber)) 
																		{
																			document.adminForm.confirmation_payment_status.value 			= "<?php echo PAYMENT_STATUS_PAYED?>";
																		}
																		else
																		{
																			this.value = "<?php echo $reservation->status_reservation_id?>";
																			alert("<?php echo JText::_('LNG_PERCENT_ERROR',true)?>',true);
																			return false;
																		}
																	}
																}
																else
																{
																	this.value = "<?php echo $reservation->status_reservation_id?>";
																	return false;
																}
															}
															else if( this.value == "<?php echo CHECKEDOUT_ID?>" )
															{
																	if( confirm("<?php echo JText::_('LNG_DO_YOU_WANT_TO_CHECKED_OUT_THIS_RESERVATION',true)?>',true) )
																	{
																		<?php
																		if( JHotelUtil::my_round($reservation->total,2) == JHotelUtil::my_round($reservation->total_payed,2)  )
																		{
																		
																		}
																		else
																		{
																			?>
																			document.adminForm.tip_confirmation_payment_status.value 			= "cash";
																			<?php
																			if( $reservation->is_enable_payment && $is_accept_card )	
																			{
																			?>
																			if( confirm("<?php echo JText::_('LNG_USE_CREDIT_CARD',true)?>',true))
																			{
																				document.adminForm.tip_confirmation_payment_status.value 		= "card";
																				document.adminForm.payment_processor_sel_type.value				= "<?php echo $payment_card_processor_sel_type?>"
																				document.adminForm.payment_processor_sel_id.value				= "<?php echo $payment_card_processor_sel_id?>"
																				
																			}
																			<?php
																			}
																		}
																		?>
																		document.adminForm.payment_type_id.value					= "<?php echo $reservation->status_reservation_id?>";
																		document.adminForm.payment_id.value 						= "<?php echo $payment_id_CHECKOUT?>";
																		document.adminForm.confirmation_payment_status.value 		= "<?php echo PAYMENT_STATUS_PAYED?>";
																	}
																	else
																	{
																		this.value = "<?php echo $reservation->status_reservation_id?>";
																		return false;
																	}

															}
															// return false;
															document.adminForm.is_enable_payment.value 		= "<?php echo $reservation->is_enable_payment?>";
															document.adminForm.status_reservation_id.value	= this.value;
															document.adminForm.is_status.value				= "1";
															document.adminForm.task.value					= "save";
															document.adminForm.view.value					= "managereservations";
															document.adminForm.confirmation_id.value		= "<?php echo $reservation->confirmation_id?>";
															document.adminForm.total.value					= "<?php echo $reservation->total?>";
															document.adminForm.hotel_id.value				= "<?php echo $reservation->hotel_id?>";
															document.adminForm.email.value					= "<?php echo $reservation->email?>";
															document.adminForm.total_payed.value			= "<?php echo $reservation->total_payed?>";
																												
															document.adminForm.submit();
															//return false;
															
														'
									>
									<?php
									
									
									
									for($k = 0; $k <  count( $this->itemsStatus ); $k++)
									{
										$status = $this->itemsStatus[$k]; 
										if($is_disable_status  && $reservation->status_reservation_id != $status->status_reservation_id )
										{
											continue;
										}
										else
										{
											// if( $reservation->total != $reservation->total_payed && $status->status_reservation_id == CHECKEDOUT_ID )
												// continue;
											
											if(
												$reservation->total == $reservation->total_payed 
												&& 
												(
													$status->status_reservation_id != CHECKEDOUT_ID
													&&
													$reservation->status_reservation_id != $status->status_reservation_id
													&&
													in_array($status->status_reservation_id, array(CANCELED_ID) )
												)
												
											)
												continue;
										}
										?>
										<option 
											value		= '<?php echo $status->status_reservation_id?>' 
											<?php echo $reservation->status_reservation_id==$status->status_reservation_id? " selected " : ""?>
											style 		= 'background-color:<?php echo $status->bkcolor?>;color:<?php echo $status->color?>;width:90px'
											
										>
											<?php echo $status->status_reservation_name?>
										</option>
										<?php
									}
									?>
									</select>
								<?php
								}
								?>

							</TD>
							<TD align=center nowrap>
								<?php
								if( $is_pending == true || $is_waiting == true  )
								{
									echo "&nbsp;";
								}
								else
								{
									$info = '';
									foreach( $reservation->itemPayments as $p )
									{
										if( $p->payment_type_id == PENALTY_PAYMENT_ID )
										{
											if( strlen($info) > 0 )
												$info .=" | ";
											$info .= 'Payment '.$p->payment_explication.' '.$p->payment_percent.' %';
										}
									}

									if( 
										$reservation->status_reservation_id 		== CHECKEDOUT_ID
										// ||
										// $reservation->payment_penalty_percent != 0 
										|| 
										$reservation->confirmation_payment_status 	== PAYMENT_STATUS_PAYED 
									)
									{
										?>
										<img border= 1 
											width		=16px
											height		=16px
											title		='<?php echo $info?>'
											src 		="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/penalties_".( strlen($info) == 0 ? "no" : "ok").".gif" ?>" 
										/>
										<?php
									}
									else
									{
									?>
									<a href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task=penalties&confirmation_id[]='. $reservation->confirmation_id )?>'>
										<img border= 1 
											width=16px
											height=16px
											title='<?php echo $info?>'
											src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/penalties_".(strlen($info) == 0 ? "no" : "ok").".gif" ?>" 
										/>
									</a>
									<?php
									}
								}
								?>

								
							</TD>
							
							<TD align=center nowrap
								style		= 	"background-color:<?php echo $reservation->confirmation_payment_status !== PAYMENT_STATUS_PAYED? "#EEEEEE" : "#99FF99"?>"
							>
								<?php
								if( $is_waiting == false  )
								{
									echo "&nbsp;";
								}
								if( $is_pending == true )
								{
									echo "&nbsp;";
								}
								else
								{
								?>
								<select 
									name	='confirmation_payment_status_<?php echo $reservation->confirmation_id?>' 
									id		='confirmation_payment_status_<?php  echo $reservation->confirmation_id?>' 
									style	='width:90px'
									
									onchange	= 	'
														if( this.value == "<?php echo $reservation->confirmation_payment_status?>" )
															return;
														if( this.value == "<?php echo PAYMENT_STATUS_PAYED?>" )
														{
															if( confirm("<?php echo JText::_('LNG_DO_YOU_WANT_TO_MARK_THIS_RESERVATION_AS_PAYED',true)?>',true) )
															{
																document.adminForm.confirmation_payment_status.value 			= "<?php echo PAYMENT_STATUS_PAYED?>";
																document.adminForm.payment_id.value								= "<?php echo $payment_id_DONE ?>";
																document.adminForm.payment_type_id.value						= "<?php echo DONE_PAYMENT_ID?>";
															}
															else
															{
																this.value = "<?php echo $reservation->confirmation_payment_status?>"
																return false;
															}
														}
														<?php
														if( $is_waiting == true  )
														{
														?>
														//alert(0);
														document.adminForm.status_reservation_id.value				= "<?php echo RESERVED_ID?>";
														document.adminForm.change_confirmation_payment_status.value	= "2";
														document.adminForm.payment_explication.value				= "Manual";
														document.adminForm.payment_processor_sel_id.value			= "<?php echo $id_bank_order?>";
														document.adminForm.payment_processor_sel_type.value			= "<?php echo PROCESSOR_BANK_ORDER?>";
														<?php
														}
														else
														{
														?>
														document.adminForm.status_reservation_id.value				= "<?php echo $reservation->status_reservation_id?>";
														document.adminForm.change_confirmation_payment_status.value	= "1";
														<?php
														}
														?>
														// return false;
														document.adminForm.task.value								= "save";
														document.adminForm.view.value								= "managereservations";
														document.adminForm.confirmation_id.value					= "<?php echo $reservation->confirmation_id?>";
														document.adminForm.total.value								= "<?php echo $reservation->total?>";
														document.adminForm.hotel_id.value							= "<?php echo $reservation->hotel_id?>";
														document.adminForm.email.value								= "<?php echo $reservation->email?>";
														document.adminForm.total_payed.value						= "<?php echo $reservation->total_payed?>";
														document.adminForm.submit();
														
													'
								>
									<?php 
									if(  $reservation->confirmation_payment_status != PAYMENT_STATUS_PAYED )
									{
									?>
									<option 
										value		= '<?php echo PAYMENT_STATUS_NOTPAYED?>' 
										<?php echo $reservation->confirmation_payment_status==PAYMENT_STATUS_NOTPAYED? " selected " : ""?>
									>
										<?php echo JText::_('LNG_NOT_PAYED',true)?>
									</option>
									<?php
									}
									?>
									<option 
										value		= '<?php echo PAYMENT_STATUS_PAYED?>' 
										<?php echo $reservation->confirmation_payment_status==PAYMENT_STATUS_PAYED? " selected " : ""?>
									>
										<?php echo JText::_('LNG_PAYED',true)?>
									</option>
									
								</select>
								<?php
								}
								?>
							</TD>
							
						</TR>
						<?php
						}
						?>
						</tbody>
						<tfoot>
						    <tr>
						      <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
						    </tr>
						 </tfoot>	
					</TABLE>
				</div>
				<div id="mask"></div>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" 															value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="total" 															value="0" />
	<input type="hidden" name="total_payed" 													value="0" />
	<input type="hidden" name="payment_type_id"													value="" />
	<input type="hidden" name="payment_id" 														value="" />
	<input type="hidden" name="hotel_id" 														value="" />
	<input type="hidden" name="email" 															value="" />
	<input type="hidden" name="is_status" 														value="" />
	<input type="hidden" name="payment_percent" 												value="" />
	<input type="hidden" name="payment_value" 													value="" />
	<input type="hidden" name="tip_confirmation_payment_status" 								value="" />
	<input type="hidden" name="status_reservation_id" 											value="" />
	<input type="hidden" name="confirmation_payment_status" 									value="" />
	<input type="hidden" name="change_confirmation_payment_status" 								value="" />
	<input type="hidden" name="paymentprocessor_id" 											value="" />
	<input type="hidden" name="paymentprocessor_type" 											value="" />
	<input type="hidden" name="is_enable_payment" 												value="" />
	<input type="hidden" name="is_part_payment" 												value="" />
	<input type='hidden' name='payment_processor_sel_type' 	id='payment_processor_sel_type'	 	value='<?php echo isset($payment_cash_processor_sel_type)?$payment_cash_processor_sel_type:'' ?>' />
	<input type='hidden' name='payment_processor_sel_id' 	id='payment_processor_sel_id' 		value='<?php echo isset($payment_cash_processor_sel_id)?$payment_cash_processor_sel_id:'' ?>' />
	<input type="hidden" name="payment_explication" 											value="" />
	<input type="hidden" name="payment_explication" 											value="" />
	<input type="hidden" name="task" 															value="" />
	<input type="hidden" name="view" id='view' 													value="managereservations" />
	<input type="hidden" name="confirmation_id" 												value="" />
	<input type="hidden" name="controller" 														value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
		
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
			var form = document.adminForm;
			if(pressbutton == 'back'){
				jQuery("#view").val('');
			}
			if (pressbutton == 'edit' || pressbutton == 'Delete') 
			{
				var isSel = false;
				
				if( form.elements['boxchecked'].length == null )
				{
					if(form.elements['boxchecked'].checked)
					{
						isSel = true;
					}
				}
				else
				{
					for( i = 0; i < form.boxchecked.length; i ++ )
					{
						
						if(form.elements['boxchecked'][i].checked)
						{
							isSel = true;
							break;
						}
					}
				}
				
				if( isSel == false )
				{
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true)?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>
</form>