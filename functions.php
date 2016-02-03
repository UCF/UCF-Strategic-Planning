<?php
require_once 'functions/base.php';          // Base theme functions
require_once 'functions/feeds.php';         // Where functions related to feed data live
require_once 'functions/custom-fields.php'; // Where custom field types are defined
require_once 'custom-taxonomies.php';       // Where per theme taxonomies are defined
require_once 'custom-post-types.php';       // Where per theme post types are defined
require_once 'functions/config.php';        // Where per theme settings are registered
require_once 'functions/theme.php';         // Theme-specific functions should be added here
require_once 'functions/admin.php';         // Admin/login functions
require_once 'shortcodes.php';              // Per theme shortcodes
 
/**
 * NOTE: functions specific to this theme should be defined in
 * functions/theme.php instead of this file.  Note the load order of files
 * listed above.
 **/
function get_call_to_action() {
	$args = array(
		'post_type' => 'call_to_action'
	);

	$posts = get_posts( $args );

	$random = rand( 0, $posts.length - 1 );

	$post = $posts[$random];

	return CallToAction::toHTML( $post );
}

function get_weather_data() {
	$opts = array(
		'http' => array(
			'timeout' => 15
		)
	);

	$context = stream_context_create( $opts );

	$file = file_get_contents( get_theme_mod_or_default( 'weather_feed_url' ), false, $context );

	$weather = json_decode( $file );

	switch ($weather->condition) {
		case 'Sunny':
			$weather->icon = 'sun-o';
			break;
		case 'Foggy':
		case 'Cloudy':
			$weather->icon = 'cloud';
			break;
		case 'Stormy':
			$weather->icon = 'bolt';
			break;
		case 'default':
			$weather->icon = 'sun-o';
			break;
		default:
			$weather->icon = 'sun-o';
			break;
	}

	return $weather;
}

?>
