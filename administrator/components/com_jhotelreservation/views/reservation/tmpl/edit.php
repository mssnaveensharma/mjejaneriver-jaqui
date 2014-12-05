<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		
		if (task == 'reservation.cancel' || validateForm())
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}

	function changeOptionState(id){
		jQuery("#"+id).toggle();
	}

	function upateExtraOption(){
	}
</script>

<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservation&layout=edit'); ?>" method="post"  class="form-horizontal">
	
	<?php if($this->state->get("reservation.id")==0){?>
		<div style='text-align:left'>
			<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
			
			 <select name="hotel_id" id ="hotel_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
					<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('reservation.hotel_id'));?>
			</select>
			
			<hr>
		</div>
	<?php } ?>
	<?php
	if( $this->state->get('reservation.hotel_id') > 0 || $this->state->get("reservation.id") )
	{
	?>
	
	<fieldset class="adminform reservation reservation-box">
		<legend><?php echo JText::_('LNG_RESERVATION_DETAILS',true); ?></legend>
		<TABLE class="admintable">
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARIVAL',true); ?> </td>
				<td>
					<?php 
						if(!$this->state->get("reservation.id")){
							$startDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->start_date);
							echo JHTML::_('calendar', $startDate==$this->appSettings->defaultDateValue?'': $startDate, 'start_date', 'start_date', $this->appSettings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); 
						}else{
							echo JHotelUtil::getDateGeneralFormat($this->item->reservationData->userData->start_date);
						?>
							<input type="hidden" name="start_date" id="start_date" value="<?php echo $this->item->reservationData->userData->start_date ?>" />
							<a href="javascript:showChangeDates()"><?php echo JText::_('LNG_CHANGE_DATES',true)?></a>
						<?php }?>
				</td>
			</tr>
					
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_DEPARTURE',true); ?> </td>
				<td>
					<?php 
						if(!$this->state->get("reservation.id")){
							$endDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->end_date);
							echo JHTML::_('calendar', $endDate==$this->appSettings->defaultDateValue?'': $endDate, 'end_date', 'end_date', $this->appSettings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); 
						}else{
							echo JHotelUtil::getDateGeneralFormat($this->item->reservationData->userData->end_date);
						?>
							<input type="hidden" name="end_date" id="end_date" value="<?php echo $this->item->reservationData->userData->end_date ?>" />
						<?php }?>
				</td>
			</tr>
			<tr>
				<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARRIVAL_TIME',true); ?> </td>
				<td>
					<select name="arrival_time">
						<?php for($i=0;$i<24;$i++) {
							$j= $i.":00";	
							?>
							<option value="<?php echo $j?>" <?php echo strcmp($j, $this->item->reservationData->userData->arrival_time)==0?'selected="selected"':''?>><?php echo $j?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_REMARKS',true); ?></td>
				<td><textarea name='remarks' id='remarks' rows="3" cols="25" ><?php echo $this->item->reservationData->userData->remarks?></textarea></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_AMDIN_REMARKS',true); ?></td>
				<td><textarea name='remarks_admin' id='remarks_admin' rows="3" cols="25" ><?php echo $this->item->reservationData->userData->remarks_admin?></textarea></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_VOUCHER',true); ?></td>
				<td><input type="text" name="voucher" id="voucher" size="50" value="<?php echo $this->item->reservationData->userData->voucher ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_DISCOUNT_CODE',true); ?></td>
				<td><input type="text" name="discount_code" id="discount_code" size="50" value="<?php echo $this->item->reservationData->userData->discount_code ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_ID',true); ?></td>
				<td><?php echo $this->item->reservationData->userData->confirmation_id ?></td>
				<TD>&nbsp;</TD>
			</tr>	
		</TABLE>
	</fieldset>
	<fieldset class="adminform reservation reservation-box">
		<legend><?php echo JText::_('LNG_EDIT_GUEST_DETAILS',true); ?></legend>
		<TABLE class="admintable">
			<tr>
				<td class="key">
					<?php echo JText::_('LNG_GENDER_TYPE',true);?> <span class="mand">*</span>
				</td>
				<td class="gender-type">
					<?php 
						echo JHtml::_( 'select.radiolist', $this->guestTypes, 'guest_type', '', 'value', 'text',  $this->item->reservationData->userData->guest_type,'guest_type'); 
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_FIRST_NAME',true); ?></td>
				<td><input type="text" name="first_name" id="first_name" size="50" value="<?php echo $this->item->reservationData->userData->first_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_LAST_NAME',true); ?></td>
				<td><input type="text" name="last_name" id="last_name" size="50" value="<?php echo $this->item->reservationData->userData->last_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_BILLING_ADDRESS',true); ?></td>
				<td><input type="text" name="address" id="address" size="50" value="<?php echo $this->item->reservationData->userData->address ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_POSTAL_CODE',true); ?></td>
				<td><input type="text" name="postal_code" id="postal_code" size="50" value="<?php echo $this->item->reservationData->userData->postal_code ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_CITY',true); ?></td>
				<td><input type="text" name="city" id="city" size="50" value="<?php echo $this->item->reservationData->userData->city ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_STATE',true); ?></td>
				<td><input type="text" name="state_name" id="state_name" size="50" value="<?php echo $this->item->reservationData->userData->state_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_COUNTRY',true); ?></td>
				<td><input type="text" name="country" id="country" size="50" value="<?php echo $this->item->reservationData->userData->country ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_TELEPHONE_NUMBER',true); ?></td>
				<td><input type="text" name="phone" id="phone" size="50" value="<?php echo $this->item->reservationData->userData->phone ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_EMAIL',true); ?></td>
				<td><input type="text" name="email" id="email" size="50" value="<?php echo $this->item->reservationData->userData->email ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('LNG_COMPANY_NAME',true); ?></td>
				<td><input type="text" name="company_name" id="company_name" size="50" value="<?php echo $this->item->reservationData->userData->company_name ?>"></td>
				<TD>&nbsp;</TD>
			</tr>
		</TABLE>
		
		<table>
			<?php if(isset($this->item->reservationData->userData->guestDetails)){ ?>
				<?php foreach($this->item->reservationData->userData->guestDetails as $guestDetail){?>
					<tr>
						<TD  align=left>
						 <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME',true);?></label> <span class="mand">*</span><br/>
							<input class="req-field" 
								type 			= 'text'
								name			= 'guest_first_name[]'
								id				= 'guest_first_name'
								size			= 25
								value			= "<?php echo $guestDetail->first_name?>">
						</TD>	
						<td>
							<label for="guest_last_name"><?php echo JText::_('LNG_LAST_NAME',true);?></label> <span class="mand">*</span><br/>
							<input  class="req-field"
								type 			= 'text'
								name			= 'guest_last_name[]'
								id				= 'guest_last_name'
								size			= 25
								value			= "<?php echo $guestDetail->last_name?>">
						</td>
					
						<td><label for="guest_identification_number"><?php echo JText::_('LNG_PASSPORT_NATIONAL_ID',true);?></label><BR/>
							<input class=""
								type 			= 'text'
								name			= 'guest_identification_number[]'
								id				= 'guest_identification_number'
								size			= 25
								value			= "<?php echo $guestDetail->identification_number?>">
						</td>
					</tr>
				<?php }?>
			<?php } ?>
		</table>
	</fieldset>
	<fieldset class="adminform reservation left" id="reservation-rooms">
		<legend><?php echo JText::_('LNG_RESERVATION_ROOMS',true); ?></legend>
		
		<div class="persons">
			<?php echo JText::_('LNG_ADULTS',true)?>: 
			<select name="adults" id="adults">
				<?php for($i=0; $i<=4;$i++){?>
					<option	value="<?php echo $i?>" <?php echo $i==2 ?'selected="selected"':''?>><?php echo $i?></option>
				<?php } ?>
			</select>
			<div style="">
			<?php if($this->appSettings->show_children){?>
			<?php echo JText::_('LNG_JUNIORS',true)?>: 
			 <select name="children" id="children">
			 	<?php for($i=0; $i<=4;$i++){?>
					<option	value="<?php echo $i?>" <?php echo $i==0 ?'selected="selected"':''?> ><?php echo $i?></option>
				<?php } ?>
			</select> 
			<?php } ?>
			</div>
		</div>
		<table>
			<tr>
		        <td colspan="4" style="text-align: left;">
              	  <?php echo JText::_('LNG_ROOMS',true)?>
                    <select name="rooms" id="rooms">
                   		 <?php echo JHtml::_('select.options', $this->roomTypes, 'value', 'text', 0);?>
					</select>
			        <span id="ddlRates">&nbsp;
						<select name="rateid" id="rateid" style="display:none">
							<option value="0">Room only</option><option value="8044">Contract rate</option>
						</select>
						&nbsp;
						<button id="btnAddRoom" onclick="addRoom(); return false"><?php echo JText::_('LNG_ADDROOM',true)?></button>
					</span>
                </td>
            </tr>
        </table>
        
		<?php
		$isCustomPrice = false; 
		if(isset($this->item->rooms)){ 
			$idx = 0; 
			foreach ($this->item->rooms as $room){
		?>
		<fieldset class="roomrate" id="<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>">
			<legend>
				<?php echo (isset( $room->offer_name)?$room->offer_name." - ":"") ?> <?php echo $room->room_name ?>  &nbsp; <span
					onclick="removeRoom('<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>')" class="removeroom">[ <?php echo JText::_('LNG_DELETE',true)?> ]</span>
			</legend>
			<div>
				<input type="hidden" name="reservedItem[]" value="<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>" />
				<div class="persons">
					<?php echo JText::_('LNG_ADULTS',true)?>: 
					<select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][adults]" id="room[<?php echo $room->room_id?>][adults]">
						<?php for($i=1; $i<=$room->max_adults;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->adults ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					<div style="display:none">
						
					 <?php echo JText::_('LNG_JUNIORS',true)?>: 
					 <select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][children]">
					 	<?php for($i=1; $i<=$room->max_children;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->children ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					</div> 
				</div>
				<div class="nights">
					<ul>
						<?php 
						$startDate = $this->item->reservationData->userData->start_date;
						$endDate = $this->item->reservationData->userData->end_date;
						for( $d = strtotime($startDate);$d < strtotime($endDate); ){
							$dayString = date( 'Y-m-d', $d);
							$price = $room->daily[$dayString]["price_final"];
							if(isset($room->customPrices) && isset($room->customPrices[$dayString])){
								$isCustomPrice = true;
								$price = $room->customPrices[$dayString];
							}
						?>
						<li>
							<?php echo $dayString?>: <input type="text"	name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][price][<?php echo $dayString?>]" id="room_price_<?php echo $room->id?>_<?php echo $dayString?>" onBlur="setCustomPrice()" value="<?php echo $price?>">
							( <?php echo number_format($room->daily[$dayString]["price_final"],2) ?> )
						</li>
						<?php 
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						} 
						?>
					</ul>
				</div>
				
				<div class="extra-options"> 
					<fieldset class="adminform" >
						<legend><?php echo JText::_('LNG_EXTRA_OPTIONS',true)?></legend>
						<div>
							<div id="extra-options-container">
								<div>
								<?php
									$is_checked = isset($this->item->reservationData->extraOptions[0]->checked);
									$activated = false;
									foreach( $this->item->reservationData->extraOptions as $item ){
										if($is_checked != isset($item->checked) && !$activated){
											$activated = true;
											?>
												</div>
												<a href="javascript:void(0)" onclick="jQuery('#more-extra-options').slideDown(100)"> <?php echo JText::_("LNG_MORE")?></a>
												<div id="more-extra-options" style="display:none">
												
											<?php 
										}
										
										//dmp($this->_models['variables']->items_reserved);
										
										$extraOptionKey 	= $this->item->reservationData->userData->reservedItems[$idx].'|'.$item->id;
									?>
										<div class="extra-option">
											<div  class='extra-option-image'>
											<?php if(isset($item->image_path) && strlen($item->image_path)>0){ 
												echo "<img src='".JURI::root() .PATH_PICTURES.EXTRA_OPTISON_PICTURE_PATH.$item->image_path."'/>";;
											}else{
												echo "<img src='".JURI::root() ."components/com_jhotelreservation/img/no_image.jpg'/>";
											}
											?>
											</div>
											
												<input 
														type	='checkbox'
														name	='extraOptionIds[]'
														id		='extraOptionIds<?php echo $item->id ?>'
														value	= '<?php echo $extraOptionKey?>|1|0|0'
														class="extra-checkbox"
														onchange="changeOptionState('options-<?php echo $item->id ?>');upateExtraOption()"
														<?php echo isset($item->checked) ? " checked " : ""?>
														<?php echo $item->mandatory == 1 ? ' onclick="return false" onkeydown="return false" checked="checked" ' : ""?>
													>
											<div class="extra-option-box"> 	
												<strong><?php echo $item->name ?></strong>,	<?php echo $this->item->reservationData->userData->currency->symbol?> <?php echo JHotelUtil::fmt($item->price,2) ?><?php echo $item->price_type == 1?",&nbsp;".strtolower(JText::_('LNG_PER_PERSON',true)):"" ?><?php  echo $item->is_per_day == 1 ?",&nbsp;".JText::_('LNG_PER_DAY',true):"" ?><?php  echo $item->is_per_day == 2 ?",&nbsp;".JText::_('LNG_PER_NIGHT',true):"" ?>
												<p><i><?php echo $item->description ?></i></p>
												
												<div class="extras-options" id="options-<?php echo $item->id?>" style="display:<?php echo isset($item->checked)?"block":"none"?>">
													<?php if($item->price_type == 1){?>
														<?php echo JText::_('LNG_NUMBER_OF_PERSONS',true)?>
														<select id="persons-<?php echo $item->id?>" name="extra-option-persons-<?php echo $item->id?>">
															<?php for($i=1;$i<21;$i++){ ?>
																<option value="<?php echo $i ?>" <?php echo isset ($item->persons) && $i== $item->persons? 'selected="selected"':''?>><?php echo $i ?></option>
															<?php } ?>
														</select>
													<?php } ?>
													<br/>
													<?php if($item->is_per_day == 1 || $item->is_per_day == 2){?>
														<?php echo $item->is_per_day ==1 ? JText::_('LNG_NUMBER_OF_DAYS',true) : JText::_('LNG_NUMBER_OF_NIGHTS',true)?>
														<?php 
																$nrDays = JHotelUtil::getNumberOfDays($this->item->reservationData->userData->start_date, $this->item->reservationData->userData->end_date);
														?>
														<select id="days-<?php echo $item->id?>" name="extra-option-days-<?php echo $item->id?>"  <?php echo $item->map_per_length_of_stay==1?'onfocus="this.oldvalue=this.value;this.blur();" onchange="this.value=this.oldvalue;"':''?> >
															<?php for($i=1;$i<21;$i++){ ?>
																<option value="<?php echo $i ?>" <?php echo ($item->map_per_length_of_stay ==1 && $i== $nrDays) || ( isset($item->days) && $i==$item->days) ? 'selected="selected"':''; ?> ><?php ?><?php echo $i ?></option>
															<?php } ?>
														</select>
													<?php } ?>
													
												</div>
											</div>
											<div class="clear"> </div>
										</div>
									<?php
									}
									?>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</fieldset>
		<?php 
				}
				$idx++;
			}
		?>

	</fieldset>
	<?php if (isset($this->item->paymentInformation)){?>
		<fieldset class="adminform reservation right" id="paymentDetails">
			<legend>
					<?php echo JText::_('LNG_PAYMENT_DETAILS',true)?>
			</legend>
			<div>
				<?php echo $this->item->paymentInformation;?>
			</div>
		</fieldset>
	<?php }?>

	
	<input type="hidden" name="hotelId" id="hotelId" value="<?php echo $this->state->get('reservation.hotel_id'); ?>" />
	<input type="hidden" name="reservationId" value="<?php echo $this->item->reservationData->userData->confirmation_id ?>" />
	<input type="hidden" name="totalPaid" value="<?php echo $this->item->reservationData->userData->totalPaid  ?>" />
	<input type="hidden" name="update_price_type" id="update_price_type" value="<?php echo $isCustomPrice==true?"2":"";?>" />
	<input type="hidden" name="current" id="current" value="<?php echo isset($this->item->rooms)? count($this->item->rooms) +1 : 1 ?>" />
	
	
	<?php } ?>
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName() ?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	
</form>

<div id="change-dates" class="change-dates" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
			<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title"> <?php echo JText::_('LNG_CHANGE_DATES',true);?></h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<table>
					<tr>
						<td width=10% nowrap class="key"><?php echo JText::_('LNG_ARIVAL',true); ?> </td>
						<td>
							<?php $startDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->start_date)?>
							<?php echo JHTML::_('calendar', $startDate==$this->appSettings->defaultDateValue?'': $startDate, 'start_date', 'start_date_i', $this->appSettings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
						</td>
					</tr>
					<tr>
						<td width=10% nowrap class="key"><?php echo JText::_('LNG_DEPARTURE',true); ?> </td>
						<td>
							<?php $endDate = JHotelUtil::convertToFormat($this->item->reservationData->userData->end_date)?>
							<?php echo JHTML::_('calendar', $endDate==$this->appSettings->defaultDateValue?'': $endDate, 'end_date', 'end_date_i', $this->appSettings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
						</td>
					</tr>
					<tr>
					<tr>
						<td colspan="2">
							<?php echo JText::_('LNG_PRICE_CHOOSE',true) ?>
						</td>
					</tr>
					<tr>
						<td>
							<input id="price_type1" type="radio" value="1" name="price_type">
							<label id="price_type1-lbl" class="radiobtn" for="price_type1"><?php echo JText::_('LNG_RETRIEVE_DAY_PRICES',true) ?></label>
							<input id="price_type2" type="radio" value="2" name="price_type">
							<label id="price_type2-lbl" class="radiobtn" for="price_type2"><?php echo JText::_('LNG_APPLY_CURRENT_PRICES',true) ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<button id="btnChangeDates" onclick="changeDates(); return false"><?php echo JText::_('LNG_CHANGE_DATES',true);?></button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<script language="javascript" type="text/javascript">

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

