<fieldset class="adminform">
	<legend><?php echo JText::_( 'LNG_WIZARD_OFFER_PACKAGES' ,true); ?></legend>
		<TABLE width=100% class="admintable" align=center border=0 id='table_offer_rooms' name='table_offer_rooms' >
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_( 'LNG_ROOMS' ,true); ?> :</TD>
				<TD nowrap align=left>
					<select class='room_ids' id='room_ids' name='room_ids[]' multiple style="height:60px;width:300px">
						<?php
						foreach( $this->item->itemRooms as $value )
						{
						?>
						<option value='<?php echo $value->room_id?>' <?php echo $value->is_sel? " selected" : ""?> ><?php echo $value->room_name?></option>
						<?php
						}
						?>
					</select>
				</TD>
			</TR>
		</TABLE>
	</fieldset>