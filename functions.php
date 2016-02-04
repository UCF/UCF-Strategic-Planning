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

function get_header_menu() {
	$opts = array(
		'http' => array(
			'timeout' => 15
		)
	);

	$context = stream_context_create( $opts );

	$file = file_get_contents( get_theme_mod_or_default( 'header_menu_feed' ), false, $context );

	return json_decode( $file );
}

function display_header_menu() {
	$menu = get_header_menu();

	ob_start();
?>
	<ul class="list-inline site-header-menu">
	<?php foreach( $menu->items as $item ) : ?>
		<li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php
	echo ob_get_clean();
}

function get_footer_menu() {
	$opts = array(
		'http' => array(
			'timeout' => 15
		)
	);

	$context = stream_context_create( $opts );

	$file = file_get_contents( get_theme_mod_or_default( 'footer_menu_feed' ), false, $context );

	return json_decode( $file );
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
