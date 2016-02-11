// Define globals for JSHint validation:
/* global console */

// Theme Specific Code Here
// ...

// https://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling/488073#488073
function isScrolledIntoView(elem)
{
		var docViewTop = $(window).scrollTop(),
			docViewBottom = docViewTop + $(window).height(),
			elemTop = $(elem).offset().top,
			elemBottom = elemTop + $(elem).height();

	 return (docViewBottom >= elemTop && docViewTop <= elemBottom);
}

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

var footerAdjustments = function($) {
	$('.site-footer').find('.footer-col').matchHeight();
};

var positionHeaderBackgrounds = function($) {

	$('.section-header-image, .section-header-video').each( function() {

		var $this = $(this),
			canvasWidth = parseInt($this.parent().width()),
			canvasHeight = parseInt($this.parent().height()),
			maxRatio = Math.max(canvasWidth / $this.width(), canvasHeight / $this.height()),
			newImgWidth = maxRatio * $this.width(),
			newImgHeight = maxRatio * $this.height(),
			newImgX = (canvasWidth - newImgWidth) / 2,
			newImgY = (canvasHeight - newImgHeight) / 2;

		$this.css('width', newImgWidth);
		$this.css('height', newImgHeight);
		$this.css('left', newImgX);
		$this.css('top', newImgY);
	});
};


// Test if video auto plays
var isAutoPlay = function($) {

	// storing this in the session so we don't have to check every page load
	if (sessionStorage.canplayvideo && sessionStorage.canplayvideo === true) {
		loadVideos($);
		positionHeaderBackgrounds($);
		return true;
	}

	var mp4 = 'data:video/mp4;base64,AAAAFGZ0eXBNU05WAAACAE1TTlYAAAOUbW9vdgAAAGxtdmhkAAAAAM9ghv7PYIb+AAACWAAACu8AAQAAAQAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAnh0cmFrAAAAXHRraGQAAAAHz2CG/s9ghv4AAAABAAAAAAAACu8AAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAFAAAAA4AAAAAAHgbWRpYQAAACBtZGhkAAAAAM9ghv7PYIb+AAALuAAANq8AAAAAAAAAIWhkbHIAAAAAbWhscnZpZGVBVlMgAAAAAAABAB4AAAABl21pbmYAAAAUdm1oZAAAAAAAAAAAAAAAAAAAACRkaW5mAAAAHGRyZWYAAAAAAAAAAQAAAAx1cmwgAAAAAQAAAVdzdGJsAAAAp3N0c2QAAAAAAAAAAQAAAJdhdmMxAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAFAAOABIAAAASAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGP//AAAAEmNvbHJuY2xjAAEAAQABAAAAL2F2Y0MBTUAz/+EAGGdNQDOadCk/LgIgAAADACAAAAMA0eMGVAEABGjuPIAAAAAYc3R0cwAAAAAAAAABAAAADgAAA+gAAAAUc3RzcwAAAAAAAAABAAAAAQAAABxzdHNjAAAAAAAAAAEAAAABAAAADgAAAAEAAABMc3RzegAAAAAAAAAAAAAADgAAAE8AAAAOAAAADQAAAA0AAAANAAAADQAAAA0AAAANAAAADQAAAA0AAAANAAAADQAAAA4AAAAOAAAAFHN0Y28AAAAAAAAAAQAAA7AAAAA0dXVpZFVTTVQh0k/Ou4hpXPrJx0AAAAAcTVREVAABABIAAAAKVcQAAAAAAAEAAAAAAAAAqHV1aWRVU01UIdJPzruIaVz6ycdAAAAAkE1URFQABAAMAAAAC1XEAAACHAAeAAAABBXHAAEAQQBWAFMAIABNAGUAZABpAGEAAAAqAAAAASoOAAEAZABlAHQAZQBjAHQAXwBhAHUAdABvAHAAbABhAHkAAAAyAAAAA1XEAAEAMgAwADAANQBtAGUALwAwADcALwAwADYAMAA2ACAAMwA6ADUAOgAwAAABA21kYXQAAAAYZ01AM5p0KT8uAiAAAAMAIAAAAwDR4wZUAAAABGjuPIAAAAAnZYiAIAAR//eBLT+oL1eA2Nlb/edvwWZflzEVLlhlXtJvSAEGRA3ZAAAACkGaAQCyJ/8AFBAAAAAJQZoCATP/AOmBAAAACUGaAwGz/wDpgAAAAAlBmgQCM/8A6YEAAAAJQZoFArP/AOmBAAAACUGaBgMz/wDpgQAAAAlBmgcDs/8A6YEAAAAJQZoIBDP/AOmAAAAACUGaCQSz/wDpgAAAAAlBmgoFM/8A6YEAAAAJQZoLBbP/AOmAAAAACkGaDAYyJ/8AFBAAAAAKQZoNBrIv/4cMeQ==',
		body = document.getElementsByTagName('body')[0];

	var video = document.createElement('video');
	video.src = mp4;
	video.autoplay = true;
	video.volume = 0;
	video.style.visibility = 'hidden';

	body.appendChild(video);

	// Check if <video> can play. It won't be able to on Opera mini and IE8
	// http://stackoverflow.com/questions/14109654/check-if-a-user-is-on-ie8-for-html5-client-side
	if (typeof video.canPlayType == 'undefined') {
		sessionStorage.canplayvideo = false;
		return false;
	}

	// video.play() seems to be required for it to work,
	// despite the video having an autoplay attribute.
	video.play();

	// triggered if autoplay fails
	var removeVideoTimeout = setTimeout(function () {
		body.removeChild(video);
		$('.section-header-video-container').remove();
		sessionStorage.canplayvideo = false;
	}, 50);

	// triggered if autoplay works
	video.addEventListener('play', function () {
		clearTimeout(removeVideoTimeout);
		body.removeChild(video);
		loadVideos($);
		positionHeaderBackgrounds($);
		sessionStorage.canplayvideo = true;
	}, false);
};

var checkVideoPositionToPlay = function($) {
	$('.section-header-video').each(function () {
		if (isScrolledIntoView(this)) {
			this.play();
		}
		else {
			this.pause();
		}
	});
};

// Place videos inside placeholders
var loadVideos = function($) {

	$('.section-header-video-container').each( function() {
		var $this = $(this),
			$video = $this.children('.section-header-video'),
			video_loop = $this.data('video-loop') ? ' loop' : '',
			video_width = $this.data('video-loop') ? parseInt($this.data('video-width')) : 0,
			video_height = $this.data('video-loop') ? parseInt($this.data('video-height')) : 0,
			video_src = $this.data('video-src');

		$video.attr('loop', video_loop);
		$video.html('<source src="' + video_src + '" type="video/mp4">');
		$video.css('width', video_width);
		$video.css('height', video_height);

		$this.parent().children('.section-header-image-container').addClass('has-video');
	});
};

Number.prototype.clamp = function(min, max) {
	return Math.min(Math.max(this, min), max);
};

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready( function($) {
		headerImage($);
		footerAdjustments($);
		isAutoPlay($);
		checkVideoPositionToPlay($);

		$(window).on('resize', function() {
			positionHeaderBackgrounds($);
		});
		$(window).on('scroll', function() {
			checkVideoPositionToPlay($);
		});
	});
}

