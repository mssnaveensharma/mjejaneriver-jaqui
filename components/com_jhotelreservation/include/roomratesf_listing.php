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
//$max_package_number = 0;

$userData =  $_SESSION['userData'];
?>
<script type="text/javascript">

jQuery(document).ready(function(){
	if(jQuery('.trigger').length > 0) 
	{
		jQuery('.trigger').click(function() 
		{
			if (jQuery(this).hasClass('open')) 
			{
				jQuery(this).removeClass('open');
				jQuery(this).addClass('close');
				jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideDown(100);
				jQuery(this).children('.room_expand').addClass('expanded');
				jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_LESS',true)?> »');
				return false;
			} else {
				jQuery(this).removeClass('close');
				jQuery(this).addClass('open');
				jQuery(this).parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(100);
				jQuery(this).children('.room_expand').removeClass('expanded');
				jQuery(this).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_MORE',true)?> »');
				return false;
			}			
		});

	jQuery('.show-availability').click(function() 
				{
					if (jQuery(this).hasClass('open')) 
					{
						jQuery(this).removeClass('open');
						jQuery(this).addClass('close');
						jQuery('.room-availabity').slideDown(100);
						jQuery(this).children('.show-text').html('&nbsp;<?php echo JText::_('LNG_HIDE',true)?>');
						return false;
					} else {
						jQuery(this).removeClass('close');
						jQuery(this).addClass('open');
						jQuery('.room-availabity').slideUp(100);
						jQuery(this).children('.show-text').html('&nbsp;<?php echo JText::_('LNG_SHOW',true)?>');
						return false;
					}			
				});	
		//IE fix
		//jQuery('.trigger').parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(100);

	}
	
	
		
	jQuery('a[name=modal]').click(function(e) {
		e.preventDefault();
		//Get the A tag
		var id = jQuery(this).attr('href');
	
		//Get the screen height and width
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
			
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(1000);	
		jQuery('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
		//Set the popup window to center
		// jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
		// jQuery(id).css('left', winW/2-jQuery(id).width()/2);
		jQuery(id).css('top',  f_scrollTop() + 20);
		jQuery(id).css('left', winW/2-jQuery(id).width()/2);
			
		//transition effect
		jQuery(id).fadeIn(2000); 
	
	});
	
	//if close button is clicked
	jQuery('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#mask').hide();
		jQuery('.window').hide();
	});		
	
	//if mask is clicked
	jQuery('#mask').click(function () {
		jQuery(this).hide();
		jQuery('.window').hide();
	});	
	
	function f_clientWidth() {
		return f_filterResults (
			window.innerWidth ? window.innerWidth : 0,
			document.documentElement ? document.documentElement.clientWidth : 0,
			document.body ? document.body.clientWidth : 0
		);
	}
	function f_clientHeight() {
		return f_filterResults (
			window.innerHeight ? window.innerHeight : 0,
			document.documentElement ? document.documentElement.clientHeight : 0,
			document.body ? document.body.clientHeight : 0
		);
	}
	function f_scrollLeft() {
		return f_filterResults (
			window.pageXOffset ? window.pageXOffset : 0,
			document.documentElement ? document.documentElement.scrollLeft : 0,
			document.body ? document.body.scrollLeft : 0
		);
	}


	function f_scrollTop() {
		return f_filterResults (
			window.pageYOffset ? window.pageYOffset : 0,
			document.documentElement ? document.documentElement.scrollTop : 0,
			document.body ? document.body.scrollTop : 0
		);
	}	
	function f_filterResults(n_win, n_docel, n_body) {
		var n_result = n_win ? n_win : 0;
		if (n_docel && (!n_result || (n_result > n_docel)))
			n_result = n_docel;
		return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
	}

	//jQuery( "div.tabs-container" ).tabs();

	showRoomCalendars();
	checkReservationPendingPayments();
	
	/* jQuery.blockUI({ message: '<img src="<?php echo JURI::base()."components/com_jhotelreservation/assets/img/loading.gif"?>" />' , overlayCSS: { opacity: .8 }, css: {top:  (jQuery(window).height() - 100) /2 + 'px', 
         left: (jQuery(window).width() - 100) /2 + 'px', width: '100px' , height: '100px' }} ); 
	*/
});


jQuery(window).load(function(){
	//jQuery.unblockUI();
});

</script>
<form autocomplete='off' action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userForm" id="userForm" >
	<div id="boxes" class="hotel_reservation">
		<div id='div_room'>
			<?php 
				if(count($this->offers) & $this->appSettings->is_enable_offers){
					require_once "offers.php";
				}
			?>
			<?php 
				if(count($this->rooms)){
					require_once "rooms_listing.php";
				}
			?>		
		</div>
	</div> 
	<div id="mask"></div>
	<input type="hidden" name="task" 							id="task"	 				value="hotel.reserveRoom" />
	<input type="hidden" name="hotel_id" 						id="hotel_id"	 			value="<?php echo $this->state->get("hotel.id")?>" />
	<input type="hidden" name="tmp"								id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type="hidden" name="tip_oper" 						id="tip_oper" 				value="<?php echo JRequest::getVar( 'tip_oper') ?>" />
	<input type="hidden" name="reserved_item"					id="reserved_item" 			value="" />
	<input type="hidden" name="current"   						id="current"				value="<?php echo count($this->userData->reservedItems) +1 ?>" />
	
	<script>		
		function editReserveRoom( offer_id, room_id, current )
		{
			if( !confirm( "<?php echo JText::_('LNG_QUERY_EDIT_RESERVED_ROOM',true) ?>"))
				return;
			
		}
		
		function deleteReserveRoom( offer_id, room_id, current ){
			
		}
	
		function bookItem(offerId, roomId){
			jQuery("#reserved_item").val(offerId+"|"+roomId);
			jQuery("#userForm").submit();
		}
		function setHotelValue(hotelId){
			jQuery("#userForm input[name='hotel_id']").val(hotelId);
		}

		
		function selectCalendarDate(startDate, endDate){
			jQuery('#jhotelreservation_datas2').val(startDate);
			jQuery('#jhotelreservation_datae2').val(endDate);
			if(typeof checkRoomRates === 'function')
				checkRoomRates('searchForm');
		}

		function showRoomCalendars(){
			var postParameters='';
			postParameters +="&hotel_id="+<?php echo $this->state->get("hotel.id") ?>;
			postParameters +="&current_room="+<?php echo count($this->userData->reservedItems) +1 ?>;
			postParameters +="&tip_oper=-1";
			<?php 
					foreach($this->userData->reservedItems as $itemReserved){
						echo 'postParameters +="&items_reserved[]='.$itemReserved.'";';
					}
				?>

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
		
		function showRoomCalendar(year,month, identifier){
			//alert("show");
			var postParameters='';
			postParameters +="&month="+month;
			postParameters +="&year="+year;
			postParameters +="&identifier="+identifier;
			postParameters +="&hotel_id="+<?php echo $this->state->get("hotel.id") ?>;
			postParameters +="&tip_oper=-1";
			postParameters +="&current_room="+<?php echo count($this->userData->reservedItems) +1 ?>;

			<?php 
				foreach($this->userData->reservedItems as $itemReserved){
					echo 'postParameters +="&items_reserved[]='.$itemReserved.'";';
				}
			?>
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
		
		//jQuery(document).ajaxComplete(function(e, x) {
		   // alert(x.getResponseHeader("Content-Type"));
		//});
	</script>
</form>
