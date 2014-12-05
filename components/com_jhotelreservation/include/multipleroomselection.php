<?php 
//error_reporting(E_ALL);
//ini_set('display_errors','On');
$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

?>

<div id="advanced-search" style="display:none">
		
			<span  title="Cancel"  class="dialogCloseButton" onClick="revertValue();jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
	 	
	 		<div class="mod_hotel_reservation" id="mod_hotel_reservation">
				<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userModuleAdvancedForm" id="userModuleAdvancedForm" >
					<input id="controller3" type='hidden' name='controller' value='hotels'/>
					<input id="task3" type='hidden' name='task' value='searchHotels'/>
					<input type="hidden" name="hotel_id" id="hotel_id3" value="" />
					<input type='hidden' name='year_start' value=''/>
					<input type='hidden' name='month_start' value=''/>
					<input type='hidden' name='day_start' value=''/>
					<input type='hidden' name='year_end' value=''/>
					<input type='hidden' name='month_end' value=''/>
					<input type='hidden' name='day_end' value=''/>
					<input type='hidden' name='rooms' value='' />
					<input type='hidden' name='guest_adult' value=''/>
					<input type='hidden' name='guest_child' value=''/>
					<input type='hidden' name='filterParams' id="filterParams" value='<?php echo isset($userData->filterParams) ? $userData->filterParams :''?>' />
					<input type='hidden' name='resetSearch' value='true'>
					<div id="booking-details" style="display:none;">
						<table>
							<tr class="tr_title_hotelreservation">
							</tr>
							<?php if(ENABLE_SINGLE_HOTEL!=1){?>
								<tr class="divider" id="search-box">
									<td colspan="5">
										<div class="search-nav">
											<strong>
												<?php echo JText::_('LNG_FIND_HOTEL',true);?>
											</strong>
											
											<br/>
											<input  class="keyObserver" type="text" value="<?php echo $userData->keyword ?>" name="keyword" id="keyword" />
											<br/>
										</div>
									</td>
								</tr>
							<?php }?>
							<tr>
								<td class="td_title_hotelreservation"><?php echo JText::_('LNG_ARIVAL',true)?></td>
								<td colspan="4" nowrap="nowrap" >
									<?php
									$jhotelreservation_datas = isset($jhotelreservation_datas)?$jhotelreservation_datas:$startDate;
									
										echo JHTML::calendar(
																$jhotelreservation_datas,'jhotelreservation_datas','jhotelreservation_datas3',$appSettings->calendarFormat, 
																array(
																		'class'		=>'date_hotelreservation keyObserver inner-shadow', 
																		'onchange'	=>
																					"
																					checkStartDate(this.value,defaultStartDate,defaultEndDate);
																					setDepartureDate('jhotelreservation_datae3',this.value);
																		"
																	)
															);
		
									?>
								</td>
							</tr>
							<tr>
								<td class="td_title_hotelreservation" ><?php echo JText::_('LNG_DEPARTURE',true)?></td>
								<td colspan=4 nowrap>
									<?php
										$jhotelreservation_datae = isset($jhotelreservation_datae)?$jhotelreservation_datae:$endDate;
										echo JHTML::calendar($jhotelreservation_datae,'jhotelreservation_datae','jhotelreservation_datae3',$appSettings->calendarFormat, array('class'=>'date_hotelreservation keyObserver inner-shadow','onchange'	=>	'checkEndDate(this.value,defaultStartDate,defaultEndDate);'));
									?>
								</td>
							</tr>
							<tr>
								<td class="td_title_hotelreservation"><?php echo JText::_('LNG_ROOMS',true)?></td>
								<td colspan=4 >
									<select id='jhotelreservation_rooms3' name='jhotelreservation_rooms'
										class		= 'select_hotelreservation keyObserver' disabled="disabled"
									>
										<?php
										$i_min = 1;
										$i_max = 5;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
										<option 
											value='<?php echo $i?>'
											<?php echo $userData->rooms==$i ? " selected " : ""?>
										>
											<?php echo $i?>
										</option>
										<?php
										}
										?>
									</select>
								</td>
							</tr>
							<tr style="display:none"> 
								<td align=center>&nbsp;</td>
								<td class="td_title_hotelreservation" colspan="2">
									<?php echo JText::_('LNG_ADULTS_19',true)?>
								</td>
								<?php if($appSettings->show_children!=0){ ?>
									<td class="td_title_hotelreservation" colspan="2">
										<?php echo JText::_('LNG_CHILDREN_0_18',true)?>
									</td>
								<?php }?>
							</tr>
							<tr class="divider">
								<td  class="td_title_hotelreservation"><?php echo JText::_('LNG_GUEST',true)?></td>
								<td colspan="4">
									<select name='jhotelreservation_guest_adult' id='jhotelreservation_guest_adult3'
										class		= 'select_hotelreservation keyObserver' disabled="disabled"
									>
										<?php
										$i_min = 1;
										$i_max = 4;//$params->get("max-room-guests");
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
										<option value='<?php echo $i?>' <?php echo $userData->adults==$i ? " selected " : ""?>><?php echo $i?></option>
										<?php
										}
										?>
									</select>
								</td>
								<?php if($appSettings->show_children!=0){ ?>
									<td colspan="2">
										<select name='jhotelreservation_guest_child' id='jhotelreservation_guest_child'
											class		= 'select_hotelreservation'
										>
											<?php 
											$i_min = 0;
											$i_max = 4;
											
											
											for($i=$i_min; $i<=$i_max; $i++)
											{
											?>
											<option <?php echo $jhotelreservation_guest_child==$i ? " selected " : ""?> > <?php echo $i?> </option>
											<?php 
											}
											?>
										</select>
										
									</td>
								<?php } ?>
							</tr>
							<tr class="divider">
								<td  class="td_title_hotelreservation"><?php echo JText::_('LNG_VOUCHER',true)?></td>
								<td colspan=4>
									<input type="text" class="keyObserver" value="<?php echo $userData->voucher ?>" name="voucher" id="voucher" />
								</td>
							</tr>
							
						</table>
					</div>
					
					<div class="multiple-rooms-container" style="display: block;">
						<div class="container-outer">
							<div class="container-inner">
								<table id="rooms-container" cellpadding="4">
									<?php 
										$index = 0;
										if(isset($userData->roomGuests)){				
											foreach($userData->roomGuests as $nrGuests){
												$index++;
												?>
												<tr id="room-guests-<?php echo $index?>">
												<td><b><?php echo JText::_('LNG_ROOM',true)." ".$index ?>:</b></td><td>
												<td valign="middle">
													<select name="room-guests[]">
														<?php
															$i_min = 1;
															$i_max = 4;//$params->get("max-room-guests");
															
															for($i=$i_min; $i<=$i_max; $i++)
															{
															?>
															<option value='<?php echo $i?>'  <?php echo $nrGuests==$i ? " selected " : ""?>><?php echo $i?></option>
															<?php
															}
															?>
													</select>
												</td>
												<td valign="middle">
													<span><?php echo JText::_('LNG_GUEST',true)?></span>
												</td>
												
												<?php if($appSettings->show_children!=0){ ?>
												<td>
													<select name="room-guests-children[]">
														<?php
															$i_min = 0;
															$i_max = 4;
															
															for($i=$i_min; $i<=$i_max; $i++)
															{
															?>
															<option value='<?php echo $i?>'  <?php echo (isset($userData->roomGuestsChildren[$index-1]) &&  $userData->roomGuestsChildren[$index-1]==$i) ? " selected " : ""?>><?php echo $i?></option>
															<?php
															}
															?>
															
													</select>
												</td>
												<td valign="middle">
													<span><?php echo JText::_('LNG_CHILDREN',true)?></span>
												</td>
												<?php }?>
												<td>
													<?php if($index!=1){?>
													<div id='close-<?php echo $index?>' class='close' onclick='deleteRoom("<?php echo $index?>")'>
													</div>
													<?php }?>
												</td>
												</tr>
												<?php 
											}
										}
									?>
								</table>
								<a id="add-new-room" href="javascript:void(0)" onclick="generateRoomContent(1)"><?php echo JText::_('LNG_ADD_ROOM',true);?></a>
								</br>
								<div class="search-button-multiple clear right">
									<span class="button button-module">
										<button id ="search-btn" onClick="checkRoomRates('userModuleAdvancedForm');"
										type="button" name="checkRates" value="checkRates"><?php echo JText::_('LNG_SEARCH',true)?></button>
									</span>		
								</div>
							</div>
						</div>
				</div>
					
					
					<div class="multiple-rooms-contact"> 
						<?php echo JText::_("LNG_MULTIPLE_ROOM_CONTACT_INFO")?>
					</div>
			</form>
		</div>
		</div>
		<script>
			var before_change1;
			var before_change2;
			jQuery(document).ready(function(){
								
				jQuery("#jhotelreservation_rooms").change( function(e){
					jQuery("#search-box").show();
					//jQuery("#booking-details").show();
					jQuery("#search-btn").html("<?php echo JText::_('LNG_SEARCH',true)?>");
					
					showExpandDialog(this.value,1);
					jQuery("#jhotelreservation_rooms3").val(jQuery("#jhotelreservation_rooms").val());
					jQuery("#jhotelreservation_datas3").val(jQuery("#jhotelreservation_datas").val());
					jQuery("#jhotelreservation_datae3").val(jQuery("#jhotelreservation_datae").val());
					if(this.value!=1){
						lastSel.attr("selected", true);
						lastSel2.attr("selected", true);
					}

					jQuery("#task3").val("searchHotels");
					jQuery("#hotel_id3").val("");
					jQuery("#controller3").val('hotels');

					jQuery('#jhotelreservation_rooms').change(function(e){
					     before_change1 = jQuery(this).data('pre');//get the pre data
					    //Do your work here
					    jQuery(this).data('pre', $(this).val());//update the pre data
					});

					jQuery('#jhotelreservation_rooms2').change(function(e){
					     before_change2 = jQuery(this).data('pre');//get the pre data
					    //Do your work here
					    jQuery(this).data('pre', $(this).val());//update the pre data
					});

					
				});

				var lastSel =  jQuery("#jhotelreservation_rooms option:selected");
				var lastSel2 = jQuery("#jhotelreservation_rooms2 option:selected");

				jQuery("#jhotelreservation_rooms2").click(function(){
				    lastSel2 = jQuery("#jhotelreservation_rooms2 option:selected");
				});

				jQuery("#jhotelreservation_rooms").click(function(){
				    lastSel = jQuery("#jhotelreservation_rooms option:selected");
				});
						
								
				jQuery("#jhotelreservation_rooms2").change( function(e){
					jQuery("#search-box").hide();
					jQuery("#booking-details").hide();
					jQuery("#search-btn").html("<?php echo JText::_('LNG_UPDATE_SEARCH',true)?>");
					showExpandDialog(this.value,0);
					jQuery("#jhotelreservation_rooms3").val(jQuery("#jhotelreservation_rooms2").val());
					jQuery("#jhotelreservation_datas3").val(jQuery("#jhotelreservation_datas2").val());
					jQuery("#jhotelreservation_datae3").val(jQuery("#jhotelreservation_datae2").val());
					if(this.value!=1){
						lastSel.attr("selected", true);
						lastSel2.attr("selected", true);
					}

					jQuery("#task3").val("hotel.showHotel");
					jQuery("#hotel_id3").val(jQuery("#hotel_id").val());
					jQuery("#controller3").val('hotel');
				});

				if(jQuery("#jhotelreservation_rooms").val()==1){// || jQuery("#rooms-container tbody").children().length < 2)
				   //alert("hide");
				   jQuery("#show-expanded").hide();
				   jQuery("#show-expanded2").hide();
				}


				
				
		    });


			
			function showExpandDialog(rooms, type){
				//alert("show");
				if(rooms>1){
					jQuery("#add-new-room").show();
					jQuery('#rooms-container').children().remove();
					generateRoomContent(rooms);
					if(type==1){
						jQuery.blockUI({ message: jQuery('#advanced-search'), css: {width: '635px',top: '5%'} ,overlayCSS: { backgroundColor: '#000',opacity: 0.7  }});
					}else{ 
						jQuery.blockUI({ message: jQuery('#advanced-search'), css: {width: '395px', top: '5%' } ,overlayCSS: { backgroundColor: '#000',opacity: 0.7 }});
					}
					jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
					jQuery("#show-expanded").show();
					jQuery("#show-expanded2").show();
				}else{
					jQuery("#show-expanded").hide();
					jQuery("#show-expanded2").hide();
					jQuery(".room-search").remove();
				}
			}
			
			function showExpandedSearch(){
				jQuery.blockUI({ message: jQuery('#advanced-search'), css: {width: 'auto',top: '5%'} ,overlayCSS: { backgroundColor: '#000',opacity: 0.7  }}); 
				jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
			}
			
			function generateRoomContent(nrRooms){
				var selectContent="";
				var selectContentChildren="";
				<?php
				$i_min = 1;
				$i_max = 4;//$params->get("max-room-guests");
				
				for($i=$i_min; $i<=$i_max; $i++)
				{
				?>
					selectContent+="<option <?php echo $i==2?'selected':''?> value='<?php echo $i?>'><?php echo $i?></option>";
					selectContentChildren+="<option <?php echo $i==2?'selected':''?> value='<?php echo $i-1?>'><?php echo $i-1?></option>";
				<?php
				}
				?>
				
				for(i=1;i<=nrRooms;i++){
					var count = jQuery("#rooms-container tbody").children().length+1;
					if(count>3)
						jQuery("#add-new-room").hide();

					var guestTd = "<td valign='middle'><select name='room-guests[]'>"+selectContent+"</select></td><td><span><?php echo JText::_('LNG_GUEST',true)?></span></td>";
					var childrenTd = "<td valign='middle'><select name='room-guests-children[]'>"+selectContentChildren+"</select></td><td><span><?php echo JText::_('LNG_CHILDREN',true)?></span></td>";
					<?php if($appSettings->show_children==0){ ?>
						childrenTd = "";
					<?php }?>
					var elem = jQuery("<tr id='room-guests-"+count+"'><td><span><b><?php echo JText::_('LNG_ROOM',true)?> "+count+"</b></span></td><td></td>"+guestTd+childrenTd+"<td><div id='close-"+count+"' class='close' onclick='deleteRoom("+count+")'></div></td></tr>");
					jQuery("#rooms-container").append(elem);
				}

				jQuery('#rooms-container tr').each(function(index) {
				   jQuery(this).removeClass("last");
				});
				
				jQuery("#rooms-container tr:last").addClass("last");
				jQuery("#close-1").hide();
			}

			function deleteRoom(id){
				jQuery("#room-guests-"+id).remove();
				jQuery("#rooms-container tr td:first-child span").each(function(index) {
				    jQuery(this).text("<?php echo JText::_('LNG_ROOM',true)?> "+(index+1));
				});
			}

			function revertValue(){
				jQuery('#jhotelreservation_rooms').val(before_change1);
				jQuery('#jhotelreservation_rooms2').val(before_change2);
			}
		</script>
