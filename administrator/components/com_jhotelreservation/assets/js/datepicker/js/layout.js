(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = jQuery('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = jQuery('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		jQuery('#date').DatePicker({
			flat: true,
			date: '2008-07-31',
			current: '2008-07-31',
			calendars: 1,
			starts: 1,
			view: 'years'
		});
		var now = new Date();
		now.addDays(-10);
		var now2 = new Date();
		now2.addDays(-5);
		now2.setHours(0,0,0,0);
		jQuery('#date2').DatePicker({
			flat: true,
			date: ['2008-07-31', '2008-07-28'],
			current: '2008-07-31',
			format: 'Y-m-d',
			calendars: 1,
			mode: 'multiple',
			onRender: function(date) {
				return {
					disabled: (date.valueOf() < now.valueOf()),
					className: date.valueOf() == now2.valueOf() ? 'datepickerSpecial' : false
				}
			},
			onChange: function(formated, dates) {
			},
			starts: 0
		});
		jQuery('#clearSelection').bind('click', function(){
			jQuery('#date3').DatePickerClear();
			return false;
		});
		jQuery('#date3').DatePicker({
			flat: true,
			date: ['2009-12-28','2010-01-23'],
			current: '2010-01-01',
			calendars: 3,
			mode: 'range',
			starts: 1
		});
		jQuery('.inputDate').DatePicker({
			format:'m/d/Y',
			date: jQuery('#inputDate').val(),
			current: jQuery('#inputDate').val(),
			starts: 1,
			position: 'right',
			onBeforeShow: function(){
				jQuery('#inputDate').DatePickerSetDate(jQuery('#inputDate').val(), true);
			},
			onChange: function(formated, dates){
				jQuery('#inputDate').val(formated);
				if (jQuery('#closeOnSelect input').attr('checked')) {
					jQuery('#inputDate').DatePickerHide();
				}
			}
		});
		var now3 = new Date();
		now3.addDays(-4);
		var now4 = new Date()
		jQuery('#widgetCalendar').DatePicker({
			flat: true,
			format: 'd B, Y',
			date: [new Date(now3), new Date(now4)],
			calendars: 3,
			mode: 'range',
			starts: 1,
			onChange: function(formated) {
				jQuery('#widgetField span').get(0).innerHTML = formated.join(' &divide; ');
			}
		});
		var state = false;
		jQuery('#widgetField>a').bind('click', function(){
			jQuery('#widgetCalendar').stop().animate({height: state ? 0 : jQuery('#widgetCalendar div.datepicker').get(0).offsetHeight}, 500);
			state = !state;
			return false;
		});
		jQuery('#widgetCalendar div.datepicker').css('position', 'absolute');
	};
	
	var showTab = function(e) {
		var tabIndex = jQuery('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		jQuery(this)
			.addClass('active')
			.blur();
		jQuery('div.tab')
			.hide()
				.eq(tabIndex)
				.show();
	};
	
	//EYE.register(initLayout, 'init');
})(jQuery)