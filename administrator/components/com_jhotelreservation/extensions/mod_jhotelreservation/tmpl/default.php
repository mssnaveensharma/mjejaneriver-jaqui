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
//JHTML::_('stylesheet',					'modules/mod_jhotelreservation/assets/luxury.css');
JHTML::_('script', 							'administrator/components/com_jhotelreservation/assets/utils.js');
JHTML::_('script', 							'components/com_jhotelreservation/assets/js/search.js');
JHTML::_('script', 		'components/com_jhotelreservation/assets/jhotelreservationcalendar.js');

$appSetings = JHotelUtil::getInstance()->getApplicationSettings();
//dmp($userData->roomGuests);
?>
<script>
	var dateFormat = "<?php echo  $appSetings->dateFormat; ?>";
	var message = "<?php echo JText::_('LNG_ERROR_PERIOD',true)?>";
	var defaultEndDate = "<?php echo $params->get('end-date'); ?>";
	var defaultStartDate = "<?php echo $params->get('start-date'); ?>";
</script>

		<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userModuleForm" id="userModuleForm" >
			<input type='hidden' name='tip_oper' value='-2'/>
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
			<input type='hidden' name='searchType' id="searchType" value='<?php echo $userData->searchType ?>'/>
			<input type='hidden' name='filterParams' id="filterParams" value='<?php echo isset($userData->filterParams) ? $userData->filterParams :''?>' />
			<input type="hidden" name="resetSearch" id="resetSearch" value=""/>
			<?php 
				if(isset($userData->roomGuests)){
					foreach($userData->roomGuests as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests[]' value='<?php echo $guestPerRoom?>'/>
					<?php }
				}
			?>
			<div class="mod_hotel_reservation" id="mod_hotel_reservation">
				<table>
					<tr class="tr_title_hotelreservation">
						<td colspan="5"><?php echo JText::_('LNG_FIND_BEST_HOTEL_DEAL',true)?></td>
					</tr>
					
					<tr class="divider">
						<td colspan="5">
							<div class="search-nav">
								<strong>
									<?php echo JText::_('LNG_FIND_HOTEL',true);?>
								</strong>
								
								<br/>
								<input  class="keyObserver inner-shadow" type="text" value="<?php echo $userData->keyword ?>" name="keyword" id="keyword" />
								<br/>
							</div>
						</td>
					</tr>
					<tr>
						<td class="td_title_hotelreservation"><?php echo JText::_('LNG_ARIVAL',true)?></td>
						<td colspan="4" nowrap="nowrap" >
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

						</td>
					</tr>
					<tr>
						<td class="td_title_hotelreservation" ><?php echo JText::_('LNG_DEPARTURE',true)?></td>
						<td colspan=4 nowrap>
							<?php
								echo JHTML::calendar($jhotelreservation_datae,'jhotelreservation_datae','jhotelreservation_datae',$appSetings->calendarFormat, array('class'=>'date_hotelreservation keyObserver inner-shadow','onchange'	=>	'checkEndDate(this.value,defaultStartDate,defaultEndDate);'));

							?>
						</td>
					</tr>
					<tr>
						<td class="td_title_hotelreservation"><?php echo JText::_('LNG_ROOMS',true)?></td>
						<td colspan=4 >
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
							<a id="show-expanded" href="javascript:void(0)" onclick="showExpandedSearch()">Show</a>
						</td>
					</tr>
					<!-- tr style="display:none"> 
						<td align=center>&nbsp;</td>
						<td class="td_title_hotelreservation" colspan="2">
							<?php echo JText::_('LNG_ADULTS_19',true)?>
						</td>
						<td class="td_title_hotelreservation" colspan="2">
							<?php echo JText::_('LNG_CHILDREN_0_18',true)?>
						</td>
					</tr-->
					<tr class="divider">
						<td  class="td_title_hotelreservation"><?php echo JText::_('LNG_GUEST',true)?></td>
						<td colspan="4">
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
							
						</td>
						<td colspan="2">
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
						</td>
					</tr>
					<tr class="divider">
						<td  class="td_title_hotelreservation"><?php echo JText::_('LNG_VOUCHER',true)?></td>
						<td colspan=4>
							<input type="text" class="keyObserver inner-shadow" value="<?php echo $userData->voucher ?>" name="voucher" id="voucher" />
						</td>
					</tr>
					<tr>
						<td colspan=5 class="search-btn">				
								<a   
									href		=	"javascript:void(0);" 
									onClick		=	"checkRoomRates('userModuleForm');" 
								>
									<div class="btn_hotelreservation">&nbsp;<?php echo JText::_('LNG_SEARCH',true)?>&nbsp;</div>
								</a> 
							
						</td>
					</tr>
				</table>	
			</div>
			
			<?php 
				$filter = $userData->searchFilter;
				$filterCategories= $filter["filterCategories"];
				$showFilter = JRequest::getVar( 'showFilter');
				if(count($filterCategories)>0 && isset($showFilter)){?>
					<div id="search-filter" class="seach-filter moduletable" >
						<h3><?php echo JText::_('LNG_SEARCH_FILTER',true)?></h3>
						<?php 
							foreach ($filterCategories as $filterCategory){
								if(count($filterCategory['items'])>0){
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
							}
						?>
						
					</div>
			<?php } ?>
		</form>
		<script>
			jQuery(document).ready(function(){
				
				jQuery(".keyObserver").keypress( function(e){
					if(e.which == 13) {
						checkRoomRates('userModuleForm');
					}
				});
			});			
		</script>
		<?php 
			require_once JPATH_SITE.'/components/com_jhotelreservation/include/multipleroomselection.php';
		?> 