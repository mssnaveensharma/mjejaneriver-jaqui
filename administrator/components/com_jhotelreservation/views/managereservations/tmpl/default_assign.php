<?php ?>
<form  autocomplete='off' action="index.php" method="post" name="adminForm">
<fieldset class="adminform">
<legend><?php echo JText::_('LNG_CONTACT',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0 width=100%>
			<TR>
				<TD width=10% nowrap><B><?php echo JText::_('LNG_NAME',true)?> :</B></TD>
				<TD nowrap width=90% align=left>
					<?php echo $this->item->first_name.' '.$this->item->last_name?>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap><B><?php echo JText::_('LNG_ADDRESS',true)?> :</B></TD>
				<TD nowrap width=90% align=left>
					<?php echo $this->item->address?>
				</TD>
			<TR>
				<TD width=10% nowrap><B><?php echo JText::_('LNG_TEL',true)?>:</B></TD>
				<TD nowrap width=90% align=left>
					<?php echo $this->item->tel?>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap><B><?php echo JText::_('LNG_EMAIL',true)?>. :</B></TD>
				<TD nowrap width=90% align=left>
					<a href="mailto:<?php echo $this->item->email?>">
					<?php echo $this->item->email?>
					</a>
				</TD>
			</TR>
		</table>
		</center>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_NUMBER_ROOM_DETAILS',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0 width=100% cellpadding=0 cellspacing=0>
			<TR>
				<TD nowrap ALIGN=LEFT>
					<TABLE style='margin;0px' width=100% cellpadding=0 cellspacing=0 border=0>
					<?php
					$room_numbers 	= array();
					$pos			= 0;
					foreach( $this->item->itemRoomsSelected as $room )
					{
						if( !isset( $room_numbers[ $room->room_id ] ) )
							$room_numbers[ $room->room_id ]  = explode(',',$room->numbers_available) ;
						//dmp($room->current);
						//dmp($this->item->itemRoomsCapacity);
						
						?>
						<TR>
							<TD 
								width=10% 
								nowrap
								valign=top
							>
								#<?php echo $room->current?>&nbsp;<?php echo $room->room_name?>
							</td>
							<TD>
								<?php echo JText::_('LNG_ROOM_NUMBER',true)?>
								<select id='itemRoomsNumbers' name='itemRoomsNumbers[]' style='width:60px'
									onchange = 'alterNumbers( <?php echo $pos++?>, this.value);'
								>
									<option  value='<?php echo '0|0|0|0'?>'></option>
									
									<?php
									foreach($room_numbers[ $room->room_id ] as $k_nr => $numb )
									{			
									?>
									<option 
										value='<?php echo $room->offer_id.'|'.$room->room_id.'|'.$room->current.'|'.$numb?>'
										<?php echo $room->room_number_number == $numb ? " selected " : ""?>
									>
										<?php echo $numb?> <?php echo $room->room_number_number == $numb ? "*" : ""?>
									</option>
									<?php
										// if( $room->room_number_number == $numb  )
										// {
											// unset( $room_numbers[ $room->room_id ][ $k_nr ] );
										// }
									}
								
									?>
								</select>
							</TD> 
						</TR>
						<?php
					}
					?>
					</TABLE>
				</TD>
			</TR>
			
		</TABLE>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hotel_id" 			value="<?php echo $this->item->hotel_id ?>" />
	<input type="hidden" name="email" 				value="<?php echo $this->item->email ?>" />
	<input type="hidden" name="total" 				value="<?php echo $this->item->total ?>" />
	<input type="hidden" name="total_payed" 		value="<?php echo $this->item->total_payed ?>" />
	<input type="hidden" name="confirmation_id" value="<?php echo $this->item->confirmation_id ?>" />
	<input type="hidden" name="controller" value="managereservations>" />
	<input type="hidden" name="is_assign_number_rooms" value="1" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script>
		function alterNumbers( id_crt, value )
		{
			val = value.split('|');
			var pos = 0;
			jQuery('select[name=\'itemRoomsNumbers[]\']').each(function()
			{
				var v 	= jQuery(this).val();
				v 		= v.split('|');
				if( pos == id_crt)
				{
					//continue;
				}
				else if( val[0] == v[0] && val[1] == v[1] && val[3] == v[3] )
				{	
					jQuery(this).val('0|0|0|0');
				}
				pos ++;
			});
		}
	</script>
</form>
