<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

?>
<input type="hidden" name="informationId" value="<?php echo $this->item->informations->id ?>" />
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'LNG_IMPORTANT_INFORMATION' ,true); ?></legend>
		<table class="admintable">
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_CHECK_IN',true); ?>:</TD>
				<TD nowrap align=left>
					<select name="check_in">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_in)==0?'selected="selected"':''?>><?php echo $j?></option>
							<?php $j= $i.":30";	?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_in)==0?'selected="selected"':''?>><?php echo $j?></option>
						<?php } ?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_CHECK_OUT',true); ?>:</TD>
				<TD nowrap align=left>
					<select name="check_out">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_out)==0?'selected="selected"':''?>><?php echo $j?></option>
							<?php $j= $i.":30";	?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->informations->check_out)==0?'selected="selected"':''?>><?php echo $j?></option>
						<?php } ?>
					</select>
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_PARKING',true); ?>:</TD>
				<TD nowrap="nowrap" align=left>
					<?php echo $this->elements->parking; ?>
					<div style="display:inline; padding-left:20px">
						<?php echo JText::_('LNG_PRICE',true); ?> <input type="input" value="<?php echo $this->item->informations->price_parking?>"  name="price_parking" size="7"/>
						 &nbsp;
						 <?php echo JText::_('LNG_PERIOD',true); ?> &nbsp;
						<input type="input" value="<?php echo $this->item->informations->parking_period?>"  name=parking_period />
					</div>
				</TD>
				<td>


				</td>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_PETS',true); ?>:</TD>
				<TD nowrap align=left>
					<?php echo $this->elements->allowPets; ?>
					<div style="display:inline;padding-left:20px">
						<?php echo JText::_('LNG_PRICE',true); ?> <input type="input" value="<?php echo $this->item->informations->price_pets?>" name="price_pets" size="7"/> <input type="input" value="<?php echo $this->item->informations->pet_info?>" name="pet_info" size="35"/>
					</div>
							 
				</TD>
			</TR>
			
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_CITY_TAX',true); ?>:</TD>
				<TD nowrap align=left>
					<input type="text" class="validate[required,custom[number]] text-input" id="city_tax" name="city_tax" size="10" value="<?php echo $this->item->informations->city_tax ?>">
						<input  type="checkbox" name="city_tax_percent" value="1" <?php echo $this->item->informations->city_tax_percent == 1?'checked':'' ?>>(%)
				</TD>
				
				
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_NUMBER_OF_ROOMS',true); ?>:</TD>
				<TD nowrap align=left>
					<input type="text" name="number_of_rooms" id="number_of_rooms" class="validate[required,custom[integer],min[1]] input-text" size="10" value="<?php echo $this->item->informations->number_of_rooms ?>">
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_CANCELATION_DAYS',true); ?>:</TD>
				<td>
					<select id="cancellation_days" name="cancellation_days">
						<?php for($i=1;$i<100;$i++) {
								?>
							<option value="<?php echo $i?>" <?php echo $i==$this->item->informations->cancellation_days ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					<input type="checkbox" value="1" name="uvh_agree" id="uvh_agree" <?php echo $this->item->informations->uvh_agree == 1?'checked':''?>> <label for="uvh_agree"><?php echo JText::_('LNG_AGREE_WITH_UVH'); ?></label>
					<br/>
					<textarea class="inputbox" name="cancellation_conditions" rows=6 cols=250><?php echo $this->item->informations->cancellation_conditions ?></textarea>
				</td>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_INTERNET_WIFI',true); ?>:</TD>
				<TD nowrap align=left>
					<?php echo $this->elements->wifi; ?>
					<div style="display:inline;padding-left:20px">
						<?php echo JText::_('LNG_PRICE',true); ?> &nbsp;&nbsp;&nbsp;&nbsp;
						<input type="input" value="<?php echo $this->item->informations->price_wifi?>" name="price_wifi" size="5" />
						 &nbsp;
						<?php echo JText::_('LNG_PERIOD',true); ?> 
						 &nbsp;
						<input type="input" value="<?php echo $this->item->informations->wifi_period?>" name="wifi_period" size="25" />
					</div>
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_SUITABLE_FOR_DISABLED',true); ?>:</TD>
				<TD nowrap align=left>
					<?php echo $this->elements->suitableDisabled; ?>
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_PUBLIC_TRANSPORTATION',true); ?>:</TD>
				<TD nowrap align=left>
					<?php echo $this->elements->publicTransport; ?>
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_HOTEL_PAYMENT_OPTIONS',true); ?>:</TD>
				<TD nowrap align=left>
					<div id="paymentOption-holder" class="option-holder">
						<?php
							echo $this->paymentoptions->displayPaymentOptions( $this->item->paymentOptions, $this->item->selectedPaymentOptions );
						?>
					</div>
				<?php 
					if (checkUserAccess(JFactory::getUser()->id,"manage_options")){
				?>			
					<div class="manage-option-holder">
						<a href="javascript:" onclick="showManagePaymentOptions()"><?php  echo isset($this->item->hotel_id) ? JText::_('LNG_MANAGE_PAYMENT_OPTIONS',true):"" ?></a>
					</div>		
				<?php 
					}
				?>			
				</TD>
			</TR>
			<TR>
				<TD nowrap class="key"><?php echo JText::_('LNG_CHILDREN_AGE_CATEGORY',true); ?>:</TD>
				<TD nowrap align=left>
					<textarea name="children_category" id="children_category"  class="validate[required]" rows=6 cols=50><?php echo $this->item->informations->children_category ?></textarea>
				</TD>
			</TR>
		</table>
	</fieldset>

<div id="showPaymentOptionsNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_PAYMENT_OPTIONS',true); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup();"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_paymentOption" class="text_error" style="display: none;"></span> 
						<div class="row" id="paymentOption-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewPaymentOption(0,'')"><?php echo JText::_('LNG_ADD_NEW_PAYMENT_OPTION',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="savePaymentOptions(this.form);" type="submit" class="submit">    
                                     <span><span>Save</span></span>
                              </button>
                              <input value="Cancel" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup();" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmPaymentOptionsFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;">Please wait...</span> </div>
            </div>
          </div>
          </div>
     </div>        
     
     <script>
	     jQuery("select#paymentOptions").selectList({ 
			 sort: true,
			 classPrefix: 'paymentOptions',
			 onAdd: function (select, value, text) {
				    if(value=='new'){
					    return true;
				    }
			 }
	
		});
     </script>