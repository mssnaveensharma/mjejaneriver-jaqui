function showChangeDates(){
	jQuery.blockUI({ message: jQuery('#change-dates'), css: {
		top:  50 + 'px', 
        left: (jQuery(window).width() - 600) /2 + 'px',
		width: '600px', 
		backgroundColor: '#fff' }});
	jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI); 		
}

function changeDates(){
	jQuery("#start_date").val(jQuery("#start_date_i").val());
	jQuery("#end_date").val(jQuery("#end_date_i").val());
	jQuery("#update_price_type").val(jQuery("#change-dates input[type='radio']:checked").val());
	
	Joomla.submitbutton('reservation.apply');
}

function addRoom(){
	
	var postParameters ="&roomId="+jQuery("#rooms").val()
					+"&startDate="+jQuery("#start_date").val() 
					+"&endDate="+jQuery("#end_date").val()
					+"&current="+jQuery("#current").val()
					+"&adults="+jQuery("#adults").val()
					+"&children="+jQuery("#children").val()
					+"&discountCode="+jQuery("#discount_code").val()
					+"&hotelId="+jQuery("#hotelId").val(); 
	var postData='&task=reservation.addHotelRoom'+postParameters;
	jQuery.post(baseUrl, postData, processAddRoomResult);
}

function processAddRoomResult(responce){
	var xml = responce;
	jQuery("<div>" + xml + "</div>").find('answer').each(function()
	{
		
		jQuery("#reservation-rooms").append(jQuery(this).attr('content_records'));
		jQuery("#current").val(jQuery("#current").val()+1);
	});
}

function addOffer(){
	
}

function removeRoom(id){
	jQuery("#"+id).remove();
}

function validateForm(){
	if(jQuery(".roomrate").length==0){
		alert("Please add at least one room")
		return false;
	}
	return true;
}

function setCustomPrice(){
	jQuery("#update_price_type").val('2'); //set custom price to be considered
}