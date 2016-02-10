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

	$file_location = get_theme_mod_or_default( 'header_menu_feed' );
	if ( empty( $file_location ) ) {
		return;
	}

	$file = file_get_contents( $file_location , false, $context );

	return json_decode( $file );
}

function display_header_menu() {
	$menu = get_header_menu();

	if (empty($menu) || !in_array("items", $menu)) {
		return;
	}

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

	$file_location = get_theme_mod_or_default( 'footer_menu_feed' );

	if ( empty( $file_location ) ) {
		return;
	}

	$file = file_get_contents( $file_location, false, $context );

	return json_decode( $file );
}

function display_footer_menu() {
	$menu = get_footer_menu();

	if (empty($menu) || !in_array("items", $menu)) {
		return;
	}

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

function display_footer_news() {
	$max_news = get_theme_mod_or_default( 'news_max_items' );
	$items = get_news(0, $max_news);
	$placeholder = get_theme_mod_or_default( 'news_placeholder_image' );
	ob_start();
?>
	<a class="all-link" href="http://today.ucf.edu">All News &rsaquo;</a>
	<div class="footer-news">
	<?php foreach( $items as $key=>$item ) : $image = get_article_image( $item ); ?>
		<div class="row">
			<div class="col-xs-2 col-sm-4 col-md-3">
				<div class="news-thumbnail">
				<?php if ( $image ) : ?>
					<img class="img-responsive" src="<?php echo $image; ?>" alt="Feed image for <?php echo $item->get_title(); ?>">
				<?php else : ?>
					<img class="img-responsive" src="<?php echo $placeholder; ?>" alt="UCF Today">
				<?php endif; ?>
				</div>
			</div>
			<div class="col-xs-10 col-sm-8 col-md-9">
				<div class="news-details">
					<h3><?php echo $item->get_title(); ?></h3>
					<p><?php echo wp_trim_words( $item->get_description(), 15 ); ?></p>
					<a href="<?php echo $item->get_link(); ?>" class="read-more">Read More &rsaquo;</a>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php
	echo ob_get_clean();
}

function display_footer_events() {
    $max_events = get_theme_mod_or_default( 'events_max_items' );
    $items = get_events( 0, $max_events );
    ob_start();
?>
	<a class="all-link" href="http://events.ucf.edu">All Events &rsaquo;</a>
    <div class="footer-events">
    <?php foreach( $items as $item ) : ?>
        <?php
            $month = $item->get_date( 'M' );
            $day = $item->get_date( 'j' );
        ?>
        <div class="row">
        	<div class="col-xs-2 col-sm-4 col-md-3">
        		<div class="event-date">
        			<span class="month"><?php echo $month; ?></span>
                	<span class="day"><?php echo $day; ?></span>
               	</div>
        	</div>
        	<div class="col-xs-10 col-sm-8 col-md-9">
        		<div class="event-details">
	                <h4><?php echo $item->get_title(); ?></h4>
	                <p><?php echo wp_trim_words( $item->get_description(), 15 ); ?></p>
	                <a href="<?php echo $item->get_link(); ?>" class="read-more" target="_blank">Read More &rsaquo;</a>
	            </div>
        	</div>
        </div>
    <?php endforeach; ?>
    </div>
<?php
    echo ob_get_clean();
}

function display_contact_info() {
	$org_name  = get_theme_mod_or_default( 'organization_name' );
	$org_phone = get_theme_mod_or_default( 'organization_phone' );
	$org_email = get_theme_mod_or_default( 'organization_email' );
	ob_start();
?>
	<h2 class="org-name"><?php echo $org_name; ?></h2>
	<p>Phone: <a class="read-more" href="tel:<?php echo str_replace( array( '-', '(', ')' ), '', $org_phone);?>"><?php echo $org_phone; ?></a></p>
	<p>Email: <a class="read-more" href="mailto:<?php echo $org_email; ?>"><?php echo $org_email; ?></a></p>
<?php
	echo ob_get_clean();
}

function display_contact_form() {
	$form_id = get_theme_mod_or_default( 'footer_contact_form' );
	echo do_shortcode( '[gravityform id="'.$form_id.'" title="false" description="false"]' );
}

function display_social() {
	$facebook_url   = get_theme_mod_or_default( 'facebook_url' );
	$twitter_url    = get_theme_mod_or_default( 'twitter_url' );
	$googleplus_url = get_theme_mod_or_default( 'googleplus_url' );
	$linkedin_url   = get_theme_mod_or_default( 'linkedin_url' );
	ob_start();
?>
	<div class="social">
	<?php if ( $facebook_url ) : ?>
		<a href="<?php echo $facebook_url; ?>" target="_blank" class="social-icon ga-event-link">
			<i class="fa fa-facebook"></i>
			<span class="sr-only">Like us on Facebook</span>
		</a>
	<?php endif; ?>
	<?php if ( $twitter_url ) : ?>
		<a href="<?php echo $twitter_url; ?>" target="_blank" class="social-icon ga-event-link">
			<i class="fa fa-twitter"></i>
			<span class="sr-only">Follow us on Twitter</span>
		</a>
	<?php endif; ?>
	<?php if ( $googleplus_url ) : ?>
		<a href="<?php echo $googleplus_url; ?>" target="_blank" class="social-icon ga-event-link">
			<i class="fa fa-google-plus"></i>
			<span class="sr-only">Follow us on Google+</span>
		</a>
	<?php endif; ?>
	<?php if ( $linkedin_url ) : ?>
		<a href="<?php echo $linkedin_url; ?>" target="_blank" class="social-icon ga-event-link">
			<i class="fa fa-linkedin"></i>
			<span class="sr-only">View our LinkedIn page</span>
		</a>
	<?php endif; ?>
	</div>
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
				'freezing rain',
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
