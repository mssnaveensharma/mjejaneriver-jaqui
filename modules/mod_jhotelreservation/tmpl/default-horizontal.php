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


defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.session.session' );

$appSetings = JHotelUtil::getInstance()->getApplicationSettings();
?>
<script>
	var dateFormat = "<?php echo $appSetings->dateFormat; ?>";
	var message = "<?php echo JText::_('LNG_ERROR_PERIOD',true)?>";
	var defaultEndDate = "<?php echo $params->get('end-date'); ?>";
	var defaultStartDate = "<?php echo $params->get('start-date'); ?>";
</script>

		<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userModuleForm" id="userModuleForm" >
			<input type='hidden' name='controller' value='search'/>
			<input type='hidden' name='task' value='searchHotels'/>
			<input type='hidden' name='year_start' value=''/>
			<input type='hidden' name='month_start' value=''/>
			<input type='hidden' name='day_start' value=''/>
			<input type='hidden' name='year_end' value=''/>
			<input type='hidden' name='month_end' value=''/>
			<input type='hidden' name='hotel_id' value=''/>
			<input type='hidden' name='day_end' value=''/>
			<input type='hidden' name='rooms' value='' />
			<input type='hidden' name='guest_adult' value=''/>
			<input type='hidden' name='guest_child' value=''/>
			<input type='hidden' name='filterParams' id="filterParams" value='<?php echo isset($userData->filterParams) ? $userData->filterParams :''?>' />
			<input type="hidden" name="resetSearch" id="resetSearch" value="true"/>
			<?php 
				if(isset($userData->roomGuests)){
					foreach($userData->roomGuests as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests[]' value='<?php echo $guestPerRoom?>'/>
					<?php }
				}
			?>
			<?php 
				if(isset($userData->roomGuestsChildren)){
					foreach($userData->roomGuestsChildren as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests-children[]' value='<?php echo $guestPerRoom?>'/>
					<?php }
				}
			?>
			<div class="mod_hotel_reservation <?php echo $params->get('layout-type') ?> row-fluid " id="mod_hotel_reservation">
				<div id="mod-hotel-reservation-wrapper" class="span12">	
 				<?php if ($params->get('show-search')==1){?>
					<fieldset class="reservation-cell find ">
						<div class="search-nav">
							<label for="keyword"><?php echo JText::_('LNG_FIND_HOTEL',true);?></label>
							<input  class="keyObserver inner-shadow" type="text" value="<?php echo $userData->keyword ?>" name="keyword" id="keyword" placeholder="<?php echo JText::_("LNG_TYPE_INSTRUCTIONS")?>"/>
						</div>
					</fieldset>
				<?php } ?>
				<fieldset class="reservation-cell ">	
					<div class="small">	
					<ul class="r-dates">
				        <li class="arrive">
				        	<label for="jhotelreservation_datas" class=""><?php echo "Check In";//JText::_('LNG_ARIVAL',true)?></label>
				        	<div>
					        	<?php
										echo JHTML::calendar(
																$jhotelreservation_datas,'jhotelreservation_datas','jhotelreservation_datas',$appSetings->calendarFormat, 
																array(
																		'class'		=>'date_hotelreservation keyObserver inner-shadow', 
																		'onchange'	=>
																					"
																					checkStartDate(this.value,defaultStartDate,defaultEndDate);
																					setDepartureDate('jhotelreservation_datae',this.value);
																		"
																	)
															);
		
									?>
							</div>
						</li>
				        <li class="departure">
				       	 <label for="jhotelreservation_datae" class=" "><?php echo "Check Out"//JText::_('LNG_DEPARTURE',true)?> </label>
				       	 	<div>
					       	 	<?php
										echo JHTML::calendar($jhotelreservation_datae,'jhotelreservation_datae','jhotelreservation_datae',$appSetings->calendarFormat, array('class'=>'date_hotelreservation keyObserver inner-shadow','onchange'	=>	'checkEndDate(this.value,defaultStartDate,defaultEndDate);'));
									?>
							</div>
				       	 </li>
				       	
				    </ul>
				    </div>	
				</fieldset>		
				<fieldset class="reservation-cell units ">
					<div class="">
						<ul class="r-details">
					        
							<li class="">
								<label for="jhotelreservation_rooms" class=""><?php echo "Persons"//JText::_('LNG_GUEST',true)?></label>
								<select name='jhotelreservation_guest_adult' id='jhotelreservation_guest_adult'
									class		= 'select_hotelreservation keyObserver inner-shadow'
								>
									<?php
									$i_min = 1;
									$i_max = $params->get("max-room-guests");
									if($jhotelreservation_guest_adult>$i_max)
										$i_max = $jhotelreservation_guest_adult;
									
									for($i=$i_min; $i<=$i_max; $i++)
									{
									?>
									<option value='<?php echo $i?>'  <?php echo $jhotelreservation_guest_adult==$i ? " selected " : ""?>><?php echo $i?></option>
									<?php
									}
									?>
								</select>
							</li>
							<li class="rooms">
					        	<label for="jhotelreservation_rooms" class=""><?php echo JText::_('LNG_ROOMS',true)?></label>
			
								<select id='jhotelreservation_rooms' name='jhotelreservation_rooms'
									class		= 'select_hotelreservation keyObserver inner-shadow'
								>
									<?php
									$i_min = 1;
									$i_max = $params->get("max-rooms");
									if(!isset($i_max))
										$i_max= 10;
									
									for($i=$i_min; $i<=$i_max; $i++)
									{
									?>
									<option 
										value='<?php echo $i?>'
										<?php echo $jhotelreservation_rooms==$i ? " selected " : ""?>
									>
										<?php echo $i?>
									</option>
									<?php
									}
									?>
								</select>
								<!-- <a id="show-expanded" href="javascript:void(0)" onclick="showExpandedSearch()">Show</a> -->
							</li>
							
							<li class="" style="<?php echo $appSettings->show_children!=0 ? "":"display:none" ?>" >
								<label><?php echo JText::_('LNG_CHILDREN',true)?></label>
									<select name='jhotelreservation_guest_child' id='jhotelreservation_guest_child'
									class		= 'select_hotelreservation'
									>
										<?php
										$i_min = 0;
										$i_max = 10;
										
										for($i=$i_min; $i<=$i_max; $i++)
										{
										?>
											<option <?php echo $jhotelreservation_guest_child==$i ? " selected " : ""?> value='<?php echo $i?>'  ><?php echo $i?></option>
											<?php
											}
											?>
										</select>
							</li>
							
						</ul>
					</div>
				</fieldset>		
				<?php if ($params->get('show-voucher')==1){?>	
				<fieldset class="reservation-cell voucher  ">
					<div class="search-nav">
						<label for="voucher" class=""><?php echo JText::_('LNG_VOUCHER',true)?></label>
						<input type="text" class="keyObserver inner-shadow" value="<?php echo $userData->voucher ?>" name="voucher" id="voucher" />
					</div>
				</fieldset>					
				<?php }?>	
				<fieldset class="reservation-cell padding-search ">
					<button type="submit" class="ui-hot-button ui-hot-button-dark-blue" onClick ="jQuery('#resetSearch').val(1);checkRoomRates('userModuleForm'); ">
						<span class="ui-button-text"><?php echo "CHECK AVAILABILITY"//JText::_("LNG_SEARCH")?></span>
					</button>
				</fieldset>	
				</div>	
				<div class="clear"></div>				
			</div>
			
			<?php 
			if($params->get("show-filter")){
				$filter = $userData->searchFilter;
				$filterCategories= $filter["filterCategories"];
				$showFilter = JRequest::getVar( 'showFilter');
				if(count($filterCategories)>0 && isset($showFilter)){?>
					<div id="search-filter" class="seach-filter moduletable module-menu" >
					<div>
						<div>
						<h3><?php echo JText::_('LNG_SEARCH_FILTER',true)?></h3>
						<?php 
							foreach ($filterCategories as $filterCategory){
								echo '<div class="search-category-box">';
								echo '<h4>'.$filterCategory['name'].'</h4>';
								echo '<ul>';
								foreach ($filterCategory['items'] as $filterCategoryItem){
									if(isset($filterCategoryItem->count)){
								?>	
									<li <?php if(isset($filterCategoryItem->selected)) echo 'class="selectedlink"';  ?> >  	 										
										<a href="javascript:void(0)" onclick="<?php if(isset($filterCategoryItem->selected)) echo "removeFilterRule('$filterCategoryItem->identifier=$filterCategoryItem->id')"; else echo "addFilterRule('$filterCategoryItem->identifier=$filterCategoryItem->id')";?>"><?php echo $filterCategoryItem->name ?> <?php echo '('.$filterCategoryItem->count.')' ?> <?php if(isset($filterCategoryItem->selected)) echo '<span class="cross">(remove)</span>';  ?></a>
									</li>
								<?php
									} 
								}
								echo '</ul>';
								echo '</div>';
								
							}
						?>
						</div>
						</div>
					</div>
			<?php } 
			
				}
			?>
		</form>
		<script>
			jQuery(document).ready(function(){
				
				jQuery(".keyObserver").keypress( function(e){
					if(e.which == 13) {
						jQuery('#resetSearch').val(1);
						checkRoomRates('userModuleForm');
					}
				});
			});			
		</script>
		<?php 
			require_once JPATH_SITE.'/components/com_jhotelreservation/include/multipleroomselection.php';
			require_once 'autocomplete.php';
		?> 