<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="mangeReservations" class="manage-reservation">
	<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="reservationForm" id="reservationForm"  class="form-validate">
		<fieldset class="adminform" >
					<div id="boxes">
						<div style="align:right">
							<span class="button button-green right">
								<button value="checkRates" name="checkRates" type="button" 
									onclick="jQuery('#task').val('customeraccount.back');document.forms['reservationForm'].submit();">
								<?php echo JText::_('LNG_BACK',true);?>
								</button>
							</span>
						</div>
						<?php if(count($this->rows)>0){?>
							<table class="reviewTable" width=100% style="padding:15px;">
								<thead>	
									<th><B><?php echo JText::_('LNG_RESERVATION_DETAILS',true)?></B></th>
									
								</thead>
								<tbody>
								<?php
								for($i = 0; $i <  count( $this->rows ); $i++)
								{
									$reservation = $this->rows[$i]; 
									$date_parts1=explode("-", $reservation->start_date);   
									$date_parts2=explode("-", date('Y-m-d'));   
//									$model = $this->getModel();
	//								$cancellationData = $model->getCancellationDetails($reservation->hotel_id);
									
									//gregoriantojd() Converts a Gregorian date to Julian Day Count   
									$start_date		=	gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);   
									$end_date		=	gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);   
									$day_dif 		= 	$start_date - $end_date;
									$canCancell 	= 	true;
									//if(($cancellationData->payment_days<$day_dif && $cancellationData->is_check_days==1 && $cancellationData->is_available==1) || $cancellationData->is_check_days!=1 || $cancellationData->is_available!=1)
										//$canCancell 	= 	true;
									
								?>
								<tr class="row<?php echo $i%2 ?>"
									onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	=	"this.style.cursor='default'"
								>
										<td class="reviewQuestion" align=left style="padding:10px;border-right:0px;">
											<label title="<?php echo JText::_('LNG_NAME',true);?>">
												<?php echo JText::_('LNG_NAME',true);?>:
											</label>										
											<B><?php echo $reservation->first_name.' '.$reservation->last_name?></B>
											
											<br/><br/>
											<label title="<?php echo JText::_('LNG_HOTEL',true);?>">
												<?php echo JText::_('LNG_HOTEL',true);?>:
												</label>
											<B><?php echo $reservation->hotel_name?></B>
											
											<br/><br/>
											<label title="<?php echo JText::_('LNG_PERIOD',true);?>">
												<?php echo JText::_('LNG_PERIOD',true);?>:
											</label>
											
											<B><?php echo date('d-M-Y', strtotime($reservation->start_date))?>
											to
											<?php echo date('d-M-Y', strtotime($reservation->end_date))?></B>
											<br/><br/>
											
											<label title="<?php echo JText::_('LNG_DESCRIPTION',true);?>">
												<?php echo JText::_('LNG_DESCRIPTION',true);?>:
											</label>
											
											<B><a
												<?php echo strtotime(date('Y-m-d')) >= strtotime($reservation->end_date) && $reservation->reservation_status == CHECKEDIN_ID ? "style='color:#FF0000'" : ""?>
												href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=customeraccount&view=customeraccount&task=reservationDetails&confirmation_id='. $reservation->confirmation_id )?>'
											>
											<?php echo JText::_('LNG_ADULT_S',true)?> : <?php echo $reservation->adults?>
											,
											<?php echo JText::_('LNG_CHILD_S',true)?> : <?php echo $reservation->children?>
											,
											<?php echo JText::_('LNG_ROOMS',true)?> : <?php echo $reservation->rooms?> 
											
											</a></B>
											<br/>	<br/>
											<label title="<?php echo JText::_('LNG_NAME',true);?>"><?php echo JText::_('LNG_STATUS',true);?>:</label>	
												<B><?php echo $reservation->status_reservation_name?> </B>
											<br/><br/>
											<!-- <label title="<?php echo JText::_('LNG_NAME',true);?>"><?php echo JText::_('LNG_PAYMENT',true);?>:</label>		
												<B><?php echo $reservation->payment_status?></B> -->	
										</td>	
										<td align="center" valign="middle" style="border-left:0px;">
											<?php if(($reservation->reservation_status==1 || $reservation->reservation_status==5) && $canCancell)
											{?>
												<a onclick='javascript:editReservation("<?php echo $reservation->confirmation_id; ?>")'>
													<?php echo JText::_('LNG_EDIT_RESERVATION',true)?>&nbsp;
													<img style='cursor:hand;cursor:pointer' width='12px' height='12px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/icon_edit.png"?>" >
												</a>
											<?php }	?>
											<br/><br/>

											<?php if(($reservation->reservation_status==1 || $reservation->reservation_status==5) && $canCancell)
												{?>
												<a onclick='cancelReservation("<?php echo $reservation->confirmation_id; ?>")'>		<?php echo JText::_('LNG_CANCEL_RESERVATION',true)?>&nbsp;
													<img style='cursor:hand;cursor:pointer' width='12px' height='12px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/icon_delete.png"?>" >
												</a>
											<?php }	?>		
										</td>			
									</tr>
									<?php 
									
										$tipConfirmation = 'cash';
										if($reservation->payment_processor!=null){
										?>
											<input type="hidden" name="payment_processor_sel_id" id="payment_processor_sel_id" value="<?php echo $reservation->payment_processor?>" />
										<?php 
										}
										?>
										<input type="hidden" name="payment_id" value="<?php echo $reservation->confirmation_payment_id;?>" />
								 	<?php 								
								    }	
								 	?>
									</tbody>
									</table>
								<?php 
								}
								else { 
									echo "Currently you don't have any reservations.";
									}
								?>
			<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
			<input type="hidden" name="task" id ="task" value="" />
			<input type="hidden" name="statusId" id ="statusId" value="" />
			<input type="hidden" name="reservationId" id="reservationId" value="" />
			<input type="hidden" name="controller" value="" />
		</fieldset>
	</form>
 </div>
<script type="text/javascript">
function editReservation(confirmationId){
	
		var form = document.getElementById('reservationForm');
		document.getElementById('reservationId').value=confirmationId;
		document.getElementById('task').value = "customeraccount.editreservation";
		form.submit();
}
function cancelReservation(confirmationId){
	if(confirm("Are you sure you want yo cancel the reservation")){
		var form = document.getElementById('reservationForm');
		document.getElementById('reservationId').value=confirmationId;
		document.getElementById('task').value = "customeraccount.cancelReservation";
		document.getElementById('statusId').value='<?php echo CANCELED_PAYMENT_ID;?>';
		form.submit();
	}
}
</script>