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

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=rooms');?>" method="post" name="adminForm" id="adminForm">

	<div id="editcell">
		<fieldset class="adminform">
			<div style='text-align:left'>
				<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
				
				 <select name="hotel_id" id="hotel_id" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
				</select>
				
				<hr>
			</div>
			<?php
			if( $this->state->get('filter.hotel_id') > 0  )
			{
			?>
			
			<table class="table table-striped adminlist"  id="itemList">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<Th width='15%' align=center><B><?php echo JText::_('LNG_NAME',true)?> </B></Th>
						<Th width='5%' align=center><B><?php echo JText::_('LNG_CAPACITY',true)?></B></Th>
						<Th width='5%' align=center><B><?php echo JText::_('LNG_DISPLAY_ON_FRONT',true)?></B></Th>
						<Th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true)?></B></Th>
						<Th width='1%' align=center><B><?php echo JText::_('LNG_ORDERING',true)?></B></Th>
						<Th width='1%' align=center><B><?php echo JText::_('LNG_ORDER',true)?></B></Th>
						<Th width='1%' align=center><B><?php echo JText::_('LNG_ID',true)?></B></Th>
					</tr>
				</thead>
				<tbody>

					<?php
					$nrcrt = 1;
					
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$room = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<td class="nowrap">
							<?php echo JHtml::_('grid.id', $i, $room->room_id); ?>
						</td>
						<TD >
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=room.edit&room_id='. $room->room_id)?>'
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
							<?php if($this->appSettings->show_children!=0){ ?>
								<?php echo JText::_('LNG_CHILDREN',true)?> :<?php echo $room->max_children?>
							<?php }?>
						</TD>
						<TD >
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($room->front_display==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	= "return listItemTask('cb<?php echo $i?>','rooms.changeFrontState')"
							/>	
						</TD>
						<TD >
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($room->is_available==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	= "return listItemTask('cb<?php echo $i?>','rooms.changeState')"
							/>
							
						</TD>
						
						
						<TD width='5%' valign=top align=center nowrap >
							<B>
								<span 
									name="span_up_<?php echo $room->room_id?>"
									id	="span_up_<?php echo $room->room_id?>"
									class= "span_up"
									onclick='
													jQuery.ajax({
														url		: "<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&task=rooms.room_order&tip_order=up&room_id=<?php echo $room->room_id?>&hotel_id=<?php echo $room->hotel_id?>",
														context	: document.body,
														success	: function( responce ){
																				var xml = responce;
																				// alert(xml);
																				jQuery(xml).find("answer").each(function()
																				{
																					if( jQuery(this).attr("error") == "0" )
																					{
																						window.location="<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&view=rooms&hotel_id=<?php echo $room->hotel_id?>"
																						var row = jQuery("#span_up_<?php echo $room->room_id?>").parents("tr:first"); 
																						row.insertBefore(row.prev());
																					}
																				});
																		}
													});
											'
								>
									<?php echo JText::_('LNG_UP',true)?>
								</span>
								&nbsp;
								<span 
									name="span_down_<?php echo $room->room_id?>"
									id	="span_down_<?php echo $room->room_id?>"
									class="span_down"
									onclick='
											
													jQuery.ajax({
														url		: "<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&task=rooms.room_order&tip_order=down&hotel_id=<?php echo $room->hotel_id?>&room_id=<?php echo $room->room_id?>",
														context	: document.body,
														success	: function(responce){
																				var xml = responce;
																				jQuery(xml).find("answer").each(function()
																				{
																					if( jQuery(this).attr("error") == "0" )
																					{
																						window.location="<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&view=rooms&hotel_id=<?php echo $room->hotel_id?>"
																					
																						var row = jQuery("#span_down_<?php echo $room->room_id?>").parents("tr:first"); 
																						row.insertAfter(row.next());
																					}
																				});
																				
																			}
													});
											'
								>
									<?php echo JText::_('LNG_DOWN',true)?>
								</span>
							</B>
						</TD>
						<TD ><?php echo $room->room_order?></TD>
						<TD ><?php echo $room->room_id?></TD>
					</TR>
					<?php
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="15">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
				</TABLE>
				<?php
				}
				?>
		</fieldset>
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
			if(refreshScreen=="" && parseInt(nrHotels)==2){
				jQuery('#hotel_id :nth-child(2)').prop('selected', true); 
				jQuery('#refreshScreen').val("true");
				jQuery("#hotel_id").trigger('change');	
			}
		});	
		</script>
</form>


