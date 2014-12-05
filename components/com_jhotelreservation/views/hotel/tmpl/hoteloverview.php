<?php
$hotel =  $this->hotel;

//create dates & default values
$startDate = $this->userData->start_date;
$endDate = $this->userData->end_date;
$startDate = JHotelUtil::convertToFormat($startDate);
$endDate = JHotelUtil::convertToFormat($endDate);
?>
							
		
<div class="hotel-image-gallery row-fluid">
	<div class="image-preview-cnt span5">
		<img onclick="showTab(3)" id="image-preview" alt="<?php if (isset($hotel->pictures[0])) echo isset($hotel->pictures[0]->hotel_picture_info)?$hotel->pictures[0]->hotel_picture_info:'' ?>" src='<?php if(isset($hotel->pictures[0])) echo JURI::root().PATH_PICTURES.$hotel->pictures[0]->hotel_picture_path?>' 
		/>
	</div>
	<div class="small-images span7">
	<?php
		foreach( $this->hotel->pictures as $index=>$picture ){
			if($index>=20) break;
	?>
		<div class="image-prv-cnt">
			<img onclick="showTab(3)" class="image-prv" alt="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>"
				src='<?php echo JURI::root() .PATH_PICTURES.$picture->hotel_picture_path?>' />
		</div>	
		
	<?php } ?>
	</div>
	
	<div class="clear"> </div>
	<div class="right">
		<a href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_PHOTO_GALLERY")) ?>" ><?php echo JText::_('LNG_VIEW_ALL_PHOTOS')?></a>
	</div>
</div>

<div class="clear"> </div>
<div class="reservation-details-holder row-fluid">
	<h3><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_SEARCH_PARKS_SPECIALS') : JText::_('LNG_SEARCH_ROOMS_SPECIALS')?>:</h3>
	<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel') ?>" method="post" name="searchForm" id="searchForm">
		<input type='hidden' name='resetSearch' value='true'>
		<input type='hidden' name='task' value='hotel.changeSearch'>
		<input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo $this->hotel->hotel_id ?>" />
		<input type='hidden' name='year_start' value=''>
		<input type='hidden' name='month_start' value=''>
		<input type='hidden' name='day_start' value=''>
		<input type='hidden' name='year_end' value=''>
		<input type='hidden' name='month_end' value=''>
		<input type='hidden' name='day_end' value=''>
		<input type='hidden' name='rooms' value=''>
		<input type='hidden' name='guest_adult' value=''>
		<input type='hidden' name='guest_child' value=''>
		<input type='hidden' name='user_currency' value=''>
		
		<?php 
			if(isset($this->userData->roomGuests )){
				foreach($this->userData->roomGuests as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests[]' value='<?php echo $guestPerRoom?>'/>
				<?php }
			}
			if(isset($this->userData->roomGuestsChildren )){
				foreach($this->userData->roomGuestsChildren as $guestPerRoomC){?>
						<input class="room-search" type="hidden" name='room-guests-children[]' value='<?php echo $guestPerRoomC?>'/>
					<?php }
				}
			$hideCalendars = false;	
			if(isset($this->userData->excursions ) && is_array($this->userData->excursions) && count($this->userData->excursions)>0){
				$hideCalendars = true;
				foreach($this->userData->excursions as $excursion){?>
					<input class="excursions" type="hidden" name='excursions[]' value='<?php echo $excursion;?>' />
				<?php }
				}
		?>
		<div class="reservation-details span12" >
			<div class="reservation-detail span2">
				<label for=""><?php echo JText::_('LNG_ARIVAL')?></label>
				<?php

					 if (!$hideCalendars) {
										echo JHTML::calendar(
																$startDate,'jhotelreservation_datas','jhotelreservation_datas2',$this->appSettings->calendarFormat, 
																array(
																		'class'		=>'date_hotelreservation', 
																		'minDate'		=>'0',
																		'onchange'	=>
																					"
																						if(!checkStartDate(this.value, defaultStartDate,defaultEndDate))
																							return false;
																						setDepartureDate('jhotelreservation_datae2',this.value);
																					",
																	)
															);
					}
					else 
						{
							echo '<input  class="date_hotelreservation hasTooltip" type="text" disabled="true" value="'.$startDate.'">';  
							echo '<input id="jhotelreservation_datas2" class="date_hotelreservation hasTooltip" type="hidden" name="jhotelreservation_datas" value="'.$startDate.'">';
						}
									?>
					
				
			</div>
			
			<div class="reservation-detail span2">
				<label for=""><?php echo JText::_('LNG_DEPARTURE')?></label>
				<?php
					if (!$hideCalendars) {
						echo JHTML::calendar($endDate,'jhotelreservation_datae','jhotelreservation_datae2', $this->appSettings->calendarFormat, array('class'=>'date_hotelreservation','onchange'	=>'checkEndDate(this.value,defaultStartDate,defaultEndDate);'));
					}
					else{
						echo '<input class="date_hotelreservation hasTooltip" type="text" disabled="true" value="'.$endDate.'">';
						echo '<input id="jhotelreservation_datae2" class="date_hotelreservation hasTooltip" type="hidden"  name="jhotelreservation_datae" value="'.$endDate.'">';
					}
						
				?>
				
			</div>
			
			
			<div class="reservation-detail span1">
				<label for=""><a id="" href="javascript:void(0);" onclick="showExpandedSearch()"><?php echo JText::_('LNG_ROOMS')?></a></label>
				<select id='jhotelreservation_rooms2' name='jhotelreservation_rooms' style="margin-left:5px;" class = 'select_hotelreservation'
				>
					<?php
					$jhotelreservation_rooms = $this->userData->rooms;
					
					$i_min = 1;
					$i_max = 5;
					
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
				
			</div>
			<div class="reservation-detail span1">
				<label for=""><?php echo JText::_('LNG_ADULTS_19')?></label>
				<select name='jhotelreservation_guest_adult' id='jhotelreservation_guest_adult'
					class		= 'select_hotelreservation'
				>
					<?php
					$i_min = 1;
					$i_max = 12;
					
					$jhotelreservation_adults = $this->userData->total_adults;
					
					for($i=$i_min; $i<=$i_max; $i++)
					{
					?>
					<option value='<?php echo $i?>'  <?php echo $jhotelreservation_adults==$i ? " selected " : ""?>><?php echo $i?></option>
					<?php
					}
					?>
				</select>
			</div>
			
			<div class="reservation-detail span1" style="<?php echo $this->appSettings->show_children!=0 ? "":"display:none" ?>">
				<label for=""><?php echo JText::_('LNG_CHILDREN_0_18')?></label>
				<select name='jhotelreservation_guest_child' id='jhotelreservation_guest_child'
					class		= 'select_hotelreservation'
				>
					<?php
					$i_min = 0;
					$i_max = 10;
					$jhotelreservation_children = $this->userData->total_children;
						
					for($i=$i_min; $i<=$i_max; $i++)
					{
					?>
					<option <?php echo $jhotelreservation_children==$i ? " selected " : ""?> value='<?php echo $i?>'  ><?php echo $i?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="reservation-detail voucher span2">
				<label for=""><?php echo JText::_('LNG_VOUCHER')?></label>
				<input type="text" value="<?php echo $this->userData->voucher ?>" name="voucher" id="voucher"/>
			</div>
			<div class="reservation-detail span1" style="margin-left: 5px;">
				<span class="button button-green">
					<button	onClick		=	"checkRoomRates('searchForm');"
						type="button" name="checkRates" value="checkRates"><?php echo JText::_('LNG_CHECK')?></button>
				</span>
			</div>
		</div>
	</form>
</div>
<?php require_once 'roomrates.php'; ?>

<?php if($this->appSettings->enable_hotel_description==1){?>
<div class="hotel-description hotel-item">
	<h2><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ? JText::_('LNG_PARK_DESCRIPTION'): JText::_('LNG_HOTEL_DESCRIPTION')?>  <?php echo $this->hotel->hotel_name; ?></h2>
	<?php  
		$hotelDescription = $this->hotel->hotelDescription;
		echo $hotelDescription;
	?>
</div>
<?php }?>
<?php if($this->appSettings->enable_hotel_facilities==1){?>
<div class="hotel-facilities hotel-item">
	
	<h2><?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARK_FACILITIES') : JText::_('LNG_HOTEL_FACILITIES')?> <?php echo $this->hotel->hotel_name; ?></h2>
	<ul class="blue">
		<?php 
		foreach($this->hotel->facilities as $facility)	{
		?>
			<li><?php echo $facility->name?></li>			
		<?php } ?>
	</ul>
</div>
<?php }?>

<?php
 if(count($hotel->reviews) >= MINIMUM_HOTEL_REVIEWS & $this->appSettings->enable_hotel_rating==1){ 
	 require_once 'hotelreviews.php'; 
 }
 ?>
 
 
<?php if($this->appSettings->enable_hotel_information==1) require_once 'informations.php'; ?>

<script>

	var dateFormat = "<?php echo  $this->appSettings->dateFormat; ?>";
	var message = "<?php echo JText::_('LNG_ERROR_PERIOD',true)?>";
	var defaultEndDate = "<?php echo isset($module)?$module->params["start-date"]: ''?>";
	var defaultStartDate = "<?php echo isset($module)?$module->params["end-date"]: ''?>";
	
	// starting the script on page load
	jQuery(document).ready(function(){

		jQuery("img.image-prv").hover(function(e){
			jQuery("#image-preview").attr('src', this.src);	
		});

	
	});		
</script>
	
<?php 
	require_once JPATH_SITE.'/components/com_jhotelreservation/include/multipleroomselection.php';
?> 
