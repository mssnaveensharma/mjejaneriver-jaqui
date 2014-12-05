<?php
/**
 * @copyright	Copyright (C) 2009-2012 CMSJunkie - All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');
$appSetings = JHotelUtil::getApplicationSettings();
jimport('joomla.html.pane');

?>
<div id="page-characteristics">
	<br style="font-size: 1px;" />
	<fieldset class="adminform">
		<legend>
			
		<?php echo JText::_( 'LNG_GENERAL_INFORMATION' ,true); ?></legend>
			<div style='display: none'>
				<div id='div_calendar' class='div_calendar'>
					<p>
					</p>
				</div>
			</div>
				<TABLE class="admintable" align=center border=0 width=100%>
					<TR>
						<TD nowrap class="key"><?php echo JText::_('LNG_NAME'); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_name"
							id="hotel_name" class="validate[required] text-input" value="<?php echo stripslashes($this->item->hotel_name)?>"
							size=50 maxlength=255  />
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_EMAIL'); ?>:</TD>
						<TD nowrap align=left><input type="text" name="email" id="email"
							value='<?php echo $this->item->email?>' size=50 maxlength=80
							 class='validate[required,custom[email]]' />
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TELEPHONE_NUMBER'); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_phone" id="hotel_phone"
							value='<?php echo $this->item->hotel_phone?>' size=50 maxlength=80
							 />
						</TD>
					</TR>
					<?php if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){ ?>
					
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_AVAILABLE',true); ?></TD>
						<TD nowrap align=left>
							<input 
								style		= 'float:none'
								type		= "radio"
								name		= "is_available"
								id			= "is_available"
								value		= '1'
								<?php echo $this->item->is_available==true? " checked " :""?>
								accesskey	= "Y"
								
							/>
							<?php echo JText::_('LNG_STR_YES',true); ?>
							&nbsp;
							<input 
								style		= 'float:none'
								type		= "radio"
								name		= "is_available"
								id			= "is_available"
								value		= '0'
								<?php echo $this->item->is_available==false? " checked " :""?>
								accesskey	= "N"
							/>
							<?php echo JText::_('LNG_STR_NO',true); ?>
						</TD>
						<TD nowrap>
							&nbsp;
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_STATUS',true); ?></TD>
						<TD nowrap align=left>
							<input 
								style		= 'float:none'
								type		= "radio"
								name		= "hotel_state"
								id			= "hotel_state_live"
								value		= '1'
								<?php echo $this->item->hotel_state==true? " checked " :""?>
								accesskey	= "Y"
								
							/>
							<?php echo JText::_('LNG_LIVE',true); ?>
							&nbsp;
							<input 
								style		= 'float:none'
								type		= "radio"
								name		= "hotel_state"
								id			= "hotel_state_edit"
								value		= '0'
								<?php echo $this->item->hotel_state==false? " checked " :""?>
								accesskey	= "N"
							/>
							<?php echo JText::_('LNG_EDIT',true); ?>
						</TD>
						<TD nowrap>
							&nbsp;
						</TD>
					</TR>
					<?php } ?>
					<TR>
						<TD width="10%" class="key"><?php echo JText::_('LNG_CURRENCY')?>
							:</TD>
						<TD align="left">
							<select id='currency_id' name='currency_id'  class="validate[required]">
									<option value='0' <?php echo $this->item->currency_id==0? "selected" : ""?>></option>
								<?php
								for($i = 0; $i <  count( $this->item->currencies ); $i++)
								{
									$currency = $this->item->currencies[$i]; 
								?>
								<option value = '<?php echo $currency->currency_id?>' <?php echo $currency->currency_id==$this->item->currency_id? "selected" : ""?>> <?php echo $currency->description?></option>
								<?php
								}
								?>
							</select> &nbsp; <?php echo JText::_('LNG_SELECT_A_CURRENCY_FOR_THE_PRICES_DISPLAYED_IN_THE_RESERVATION_PROCESS')?>
						</TD>
					</TR>
					<tr>
						<TD class="key"><?php echo JText::_('LNG_STARS',true); ?>:</TD>
						<TD nowrap align=left><select name="hotel_stars" id="hotel_stars">
						<?php
							for($i=0;$i<=7;$i++)
							{
						?>
							<option
								<?php echo $this->item->hotel_stars==$i? "selected" : ""?>
									value='<?php echo $i;?>'>
								<?php echo $i ?>
							</option>
						<?php
						}
						?>
					</select>
						</td>
					</tr>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_AVAILABILITY'); ?>:</TD>
						<TD align="left">
							<?php 
								$parms = array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10');
								if(!checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
									$parms = array("readonly"=>"readonly",'class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10');
								}
							?>					
		 					<div class="tdLabel"><?php echo JText::_('LNG_START_DATE',true)?>:</div> <?php echo JHTML::_('calendar', $this->item->start_date==$appSetings->defaultDateValue?'': $this->item->start_date, 'start_date', 'start_date', $appSetings->calendarFormat, $parms); ?>
		 					<a href	="javascript:void(0);" class="tooltip" title="<?php echo JText::_('LNG_HOTEL_START_DATE_INFO',true)?>">
		 						<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/help-icon-NLP.png"?>"/>
		 					</a>
							<div class="tdLabel"><?php echo JText::_('LNG_END_DATE',true)?>:</div>   <?php echo JHTML::_('calendar', $this->item->end_date==$appSetings->defaultDateValue?'': $this->item->end_date, 'end_date', 'end_date', $appSetings->calendarFormat, $parms); ?> 
							<a 
								href	="javascript:void(0);" 
								class	="tooltip" 
								title	="<?php echo JText::_('LNG_HOTEL_END_DATE_INFO',true)?>"
							>
								<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/help-icon-NLP.png"?>"
								/>
							</a>
										
							<span style="display:none"
								class='span_ignored_days'
								name='btn_ignored_days'
								id='btn_ignored_days[]'
								onclick="clickBtnIgnoreDays();"
							>
								<?php echo JText::_('LNG_IGNORED_DAYS',true); ?>
							</span>
							
							<div class="tdLabel" > <?php echo JText::_('LNG_AVAILABILITY_INFO',true); ?> </div>
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_UNAVAILABILITY',true); ?>:</TD>
						<td>
							<span> <?php echo JText::_('LNG_UNAVAILABILITY_INFO',true); ?> </span>
							<input 
									type='hidden' 
									name='ignored_dates' 
									id='ignored_dates'
									value='<?php echo $this->item->ignored_dates;?>'
								>
							<div class="dates_hotel_calendar" id="dates_hotel_calendar"></div>
							
						</td>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DESCRIPTION',true); ?>
							:</TD>
						<TD nowrap ALIGN=LEFT>
							<?php 
								$appSettings = JHotelUtil::getApplicationSettings();
								$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
								$dirs = JFolder::folders( $path );
								sort($dirs);
								$j=0;
				
							    echo JHtml::_('tabs.start', 'tab_language_id', $options);
																
								foreach( $dirs  as $_lng ){
									echo JHtml::_('tabs.panel',  $_lng, 'tab'.$j);
									$langContent = isset($this->translations[$_lng])?$this->translations[$_lng]:"";
									if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
										$editor =JFactory::getEditor();
										echo $editor->display('hotel_description_'.$_lng, $langContent, '800', '400', '70', '15', false);
									}else{
										echo "<textarea id='hotel_description_'.$_lng' name='hotel_description_$_lng' rows='10' style='width: 760px'>$langContent</textarea>";
									 } 
								}
								echo JHtml::_('tabs.end');
							?>
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_SELLING_POINTS',true); ?>	:</TD>
						<TD nowrap ALIGN=LEFT>
							<?php 
							
							
								if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
									$editor =JFactory::getEditor();
									echo $editor->display('hotel_selling_points', $this->item->hotel_selling_points, '800', '400', '70', '15', false);
								}else{
									echo "<textarea id='hotel_selling_points' name='hotel_selling_points' rows='10' style='width: 760px'>".$this->item->hotel_selling_points."</textarea>";
								}
							
							
							
							?>
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_COUNTRY',true); ?>
							:
						</TD>
						<TD nowrap align=left>
							<select id="country" name="country_id" class="validate[required]">
								<option
									<?php echo $this->item->country_id=='0'? "selected" : ""?>
									value='0'>
								</option>
						<?php
						foreach( $this->item->countries as $country )
						{
						?>
						<option <?php echo $this->item->country_id==$country->country_id? "selected" : ""?> 
							value='<?php echo $country->country_id?>'
						>
							<?php echo $country->country_name ?>
						</option>
						<?php
						}
						?>
					</select>
						</TD>
					</TR>
					
					<tr>
						<td class="key"><?php echo JText::_('LNG_AUTOCOMPLETE_ADDRESS')?></td>
						<td>
							<input size="80" type="text" id="autocomplete" class="input_txt" placeholder="Enter your address" onFocus="" ></input>
						</td>
					</tr>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_COUNTY',true); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_county" class="validate[required] text-input"
							id="administrative_area_level_1" value='<?php echo $this->item->hotel_county?>'
							size=40 maxlength=255  />
						</TD>

					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_CITY',true); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_city" class="validate[required] text-input"
							id="locality" value='<?php echo $this->item->hotel_city?>'
							size=40 maxlength=255  />
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_ADDRESS',true); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_address"
							id="route" class="validate[required] text-input"
							value='<?php echo $this->item->hotel_address?>' size=80
							maxlength=255  />
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_POSTAL_CODE',true); ?>:</TD>
						<TD nowrap align=left>
							<input type="text" name="hotel_zipcode" id="postal_code" class="text-input"
								value='<?php echo $this->item->hotel_zipcode?>' size=40 maxlength=255  />
						</TD>
					</TR>
					<TR>
						<TD width=10% nowrap class="key"><?php echo JText::_('LNG_WEBSITE',true); ?>:</TD>
						<TD nowrap align=left><input type="text" name="hotel_website"
							id="hotel_website"
							value='<?php echo $this->item->hotel_website?>' size=40
							maxlength=255  />
						</TD>
					</TR>
					<TR>
						<TD class="key"><?php echo JText::_('LNG_LOCATION',true); ?>:</TD>
						<TD nowrap align=left>
							<?php echo JText::_('LNG_LATITUDE',true); ?> <input type="text"
								name="hotel_latitude" id="latitude"
								value='<?php echo $this->item->hotel_latitude?>' size=30
								maxlength=255  />
							<?php echo JText::_('LNG_LONGITUDE',true); ?>
							<input 
								type		= "text"
								name		= "hotel_longitude"
								id			= "longitude"
								value		= '<?php echo $this->item->hotel_longitude?>'
								size		= 30
								maxlength	= 255
								
							/>
						</TD>
					</TR>
					<tr>
						<td class="key"></td>
						<td>
							<div id="map-container">
								<div id="company_map">
								</div>
							</div>
						</td>
					</tr>
					
				</TABLE>
	</fieldset>
</div>


<script>
var placeSearch, autocomplete;
var component_form = {
	'route': 'long_name',
	'locality': 'long_name',
	'administrative_area_level_1': 'long_name',
	'country': 'long_name',
	'postal_code': 'short_name'
};

function initializeAutocomplete() {
	  autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'), { types: [ 'geocode' ] });
	  google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    fillInAddress();
	  });
}

function fillInAddress() {
  var place = autocomplete.getPlace();

  for (var component in component_form) {
     // console.debug(component);
    document.getElementById(component).value = "";
    document.getElementById(component).disabled = false;
  }
  
  for (var j = 0; j < place.address_components.length; j++) {
    var att = place.address_components[j].types[0];
  
    if (component_form[att]) {
      var val = place.address_components[j][component_form[att]];
      //console.debug("#"+att);
      //console.debug(val);
      //console.debug(jQuery(att).val());
      jQuery("#"+att).val(val);
      if(att=='country'){
      	jQuery('#country option').filter(function () {
      		   return jQuery(this).text() === val;
      		}).attr('selected', true);
      }
    }
  }

  if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17); 
      addMarker(place.geometry.location);
    }
}
  
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
      autocomplete.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
    });
  }
}


var map;
var markers = [];


function initialize() {
	<?php 
		$latitude = isset($this->item->hotel_latitude) && strlen($this->item->hotel_latitude)>0?$this->item->hotel_latitude:"0";
		$longitude = isset($this->item->hotel_longitude) && strlen($this->item->hotel_longitude)>0?$this->item->hotel_longitude:"0";
	 ?>
	var companyLocation = new google.maps.LatLng(<?php echo $latitude ?>, <?php echo $longitude ?>);

	var mapOptions = {
	  zoom: <?php echo !(isset($this->item->hotel_latitude) && strlen($this->item->hotel_latitude))?1:15?>,
	  center: companyLocation,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	var mapdiv = document.getElementById("company_map");
	mapdiv.style.width = '530px';
	mapdiv.style.height = '400px';
	map = new google.maps.Map(mapdiv,  mapOptions);
	
	var latitude = '<?php echo  $this->item->hotel_latitude ?>';
	var longitude = '<?php echo  $this->item->hotel_longitude ?>';
	
	if(latitude && longitude)
	    addMarker(new google.maps.LatLng(latitude, longitude ));
			  
	google.maps.event.addListener(map, 'click', function(event) {
		 deleteOverlays();
	   addMarker(event.latLng);
	});

}

//Add a marker to the map and push to the array.
function addMarker(location) {
	document.getElementById("latitude").value = location.lat();
	document.getElementById("longitude").value = location.lng();
	
	marker = new google.maps.Marker({
	  position: location,
	  map: map
	});
	markers.push(marker);
}

//Sets the map on all markers in the array.
function setAllMap(map) {
	for (var i = 0; i < markers.length; i++) {
	  markers[i].setMap(map);
	}
}

//Removes the overlays from the map, but keeps them in the array.
function clearOverlays() {
	setAllMap(null);
}

//Shows any overlays currently in the array.
function showOverlays() {
	setAllMap(map);
}

//Deletes all markers in the array by removing references to them.
function deleteOverlays() {
	clearOverlays();
	markers = [];
}

function loadScript() {
	initialize();
}

jQuery(document).ready(function(){
	initializeAutocomplete();
	loadScript();

	jQuery(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });
});


</script>