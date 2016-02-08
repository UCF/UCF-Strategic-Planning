// Define globals for JSHint validation:
/* global console */

// Theme Specific Code Here
// ...
var calendarWidget = function($) {
	if ($('.calendar-slider')) {
		//Helper functions
		var padNumber = function(n) {
			var retval = n.toString();
			if (retval.length == 1) {
				return '0' + retval;
			} else {
				return retval;
			}
		};

		var getRestUrl = function(id, slug, year, month) {
			var params = [
				Webcom.eventsProxy,
				id,
				slug,
				'small',
				year,
				month
			];

			return params.join('/');
		};

		var updateCalendar = function(url) {
			$.get(url, function(data) {
				$cal.replaceWith(data);
				$cal = $('.calendar-slider');
			});
		};

		var updateEvents = function(url) {
			$.get(url, function(data) {
				var $events = $eventSlider.find('.carousel-inner');
				$events.empty();
				for(var i in data) {
					var $template = $('#event-template').clone(),
							item = data[i];
					$template.find('a').attr('href', item.url);
					$template.find('h4').text(item.title);
					$template.find('a').append(item.description);
					$template.removeAttr('id');
					$template.removeClass('sr-only');
					if (i === '0') {
						$template.addClass('active');
					}
					$events.append($template);
				}
			});
		};

		var $cal         = $('.calendar-slider'),
				$eventSlider = $('#calendar_widget_slider'),
				id           = 1,
				slug         = $cal.attr('data-slug'),
				date         = new Date(),
				year         = date.getFullYear(),
				month        = padNumber(date.getMonth() + 1);

		$('#calendar_widget').on('click', '.pager a', function(e) {
			e.preventDefault();
			var $obj  = $(this),
					params = $obj.attr('data-ajax-link').split('/'),
					id     = params[4],
					slug   = params[5],
					year   = params[7],
					month  = padNumber(params[8]);

			var url = getRestUrl(id, slug, year, month);
			updateCalendar(url);
		});

		$('#calendar_widget').on('click', 'table a', function(e) {
			e.preventDefault();
			var $obj = $(this),
					url = $obj.attr('href') + 'feed.json';

			updateEvents(url);
		});

		var url = getRestUrl(id, slug, year, month);
		updateCalendar(url);
	}
};

var monthName = function(monthIndex) {
	var months = {
		0: '',
		1: 'January',
		2: 'February',
		3: 'March',
		4: 'April',
		5: 'May',
		6: 'June',
		7: 'July',
		8: 'August',
		9: 'September',
		10: 'October',
		11: 'November',
		12: 'December'
	};

	return months[monthIndex];
};

var daysInMonth= function(month, year) {
	return new Date(year, month, 0).getDate();
};

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready( function($) {
		calendarWidget($);
	});
}

