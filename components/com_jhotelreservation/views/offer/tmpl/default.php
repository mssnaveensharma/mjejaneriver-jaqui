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
$offerUrl = JURI::current().'?controller=offers&task=displayOffer&offerId='.$this->offer->offer_id;
$config =JFactory::getConfig();
$document = JFactory::getDocument();

$title = JText::_('LNG_METAINFO_OFFER_TITLE',true);
$description = JText::_('LNG_METAINFO_OFFER_DESCRIPTION',true);
$keywords = JText::_('LNG_METAINFO_OFFER_KEYWORDS',true);

$title =  str_replace("<<hotel>>", $this->offer->hotel_name, $title);
$title =  str_replace("<<city>>", $this->offer->hotel_city, $title);
$title =  str_replace("<<province>>", $this->offer->hotel_county, $title);

$description =  str_replace("<<hotel>>", $this->offer->hotel_name, $description);
$description =  str_replace("<<city>>", $this->offer->hotel_city, $description);
$description =  str_replace("<<province>>", $this->offer->hotel_county, $description);

$keywords =  str_replace("<<hotel>>", $this->offer->hotel_name, $keywords);
$keywords =  str_replace("<<city>>", $this->offer->hotel_city, $keywords);
$keywords =  str_replace("<<province>>", $this->offer->hotel_county, $keywords);

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

?>
<div id="offer-container" class="offer-container">

	<div class="right">
		<ul>
			<li> 
				<span><strong><?php echo JText::_('LNG_SHARE',true)?>:</strong></span> 
				<?php  ?>
				<a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode($offerUrl)?>&t=<?php echo  urlencode('tthis is the tile to share') ?>" title="<?php echo JText::_('LNG_SHARE_FACEBOOK',true) ?>"><img src="<?php echo JURI::base()?>media/com_jhotelreservation/images/fbshare.gif" /></a>
				<a target="_blank" href="http://twitter.com/home?status=<?php echo urlencode($offerUrl) ?>" title="<?php echo JText::_('LNG_SHARE_TWITTER',true) ?>"><img src="<?php echo JURI::base()?>media/com_jhotelreservation/images/twittershare.png" /></a>
			 </li>
		</ul>
	</div>
	<div class="offer-title">
		<h3> 
			<?php echo $this->offer->offer_name?>
		</h3>
	</div>
	 <span class="button button-green right">
		<button value="checkRates" name="checkRates" type="button" onclick="bookOffer()">
			<?php echo JText::_('LNG_BOOK_IT',true);?>
		</button>
	</span>
	<div class="hotel-name">
		<b><?php echo $this->offer->hotel_name?></b>
		<span class="hotel-stars">
			<?php
			for ($i=1;$i<= $this->offer->hotel_stars;$i++){ ?>
				<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
			<?php } ?>
		</span><br/>
		<span class="location">
			<span class="adr"> <span class="locality"> <?php echo $this->offer->hotel_city?> </span>, <span
				class="country-name"><?php echo $this->offer->country_name ?></span>
			</span>
		</span>
		<a href="javascript:void(0)" onclick="bookOffer();"><?php echo JText::_('LNG_VIEW_HOTEL',true);?></a>
	</div>
		
	<div class="offer-image-gallery">
		<?php
			$offerImageNames = array();
			foreach( $this->offer->pictures as $index=>$picture ){
				$offerImageNames[]= basename($picture->offer_picture_path);
				if($index>=3) break;
		?>
			<div class="image-prv-cnt left">
				<img class="image-prv" alt="<?php echo isset($picture->offer_picture_info)?$picture->offer_picture_info:'' ?>"
					src='<?php echo JURI::root().PATH_PICTURES.$picture->offer_picture_path?>' />
			</div>	
			
		<?php } ?>
		<?php
		$counter = count($this->offer->pictures);
		foreach( $this->offer->hotel->pictures as $index=>$picture ){
			if(in_array(basename($picture->hotel_picture_path), $offerImageNames))
				continue;
			if($counter>=3) break;
			$counter++;
	?>
		<div class="image-prv-cnt left">
			<img onclick="showTab(3)" class="image-prv" alt="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>"
				src='<?php echo JURI::root() .PATH_PICTURES.$picture->hotel_picture_path?>' />
		</div>	
		
	<?php } ?>
		
		<div class="clear"> </div>
	</div>
	
	<div class="sp_offers">
		<div class="offerDescription">
			<div>
				
				<div class="richTxt"><?php echo $this->offer->offer_description."<br/>"; ?> <?php echo $this->offer->offer_content?> <br/> <?php echo $this->offer->offer_other_info?></div>
				<div class="offer-date-interval splStayDate">
					<div>
						<b><?php echo JText::_('LNG_RESERVE_BY',true);?>:</b> <?php echo JHotelUtil::getDateGeneralFormat($this->offer->offer_dataef) ?>
					</div>
					<div class="offer-date-interval splStayDate">		
						<b><?php echo JText::_('LNG_STAY',true);?>:</b> <?php echo JHotelUtil::getDateGeneralFormat($this->offer->offer_datas) ?> - <?php echo JHotelUtil::getDateGeneralFormat($this->offer->offer_datae) ?>
					</div>
					<div class="offer-date-interval splStayDate">		
						<b><?php echo JText::_('LNG_STARTING_PRICE',true);?>:</b> <?php echo $this->offer->currency_symbol  ?> <?php echo JHotelUtil::fmt($this->offer->starting_price,2)?> p.p.
					</div>
					<div class="offer-date-interval splStayDate">		
						<b><?php echo JText::_('LNG_MINIMUM_NIGHTS',true);?>:</b> <?php echo $this->offer->offer_min_nights ?>
					</div>
					
				</div>
				<br/>
				 <span class="button button-green right">
					<button value="checkRates" name="checkRates" type="button" onclick="bookOffer()">
						<?php echo JText::_('LNG_BOOK_IT',true);?>
					</button>
				</span>
			</div>
		</div>
	</div>	
</div>

<script>
function bookOffer(){
 	document.location = "<?php echo JHotelUtil::getHotelLink($this->offer).'?minNights='.$this->offer->offer_min_nights.(!empty($this->mediaReferer)?'&mediaReferer='.$this->mediaReferer:'').(!empty($this->voucher)?'&voucher='.$this->voucher:'') ?>";
}

</script>
