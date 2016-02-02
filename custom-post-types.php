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

		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;


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
	 * Creates metabox array for custom post type. Override method in
	 * descendants to add or modify metaboxes.
	 * */
	public function metabox() {
		if ( $this->options( 'use_metabox' ) ) {
			return array(
				'id'       => $this->options( 'name' ).'_metabox',
				'title'    => __( $this->options( 'singular_name' ).' Fields' ),
				'page'     => $this->options( 'name' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}
		return null;
	}


	/**
	 * Registers metaboxes defined for custom post type.
	 * */
	public function register_metaboxes() {
		if ( $this->options( 'use_metabox' ) ) {
			$metabox = $this->metabox();
			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				'show_meta_boxes',
				$metabox['page'],
				$metabox['context'],
				$metabox['priority']
			);
		}
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

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Stylesheet',
				'description' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
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

class CallToAction extends CustomPostType {
	public
		$name           = 'call_to_action',
		$plural_name    = 'Calls to Action',
		$singular_name  = 'Call to Action',
		$add_new_item   = 'Add New Call to Action',
		$edit_item      = 'Edit Call to Action',
		$new_item       = 'New Call to Action',
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
				'name'        => 'Call to Action Title Text Color',
				'description' => 'The color of the overlay text',
				'id'          => $prefix.'text_color',
				'type'        => 'color',
				'default'     => '#ffffff'
			),
			array(
				'name'        => 'Call to Action Button Color',
				'description' => 'The background color of the call to action button',
				'id'          => $prefix.'btn_background',
				'type'        => 'color',
				'default'     => '#ffcc00'
			),
			array(
				'name'        => 'Call to Action Button Text Color',
				'description' => 'The text color of the call to action button',
				'id'          => $prefix.'btn_foreground',
				'type'        => 'color',
				'default'     => '#ffffff'
			),
			array(
				'name'        => 'Call to Action Button Text',
				'description' => 'The text of the call to action button',
				'id'          => $prefix.'btn_text',
				'type'        => 'text'
			),
			array(
				'name'        => 'Call to Action URL',
				'description' => 'The url of the call to action',
				'id'          => $prefix.'url',
				'type'        => 'text'
			)
		);
	}

	public function toHTML( $object ) {
		$image_url = has_post_thumbnail( $object->ID ) ? 
			wp_get_attachment_image_src( get_post_thumbnail_id( $object->ID ), 'call_to_action' )[0] :
			null;
		$url = get_post_meta( $object->ID, 'call_to_action_url', True );

		$title_color = get_post_meta( $object->ID, 'call_to_action_text_color', True );
		$btn_background = get_post_meta( $object->ID, 'call_to_action_btn_background', True );
		$btn_foreground = get_post_meta( $object->ID, 'call_to_action_btn_foreground', True );
		$btn_text = get_post_meta( $object->ID, 'call_to_action_btn_text', True );

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
				<span class="btn btn-lg btn-ucf" <?php if ( !empty( $btn_styles ) ) : echo explode( $btn_styles, ' ' ); endif; ?>>
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
		$use_editor     = True,
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
				'type'        => 'file'
			),
			array(
				'name'        => 'Header Video (mp4)',
				'description' => 'The video that appears as the header background (mp4).',
				'id'          => $prefix.'header_video_mp4',
				'type'        => 'file'
			),
			array(
				'name'        => 'Header Text',
				'description' => 'The text that will appear over the video header.',
				'id'          => $prefix.'header_text',
				'type'        => 'text'
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
			)
		);
	}
}

?>
