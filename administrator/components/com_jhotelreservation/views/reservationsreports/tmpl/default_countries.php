<?php
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
?>

<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_RESERVATIONS_BY_COUNTRY_REPORT',true); ?></legend>
			<div style='text-align:left'>
				<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
				<select name='hotel_id' id='hotel_id' style='width:300px'
					onchange ='
								var form 	= document.adminForm; 
								form.task.value = "reservationsreports.countryReservationReport";
								form.submit();
								'
				>
					<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
					<?php
					foreach($this->hotels as $hotel )
					{
					?>
					<option value='<?php echo $hotel->hotel_id?>' 
						<?php echo $this->hotel_id ==$hotel->hotel_id||(count($this->hotels)==1)? 'selected' : ''?>
					>
						<?php 
							echo $hotel->hotel_name;
							echo (strlen($hotel->country_name)>0? ", ".$hotel->country_name : "");
							echo (strlen($hotel->hotel_city)>0? ", ".$hotel->hotel_city : "");
						?>
					</option>
					<?php
					}
					?>
				</select>
				<hr>
			</div>
			<?php
			if( $this->hotel_id > 0 )
			{
			?>
			<table width="auto" border=0 align="right">
				<tr>
								
					<TD><?php echo JText::_('LNG_PERIOD',true)?></TD>
					<TD >
						<?php echo JHTML::_('calendar', $this->filter_datas, 'filter_datas', 'filter_datas', $this->appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					</TD>	
					<TD>
						<?php echo JHTML::_('calendar', $this->filter_datae, 'filter_datae', 'filter_datae', $this->appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					</TD>	
					<TD width=10% nowrap><?php echo JText::_('LNG_ROOM_TYPE',true)?> :</TD>
					<TD>
						<select name='filter_room_types' id='filter_room_types' style='width:220px' onChange="generateChart()">
							<option value='0' <?php echo $this->filter_room_types==0? " selected " : ""?> ><?php echo JText::_('LNG_ALL_ROOMS',true)?></option>
						<?php
						
						for($i = 0; $i <  count( $this->itemsRoomTypes ); $i++)
						{
							$room = $this->itemsRoomTypes[$i]; 
							?>
							<option value='<?php echo $room->room_id?>' <?php echo $this->filter_room_types==$room->room_id? " selected " : ""?>>
								<?php echo $room->room_name?>
							</option>
							<?php
						}
						?>
						</select>
					</TD>
					<!-- <TD nowrap><?php echo JText::_('LNG_REPORT_BY',true)?>:</TD>
					
					<TD >
						<select name='filter_report_type' id='filter_report_type' style='width:220px' onChange="generateChart()">
							<option value='DAY' <?php echo $this->filter_report_type=="DAY"? " selected " : ""?> ><?php echo JText::_('LNG_REPORT_DAY',true)?></option>
							<option value='WEEK' <?php echo $this->filter_report_type=="WEEK"? " selected " : ""?> ><?php echo JText::_('LNG_REPORT_WEEK',true)?></option>
							<option value='MONTH' <?php echo $this->filter_report_type=="MONTH"? " selected " : ""?> ><?php echo JText::_('LNG_REPORT_MONTH',true)?></option>
							<option value='YEAR' <?php echo $this->filter_report_type=="YEAR"? " selected " : ""?> ><?php echo JText::_('LNG_REPORT_YEAR',true)?></option>
						</select>
					</TD>	 -->

					<TD colspan=2 align="right">
						<input 
							type		='button'
							value		='<?php echo JText::_('LNG_GENERATE',true)?>'
							style		='width:120px;height:30px;font-size:12px'
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"
							onClick = "generateChart(this.value)"	
						>
					</TD>
				</TR>
			</table>
			<?php
			}
			?>
		</fieldset>
		
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="tip" value="" />
	<input type="hidden" name="task" value="reservationsreports.countryReservationReport" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>


<div id="chartdiv" style="height:400px;width:1200px;margin:0 auto;"></div>


<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton) 

	{
		var form = document.adminForm;
		form.task.value='';
		submitform( pressbutton );
	}
		
	$(document).ready(function(){
		var hotelId=jQuery('#hotel_id').val();
		var refreshScreen=jQuery('#refreshScreen').val();
		var nrHotels = jQuery('#hotel_id option').length;
		if(hotelId>0 && refreshScreen=="" && parseInt(nrHotels)==2){
			jQuery('#refreshScreen').val("true");
			jQuery("#hotel_id").trigger('change');	
		}
		<?php if( $this->hotel_id > 0 )	{ ?>	
		 	generateChart();
		<?php }?>
	});	
	function showLoading(){
		var imgLoading = "	<div style='text-align:center;padding-top:40px;width:100%'><img src='<?php echo JURI::base()."/components/".getBookingExtName();?>/assets/img/loader.gif'></div>";
		jQuery('#chartdiv').html(imgLoading);
	}
	function generateChart(){
	showLoading();
	var siteRoot = '<?php echo JURI::root();?>';
	var compName = '<?php echo getBookingExtName();?>';
	var currencyCode = '<?php echo JHotelUtil::getApplicationSettings()->currency_id;?>';

	var dateStart = jQuery('#filter_datas').val();
	var dateEnd = jQuery('#filter_datae').val();
	var roomTypeId = 	jQuery('#filter_room_types').val();
	var hotelId = jQuery('#hotel_id').val();
	var reportType =  jQuery('#filter_report_type').val();

	var inputParams ='&dateStart='+dateStart+'&dateEnd='+dateEnd+'&roomTypeId='+roomTypeId+'&hotelId='+hotelId+'&reportType='+reportType;
	var jsonurl = siteRoot+'administrator/index.php?option='+compName+'&task=reservationsreports.getJsonCountriesData'+inputParams;

	 var labels = ["<?php echo JText::_('LNG_RESERVATIONS',true)?>"];
	
	var ajaxDataRenderer = function(url, plot, options) {
	var ret = null;
	jQuery.ajax({
		// have to use synchronous here, else the function 
		// will return before the data is fetched
		async: false,
		url: url,
		data: options,
		dataType:"json",
		success: function(data) {
			ret = data;
			jQuery('#chartdiv').empty();
		},
		error: function(xhr, ajaxOptions, thrownError) {
             alert("ERROR: " + xhr.statusText +" " + ajaxOptions+" " +thrownError);

		}
	});
	return ret;
	};
	
	// passing in the url string as the jqPlot data argument is a handy
	// shortcut for our renderer.� You could also have used the
	// "dataRendererOptions" option to pass in the url.
	var plot2 = jQuery.jqplot('chartdiv', jsonurl,{
    	title: '<?php echo JText::_('LNG_RESERVATIONS_BY_COUNTRY_REPORT',true)?>',
    	dataRenderer: ajaxDataRenderer,
    	 legend: {
    		 show:true,
    		 title:'Test',
             background: 'white',
             textColor: 'black',
             fontFamily: 'Times New Roman',
             border: '1px solid black',
             placement: 'outsideGrid',
             labels: labels
         },
         seriesDefaults:{
             renderer:jQuery.jqplot.BarRenderer,
             pointLabels: { show: true }
         },
	        // Turns on animatino for all series in this plot.
	        animate: true,
	        // Will animate plot on calls to plot1.replot({resetAxes:true})
	        animateReplot: true,
	        cursor: {
	            show: false,
	        },
        
    	dataRendererOptions: {
    		unusedOptionalUrl: jsonurl
    	},
    	axes: {
	            // These options will set up the x axis like a category axis.
	           xaxis:{
		          renderer:jQuery.jqplot.CategoryAxisRenderer,
		        },
		        yaxis:{
			          tickOptions:{
			            formatString:'%.2f'
			            }
			        }
	        }
    	});

}
</script>