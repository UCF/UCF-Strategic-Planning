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

function get_custom_header_image() {
	global $post;
	$image_id = get_field( 'page_header_image', $post->ID );
	if ( is_front_page() || is_home() || empty( $image_id ) ) {
		return header_image();
	} else {
		$image = wp_get_attachment_image_src( $image_id, 'large' );
		return $image[0];
	}
}

function get_remote_menu( $menu_name ) {
	global $wp_customize;
	$customizing = isset( $wp_customize );
	$result_name = $menu_name.'_json';
	$result = get_transient( $result_name );

	if ( false === $result || $customizing ) {
		$opts = array(
			'http' => array(
				'timeout' => 15
			)
		);

		$context = stream_context_create( $opts );

		$file_location = get_theme_mod_or_default( $menu_name.'_feed' );
		if ( empty( $file_location ) ) {
			return;
		}

		$headers = get_headers( $file_location );
		$response_code = substr( $headers[0], 9, 3 );
		if ( $response_code !== '200' ) {
			return;
		}

		$result = json_decode( file_get_contents( $file_location, false, $context ) );
		if ( ! $customizing ) {
			set_transient( $result_name, $result, (60 * 60 * 24) );
		}
	}

	return $result;
}

function display_header_menu() {
	$menu = get_remote_menu( 'header_menu' );

	if ( empty( $menu ) ) {
		return;
	}

	ob_start();
?>
	<nav id="nav-header-wrap" role="navigation" class="screen-only hidden-xs hidden-sm">
		<ul id="header-menu" class="menu-list-unstyled list-inline text-center horizontal">
		<?php foreach( $menu->items as $item ) : ?>
			<li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
		<?php endforeach; ?>
		</ul>
	</nav>
	<div class="container">
		<nav id="site-nav-xs" class="hidden-md hidden-lg navbar navbar-inverse">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu-xs-collapse" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<span class="navbar-title">Navigation</span>
			</div>
			<div class="collapse navbar-collapse" id="header-menu-xs-collapse">
				<ul id="header-menu-xs" class="menu nav navbar-nav">
				<?php foreach( $menu->items as $item ) : ?>
					<li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
		</nav>
	</div>
<?php
	echo ob_get_clean();
}

function display_weather() {
	$weather = get_weather_data();
	ob_start();
?>
	<?php if ( $weather ) : ?>
		<div class="weather">
			<?php if ( $weather->icon ) : ?>
				<span class="icon" title="<?php echo $weather->condition; ?>">
					<span class="<?php echo $weather->icon; ?>"></span>
				</span>
			<?php endif; ?>
			<span class="location">Orlando, FL</span>
			<span class="vertical-rule"></span>
			<span class="temp"><?php echo $weather->tempN; ?>&deg;F</span>
		</div>
	<?php endif; ?>
<?php
	return ob_get_clean();
}

function display_footer_menu() {
	$menu = get_remote_menu( 'footer_menu' );

	if ( empty( $menu) ) {
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
	<div class="footer-news">
	<?php foreach( $items as $key=>$item ) : $image = get_article_image( $item ); ?>
		<a href="<?php echo $item->get_link(); ?>">
			<div class="row news-item">
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
					</div>
				</div>
			</div>
		</a>
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
    <div class="footer-events">
    <?php foreach( $items as $item ) : ?>
        <?php
            $month = $item->get_date( 'M' );
            $day = $item->get_date( 'j' );
            $start_date = $item->get_item_tags( 'http://events.ucf.edu', 'startdate' );
        	$end_date = $item->get_item_tags( 'http://events.ucf.edu', 'enddate' );
        	$start_time = date( 'g:i a', strtotime( $start_date[0]['data'] ) );
        	$end_time = date( 'g:i a', strtotime( $end_date[0]['data'] ) );
        	$time_string = '';
        	if ( $start_time == $end_time ) {
        		$time_string = $start_time;
        	} else {
        		$time_string = $start_time . ' - ' . $end_time;
        	}
        ?>
        <a href="<?php echo $item->get_link(); ?>" target="_blank">
        	<div class="row event">
	        	<div class="col-xs-2 col-sm-4 col-md-3">
	        		<div class="event-date">
	        			<span class="month"><?php echo $month; ?></span>
	                	<span class="day"><?php echo $day; ?></span>
	               	</div>
	        	</div>
	        	<div class="col-xs-10 col-sm-8 col-md-9">
	        		<div class="event-details">
		                <h4><?php echo $item->get_title(); ?></h4>
		                <?php

		                ?>
		                <p class="time"><?php echo $time_string; ?></p>
		            </div>
	        	</div>
	    	</div>
	    </a>
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
	$instagram_url   = get_theme_mod_or_default( 'instagram_url' );
	$pinterest_url   = get_theme_mod_or_default( 'pinterest_url' );
	$youtube_url   = get_theme_mod_or_default( 'youtube_url' );
	ob_start();
?>
	<div class="social">
	<?php if ( $facebook_url ) : ?>
		<a href="<?php echo $facebook_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-facebook"></span>
			<span class="sr-only">Like us on Facebook</span>
		</a>
	<?php endif; ?>
	<?php if ( $twitter_url ) : ?>
		<a href="<?php echo $twitter_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-twitter"></span>
			<span class="sr-only">Follow us on Twitter</span>
		</a>
	<?php endif; ?>
	<?php if ( $googleplus_url ) : ?>
		<a href="<?php echo $googleplus_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-google-plus"></span>
			<span class="sr-only">Follow us on Google+</span>
		</a>
	<?php endif; ?>
	<?php if ( $linkedin_url ) : ?>
		<a href="<?php echo $linkedin_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-linkedin"></span>
			<span class="sr-only">View our LinkedIn page</span>
		</a>
	<?php endif; ?>
	<?php if ( $instagram_url ) : ?>
		<a href="<?php echo $instagram_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-instagram"></span>
			<span class="sr-only">View our Instagram page</span>
		</a>
	<?php endif; ?>
	<?php if ( $pinterest_url ) : ?>
		<a href="<?php echo $pinterest_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-pinterest-p"></span>
			<span class="sr-only">View our Pinterest page</span>
		</a>
	<?php endif; ?>
	<?php if ( $youtube_url ) : ?>
		<a href="<?php echo $youtube_url; ?>" target="_blank" class="social-icon ga-event-link">
			<span class="fa fa-youtube"></span>
			<span class="sr-only">View our YouTube page</span>
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

function get_academic_calendar_items() {

	$result_name = 'academic_calendar';

	$retval = get_transient( $result_name );

	if ( false === $retval ) {
		$opts = array(
			'http' => array(
				'timeout' => 15
			)
		);

		$context = stream_context_create( $opts );

		$file_location = get_theme_mod_or_default( 'academic_calendar_feed_url' );
		if ( empty( $file_location ) ) {
			return;
		}

		$result = json_decode( file_get_contents( $file_location, false, $context ) );
		if ( empty( $result ) ) {
			return;
		}

		$result = $result->terms[0]->events;
		foreach( $result as $r ) {
			if ( $r->isImportant ) {
				$retval[] = $r;
			}
			if ( count( $retval ) == 7 ) {
				break;
			}
		}

		set_transient( $result_name, $retval, (60 * 60 * 12) );
	}

	return $retval;

}

function google_tag_manager() {
	ob_start();
	$gtm_id = get_theme_mod_or_default( 'gtm_id' );
	if ( $gtm_id ) :
?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo $gtm_id; ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $gtm_id; ?>');</script>
<!-- End Google Tag Manager -->
<?php
	endif;

	return ob_get_clean();
}

?>
