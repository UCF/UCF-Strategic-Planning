<?php

/***************************************************************************
 * CLASSES
 *
 ***************************************************************************/

/**
 * The Config class provides a set of static properties and methods which store
 * and facilitate configuration of the theme.
 * */
class ArgumentException extends Exception {}

class Config {
	static
		$custom_post_types = array(), // Custom post types to register
		$custom_taxonomies = array(), // Custom taxonomies to register
		$setting_defaults  = array(), // Default settings for theme mods
		$shortcodes        = array(), // Shortcodes to register
		$styles            = array(), // Stylesheets to register
		$scripts           = array(), // Scripts to register
		$links             = array(), // <link>s to include in <head>
		$metas             = array(); // <meta>s to include in <head>


	/**
	 * Creates and returns a normalized name for a resource url defined by $src.
	 * */
	static function generate_name( $src, $ignore_suffix='' ) {
		$base = basename( $src, $ignore_suffix );
		$name = slugify( $base );
		return $name;
	}


	/**
	 * Registers a stylesheet with built-in wordpress style registration.
	 * Arguments to this can either be a string or an array with required css
	 * attributes.
	 *
	 * A string argument will be treated as the src value for the css, and all
	 * other attributes will default to the most common values.  To override
	 * those values, you must pass the attribute array.
	 *
	 * Array Argument:
	 * $attr = array(
	 *    'name'  => 'theme-style',  # Wordpress uses this to identify queued files
	 *    'media' => 'all',          # What media types this should apply to
	 *    'admin' => False,          # Should this be used in admin as well?
	 *    'src'   => 'http://some.domain/style.css',
	 * );
	 * */
	static function add_css( $attr ) {
		// Allow string arguments, defining source.
		if ( is_string( $attr ) ) {
			$new        = array();
			$new['src'] = $attr;
			$attr       = $new;
		}

		if ( !isset( $attr['src'] ) ) {
			throw new ArgumentException( 'add_css expects argument array to contain key "src"' );
		}
		$default = array(
			'name'  => self::generate_name( $attr['src'], '.css' ),
			'media' => 'all',
			'admin' => False,
		);
		$attr = array_merge( $default, $attr );

		$is_admin = ( is_admin() or is_login() );

		if (
			( $attr['admin'] and $is_admin ) or
			( !$attr['admin'] and !$is_admin )
		) {
			wp_deregister_style( $attr['name'] );
			wp_enqueue_style( $attr['name'], $attr['src'], null, null, $attr['media'] );
		}
	}


	/**
	 * Functions similar to add_css, but appends scripts to the footer instead.
	 * Accepts a string or array argument, like add_css, with the string
	 * argument assumed to be the src value for the script.
	 *
	 * Array Argument:
	 * $attr = array(
	 *    'name'  => 'jquery',  # Wordpress uses this to identify queued files
	 *    'admin' => False,     # Should this be used in admin as well?
	 *    'src'   => 'http://some.domain/style.js',
	 * );
	 * */
	static function add_script( $attr ) {
		// Allow string arguments, defining source.
		if ( is_string( $attr ) ) {
			$new        = array();
			$new['src'] = $attr;
			$attr       = $new;
		}

		if ( !isset( $attr['src'] ) ) {
			throw new ArgumentException( 'add_script expects argument array to contain key "src"' );
		}
		$default = array(
			'name'  => self::generate_name( $attr['src'], '.js' ),
			'admin' => False,
		);
		$attr = array_merge( $default, $attr );

		$is_admin = ( is_admin() or is_login() );

		if (
			( $attr['admin'] and $is_admin ) or
			( !$attr['admin'] and !$is_admin )
		) {
			// Override previously defined scripts
			wp_deregister_script( $attr['name'] );
			wp_enqueue_script( $attr['name'], $attr['src'], null, null, True );
		}
	}
}

/***************************************************************************
 * UTILITY FUNCTIONS
 *
 * The functions below can be WordPress-related but should NOT specific to
 * this theme.
 *
 ***************************************************************************/

/**
 * When called, prevents direct loads of the value of $page.
 **/
function disallow_direct_load( $page ) {
	if ( $page == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
		die( 'No' );
	}
}


/**
 * Really get the post type.  A post type of revision will return its parent
 * post type.
 *
 * @return string
 * @author Jared Lang
 * */
function post_type( $post ) {
	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	// check post_type field
	$post_type = $post->post_type;

	if ( $post_type === 'revision' ) {
		$parent    = (int)$post->post_parent;
		$post_type = post_type( $parent );
	}

	return $post_type;
}


/**
 * Get featured image url for a post
 *
 * @return array
 * @author Chris Conover
 **/
function get_featured_image_url( $post ) {
	if ( has_post_thumbnail( $post ) && ( $thumbnail_id = get_post_thumbnail_id( $post ) ) && ( $image = wp_get_attachment_image_src( $thumbnail_id ) ) ) {
		return $image[0];
	}
	return False;
}


/**
 * Returns true if the current request is on the login screen.
 *
 * @return boolean
 * @author Jared Lang
 **/
function is_login() {
	return in_array( $GLOBALS['pagenow'], array(
			'wp-login.php',
			'wp-register.php',
		) );
}


/**
 * Given a mimetype, will attempt to return a string representing the
 * application it is associated with.  If the mimetype is unknown, the default
 * return is 'document'.
 *
 * @return string
 * @author Jared Lang
 * */
function mimetype_to_application( $mimetype ) {
	switch ( $mimetype ) {
	default:
		$type = 'document';
		break;
	case 'text/html':
		$type = 'html';
		break;
	case 'application/zip':
		$type = 'zip';
		break;
	case 'application/pdf':
		$type = 'pdf';
		break;
	case 'application/msword':
	case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
		$type = 'word';
		break;
	case 'application/msexcel':
	case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
		$type = 'excel';
		break;
	case 'application/vnd.ms-powerpoint':
	case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
		$type = 'powerpoint';
		break;
	}
	return $type;
}


/**
 * Modifies a string to be suitable for use in a URL.
 * Alias for WordPress's built-in sanitize_title().
 **/
function slugify( $string, $fallback='' ) {
	return sanitize_title( $string, $fallback );
}


/**
 * Creates a string of attributes and their values from the key/value defined by
 * $attr.  The string is suitable for use in html tags.
 *
 * @return string
 * @author Jared Lang
 * */
function create_attribute_string( $attr ) {
	$attr_string = '';
	foreach ( $attr as $key=>$value ) {
		$attr_string .= " {$key}=\"{$value}\"";
	}
	return $attr_string;
}


/**
 * Creates an arbitrary html element.  $tag defines what element will be created
 * such as a p, h1, or div.  $attr is an array defining attributes and their
 * associated values for the tag created. $content determines what data the tag
 * wraps.  And $self_close defines whether or not the tag should close like
 * <tag></tag> (False) or <tag /> (True).
 *
 * @return string
 * @author Jared Lang
 * */
function create_html_element( $tag, $attr=array(), $content=null, $self_close=false ) {
	$attr_str = create_attribute_string( $attr );
	if ( $content !== null ) {
		$element = "<{$tag}{$attr_str}>{$content}</{$tag}>";
	}
	else {
		if ( $self_close ) {
			$element = "<{$tag}{$attr_str}/>";
		}
		else {
			$element = "<{$tag}{$attr_str}>";
		}
	}

	return $element;
}


/***************************************************************************
 * GENERAL USE FUNCTIONS
 *
 * Theme-wide general use functions. (Alphabetized)
 *
 ***************************************************************************/

/**
 * University Header enqueue-ing fix.
 * */
function add_id_to_ucfhb( $url ) {
	if ( ( false !== strpos( $url, 'bar/js/university-header.js' ) ) || ( false !== strpos( $url, 'bar/js/university-header-full.js' ) ) ) {
		remove_filter( 'clean_url', 'add_id_to_ucfhb', 10, 3 );
		return "$url' id='ucfhb-script";
	}
	return $url;
}
add_filter( 'clean_url', 'add_id_to_ucfhb', 10, 3 );


/**
 * Walker function to add Bootstrap classes to nav menus using wp_nav_menu()
 *
 * based on https://gist.github.com/1597994
 * */
function bootstrap_menus() {
	class Bootstrap_Walker_Nav_Menu extends Walker_Nav_Menu {

		function start_lvl( &$output, $depth = 0, $args = array() ) {

			$indent = str_repeat( "\t", $depth );
			$output    .= "\n$indent<ul class=\"dropdown-menu\">\n";

		}

		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$li_attributes = '';
			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = ( $args->has_children ) ? 'dropdown' : '';
			$classes[] = ( $item->current || $item->current_item_ancestor ) ? 'active' : '';
			$classes[] = 'menu-item-' . $item->ID;


			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			$attributes .= ( $args->has_children )      ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ( $args->has_children ) ? ' <span class="caret"></span></a>' : '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

			if ( !$element )
				return;

			$id_field = $this->db_fields['id'];

			//display this element
			if ( is_array( $args[0] ) )
				$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
			else if ( is_object( $args[0] ) )
					$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
				$cb_args = array_merge( array( &$output, $element, $depth ), $args );
			call_user_func_array( array( &$this, 'start_el' ), $cb_args );

			$id = $element->$id_field;

			// descend only when the depth is right and there are childrens for this element
			if ( ( $max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id] ) ) {

				foreach ( $children_elements[ $id ] as $child ) {

					if ( !isset( $newlevel ) ) {
						$newlevel = true;
						//start the child delimiter
						$cb_args = array_merge( array( &$output, $depth ), $args );
						call_user_func_array( array( &$this, 'start_lvl' ), $cb_args );
					}
					$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
				}
				unset( $children_elements[ $id ] );
			}

			if ( isset( $newlevel ) && $newlevel ) {
				//end the child delimiter
				$cb_args = array_merge( array( &$output, $depth ), $args );
				call_user_func_array( array( &$this, 'end_lvl' ), $cb_args );
			}

			//end this element
			$cb_args = array_merge( array( &$output, $element, $depth ), $args );
			call_user_func_array( array( &$this, 'end_el' ), $cb_args );

		}

	}
}
add_action( 'after_setup_theme', 'bootstrap_menus' );


/**
 * Given a name will return the custom post type's class name, or null if not
 * found
 *
 * @return string
 * @author Jared Lang
 **/
function get_custom_post_type( $name ) {
	$installed = installed_custom_post_types();
	foreach ( $installed as $object ) {
		if ( $object->options( 'name' ) == $name ) {
			return get_class( $object );
		}
	}
	return null;
}


/**
 * Uses the google search appliance to search the current site or the site
 * defined by the argument $domain.
 *
 * @return array
 * @author Jared Lang
 * */
function get_search_results(
	$query,
	$start=null,
	$per_page=null,
	$domain=null,
	$search_url="http://google.cc.ucf.edu/search"
) {
	$start     = ( $start ) ? $start : 0;
	$per_page  = ( $per_page ) ? $per_page : 10;
	$domain    = ( $domain ) ? $domain : $_SERVER['SERVER_NAME'];
	$results   = array(
		'number' => 0,
		'items'  => array(),
	);
	$query     = trim( $query );
	$per_page  = (int)$per_page;
	$start     = (int)$start;
	$query     = urlencode( $query );
	$arguments = array(
		'num'        => $per_page,
		'start'      => $start,
		'ie'         => 'UTF-8',
		'oe'         => 'UTF-8',
		'client'     => 'default_frontend',
		'output'     => 'xml',
		'sitesearch' => $domain,
		'q'          => $query,
	);

	if ( strlen( $query ) > 0 ) {
		$query_string = http_build_query( $arguments );
		$url          = $search_url.'?'.$query_string;
		$response     = file_get_contents( $url );

		if ( $response ) {
			$xml   = simplexml_load_string( $response );
			$items = $xml->RES->R;
			$total = $xml->RES->M;

			$temp = array();

			if ( $total ) {
				foreach ( $items as $result ) {
					$item            = array();
					$item['url']     = str_replace( 'https', 'http', $result->U );
					$item['title']   = $result->T;
					$item['rank']    = $result->RK;
					$item['snippet'] = $result->S;
					$item['mime']    = $result['MIME'];
					$temp[]          = $item;
				}
				$results['items'] = $temp;
			}
			$results['number'] = $total;
		}
	}

	return $results;
}


/**
 * Returns the default value for a setting in Config::$setting_defaults,
 * or $fallback if one is not available.
 **/
function get_setting_default( $setting, $fallback=null ) {
	return isset( Config::$setting_defaults[$setting] ) ? Config::$setting_defaults[$setting] : $fallback;
}


/**
 * Returns a theme mod, the theme mod's default defined in
 * Config::$setting_defaults, or $fallback.
 **/
function get_theme_mod_or_default( $mod, $fallback='' ) {
	return get_theme_mod( $mod, get_setting_default( $mod, $fallback ) );
}


/**
 * Fetches objects defined by arguments passed, outputs the objects according
 * to the objectsToHTML method located on the object. Used by the auto
 * generated shortcodes enabled on custom post types. See also:
 *
 * CustomPostType::objectsToHTML
 * CustomPostType::toHTML
 *
 * @return string
 * @author Jared Lang
 * */
function sc_object_list( $attrs, $options = array() ) {
	if ( !is_array( $attrs ) ) {return '';}

	$default_options = array(
		'default_content' => null,
		'sort_func' => null,
		'objects_only' => False
	);

	extract( array_merge( $default_options, $options ) );

	// set defaults and combine with passed arguments
	$default_attrs = array(
		'type'    => null,
		'limit'   => -1,
		'join'    => 'or',
		'class'   => '',
		'orderby' => 'menu_order title',
		'order'   => 'ASC',
		'offset'  => 0
	);
	$params = array_merge( $default_attrs, $attrs );

	// verify options
	if ( $params['type'] == null ) {
		return '<p class="error">No type defined for object list.</p>';
	}
	if ( !is_numeric( $params['limit'] ) ) {
		return '<p class="error">Invalid limit argument, must be a number.</p>';
	}
	if ( !in_array( strtoupper( $params['join'] ), array( 'AND', 'OR' ) ) ) {
		return '<p class="error">Invalid join type, must be one of "and" or "or".</p>';
	}
	if ( null == ( $class = get_custom_post_type( $params['type'] ) ) ) {
		return '<p class="error">Invalid post type.</p>';
	}

	$class = new $class;

	// Use post type specified ordering?
	if ( !isset( $attrs['orderby'] ) && !is_null( $class->default_orderby ) ) {
		$params['orderby'] = $class->orderby;
	}
	if ( !isset( $attrs['order'] ) && !is_null( $class->default_order ) ) {
		$params['order'] = $class->default_order;
	}

	// get taxonomies and translation
	$translate = array(
		'tags' => 'post_tag',
		'categories' => 'category',
		'org_groups' => 'org_groups'
	);
	$taxonomies = array_diff( array_keys( $attrs ), array_keys( $default_attrs ) );

	// assemble taxonomy query
	$tax_queries = array();
	$tax_queries['relation'] = strtoupper( $params['join'] );

	foreach ( $taxonomies as $tax ) {
		$terms = $params[$tax];
		$terms = trim( preg_replace( '/\s+/', ' ', $terms ) );
		$terms = explode( ' ', $terms );

		if ( array_key_exists( $tax, $translate ) ) {
			$tax = $translate[$tax];
		}

		$tax_queries[] = array(
			'taxonomy' => $tax,
			'field' => 'slug',
			'terms' => $terms,
		);
	}

	// perform query
	$query_array = array(
		'tax_query'      => $tax_queries,
		'post_status'    => 'publish',
		'post_type'      => $params['type'],
		'posts_per_page' => $params['limit'],
		'orderby'        => $params['orderby'],
		'order'          => $params['order'],
		'offset'         => $params['offset']
	);

	$query = new WP_Query( $query_array );

	global $post;
	$objects = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$objects[] = $post;
	}

	// Custom sort if applicable
	if ( $sort_func !== null ) {
		usort( $objects, $sort_func );
	}

	wp_reset_postdata();

	if ( $objects_only ) {
		return $objects;
	}

	if ( count( $objects ) ) {
		$html = $class->objectsToHTML( $objects, $params['class'] );
	}else {
		$html = $default_content;
	}
	return $html;
}


/***************************************************************************
 * HEADER AND FOOTER FUNCTIONS
 *
 * Functions that generate output for the header and footer, including
 * <meta>, <link>, page titles, body classes and Facebook OpenGraph
 * stuff.
 *
 ***************************************************************************/

/**
 * Header content modifications
 **/
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// If Yoast SEO is activated, assume we're handling ALL SEO-related
// modifications with it.  Don't use opengraph_setup().
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( !is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
	opengraph_setup();
}

add_action( 'wp_head', 'header_meta', 1 );
add_action( 'wp_head', 'header_links', 10 );


/**
 * Handles generating the meta tags configured for this theme.
 *
 * @return string
 * @author Jared Lang
 * */
function header_meta() {
	$metas     = Config::$metas;
	$meta_html = array();
	$defaults  = array();

	foreach ( $metas as $meta ) {
		$meta        = array_merge( $defaults, $meta );
		$meta_html[] = create_html_element( 'meta', $meta );
	}

	$meta_html = implode( "\n", $meta_html );
	echo $meta_html;
}


/**
 * Handles generating the link tags configured for this theme.
 *
 * @return string
 * @author Jared Lang
 * */
function header_links() {
	$links      = Config::$links;
	$links_html = array();
	$defaults   = array();

	foreach ( $links as $link ) {
		$link         = array_merge( $defaults, $link );
		$links_html[] = create_html_element( 'link', $link );
	}

	$links_html = implode( "\n", $links_html );
	echo $links_html;
}


/**
 * Generates a title based on context page is viewed.  Stolen from Thematic
 * */
function header_title( $title, $separator ) {
	$site_name = get_bloginfo( 'name' );

	if ( is_single() ) {
		$content = single_post_title( '', FALSE );
	}
	elseif ( is_home() || is_front_page() ) {
		$content = get_bloginfo( 'description' );
	}
	elseif ( is_page() ) {
		$content = single_post_title( '', FALSE );
	}
	elseif ( is_search() ) {
		$content = __( 'Search Results for:' );
		$content .= ' ' . esc_html( stripslashes( get_search_query() ) );
	}
	elseif ( is_category() ) {
		$content = __( 'Category Archives:' );
		$content .= ' ' . single_cat_title( '', false );
	}
	elseif ( is_404() ) {
		$content = __( 'Not Found' );
	}
	else {
		$content = get_bloginfo( 'description' );
	}

	if ( get_query_var( 'paged' ) ) {
		$content .= ' ' .$separator. ' ';
		$content .= 'Page';
		$content .= ' ';
		$content .= get_query_var( 'paged' );
	}

	if ( $content ) {
		if ( is_home() || is_front_page() ) {
			$elements = array(
				'site_name' => $site_name,
				'separator' => $separator,
				'content' => $content,
			);
		} else {
			$elements = array(
				'content' => $content,
			);
		}
	} else {
		$elements = array(
			'site_name' => $site_name,
		);
	}

	// But if they don't, it won't try to implode
	if ( is_array( $elements ) ) {
		$doctitle = implode( ' ', $elements );
	}
	else {
		$doctitle = $elements;
	}

	return $doctitle;
}
add_filter( 'wp_title', 'header_title', 10, 2 );


/**
 * Assembles the appropriate meta elements for facebook's opengraph stuff.
 * Utilizes the themes Config object to queue up the created elements.
 *
 * @return void
 * @author Jared Lang
 * */
function opengraph_setup() {
	if ( !(bool)get_theme_mod_or_default( 'enable_og' ) ) { return; }
	if ( is_search() || is_404() ) { return; }

	global $post;

	if ( !isset( $post ) ) { return; }

	setup_postdata( $post );

	if ( is_front_page() ) {
		$title       = htmlentities( get_bloginfo( 'name' ) );
		$url         = get_bloginfo( 'url' );
		$site_name   = $title;
	} else {
		$title     = htmlentities( $post->post_title );
		$url       = get_permalink( $post->ID );
		$site_name = htmlentities( get_bloginfo( 'name' ) );
	}

	// Set description
	if ( is_front_page() ) {
		$description = htmlentities( get_bloginfo( 'description' ) );
	}else {
		ob_start();
		the_excerpt();
		$description = trim( str_replace( '[...]', '', ob_get_clean() ) );
		// Generate a description if excerpt is unavailable
		if ( strlen( $description ) < 1 ) {
			ob_start();
			the_content();
			$description = apply_filters( 'the_excerpt', preg_replace(
					'/\s+/',
					' ',
					strip_tags( ob_get_clean() ) )
			);
			$words       = explode( ' ', $description );
			$description = implode( ' ', array_slice( $words, 0, 60 ) );
		}
	}

	$metas = array(
		array( 'property' => 'og:title'      , 'content' => $title ),
		array( 'property' => 'og:url'        , 'content' => $url ),
		array( 'property' => 'og:site_name'  , 'content' => $site_name ),
		array( 'property' => 'og:description', 'content' => $description ),
	);

	// Include image if available
	if ( !is_front_page() and has_post_thumbnail( $post->ID ) ) {
		$image = wp_get_attachment_image_src(
			get_post_thumbnail_id( $post->ID ),
			'single-post-thumbnail'
		);
		$metas[] = array( 'property' => 'og:image', 'content' => $image[0] );
	}


	// Include admins if available
	$admins = trim( get_theme_mod_or_default( 'fb_admins' ) );
	if ( strlen( $admins ) > 0 ) {
		$metas[] = array( 'property' => 'fb:admins', 'content' => $admins );
	}

	Config::$metas = array_merge( Config::$metas, $metas );
}


/***************************************************************************
 * REGISTRATION AND INSTALLATION FUNCTIONS
 *
 * Functions that register and install custom post types, taxonomies,
 * and meta boxes.
 *
 ***************************************************************************/

/**
 * Adding custom post types to the installed array defined in this function
 * will activate and make available for use those types.
 * */
function installed_custom_post_types() {
	$installed = Config::$custom_post_types;

	return array_map( function( $class ) {
		return new $class;
	}, $installed );
}

/**
 * Returns all shortcodes registered in the
 * Config::$installed_shortcodes array.
 **/
function installed_shortcodes() {
	$installed = Config::$shortcodes;

	return array_map( function( $class ) {
		return new $class;
	}, $installed );
}

/**
 * Adding custom post types to the installed array defined in this function
 * will activate and make available for use those types.
 * */
function installed_custom_taxonomies() {
	$installed = Config::$custom_taxonomies;

	return array_map( function( $class ) {
		return new $class;
	}, $installed );
}

function flush_rewrite_rules_if_necessary() {
	global $wp_rewrite;
	$original = get_option( 'rewrite_rules' );
	$rules    = $wp_rewrite->rewrite_rules();

	if ( !$rules or !$original ) {
		return;
	}
	ksort( $rules );
	ksort( $original );

	$rules    = md5( implode( '', array_keys( $rules ) ) );
	$original = md5( implode( '', array_keys( $original ) ) );

	if ( $rules != $original ) {
		flush_rewrite_rules();
	}
}

/**
 * Registers all installed custom taxonomies
 *
 * @return void
 * @author Chris Conover
 * */
function register_custom_taxonomies() {
	//Register custom post types
	foreach ( installed_custom_taxonomies() as $custom_taxonomy ) {
		$custom_taxonomy->register();
	}
}
add_action( 'init', 'register_custom_taxonomies' );

/**
 * Registers all installed custom post types
 *
 * @return void
 * @author Jared Lang
 * */
function register_custom_post_types() {
	//Register custom post types
	foreach ( installed_custom_post_types() as $custom_post_type ) {
		$custom_post_type->register();
	}

	//This ensures that the permalinks for custom posts work
	flush_rewrite_rules_if_necessary();
}
add_action( 'init', 'register_custom_post_types' );

function register_custom_post_type_fields() {
	foreach ( installed_custom_post_types() as $custom_post_type ) {
		$custom_post_type->register_metaboxes();
	}

	flush_rewrite_rules_if_necessary();
}
add_action( 'init', 'register_custom_post_type_fields' );

/**
 *
 * Registers all installed shortcode.
 * @return void
 * @author Jim Barnes
 **/
function register_shortcodes() {
	foreach( installed_shortcodes() as $shortcode ) {
		$shortcode->register_shortcode();
	}
}

add_action( 'init', 'register_shortcodes' );

?>
