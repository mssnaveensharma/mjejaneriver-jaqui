
<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_ROOM_DETAILS',true); ?></legend>

		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?></TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "room_name"
						id			= "room_name"
						value		= '<?php echo $this->item->room_name?>'
						size		= 64
						maxlength	= 128
						
					/>
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
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DISPLAY_ON_FRONT',true); ?></TD>
				<TD nowrap align=left>
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "front_display"
						id			= "front_display"
						value		= '1'
						<?php echo $this->item->front_display==true? " checked " :""?>
						accesskey	= "Y"
						
					/>
					<?php echo JText::_('LNG_STR_YES',true); ?>
					&nbsp;
					<input 
						style		= 'float:none'
						type		= "radio"
						name		= "front_display"
						id			= "front_display"
						value		= '0'
						<?php echo $this->item->front_display==false? " checked " :""?>
						accesskey	= "N"
					/>
					<?php echo JText::_('LNG_STR_NO',true); ?>
				</TD>
				<TD nowrap>
					&nbsp;
				</TD>
			</TR>
			<TR style="display:none">
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_SHORT_DESCRIPTION',true); ?>:</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<textarea id='room_short_description' name='room_short_description' rows=2 cols=135><?php echo $this->item->room_short_description?></textarea>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_MAIN_DESCRIPTION',true); ?>:</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
				<?php 
						$appSettings = JHotelUtil::getApplicationSettings();
						$options = array(
												    'onActive' => 'function(title, description){
												        description.setStyle("display", "block");
												        title.addClass("open").removeClass("closed");
												    }',
												    'onBackground' => 'function(title, description){
												        description.setStyle("display", "none");
												        title.addClass("closed").removeClass("open");
												    }',
												    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
												    'useCookie' => true, // this must not be a string. Don't use quotes.
						);
						
						echo JHtml::_('tabs.start', 'tab_group_id', $options);
						
						$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
						$dirs = JFolder::folders( $path );
						sort($dirs);
						//dmp($dirs);
						$j=0;
						foreach( $dirs  as $_lng ){
							
							echo JHtml::_('tabs.panel', $_lng, 'tab'.$j );						
							$langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:"";
							$editor =JFactory::getEditor();
							echo $editor->display('room_main_description_'.$_lng, $langContent, '800', '400', '70', '15', false);
							
						}
						echo JHtml::_('tabs.end');
					?>
				</TD>
			</TR>
			<TR >
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DETAILS',true); ?>:</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<textarea id='room_details' name='room_details' rows=2 cols=135><?php echo $this->item->room_details?></textarea>
				</TD>
			</TR>
			
		</TABLE>
	</fieldset>
	
	<fieldset class='adminform'>
		<legend><?php echo JText::_('LNG_ROOM_CAPACITY',true); ?></legend>
		<?php
		
		$crt_limit	= 0;
		
		?>
		<input type='hidden' name='crt_interval_number' id='crt_interval_number' value='-1'>
		<div style='display:none'>
			<div id='div_calendar' class='div_calendar'>
				<p>
					<div class="dates_room_calendar" id="dates_room_calendar"></div>
				</p>
			</div>
		</div>
		<TABLE class="admintable" align=left id='table_room_numbers' name='table_room_numbers' >
			<TR>
				<TD nowrap='nowrap' class="key"><?php echo JText::_('LNG_MAX_ADULTS',true); ?> :</TD>
				<TD nowrap='nowrap' align=left>
					<input 
						type		= "text"
						name		= "max_adults"
						id			= "max_adults"
						value		= '<?php echo $this->item->max_adults?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'
					/>
					<a 
						href	="javascript:;" 
						class	="tooltip" 
						title	="<?php echo JText::_('LNG_INFO_MAX_ADULTS',true)?>"
					>
						<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/help-icon-NLP.png"?>"
						/>
					</a>
				</TD>
				<TD align=left colspan="3">&nbsp;</TD>
			</TR>
			<?php if($this->appSettings->show_children!=0){ ?>
			<TR>
				<TD nowrap='nowrap' class="key"><?php echo JText::_('LNG_MAX_CHILDREN',true); ?> :</TD>
				<TD nowrap='nowrap' align=left>
					<input 
						type		= "text"
						name		= "max_children"
						id			= "max_children"
						value		= '<?php echo $this->item->max_children?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:center'
					/>
					<a 
						href	="javascript:;" 
						class	="tooltip" 
						title	="<?php echo JText::_('LNG_INFO_MAX_CHILDREN',true)?>"
					>
						<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/help-icon-NLP.png"?>"
						/>
					</a>
				</TD>
				<TD align=left colspan="3">&nbsp;</TD>
			</TR>
			<?php } ?>
		</TABLE>
	</fieldset>