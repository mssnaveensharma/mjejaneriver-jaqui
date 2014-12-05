<?php

$hotel =  $this->hotel;
$hotelUrl = JURI::current().'?hotel_id='.$this->hotel->hotel_id.'&tip_oper=-1&task=showTab';

if( $this->state->get("hotel.tabId") == 4){
	$config =JFactory::getConfig();
	$document = JFactory::getDocument();
	
	$title = JText::_('LNG_HOTEL',true)." ".$hotel->hotel_name." ".JText::_('LNG_IN',true)." ".$hotel->hotel_city.", ".$hotel->hotel_county.", ".JText::_('LNG_READ_REVIEWS_AT',true)." ".$config->get( 'config.sitename' );
	$description = JText::_('LNG_READ_REVIEWS_OF_HOTEL',true)." ".$hotel->hotel_name." ".JText::_('LNG_IN',true)." ".$hotel->hotel_city.", ".$hotel->hotel_county.". ".JText::_("LNG_READ_WHAT_PEOPLE_SAID",true)." ".$hotel->hotel_name." ".JText::_('LNG_IN',true)." ".$hotel->hotel_city.", ".$hotel->hotel_county;
	
	$document->setTitle($title);
	$document->setDescription($description);
	$document->setMetaData('keywords', JText::_('LNG_HOTEL_REVIEW',true)." ".$hotel->hotel_name." ".$hotel->hotel_city.", ".$hotel->hotel_county." / ".JText::_('LNG_EXPERIENCES',true)." ".$hotel->hotel_name." ".JText::_("LNG_IN",true)." ".$hotel->hotel_city." / ".JText::_("LNG_WHAT_PEOPLE_FIND_OF",true)." ".$hotel->hotel_name." ".JText::_("LNG_IN",true)." ".$hotel->hotel_city."/ ".JText::_('LNG_CUSTOMER_REMARKS',true)." ".$hotel->hotel_name." ".JText::_('LNG_IN',true)." ".$hotel->hotel_city);
	
	$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
	$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');
	$document->addCustomTag('<meta property="og:image" content="'.JURI::base(). 'images' . DS .'icon-facebook.jpg" /> ');
	$document->addCustomTag('<meta property="og:type" content="website"/>');
	$document->addCustomTag('<meta property="og:url" content="'.$hotelUrl.'"/>');
	$document->addCustomTag('<meta property="og:site_name" content="'.$config->get( 'config.sitename' ).'"/>');
	$document->addCustomTag('<meta property="fb:admins" content="george.bara"/>');
	
}
?>
<div class="hotel-box hotel-item">
<h2><?php echo JText::_('LNG_REVIEWS_OF',true).' '.$this->hotel->hotel_name ?></h2>
	<div class="hotel-rating-info">
		<div class="rating-score left">
			<h4><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_REVIEW_TOTAL_SCORE_PARK',true) : JText::_('LNG_REVIEW_TOTAL_SCORE',true);?></h4>
				<div >
					<div class="rating_total_score_number">
						<strong><?php echo JHotelUtil::fmt($hotel->hotel_rating_score,1)?></strong>
					</div>
				</div>
				<p> <?php echo JText::_('LNG_REVIEW_BASED_ON',true);?> <a href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_REVIEWS")) ?>" ><?php echo count($hotel->reviews)?> <?php echo JText::_('LNG_REVIEW_NAMING',true);?></a></p>
				<p> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_1',true);?>  <strong> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_2',true);?> </strong> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_3',true);?> <i><?php echo $this->hotel->hotel_name?></i>.</p>

		</div>
		<div class="rating-criterias right">
			<h4><strong> <?php echo JText::_('LNG_SCORE_BREAKDOWN',true).' '.$this->hotel->hotel_name ?></strong> :</h4>
			<?php foreach($hotel->reviewAnwersScore as $answer){?>
			<div class="rating-criteria">
				<p class="rating-criteria-title">
					<?php echo $answer->question?>
				</p>
				<div class="rating-criteria-score">
					<?php echo JHotelUtil::fmt( $answer->average,1)?></font>
				</div>
				<div class="criteria-score">
					<div style="width: <?php echo JHotelUtil::fmt( $answer->average,1) * 10?>%" class="rating-bar">&nbsp;</div>
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?php $reviews = JRequest::getVar( strtolower(JText::_("LNG_REVIEWS"))); ?>
<?php if( isset($reviews)){?>
<div id="reviews-container">	
	<div class="blue-box">
		<div class="result-counter"><?php //echo $this->pagination->getResultsCounter()?></div>
	</div>
	<div class="hotel-reviews">
		<?php foreach($hotel->reviews as $review){?>
			<div class="hotel-review">	
				<div class="rating_total_score_number">
						<strong> <?php echo JHotelUtil::fmt( $review->average,1)?></strong>
					</div> 
				<div class="review-details">
					<div class="reviewer-name">
					 	<?php echo $review->last_name?>
					</div>
					<div class="reviewer-type">
						<?php echo $review->party_composition?>
					</div>
					<div class="reviewer-location">
						<span style="text-transform: capitalize;" class="city"><?php echo $review->city?></span>, 
						<span class="country"><?php echo $review->country?></span>
					</div>
					<span class="review-date"><?php echo  strftime("%B %d, %Y", strtotime($review->review_date)) ?></span>
				</div>
				<div class="review-container">
					<div class="review-comment">
						<div class="review-tile"><?php echo $review->review_short_description?></div>
						<div class="review-description"> <?php echo $review->review_remarks?></div>
						<div class="hotel-actions">
						<ul>
							<li> 
								<span><strong><?php echo JText::_('LNG_SHARE',true)?>:</strong></span> 
								<a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode($hotelUrl)?>&t=<?php echo  urlencode('tthis is the tile to share') ?>" title="<?php echo JText::_('LNG_SHARE_FACEBOOK',true) ?>"><img src="<?php echo JURI::base()?>media/com_jhotelreservation/images/fbshare.gif" /></a>
								<a target="_blank" href="http://twitter.com/home?status=<?php echo urlencode($hotelUrl) ?>" title="<?php echo JText::_('LNG_SHARE_TWITTER',true) ?>"><img src="<?php echo JURI::base()?>media/com_jhotelreservation/images/twittershare.png" /></a>
							 </li>
						</ul>
					</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php } ?>	
	</div>
	<div>
		<?php //echo $this->pagination->getListFooter(); ?>
	</div>
</div>
<?php } ?>