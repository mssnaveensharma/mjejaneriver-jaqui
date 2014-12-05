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

<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userForm" id="userForm">
	<input type="hidden" name="task" 	 id="task" 		value="paymentoptions.processPayment" />
	<input type="hidden" name="hotel_id" id="hotel_id"	value="<?php echo $this->hotel->hotel_id?>" />
	<div class="hotel_reservation">
		<table width="100%" cellspacing="0" >
			<TR>
				<TD valign=top colspan=1>
					<?php echo $this->reservationDetails->reservationInfo?>
				</TD>
			</TR>
			<TR>
				<TD valign=top colspan=1>
					&nbsp;
				</TD>
			</TR>
			<TR>
				<TD align=left colspan=1>	
					<?php echo JText::_('LNG_OVERVIEW_RESERVATION_INFO');?> 
				</TD>
			</TR>
			<?php if($this->appSettings->enable_discounts &&  $this->reservationDetails->showDiscounts ){?>
			<TR>
				<TD valign=top colspan=1>
					<fieldset class="dicount-code-block">
				  		  <h4><?php echo JText::_('LNG_DISCOUNT_CODE');?></h4>
				    	<div style="margin-left:250px;">
				      	 	 <span class="button button-green right">
								<button value="checkRates" name="checkRates" type="button" 
									onclick="applyDiscountCode()">
								<?php echo JText::_('LNG_APPLY');?>
								</button>
							</span>
				            <label for="coupon_code"><?php echo JText::_('LNG_DISCOUNT_TXT');?></label><br/>
				          	<input type="text" size="40" value="<?php echo $this->userData->discount_code ?>" name="discount_code" id="discount_code" class="input-text noSubmit"> &nbsp;
				          	
				        </div>
				    </fieldset>
				</TD>
			</TR>
			<?php } ?>
			<TR>
				<TD align=left colspan=1>	
					<?php 
						$taxText ='';
						$tax = JHotelUtil::fmt($this->hotel->informations->city_tax,2);
						if($tax>0){
							if($this->hotel->informations->city_tax_percent){
								$taxText =  JText::_('LNG_CITY_TAX_INFO_PERCENT',true);
								$tax = $tax.'%';
							}else{
								$taxText =  JText::_('LNG_CITY_TAX_INFO',true);
								$tax = $this->userData->currency->symbol.' '.$tax;
							}
							
							$taxText = str_replace("<<city-tax>>", $tax, $taxText);
							
							echo $taxText;
						}
					?> 
				</TD>
			</TR>
			
		
			<?php 
				$isSuperUser = isSuperUser(JFactory::getUser()->id);
				$showPaymentOption = false;
				if($isSuperUser && SHOW_PAYMENT_ADMIN_ONLY==1)
					$showPaymentOption = true;
				else if(SHOW_PAYMENT_ADMIN_ONLY==0)
					$showPaymentOption = true;
				
				$this->state->set("payment.payment_method","buckaroo");
				
			?>
			<?php if($this->appSettings->is_enable_payment){ ?>
			<tr style="display:<?php echo $showPaymentOption?"block":"none" ?>" >
				<td>
					<strong><?php echo JText::_("LNG_PAYMENT_METHODS");?></strong>
					<dl class="sp-methods" id="checkout-payment-method-load">
						<?php
							$oneMethod = count($this->paymentMethods) <= 1;
						    foreach ($this->paymentMethods as $method){
						?>
						    <dt>
						    <?php if(!$oneMethod){ ?>
						        <input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" title="<?php echo $method->name ?>" onclick="switchMethod('<?php echo $method->type ?>')"<?php if($this->state->get("payment.payment_method")==$method->type): ?> checked="checked"<?php endif; ?> class="radio" />
						    <?php }else{ ?>
						        <span class="no-display"><input id="p_method_<?php echo $method->type ?>" value="<?php echo $method->type ?>" type="radio" name="payment_method" class="radio"  onclick="switchMethod('<?php echo $method->type ?>')"/></span>
						        <?php $oneMethod = $method->type; ?>
						    <?php } ?>
							    <img class="payment-icon" src="<?php echo JURI::base() ."components/".getBookingExtName().'/assets/img/payment/'.strtolower($method->type).'.gif' ?>"  />
						        <label for="p_method_<?php echo $method->type ?>"><?php echo $method->name ?> </label>
						    </dt>
						   	 	<?php if ($html = $method->getPaymentProcessorHtml()){ ?>
							    <dd>
							        <?php echo $html; ?>
							    </dd>
							<?php } ?>
						<?php } ?>
					</dl>
				</td>
			</tr>
			<?php } ?>	
			<TR >
				<TD valign=top align=left>
					<BR>
					<div class='div_reservation_policies_title' style="display:none"><?php echo JText::_('LNG_RESERVATION_POLICIES');?></div>
					<div class='div_reservation_policies_info' style="display:none">
						<?php echo JText::_('LNG_RESERVATION_POLICIES_DETAILS');?>
					</div>
					<div>
						<input 
							type 		='checkbox'
							id			= 'is_accept_policies'
							name		= 'is_accept_policies'
						>&nbsp; 
						<a href="javascript:void(0);" onclick="showTerms()"><?php echo JText::_('LNG_AGREE_WITH_TERMS')?></a>	
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
							<?php echo JText::_('LNG_MAKE_RESERVATION');?>
							</button>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div> 
	
	
	<script>
	
		jQuery(document).ready(function(){
			jQuery(function(){
				jQuery("input.noSubmit").keypress(function(e){
			         var k=e.keyCode || e.which;
			         if(k==13){
			             e.preventDefault();
			         }
			     });
			 });
		});
		
		function checkContinue()
		{
			var is_ok	= false;
			var form 	= document.forms['userForm'];

			//jQuery('#userForm').validationEngine('attach');				
			//if(!jQuery('#userForm').validationEngine('validate'))
			//	return false;

			<?php if($this->appSettings->is_enable_payment ){?>
			if( !validateField( form.elements['payment_method'], 'radio', false, "<?php echo JText::_('LNG_PLEASE_SELECT_PAYMENT_PROCESSOR');?>" ) )
			{
				return false;
			}
			<?php } ?>
			
			if(!form.elements['is_accept_policies'].checked)
			{
				alert("<?php echo JText::_('LNG_MUST_ENABLE_ACCEPT_POLICY',true)?>"+'!');
				return false;
			}
			
			return true;
		}

		function applyDiscountCode(){
			jQuery("#task").val("paymentoptions.applyDiscount");
			jQuery("#userForm").submit();
		}
		
		
		
		function formBack() 
		{
			var form 	= document.forms['userForm'];
			form.task.value	="paymentoptions.back";
			form.submit();
		}

		function showTerms(){
			jQuery.fn.center = function () {
				this.css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
				this.css("top", ( jQuery(window).height() - this.height() ) /1.5 +jQuery(window).scrollTop() + "px");
				return this;
			}
			jQuery.blockUI({ message: jQuery('#conditions'), css: {width: '70%',top: '5%', position: 'absolute'} }); 
			jQuery('.blockUI.blockMsg').center();
			var x =jQuery('.blockUI.blockMsg').offset().top - 100; // 100 provides buffer in viewport
			$('html,body').animate({scrollTop: x}, 500);
			jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
		} 

		function switchMethod(method){
			jQuery("#checkout-payment-method-load ul").each(function(){
				jQuery(this).hide();
			});
			//console.debug(method);
			jQuery("#payment_form_"+method).show();
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
			<h3 class="title"> <?php echo JText::_('LNG_TERMS_AND_CONDITIONS');?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<?php echo JText::_('LNG_HOTEL_TERMS_AND_CONDITIONS');?>
			</div>
		</div>
	</div>
</div>



