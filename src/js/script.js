// Define globals for JSHint validation:
/* global console */

// Theme Specific Code Here
// ...
var headerImage = function($) {
	var resize = function() {
		var $image = $('.header-image'),
			width = $image.width(),
			height = (width * 0.375).clamp(400, 750);

		$image.height(height + 'px');
	};

	resize();

	$(window).on('resize', function() {
		resize();
	});
};

Number.prototype.clamp = function(min, max) {
	return Math.min(Math.max(this, min), max);
};

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

		var stripTags = function( str ) {
			var tmp = document.createElement('div');
			tmp.innerHTML = str;
			return tmp.textContent || tmp.innerText;
		};

		var shortenDesc = function(desc) {
			var charCount = 0,
					charCountMax = 127,
					words = stripTags(desc).split(' ');
					retval = [];

			for (var i in words) {
				var word = words[i];
				charCount += word.length;
				if (charCount >= charCountMax) {
					retval.push('&hellip;');
					return retval.join(' ');
				} else {
					retval.push(word);
				}
			}

			return retval.join(' ');

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
					$template.find('p').html(shortenDesc(item.description));
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
				day          = date.getDate(),
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

		var todayEventFeed = 'https://events.ucf.edu/' + year + '/' + month + '/' + day + '/feed.json';
		updateEvents(todayEventFeed);
	}
};

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready( function($) {
		headerImage($);
		calendarWidget($);
	});
}

