<?php defined('_JEXEC') or die('Restricted access'); 
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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$appSetings = JHotelUtil::getApplicationSettings();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task != 'reservations.delete' || confirm('<?php echo JText::_('COM_JHOTELRESERVATION_RESERVATIONS_CONFIRM_DELETE', true,true);?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservations');?>" method="post" name="adminForm" id="adminForm">
<div id="boxes">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL',true); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC',true); ?>" />
			
			<label class="filter-search-lbl" for="filter_voucher"><?php echo JText::_('LNG_VOUCHER',true); ?>:</label>
			<input type="text" name="filter_voucher" id="filter_voucher" value="<?php echo $this->escape($this->state->get('filter.voucher')); ?>" title="<?php echo JText::_('COM_JHOTELRESRVATION_FILTER_VOUCHER_DESC',true); ?>" />
			
			<label class="filter-search-lbl" for="filter_start_date"><?php echo JText::_('LNG_FROM',true); ?></label>
			<?php echo JHTML::_('calendar', $this->state->get('filter.start_date'), 'filter_start_date', 'filter_start_date', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
			
			<label class="filter-search-lbl" for="filter_end_date"><?php echo JText::_('LNG_TO',true); ?></label>
			<?php echo JHTML::_('calendar', $this->state->get('filter.end_date'), 'filter_end_date', 'filter_end_date', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
			
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT',true); ?></button>
			<button type="button" onclick="clearSearch();this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR',true); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_hotel_id" class="inputbox" onchange="jQuery('#filter_room_type').attr('selectedIndex',0);this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_HOTEL',true)?></option>
				<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
			</select>

			<select id="filter_room_type" name="filter_room_type" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ROOM_TYPES',true);?></option>
				<?php echo JHtml::_('select.options', $this->roomTypes, 'value', 'text', $this->state->get('filter.room_type'));?>
			</select>
	
			<select name="filter_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_STATUS',true);?></option>
				<?php echo JHtml::_('select.options', $this->reservationStatuses, 'value', 'text', $this->state->get('filter.status'));?>
			</select>
			
			<select name="filter_payment_status" class="inputbox" onchange="this.form.submit()">
				<option value="-1"><?php echo JText::_('JOPTION_SELECT_PAYMENT_STATUS',true);?></option>
				<?php echo JHtml::_('select.options', $this->paymentStatuses, 'value', 'text', $this->state->get('filter.payment_status'));?>
			</select>
			
		</div>
	</fieldset>
	<div class="clr"> </div>
	
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%"></th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort', 'LNG_ID', 'c.confirmation_id', $listDirn, $listOrder); ?>
				</th>
				<th width="6%">
					<?php echo JHtml::_('grid.sort', 'LNG_GUEST_NAME', 'c.first_name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'LNG_HOTEL', 'h.hotel_name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'LNG_VOUCHER', 'c.voucher', $listDirn, $listOrder); ?>
				</th>
				<th width="4%">
					<?php echo JHtml::_('grid.sort', 'LNG_CHECK_IN', 'c.start_date', $listDirn, $listOrder); ?>
				</th>
				<th width="4%">
					<?php echo JHtml::_('grid.sort', 'LNG_CHECK_OUT', 'c.end_date', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'LNG_CREATED', 'c.created', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JText::_('LNG_DESCRIPTION',true) ?>
				</th>
				<th width="4%">
					<?php echo JText::_('LNG_STATUS',true) ?>
				</th>
				<th width="4%">
					<?php echo JText::_('LNG_PAYMENT',true) ?>
				</th>
				<th width="1%">
					<?php echo JText::_('LNG_ACTIONS',true) ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) {?>
			
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $i+1; ?></td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->confirmation_id); ?>
				</td>
				<td class="center">
					<a
						href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&task=reservation.edit&reservationId='. $item->confirmation_id )?>'
						title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"> 
						<?php echo JHotelUtil::getStringIDConfirmation($item->confirmation_id);?>
					</a>	
					
				</td>
				<td class="center"  width="1%">
					<?php echo $item->first_name.' '.$item->last_name?>
				</td>
				<td class="center" width="1%">
					<?php echo stripslashes($item->hotel_name)?>
				</td>
				<td class="center">
					<?php echo stripslashes($item->voucher)?>
				</td>
				<td >
					<?php echo JHotelUtil::getDateGeneralFormat($item->start_date)?>
				</td>
				<td >
					<?php echo JHotelUtil::getDateGeneralFormat($item->end_date)?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2',true)); ?>
				</td>
				<td class="center">
					<?php echo JText::_('LNG_ADULTS',true)?>: <?php echo $item->total_adults?>
					 &nbsp;&nbsp;&nbsp;
					 <?php if($appSetings->show_children){?>
						<?php echo JText::_('LNG_CHILDREN',true)?>: <?php echo $item->total_children?>
						&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php echo JText::_('LNG_ROOMS',true)?>: <?php echo $item->rooms?> 
				</td>
				<td class="center">
					<div class="reservation-status-<?php echo $item->reservation_status?> reservation-status">
						<select name="reservation_status" class="inputbox" onchange="changeStatus(this.value,<?php echo $item->confirmation_id ?>)">
							<?php echo JHtml::_('select.options', $this->reservationStatuses, 'value', 'text', $item->reservation_status);?>
						</select>
					</div>
				</td>
				<td class="center">
					<div class="payment-status-<?php echo $item->payment_status?> payment-status <?php echo $item->amount_paid == $item->total && ($item->payment_status == PAYMENT_STATUS_PAID || $item->payment_status == PAYMENT_STATUS_WAITING )? "full-payment":"" ?>">
						<select name="reservation_status" class="inputbox" onchange="changePaymentStatus(this.value,<?php echo $item->confirmation_id ?>)">
							<?php echo JHtml::_('select.options', $this->paymentStatuses, 'value', 'text', $item->payment_status);?>
						</select>
					</div>
				</td>
				<td nowrap="nowrap" width="6%">
					<a class="quick-action" href="javascript:showConfirmation(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_PREVIEW',true); ?>">
						<img   src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/preview.png" ?>' />
					</a>
					&nbsp;
					<a class="quick-action" href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&task=reservation.edit&reservationId='. $item->confirmation_id )?>'
						title="<?php echo JText::_('LNG_EDIT',true); ?>"> 
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/edit.png" ?>' />
					</a>
					&nbsp;
					<a class="quick-action" href="javascript:showConfirmation(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_PRINT',true); ?>">
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/print.png" ?>' />
					</a>
					&nbsp;
					<a  class="quick-action" href="javascript:sendEmail(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_SEND_EMAIL',true); ?>">
						<img src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/email.png" ?>' />
					</a>
					&nbsp;
					<?php if($item->reservation_status==CANCELED_ID){?>
					<a class="quick-action" href="javascript:void;" title="<?php echo $item->cancellation_notes?$item->cancellation_notes:"n/a"; ?>"> 
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/cancel_notes.png" ?>' />
					</a>
					<?php }?>
				</td>
			</tr>
			<?php 
			}?>
		</tbody>	
	</table>
	</div>

	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" id="reservationId" name="reservationId" value="" />
	<input type="hidden" id="statusId" name="statusId" value="" />
	<input type="hidden" id="paymentStatusId" name="paymentStatusId" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>

<div id="reservation-view"  style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			
			<iframe id="confirmationIfr" height="700" width="800" src="">
			
			</iframe>
		</div>
	</div>
</div>

<script>
// starting the script on page load
	jQuery(document).ready(function(){
	});		
	
	function showConfirmation(reservationId){
		var baseUrl = "<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservation&tmpl=component&layout=single',false,-1); ?>";
		baseUrl = baseUrl + "&reservationId="+reservationId;
		jQuery("#confirmationIfr").attr("src",baseUrl);
		jQuery.blockUI({ message: jQuery('#reservation-view'), css: {width: '810px', top: '5%'} });
		jQuery('.blockOverlay').click(jQuery.unblockUI); 
	}

	function sendEmail(reservationId){
		if(confirm('<?php echo JText::_('COM_JHOTELRESERVATION_SEND_EMAIL', true,true);?>')){
			jQuery("#reservationId").val(reservationId);
			jQuery("#task").val("reservations.sendEmail");
			jQuery("#adminForm").submit();
		}
	}

	function clearSearch(){
		document.id('filter_search').value='';
		document.id('filter_voucher').value='';
		document.id('filter_start_date').value='';
		document.id('filter_end_date').value='';
		jQuery("#task").val("");
	}
	
	function changeStatus(status, reservationId){
		jQuery("#statusId").val(status);
		jQuery("#reservationId").val(reservationId);
		jQuery("#task").val("reservations.changeStatus");
		jQuery("#adminForm").submit();
	}

	function changePaymentStatus(status, reservationId){
		jQuery("#paymentStatusId").val(status);
		jQuery("#reservationId").val(reservationId);
		jQuery("#task").val("reservations.changePaymentStatus");
		jQuery("#adminForm").submit();
	}
</script>

