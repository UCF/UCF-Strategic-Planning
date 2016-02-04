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
				'fair',
				'default'
			),
			'hot' => array(
				'hot',
				'haze'
			),
			'cloudy' => array(
				'overcast',
				'partly cloudy',
				'mostly cloudy'
			),
			'snowflake-cold' => array(
				'blowing snow',
				'cold',
				'snow'
			),
			'showers' => array(
				'showers',
				'drizzle',
				'mixed rain/sleet',
				'mixed rain/hail',
				'mixed snow/sleet',
				'hail',
				'freezing drizzle'
			),
			'cloudy-gusts' => array(
				'windy'
			),
			'fog' => array(
				'dust',
				'smoke',
				'foggy'
			),
			'storm-showers' => array(
				'scattered thunderstorms',
				'scattered thundershowers',
				'scattered showers',
				'Freezing rain',
				'isolated thunderstorms',
				'isolated thundershowers'
			),
			'lightning' => array(
				'tornado',
				'severe thunderstorms'
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

