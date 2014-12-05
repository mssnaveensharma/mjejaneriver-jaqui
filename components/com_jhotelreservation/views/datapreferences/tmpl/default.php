<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" id="userForm" >
	<div class="hotel_reservation">
		<table width=100% class="admintable">
			<?php
			if( JRequest::getVar( 'IS_ERROR_SSL') == '1' )	
			{
			?>
				<tr>
					<TD colspan=3 width=33% valign=top align=center 
						class='INFO_REDIRECT_HTTPS'
					>
						<B><?php echo JText::_('LNG_REDIRECT_TO_HTTP_SECURE_CONNECTION_IN_FEW_SECONDS',true);?></B>
					</TD>
				</TR>	
			<?php
			}
			else
			{
				if( strlen(JRequest::getVar( 'infoCheckAvalability'))  > 0 )	
				{
				?>
				<tr>
					<TD colspan=3 width=33% valign=top align=center 
						class='INFO_CHECK_AVALABILITY'
					>
						<B><?php echo htmlspecialchars_decode(JRequest::getVar( 'infoCheckAvalability', '', 'post', 'word', JREQUEST_ALLOWRAW ) )?></B>
					</TD>
				</TR>	
				<?php
				}			
				?>
				<tr> 
					<td valign="top">
						<table 
							name 	= 'table_parameters'
							id 		= 'table_parameters'
							STYLE	='<?php echo $this->_models['variables']->tip_oper==1 ? "display:none" : "" ?>'
							width	=100% 
							class	="table_calendar" 
						>
							
							<tr class="tr_calendar">
								<th align=center colspan=5>
									<?php echo JText::_('LNG_CHECK_AVAILABILITY',true);?>
								</th>
							</TR>
							<tr>
								<TD align=center>
									<B><?php echo JText::_('LNG_HOTEL',true)?></B>
								</TD>
								<td align=left colspan=4>
									<a href="javascript:"  
										onClick="return showHotel(true);" 
										onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
									>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_name?>
										( <?php echo $this->_models['variables']->itemHotelSelected->hotel_city?> )
									</a> 
								</TD>
							</TR>
							<tr>
								<TD align=center><B><?php echo JText::_('LNG_ARIVAL',true);?></B></TD>
								<TD>
									<?php
									$nameDay = date('D');
									if( $this->_models['variables']->tip_oper == 1 )
									{
										$nameDay = date('D', strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start));
										
									}
									?>
									<input 
										id 		= 'day_name_start'
										name 	= 'day_name_start'
										readonly 
										size	= 3
										value	= '<?php echo substr(JText::_( substr(strtoupper($nameDay),0,3) ), 0, 3)?>' 
										style1	= 'text-align:center'
										class 	= 'DAY_NAME_START'
									/>
								</TD>
								<TD>
									<select
										id 			= 'year_start'
										name 		= 'year_start'
										
										onchange 	=	"
															var form 		= document.getElementById('userForm');
															var yObj	 	= form.elements['year_start'];
															var mObj	 	= form.elements['month_start'];	
															JHotelReservationCalendar('td_data_calendar_1', mObj.value, yObj.value, '<?php echo _PATH_IMG?>');
															createControls('td_data_calendar_1', 1, mObj.value, yObj.value, '<?php echo _PATH_IMG?>', true);
															updateCalendars('td_data_calendar_1','<?php echo _PATH_IMG?>' );
															markSelectInterval();
														"
									>
										<?php
										$Y 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->year_start : date('Y');
										$max	= $this->_models['variables']->tip_oper == 1? 0 : 10;
										for($i=$Y;$i<=$Y+$max;$i++)
										{
										?>
										<option value='<?php echo $i?>'  <?php echo $this->_models['variables']->year_start ==$i? "selected" : ""?> >
											<?php echo $i?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
								<TD>
									<select
										id 			= 'month_start'
										name 		= 'month_start'
										onchange 	=	"
															var form 		= document.getElementById('userForm');
															var yObj	 	= form.elements['year_start'];
															var mObj	 	= form.elements['month_start'];	
															
															JHotelReservationCalendar('td_data_calendar_1', mObj.value, yObj.value, '<?php echo _PATH_IMG?>');
															createControls('td_data_calendar_1', 1, mObj.value, yObj.value, '<?php echo _PATH_IMG?>', true);
															updateCalendars('td_data_calendar_1','<?php echo _PATH_IMG?>' );
															markSelectInterval();
														"
									>
										<?php
										$m 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->month_start : 1;
										$max 	= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->month_start : 12;
										for($i=$m+0;$i<=$max+0;$i++)
										{
										?>
										<option 
											<?php echo strtotime(date('Y-m-01')) > strtotime( $this->_models['variables']->year_start."-".($i>9? $i :"0$i")."-01" )? " disabled " : ""?>
											value='<?php echo $i?>' <?php echo $this->_models['variables']->month_start ==$i? "selected" : ""?> 
										>
											<?php echo JText::_( strtoupper(date("F", strtotime("1970-$i-01"))) )?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
								<TD colspan=1>
									<select
										id 			= 'day_start'
										name 		= 'day_start'
										onchange 	=	"
															selectDay('td_data_calendar_1');
															markSelectInterval();
														"
									>
										<?php
										$d 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->day_start : 1;
										$max 	= $this->_models['variables']->tip_oper == 1? 
													$this->_models['variables']->day_start 
													: 
													date('t', strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start));
													
										
										for($i=$d+0;$i<=$max+0;$i++)
										{
										?>
										<option 
											value='<?php echo $i?>'
											<?php echo strtotime(date('Y-m-d')) > strtotime( $this->_models['variables']->year_start."-".$this->_models['variables']->month_start."-".($i>9? $i :"0$i") )? " disabled " : ""?>
											<?php echo $this->_models['variables']->day_start ==$i? " selected " : ""?> 
											
										>
											<?php echo strlen($i) ==1 ? "0$i" : "$i"?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
							</TR>
							<tr>
								<TD align=center><B><?php echo JText::_('LNG_DEPARTURE',true);?></B></TD>
								<TD>
									<?php 	
									$nameDay = date('D', strtotime(' +1 day '));
									if( $this->_models['variables']->tip_oper == 1 )
									{
										$nameDay = date('D', strtotime( $this->_models['variables']->year_end.'-'.$this->_models['variables']->month_end.'-'.$this->_models['variables']->day_end));
									}
									?>
									<input 
										id 		= 'day_name_end'
										name 	= 'day_name_end'
										readonly 
										size	= 3 
										value	= '<?php echo substr(JText::_( substr(strtoupper($nameDay),0,3)), 0, 3)?>' 
										class 	= 'DAY_NAME_START'
									/>
								</TD>
								<TD>
									<select
										id 			= 'year_end'
										name 		= 'year_end'
										onchange 	=	"
															var form 		= document.getElementById('userForm');
															var yObj	 	= form.elements['year_end'];
															var mObj	 	= form.elements['month_end'];	
															JHotelReservationCalendar('td_data_calendar_2', mObj.value, yObj.value, '<?php echo _PATH_IMG?>');
															createControls('td_data_calendar_2', 1, mObj.value, yObj.value, '<?php echo _PATH_IMG?>', true);
															updateCalendars('td_data_calendar_2','<?php echo _PATH_IMG?>' );
															markSelectInterval();
														"
									>
										<?php
										$Y 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->year_end : date('Y', strtotime(' + 1 day' ));
										$max 	= $this->_models['variables']->tip_oper == 1? 0 : 10;
										
										for($i=$Y;$i<=$Y+$max;$i++)
										{
										?>
										<option 
											value='<?php echo $i?>'
											<?php echo $this->_models['variables']->year_end ==$i? "selected" : ""?> 
										><?php echo $i?></option>
										<?php
										}
										?>
									</select>
								</TD>
								<TD>
									<select
										id 			= 'month_end'
										name 		= 'month_end'
										onchange 	=	"
															var form 		= document.getElementById('userForm');
															var yObj	 	= form.elements['year_end'];
															var mObj	 	= form.elements['month_end'];	
															JHotelReservationCalendar('td_data_calendar_2', mObj.value, yObj.value, '<?php echo _PATH_IMG?>');
															createControls('td_data_calendar_2', 1, mObj.value, yObj.value, '<?php echo _PATH_IMG?>', true);
															updateCalendars('td_data_calendar_2','<?php echo _PATH_IMG?>' );
															markSelectInterval();
														"
									>
										<?php
										$m 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->month_end : 1;
										$max 	= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->month_end : 12;
										
										for($i=$m+0;$i<=$max+0;$i++)
										{
										?>
										<option 
											<?php echo strtotime(date('Y-m-01')) > strtotime( $this->_models['variables']->year_end."-".($i>9? $i :"0$i")."-01"  )? " disabled " : ""?>
											value='<?php echo $i?>' 
											<?php echo $this->_models['variables']->month_end ==$i? "selected" : ""?> 
										>
											<?php echo JText::_( strtoupper(date("F", strtotime("1970-$i-01")) ) )?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
								<TD colspan=1>
									<select
										id 			= 'day_end'
										name 		= 'day_end'
										onchange 	=	"
															selectDay('td_data_calendar_2');
															markSelectInterval();
														"
									>
										<?php
										$d 		= $this->_models['variables']->tip_oper == 1? $this->_models['variables']->day_end : 1;
										$max 	= $this->_models['variables']->tip_oper == 1? 
													$this->_models['variables']->day_end 
														: 
													date('t', strtotime( $this->_models['variables']->year_end.'-'.$this->_models['variables']->month_end.'-'.$this->_models['variables']->day_end));
										
										for($i=$d+0;$i<=$max+0;$i++)
										{
										?>
										<option 
											value='<?php echo $i?>'
											<?php echo strtotime(date('Y-m-d')) > strtotime( $this->_models['variables']->year_end."-".$this->_models['variables']->month_end."-".($i>9? $i :"0$i") )? " disabled " : ""?>
											<?php echo $this->_models['variables']->day_end ==$i? "selected" : ""?> 
											
										>
											<?php echo strlen($i) ==1 ? "0$i" : "$i"?>
										</option>
										<?php
										}
										?>
									</select>
								</TD>
							</TR>
							<tr>
								<TD align=center><B><?php echo JText::_('LNG_ROOMS',true);?></B></TD>
								<TD colspan=4 align=left>
									<select id='rooms' name='rooms'>
										<?php
										$i_min = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->rooms  : 1;
										$i_max = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->rooms  : 10;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
										<option <?php echo $this->_models['variables']->rooms ==$i? "selected" : ""?>  value='<?php echo $i?>'><?php echo $i?></option>
										<?php
										}
										?>
									</select>
								</TD>
							</TR>
							<tr>
								<TD align=center><B>&nbsp;</B></TD>
								<TD align=left colspan=2>
									<?php echo JText::_('LNG_ADULTS_19',true);?>
									
								</TD>
								<TD align=left colspan=2>
									<?php echo JText::_('LNG_CHILDREN_0_18',true);?>
								</TD>
							</TR>
							<tr>
								<TD align=center><B><?php echo JText::_('LNG_GUEST',true);?></B></TD>
								<TD colspan=2>
									<select name='guest_adult' id='guest_adult'>
										<?php
										$i_min = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->guest_adult  : 1;
										$i_max = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->guest_adult  : 10;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
										<option value='<?php echo $i?>' <?php echo $this->_models['variables']->guest_adult ==$i? "selected" : ""?> ><?php echo $i?></option>
										<?php
										}
										?>
									</select>
								</TD>
								<TD colspan=2>
									<select name='guest_child' id='guest_child'>
										<?php
										$i_min = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->guest_child  : 0;
										$i_max = $this->_models['variables']->tip_oper == 1? $this->_models['variables']->guest_child  : 10;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
										<option value='<?php echo $i?>' <?php echo $this->_models['variables']->guest_child ==$i? "selected" : ""?> ><?php echo $i?></option>
										<?php
										}
										?>
									</select>
								</TD>
							</TR>
							<tr>
								<TD colspan=5>
									<BR>
								</TD>
							</TR>
							<!--
							<tr>
								<td align=center colspan=5>
									<div class="button_holder">
										<div class="btn_general"> 
											<a href="javascript false;"  
												onClick="return showHotel(true);" 
												onmouseover="this.style.cursor='hand';this.style.cursor='pointer'">
												<?php echo JText::_('LNG_HOTEL_DESCRIPTIONS',true);?>
											</a> 
										</div>
									</div>
								</TD>
							</TR>
							
							<tr>
								<TD colspan=2 align=center><B>Coupon code</B></TD>
								<TD colspan=3>
									<input name='coupon_code' id='coupon_code' type='text' size=8 value='<?php echo $this->_models['variables']->coupon_code?>'  /> 
								</TD>
							</TR>
							-->
						</table>
					</TD>
					<td width='<?php echo $this->_models['variables']->tip_oper==1 ? "100" : "66" ?>%' valign="top">
						<TABLE  border=0 width=100%>
							<TR name = 'tr_calendar_script'  id = 'tr_calendar_script' STYLE='<?php echo $this->_models['variables']->tip_oper==1 ? "display:none" : "" ?>'>
								<TD valign=top name='td_data_calendar_1' class="tdCalendar1" id='td_data_calendar_1'>
								</TD>
								<TD valign=top name='td_data_calendar_2' class="tdCalendar2" id='td_data_calendar_2'>
								</TD>
							</TR>
							<TR 
								name = 'tr_show_dates' 
								id = 'tr_show_dates'  
								STYLE='<?php echo $this->_models['variables']->tip_oper==0 ? "display:none" : "" ?>'
								class='table_calendar'
							>
								<TD width=100% colspan=2 valign=middle align=left>
									<div class="reservation_period">
										<TABLE cellpadding="5" cellspacing="5">
											<TR>
												<TD>
													<div style='padding-top:5px;height:20px;text-align:center;font-weight:bold;'><?php echo JText::_('LNG_RESERVATION_PERIOD',true)?></div>
												</TD>
												<TD>
													<?php 
																										$data_1 = strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start );
							
													echo JText::_( substr(strtoupper(date( 'l', $data_1 )),0,3));//date( 'F', $data_1 );
													echo ',';
													echo JText::_( strtoupper(date( 'F', $data_1 )));//date( 'F', $data_1 );
													echo ',';
													echo date( ' d, Y', $data_1 );

													//echo date( 'l, F d, Y', strtotime( $this->_models['variables']->year_start.'-'.$this->_models['variables']->month_start.'-'.$this->_models['variables']->day_start ) )
												?> 	
												- 	
												<?php 
													$data_2 = strtotime( $this->_models['variables']->year_end.'-'.$this->_models['variables']->month_end.'-'.$this->_models['variables']->day_end );
							
													echo JText::_( substr(strtoupper(date( 'l', $data_2 )),0,3));//date( 'F', $data_2 );
													echo ',';
													echo JText::_( strtoupper(date( 'F', $data_2 )));//date( 'F', $data_2 );
													echo ',';
													echo date( ' d, Y', $data_2 );
													
													//echo date( 'l, F d, Y', strtotime( $this->_models['variables']->year_end.'-'.$this->_models['variables']->month_end.'-'.$this->_models['variables']->day_end ) )
												?>
												
												</TD>
												<td nowrap=nowrap>
													<?php
													if( $this->_models['variables']->tip_oper != 0 )
													{
													?>
													<div class="btn_general" style='text-align:center'>
														<a 
															href="javascript:" 
															onClick='redefineCriteria(this)' 
															onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
														>
															<?php echo JText::_('LNG_CHANGE_PERIOD',true);?>
															
														</a> 
													</div>
													<?php
													}
													?>
												</td>
												<td nowrap>
													<?php
													if( $this->_models['variables']->tip_oper != 0 )
													{
													?>
													
													<div class="btn_general" style='text-align:center'>
														<a href="javascript:" 
															onClick="return showHotel(true);" 
															onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
														>
															<?php echo JText::_('LNG_HOTEL_DESCRIPTIONS',true);?>
														</a> 
													</div>
													<?php
													}
													?>
												</td>
											</TR>
										</TABLE>
									</div>
								</TD>
							</TR>
						</TABLE>
					</td>
					
				</TR>
			<?php
			}
			?>
		</table>
		
		
		<table width=100% class="table_hotels" id='table_hotels' name='table_hotels' style='display:none' valign=top >
			<tr>	
				<TD colspan=2>
					<fieldset>
						<legend><?php echo JText::_('LNG_HOTEL_DESCRIPTIONS',true)?></legend>
							<table width=100% class="admintable" valign='top' >
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_NAME',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_name?>
									</TD>
								</TR>
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_CURRENCY',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_currency?>
									</TD>
								</TR>
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_COUNTRY',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->country_name?>
									</TD>
								</TR>
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_COUNTY',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_county?>
									</TD>
								</TR>
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_CITY',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_city?>
									</TD>
								</TR>
								<tr valign=top>
									<TD align=center>
										<B><?php echo JText::_('LNG_DESCRIPTION',true)?></B>
									</TD>
									<TD align=left width=90%>
										<?php echo $this->_models['variables']->itemHotelSelected->hotel_description?>
									</TD>
								</TR>
								<TR>
									<td colspan=5>
										<hr>
									</td>
								
							</table>
					</fieldset>
				</td>
			<TR>
				<TD nowrap colspan=2 align=right>
					<div class="button_holder">
						<div class="btn_general">
							<a href="javascript:;" 
								onClick="return showHotel(false);" 
								onmouseover="this.style.cursor='hand';this.style.cursor='pointer'">
								<?php echo JText::_('LNG_BACK',true);?>
							</a> 
						</div>
					</div>
				</TD>
			</tr>
		</TABLE>
		<?php
		if( $this->_models['variables']->tip_oper == 1 && count( $this->_models['variables']->itemFeatures) > 0 )
		{
		?>
		<table width=100% class="room_preferences_options" id='table_room_preferences' name='table_room_preferences' >
			<tr >
				<TD class="header_line"><strong><?php echo JText::_('LNG_ROOM_PREFERENCES',true);?></strong></TD>
			</TR>
			<tr>
				<TD align=left><?php echo JText::_('LNG_YOUR_PREFERENCES_WILL_BE_SUBMITTED_WITH_YOUR_RESERVATION_AND_ARE_SUBJECT_TO_HOTEL_AVAILABILITY',true);?></TD>
			</TR>
			
			<TR>
				<TD width=100%>
					<TABLE width=100%>
					<?php
					
					foreach( $this->_models['variables']->itemFeatures as $keyF => $feature )
					{
						?>
						<TR>
							<TD align=left width=20% nowrap>
								<B><?php echo $feature->feature_name?> :</B>
							</TD>
							<TD align=left>
								<TABLE width=100% border=0>
								<?php
								if( count($feature->options) > 0 )
								{
									for( $k = 0; $k < 5 % count($feature->options); $k++ )
									{
										?>
										<TR>
										<?php
										for( $i=0; $i<5;$i++ )
										{
											$option = count($feature->options) > $i + 5*$k?  $feature->options[$i + 5*$k] : null;
										?>
										<TD width=20% align=left>
											<?php
											if(isset($option))
											{
												?>
												<input
													type	=	'<?php echo $feature->is_multiple_selection==false? "radio" : "checkbox"?>'
													id		= 	'option_ids<?php echo $keyF?>[]'
													name	= 	'option_ids<?php echo $keyF?>[]'
													<?php echo in_array( $option->option_id, $this->_models['variables']->option_ids )? " checked " : ""?>
													value	= 	'<?php echo $option->option_id?>'
													<?php echo in_array( $option->option_id, $this->_models['variables']->room_feature_available_ids)? "" : " disabled "?>
													onmouseover			=	"this.style.cursor='hand';this.style.cursor='pointer'"
													onmouseout			=	"this.style.cursor='default'"
													ondblclick	= "
																if(this.checked==false)
																	this.checked=true;
																else
																	this.checked=false;
															"
												/>
													<?php echo $option->option_name?>
												<?php
											}
											?>
										</TD>
										<?php
										}
										?>
										</TR>
										<?php
									}
								}
								?>
								</TABLE>
							</TD>
						</TR>
					<?php
					}
					?>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
		<?php
		}
		?>
		<TABLE width='100%' class="room_preferences_options">
			<tr>
				<td width=99% align=left>
					<div class="button_holder">
						<div class="btn_general" style='float:left'>
							<a href="javascript:" onClick="return listHotels();" onmouseover="this.style.cursor='hand';this.style.cursor='pointer'">
								<?php echo JText::_('LNG_LIST_HOTELS',true);?>
							</a> 
						</div>
					</div>
				</td>
				<td align='right'>
				<?php
					if ($this->_models['variables']->itemAppSettings->is_enable_reservation ==true	)
					{
					?>
						<div class="button_holder">
							<div class="btn_general">
								<a href="javascript:" onClick="return checkContinue();" onmouseover="this.style.cursor='hand';this.style.cursor='pointer'">
									<?php echo JText::_('LNG_CHECK_AVAILABILITY',true);?>
								</a> 
							</div>
						</div>
					<?php
					}
					else 
					{
					?>
						<div class="btn_general">
							<a>
								<?php echo JText::_('LNG_RESERVATIONS_ARE_DISABLED',true);?>
							</a> 
						</div>
					
					<?php
					} ?>

				</td>
			</tr>
			
		</TABLE>
		
	</div>
	<input type="hidden" name="option" id="option"  value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="_lang" id="_lang" value="<?php echo JRequest::getVar('_lang') ?>" />
	<input type="hidden" name="tip_oper" id="tip_oper" value="<?php echo JRequest::getVar('tip_oper') ?>" />
	<input type="hidden" name="tmp" id="tmp" value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="controller" id="controller" value="" />
	<input type="hidden" name="view" id="view" value="JHotelReservation" /> 
	<input type="hidden" name="room_feature_available_ids" id="room_feature_available_ids"	value="<?php echo implode(',' , $this->_models['variables']->room_feature_available_ids)?>" 	/> 
	<input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo $this->_models['variables']->hotel_id?>" /> 
	
	<input type="hidden" name="first_name" id="first_name" value="<?php echo $this->_models['variables']->first_name?>" /> 
	<input type="hidden" name="last_name" id="last_name" value="<?php echo $this->_models['variables']->last_name?>" /> 
	<input type="hidden" name="details" id="details" value="<?php echo $this->_models['variables']->details?>" /> 
	<input type="hidden" name="address" id="address" value="<?php echo $this->_models['variables']->address?>" /> 
	<input type="hidden" name="city" id="city" value="<?php echo $this->_models['variables']->city?>" /> 
	<input type="hidden" name="state_name" id="state_name" value="<?php echo $this->_models['variables']->state_name?>" /> 
	<input type="hidden" name="country" id="country" value="<?php echo $this->_models['variables']->country?>" /> 
	<input type="hidden" name="postal_code" id="postal_code" value="<?php echo $this->_models['variables']->postal_code?>" /> 
	<input type="hidden" name="tel" id="tel" value="<?php echo $this->_models['variables']->tel?>" /> 
	<input type="hidden" name="email" id="email" value="<?php echo $this->_models['variables']->email?>" /> 
	<input type="hidden" name="conf_email" id="conf_email" value="<?php echo $this->_models['variables']->conf_email?>" /> 
	<input type="hidden" name="currency_selector"				id="currency_selector"					value="<?php echo $this->_models['variables']->currency_selector?>" 							/> 
	
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script>
		<?php
		if( JRequest::getVar( 'IS_ERROR_SSL') == '1' )	
		{
		?>
		setTimeout(redirect_HTTPS, 5000);
		<?php
		}
		else
		{
		?>
		window.onload = init();
		<?php
		}
		?>
		
		
		function redefineCriteria(obj_link)
		{
			var form 	= document.getElementById('userForm');
			
			var tParams 	= document.getElementById('table_parameters');
			var tRoomPrefer	= document.getElementById('table_room_preferences');
			
			var trCalScript = document.getElementById('tr_calendar_script');
			var trDisDate 	= document.getElementById('tr_show_dates');
			
			tRoomPrefer.style.display 	= 'none';
			tParams.style.display 		= 'block';
			trCalScript.style.display 	= 'block';
			trDisDate.style.display 	= 'none';
			
			
			var d1Obj	 	= form.elements['day_start'];	
			var m1Obj	 	= form.elements['month_start'];	
			var y1Obj	 	= form.elements['year_start'];	
			
			var d2Obj	 	= form.elements['day_end'];	
			var m2Obj	 	= form.elements['month_end'];	
			var y2Obj	 	= form.elements['year_end'];	
			
			
			var rooms	 	= form.elements['rooms'];	
			cleanSelectParams(rooms);
			createSelectParams( rooms, 1, 10, '<?php echo $this->_models['variables']->rooms ?>' );
			
			var guest_adult	= form.elements['guest_adult'];	
			cleanSelectParams(guest_adult);
			createSelectParams( guest_adult, 1, 10, '<?php echo $this->_models['variables']->guest_adult ?>' );
			
			var guest_child	= form.elements['guest_child'];	
			cleanSelectParams(guest_child);
			createSelectParams( guest_child, 0, 10, '<?php echo $this->_models['variables']->guest_child ?>' );
			
			
			createControls('td_data_calendar_1', d1Obj.value, m1Obj.value, y1Obj.value, '<?php echo _PATH_IMG?>', true, true);
			createControls('td_data_calendar_2', d2Obj.value, m2Obj.value, y2Obj.value, '<?php echo _PATH_IMG?>', true, true);
			
			obj_link.style.display='none';
			form.elements['tip_oper'].value	=	'0';
		
			//form.elements['task'].task.value="cleanForm";
			//userForm.submit()
			
		}
		
		function redirect_HTTPS()
		{
			document.location.href='https://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>';
		}
		
		function init()
		{
			var cal_1 = JHotelReservationCalendar('td_data_calendar_1', '<?php echo $this->_models['variables']->month_start?>', '<?php echo $this->_models['variables']->year_start?>', '<?php echo _PATH_IMG?>', '<?php echo $this->_models['variables']->tip_oper ==1? !false : true?>');
			var cal_2 = JHotelReservationCalendar('td_data_calendar_2', '<?php echo $this->_models['variables']->month_end?>', '<?php echo $this->_models['variables']->year_end?>', '<?php echo _PATH_IMG?>','<?php echo $this->_models['variables']->tip_oper ==1? !false : true?>');
			<?php
			if( 1 ) //$this->_models['variables']->tip_oper == 1 )
			{
			?>
				//alert("<?php echo $this->_models['variables']->tip_oper?>");
				markSelectInterval();
			<?php
			}
			?>
		}
		function listHotels()
		{
			var form 	= document.getElementById('userForm');
			form.tip_oper.value	= "-2";
			form.submit();
		}
		
		function showHotel(show)
		{
			var form 	= document.getElementById('userForm');
			var tParams 	= document.getElementById('table_parameters');
			var tRoomPrefer	= document.getElementById('table_room_preferences');
			var trCalScript = document.getElementById('tr_calendar_script');
			var trDisDate 	= document.getElementById('tr_show_dates');
			var tHotels 	= document.getElementById('table_hotels');
			var tChAvil 	= document.getElementById('table_check_availability');
			
			if( show==true )
			{
				if(tChAvil)
					tChAvil.style.display 		= 'none';
				if(tRoomPrefer)
					tRoomPrefer.style.display 	= 'none';
				if(tParams)
					tParams.style.display 		= 'none';
				if(trCalScript)
					trCalScript.style.display 	= 'none';
				if(trDisDate)
					trDisDate.style.display 	= 'none';
				if(tHotels)
					tHotels.style.display 		= 'block';
			}
			else
			{
				if(tHotels)
					tHotels.style.display 		= 'none';
				if( form.tip_oper.value == 0 )
				{
					if(tParams)
						tParams.style.display 		= 'block';
					if(trCalScript)
						trCalScript.style.display 	= 'block';
					if(tChAvil)
						tChAvil.style.display 		= 'block';
				}
				else
				{
					if(trDisDate)
						trDisDate.style.display 	= 'block';
					if(tRoomPrefer)
						tRoomPrefer.style.display 	= 'block';
					if(tChAvil)
						tChAvil.style.display 		= 'block';
				}
			
			}
		
		}
		
		function selectHotel(id)
		{
			var form 			= document.getElementById('userForm');
			form.hotel_id.value = id;
			form.tip_oper.value	= parseInt(form.tip_oper.value);
			form.task.value		="checkAvalability";
			form.submit();
		}
		
		function checkContinue() 
		{
			var form 			= document.getElementById('userForm');
			
			var yearObj_start	= form.elements['year_start'];
			var monthObj_start	= form.elements['month_start'];
			var dayObj_start	= form.elements['day_start'];
			
			var yearObj_end		= form.elements['year_end'];
			var monthObj_end	= form.elements['month_end'];
			var dayObj_end		= form.elements['day_end'];
			if( 
				yearObj_start.value 	==  yearObj_end.value 
				&&
				monthObj_start.value 	==  monthObj_end.value 
				&&
				dayObj_start.value 		==  dayObj_end.value 
			)
			{
				alert( "<?php echo JText::_('LNG_PERIOD_ERROR',true);?>");
				return false;
			}
			
			form.tip_oper.value	= parseInt(form.tip_oper.value) + <?php echo $this->_models['variables']->itemAppSettings->is_enable_screen_room_preferences ==false?2:1?>;
			form.task.value		="checkAvalability";
			form.submit();
		}
		
		function cleanSelectParams( fieldSelect )
		{
			for (i = fieldSelect.length - 1; i>=0; i--) 
			{
				fieldSelect.remove(i);
			}
		}
		
		function createSelectParams( fieldObj, start, stop, crt )
		{
			for( i = start; i <= stop; i++ )
			{
				var elOptNew 		= document.createElement('OPTION');
				elOptNew.text 		= i;
				elOptNew.value 		= i;
				if( i == crt )
					elOptNew.selected 	= true;
				else
					elOptNew.selected 	= false;
				
				fieldObj.options.add(elOptNew);
			}
		}
	</script>
</form>