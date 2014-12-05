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
if( strlen( $this->_models['variables']->tmp ) > 0 )
	$need_all_fields = $this->_models['variables']->tmp > strtotime( " - 10 min ")? false : true; //allow only 10 min for admin
?>

<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationinfo.php'; ?>
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<div class="right">
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

<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="userForm" >
	<div class="hotel_reservation">
		<table width="100%" cellspacing="0" >
			<TR>
				<TD valign=top colspan=1>
					<?php echo $this->_models['variables']->Reservation_Details?>
				</TD>
			</TR>
			<TR>
				<TD valign=top colspan=1>
					&nbsp;
				</TD>
			</TR>
			<?php if($this->_models['variables']->itemAppSettings->enable_discounts &&  $this->_models['variables']->showDiscounts ){?>
			<TR>
				<TD valign=top colspan=1>
					<fieldset class="dicount-code-block">
				  		  <h4><?php echo JText::_('LNG_DISCOUNT_CODE',true);?></h4>
				    	<div style="margin-left:240px;">
				      	 	 <span class="button button-green right">
								<button value="checkRates" name="checkRates" type="button" 
									onclick="applyDiscountCode()">
								<?php echo JText::_('LNG_APPLY',true);?>
								</button>
							</span>
				            <label for="coupon_code"><?php echo JText::_('LNG_DISCOUNT_TXT',true);?></label><br/>
				          	<input size="40" value="<?php echo $this->_models['variables']->discount_code ?>" name="discount_code" id="discount_code" class="input-text"> &nbsp;
				          	
				        </div>
				    </fieldset>
				</TD>
			</TR>
			<?php } ?>
			<TR>
				<TD align=left colspan=1>	
					<?php echo JText::_('LNG_OVERVIEW_RESERVATION_INFO',true);?> 
				</TD>
			</TR>
			
			<TR>
				<TD align=left colspan=1>	
					<?php 
						$taxText ='';
						$tax = JHotelUtil::fmt($this->_models['variables']->itemHotelSelected->informations->city_tax,2);
						if($tax>0){
							if($this->_models['variables']->itemHotelSelected->informations->city_tax_percent){
								$taxText =  JText::_('LNG_CITY_TAX_INFO_PERCENT',true);
								$tax = $tax.'%';
							}else{
								$taxText =  JText::_('LNG_CITY_TAX_INFO',true);
								$tax = $this->_models['variables']->itemCurrency->currency_symbol.' '.$tax;
							}
							
							$taxText = str_replace("<<city-tax>>", $tax, $taxText);
							
							echo $taxText;
						}
					?> 
				</TD>
			</TR>
			
			<TR style="display:none">
				<TD align=left colspan=1>	
					<B><?php echo JText::_('LNG_SPECIAL_NOTES',true);?> </B><?php echo $this->_models['variables']->itemAppSettings->special_notes?>
				</TD>
			</TR>
			<TR style="display:none">
				<TD align=left colspan=1>	
					<B><?php echo JText::_('LNG_TERMS_AND_CONDITIONS',true);?> </B> <?php echo $this->_models['variables']->itemAppSettings->terms_and_conditions?>
					<br/>
					<br/>
				</TD>
			</TR>
			<tr>
				<td class="header_line" colspan="1">
					<strong><?php echo JText::_('LNG_ACCOUNT_DETAILS',true);?></strong>
				</td>
			</tr>
			<TR>
				<TD  colspan=1 align=left  style="padding-top:10px;padding-bottom:10px;">	
					-<?php echo JText::_('LNG_FIELDS_MARKED_WITH',true);?>
					<span class="mand">*</span>
					<?php echo JText::_('LNG_ARE_MANDATORY',true);?>-
				</TD>
			</TR>
			<?php 
			if($this->_models['variables']->itemAppSettings->save_all_guests_data)
			{ 
			?>
				<tr style='background-color:##CCCCCC'>
					<td class="header_line" colspan="3">
						<strong><?php echo JText::_('LNG_GUEST_INFORMATIONS',true);?></strong>
					</td>
				</tr>
				<tr style='background-color:##CCCCCC'>
					<td colspan="3">
						<table>
							
				<?php for($i=1;$i<=$this->_models['variables']->guest_adult;$i++){
					if( !isset($this->_models['variables']->guest_first_name[$i-1]) )
						$this->_models['variables']->guest_first_name[$i-1] = '';
					if( !isset($this->_models['variables']->guest_last_name[$i-1]) )
						$this->_models['variables']->guest_last_name[$i-1] = '';
					if( !isset($this->_models['variables']->guest_identification_number[$i-1]) )
						$this->_models['variables']->guest_identification_number[$i-1] = '';
				?>	
				<tr style='background-color:##CCCCCC'>
					
					<TD colspan=1 width=20%  align=left>
						<?php echo JText::_('LNG_GUEST_DETAILS',true);?>
					</TD>
				
					<TD  align=left>
					 <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME',true);?></label> <span class="mand">*</span><br/>
						<input class="req-field" 
							type 			= 'text'
							name			= 'guest_first_name[]'
							id				= 'guest_first_name'
							
							size			= 25
							value			= "<?php echo $this->_models['variables']->guest_first_name[$i-1]?>">
					</TD>	
					<td>
						<label for="guest_last_name"><?php echo JText::_('LNG_LAST_NAME',true);?></label> <span class="mand">*</span><br/>
						<input  class="req-field"
							type 			= 'text'
							name			= 'guest_last_name[]'
							id				= 'guest_last_name'
							
							size			= 25
							value			= "<?php echo $this->_models['variables']->guest_last_name[$i-1]?>">
					</td>
				
					<td><label for="guest_identification_number"><?php echo JText::_('LNG_PASSPORT_NATIONAL_ID',true);?></label><BR/>
						<input class=""
							type 			= 'text'
							name			= 'guest_identification_number[]'
							id				= 'guest_identification_number'
							
							size			= 25
							value			= "<?php echo $this->_models['variables']->guest_identification_number[$i-1]?>">
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
					<strong><?php echo JText::_('LNG_BILLING_INFORMATION',true);?></strong>
				</td>
			</tr>
			<TR>
				<TD  colspan=1  align=left>	
					<TABLE width=100% valign=top class='table_data'>
						<tr style='background-color:##CCCCCC;display:none'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_COMPANY_NAME',true);?> 
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'company_name'
									id				= 'company_name'
									
									size			= 25
									value			= "<?php echo $this->_models['variables']->company_name?>"
								>
							</TD>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('LNG_GENDER_TYPE',true);?> <span class="mand">*</span>
							</td>
							<td>
									<?php 
										
										echo JHtml::_( 'select.radiolist', $this->_models['variables']->guest_types, 'guest_type', '', 'value', 'text',  $this->_models['variables']->guest_type,'guest_type'); 
									?>
														</td>
						</tr>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_FIRST_NAME',true);?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'first_name'
									id				= 'first_name'
									
									size			= 25
									value			= "<?php echo $this->_models['variables']->first_name?>"
								>
							</TD>
						</tr>

						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_LAST_NAME',true);?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'last_name'
									id				= 'last_name'
									
									size			= 25
									value			= "<?php echo $this->_models['variables']->last_name?>"
								>
							</TD>
						</TR>
						
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_BILLING_ADDRESS',true);?><span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'address'
									id				= 'address'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->address?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_POSTAL_CODE',true);?><?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'postal_code'
									id				= 'postal_code'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->postal_code?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_CITY',true);?><?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'city'
									id				= 'city'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->city?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC; display:none'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_STATE',true);?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'state_name'
									id				= 'state_name'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->state_name?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_COUNTRY',true);?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=2 align=left>
								<select	id= "country" name	= "country"	>
									<option value = ''> <?php echo JText::_('LNG_SELECT_COUNTRY',true);?></option>
									<?php
									
									foreach($this->_models['variables']->countries as $country)
									{
									?>	
									<option value = '<?php echo $country->country_name?>' <?php echo ($country->country_name==$this->_models['variables']->country || $country->country_name == "Nederland")? "selected" : ""?>> <?php echo $country->country_name?></option>
									<?php
									}
									?>
								</select>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_TELEPHONE_NUMBER',true);?> <span class="mand">*</span>
							</TD>
							<TD colspan=2 align=left>
								<input 
									type 			= 'text'
									name			= 'tel'
									id				= 'tel'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->tel?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_EMAIL',true);?> <span class="mand">*</span>
							</TD>
							<TD align=left>
								<input 
									type 			= 'text'
									name			= 'email'
									id				= 'email'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->email?>"
								>
							</TD>
							<TD rowspan=2 >
								<?php echo JText::_('LNG_PLEASE_NOTE_THAT_YOUR_EMAIL_WILL_BE_USED_AS_A_USERNAME',true);?>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_CONFIRM_EMAIL',true);?> <?php echo $need_all_fields? "<span class='mand'>*</span>" : ""?>
							</TD>
							<TD colspan=1 align=left>
								<input 
									type 			= 'text'
									name			= 'conf_email'
									id				= 'conf_email'
									
									size			= 50
									value			= "<?php echo $this->_models['variables']->conf_email?>"
								>
							</TD>
						</TR>
						<tr style='background-color:##CCCCCC'>
							<TD colspan=1 width=20%  align=left>
								<?php echo JText::_('LNG_EXTRA_INFO',true);?> 
							</TD>
							<TD colspan=2 align=left>
								<textarea name='details' id='details'  rows="3" cols="38" ><?php echo $this->_models['variables']->details?></textarea>
							</TD>
						</tr>
					</TABLE>
				</TD>
			</TR>
			<?php
			if($this->_models['variables']->itemAppSettings->is_enable_payment )
			{
			?>
			<TR>
				<TD colspan=1 align=left>	
					&nbsp;
				</TD>
			</TR>
			<tr style="display:<?php echo $cssDisplay ?>">
				<td class="header_line" colspan="1">
					<strong><?php echo JText::_('LNG_PAYMENT_DETAILS',true);?></strong>
				</td>
			</tr>
			<tr style="display:<?php echo $cssDisplay ?>">
				<td>
					<?php echo JText::_('LNG_PAYMENT_OPTION_SELECT',true);?>
				</td>
			</tr>
			<TR style="display:<?php echo $cssDisplay ?>" <?php echo count($this->_models['variables']->itemPaymentProcessors) == 0 ? "style='display:none'" : "" ?> >
				<TD valign=top align=center>
					<?php echo $this->_models['variables']->displayPaymentProcessors()?>
				</td>
			</TR>

			<?php
			}
			?>
			<TR style="display:block">
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
			</TR>
			<TR >
				<TD valign=top align=left>
					<BR>
					<div class='div_reservation_policies_title' style="display:none"><?php echo JText::_('LNG_RESERVATION_POLICIES',true);?></div>
					<div class='div_reservation_policies_info' style="display:none">
						<?php echo JText::_('LNG_RESERVATION_POLICIES_DETAILS',true);?>
					</div>
					<div>
						<input 
							type 		='checkbox'
							id			= 'is_accept_policies'
							name		= 'is_accept_policies'
						>&nbsp; <a href="javascript:void(0);" onclick="showTerms()"><?php echo JText::_('LNG_AGREE_WITH_TERMS',true)?></a>
					</div>
				</TD>
			</TR>
		</table>
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
							<?php echo JText::_('LNG_MAKE_RESERVATION',true);?>
							</button>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div> 
	<input type="hidden" name="option" 					id="option" 						value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" 					id="task" 							value="" />
	<input type="hidden" name="tip_oper" 				id="tip_oper" 						value="<?php echo JRequest::getVar( 'tip_oper') ?>" />
	<input type="hidden" name="tmp" 					id="tmp" 							value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="controller" 				id="controller" 					value="" />
	<input type="hidden" name="view" 					id="view" 							value="JHotelReservation" /> 
	<input type="hidden" name="_lang" id="_lang" 		value="<?php echo JRequest::getVar('_lang') ?>" />
	<input type="hidden" name="hotel_id" 				id="hotel_id" 						value="<?php echo $this->_models['variables']->hotel_id?>" 				/> 	                                                
																	
	<input type="hidden" name="year_start" 				id="year_start" 					value="<?php echo $this->_models['variables']->year_start?>" 				/> 
	<input type="hidden" name="month_start" 			id="month_start" 					value="<?php echo $this->_models['variables']->month_start?>" 				/> 
	<input type="hidden" name="day_start" 				id="day_start" 						value="<?php echo $this->_models['variables']->day_start?>" 					/> 
	<input type="hidden" name="year_end" 				id="year_end" 						value="<?php echo $this->_models['variables']->year_end?>" 					/> 
	<input type="hidden" name="month_end" 				id="month_end" 						value="<?php echo $this->_models['variables']->month_end?>" 					/> 
	<input type="hidden" name="day_end" 				id="day_end" 						value="<?php echo $this->_models['variables']->day_end?>" 					/> 
	<input type="hidden" name="rooms" 					id="rooms" 							value="<?php echo $this->_models['variables']->rooms?>" 						/> 
	<input type="hidden" name="guest_adult" 			id="guest_adult" 					value="<?php echo $this->_models['variables']->guest_adult?>" 				/> 
	<input type="hidden" name="guest_child" 			id="guest_child" 					value="<?php echo $this->_models['variables']->guest_child?>" 				/> 
	<input type="hidden" name="coupon_code"				id="coupon_code"					value="<?php echo $this->_models['variables']->coupon_code?>" 					/> 
	<input type="hidden" name="room_available_ids"		id="room_available_ids"				value="<?php echo implode(',' , $this->_models['variables']->room_available_ids)?>" 	/> 
	<input type="hidden" name="itemRoomsCapacity"		id="itemRoomsCapacity"				value="<?php echo $this->_models['variables']->getStringRoomsCapacity($this->_models['variables']->itemRoomsCapacity)?>" 	/> 
	<input type="hidden" name="option_ids"				id="option_ids"						value="<?php echo implode(',' , $this->_models['variables']->option_ids)?>" 	/> 
	<input type="hidden" name="room_ids"				id="room_ids"						value="<?php echo implode(',' , $this->_models['variables']->room_ids)?>" 	/> 
	<input type="hidden" name="currency_selector"		id="currency_selector"				value="<?php echo $this->_models['variables']->currency_selector?>" 					/> 
	<input type="hidden" name="reserve_room_id" 		id="reserve_room_id" 				value="" 																				/> 
	<input type="hidden" name="reserve_offer_id"		id="reserve_offer_id" 				value="" 																				/> 
	<input type="hidden" name="reserve_current" 		id="reserve_current" 				value="<?php echo $this->_models['variables']->reserve_current?>" 						/> 
	<input type="hidden" name="mediaReferer" 			id="mediaReferer" 					value="<?php echo $this->_models['variables']->mediaReferer?>"/>
	<input type="hidden" name="voucher" 				id="voucher" 						value="<?php echo $this->_models['variables']->voucher?>"/>
	
	<?php
	$this->_models['variables']->displayHiddenValues( 'items_reserved', 				array('type'=>'value') );
	$this->_models['variables']->displayHiddenValues( 'package_ids', 					array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'package_day', 					array('type'=>'multiarray') );
	$this->_models['variables']->displayHiddenValues( 'itemPackageNumbers', 			array('type'=>'array', 'check_field_zero'=>4) );
	$this->_models['variables']->displayHiddenValues( 'arrival_option_ids', 			array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'extraOptionIds', 				array('type'=>'array')  );
	$this->_models['variables']->displayHiddenValues( 'airport_airline_ids',			array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_type_ids', 		array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_dates', 		array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_hours', 	array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_time_mins', 	array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_flight_nrs', 	array('type'=>'array') );
	$this->_models['variables']->displayHiddenValues( 'airport_transfer_guests', 		array('type'=>'array') );
	?>	
	<script>
		function checkContinue()
		{
			var is_ok	= false;
			var form 	= document.forms['userForm'];
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
			else if( !validateField( form.elements['tel'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_TELEPHONE_NUMBER',true);?>" ) )
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
			?>
			
			if(!form.elements['is_accept_policies'].checked)
			{
				alert("<?php echo JText::_('LNG_MUST_ENABLE_ACCEPT_POLICY',true)?>"+'!');
				return false;
			}
			
			<?php
			$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
			if( $appSettings->save_all_guests_data )
			{
			?>
			if(!checkGuestDetails())
				return false;
			<?php
			}
			?>
			<?php
			}
			?>

			form.elements['tip_oper'].value = '5';
			return true;
			
		}

		function applyDiscountCode(){
			var form 	= document.forms['userForm'];
			form.elements['tip_oper'].value = '4';
			form.submit();
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
			form.elements['tip_oper'].value = '-1';
			deleteReservedItems();
			form.task.value		="checkAvalability";
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
	<?php echo $this->_models['variables']->itemAppSettings->terms_and_conditions?>
</div>
</div>
</div>
	
</div>
