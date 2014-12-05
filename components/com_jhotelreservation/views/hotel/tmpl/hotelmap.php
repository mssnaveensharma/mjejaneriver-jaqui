<?php
$hotel =  $this->hotel;

?>
<div id="hotel_map">
</div>
<script>
function initialize() {
	  var myLatlng = new google.maps.LatLng(<?php echo $this->hotel->hotel_latitude?>, <?php echo $this->hotel->hotel_longitude?>);
	  var myOptions = {
		zoom: 12,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	  }

	 var mapdiv = document.getElementById("hotel_map");
	  mapdiv.style.width = '100%';
	  mapdiv.style.height = '400px';

	  var map = new google.maps.Map(mapdiv, myOptions);
	  var image = '<?php echo JURI::base() ."/components/".getBookingExtName()?>/assets/img/marker.jpg';
	  var marker = new google.maps.Marker({
	      position: new google.maps.LatLng(<?php echo $this->hotel->hotel_latitude?>, <?php echo $this->hotel->hotel_longitude?>),
	      map: map,
	      title: "<?php echo $hotel->hotel_name?>",
	      clickable: false,
	      icon: image
	  });
	  
	}
	  
	function loadScript() {
	  var script = document.createElement("script");
	  script.type = "text/javascript";
	  script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
	  document.body.appendChild(script);
	}
	  
	window.onload = loadScript;
</script>