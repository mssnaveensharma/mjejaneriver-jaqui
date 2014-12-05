jQuery(document).ready(function(){
	generateChart(30);
	});	

function generateChart(daysLag){
	var inputParams ='&daysLag='+daysLag;
	var jsonurl = baseUrl+'&task=reservationsreports.getJsonReservationData'+inputParams;

	var labels = ["# Reservations"];

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
				if(data==""){
					jQuery('#chartdiv').text('No data found')
				}
				plot.title.text="Reservation Made("+daysLag+" days)";
			},
			error: function(xhr, ajaxOptions, thrownError) {
				jQuery('#chartdiv').text('Could not retrieve data'+thrownError);
			}
		});
		return ret;
		};

		var formatDate= "";
		var intervaOption= "";
		switch(daysLag)
		{
		case '7':
		  formatDate ='%d %b';
		  intervaOption = '1 week';
		  break;
		case '30':
		  formatDate ='%d %b';
		  intervaOption = '1 week';
		  break;
		case '90':
		case '180':
		case '365':
		  formatDate ='%b %Y';
		  intervaOption = '1 month';
		  break;
		case '730':
		case '1095':
		  intervaOption = '1 year';
		  formatDate ='%Y';
		  break; 
		default:
		  formatDate ='%d %b';
		  intervaOption = '1 week';
		  break;
		}

		var plot2 = jQuery.jqplot('chartdiv', jsonurl,{
	    	dataRenderer: ajaxDataRenderer,
	         grid: {
	             drawBorder: false,
	             shadow: false,
	             background: '#FFF'
	         },    series: [
	                        {
	                            color: 'rgba(204,255,255,.6)',
	                            negativeColor: 'rgba(190,50,50,.6)',
	                            showMarker: true,
	                            showLine: true,
	                            fill: true,
	                            fillAndStroke: true,
	                            markerOptions: {
	                                style: 'filledCircle',
	                                size: 8
	                            },
	                            rendererOptions: {
	                                smooth: true
	                            }
	                        },
	                        {
	                            color: 'rgba(144, 190, 160, 0.7)',
	                            showMarker: true,
	                            rendererOptions: {
	                                smooth: true,
	                            },
	                            markerOptions: {
	                                style: 'filledSquare',
	                                size: 8
	                            },
	                        }
	                    ],
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
			          renderer:jQuery.jqplot.DateAxisRenderer,
                      tickInterval:intervaOption,
			          tickOptions:{
			            formatString:formatDate,
			            showGridline: true
			          } 
			        },
			        yaxis:{
				          tickOptions:{
				            formatString:'%d',
				            showGridline: true

				            }
				        }
		        },
		        highlighter: {
		            show: true, 
		            showLabel: true, 
		            tooltipAxes: 'y',
		            tooltipLocation : 'ne'
		        }
	    	
	    	});

}