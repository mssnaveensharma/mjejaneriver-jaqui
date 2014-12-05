<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if( 
	JRequest::getString( 'task') !='edit' 
	&& 
	JRequest::getString( 'task') !='add' 
) 
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_MANAGE_EMAIL_DEFAULT',true); ?></legend>
			<center>
				<TABLE class="admintable" width='100%' >
					<TR class='tr_title'>
						<TD width='1%'>&nbsp;</TD>
						<TD width='1%'  align=center>&nbsp;</TD>
						<TD width='10%' align=center><B><?php echo JText::_('LNG_TYPE',true); ?></B></TD>
						<TD width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></TD>
						<TD width='20%' align=center><B><?php echo JText::_('LNG_SUBJECT',true); ?></B></TD>
						<TD  align=center ><B><?php echo JText::_('LNG_CONTENT',true); ?></B></TD>
					</TR>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$email = $this->items[$i]; 
						$emailContent = $this->hoteltranslationsModel->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION,$email->email_default_id,JRequest::getVar( '_lang'));
						
					?>
					<TR
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
						valign=top
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $email->email_default_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.email_default_id.value = '<?php echo $email->email_default_id?>'
										" 
							/>
							
						</TD>
						<TD align=center>
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=manageemailsdefault&view=manageemailsdefault&task=edit&email_default_id[]='. $email->email_default_id)?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $email->email_default_type?></TD></b>
							</a>
						<TD align=left>
							<?php echo $email->email_default_name?>
						</TD>
						<TD align=center><?php echo $email->email_default_subject?></TD>
						<TD wrap align=left><?php echo isset($emailContent)?$emailContent->content:"";?></TD>
						
					</TR>
					<?php
					}
					?>
				</TABLE>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="" />
	<input type="hidden" name="email_default_id" value="" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
	
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'edit') 
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
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true); ?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else if (pressbutton == 'back') {
				form.view.value = 'applicationsettings';
				form.controller.value = 'applicationsettings';
				//form.submit();
				submitform( pressbutton );
			} else {
				submitform( pressbutton );
			}
		}
	</script>
</form>
<?php
}
else 
{
?>
<script>
	

	jQuery(document).ready(function()
	{
		
		tinyMCE.init({
			// General options
			mode : "exact",
			elements : "email_default_content",
			theme : "advanced",
			skin : "o2k7",
			skin_variant : "silver",
			plugins : "lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
		
			// Theme options
			//theme_advanced_buttons1 : "mylistbox",
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/template_list.js",
			external_link_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/link_list.js",
			external_image_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/image_list.js",
			media_external_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
			
		});
	})

</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_EMAIL_DETAILS',true); ?></legend>
		<center>
		
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
				<TD nowrap colspan=2 align=left>
					
						<select
						id 		= "email_default_type"
						name	= "email_default_type"
						style	= "width:145px"
					>
							<option <?php echo $this->item->email_default_type=='Reservation Email'? "selected" : ""?> value='Reservation Email'><?php echo JText::_('LNG_RESERVATION_EMAIL',true); ?></option>
							<option <?php echo $this->item->email_default_type=='Cancelation Email'? "selected" : ""?> value='Cancelation Email'><?php echo JText::_('LNG_CANCELATION_EMAIL',true); ?></option>
							<option <?php echo $this->item->email_default_type=='Review Email'? "selected" : ""?> value='Review Email'><?php echo JText::_('LNG_REVIEW_EMAIL',true); ?></option>
							<option <?php echo $this->item->email_default_type=='Invoice Email'? "selected" : ""?> value='Invoice Email'><?php echo JText::_('LNG_INVOICE_EMAIL',true); ?></option>
							<option <?php echo $this->item->email_default_type=='Bookings List'? "selected" : ""?> value='Bookings List'><?php echo JText::_('LNG_BOOKINGS_LIST',true); ?></option>
							<option <?php echo $this->item->email_default_type=='Guest List Email'? "selected" : ""?> value='Guest List Email'><?php echo JText::_('LNG_GUEST_LIST',true); ?></option>
						</select>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap><?php echo JText::_('LNG_NAME',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "email_default_name"
						id			= "email_default_name"
						value		= '<?php echo $this->item->email_default_name?>'
						size		= 50
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10% nowrap><?php echo JText::_('LNG_SUBJECT',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "email_default_subject"
						id			= "email_default_subject"
						value		= '<?php echo $this->item->email_default_subject?>'
						size		= 50
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"> <?php echo JText::_('LNG_CONTENT',true); ?> :</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<?php echo JText::_('LNG_USE_ONE_OF_EMAILS_TAG_IN_THE_EDITOR_TO_INSERT_CONTENT_WHEN_EMAIL_IS_SENT',true)?>
					<select style='text-align:center'
						onchange = 	"
										if( this.value != '')
										{
											var selectedEditor = 'email_content_'+jQuery('dl.tabs dt.open a').text();
											tinyMCE.get(selectedEditor).execCommand('mceReplaceContent',false,this.value); 
										}
									"
					>
						<option></option>

						
						<option value="<?php echo htmlentities(EMAIL_RESERVATIONFIRSTNAME)?>"><?php echo htmlspecialchars(EMAIL_RESERVATIONFIRSTNAME)?></option>
						<option value='<?php echo htmlentities(EMAIL_RESERVATIONLASTNAME)?>'><?php echo htmlentities(EMAIL_RESERVATIONLASTNAME)?></option>
						<option value='<?php echo htmlentities(EMAIL_RESERVATIONDETAILS)?>'><?php echo htmlentities(EMAIL_RESERVATIONDETAILS)?></option>
						<option value='<?php echo htmlentities(EMAIL_BILINGINFORMATIONS)?>'><?php echo htmlentities(EMAIL_BILINGINFORMATIONS)?></option>
						<option value='<?php echo htmlentities(EMAIL_COMPANY_NAME)?>'><?php echo htmlentities(EMAIL_COMPANY_NAME)?></option>
						<option value='<?php echo htmlentities(EMAIL_COMPANY_LOGO)?>'><?php echo htmlentities(EMAIL_COMPANY_LOGO)?></option>
						<option value='<?php echo htmlentities(EMAIL_HOTEL_IMAGE)?>'><?php echo htmlentities(EMAIL_HOTEL_IMAGE)?></option>
						<option value='<?php echo htmlentities(EMAIL_RATING_URL)?>'><?php echo htmlentities(EMAIL_RATING_URL)?></option>
						<option value='<?php echo htmlentities(EMAIL_SOCIAL_SHARING)?>'><?php echo htmlentities(EMAIL_SOCIAL_SHARING)?></option>
						<option value='<?php echo htmlentities(EMAIL_INVOICE_HOTEL_DETAILS)?>'><?php echo htmlentities(EMAIL_INVOICE_HOTEL_DETAILS)?></option>
						<option value='<?php echo htmlentities(EMAIL_INVOICE_DATE)?>'><?php echo htmlentities(EMAIL_INVOICE_DATE)?></option>
						<option value='<?php echo htmlentities(EMAIL_INVOICE_NUMBER)?>'><?php echo htmlentities(EMAIL_INVOICE_NUMBER)?></option>
						<option value='<?php echo htmlentities(EMAIL_INVOICE_FIELDS)?>'><?php echo htmlentities(EMAIL_INVOICE_FIELDS)?></option>
						
						<option value='<?php echo htmlentities(EMAIL_START_DATE)?>'><?php echo htmlentities(EMAIL_START_DATE)?></option>
						<option value='<?php echo htmlentities(EMAIL_END_DATE)?>'><?php echo htmlentities(EMAIL_END_DATE)?></option>
						<option value='<?php echo htmlentities(EMAIL_CHECKIN_TIME)?>'><?php echo htmlentities(EMAIL_CHECKIN_TIME)?></option>
						<option value='<?php echo htmlentities(EMAIL_CHECKOUT_TIME)?>'><?php echo htmlentities(EMAIL_CHECKOUT_TIME)?></option>
					</select>
					&nbsp; <?php echo JText::_('LNG_EMAILS_TAG_EDITOR',true)?>
					<BR>
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
						  	echo $editor->display('email_content_'.$_lng, $langContent, '800', '400', '70', '15', false);
						  
						  }
						  echo JHtml::_('tabs.end');
						  ?>
				</TD>
			</TR>
		</TABLE>
	</fieldset>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'save') 
			{
				if( !validateField( form.elements['email_default_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_EMAIL_NAME',true); ?>" ) )
					return false;
				//if( !validateField( form.email_description, 'string', false, "Please insert content email !" ) )
				//	return false;
			
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>

	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="email_default_id" value="<?php echo $this->item->email_default_id ?>" />
	<input type="hidden" name="controller" value="manageemailsdefault" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

