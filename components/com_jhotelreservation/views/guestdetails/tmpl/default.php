<?php // no direct access
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

defined('_JEXEC') or die('Restricted access'); 

$isSuperUser = isSuperUser(JFactory::getUser()->id);
$cssDisplay = $isSuperUser?"block":"none";
$need_all_fields = true;
?>

<?php // require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationinfo.php'; ?>
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" id="userForm">
	<div class="hotel_reservation row-fluid">
		<div class="right span3 hidden-phone">
			<?php 
				jimport('joomla.application.module.helper');
				// this is where you want to load your module position
				$modules = JModuleHelper::getModules('reservation-info');
			
				foreach($modules as $module)
				{
					echo JModuleHelper::renderModule($module);
				}
			?>
		</div>
		<div class="guestDetails span9">
		<table cellspacing="0"  style="width:100%">
			<TR>
				<TD valign=top colspan=1>
					&nbsp;
				</TD>
			</TR>
						
			<tr>
				<td class="header_line" colspan="1">
					<strong><?php echo JText::_('LNG_ACCOUNT_DETAILS');?></strong>
				</td>
			</tr>
			<TR>
				<TD  colspan=1 align=left  style="padding-top:10px;padding-bottom:10px;">	
					-<?php echo JText::_('LNG_FIELDS_MARKED_WITH');?>
					<span class="mand">*</span>
					<?php echo JText::_('LNG_ARE_MANDATORY');?>-
				</TD>
			</TR>
			<?php 
			if($this->appSettings->save_all_guests_data)
			{ 
			?>
				<tr style='background-color:##CCCCCC'>
					<td class="header_line" colspan="3">
						<strong><?php echo JText::_('LNG_GUEST_INFORMATIONS');?></strong>
					</td>
				</tr>
				<tr style='background-color:##CCCCCC'>
					<td colspan="3">
						<table>
							
				<?php  foreach($this->userData->guestDetails as $guestDetail){
					
				?>	
				<tr style='background-color:##CCCCCC'>
					
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_GUEST_DETAILS');?>
					</TD>
				
					<TD  align=left>
					 <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME');?></label> <span class="mand">*</span><br/>
						<input class="req-field" style="width: auto !important"
							type 			= 'text'
							name			= 'guest_first_name[]'
							id				= 'guest_first_name'
							size			= 20
							value			= "<?php echo $guestDetail->first_name?>">
					</TD>	
					<td>
						<label for="guest_last_name"><?php echo JText::_('LNG_LAST_NAME');?></label> <span class="mand">*</span><br/>
						<input  class="req-field" style="width: auto !important"
							type 			= 'text'
							name			= 'guest_last_name[]'
							id				= 'guest_last_name'
							size			= 20
							value			= "<?php echo $guestDetail->last_name?>">
					</td>
				
					<td><label for="guest_identification_number"><?php echo JText::_('LNG_PASSPORT_NATIONAL_ID');?></label><BR/>
						<input class="" style="width: auto !important"
							type 			= 'text'
							name			= 'guest_identification_number[]'
							id				= 'guest_identification_number'
							size			= 20
							value			= "<?php echo $guestDetail->identification_number?>">
					</td>
				</tr>
			<?php 
					} ?>

					</table>
				</td>
			</tr>
			<?php 		
			} 
			?>
			<tr>
				
				<td class="header_line" colspan="1">
					<strong><?php echo JText::_('LNG_BILLING_INFORMATION');?></strong>
				</td>
			</tr>
			<TR>
				<TD  colspan=1  align=left>	
					<TABLE width=100% valign=top class='table_data'>
						<tr style='background-color:##CCCCCC;display:none'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_COMPANY_NAME');?> 
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'company_name'
									id				= 'company_name'
									size			= 25
									value			= "<?php echo $this->userData->company_name?>"
								>
							</TD>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('LNG_GENDER_TYPE');?> <span class="mand">*</span>
							</td>
							<td>
								<?php 
									echo JHtml::_( 'select.radiolist', $this->guestTypes, 'guest_type', '', 'value', 'text',  $this->userData->guest_type,'guest_type'); 
								?>
							</td>
						</tr>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_FIRST_NAME');?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'first_name'
									id				= 'first_name'
									size			= 25
									value			= "<?php echo $this->userData->first_name?>"
								>
							</TD>
						</tr>

						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_LAST_NAME');?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'last_name'
									id				= 'last_name'
									size			= 25
									value			= "<?php echo $this->userData->last_name?>"
								>
							</TD>
						</TR>
						
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_BILLING_ADDRESS');?><span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'address'
									id				= 'address'
									size			= 50
									value			= "<?php echo $this->userData->address?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_POSTAL_CODE');?><?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'postal_code'
									id				= 'postal_code'
									size			= 50
									value			= "<?php echo $this->userData->postal_code?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_CITY');?><?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'city'
									id				= 'city'
									size			= 50
									value			= "<?php echo $this->userData->city?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC; display:none'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_STATE');?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'state_name'
									id				= 'state_name'
									size			= 50
									value			= "<?php echo $this->userData->state_name?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_COUNTRY');?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<select	id= "country" name	= "country"	>
									<option value = ''> <?php echo JText::_('LNG_SELECT_COUNTRY');?></option>
									<?php
									
									foreach($this->countries as $country)
									{
									?>	
									<option value = '<?php echo $country->country_name?>' <?php echo ($country->country_name==$this->userData->country || $country->country_name == "Nederland")? "selected" : ""?>> <?php echo $country->country_name?></option>
									<?php
									}
									?>
								</select>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left nowrap="nowrap">
								<?php echo JText::_('LNG_TELEPHONE_NUMBER');?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'phone'
									id				= 'phone'
									size			= 50
									value			= "<?php echo $this->userData->phone?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_EMAIL');?> <span class="mand">*</span>
							</TD>
							<TD align=left>
								<input 
									type 			= 'text'
									name			= 'email'
									id				= 'email'
									size			= 50
									value			= "<?php echo $this->userData->email?>"
								>
								<br/>
								<?php echo JText::_('LNG_PLEASE_NOTE_THAT_YOUR_EMAIL_WILL_BE_USED_AS_A_USERNAME');?>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_CONFIRM_EMAIL');?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=1 align=left>
								<input 
									type 			= 'text'
									name			= 'conf_email'
									id				= 'conf_email'
									size			= 50
									value			= "<?php echo $this->userData->conf_email?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_EXTRA_INFO');?> 
							</TD>
							<TD colspan=2 align=left>
								<textarea name='remarks' id='remarks'  rows="3" cols="38" ><?php echo $this->userData->remarks?></textarea>
							</TD>
						</tr>
					</TABLE>
				</TD>
			</TR>
			
<!-- 			<TR style="display:block">
				<TD valign=top align=left>
					<BR>
					<div>
						<input 
							type 		='checkbox'
							id			= 'subscribeToNewsletter'
							name		= 'subscribeToNewsletter'
							value       = 1
						>&nbsp; <?php echo JText::_('LNG_SUBSCRIBE_TO_NEWSLETTER',true)?>
					</div>
				</TD>
			</TR> -->
			
		</table>
		</div>
		<BR>
		<div CLASS='DIV_BUTTONS'>
			<table width='100%' align=center>
				<tr>
					<td align=left>
						<span class="button button-green">
							<button value="checkRates" name="checkRates" type="button" onclick="formBack()">
								<?php echo JText::_('LNG_BACK',true)?>
							</button>
						</span>
					</td>
					<td align=right>
					   <span class="button button-green right">
							<button value="checkRates" name="checkRates" type="button" 
								onclick="if (checkContinue()) document.forms['userForm'].submit();">
							<?php echo JText::_('LNG_NEXT');?>
							</button>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div> 
	<input type="hidden" name="task" id="task" value="guestdetails.addGuestDetails" />
	<input type="hidden" name="hotel_id" id="hotel_id" 	value="<?php echo $this->hotel->hotel_id?>" 				/> 	                                                
	<input type="hidden" name="reservedItems" id="reservedItems" value="<?php echo JRequest::getVar("reservedItems") ?>" />																	

	<script>
		function checkContinue()
		{
			var is_ok	= false;
			var form 	= document.forms['userForm'];
			if (!jQuery("input[name='guest_type']:checked").val()) {
			    alert( "<?php echo JText::_('LNG_PLEASE_SELECT_GUEST_TYPE',true);?>");
				return false;
		    }
			if( !validateField( form.elements['first_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_FIRST_NAME',true);?>" ) )
			{
				return false;
			}
			else if( !validateField( form.elements['last_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_LAST_NAME',true);?>" ) )
			{
				return false;
			}
			else if( !validateField( form.elements['address'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_ADDRESS',true);?>" ) )
			{
				return false;
			}

			
			<?php 
			if( $need_all_fields )
			{
			?>
			else if( !validateField( form.elements['city'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_CITY_NAME',true);?>" ) )
			{
				return false;
			}
			else if( form.elements['country'].selectedIndex ==0 )
			{
				alert( "<?php echo JText::_('LNG_PLEASE_SELECT_COUNTRY',true);?>");
				return false;
			}
			//else if( !validateField( form.elements['state_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_STATE_NAME',true);?>" ) )
			//{
			//	return false;
			//}
			else if( !validateField( form.elements['postal_code'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_POSTAL_CODE',true);?>" ) )
			{
				return false;
			}
			<?php 
			}
			?>
			else if( !validateField( form.elements['phone'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_TELEPHONE_NUMBER',true);?>" ) )
			{
				return false;
			}
			else if( !validateField( form.elements['email'], 'email', false, "<?php echo JText::_('LNG_PLEASE_INSERT_EMAIL_ADDRESS',true);?>" ) )
			{
				return false;
			}
			<?php 
			if( $need_all_fields )
			{
			?>
			else if( !validateField( form.elements['conf_email'], 'email', false, "<?php echo JText::_('LNG_PLEASE_INSERT_EMAIL_CONFIRMATION_ADDRESS',true);?>" ) )
			{
				return false;
			}
			else if( form.elements['email'].value != form.elements['conf_email'].value )
			{
				alert("<?php echo JText::_('LNG_PLEASE_INSERT_SAME_EMAILS_ADDRESSES',true);?>")
				return false;
			}
			<?php 
			}
			
			
			if( $this->appSettings->save_all_guests_data )
			{
			?>
			if(!checkGuestDetails())
				return false;
			<?php
			}
			?>
			
			return true;
		}

		function checkGuestDetails(){
			var is_ok	= true;
			var form 	= document.forms['userForm'];

			var taskArray = new Array();
			jQuery(".req-field").each(function() {
			   if(!jQuery(this).val()){
				   is_ok = false;
				   alert("<?php echo JText::_('LNG_PLEASE_INSERT_ALL_GUEST_DETAILS',true);?>");
				   jQuery(this).focus();
				   return false;
			   }
			});

			return is_ok;
		}


		
		
		function formBack() 
		{
			var form 	= document.forms['userForm'];
			form.task.value	="guestdetails.back";
			form.submit();
		}

		function showTerms(){
			jQuery.blockUI({ message: jQuery('#conditions'), css: {
								top:  50 + 'px', 
					            left: (jQuery(window).width() - 800) /2 + 'px',
								width: '800px', 
								backgroundColor: '#fff' }});
			jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI); 
		} 
	</script>
</form>

<div id="conditions" class="terms-conditions" style="display:none">
<div id="dialog-container">
<div class="titleBar">
<span class="dialogTitle" id="dialogTitle"></span>
<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
<span title="Cancel" class="closeText">x</span>
</span>
</div>

<div class="dialogContent">
<h3 class="title"> <?php echo JText::_('LNG_TERMS_AND_CONDITIONS',true);?></h3>
<div class="dialogContentBody" id="dialogContentBody">
	<?php echo $this->appSettings->terms_and_conditions?>
</div>
</div>
</div>
	
</div>
