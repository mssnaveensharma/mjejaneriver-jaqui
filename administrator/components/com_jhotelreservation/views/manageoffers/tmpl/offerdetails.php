
<?php $editor =JFactory::getEditor(); ?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'LNG_WIZARD_OFFER_PACKAGES' ,true); ?></legend>
					<TABLE class="admintable" align=center border=0>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_NAME' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<input 
									type		= "text"
									name		= "offer_name"
									id			= "offer_name"
									value		= '<?php echo $this->item->offer_name?>'
									size		= 32
									maxlength	= 128
									
								/>
							</TD>
							<TD>&nbsp;</TD>
						</TR>
						<?php
						if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
							?>
											
								<TR>
									<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_VOUCHER_CODE' ,true); ?>:</TD>
									<TD nowrap width=1% align=left>
										<div class="row" id="voucher-container">
											<input type="hidden" name="processVouchers" value ="1"/>
											<?php 
											$index = 0;
											foreach($this->item->vouchers as $voucher) {
												$index++;
												?>
											
											<div class="form_row" id="voucherRow<?php echo $index; ?>">
												<div class="outer_input" style="maring: 0 5px 0 0">
													<input 
														type		= "text"
														name		= "vouchers[]"
														value		= '<?php echo $voucher->voucher?>'
														size		= 32
														maxlength	= 128
														
													/>
													<span id="theme_error_msg1" class="error_msg errormsg" style="display: none;"></span>
												</div>
									
												<img class='btn_picture_delete'
													src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>'
													onclick =  "removeRow('voucherRow<?php echo $index ?>')"
												/>
											</div>
											<div class="clear"></div>
											<?php } ?>
											
										</div>
											
										<div class="option-row">
											<a href="javascript:void(0)" onclick="addNewVoucher(0,'');"><?php echo JText::_('LNG_ADD_NEW_VOUCHER',true); ?></a>
										</div>
										
									</TD>
									<TD>&nbsp;</TD>
								</TR>
								<?php } ?>
		
						
						<?php 
							if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
						?>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_PUBLIC' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<input 
									type		= "radio"
									name		= "public"
									id			= "public"
									value		= '1'
									<?php echo $this->item->public==true? " checked " :""?>
									accesskey	= "Y"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
									
								/>
								<?php echo JText::_( 'LNG_STR_YES' ,true); ?>
								&nbsp;
								<input 
									type		= "radio"
									name		= "public"
									id			= "public"
									value		= '0'
									<?php echo $this->item->public==false? " checked " :""?>
									accesskey	= "N"
									onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	="this.style.cursor='default'"
		
								/>
								<?php echo JText::_( 'LNG_STR_NO' ,true); ?>
							</TD>
							<TD>&nbsp;</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_('LNG_AVAILABLE',true); ?></TD>
							<TD nowrap align=left>
								<input 
									style		= 'float:none'
									type		= "radio"
									name		= "is_available"
									id			= "is_available"
									value		= '1'
									<?php echo $this->item->is_available==true? " checked " :""?>
									accesskey	= "Y"
									
								/>
								<?php echo JText::_('LNG_STR_YES',true); ?>
								&nbsp;
								<input 
									style		= 'float:none'
									type		= "radio"
									name		= "is_available"
									id			= "is_available"
									value		= '0'
									<?php echo $this->item->is_available==false? " checked " :""?>
									accesskey	= "N"
								/>
								<?php echo JText::_('LNG_STR_NO',true); ?>
							</TD>
							<TD nowrap>
								&nbsp;
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_('LNG_STATUS',true); ?></TD>
							<TD nowrap align=left>
								<input 
									style		= 'float:none'
									type		= "radio"
									name		= "state"
									id			= "state"
									value		= '1'
									<?php echo $this->item->state==true? " checked " :""?>
									accesskey	= "Y"
									
								/>
								<?php echo JText::_('LNG_LIVE',true); ?>
								&nbsp;
								<input 
									style		= 'float:none'
									type		= "radio"
									name		= "state"
									id			= "state"
									value		= '0'
									<?php echo $this->item->state==false? " checked " :""?>
									accesskey	= "N"
								/>
								<?php echo JText::_('LNG_EDIT',true); ?>
							</TD>
							<TD nowrap>
								&nbsp;
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_COMMISSION' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<input 
									type		= "text"
									name		= "offer_commission"
									id			= "offer_commission"
									value		= '<?php echo $this->item->offer_commission?>'
									size		= 10
									maxlength	= 128
									
								/>(%)
							</TD>
							<TD>&nbsp;</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_RESERVATION_COSTS' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<input 
									type		= "text"
									name		= "offer_reservation_cost_val"
									id			= "offer_reservation_cost_val"
									value		= '<?php echo $this->item->offer_reservation_cost_val!=0? $this->item->offer_reservation_cost_val :''?>'
									size		= 10
									maxlength	= 128
									
									style		= 'text-align:right'
								/>
								<input 
									type		= "text"
									name		= "offer_reservation_cost_proc"
									id			= "offer_reservation_cost_proc"
									value		= '<?php echo $this->item->offer_reservation_cost_proc!=0? $this->item->offer_reservation_cost_proc : ''?>'
									size		= 10
									maxlength	= 128
									style		= 'text-align:center'
									
								/>
								%
							</TD>
							<TD>&nbsp;</TD>
						</TR>
						<?php } ?>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_('LNG_THEMES'); ?> :</TD>
							<TD nowrap ALIGN=LEFT>
								<div id="theme-holder" class="option-holder">
									<?php
										echo $this->displayThemes( $this->item->themes, $this->item->selectedThemes );
									?>
								</div>
								<?php 
									if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
								?>
									<div class="manage-option-holder">
										<a href="javascript:" onclick="showManageThemes()"><?php echo JText::_('LNG_MANAGE_THEMES'); ?></a>
									</div>
								<?php 
									}
								?>	
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_SHORT_DESCRIPTION' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<?php echo  $editor->display('offer_short_description',  $this->item->offer_short_description, '800', '400', '70', '15', false);?>
								
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_DESCRIPTION' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<?php echo  $editor->display('offer_description',  $this->item->offer_description, '800', '400', '70', '15', false);?>
								
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_CONTENT' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<?php echo  $editor->display('offer_content',  $this->item->offer_content, '800', '400', '70', '15', false);?>
								
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_OTHER_INFO' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<?php echo  $editor->display('offer_other_info',  $this->item->offer_other_info, '800', '400', '70', '15', false);?>
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_NIGHTS' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<input 
									type		= "text"
									name		= "offer_min_nights"
									id			= "offer_min_nights"
									value		= '<?php echo $this->item->offer_min_nights !=0 ? $this->item->offer_min_nights : ''?>'
									size		= 2
									maxlength	= 5
									
								/>
								&nbsp;<>&nbsp;
								<input 
									type		= "text"
									name		= "offer_max_nights"
									id			= "offer_max_nights"
									value		= '<?php echo $this->item->offer_max_nights !=0 ? $this->item->offer_max_nights : ''?>'
									size		= 2
									maxlength	= 5
									
								/>
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_DAYS' ,true); ?> :</TD>
							<TD nowrap colspan=2 ALIGN=LEFT>
								<TABLE>
									<TR>
										<?php
										for($day=1;$day<=7;$day++)
										{
											?>
											<TD>
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
										for($day=1;$day<=7;$day++)
										{
											$tag_name = "offer_day_$day";?>
											<TD <?php echo $day<7 ? "style='border-right:solid 2px black'" :""?> align=center >
												<input 
												type	= 'checkbox' 
												name	= 'offer_day_<?php echo $day?>'
												id		= 'offer_day_<?php echo $day?>'
												value	= "1"
												class="offer-day"
												<?php echo $this->item->{$tag_name} == 1 ? " checked " : " "?>
											>
											</TD>
										<?php
										}
										?>
									</TR>
								</TABLE>
							</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_PERIOD' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<div class="period_offer_calendar" id="period_offer_calendar"></div>
								
								<?php echo JHTML::_('calendar', $this->item->offer_datas==$appSetings->defaultDateValue ? '' : $this->item->offer_datas, 'offer_datas', 'offer_datas', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
								<?php echo JHTML::_('calendar', $this->item->offer_datas==$appSetings->defaultDateValue ? '' : $this->item->offer_datae, 'offer_datae', 'offer_datae', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
								<!--  input 
									type	='hidden' 
									name	='offer_datas' 
									id		='offer_datas'
									value	='<?php echo $this->item->offer_datas?>'
								>
								<input 
									type	='hidden' 
									name	='offer_datae' 
									id		='offer_datae'
									value	='<?php echo $this->item->offer_datae?>'
								-->
							</TD>
							<TD>&nbsp;</TD>
						</TR>
						<TR>
							<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_DISPLAY_ON_FRONT' ,true); ?>:</TD>
							<TD nowrap width=1% align=left>
								<?php echo JHTML::_('calendar', $this->item->offer_datasf==$appSetings->defaultDateValue ? '' : $this->item->offer_datasf, 'offer_datasf', 'offer_datasf', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
								<?php echo JHTML::_('calendar', $this->item->offer_datasf==$appSetings->defaultDateValue ? '' : $this->item->offer_dataef, 'offer_dataef', 'offer_dataef', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
							</TD>
							<TD>&nbsp;</TD>
						</TR>
					</TABLE>
				</fieldset>
				
<script>
	var deleteImagePath = "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>";

	jQuery("select#themes").selectList({ 
		 sort: true,
		 classPrefix: 'themes',
		 onAdd: function (select, value, text) {
			    if(value=='new'){
				    return true;
			    }
		 },

		onRemove: function (select, value, text) {
			 jQuery('select#themes option[value='+value+']').removeAttr('selected');	
		 }

	});
</script>