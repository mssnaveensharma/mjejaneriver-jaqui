<?php 
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$editor =JFactory::getEditor();
?>

 <fieldset class='adminform'>
			<legend><?php echo JText::_('LNG_FRONT_END_STYLE',true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_TABS',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_hotel_tabs"
							id			= "enable_hotel_tabs"
							value		= '1'
							<?php echo $this->item->enable_hotel_tabs==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_hotel_tabs"
							id			= "enable_hotel_tabs"
							value		= '0'
							<?php echo $this->item->enable_hotel_tabs==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_RATING',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_hotel_rating"
							id			= "enable_hotel_rating"
							value		= '1'
							<?php echo $this->item->enable_hotel_rating==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_hotel_rating"
							id			= "enable_hotel_rating"
							value		= '0'
							<?php echo $this->item->enable_hotel_rating==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_HOTEL_DESCRIPTION',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_hotel_description"
							id			= "enable_hotel_description"
							value		= '1'
							<?php echo $this->item->enable_hotel_description==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_hotel_description"
							id			= "enable_hotel_description"
							value		= '0'
							<?php echo $this->item->enable_hotel_description==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('Enable Hotel Facilities',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_hotel_facilities"
							id			= "enable_hotel_facilities"
							value		= '1'
							<?php echo $this->item->enable_hotel_facilities==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_hotel_facilities"
							id			= "enable_hotel_facilities"
							value		= '0'
							<?php echo $this->item->enable_hotel_facilities==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('Enable Hotel Information',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_hotel_information"
							id			= "enable_hotel_information"
							value		= '1'
							<?php echo $this->item->enable_hotel_information==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_hotel_information"
							id			= "enable_hotel_information"
							value		= '0'
							<?php echo $this->item->enable_hotel_information==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				
				
				<TR>
					<td width="15%" align="left" class="key" nowrap ><?php echo JText::_('LNG_SAVE_ALL_GUEST_DATA',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "save_all_guests_data"
							id			= "save_all_guests_data"
							value		= '1'
							<?php echo $this->item->save_all_guests_data==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "save_all_guests_data"
							id			= "save_all_guests_data"
							value		= '0'
							<?php echo $this->item->save_all_guests_data==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				<?php if(PROFESSIONAL_VERSION==1){?>
				<TR>
					<td width="10%" align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_EXTRA_OPTIONS',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "is_enable_extra_options"
							id			= "is_enable_extra_options"
							value		= '1'
							<?php echo $this->item->is_enable_extra_options==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_extra_options"
							id			= "is_enable_extra_options"
							value		= '0'
							<?php echo $this->item->is_enable_extra_options==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				
				<TR>
					<td width="10%" align="left" class="key" nowrap ><?php echo JText::_('LNG_ENABLE_EXCURSIONS',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "enable_excursions"
							id			= "enable_excursions"
							value		= '1'
							<?php echo $this->item->enable_excursions==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_excursions"
							id			= "enable_excursions"
							value		= '0'
							<?php echo $this->item->enable_excursions==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				
				
				
				<?php }?>
	<!-- 			<TR>
					<td width="10%" align="left" class="key" nowrap ><?php echo JText::_('LNG_SHOW_AIRPORT_TRANSFER',true)?> :</TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "is_enable_screen_airport_transfer"
							id			= "is_enable_screen_airport_transfer"
							value		= '1'
							<?php echo $this->item->is_enable_screen_airport_transfer==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_screen_airport_transfer"
							id			= "is_enable_screen_airport_transfer"
							value		= '0'
							<?php echo $this->item->is_enable_screen_airport_transfer==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR>
				
				<tr>
					<td width="10%" align="left" class="key" nowrap >
						<?php echo JText::_( 'LNG_ENABLE_SPECIAL_DISCOUNTS' ,true); ?>:
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "enable_discounts"
							id			= "enable_discounts"
							value		= '1'
							<?php echo $this->item->enable_discounts==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_( 'LNG_YES' ,true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "enable_discounts"
							id			= "enable_discounts"
							value		= '0'
							<?php echo $this->item->enable_discounts==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_( 'LNG_NO' ,true); ?>
					</td>
				<TR> -->
					<TD width=10% nowrap class="key" ><?php echo JText::_('LNG_HOTEL_MODULE_STYLE',true); ?> :</TD>
					<TD nowrap colspan=2>
						<select
							id		= 'css_module_style'
							name	= 'css_module_style'
							
						>
							<?php
							for($i = 0; $i <  count( $this->item->css_module_styles ); $i++)
							{
								$css_module_style = basename($this->item->css_module_styles[$i]); 
							?>
							<option
								value = '<?php echo $css_module_style?>' 
								<?php echo $css_module_style == $this->item->css_module_style ? "selected" : ""?>
							> 
								<?php echo $css_module_style?>
							</option>
							<?php
							}
							?>
							
						</select>
					</TD>
				</TR>
				<TR>
					<TD width=10% nowrap class="key" ><?php echo JText::_('LNG_HOTEL_COMPONENT_STYLE',true); ?> :</TD>
					<TD nowrap colspan=2>
						<select
							id		= 'css_style'
							name	= 'css_style'
							
						>
							<?php
							for($i = 0; $i <  count( $this->item->css_styles ); $i++)
							{
								$css_style = basename($this->item->css_styles[$i]); 
							?>
							<option
								value = '<?php echo $css_style?>' 
								<?php echo $css_style == $this->item->css_style ? "selected" : ""?>
							> 
								<?php echo $css_style?>
							</option>
							<?php
							}
							?>
							
						</select>
					</TD>
				</TR>
				
			</TABLE>
			
		</fieldset>
	
		
	
