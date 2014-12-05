 <script>
      function initialize() {
        var mapOptions = {
          zoom: 6,
          center: new google.maps.LatLng(52.414748, 6.447852),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

      var mapdiv = document.getElementById("hotels-map");
  	  mapdiv.style.width = '835px';
  	  mapdiv.style.height = '600px';
  	  
       var map = new google.maps.Map(mapdiv, mapOptions);
  		 
        setMarkers(map, hotels);
      }

      /**
       * Data for the markers consisting of a name, a LatLng and a zIndex for
       * the order in which these markers should display on top of each
       * other.
       */
      var hotels = [
        <?php 
        $db = JFactory::getDBO();
        foreach($hotels as $hotel){
        	    
        	$description = str_replace("\r\n","",$hotel->hotelDescription);
        	$description = str_replace("\\\'","",$description);
        	$description = addslashes($description);
        	
        	$contentString = '<div id="map-content">'.
        	'<h1 id="firstHeading" class="firstHeading">'.$db->escape($hotel->hotel_name).'</h1>'.
        	'<p>'.
        	'<img src="'.JURI::root().PATH_PICTURES.$hotel->hotel_picture_path.'" alt="'.$db->escape($hotel->hotel_name).'">'.
        	JHotelUtil::truncate(strip_tags($description), 180, ' &hellip; ', true).
        	'</p>'.
        	'<p><a href="'.$db->escape(JHotelUtil::getHotelLink($hotel)).'">'.JText::_('LNG_BOOK_HOTEL').'</a></p>'.
        	'</div>';
        	echo "['".$hotel->hotel_name."', '".$hotel->hotel_latitude."','".$hotel->hotel_longitude."', 4,'".$contentString."'],"."\n";
     	 } ?>
     	
      ];

      function setMarkers(map, locations) {
        // Add markers to the map

        // Marker sizes are expressed as a Size of X,Y
        // where the origin of the image (0,0) is located
        // in the top left of the image.

        // Origins, anchor positions and coordinates of the marker
        // increase in the X direction to the right and in
        // the Y direction down.
        
 
		var bounds = new google.maps.LatLngBounds();
        
         for (var i = 0; i < locations.length; i++) {
	          var hotel = locations[i];


	          var image = new google.maps.MarkerImage('<?php echo JURI::base() ."/components/com_jhotelreservation/assets/img/marker.jpg"?>',
	              // This marker is 20 pixels wide by 32 pixels tall.
	              new google.maps.Size(32, 32),
	              // The origin for this image is 0,0.
	              new google.maps.Point(0,0),
	              // The anchor for this image is the base of the flagpole at 0,32.
	              new google.maps.Point(0, 32));

		      var shape = {
	              coord: [1, 1, 1, 20, 18, 20, 18 , 1],
	              type: 'poly'
	          };
	          
	          var myLatLng = new google.maps.LatLng(hotel[1], hotel[2]);
	          var marker = new google.maps.Marker({
	              position: myLatLng,
	              map: map,
	              icon: image,
	              shape: shape,
	              title: hotel[0],
	              zIndex: hotel[3]
	          });
			 
	          var contentBody = hotel[4];
	          var infowindow = new google.maps.InfoWindow({
		          content: contentBody
		      });
	      	 

	          google.maps.event.addListener(marker, 'click', function(contentBody) {
	        	    return function(){
	        	        infowindow.setContent(contentBody);//set the content
	        	        infowindow.open(map,this);
	        	    }
	        	}(contentBody));
	        		        	
	          bounds.extend(myLatLng);
      	  }
         map.fitBounds(bounds);
    	  
      }

      function loadScript() {
          if(!initialized){
	    	  var script = document.createElement("script");
	    	  script.type = "text/javascript";
	    	  script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
	    	  document.body.appendChild(script);
	    	  initialized = true;
          }
    	  
    	}

  		var initialized = false;  
    	jQuery(document).ready(function(){
			jQuery("#show_hotels_map").click(function(){
				jQuery.blockUI({ message: jQuery('#hotel-map-container'), css: {
					top:  100 + 'px', 
		            left: (jQuery(window).width() - 850) /2 + 'px',
					width: '850px', 
					backgroundColor: '#fff' }});
				
					jQuery('.blockUI').click(function(){
						//jQuery.unblockUI();
					});
				loadScript();
			});
		});			
    </script>

<div id="hotel-map-container" class="popup map" style="display:none; position: relative">
	<div class="titleBar">
		<span class="popup-title"></span>
		<span  title="Cancel"  class="popup-close-button" onClick="jQuery.unblockUI();">
			<span title="Cancel" class="closeText">x</span>
		</span>
	</div>

	<div class="popup-content">
		<h3 class="title"> <?php echo JText::_('LNG_FIND_BEST_HOTEL_DEAL',true);?></h3>
		<div class="popup-content-body">
			<div id="hotels-map" style="position: relative;">	
			</div>
		</div>
	</div>  
</div>


