<?php
/**
 * Abstract class for defining custom post types.
 **/
abstract class CustomPostType {
	public
		$name           = 'custom_post_type',
		$plural_name    = 'Custom Posts',
		$singular_name  = 'Custom Post',
		$add_new_item   = 'Add New Custom Post',
		$edit_item      = 'Edit Custom Post',
		$new_item       = 'New Custom Post',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = False, // Featured images
		$use_order      = False, // Wordpress built-in order meta data
		$use_metabox    = False, // Enable if you have custom fields to display in admin
		$use_shortcode  = False, // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag' ),
		$built_in       = False,
		$metaboxes      = array(),

		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;


	public function __construct() {
		$fields = $this->get_fields();
		if ( !empty( $fields ) ) {
			$this->metaboxes[] = array(
				'fields' => $fields
			);
		}
	}

	/**
	 * Wrapper for get_posts function, that predefines post_type for this
	 * custom post type.  Any options valid in get_posts can be passed as an
	 * option array.  Returns an array of objects.
	 * */
	public function get_objects( $options=array() ) {
		$defaults = array(
			'numberposts'   => -1,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'post_type'     => $this->options( 'name' ),
		);
		$options = array_merge( $defaults, $options );
		$objects = get_posts( $options );
		return $objects;
	}


	/**
	 * Similar to get_objects, but returns array of key values mapping post
	 * title to id if available, otherwise it defaults to id=>id.
	 **/
	public function get_objects_as_options( $options ) {
		$objects = $this->get_objects( $options );
		$opt     = array();
		foreach ( $objects as $o ) {
			switch ( True ) {
			case $this->options( 'use_title' ):
				$opt[$o->post_title] = $o->ID;
				break;
			default:
				$opt[$o->ID] = $o->ID;
				break;
			}
		}
		return $opt;
	}


	/**
	 * Return the instances values defined by $key.
	 * */
	public function options( $key ) {
		$vars = get_object_vars( $this );
		return $vars[$key];
	}


	/**
	 * Additional fields on a custom post type may be defined by overriding this
	 * method on an descendant object.
	 * */
	public function fields() {
		return array();
	}

	/**
	 * Using instance variables defined, returns an array defining what this
	 * custom post type supports.
	 * */
	public function supports() {
		// Default support array
		$supports = array();
		if ( $this->options( 'use_title' ) ) {
			$supports[] = 'title';
		}
		if ( $this->options( 'use_order' ) ) {
			$supports[] = 'page-attributes';
		}
		if ( $this->options( 'use_thumbnails' ) ) {
			$supports[] = 'thumbnail';
		}
		if ( $this->options( 'use_editor' ) ) {
			$supports[] = 'editor';
		}
		if ( $this->options( 'use_revisions' ) ) {
			$supports[] = 'revisions';
		}
		return $supports;
	}


	/**
	 * Creates labels array, defining names for admin panel.
	 * */
	public function labels() {
		return array(
			'name'          => __( $this->options( 'plural_name' ) ),
			'singular_name' => __( $this->options( 'singular_name' ) ),
			'add_new_item'  => __( $this->options( 'add_new_item' ) ),
			'edit_item'     => __( $this->options( 'edit_item' ) ),
			'new_item'      => __( $this->options( 'new_item' ) ),
		);
	}

	/**
	 * Registers the custom post type and any other ancillary actions that are
	 * required for the post to function properly.
	 * */
	public function register() {
		$registration = array(
			'labels'     => $this->labels(),
			'supports'   => $this->supports(),
			'public'     => $this->options( 'public' ),
			'taxonomies' => $this->options( 'taxonomies' ),
			'_builtin'   => $this->options( 'built_in' )
		);

		if ( $this->options( 'use_order' ) ) {
			$registration = array_merge( $registration, array( 'hierarchical' => True, ) );
		}

		register_post_type( $this->options( 'name' ), $registration );

		if ( $this->options( 'use_shortcode' ) ) {
			add_shortcode( $this->options( 'name' ).'-list', array( $this, 'shortcode' ) );
		}
	}

	/**
	 * Registers the metaboxes for the custom post type.
	 **/
	public function register_metaboxes() {
		foreach( $this->metaboxes as $metabox ) {
			$options = array_merge( $this->default_metabox_options(), $metabox );
			if ( function_exists( 'register_field_group' ) ) {
				register_field_group( $options );
			}
		}
	}

	private function default_metabox_options() {
		return array(
			'id'         => 'custom_'.$this->options( 'name' ).'_fields',
			'title'      => __( $this->options( 'singular_name' ) . ' Fields' ),
			'fields'     => array(),
			'location'   => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => $this->options( 'name' ),
						'order_no' => 0,
						'group_no' => 0
					)
				)
			),
			'options' => array(
				'position'       => 'normal',
				'layout'         => 'default',
				'hide_on_screen' => array()
			),
			'menu_order' => 0
		);
	}

	public function get_fields( $fields=null ) {

		$fields = $fields ? $fields : $this->fields();

		$retval = array();

		foreach( $fields as $field ) {
			$opts = array_merge( $field,
				array(
					'key'          => $field['id'],
					'label'        => $field['name'],
					'name'         => $field['id'],
					'instructions' => $field['description'],
					'required'     => $field['required'] ? $field['required'] : false
				)
			);

			switch( $field['type'] ) {
				case 'text':
					$opts = array_merge( $opts,
						array(
							'type'          => 'text',
							'default_value' => $field['default'] ? $field['default'] : '',
							'placeholder'   => $field['placeholder'] ? $field['placeholder'] : '',
							'formatting'    => 'html'
						)
					);
					$retval[] = $opts;
					break;
				case 'textarea':
					$opts = array_merge( $opts,
						array(
							'type'          => 'textarea',
							'default_value' => $field['default'] ? $field['default'] : '',
							'placeholder'   => $field['placeholder'] ? $field['placeholder'] : '',
							'formatting'    => 'html'
						)
					);
					$retval[] = $opts;
					break;
				case 'number':
					$opts = array_merge( $opts,
						array(
							'type'          => 'number',
							'default_value' => $field['default'] ? $field['default'] : '',
							'placeholder'   => $field['placeholder'] ? $field['placeholder'] : '',
							'min'           => $field['min'] ? $field['min'] : null,
							'max'           => $field['max'] ? $field['max'] : null
						)
					);
					$retval[] = $opts;
					break;
				case 'email':
					$opts = array_merge( $opts,
						array(
							'type'          => 'email',
							'default_value' => $field['default'] ? $default['default'] : '',
							'placeholder'   => $field['placeholder'] ? $field['placeholder'] : '',
						)
					);
					$retval[] = $opts;
					break;
				case 'image':
					$opts = array_merge( $opts,
						array(
							'type'          => 'image',
							'preview_size'  => $field['preview_size'] ? $field['preview_size'] : 'thumbnail',
							'save_format'   => 'object',
							'library'       => $field['library'] ? $field['library'] : 'all'
						)
					);
					$retval[] = $opts;
					break;
				case 'file':
					$opts = array_merge( $opts,
						array(
							'type'          => 'file',
							'save_format'   => $field['save_as'] ? $field['save_as'] : 'object',
							'library'       => $field['library'] ? $field['library'] : 'all'
						)
					);
					$retval[] = $opts;
					break;
				case 'select':
					$opts = array_merge( $opts,
						array(
							'type'          => 'select',
							'choices'       => $field['choices'],
							'default_value' => $field['default'] ? $field['default'] : '',
							'allow_null'    => $field['allow_null'] ? $field['allow_null'] : 0,
							'multiple'      => $field['multiple'] ? $field['multiple'] : 0
						)
					);
					$retval[] = $opts;
					break;
				case 'checkbox-list':
					$opts = array_merge( $opts,
						array(
							'type'          => 'checkbox',
							'choices'       => $field['choices'],
							'default_value' => $field['default'] ? $field['default'] : null,
							'layout'        => $field['layout'] ? $field['layout'] : 'vertical'
						)
					);
					$retval[] = $opts;
					break;
				case 'radio':
					$opts = array_merge( $opts,
						array(
							'type'          => 'radio',
							'choices'       => $field['choices'],
							'default_value' => $field['default'] ? $field['default'] : null,
							'layout'        => $field['layout'] ? $field['layout'] : 'vertical'
						)
					);
					$retval[] = $opts;
					break;
				case 'checkbox':
					$opts = array_merge( $opts,
						array(
							'type'          => 'true_false',
							'message'       => $field['label'] ? $field['label'] : $field['name'],
							'default_value' => $field['default'] ? $field['default'] : 0
						)
					);
					$retval[] = $opts;
					break;
				case 'color':
					$opts = array_merge( $opts,
						array(
							'type'              => 'color_picker',
							'default_value'     => $field['default'] ? $field['default'] : null
						)
					);
					$retval[] = $opts;
					break;
				case 'icon':
					$opts = array_merge( $opts,
						array(
							'type'              => 'fa_icon'
						)
					);
					$retval[] = $opts;
					break;
				case 'wysiwyg':
					$opts = array_merge( $opts,
						array(
							'type'              => 'wysiwyg',
							'toolbar'           => $field['toolbar'] ? $field['toolbar'] : 'full',
							'media_upload'      => $field['media_upload'] ? $field['media_upload'] : 'no'
						)
					);
					$retval[] = $opts;
					break;
				case 'post_object':
					$opts = array_merge( $opts,
						array(
							'type'              => 'post_object',
							'post_type'         => $field['post_type'] ? $field['post_type'] : array( 'post' ),
							'taxonomy'          => $field['taxonomy'] ? $field['taxonomy'] : array( 'all' ),
							'allow_null'        => $field['allow_null'] ? $field['allow_null'] : 1,
							'multiple'          => $field['multiple'] ? $field['multiple'] : 0
						)
					);
					$retval[] = $opts;
					break;
				case 'menu':
					$opts = array_merge( $opts,
						array(
							'type'              => 'menu_select'
						)
					);
					$retval[] = $opts;
					break;
			}
		}

		return $retval;
	}

	/**
	 * Shortcode for this custom post type.  Can be overridden for descendants.
	 * Defaults to just outputting a list of objects outputted as defined by
	 * toHTML method.
	 * */
	public function shortcode( $attr ) {
		$default = array(
			'type' => $this->options( 'name' ),
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default, $attr );
		}else {
			$attr = $default;
		}
		return sc_object_list( $attr );
	}


	/**
	 * Handles output for a list of objects, can be overridden for descendants.
	 * If you want to override how a list of objects are outputted, override
	 * this, if you just want to override how a single object is outputted, see
	 * the toHTML method.
	 * */
	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return '';}

		$class = get_custom_post_type( $objects[0]->post_type );
		$class = new $class;

		ob_start();
?>
		<ul class="<?php if ( $css_classes ):?><?php echo $css_classes?><?php else:?><?php echo $class->options( 'name' )?>-list<?php endif;?>">
			<?php foreach ( $objects as $o ):?>
			<li>
				<?php echo $class->toHTML( $o )?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php
			$html = ob_get_clean();
		return $html;
	}


	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 * */
	public function toHTML( $object ) {
		$html = '<a href="'.get_permalink( $object->ID ).'">'.$object->post_title.'</a>';
		return $html;
	}
}

class Page extends CustomPostType {
	public
		$name           = 'page',
		$plural_name    = 'Pages',
		$singular_name  = 'Page',
		$add_new_item   = 'Add New Page',
		$edit_item      = 'Edit Page',
		$new_item       = 'New Page',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$built_in       = True;

	public function __construct() {
		// Register Home Page fields for front_page and posts_page only
		$homepage_metabox = array(
			'id'        => 'custom_homepage_fields',
			'title'     => 'Home Page Fields',
			'fields'    => $this->get_fields (
				array(
					array(
						'id'          => 'homepage_message',
						'name'        => __( 'Home Page Message' ),
						'description' => 'The message that will appear below the header image',
						'type'        => 'textarea',
						'formatting'  => 'html'
					),
					array(
						'id'          => 'homepage_spotlight',
						'name'        => __( 'Home Page Spotlight' ),
						'description' => 'The spotlight that will appear to the right of the home page message',
						'type'         => 'post_object',
						'post_type'    => array( 'spotlight' )
					)
				)
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
						'order_no' => 0,
						'group_no' => 0
					), // and
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
						'order_no' => 1,
						'group_no' => 0
					)
				), // or
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'posts_page',
						'order_no' => 0,
						'group_no' => 1
					)
				)
			),
			'options' => array(
				'position'       => 'acf_after_title',
			)
		);

		$this->metaboxes[] = $homepage_metabox;

		// Build default post type meta fields
		parent::__construct();

	}

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name'        => 'Custom Header Image',
				'description' => 'Replace the them header image with a custom iamge',
				'id'          => $prefix.'header_image',
				'type'        => 'image',
				'save_format' => 'object'
			),
			array(
				'name'        => 'Page Stylesheet',
				'description' => 'Add a custom stylesheet to this page',
				'id'          => $prefix.'stylesheet',
				'type'        => 'file'
			),
			array(
				'name'        => 'Page Javascript',
				'description' => 'Add a custom javascript file to this page',
				'id'          => $prefix.'javascript',
				'type'        => 'file'
			),
		);
	}
}

class Post extends CustomPostType {
	public
		$name           = 'post',
		$plural_name    = 'Posts',
		$singular_name  = 'Post',
		$add_new_item   = 'Add New Post',
		$edit_item      = 'Edit Post',
		$new_item       = 'New Post',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array( 'post_tag', 'category' ),
		$built_in       = True;

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Stylesheet',
				'description' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file'
			)
		);
	}
}

class IconLink extends CustomPostType {
	public
		$name           = 'icon_link',
		$plural_name    = 'Icon Links',
		$singular_name  = 'Icon Link',
		$add_new_item   = 'Add New Icon Link',
		$edit_item      = 'Edit Icon Link',
		$new_item       = 'New Icon Link',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array( );

	public function fields() {
		$prefix = $this->options( 'name' ) . '_';
		return array(
			array(
				'name' => 'Icon',
				'description' => '',
				'id' => $prefix.'icon',
				'type' => 'icon'
			),
			array(
				'name' => 'URL',
				'description' => 'The URL of the icon link',
				'id' => $prefix.'url',
				'type' => 'text'
			)
		);
	}

	public function toHTML( $object ) {
		$icon = get_field( 'icon_link_icon', $object->ID );
		$url = get_field( 'icon_link_url', $object->ID );
		ob_start();
?>
		<div class="icon-link">
			<a href="<?php echo $url; ?>" target="_blank">
				<div class="icon-wrapper">
					<span class="fa <?php echo $icon; ?>"></span>
				</div>
				<h3><?php echo $object->post_title; ?></h3>
				<p><?php echo $object->post_content; ?></p>
			</a>
		</div>
<?php
		return ob_get_clean();
	}
}

class Spotlight extends CustomPostType {
	public
		$name           = 'spotlight',
		$plural_name    = 'Spotlights',
		$singular_name  = 'Spotlight',
		$add_new_item   = 'Add New Spotlight',
		$edit_item      = 'Edit Spotlight',
		$new_item       = 'New Spotlight',
		$public         = True,
		$use_editor     = False,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array();

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name'        => 'Title Text Color',
				'description' => 'The color of the overlay text',
				'id'          => $prefix.'text_color',
				'type'        => 'color',
				'default'     => '#ffffff'
			),
			array(
				'name'        => 'Button Color',
				'description' => 'The background color of the call to action button',
				'id'          => $prefix.'btn_background',
				'type'        => 'color',
				'default'     => '#ffcc00'
			),
			array(
				'name'        => 'Button Text Color',
				'description' => 'The text color of the call to action button',
				'id'          => $prefix.'btn_foreground',
				'type'        => 'color',
				'default'     => '#ffffff'
			),
			array(
				'name'        => 'Button Text',
				'description' => 'The text of the call to action button',
				'id'          => $prefix.'btn_text',
				'type'        => 'text'
			),
			array(
				'name'        => 'URL',
				'description' => 'The url of the call to action',
				'id'          => $prefix.'url',
				'type'        => 'text'
			)
		);
	}

	public function toHTML( $object ) {
		$image_url = has_post_thumbnail( $object->ID ) ?
			wp_get_attachment_image_src( get_post_thumbnail_id( $object->ID ), 'spotlight' ) :
			null;

		if ( $image_url ) {
			$image_url = $image_url[0];
		}

		$url = get_field( 'spotlight_url', $object->ID );

		$title_color = get_field( 'spotlight_text_color', $object->ID );
		$btn_background = get_field( 'spotlight_btn_background', $object->ID );
		$btn_foreground = get_field( 'spotlight_btn_foreground', $object->ID );
		$btn_text = get_field( 'spotlight_btn_text', $object->ID );

		$btn_styles = array();
		if ( $btn_background ) : $btn_styles[] = 'background: '.$btn_background; endif;
		if ( $btn_foreground ) : $btn_styles[] = 'color: '.$btn_foreground; endif;

		ob_start();
		if ( $image_url && $url ) :
?>
		<a class="call-to-action" href="<?php echo $url; ?>" target="_blank">
			<img src="<?php echo $image_url; ?>" alt="<?php echo $object->post_title; ?>">
			<h2 <?php if ( $title_color ) : echo 'style="color: '.$title_color.'"'; ?>><?php echo $object->post_title; endif; ?></h2>
			<?php if ( $btn_text ) : ?>
			<div class="btn-wrapper">
				<span class="btn btn-lg btn-ucf" style="<?php if ( !empty( $btn_styles) ) : echo implode( '; ', $btn_styles ); endif; ?>">
					<?php echo $btn_text; ?>
				</span>
			</div>
			<?php endif; ?>
		</a>
<?php
		endif;
		return ob_get_clean();
	}
}

class Section extends CustomPostType {
	public
		$name           = 'section',
		$plural_name    = 'Sections',
		$singular_name  = 'Section',
		$add_new_item   = 'Add New Section',
		$edit_item      = 'Edit Section',
		$new_item       = 'New Section',
		$public         = True,
		$use_editor     = False,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array();

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name'        => 'Header Image',
				'description' => 'This image will be used in the section header when the header video ends or if the user\'s browser does not support video playback.',
				'id'          => $prefix.'header_image',
				'type'        => 'image',
				'preview_size'=> 'large'
			),
			array(
				'name'        => 'Header Video (mp4)',
				'description' => 'The video that appears as the header background (mp4).',
				'id'          => $prefix.'header_video_mp4',
				'type'        => 'file'
			),
			array(
				'name'        => 'Loop Header Video',
				'description' => 'If a header video is set, replay it indefinitely.',
				'id'          => $prefix.'header_video_loop',
				'label'       => 'Enable',
				'type'        => 'checkbox'
			),
			array(
				'name'        => 'Header Text',
				'description' => 'The text that will appear over the video header.',
				'id'          => $prefix.'header_text',
				'type'        => 'text'
			),
			array(
				'name'        => 'Header Text Position',
				'description' => 'The position of the header text.',
				'id'          => $prefix.'header_text_position',
				'type'        => 'radio',
				'choices'     => array(
					'' => 'Centered/Middle',
					'top'           => 'Centered/Top',
					'left'          => 'Left Aligned/Middle',
					'right'         => 'Right Aligned/Middle'
				),
				'default'     => ''
			),
			array(
				'name'        => 'Header Text Color',
				'description' => 'The color of the header text.',
				'id'          => $prefix.'header_text_color',
				'type'        => 'color'
			),
			array(
				'name'        => 'Lead Text',
				'description' => 'The lead text that will appear immediately under the section title.',
				'id'          => $prefix.'lead_text',
				'type'        => 'textarea'
			),
			array(
				'name'        => 'Feature Type',
				'description' => 'Choose the kind of feature to use for this section.',
				'id'          => $prefix.'feature_type',
				'type'        => 'radio',
				'choices'     => array(
					'feature_image' => 'Image',
					'feature_spotlight' => 'Spotlight'
				),
				'default'     => 'feature_image'
			),
			array(
				'name'        => 'Feature Image',
				'description' => 'The image that will appear in the content area.',
				'id'          => $prefix.'feature_image',
				'type'        => 'image',
				'conditional_logic' => array(
					'status' => 1,
					'rules'  => array(
						array(
							'field' => $prefix.'feature_type',
							'operator' => '==',
							'value' => 'feature_image'
						)
					),
					'allorany' => 'all'
				)
			),
			array(
				'name'        => 'Feature Spotlight',
				'description' => 'The call to action that will appear in the content area.',
				'id'          => $prefix.'feature_spotlight',
				'type'        => 'post_object',
				'post_type'   => array( 'spotlight' ),
				'conditional_logic' => array(
					'status' => 1,
					'rules'  => array(
						array(
							'field'    => $prefix.'feature_type',
							'operator' => '==',
							'value'    => 'feature_spotlight'
						)
					),
					'allorany' => 'all'
				)
			),
			array(
				'name'        => 'Content',
				'description' => 'The content that will appear to the right of the featured image.',
				'id'          => $prefix.'content',
				'type'        => 'wysiwyg',
				'toolbar'     => 'basic'
			),
			array(
				'name'        => 'Resource Links',
				'description' => 'A menu of external links to display',
				'id'          => $prefix.'resource_links',
				'type'        => 'menu'
			),
		);
	}

	function add_post_meta( $object ) {

		$post_id    = $object->ID;
		$prefix     = 'section_';

		$object->header_image         = get_field( $prefix.'header_image', $post_id );
		$object->header_video_mp4     = get_field( $prefix.'header_video_mp4', $post_id );
		$object->header_video_loop    = get_field( $prefix.'header_video_loop', $post_id );
		$object->header_text          = get_field( $prefix.'header_text', $post_id );
		$object->header_text_position = get_field( $prefix.'header_text_position', $post_id );
		$object->header_text_color    = get_field( $prefix.'header_text_color', $post_id );
		$object->lead_text            = get_field( $prefix.'lead_text', $post_id );
		$object->feature_type         = get_field( $prefix.'feature_type', $post_id );
		$object->feature_image        = get_field( $prefix.'feature_image', $post_id );
		$object->feature_spotlight    = get_field( $prefix.'feature_spotlight', $post_id );
		$object->content              = get_field( $prefix.'content', $post_id );
		$object->menu                 = get_field( $prefix.'resource_links', $post_id );

		return $object;
	}

	public function toHTML( $object ) {
		$object = Section::add_post_meta( $object );
		ob_start();
?>
		<section id="<?php echo $object->post_name; ?>" class="bucket-section">
			<div class="section-header">
				<div class="section-header-text-wrapper">
					<span class="section-header-text <?php echo $object->header_text_position; ?>" <?php if ( $object->header_text_color ) { echo 'style="color: '.$object->header_text_color.'" '; } ?>>
					<?php echo $object->header_text; ?>
					</span>
				</div>
				<?php if ( $object->header_image ) : ?>
					<?php
						$header_img = wp_get_attachment_image_src( $object->header_image, array( 2000, 750 ) );
					?>
					<div class="section-header-image-container">
						<img class="section-header-image" src="<?php echo $header_img[0]; ?>" alt="">
					</div>
				<?php endif; ?>
				<?php if ( $object->header_video_mp4 ) : ?>
					<?php
						$header_video_url = wp_get_attachment_url( $object->header_video_mp4 );
						$header_video_meta = wp_get_attachment_metadata( $object->header_video_mp4 );
					?>

					<div class="section-header-video-container" data-video-src="<?php echo $header_video_url; ?>" data-video-width="<?php echo $header_video_meta['width']; ?>" data-video-height="<?php echo $header_video_meta['height']; ?>" data-video-loop="<?php echo $object->header_video_loop ? 'true' : 'false'; ?>">
						<video class="section-header-video" muted></video>
					</div>
				<?php endif; ?>
			</div>
			<div class="container">
				<h2 class="section-title"><?php echo $object->post_title; ?></h2>
				<p class="lead"><?php echo $object->lead_text; ?></p>
				<div class="row">
					<div class="col-md-5 col-sm-6 col-xs-12 no-pad">
					<?php if ( $object->feature_type == 'feature_image' ) : ?>
						<?php $featured_img = wp_get_attachment_image_src( $object->feature_image, 'large' ); ?>
						<img class="img-responsive section-image" src="<?php echo $featured_img[0]; ?>">
					<?php else: ?>
						<?php echo Spotlight::toHTML( $object->feature_spotlight ); ?>
					<?php endif; ?>
					</div>
					<div class="col-md-6 col-md-offset-1 col-sm-6 section-content">
						<?php echo apply_filters( 'the_content', $object->content); ?>
						<?php if ( $object->menu ) : ?>
						<div class="menu-wrapper">
							<h2>Explore Further</h2>
							<?php
								wp_nav_menu(
									array(
										'menu'  => $object->menu,
										'container' => ''
									)
								);
							?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
<?php
		return ob_get_clean();
	}
}

?>
