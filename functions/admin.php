<?php

function add_shortcode_interface() {
	ob_start();
?>
	<a href="#TB_inline?width=600&height=700&inlineId=select-shortcode-form" class="thickbox button" id="add-shortcode" title="Add Shortcode"><span class="fa fa-code"></span> Add Shortcode</a>
<?php
	echo ob_get_clean();
}

add_action( 'media_buttons', 'add_shortcode_interface' );

function add_shortcode_interface_modal() {
	$page = basename( $_SERVER['PHP_SELF'] );
	if ( in_array( $page, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ) {
		include_once( THEME_DIR.'/includes/shortcode-interface.php' );
	}
}

add_action( 'admin_footer', 'add_shortcode_interface_modal' );

/**
 * Prints out additional login scripts, called by the login_head action
 *
 * @return void
 * @author Jared Lang
 **/
function login_scripts() {
	ob_start();
?>
	<link rel="stylesheet" href="<?php echo THEME_CSS_URL; ?>/admin.min.css" type="text/css" media="screen" charset="utf-8">
<?php
	echo ob_get_clean();
}

if ( is_login() ) {
	add_action( 'login_head', 'login_scripts', 0 );
}


/**
 * Registers utility pages with wordpress' admin.
 *
 * @return void
 * @author Jared Lang
 * */
function create_utility_pages() {
	add_menu_page(
		__( 'Help' ),
		__( 'Help' ),
		'edit_posts',
		'theme-help',
		'theme_help_page',
		'dashicons-editor-help'
	);
}

if ( is_admin() ) {
	add_action( 'admin_menu', 'create_utility_pages' );
}


/**
 * Outputs theme help page
 *
 * @return void
 * @author Jared Lang
 * */
function theme_help_page() {
	include THEME_INCLUDES_DIR . '/theme-help.php';
}


/**
 * Modifies the default stylesheets associated with the TinyMCE editor.
 *
 * @return string
 * @author Jared Lang
 * */
function editor_styles( $css ) {
	$css   = array_map( 'trim', explode( ',', $css ) );
	$css   = implode( ',', $css );
	return $css;
}
add_filter( 'mce_css', 'editor_styles' );


/**
 * Edits second row of buttons in tinyMCE editor. Removing/adding actions
 *
 * @return array
 * @author Jared Lang
 * */
function editor_format_options( $row ) {
	$found = array_search( 'underline', $row );
	if ( False !== $found ) {
		unset( $row[$found] );
	}
	return $row;
}
add_filter( 'mce_buttons_2', 'editor_format_options' );


/**
 * Remove paragraph tag from excerpts
 * */
remove_filter( 'the_excerpt', 'wpautop' );


/**
 * Enqueue the scripts and css necessary for the WP Media Uploader on
 * all admin pages
 * */
function enqueue_wpmedia_throughout_admin() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'enqueue_wpmedia_throughout_admin' );


/**
 * Add 'iconOrThumb' value to js-based attachment objects (for wp.media)
 * */
function add_icon_or_thumb_to_attachmentjs( $response, $attachment, $meta ) {
	$response['iconOrThumb'] = wp_attachment_is_image( $attachment->ID ) ? $response['sizes']['thumbnail']['url'] : $response['icon'];
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'add_icon_or_thumb_to_attachmentjs', 10, 3 );

function add_advanced_styles_button( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'add_advanced_styles_button' );

function add_editor_styles( $init_array ) {
	$style_formats = array(
		array(
			'title' => 'Text Transforms',
			'items' => array(
				array(
					'title'    => 'Uppercase Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-uppercase',
				),
				array(
					'title'    => 'Lowercase Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-lowercase'
				),
				array(
					'title'    => 'Capitalize Text',
					'selector' => 'h1,h2,h3,h4,h5,p',
					'classes'  => 'text-capitalize'
				),
			)
		),
		array(
			'title' => 'List Styles',
			'items' => array(
				array(
					'title'    => 'Unstyled List',
					'selector' => 'ul,ol',
					'classes'  => 'list-unstyled'
				),
				array(
					'title'    => 'Horizontal List',
					'selector' => 'ul,ol',
					'classes'  => 'list-inline'
				),
			),
		),
		array(
			'title' => 'Buttons',
			'items' => array(
				array(
					'title' => 'Button Sizes',
					'items' => array(
						array(
							'title'    => "Large Button",
							'selector' => 'a,button',
							'classes'  => 'btn btn-lg'
						),
						array(
							'title'    => 'Default Button',
							'selector' => 'a,button',
							'classes'  => 'btn'
						),
						array(
							'title'    => 'Small Button',
							'selector' => 'a,button',
							'classes'  => 'btn btn-sm'
						),
						array(
							'title'    => 'Extra Small Button',
							'selector' => 'a,button',
							'classes'  => 'btn btn-xs'
						),
					),
				),
				array(
					'title' => 'Button Styles',
					'items' => array(
						array(
							'title'    => 'Default',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-default'
						),
						array(
							'title'    => 'UCF Gold',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-ucf'
						),
						array(
							'title'    => 'Primary',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-primary'
						),
						array(
							'title'    => 'Success',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-success'
						),
						array(
							'title'    => 'Info',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-info'
						),
						array(
							'title'    => 'Warning',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-warning'
						),
						array(
							'title'    => 'Danger',
							'selector' => 'a.btn,button.btn',
							'classes'  => 'btn-danger'
						),
					),
				)
			),
		),
		array(
			'title'    => 'Lead',
			'selector' => 'p',
			'classes'  => 'lead'
		),
	);

	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;
}

add_filter( 'tiny_mce_before_init', 'add_editor_styles' );

function add_mce_stylesheet( $url ) {
	if ( ! empty( $url ) ) {
		$url .= ',';
	}

	$url .= THEME_CSS_URL . '/style.min.css';

	return $url;
}
add_filter( 'mce_css', 'add_mce_stylesheet' );

?>
