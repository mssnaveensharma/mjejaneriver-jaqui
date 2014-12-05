<fieldset class='adminform'>
		<legend><?php echo JText::_('LNG_ROOM_FEATURES',true); ?></legend>
		<?php
		
		$crt_limit	= 0;
		
		if( count( $this->roomFeatures ) == 0 )
		{
			echo JText::_('LNG_CURRENTLY_THERE_ARE_NO_FEATURES_DEFINED',true);
		}
		
		?>
		<TABLE class='admintable' align=left border=0>
			<TR>
				<TD colspan=2 align=left ><?php echo JText::_('LNG_SELECT_AVAILABLE_FEATURES_FOR_THE_CURRENT_ROOM',true); ?></TD>
			</TR>
			<tr>
				<TD width=10% nowrap class="key"><B><?php echo JText::_('LNG_BREAKFAST_INCLUDED',true); ?> :</B></TD>
				<TD nowrap width=80%  valign=top align=left>
					<input type="checkbox" name="has_breakfast" id="has_breakfast" value="1" <?php echo $this->item->has_breakfast? " checked " :""?> />
				</TD>
			</tr>
			<?php 
				if( count( $this->roomFeatures ) > 0 )			{			?>
			<TR>
				<TD width=10% nowrap class="key"><B><?php echo JText::_('LNG_AVAILABLE_FEATURE_OPTIONS',true); ?> :</B></TD>
				<TD nowrap width=80%  valign=top align=left>
					<TABLE valign=top align=left border=0 width=100%>
						<?php
						foreach( $this->roomFeatures  as $keyF => $valueF )
						{
							?>
							<TR>
								<TD WIDTH = '10%'>
									<B><?php echo $valueF->feature_name ?></B>
								</TD>
								<TD valign=top align=left>
									<TABLE valign=top border=0 width=100%>
									<?php
									$crt_limit 		= ceil(count( $valueF->options ) / 5);
										if( count($valueF->options)==0)
											break;
									?>
										<TR>
											<?php
											$crt = 0;
											
											foreach( $valueF->options as $keyO => $value )
											{
											?>
											<TD width=20% valign=top nowrap>
												<?php
												if( 5 == $crt )
													break;
												?>
											
												<input 
													type		=	'<?php echo $valueF->is_multiple_selection? 'checkbox' : 'radio'?>'
													name		=	'option_ids_<?php echo $keyF?>[]'
													id			=	'option_ids_<?php echo $keyF?>[]'
													onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
													onmouseout	=	"this.style.cursor='default'"
													<?php echo count($this->item->option_ids) > 0  && in_array( $value['option_id'], $this->item->option_ids) == true? " checked " : ""?>
													value		= 	"<?php echo $value['option_id']?>"
												>
												<?php echo $value['option_name']?>
												<?php
												$crt ++;
												
												?>
											</TD>
											<?php
											}
											
											
											?>
										</TR>
									
									</TABLE>
								</TD>
							</TR>
						<?php
						}
						?>
					</TABLE>
				</TD>
			</TR>
			<TR>
				<TD colspan=2 align=left>
					<BR><?php echo JText::_('LNG_SELECTED_FEATURES_WILL_ASSIGN_TO_THE_ROOM_DEFINED_AND_WILL_DISPLAYED_ON_THE_CLIENT_SCREEN_WHEN_THE_RESERVATION_IS_MADE',true); ?>
				</TD>
			</TR>
			<?php } ?>
			
		</TABLE>
	</fieldset>