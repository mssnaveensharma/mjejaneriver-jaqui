	
	<fieldset class='adminform'>
		<input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
		<input type='hidden' name='crt_pos' id='crt_pos' value=''>
		<input type='hidden' name='crt_path' id='crt_path' value=''>
		<legend><?php echo JText::_('LNG_ROOM_PICTURES'); ?></legend>
		<TABLE class='admintable' align=left border=0>
			<TR>
				<TD align=left class="key"><?php echo JText::_('LNG_PICTURES',true); ?>:</TD>
				<TD>
					<TABLE class="admintable" align=center  id='table_room_pictures' name='table_room_pictures' >
						<?php
						$pos = 0;
						foreach( $this->pictures as $picture )
						{
						?>
						<TR>
							<TD align=left>
								<textarea cols=50 rows=2 name='room_picture_info[]' id='room_picture_info'><?php echo $picture['room_picture_info']?></textarea>
							</TD>
							<td align=center>
								<img class='img_picture_room' src='<?php echo JURI::root().PATH_PICTURES.$picture['room_picture_path']?>'/>
								<BR>
								<?php echo basename($picture['room_picture_path'])?>
								<input type='hidden' 
									value='<?php echo $picture['room_picture_enable']?>' 
									name='room_picture_enable[]' 
									id='room_picture_enable'
								>
								<input type='hidden' 
									value='<?php echo $picture['room_picture_path']?>' 
									name='room_picture_path[]' 
									id='room_picture_path'
								>
							</td>
							<td align=center>
								<img class='btn_picture_delete' 
									src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>'
									onclick =  " 
												if(!confirm('<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE',true)?>')) 
													return; 
												
												var row 		= jQuery(this).parents('tr:first');
												var row_idx 	= row.prevAll().length;
											
												jQuery('#crt_pos').val(row_idx);
												jQuery('#crt_path').val('<?php echo $picture['room_picture_path']?>');
												jQuery('#btn_removefile').click();
											"
		
								/>
							</td>
							<td align=center>
								<img class='btn_picture_status' 
									src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($picture['room_picture_enable'] ? 'checked' : 'unchecked').".gif"?>'
									onclick =  " 
												var form 		= document.adminForm;
												var v_status  	= null;
												if( form.elements['room_picture_enable[]'].length == null )
												{
													v_status  = form.elements['room_picture_enable[]'];
												}
												else
												{
													v_status  = form.elements['room_picture_enable[]'][<?php echo $pos ?>];
												}
											
												if( v_status.value == '1') 
												{
													jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/unchecked.gif"?>');
													v_status.value ='0';
												}
												else
												{
													jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/checked.gif"?>');
													v_status.value ='1';
												}
											"
		
								/>
							</td>
							<td>
								<span 
									class="span_up"
									onclick='var row = jQuery(this).parents("tr:first");  row.insertBefore(row.prev());'
								>
									<?php echo JText::_('LNG_STR_UP',true)?>
								</span>
								<span 
									class="span_down"
									onclick='var row = jQuery(this).parents("tr:first"); row.insertAfter(row.next());'
								>
									<?php echo JText::_('LNG_STR_DOWN',true)?>
								</span>
							</td>
							
							
						</TR>
						<?php
						$pos ++;
						}
						?>
					</TABLE>
				</TD>
			</TR>
			<TR>
				<TD align=left class="key">
					<?php echo JText::_('LNG_PLEASE_CHOOSE_A_FILE',true); ?>
				</TD>
				<TD>
					<input name="uploadedfile" id="uploadedfile" size=80 type="file" />
					
				</TD>
			</TR>
		</TABLE>
	</fieldset>