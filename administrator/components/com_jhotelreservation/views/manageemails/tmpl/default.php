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
			<legend><?php echo JText::_('LNG_MANAGE_EMAIL_TEMPLATES',true); ?></legend>
			<center>
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "manageemails";
									form.appendChild(obView);
									// form.view.value="managerooms";
									form.submit();
									'
					>
						<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php
						foreach($this->hotels as $hotel )
						{
						?>
						<option value='<?php echo $hotel->hotel_id?>' 
							<?php echo $this->hotel_id ==$hotel->hotel_id? 'selected' : ''?>
						>
							<?php 
								echo stripslashes($hotel->hotel_name);
								echo (strlen($hotel->country_name)>0? ", ".$hotel->country_name : "");
								echo stripslashes(strlen($hotel->hotel_city)>0? ", ".$hotel->hotel_city : "");
							?>
						</option>
						<?php
						}
						?>
					</select>
					<hr>
				</div>
				<?php
				if( $this->hotel_id > 0  )
				{
				?>
				<TABLE class="adminlist" >
					<thead>
						<th width='1%'>#</th>
						<th width='1%'  align=center>&nbsp;</th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_TYPE',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_SUBJECT',true); ?></B></th>
						<th width='30%' align=center ><B><?php echo JText::_('LNG_CONTENT',true); ?></B></th>
						<th width='1%' align=center><B><?php echo JText::_('LNG_DEFAULT',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$email = $this->items[$i]; 
						$emailContent = $this->hoteltranslationsModel->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION,$email->email_id,JRequest::getVar( '_lang'));
					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $email->email_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.email_id.value = '<?php echo $email->email_id?>'
										" 
							/>
							
						</TD>
						<TD align=left>
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageemails.edit&email_id[]='. $email->email_id.'&hotel_id='.$this->hotel_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $email->email_name?></B>
							</a>	
							
						</TD>
						<TD align=center><?php echo $email->email_type?></TD>
						<TD align=center><?php echo $email->email_subject?></TD>
						<TD wrap align=left><?php echo isset($emailContent)?$emailContent->content:"";?></TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($email->is_default==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"	
												<?php
												if( $email->is_default ==false )
												{
												?>
												document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageemails.state&email_id[]='. $email->email_id.'&hotel_id='.$this->hotel_id )?> '
												<?php
												}
												?>
											"
							/>
							
						</TD>
						
					</TR>
					<?php
					}
					?>
					</tbody>
				</TABLE>
				<?php
				}
				?>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="email_id" value="" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'edit' || pressbutton == 'Delete') 
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
			} else {
				submitform( pressbutton );
			}
		}

			jQuery(document).ready(function()
				{
					var hotelId=jQuery('#hotel_id').val();
					var refreshScreen=jQuery('#refreshScreen').val();
					var nrHotels = jQuery('#hotel_id option').length;
					if(refreshScreen=="" && parseInt(nrHotels)==2){
						jQuery('#hotel_id :nth-child(2)').prop('selected', true); 
						jQuery('#refreshScreen').val("true");
						jQuery("#hotel_id").trigger('change');	
					}
				});	
	</script>
</form>
<?php
}
else 
{
?>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_EMAIL_DETAILS',true); ?></legend>
		<center>
		<div style='text-align:left'>
			<strong>
				<?php echo JText::_('LNG_HOTEL',true)?> : 
				<?php 
					echo stripslashes($this->hotel->hotel_name);
					echo (strlen($this->hotel->country_name)>0? ", ".$this->hotel->country_name : "");
					echo stripslashes(strlen($this->hotel->hotel_city)>0? ", ".$this->hotel->hotel_city : "");
				?>
			</strong>
			<hr>
		</div>
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
				<TD nowrap colspan=2 align=left>
					<select
						id 		= "email_type"
						name	= "email_type"
						style	= "width:145px"
					>
						<option <?php echo $this->item->email_type=='Reservation Email'? "selected" : ""?> value='Reservation Email'><?php echo JText::_('LNG_RESERVATION_EMAIL',true); ?></option>
						<option <?php echo $this->item->email_type=='Cancelation Email'? "selected" : ""?> value='Cancelation Email'><?php echo JText::_('LNG_CANCELATION_EMAIL',true); ?></option>
						<option <?php echo $this->item->email_type=='Review Email'? "selected" : ""?> value='Review Email'><?php echo JText::_('LNG_REVIEW_EMAIL',true); ?></option>
						<option <?php echo $this->item->email_type=='Invoice Email'? "selected" : ""?> value='Invoice Email'><?php echo JText::_('LNG_INVOICE_EMAIL',true); ?></option>
						<option <?php echo $this->item->email_type=='Bookings List'? "selected" : ""?> value='Bookings List'><?php echo JText::_('LNG_BOOKINGS_LIST',true); ?></option>
						<option <?php echo $this->item->email_type=='Guest List Email'? "selected" : ""?> value='Guest List Email'><?php echo JText::_('LNG_GUEST_LIST',true); ?></option>
						</select>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "email_name"
						id			= "email_name"
						value		= '<?php echo $this->item->email_name?>'
						size		= 50
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_SUBJECT',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "email_subject"
						id			= "email_subject"
						value		= '<?php echo $this->item->email_subject?>'
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
				if( !validateField( form.elements['email_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_EMAIL_NAME',true); ?>" ) )
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
	<input type="hidden" name="email_id" value="<?php echo $this->item->email_id ?>" />
	<input type="hidden" name="is_default" value="<?php echo $this->item->is_default?>" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />
	<input type="hidden" name="controller" value="manageemails" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

