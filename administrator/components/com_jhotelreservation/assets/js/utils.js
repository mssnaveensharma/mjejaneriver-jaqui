function validateField( objField, type, accept_empty, msg )
{
	if( accept_empty == null )
		accept_empty = true;
	if( msg == null )
		msg = '';
	if( objField == null )
		return false;
	if( accept_empty == true && objField.value == '')
		return true;
	
	var ret = true;

	if( type == 'numeric' )
	{
		ret = isNumeric(objField.value);
	}
	else if( type == 'string' )
	{
		ret = objField.value == ''? false : true ;
	}
	else if( type =='email')
	{
		var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
		ret = filter.test(objField.value); 
	}
	else if( type =='date')
	{
		return checkDate(objField);
	}
	else if( type == 'radio' || type == 'checkbox' )
	{
		if( objField.length == null )
		{
			
			ret = objField.checked ? true : false  ;
		}
		else
		{
			
			var nLen  	= objField.length;
			var nSel	= false;
			ret 		= false;
			
			for( i = 0; i < nLen; i ++ )
			{
				if( objField[i].checked)
				{
					ret = true;
					break;
				}
			}
		}
		
	}
	
	if( ret == false && msg != '' )
	{
		alert(msg);
		if( objField.focus )
			objField.focus();
	}
	//myRegExpPhoneNumber = /(\d\d\d) \d\d\d-\d\d\d\d/
	return ret;
	
}

function isNumeric(str)
{
	return parseFloat(str)==str;

	//mystring = str;
	//alert(str);
	//if (mystring.match(/^\d+$|^\d+\.\d{2}$/ ) ) 
//	{
	//	return true;
	//}
	//return false;

}

function classOf(o) 
{
	if (undefined === o) 
		return "Undefined";
	if (null === o) 
		return "Null";
	return {}.toString.call(o).slice(8, -1);
}

function isArray(obj) 
{
	//alert(obj.constructor);
	//returns true is it is an array
	if (obj.constructor = Array )
		return false;
	else
		return true;
}


Date.prototype.getMonthName = function() 
{
	var m = ['January','February','March','April','May','June','July','August','September','October','November','December'];
	return m[this.getMonth()];
} 
Date.prototype.getDayName = function() 
{
	var d = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	return d[this.getDay()];
}
function daysInMonth(year, month) 
{
	//alert(month + " " + year);
	var dd = new Date(year, month, 0);
	return dd.getDate();

} 

function checkDate(field) 
{ 
	var allowBlank 	= !true; 
	var minYear 	= 1902; 
	var maxYear 	= (new Date()).getFullYear(); 
	var errorMsg 	= ""; 
	
	// regular expression to match required date format 
	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/; 
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/; 
	re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/; 
	
	if(field.value != '') 
	{ 
		
		if(regs = field.value.match(re)) 
		{ 
			if(regs[3] < 1 || regs[3] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[2] < 1 || regs[2] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[2]; 
			} 
			else if(regs[1] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}
		else if (regs= field.value.match(re2))
		{ 
			if(regs[1] < 1 || regs[1] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[2] < 1 || regs[2] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[2]; 
			} 
			else if(regs[3] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}
		else if (regs= field.value.match(re3))
		{ 
			if(regs[1] < 1 || regs[1] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[0] < 1 || regs[0] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[0]; 
			} 
			else if(regs[2] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}	
		else{
			errorMsg = "Invalid date format: " + field.value; 
		} 
	} 
	else if(!allowBlank) 
	{ 
		errorMsg = "Empty date not allowed!"; 
	} 
	
	if(errorMsg != "") 
	{ 
		alert(errorMsg);return false; 
	} 
	return true; 
}


function compareDate(field1, field2,msg) 
{ 
	var ret = false;
	// regular expression to match required date format 
	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/; 
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
	re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/; 

	if(field1.value != '' && field2.value != '') 
	{ 
		if(regs1 = field1.value.match(re)){
			regs1 = field1.value.split('-');
			regs2 = field2.value.split('-');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[0],regs1[1]-1,regs1[2]);
				date2 = new Date(regs2[0],regs2[1]-1,regs2[2]);
				ret = date1.getTime() < date2.getTime();
			}
		}else if(regs1 = field1.value.match(re2)){
			regs1 = field1.value.split('-');
			regs2 = field2.value.split('-');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[2],regs1[1]-1,regs1[0]);
				date2 = new Date(regs2[2],regs2[1]-1,regs2[0]);
				ret = date1.getTime() < date2.getTime();
			}
		} 
		else if(regs1 = field1.value.match(re3)){
			regs1 = field1.value.split('/');
			regs2 = field2.value.split('/');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[2],regs1[0]-1,regs1[1]);
				date2 = new Date(regs2[2],regs2[0]-1,regs2[1]);
				ret = date1.getTime() < date2.getTime();
			}
		} 
	} 
	
	if( ret == false && msg != '' )
	{
		alert(msg);
		if( field1.focus )
			field1.focus();
	}
	
	return ret;

}


function deleteReservedItems()
{
	//remove from all elements
	var arrFields 		= new Array();
	arrFields[ 0] 		= 'items_reserved';
	arrFields[ 1] 		= 'package_ids';
	arrFields[ 2] 		= 'package_day';
	arrFields[ 3] 		= 'itemPackageNumbers';
	arrFields[ 4] 		= 'arrival_option_ids';
	arrFields[ 5] 		= 'airport_airline_ids';
	arrFields[ 6] 		= 'airport_transfer_type_ids';
	arrFields[ 7] 		= 'airport_transfer_dates';
	arrFields[ 8] 		= 'airport_transfer_time_hours';
	arrFields[ 9] 		= 'airport_transfer_time_mins';
	arrFields[10] 		= 'airport_transfer_flight_nrs';
	arrFields[11] 		= 'airport_transfer_guests';

	
	for( i = 0; i < arrFields.length; i ++ )
	{
		var crt = 1;
		jQuery("input[name=\""+arrFields[i]+"[]\"]").each(function()
		{
		
			jQuery(this).remove();
		
		});
	}
	
}
