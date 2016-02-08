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

function display_footer_menu() {
	$menu = get_footer_menu();

	ob_start();
?>
	<ul class="list-inline site-footer-menu">
	<?php foreach( $menu->items as $item ) : ?>
		<li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php
	echo ob_get_clean();
}

function display_top_news() {
	$items = get_news(0, 2);
	ob_start();
?>
	<ul class="footer-news">
	<?php foreach( $items as $key=>$item ) : ?>
		<li class="footer-news-item">
			<a href="<?php echo $item->get_link(); ?>">
				<h3><?php echo $item->get_title(); ?></h3>
				<p class="read-more">Read More &rsaquo;</p>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
	<a class="all-link" href="http://today.ucf.edu">All News &rsaquo;</a>
<?php
	echo ob_get_clean();
}

function display_events_widget() {
	ob_start();
?>

<?php
	echo ob_get_clean();
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

class EventsProxy {
	public static function get_plugin_namespace() {
		return 'events/v1';
	}

	public function register_routes() {
		register_rest_route( self::get_plugin_namespace(), 
			'/(?P<id>\d+)/(?P<slug>[a-zA-Z0-9_-]+)/(?P<size>[a-zA-Z0-9_-]+)/(?P<year>\d{4})/(?P<month>\d{2})', 
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_calendar' ),
					'args'     => array(
                        'context' => array(
                            'default' => 'view'
                        )
                    )   
				)
		) );
	}

	public static function get_calendar( $request ) {
		$id    = (int) $request['id'];
		$slug  = $request['slug'];
		$size  = $request['size'] ? $request['size'] : 'small';
		$year  = $request['year'] ? $request['year'] : date( 'Y' );
		$month = $request['month'] ? $request['month'] : date( 'm' );
		$base  = UCF_EVENTS_WIDGET;
		$params = array(
			$base,
			$id,
			$slug,
			$size,
			$year,
			$month
		);

		$url = implode( '/', $params );

		$opts = array(
			'http' => array(
				'timeout'=> 15
			)
		);

		$context = stream_context_create( $opts );
		$file = file_get_contents( $url, false, $context );

		return $file;
	}
}

if ( ! function_exists( 'events_proxy_init' ) ) {
	function events_proxy_init() {
		$class = new EventsProxy();
		add_filter( 'rest_api_init', array( $class, 'register_routes' ) );
	}
}

add_action( 'init', 'events_proxy_init' );

?>
