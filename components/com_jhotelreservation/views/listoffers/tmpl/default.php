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

$offerUrl = JURI::current().'?controller=offers&task=displayOffers';
$config =JFactory::getConfig();
$document = JFactory::getDocument();

$title = JText::_('LNG_METAINFO_LANDING_PAGE_TITLE',true);
$description = JText::_('LNG_METAINFO_LANDING_PAGE_DESCRIPTION',true);
$keywords = JText::_('LNG_METAINFO_LANDING_PAGE_KEYWORDS',true);

$document->setTitle($title);
$document->setDescription($description);
$document->setMetaData('keywords', $keywords);

$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');
$document->addCustomTag('<meta property="og:image" content="'.JURI::base(). 'images' . DS .'icon-facebook.jpg" /> ');
$document->addCustomTag('<meta property="og:type" content="website"/>');
$document->addCustomTag('<meta property="og:url" content="'.$offerUrl.'"/>');
$document->addCustomTag('<meta property="og:site_name" content="'.$config->get( 'config.sitename' ).'"/>');
$document->addCustomTag('<meta property="fb:admins" content="george.bara"/>');

$orderBy = $this->orderBy;
if($orderBy == ''){
	$orderBy = " rand() ";
}

?>
<div id="special-offers-container">
	<div id="search-container">
		<h3>	
			<?php echo JText::_('LNG_OFFERS_RESULTS',true);?>
		</h3>
		<div id="search-order">
			<?php if(count($this->offers)>0){ ?>
			<strong><?php echo $this->pagination->getResultsCounter()?></strong>
			<br/>
			<ul class="horizontal">
				<li class="title">
					<span><?php echo JText::_('LNG_SORT_BY',true);?>: </span>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_city asc'?"checked=checked":'' ?> value="hotel_city asc" name="orderBy" id="place" class="radio" type="radio"> &nbsp;
					<label for="place"><?php echo JText::_('LNG_PLACE',true);?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_name'?"checked=checked":'' ?> name="orderBy" id="hotelName" value="hotel_name" class="radio" type="radio"> &nbsp;
					<label for="hotelName"><?php echo JText::_('LNG_HOTEL_NAME',true);?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='starting_price asc'?"checked=checked":'' ?>  value="starting_price asc" name="orderBy" id="price" class="radio" type="radio"> &nbsp;
					<label for="price"><?php echo JText::_('LNG_PRICE',true);?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_stars desc'?"checked=checked":'' ?> value="hotel_stars desc" name="orderBy" id="stars" class="radio" type="radio">
					&nbsp;<label for="stars"><?php echo JText::_('LNG_STARS',true);?></label>
				</li>
				<li>
					<input onclick="changeOrder(this.value)" <?php echo $orderBy=='hotel_rating_score desc'?"checked=checked":'' ?> value="hotel_rating_score desc" name="orderBy" id="rating" class="radio" type="radio"> &nbsp;
					<label for="rating"><?php echo JText::_('LNG_RATING',true);?></label>
				</li>
			</ul>
			<?php } else{?>
				<br/>
				<strong><?php echo JText::_('LNG_NO_OFFER_FOUND',true)?></strong>
			<?php }?>
		</div>
				
		</div>
	</div>
	<div id="special-offers" class="special-offers">
	
	<?php 
		if(count($this->offers)>0)
		foreach($this->offers as $offer){?>
	
		<div class="offer-container <?php echo $offer->featuredOffer==1?"featured":"" ?> row-fluid">
			<div class="hotel-name span12">
				<b><?php echo $offer->hotel_name?></b> 
				<span class="hotel-stars">
					<?php
					for ($i=1;$i<= $offer->hotel_stars;$i++){ ?>
						<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
					<?php } ?>
				</span>
			</div>
			<div class="row-fluid">
				<div class="hotel-details span4">
					<dl class="special-search-result">
						<dd class="thumbnail">
							<a href="<?php echo JHotelUtil::getOfferLink($offer, $this->mediaReferer, $this->voucher) ?>"> 
								<img src="<?php if(count($offer->pictures)>0) echo JURI::root().PATH_PICTURES.$offer->pictures[0]->offer_picture_path?>" alt=""/>
							</a>
						</dd>
						<dd class="location">
							<span class="adr"> <span class="locality"> <?php echo $offer->hotel_city?> </span>, <span
								class="country-name"><?php echo $offer->country_name ?></span>
							</span>
						</dd>
						<dd class="">
							<div class="small-arrow">
								<a href="<?php echo JHotelUtil::getHotelLink($offer).'?minNights='.$offer->offer_min_nights.(!empty($this->mediaReferer)?'&mediaReferer='.$this->mediaReferer:'').(!empty($this->voucher)?'&voucher='.$this->voucher:''); ?>"><?php echo JText::_('LNG_VIEW_HOTEL',true);?></a>
							</div>
						</dd>
					</dl>
				</div>
				<div class="sp_offers span8">
					<div class="offerDescription">
						<div class="offer-price hidden-phone">
							<span class="details"><?php  echo JText::_('LNG_FROM',true);?> </span>
							<span class="price"><?php echo $offer->currency_symbol  ?> <?php echo JHotelUtil::fmt($offer->starting_price, 2)  ?></span> p.p.
							<div class="view-offer">
								<a href="<?php echo JHotelUtil::getOfferLink($offer, $this->mediaReferer, $this->voucher) ?>"><?php  echo JText::_('LNG_VIEW_OFFER',true);?></a>
							</div>
						</div>
						<div>
							<div>
								<h3 class="offer-title">
									<a href="<?php echo JHotelUtil::getOfferLink($offer, $this->mediaReferer, $this->voucher) ?>">
										<?php echo $offer->offer_name?> </a>
								</h3>
							</div>
							<div class="richTxt">
								<?php
								$offer->offer_description = $offer->offer_short_description;
								 if( strlen($offer->offer_description) > 100 ){
									 echo JHotelUtil::truncate($offer->offer_description, 100, '&hellip;', true);
								?>
									 
								<?php } else {
									echo $offer->offer_description;
								}
								
								?>	 
								<br/>
								<?php						
									 if( strlen($offer->offer_content) > 300 ){
									 	echo JHotelUtil::truncate($offer->offer_content, 300, '&hellip;', true);
									 } else {
										echo $offer->offer_content;
									 }
								?>	 
								<a href="<?php echo  JHotelUtil::getOfferLink($offer, $this->mediaReferer, $this->voucher) ?>">  <?php  echo JText::_('LNG_READ_MORE',true);?></a>
							</div>
							<div class="offer-date-interval splStayDate" style="display:none">
								<div>
									<b><?php echo JText::_('LNG_RESERVE_BY',true);?>:</b> <?php echo JHotelUtil::getDateGeneralFormat($offer->offer_dataef) ?>
								</div>
								<div class="offer-date-interval splStayDate">		
									<b><?php echo JText::_('LNG_STAY',true);?>:</b> <?php echo JHotelUtil::getDateGeneralFormat($offer->offer_datas) ?> - <?php echo JHotelUtil::getDateGeneralFormat($offer->offer_datae) ?>
								</div>
								<div class="offer-date-interval splStayDate">		
									<b><?php echo JText::_('LNG_STARTING_PRICE',true);?>:</b> <?php echo $offer->currency_symbol ?> <?php echo JHotelUtil::fmt($offer->starting_price ,2)?> p.p.
								</div>
								<div class="offer-date-interval splStayDate">		
									<b><?php echo JText::_('LNG_MINIMUM_NIGHTS',true);?>:</b> <?php echo $offer->offer_min_nights ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div> 
		</div>
		<div class="offers-divider">
		</div>		
		<?php } ?>
	</div>
	<?php if(count($this->offers)>0){ ?>
	<form id="searchForm" name="searchForm" action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post">
		<input type="hidden" name="task" 				id="task" 					value="offers.searchOffers" />
		<input type="hidden" name="voucher" 			id="voucher" 				value="<?php echo $this->voucher ?>" />
		<input type="hidden" name="orderBy" 			id="orderBy" 				value="<?php echo $orderBy?>" />
		<div class="pagination">
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
		
	</form>
	<?php } ?>
		
</div>


<script>
	function changeOrder(orderField){
		jQuery("#orderBy").val(orderField);
		jQuery("#searchForm").submit();	
	}

	jQuery(document).ready(function(){
		jQuery(".pagenav").each(function(){
			//console.log(jQuery(this).attr("href"));
			var str = jQuery(this).attr("href")+"";
			str=str.replace("Itemid","");
			str=str.replace("view","");
			//console.log(str);
			jQuery(this).attr("href",str);
		});
	});			
</script>