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
?>
<div class="reservationButtons">
	<span class="button button-green right">
		<button value="checkRates" name="checkRates" type="button"
			onclick="cancelEdit()">
		<?php echo JText::_('LNG_BACK',true);?>
		</button>
	</span>		

	<span class="button button-green right">
		<button value="checkRates" name="checkRates" type="button"
			onclick="saveClose()">
		<?php echo JText::_('LNG_SAVE_CLOSE',true);?>
		</button>
	</span>
	
	<span class="button button-green right">
		<button value="checkRates" name="checkRates" type="button"
			onclick="save()">
		<?php echo JText::_('LNG_SAVE',true);?>
		</button>
	</span>
</div>
<div style="width:100%;float:left;background-color: #FFF;padding-left:10px;">
	<?php include(JPATH_COMPONENT_ADMINISTRATOR.'/views/reservation/tmpl/edit.php');?>
</div>

<script type="text/javascript">	
function changeDates(){
	jQuery("#start_date").val(jQuery("#start_date_i").val());
	jQuery("#end_date").val(jQuery("#end_date_i").val());
	jQuery("#update_price_type").val(jQuery("#change-dates input[type='radio']:checked").val());
	Joomla.submitbutton('customeraccount.saveReservation');
}
function cancelEdit(){
	jQuery("input[name='task']").val("customeraccount.managereservations");
	jQuery("input[name='view']").val("customeraccount");
	jQuery("form[name='adminForm']").action ="index.php?option=<?php getBookingExtName();?>"
	jQuery("form[name='adminForm']").submit();
}

function save(){
	jQuery("input[name='task']").val("customeraccount.saveReservation");
	jQuery("form[name='adminForm']").submit();
}

function saveClose(){
	jQuery("input[name='task']").val("customeraccount.saveCloseReservation");
	jQuery("form[name='adminForm']").submit();
}
</script>
