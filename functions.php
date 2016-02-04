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

	$weather->icon = get_weather_icon( $weather->condition );

	return $weather;
}

function get_weather_icon( $condition ) {
	// https://erikflowers.github.io/weather-icons/
	// TODO: add more icons to this now that we're using weather icons

	$icon_prefix = "wi wi-";
	$icons_to_conditions = array(
			'day-sunny' => array(
				'sunny',
				'fair (day)',
				'fair',
				'default'
			),
			'night-clear' => array(
				'clear (night)',
				'fair (night)',
				'hot',
				'night'
			),
			'hot' => array(
				'hot'
			),
			'cloudy' => array(
				'cloudy',
				'mostly cloudy (night)',
				'mostly cloudy (day)',
				'partly cloudy (night)',
				'partly cloudy (day)',
				'partly cloudy',
				'partly cloudy'
			),
			'snowflake-cold' => array(
				'cold'
			),
			'showers' => array(
				'mixed rain and snow',
				'mixed rain and sleet',
				'mixed snow and sleet',
				'freezing drizzle',
				'drizzle',
				'freezing rain',
				'showers',
				'showers',
				'snow flurries',
				'light snow showers',
				'blowing snow',
				'snow',
				'hail',
				'sleet',
				'mixed rain and hail',
				'rainy',
				'thundershowers',
				'snow showers',
				'scattered showers',
			),
			'cloudy-gusts' => array(
				'blustery',
				'windy'
			),
			'fog' => array(
				'foggy',
				'haze',
				'smoky',
				'dust'
			),
			'storm-showers' => array(
				'isolated thundershowers',
				'isolated thunderstorms',
				'scattered thunderstorms',
				'scattered thunderstorms'
			),
			'lightning' => array(
				'heavy snow',
				'scattered snow showers',
				'heavy snow',
				'tornado',
				'tropical storm',
				'hurricane',
				'severe thunderstorms',
				'thunderstorms',
				'stormy'
			)
		);

	$condition = strtolower( $condition );

	foreach ( $icons_to_conditions as $icon => $condition_array ) {
		if ( in_array( $condition, $condition_array ) ) {
			return $icon_prefix . $icon;
		}
	}

	// If the condition for some reason isn't listed here,
	// no icon name will be returned and so no icon will be used
	return false;
}

?>
