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

 	 <fieldset class="adminform">
			<legend><?php echo JText::_('LNG_COMPANY',true); ?></legend>
			
			<table class="admintable"  width=100%>
				<TR>
					<td width="10%" align="left" class="key" nowrap >
							<?php echo JText::_('LNG_NAME',true); ?>:
					</td>
					<td align="left" nowrap>
						<input type='text' size=50 maxlength=255  id='company_name' name = 'company_name' value="<?php echo $this->item->company_name?>">
					</TD>
					<td align="right" rowspan="2">
						<div class="picture-preview" id="picture-preview">
							<?php
							if(isset($this->item->logo_path)){
								echo "<img src='".JURI::root().PATH_PICTURES.$this->item->logo_path."'/>";
							}
							?>
						</div>
					</td>
				</TR>
				<TR>
					<td width="10%" align="left" class="key" nowrap >
							<?php echo JText::_('LNG_EMAIL',true); ?>:
					</td>
					<td align="left" nowrap>
						<input type='text' size=50 maxlength=255  id='company_email' name = 'company_email' value='<?php echo $this->item->company_email?>'>
						<input type="hidden" name="logo_path" id="imageLocation" value="<?php echo $this->item->logo_path?>"> 
					</TD> 
				</TR>
				
				<TR>
					<TD align=left class="key">
						<?php echo JText::_('LNG_COMPANY_LOGO',true); ?>:
					</TD>
					<TD>
						<input name="uploadedfile" id="uploadedfile" size=50 type="file" />
					</TD>
				</TR>
				
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_APPLICATION_SETTINGS',true); ?></legend>

			<table class="admintable"  width=100% >
				<tr>
					<td width="10%" align="left" class="key" nowrap >
							<?php echo JText::_('LNG_ENABLE_RESERVATION',true); ?>:
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "is_enable_reservation"
							id			= "is_enable_reservation"
							value		= '1'
							<?php echo $this->item->is_enable_reservation==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_reservation"
							id			= "is_enable_reservation"
							value		= '0'
							<?php echo $this->item->is_enable_reservation==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</td>
					<td align="left" nowrap >
						<?php echo JText::_('LNG_ENABLE_DISABLE_RESERVATION',true); ?>
					</TD>
				</TR>
				<TR>
					<td width="10%" align="left" class="key" nowrap >
							<?php echo JText::_( 'LNG_ENABLE_OFFERS' ,true); ?>
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "is_enable_offers"
							id			= "is_enable_offers"
							value		= '1'
							<?php echo $this->item->is_enable_offers==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_( 'LNG_YES' ,true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_offers"
							id			= "is_enable_offers"
							value		= '0'
							<?php echo $this->item->is_enable_offers==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_( 'LNG_NO' ,true); ?>
					</td>
					<td align="left" nowrap >
						<?php echo JText::_( 'LNG_INFO_APPLICATION_SET_OFFERS_ON_OFF' ,true); ?>
					</TD>
				</TR>
				<tr>
					<td width="10%" align="left" class="key" nowrap ><?php echo JText::_('LNG_DATE_FORMAT',true)?> :</Td>
					<td>
						<select
							id		= 'date_format_id'
							name	= 'date_format_id'
							
						>
							<?php
							foreach ($this->item->dateFormats as $dateFormat)
							{
							?>
							<option value = '<?php echo $dateFormat->id?>' <?php echo $dateFormat->id==$this->item->date_format_id? "selected" : ""?>> <?php echo $dateFormat->name?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				<!-- <tr>
					<td width="10%" align="left" class="key" nowrap ><?php echo JText::_('LNG_DATE_LANGUAGE',true)?> :</Td>
					<td>
						<select
							id		= 'date_language'
							name	= 'date_language'
							
						>
							<option value = 'nl_NL' <?php echo 'nl_NL'==$this->item->date_language? "selected" : ""?>>Dutch</option>
							<option value = 'en_EN' <?php echo 'en_EN'==$this->item->date_language? "selected" : ""?>>English</option>
							<option value = 'fr_FR' <?php echo 'fr_FR'==$this->item->date_language? "selected" : ""?>>French</option>
							<option value = 'el_GR' <?php echo 'el_GR'==$this->item->date_language? "selected" : ""?>>Greek</option>
							<option value = 'ro_RO' <?php echo 'ro_RO'==$this->item->date_language? "selected" : ""?>>Romanian</option>
							<option value = 'es_ES' <?php echo 'es_ES'==$this->item->date_language? "selected" : ""?>>Spanish</option>
						</select>
					</td>
				</tr>
				 -->
				<!--
				<TR style=''>
					<td width="10%" align="left" class="key" nowrap >Email servers :</TD>
					<TD nowrap colspan=2>
						<input type='text' id='sendmail_from' name='sendmail_from' 
							value="<?php echo $this->item->sendmail_from ?>" 
							size=50 
							maxlength=120 
						
					</TD>
				</TR>
				<TR>
					<td width="10%" align="left" class="key" nowrap >Email servers :</TD>
					<TD nowrap colspan=2>
						<input type='text' id='sendmail_name' name='sendmail_name' 
							value="<?php echo $this->item->sendmail_name ?>" 
							size=50 
							maxlength=120 
						
					</TD>
				</TR>
				-->
			
				<TR>
					<td width="10%" align="left" class="key">
							<?php echo JText::_( 'LNG_NOTIFY_EMAIL_CANCEL_PENDING' ,true); ?>
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "is_email_notify_canceled_pending"
							id			= "is_email_notify_canceled_pending"
							value		= '1'
							<?php echo $this->item->is_email_notify_canceled_pending==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_( 'LNG_YES' ,true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_email_notify_canceled_pending"
							id			= "is_email_notify_canceled_pending"
							value		= '0'
							<?php echo $this->item->is_email_notify_canceled_pending==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_( 'LNG_NO' ,true); ?>
					</td>
					<td align="left" nowrap >
						<?php echo JText::_( 'LNG_INFO_NOTIFY_EMAIL_CANCEL_PENDING' ,true); ?>
					</TD>
				</TR>
				
				<TR>
					<td width="10%" align="left" class="key">
							<?php echo JText::_( 'LNG_HIDE_USER_EMAIL' ,true); ?>
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "hide_user_email"
							id			= "hide_user_email"
							value		= '1'
							<?php echo $this->item->hide_user_email==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_( 'LNG_YES' ,true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "hide_user_email"
							id			= "hide_user_email"
							value		= '0'
							<?php echo $this->item->hide_user_email==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_( 'LNG_NO' ,true); ?>
					</td> 
				</TR>	
				
				<TR>
					<td width="10%" align="left" class="key">
							<?php echo JText::_( 'LNG_SHOW_CHILDREN' ,true); ?>
					</td>
					<td align="left" nowrap>
						<input 
							type		= "radio"
							name		= "show_children"
							id			= "show_children"
							value		= '1'
							<?php echo $this->item->show_children==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
						<?php echo JText::_( 'LNG_YES' ,true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "show_children"
							id			= "show_children"
							value		= '0'
							<?php echo $this->item->show_children==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_( 'LNG_NO' ,true); ?>
					</td> 
				</TR>	
			</table>
		</fieldset>
		
<script type="text/javascript">
jQuery(function()
		{
			jQuery('#uploadedfile').change(function() {
				var fisRe 	= /^.+\.(jpg|bmp|gif|png)$/i;
				var path = jQuery('#uploadedfile').val();
				if (path.search(fisRe) == -1)
				{   
					alert(' JPG, BMP, GIF, PNG only!');
					return false;
				}  
				jQuery(this).upload('<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/upload.php?t=<?php echo strtotime('now')?>&resizeImage=0&_root_app=<?php echo urlencode(JPATH_ROOT)?>&_target=<?php echo urlencode(PATH_PICTURES.LOGO_PICTURE_PATH)?>', function(responce) 
																									{
																										//alert(responce);
																										if( responce =='' )
																										{
																											alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
																											jQuery(this).val('');
																										}
																										else
																										{
																											var xml = responce;
																											// alert(responce);
																											jQuery(xml).find("picture").each(function()
																											{
																												if(jQuery(this).attr("error") == 0 )
																												{
																													setUpLogo(jQuery(this).attr("path"),jQuery(this).attr("name"));
																												}
																												else if( jQuery(this).attr("error") == 1 )
																													alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
																												else if( jQuery(this).attr("error") == 2 )
																													alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
																												else if( jQuery(this).attr("error") == 3 )
																													alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
																												else if( jQuery(this).attr("error") == 4 )
																													alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
																											});
																											
																											jQuery(this).val('');
																										}
																									}, 'html'
				);
	        });
			
		});
		
	function setUpLogo(path, name){
	<?php 
			$baseUrl = JURI::root();
		?>
		jQuery("#imageLocation").val(<?php echo LOGO_PICTURE_PATH?>+path);
		var img_new = document.createElement('img');
		img_new.setAttribute('src', "<?php echo $baseUrl.PATH_PICTURES.LOGO_PICTURE_PATH?>" + path );
		img_new.setAttribute('class', 'company-logo');
		jQuery("#picture-preview").empty();
		jQuery("#picture-preview").append(img_new);
	}
</script>
	
		
	
