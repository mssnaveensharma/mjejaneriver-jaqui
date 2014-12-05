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
$hotel = $this->hotel;
$hotelUrl = JURI::current(); 
//set metainfo

$config =JFactory::getConfig();
$document = JFactory::getDocument();

if( $this->state->get("hotel.tabId") != 4){
	$title = JText::_('LNG_METAINFO_HOTEL_TITLE');
	$description = JText::_('LNG_METAINFO_HOTEL_DESCRIPTION');
	$keywords = JText::_('LNG_METAINFO_HOTEL_KEYWORDS');
	
	$title =  str_replace("<<hotel>>", $hotel->hotel_name, $title);
	$title =  str_replace("<<city>>", $hotel->hotel_city, $title);
	$title =  str_replace("<<province>>", $hotel->hotel_county, $title);
	
	$description =  str_replace("<<hotel>>", $hotel->hotel_name, $description);
	$description =  str_replace("<<city>>", $hotel->hotel_city, $description);
	$description =  str_replace("<<province>>", $hotel->hotel_county, $description);
	$description =  str_replace("<<hotel-stars>>", $hotel->hotel_stars, $description);
	
	$keywords =  str_replace("<<hotel>>", $hotel->hotel_name, $keywords);
	$keywords =  str_replace("<<city>>", $hotel->hotel_city, $keywords);
	$keywords =  str_replace("<<province>>", $hotel->hotel_county, $keywords);
	
	
	$document->setTitle($title);
	$document->setDescription($description);
	$document->setMetaData('keywords', $keywords);
	$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
	$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');
	$document->addCustomTag('<meta property="og:image" content="'.(isset($hotel->pictures[0])?JURI::root().PATH_PICTURES.$hotel->pictures[0]->hotel_picture_path:'').'"/>');
	$document->addCustomTag('<meta property="og:type" content="website"/>');
	$document->addCustomTag('<meta property="og:url" content="'.$hotelUrl.'"/>');
	$document->addCustomTag('<meta property="og:site_name" content="'.$hotelUrl.'"/>');
}
$need_all_fields = true;
//if( strlen( $this->_models['variables']->tmp ) > 0 )
//	$need_all_fields = $this->_models['variables']->tmp > strtotime( " - 10 min ")? false : true; //allow only 10 min for admin
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

	<div class="hotel_reservation">
		<div id="hotel-presentation">
			<div class="hotel-content">
				<?php if(count($hotel->reviews) >= MINIMUM_HOTEL_REVIEWS & $this->appSettings->enable_hotel_rating==1){ ?>
					<div class="hotel-rating">
						<div class="info">
							<strong><?php  echo JText::_('LNG_CUSTOMER_REVIEW');?></strong>
							<a href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_REVIEWS")) ?>" ><?php echo count($hotel->reviews)?> <?php echo JText::_('LNG_REVIEWS')?></a>
						</div>
						<div class="rating">
							<?php echo JHotelUtil::fmt($hotel->hotel_rating_score,1)?>
						</div>
						<div class="clear"></div>					
					</div>
				<?php } ?>
			
			<div class="hotel-details">
				<?php if($hotel->recommended==1){?>
				<div class="hotel-recommanded">
					<span><?php  echo JText::_('LNG_RECOMMENDED');?></span>
				</div>
				<?php } ?>
			</div>	
			
				<div class="hotel-title">
					<h1>
						<?php echo stripslashes($this->hotel->hotel_name) ?> 
					</h1>
					<span class="hotel-stars">
						<?php
						for ($i=1;$i<= $this->hotel->hotel_stars;$i++){ ?>
							<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
						<?php } ?>
					</span>
				</div>
				
				<div class="right">
					<span class="button button-green">
						<button value="checkRates" name="checkRates" type="button" onclick="goBack()">
							<?php echo JText::_('LNG_BACK',true)?>
						</button>
					</span>
				</div>
				
				<div class="hotel-address">
					<?php echo $this->hotel->hotel_address?>, <?php echo $this->hotel->hotel_zipcode?$this->hotel->hotel_zipcode.", ":""?> <?php echo $this->hotel->hotel_city?>,
					 <?php echo $this->hotel->hotel_county?$this->hotel->hotel_county.", ":""?><?php echo $this->hotel->country_name?>
				</div>
				<div class="clear"></div>
				
				<!-- <div class="styled">
				
					<select name="user_currency" id="user_currency" onChange="checkRoomRates('searchForm')">
						<?php foreach ($this->currencies as $currency) {?>
							<option value="<?php echo $currency->currency_id ?>" <?php if($this->userData->user_currency==$currency->description) echo "selected"?>><?php echo $currency->description; if( $this->hotel->hotel_currency==$currency->description) echo " *" ?></option>
						<?php }?>	
					</select>
				</div> -->
				
				
				<div class="hotel-actions right">
					<ul>
						<li style="display:none"><a href="javascript:void(0)" onclick="showEmailDialog()"><strong><?php echo JText::_('LNG_EMAIL')?></strong><img src="<?php echo JURI::base()?>."components/".getBookingExtName()."/assets/img/email.png"/></a></li>
						
						<li>
							<div class="fb-like" data-href="<?php echo JHotelUtil::getHotelLink($this->hotel)?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
						</li>
					</ul>
				</div>
			
				<div class="clear"></div>
			</div>
			<?php 
			
			$map = JRequest::getVar(strtolower(JText::_("LNG_MAP")));
			$fotoGallery = JRequest::getVar(strtolower(JText::_("LNG_PHOTO")));
			$reviews = JRequest::getVar( strtolower(JText::_("LNG_REVIEWS")));
			$facilities = JRequest::getVar(strtolower(JText::_("LNG_FACILITIES")));
			
			$overview = !(isset($map)|| isset($fotoGallery) || isset($reviews) || isset($facilities));
			
			?>
			<?php if ($this->appSettings->enable_hotel_tabs==1) {?>	
			<div class="rel">
				<div class="tabs">
					<ul>
						<li class="<?php echo $overview?'selected':''?>">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel) ?>"><span><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARK_OVERVIEW'): JText::_("LNG_HOTEL_OVERVIEW")?></span></a>
						</li>
						<li class="<?php echo isset($map)?'selected':''?>">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_MAP")) ?>"><span><?php echo JText::_('LNG_MAP')?></span></a>
						</li>
						<li class="<?php echo isset($fotoGallery)?'selected':''?>">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_PHOTO")) ?>"><span><?php echo JText::_('LNG_PHOTO_GALLERY')?></span></a>
						</li>
						<li class="<?php echo isset($reviews)?'selected':''?>">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_REVIEWS")) ?>"><span><?php echo JText::_('LNG_REVIEWS')?></span></a>
						</li>
						<?php if($this->appSettings->enable_hotel_facilities==1){?>
							<li class="<?php echo isset($facilities)?'selected':''?>">
								<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_FACILITIES")) ?>"><span><?php echo  isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ? JText::_('LNG_PARK_FACILITIES'):JText::_('LNG_HOTEL_FACILITIES')?></span></a>
							</li>
						<?php }?>
					</ul>
				</div>
			</div>
			<?php }?>
			<div class="hotel_details_container">
				<?php 
					
					if(isset($map)){
						require_once 'hotelmap.php';
					} else if(isset($fotoGallery)){
						require_once 'hotelgallery.php';
					} else if(isset($reviews)){
						require_once 'hotelreviews.php';
					}else if(isset($facilities)){
						require_once 'hotelfacilities.php';
					}else{
						require_once 'hoteloverview.php';
					}
				?>
			</div>
		</div> 
	</div>
	
	<div id="share-hotel-email" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title">E-Mail</h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<span class="error" id="emailError" display="none"></span>
				<p>
					Send the hotel details to yourself or up to 5 friends.
				</p>
				<p>
					<span class="reqd">*Required field</span>
				</p>
				<form action="index.php" id="emailForm" name="emailForm" method="post">
					<div>
						<label for="email_to_address">To E-mail Address(es): <span class="reqd" title="Required Field">*</span></label>
						<input id="email_to_address" name="email_to_address" size="19" type="text">
						<span class="note">
							Separate addresses with a comma
						</span>
					</div>
					<div>
						<label for="email_from_name">Your Name: <span class="reqd" title="Required Field">*</span></label>
						<input id="email_from_name" name="email_from_name" size="19" type="text">
					</div>
					<div>
						<label for="email_from_address">Your E-mail Address: <span class="reqd" title="Required Field">*</span></label>
						<input id="email_from_address" name="email_from_address" size="19" type="text">
					</div>
					<div>
						<label for="email_note">Add a Personal Note:</label>
						<textarea cols="26" id="email_note" name="email_note" rows="4"></textarea>
						<div class="checkbox indent">
							<span id="emailnote_lengthmsg">
							Upto 250 characters.
							</span>
							<span id="emailnote_lengthinfo"></span>
							<br>
							<input name="copy_yourself" value="0" type="hidden">
							<input id="copy_yourself" name="copy_yourself" value="1" type="checkbox">
							<label for="copy_yourself">Send Yourself a Copy</label>
						</div>
					</div>
					
					<div class="cancelbutton">
						<input class="grey-button" id="email-cancel" value="Cancel" type="button" onclick="jQuery.unblockUI();">
					</div>
					<div class="sendbutton">
						<input class="grey-button" id="email-submit" onclick='sendMail()' value="Send" type="button">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="share-hotel-email-message" style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			<h3 class="title">E-Mail</h3>
			<div class="dialogContentBody" id="dialogContentBody">
				<p id="email-message">
					Send the hotel details to yourself or up to 5 friends.
				</p>
			</div>
		</div>
	</div>
</div>	
	
	<script>
	<?php if(JRequest::getVar('rm_id',0)>0){?>
		var roomId = "#room_<?php echo JRequest::getVar('rm_id',0)?> div";
		jQuery(document).ready(function(){
				setTimeout(openSelectedRoom, 500);
			});
	<?php }?>	

		function openSelectedRoom(){
			jQuery(roomId).removeClass('open');
			jQuery(roomId).addClass('close');
			jQuery(roomId).parent().parent('tr').next().children('.td_cnt').children('.cnt').slideDown(100);
			jQuery(roomId).children('.room_expand').addClass('expanded');
			jQuery(roomId).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_LESS',true)?> »');
			jQuery(roomId).focus();
			jQuery('html, body').animate({ scrollTop: jQuery(roomId).offset().top-40 }, 'slow');
			
			return false;
			}	
	
		function showEmailDialog(){
			jQuery.blockUI({ message: jQuery('#share-hotel-email'), css: {width: '600px'} }); 
			var form = document.emailForm;
			form.elements["email_to_address"].value='';
			form.elements["email_from_name"].value='';
			form.elements["email_from_address"].value='';
			form.elements["email_note"].value='';
			form.elements["copy_yourself"][1].checked=false;
			
		}

		function goBack(){
			var form 	= document.forms['userForm'];
			form.task.value	="hotels.searchHotels";
			form.submit();
		}
	
		function showTab(tabId){
			location = "<?php echo $hotelUrl ?>"+"?tabId="+tabId;
		}

		function sendMail(){
			
			jQuery("#emailError").hide();
			var form = document.emailForm;
			var postParameters='';
			postParameters +="&email_to_address=" + form.elements["email_to_address"].value;
			postParameters +="&email_from_name=" + form.elements["email_from_name"].value;
			postParameters +="&email_from_address=" + form.elements["email_from_address"].value;
			postParameters +="&email_note=" + form.elements["email_note"].value;
			postParameters +="&copy_yourself=" + form.elements["copy_yourself"][1].checked;
			var postData='&controller=email&task=sendEmail'+postParameters;

			jQuery.post(baseUrl, postData, sendMailResult);
		}


		function sendMailResult(responce){
			var xml = responce;
			alert(xml);
			//jQuery('#frmFacilitiesFormSubmitWait').hide();
			jQuery(xml).find('answer').each(function()
			{
				if(jQuery(this).attr('result')==true){
					jQuery("#email-message").html("<p><?php echo JText::_('LNG_EMAIL_SUCCESSFULLY_SENT') ?></p>");
					jQuery.unblockUI();
					jQuery.blockUI({ message: jQuery('#share-hotel-email-message'), css: {width: '600px'} }); 
					setTimeout(jQuery.unblockUI, 2500);
				}else{
					jQuery("#emailError").html(jQuery(this).attr('result'));
					jQuery("#emailError").show();
				}
			});
		}


		</script> 
	
	

