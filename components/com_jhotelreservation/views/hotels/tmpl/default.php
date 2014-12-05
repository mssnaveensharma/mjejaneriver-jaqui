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

$session = JFactory::getSession();
$userData =  $_SESSION['userData'];

?>
<?php if (isset($this->userData->excursions) && is_array($this->userData->excursions) && count($this->userData->excursions)!=0)
{
?>
<form action="<?php echo JRoute::_('index.php?task=guestDetails.showGuestDetails') ?>" method="post"  name="skipAccomodations" id="skipAccomodations" >
	<div class="right" style="height:200px;">
		<span class="button button-green">
			<button type='submit'><?php echo JText::_('LNG_SKIP_ACCOMODATION',true)?></button>
		</span>
	</div>
	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="view" 			id="view" 				value="guestDetails" />
	<input type="hidden" name="task" 			id="task" 				value="guestDetails.showGuestDetails" />
	<input type="hidden" name="option" 			id="option" 				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="orderBy" 			id="orderBy" 				value="" />
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
	<input type='hidden'	name='reserved_item' 	value='0|0'>
	<input type='hidden'	name='hotel_id'		id="hotel_id" value='0'>
</form>
<?php }?>
</br>
<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&task = hotels.searchHotels') ?>" method="post"  name="searchForm" id="searchForm" >
	<div id="search-container">
		<h3>	
			<?php echo JText::_('LNG_SEARCH_RESULTS');?>
		</h3>
		<div id="search-info">
			<span class="search-title"><?php echo $this->pagination->total ?> <?php echo JText::_('LNG_HOTELS_FOUND',true);?> </span>
			<strong class="search-available"></strong>
			
			<strong><?php  echo JText::_('LNG_YOU_SEARCHED_FOR')?>:</strong>
			<span class=""><?php echo JHotelUtil::getDateGeneralFormat($this->userData->start_date).' '.JText::_('LNG_TO',true).' '.JHotelUtil::getDateGeneralFormat($this->userData->end_date).' - '.JText::_('LNG_NUMBER_OF',true).' '.strtolower(JText::_('LNG_ADULTS',true)).': ', $this->userData->adults. ($this->appSettings->show_children!=0?(' ,'.strtolower(JText::_('LNG_CHILDREN',true)).': '.$this->userData->children):""). ', '.JText::_('LNG_NUMBER_OF',true).' '.JText::_('LNG_ROOMS',true).': '.$this->userData->rooms ?>  </span>
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
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_stars desc'?"checked=checked":'' ?> value="hotel_stars desc" name="orderBy" id="stars" class="radio" type="radio">
					&nbsp;<label for="stars"><?php echo JText::_('LNG_STARS');?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_rating_score desc'?"checked=checked":'' ?> value="hotel_rating_score desc" name="orderBy" id="rating" class="radio" type="radio">
					<label for="rating"><?php echo JText::_('LNG_RATING');?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='noBookings desc'?"checked=checked":'' ?>  value="noBookings desc" name="orderBy" id="mostBooked" class="radio" type="radio">
					&nbsp;<label for="mostBooked"><?php echo JText::_('LNG_MOST_BOOKED');?></label>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="hotel-search-list">
			<?php
			if(count($this->hotels)>0){
				$showNearby = true;
				foreach( $this->hotels as $hotel ){
				?>
				<?php if(isset($hotel->nearBy) && $showNearby){ 
					$showNearby = false
				?>
					<div class="near-by-header"><?php echo JText::_("LNG_NEAR_BY_HOTELS")?></div>	
				<?php } ?>
					<div class="hotel-info row-fluid">
						<div class="hotel-image-holder span3">
							<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>" alt="<?php echo stripslashes($hotel->hotel_name) ?>" title="<?php echo stripslashes($hotel->hotel_name) ?>">
								<img class="hotel-image" 
									src='<?php echo JURI::root().PATH_PICTURES.$hotel->hotel_picture_path?>'
									alt="<?php echo isset($hotel->hotel_picture_info)?$hotel->hotel_picture_info:''; ?>" 
								/>
							</a>
						</div>
						<div class="hotel-content span6">								
							<div class="hotel-title">
								<h2>
									<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>" alt="<?php echo stripslashes($hotel->hotel_name) ?>" title="<?php echo stripslashes($hotel->hotel_name) ?>">
										<?php echo stripslashes($hotel->hotel_name) ?>
									</a> 
								</h2>
								<span class="hotel-stars">
									<?php
									for ($i=1;$i<=$hotel->hotel_stars;$i++){ ?>
										<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
									<?php } ?>
								</span>
							</div>
							
							<div class="hotel-address">
								<?php echo $hotel->hotel_address?>, <?php echo $hotel->hotel_city?>, <?php echo $hotel->hotel_county?>, <?php echo $hotel->country_name?>
							</div>
							<?php if(isset($hotel->nearBy)){?>
								<div class="location-distance">
									<?php echo JText::_("LNG_DISTANCE").": ".round($hotel->distance,1) ?> km
								</div>		
							<?php }?>
							
							<div class="clear"></div>
							<div class="hotel-description">
								<div>
								<?php 
								$hotelDescription = $hotel->hotel_description;
								if( strlen($hotelDescription) > MAX_LENGTH_HOTEL_DESCRIPTION ){
									 echo JHotelUtil::truncate($hotelDescription, MAX_LENGTH_HOTEL_DESCRIPTION, '&hellip;', true);
								?>
								<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>">  <?php  echo JText::_('LNG_READ_MORE',true);?></a>
								<?php } 
								else{
									echo $hotelDescription;
								}
								?>	 
								</div>
								<div class="hotel-selling-points">
									<?php echo $hotel->hotel_selling_points ?>
								</div>
								<ul class="hotel_links">
									<li> <a href="<?php echo JHotelUtil::getHotelLink($hotel)."?".strtolower(JText::_("LNG_PHOTO_GALLERY")) ?>"> <?php  echo ($hotel->hotel_pictures_count)." ".JText::_('LNG_PHOTOS');?></a> </li>
									<li> <a href="<?php echo JHotelUtil::getHotelLink($hotel)."?".strtolower(JText::_("LNG_MAP")) ?>"> <?php  echo JText::_('LNG_VIEW_ON_MAP');?></a> </li>
								</ul>
								<div class="clear"></div>
							</div>
							
						</div>
						<div class="hotel-details span3">
							<?php if($hotel->noReviews >= MINIMUM_HOTEL_REVIEWS) {?>
								<div class="hotel-rating">
									<div class="info">
										<strong><?php  echo JText::_('LNG_CUSTOMER_REVIEW',true);?></strong>
										<a href="<?php echo JHotelUtil::getHotelLink($hotel).'?'.strtolower(JText::_("LNG_REVIEWS")); ?>"><?php echo $hotel->noReviews?> reviews</a>
									</div>
									<div class="rating">
										<?php echo JHotelUtil::fmt($hotel->hotel_rating_score,1)?>
									</div>
									<div class="clear"></div>					
								</div>
							<?php } ?>
							
							<?php if($hotel->min_room_price > 0  && (!isset($this->userData->voucher) || $this->userData->voucher=='')){ ?>
							<div class="hotel-price">
								<span class="price-type"> <?php  echo JText::_('LNG_ROOMS',true);?> </span>
								<span class="details"><?php  echo JText::_('LNG_FROM',true);?> </span>
								<span class="price"><?php echo $hotel->currency_symbol ?> <?php echo JHotelUtil::fmt($hotel->min_room_price,2) ?></span>
								
								<span class="details"><?php echo $this->appSettings->show_price_per_person == true ?"p.p.p.n":"p.r.p.n" ?></span>
								<div class="view-hotel">
									<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>"><?php  echo JText::_('LNG_VIEW_HOTEL',true);?></a>
								</div>
							</div>
							<?php } ?>
							
							<?php if($hotel->min_offer_price>0 & $this->appSettings->is_enable_offers){ ?>
								<div class="offer-price" >
									<span class="price-type"> <?php  echo JText::_('LNG_SPECIAL_OFFERS',true);?> </span>
									<span class="details"><?php  echo JText::_('LNG_FROM',true);?> </span>
									<?php 
										//TODO - find a better solution
										$price = $hotel->min_offer_price;
										if($hotel->offer_price_type == 0){
											$price = $price/$hotel->offer_base_adults; 
										}	
									?>
									<span class="price"><?php echo $hotel->currency_symbol ?> <?php echo JHotelUtil::fmt($price,2) ?></span> 
									<span class="details">p.p</span>
									<div class="view-offer">
										<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>"><?php  echo JText::_('LNG_VIEW_HOTEL',true);?></a>
										<!-- a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&controller=offers&task=searchOffers&hotelId='.$hotel->hotel_id);?>"><?php  echo JText::_('LNG_VIEW_OFFER',true);?></a-->
									</div>
								</div>
							<?php } ?>	
							
							<?php if($hotel->recommended==1){?>
						
								<div class="hotel-recommanded">
									<span><?php  echo JText::_('LNG_RECOMMENDED',true);?></span>
									<div class="hotel-recomandation-popup" >
										To help make your hotel selection process easier, HotelClub 
										has taken on the role to review and identify hotels for you based on price and value. With the HotelClub <img src="/images/ThumbsUp.gif" align="middle" alt=""><span class="fc-hilite"><strong>Recommended Hotel</strong></span> program, you will get the very best room rates available online.<br><br>Whenever you see <img src="/images/ThumbsUp.gif" align="middle" alt=""><span class="fc-hilite">Recommended Hotel</span>, you will know that<ul class="popup_list">
										<li>You have received the best available hotel rate - backed by our <a href="javascript:void(0);" onclick="javascript:openWnd('RateGuarantee.asp', 'Help', 400, 600, 0, 0, 0, 1, 1, 0, 0);">Best Price Guarantee</a></li>
											<li>You have instant confirmation on your reservation**
										                            </li>
										</ul><br><span class="condition"><span>*</span>Conditions apply. Subject to availability.</span></td>
									</div>
								</div>
							<?php } ?>
							
						</div>
						
						<div class="clear"></div>
						
						<div class="hotel-packages" style="display:block">
								<?php if(!empty($hotel->rooms)){ ?>
									<!-- div class="hotel-rooms clear">
										<h3><?php echo JText::_("LNG_ROOMS")?></h3>
										<ul>
											<?php foreach($hotel->rooms as $room){?>
												<li class="clear">
													<div class="overview">
														<div class="name"><strong><?php echo $room[0]?></strong></div>
														<div class="nights">
															<strong>1</strong>
															<em><?php echo JText::_("LNG_NIGHTS")?></em>
														</div>
														<div class="price">
														
															<span class="price-small">
																<span class="currency"><?php echo $hotel->currency_symbol ?></span><span class="amount"><?php echo JHotelUtil::fmt($room[2],2) ?></span>
																<span class="pppn">p.p.p.n.</span>
															</span>
															
															<a href="<?php echo JHotelUtil::getHotelLink($hotel)?>">
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
								<?php if(!empty($hotel->offers) & $this->appSettings->is_enable_offers){ ?>
								<div class="hotel-offers clear">
								<!-- h3><?php echo JText::_("LNG_OFFERS")?></h3-->
									<ul>
										<?php foreach($hotel->offers as $i=>$offer){ ?>
											<?php if ($i>=2) break;?>
												<li id="offer-<?php echo $offer[1]?>" class="offer-container clear" onclick="getRoomCalendar(<?php echo $hotel->hotel_id ?>, <?php echo $userData->year_start?>, <?php echo $userData->month_start?> ,'<?php echo ''.$offer[1].''.$offer[7]?>')">
													<div class="overview row-fluid">
														<div class="span6 package-cell">
															<div class="toggle"><div></div></div>
															<div class="name"><strong><?php echo $offer[0]?></strong></div>
														</div>
														<div class="span6 package-cell">
															<div class="nights">
																<em><?php echo JText::_("LNG_NUMBER_OF_NIGHTS")?></em>
																<strong><?php echo $offer[3]?></strong>
																
															</div>
															<div class="price">
															
																<span class="price-small">
																	<span class="currency"><?php echo $hotel->currency_symbol ?></span><span class="amount"><?php echo JHotelUtil::fmt($offer["price"],2) ?></span>
																	<span class="pppn">p.p.</span>
																</span>
																
																<a class="prevent-click" href="<?php echo JHotelUtil::getHotelLink($hotel)?>">
																	<span class="button button-green">
																		<button type='button'><?php echo JText::_('LNG_BOOK',true)?></button>
																	</span>
																</a>
																
															 </div>
														</div>
														<div class="clear"></div>
													</div>
													<div id="offer-details-<?php echo $offer[1]?>" class="offer-details  row-fluid" style="display:none">
														<div  class="offer-description span6">
															<?php
																echo $offer[8];
															?>
														</div>
														<div class="span6 offer-calendar" id="calendar-holder-<?php echo ''.$offer[1].''.$offer[7]?>" class="room-calendar">	
															<div class="room-loader right"></div>
														
														</div>
														<div class="clear"></div>
													</div>
													
												</li>
										<?php } ?>
									</ul>									
									<?php if(count($hotel->offers)>2){ ?>
										<div>
											<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>">
												<?php echo JText::_("LNG_SHOW_ALL")." ".count($hotel->offers)." ".strtolower(JText::_("LNG_OFFERS"))." ".strtolower(JText::_("LNG_FROM_HOTEL"))." ".stripslashes($hotel->hotel_name) ?> 
											</a>
										</div>
									<?php } ?>
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

<?php if (isset($this->userData->excursions) && is_array($this->userData->excursions) && count($this->userData->excursions)!=0)
{
?>
<form action="<?php echo JRoute::_('index.php?task=guestDetails.showGuestDetails') ?>" method="post"  name="skipAccomodations" id="skipAccomodations" >
	<div class="right" style="height:200px;">
		<span class="button button-green">
			<button type='submit'><?php echo JText::_('LNG_SKIP_ACCOMODATION',true)?></button>
		</span>
	</div>
	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="view" 			id="view" 				value="guestDetails" />
	<input type="hidden" name="task" 			id="task" 				value="guestDetails.showGuestDetails" />
	<input type="hidden" name="option" 			id="option" 				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="orderBy" 			id="orderBy" 				value="" />
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
	<input type='hidden'	name='reserved_item' 	value='0|0'>
	<input type='hidden'	name='hotel_id'		id="hotel_id" value='0'>
</form>
<?php }?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel') ?>" method="post" name="searchFormHotel" id="searchFormHotel">
	<input type="hidden" value="hotel.changeSearch" name="task">
	<input type='hidden' name='resetSearch' value='true'>
	<input id="hotel_id" type="hidden" value="" name="hotel_id">
	<input type="hidden" value="" id="jhotelreservation_datas2" name="jhotelreservation_datas" >
	<input type="hidden" value="" id="jhotelreservation_datae2" name="jhotelreservation_datae" >
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
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


		jQuery(document).ready(function(){
			jQuery('.offer-container').click(function(){
				jQuery(this).toggleClass("open");
				offerId = jQuery(this).attr("id");
				offerId = offerId.replace("offer-","");
				
				if(jQuery(this).hasClass("open")){
					jQuery("#offer-details-"+offerId).slideDown(100);
				}else{
					jQuery("#offer-details-"+offerId).slideUp(100);
				}
			});

			jQuery(".offer-description").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".room-calendar").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".room-calendar").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".prevent-click").click( function(event) {
			    event.stopPropagation();
			} );
	
			
			showRoomCalendars(<?php echo $this->hotels[0]->hotel_id ?>);
		});

		function showRoomCalendars(hotelId){
			var postParameters='';
			postParameters +="&hotel_id="+hotelId;
			postParameters +="&current_room=1";
			postParameters +="&tip_oper=-1";
			
			var postData='&task=hotel.getRoomCalendars'+postParameters;
			jQuery.post(baseUrl, postData, processShowRoomCalendarResults);
		}

		function processShowRoomCalendarResults(responce){
			var xml = responce;
			//alert(xml);
			//xml = parseXml(xml);
			//alert(xml);
			//console.log(xml);
			jQuery("<div>" + xml + "</div>").find('answer').each(function()
			{
				var identifier = jQuery(this).attr('identifier');
				//console.debug(identifier);
				//alert(jQuery("#calendar-holder-"+identifier));
				jQuery("#calendar-holder-"+identifier).html(jQuery(this).attr('calendar'));
			});
		}

		function checkReservationPendingPayments(){
			var postParameters='';
			var postData='&task=hotel.checkReservationPendingPayments';
			jQuery.post(baseUrl, postData, processShowRoomCalendarResult);
		}

		function getRoomCalendar(hotelId, year,month, identifier){
			var htmlContent = jQuery("#calendar-holder-"+identifier).html();
			
			if(htmlContent.search("room-calendar")==-1){
				showRoomCalendar(hotelId, year,month, identifier);
			}
		}
		
		function showRoomCalendar(hotelId,year,month, identifier){
			//alert("show");
			var postParameters='';
			postParameters +="&month="+month;
			postParameters +="&year="+year;
			postParameters +="&identifier="+identifier;
			postParameters +="&hotel_id="+hotelId;
			postParameters +="&tip_oper=-1";
			postParameters +="&current_room=1";

			//alert(postParameters);
			
			jQuery("#loader-"+identifier).show();
			jQuery("#room-calendar-"+identifier).hide();
			
			var postData='&task=hotel.getRoomCalendar'+postParameters;
			//alert(baseUrl + postData);
			jQuery.post(baseUrl, postData, processShowRoomCalendarResult);
		}

		function processShowRoomCalendarResult(responce){
			var xml = responce;
			//alert(xml);
			//xml = parseXml(xml);
			//alert(xml);
			jQuery("<div>" + xml + "</div>").find('answer').each(function()
			{
				//alert("here");
				var identifier = jQuery(this).attr('identifier');
				//console.debug(identifier);
				//alert(jQuery("#calendar-holder-"+identifier));
				jQuery("#calendar-holder-"+identifier).html(jQuery(this).attr('calendar'));
			});
		}

		function parseXml(xml) {
		     if (jQuery.browser.msie) {
		        var xmlDoc = new ActiveXObject("Microsoft.XMLDOM"); 
		        xmlDoc.loadXML(xml);
		        xml = xmlDoc;
		    }   
		    return xml;
		}

		function selectCalendarDate(hotelId, startDate, endDate){
			jQuery('#jhotelreservation_datas2').val(startDate);
			jQuery('#jhotelreservation_datae2').val(endDate);
			jQuery('#hotel_id').val(hotelId);
			jQuery("#searchFormHotel").submit();
		}
				
	</script>
	