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
?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=availability');?>" method="post" name="adminForm" id="adminForm">


	<div id="editcell">
		<div style='text-align:left'>
			<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
			
			 <select name="hotel_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
					<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
			</select>
			
			<hr>
		</div>
			
		<?php
			if( $this->state->get('filter.hotel_id') > 0  )	{
		?>
	
		<fieldset>
			<?php echo JText::_('LNG_UNAVAILABILITY',true); ?>:
			<span> <?php echo JText::_('LNG_UNAVAILABILITY_INFO',true); ?> </span>
			<?php 
				$ignored_dates = "";
				foreach($this->hotels as $hotel){
					if($hotel->hotel_id == $this->state->get('filter.hotel_id')){
						$ignored_dates = $hotel->ignored_dates;
					}
				}
			?>
			<input 
					type='hidden' 
					name='ignored_dates' 
					id='ignored_dates'
					value='<?php echo $ignored_dates;?>'
				>
			<div class="dates_hotel_calendar" id="dates_hotel_calendar"></div>
			
		</fieldset>
	
		<fieldset class="adminform">
			<table class="table table-striped adminlist"  id="itemList">
				<thead>
					<tr>
						<Th width='15%' align=center><B><?php echo JText::_('LNG_NAME',true)?> </B></Th>
						<Th width='5%' align=center><B><?php echo JText::_('LNG_CAPACITY',true)?></B></Th>
						<Th width='1%' align=center><B><?php echo JText::_('LNG_ID',true)?></B></Th>
					</tr>
				</thead>
				<tbody>

					<?php
					$nrcrt = 1;
					
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$room = $this->items[$i]; 
						if(!$room->is_available){
							continue;
						}
					?>
					<TR class="row<?php echo $i%2 ?>">
						<TD >
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=roomrateprices&layout=edit&onlyAvailability=true&rate_id='. $room->rate_id."&room_id=".$room->room_id)?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true)?>"
							>
								<B><?php echo $room->room_name?></B>
							</a>	
							
						</TD>
						<!--
						<TD align=center><?php /* echo ($room->room_datas!='0000-00-00'  ? $room->room_datas.' > ' : "&nbsp;").' '.($room->room_datae!='0000-00-00'  ? '< '.$room->room_datae : "&nbsp;")*/?></TD>
						-->
					
						<TD >
							<?php echo JText::_('LNG_ADULTS',true)?> :<?php echo $room->max_adults?> <br/>
							<?php echo JText::_('LNG_CHILDREN',true)?> :<?php echo $room->max_children?>
						</TD>
						<TD ><?php echo $room->room_id?></TD>
					</TR>
					<?php
					}
					?>
					</tbody>
				</TABLE>
		</fieldset>
		<?php
			}
		?>
		
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	
	<!-- input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" /-->
	<?php echo JHTML::_( 'form.token' ); ?> 
	
	
	<script language="javascript" type="text/javascript">

		Joomla.submitbutton = function(task) {
			if (task != 'items.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true,true);?>')) {
				Joomla.submitform(task);
			}
		}

		jQuery(document).ready(function()
		{
			var hotelId=jQuery('#hotel_id').val();
			var refreshScreen=jQuery('#refreshScreen').val();
			var nrHotels = jQuery('#hotel_id option').length;
			if(hotelId>0 && refreshScreen=="" && parseInt(nrHotels)==2){
				jQuery('#refreshScreen').val("true");
				jQuery("#hotel_id").trigger('change');	
			}
		});	

		jQuery('#dates_hotel_calendar').DatePicker(
				{
					flat: 		true,
					date: 			[  ],
					current: 		new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, 1, 0,0,0),
					format: 		'Y-m-d',
					calendars: 		5,
					mode: 			'multiple',
					position:		'right',
					className: 		'custom-picker',
					starts: 		0,
					onRender: function(date) {
												var d =  new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, <?php echo date('d')?>, 0,0,0);
												return {
													disabled: (date.valueOf() < d.valueOf()),
													className: date.valueOf() == d.valueOf() ? 'datepickerSpecial' : false
												}
											},
					onBeforeShow: function(){
												
												var crtVal = new Array();
												crtVal = (jQuery("#ignored_dates").val( )).split(',');
												jQuery('#dates_hotel_calendar').DatePickerClear();
												jQuery('#dates_hotel_calendar').DatePickerSetDate(crtVal);
											},
					onHide: function()
											{
												
												return true;
											},

					onChange: function(formated, dates){
														jQuery("#ignored_dates").val( formated.join(',') );
													}

				}
			);
		var crtVal = new Array();
		crtVal = (jQuery("#ignored_dates").val( )).split(',');
		jQuery('#dates_hotel_calendar').DatePickerClear();
		jQuery('#dates_hotel_calendar').DatePickerSetDate(crtVal);
		jQuery('#dates_hotel_calendar').DatePickerShow();
		
		function clickBtnIgnoreDays()
		{
			jQuery('#dates_hotel_calendar').DatePickerHide();
			jQuery('#hotel_availability_dates').append( jQuery('#div_calendar') );
			jQuery('#dates_hotel_calendar').DatePickerShow();
			this.className = 'span_ignored_days_sel';
		}

		
		</script>
</form>


