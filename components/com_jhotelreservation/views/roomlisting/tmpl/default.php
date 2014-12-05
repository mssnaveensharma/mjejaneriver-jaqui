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

defined('_JEXEC') or die('Restricted access'); 
// dmp($this->_models);
$need_all_fields = true;
if( isset($this->_models['search'] ) && isset($this->_models['search']->tmp ) )
{
	if( strlen( $this->_models['search']->tmp ) > 0 )
		$need_all_fields = $this->_models['search']->tmp > strtotime( " - 10 min ")? false : true; //allow only 10 min for admin
}
if( isset($this->userData->searchFilter) )
	$searchFilter= $this->userData->searchFilter;
if( isset($this->userData->orderBy) )
	$orderBy = $this->userData->orderBy;
else	
	$orderBy = '';

?>
<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=roomlisting&layout=rooms') ?>" method="post"  name="searchForm" id="searchForm" >
	<div id="search-container">
		<h3>	
			<?php echo JText::_('LNG_SEARCH_RESULTS');?>
		</h3>
		<div id="search-info">
			<span class="search-title"><?php echo $this->pagination->total ?> <?php echo JText::_('LNG_ROOMS_FOUND',true);?> </span>
			<strong class="search-available"></strong>
			
			<strong><?php  echo JText::_('LNG_YOU_SEARCHED_FOR')?>:</strong>
			<span class=""><?php echo strtolower(JHotelUtil::getDateGeneralFormat($this->userData->start_date)).' '.JText::_('LNG_TO',true).' '.strtolower(JHotelUtil::getDateGeneralFormat($this->userData->end_date)).', '.JText::_('LNG_NUMBER_OF',true).' '.strtolower(JText::_('LNG_ADULTS',true)).': ', $this->userData->adults.' ,'.strtolower(JText::_('LNG_CHILDREN',true)).': ', $this->userData->children. ', '.JText::_('LNG_NUMBER_OF',true).' '.strtolower(JText::_('LNG_ROOMS',true)).': '.$this->userData->rooms ?>  </span>
		</div>
		<div id="search-order">
			<strong><?php echo $this->pagination->getResultsCounter()?></strong><br>
			<a style="display:none" href="" class="next">Next 25 Hotels ></a>
			<ul class="horizontal">
				<li class="title">
					<span><?php echo JText::_('LNG_SORT_BY');?>: </span>
				</li>
				<li style="display:none">
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_city asc'?"checked=checked":'' ?> value="hotel_city asc" name="orderBy" id="place" class="radio" type="radio"> &nbsp;
					<label for="place"><?php echo JText::_('LNG_PLACE');?></label>
				</li>
				<li style="display:none">
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_name'?"checked=checked":'' ?> name="orderBy" id="hotelName" value="hotel_name" class="radio" type="radio"> &nbsp;
					<label for="hotelName"><?php echo JText::_('LNG_HOTEL_NAME');?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='lowest_hotel_price asc' || $orderBy=='starting_price_offers asc'?"checked=checked":'' ?>  value="<?php echo  isset($this->userData->voucher) & $this->userData->voucher!='' ? 'starting_price_offers asc': 'lowest_hotel_price asc' ?>" name="orderBy" id="price" class="radio" type="radio"> 
					<label for="price"><?php echo JText::_('LNG_PRICE');?></label>
				</li>
<!-- 				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_stars desc'?"checked=checked":'' ?> value="hotel_stars desc" name="orderBy" id="stars" class="radio" type="radio">
					&nbsp;<label for="stars"><?php echo JText::_('LNG_STARS');?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_rating_score desc'?"checked=checked":'' ?> value="hotel_rating_score desc" name="orderBy" id="rating" class="radio" type="radio">
					<label for="rating"><?php echo JText::_('LNG_RATING');?></label>
				</li> -->
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='noBookings desc'?"checked=checked":'' ?>  value="noBookings desc" name="orderBy" id="mostBooked" class="radio" type="radio">
					&nbsp;<label for="mostBooked"><?php echo JText::_('LNG_MOST_BOOKED');?></label>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="hotel-search-list">
			<?php
			if(count($this->rooms)>0){
			foreach( $this->rooms as $room )
			{
			?>
					<div class="hotel-info row-fluid">
						<div class="hotel-image-holder span3">
							<a href="<?php echo JHotelUtil::getRoomLink($room) ?>" alt="<?php echo stripslashes($room->room_name) ?>" title="<?php echo stripslashes($room->room_name) ?>">
								<img class="hotel-image" 
									src='<?php echo JURI::root().PATH_PICTURES.$room->pictures[0]->room_picture_path;?>'
									alt="<?php echo isset($room->pictures[0]->room_picture_info)?$room->pictures[0]->room_picture_info:''; ?>" 
								/>
							</a>
						</div>

						<div class="hotel-content span6">								
							<div class="hotel-title">
								<h2>
									<a href="<?php echo JHotelUtil::getRoomLink($room) ?>" alt="<?php echo stripslashes($room->hotel_name) ?>" title="<?php echo stripslashes($room->room_name) ?>">
										<?php echo stripslashes($room->room_name) ?>
									</a> 
								</h2>
								<!-- <span class="hotel-stars">
									<?php
									for ($i=1;$i<=$room->hotel_stars;$i++){ ?>
										<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
									<?php } ?>
								</span> -->
							</div>
							
							<div class="hotel-address">
								<?php echo $room->hotel_name?> <br>
								<?php echo $room->hotel_address?>, <?php echo $room->hotel_city?>, <?php echo $room->hotel_county?>, <?php echo $room->country_name?>
							</div>
							
							<div class="clear"></div>
							<div class="hotel-description">
								<div>
								<?php 
								$roomDescription = $room->room_main_description;
								if( strlen($roomDescription) > MAX_LENGTH_HOTEL_DESCRIPTION ){
									 echo JHotelUtil::truncate($roomDescription, MAX_LENGTH_HOTEL_DESCRIPTION, '&hellip;', true);
								?>
								<a href="<?php echo JHotelUtil::getRoomLink($room) ?>">  <?php  echo JText::_('LNG_READ_MORE',true);?></a>
								<?php } 
								else{
									echo $roomDescription;
								}
								?>	 
								</div>
								<ul class="hotel_links">
									<li> <a href="<?php echo JHotelUtil::getRoomLink($room) ?>"> <?php  echo (count($room->pictures))." ".JText::_('LNG_PHOTOS');?></a> </li>
									<li> <a href="<?php echo JHotelUtil::getHotelLink($room) ?>?map"> <?php  echo JText::_('LNG_VIEW_ON_MAP');?></a> </li>
								</ul>
							</div>
							
						</div>
						
						<div class="hotel-details span3">
							
							<?php if(!isset($this->userData->voucher) || $this->userData->voucher==''){ ?>
							<div class="hotel-price">
								<span class="details"><?php  echo JText::_('LNG_FROM',true);?> </span>
								<span><br> </span>
								<span class="price"><?php echo $room->currency_symbol ?>
									<?php 
										if(!$room->is_disabled){
											if(JRequest::getVar( 'show_price_per_person')==1){
												echo $room->pers_total_price;
											}else{ 
												echo $room->room_average_display_price;
											}
										}
									?>
								</span>
								<div class="view-hotel">
									<a href="<?php echo JHotelUtil::getRoomLink($room) ?>"><?php  echo JText::_('LNG_VIEW_HOTEL',true);?></a>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="clear"></div>
						
						<div class="hotel-packages" style="display:block">
								<?php if(!empty($room->rooms)){ ?>
									<!-- div class="hotel-rooms clear">
										<h3><?php echo JText::_("LNG_ROOMS")?></h3>
										<ul>
											<?php foreach($room->rooms as $room){?>
												<li class="clear">
													<div class="overview">
														<div class="name"><strong><?php echo $room[0]?></strong></div>
														<div class="nights">
															<strong>1</strong>
															<em><?php echo JText::_("LNG_NIGHTS")?></em>
														</div>
														<div class="price">
														
															<span class="price-small">
																<span class="currency"><?php echo $room->currency_symbol ?></span><span class="amount"><?php echo JHotelUtil::fmt($room[2],2) ?></span>
																<span class="pppn">p.p.p.n.</span>
															</span>
															
															<a href="<?php echo JHotelUtil::getRoomLink($room)?>">
																<span class="button button-green">
																	<button type='button'><?php echo JText::_('LNG_BOOK',true)?></button>
																</span>
															</a>
															
														</div>
														<div class="clear"></div>
													</div>
												</li>
											<?php } ?>
										</ul>
									</div -->
								<?php } ?>
								<?php if(!empty($room->offers) & $this->appSettings->is_enable_offers){ ?>
								<div class="hotel-offers clear">
								<!-- h3><?php echo JText::_("LNG_OFFERS")?></h3-->
									<ul>
										<?php foreach($room->offers as $i=>$offer){ ?>
											<?php if ($i>=2) break;?>
												<li class="clear">
													<div class="overview">
														<div class="name"><strong><?php echo $offer[0]?></strong></div>
														<div class="nights">
															<em><?php echo JText::_("LNG_NUMBER_OF_NIGHTS")?></em>
															<strong><?php echo $offer[3]?></strong>
															
														</div>
														<div class="price">
														
															<span class="price-small">
																<span class="currency"><?php echo $room->currency_symbol ?></span><span class="amount"><?php echo JHotelUtil::fmt($offer["price"],2) ?></span>
																<span class="pppn">p.p.</span>
															</span>
															
															<a href="<?php echo JHotelUtil::getRoomLink($room)?>">
																<span class="button button-green">
																	<button type='button'><?php echo JText::_('LNG_BOOK',true)?></button>
																</span>
															</a>
															
														</div>
														<div class="clear"></div>
													</div>
												</li>
										<?php } ?>
									</ul>
								</div>
								<?php } ?>
						</div>
					</div>
			<?php
				}
			}
			?>
			<div class="pagination">
				<?php echo $this->pagination->getListFooter(); ?>
				<div class="clear"></div>
			</div>		
	</div> 
	
	
	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="orderBy" 			id="orderBy" 				value="" />
	
	<?php 
		$session = JFactory::getSession();
		$userData =  $_SESSION['userData'];
	?>

	<input type='hidden'	name='task' value='hotels.searchRooms'>
	<input type='hidden'	name='jhotelreservation_datas' value='<?php echo $userData->start_date?>'>
	<input type='hidden'	name='jhotelreservation_datae' value='<?php echo $userData->end_date?>'>
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
	<input type='hidden'	name='filterParams'		id="filterParams" value='<?php  echo $this->searchFilter ?>'>
</form>

<script>
		//not used anymore
		function setCheckedValue(radioObj, newValue) {
			if(!radioObj)
				return;
			var radioLength = radioObj.length;
			if(radioLength == undefined) {
				radioObj.checked = (radioObj.value == newValue.toString());
				return;
			}
			for(var i = 0; i < radioLength; i++) {
				radioObj[i].checked = false;
				if(radioObj[i].value == newValue.toString()) {
					radioObj[i].checked = true;
				}
			}
		}
		
		function showHotel(hotelId, selectedTab){
			jQuery("#tabId").val(selectedTab);
			jQuery("#tip_oper").val('-1');
			jQuery("#controller").val('');
			jQuery("#task").val('checkAvalability');
			jQuery("#hotel_id").val(hotelId);
			jQuery("#searchForm").submit();
		}
		
		function changeOrder(orderField){
			jQuery("#orderBy").val(orderField);
			jQuery("#searchForm").submit();	
		}
		
	</script>
	