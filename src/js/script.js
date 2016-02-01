// Define globals for JSHint validation:
/* global console */

// Theme Specific Code Here
// ...
var headerImage = function($) {
	var resize = function() {
		var $image = $('.header-image'),
			width = $image.width(),
			height = (width * 0.375).clamp(200, 750);

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

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready(function ($) {
		headerImage($);
	});
}