<?php
/**
 * Base Shortcode class
 **/
abstract class Shortcode {
    public
        $name        = 'Shortcode', // The name of the shortcode.
        $command     = 'shortcode', // The command used to call the shortcode.
        $description = 'This is the description of the shortcode.', // The description of the shortcode.
        $params      = array(), // The parameters used by the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    /*
     * Register the shortcode.
     * @since v0.0.1
     * @author Jim Barnes
     * @return void
     */
    public function register_shortcode() {
        add_shortcode( $this->command, array( $this, $this->callback ) );
    }

    /*
     * Returns the html option markup.
     * @since v0.0.1
     * @author Jim Barnes
     * @return string
     */
    public function get_option_markup() {
        return sprintf('<option value="%s">%s</option>', $this->command, $this->name);
    }

    /*
     * Returns the description html markup.
     * @since v0.0.1
     * @author Jim Barnes
     * @return string
     */
    public function get_description_markup() {
        return sprintf('<li class="shortcode-%s">%s</li>', $this->command, $this->description);
    }

    /*
     * Returns the form html markup.
     * @since v0.0.1
     * @author Jim Barnes
     * @return string
     */
    public function get_form_markup() {
        ob_start();
?>
        <li class="shortcode-<?php echo $this->command; ?>">
            <h3><?php echo $this->name; ?> Options</h3>
<?php
        foreach($this->params as $param) :
?>
            <h4><?php echo $param->name; ?></h4>
            <p class="help"><?php echo $param->help_text; ?></p>
            <?php echo $this->get_field_input( $param, $this->command ); ?>
<?php
        endforeach;
?>
        </li>
<?php
        return ob_get_clean();
    }

    /*
     * Returns the appropriate markup for the field.
     * @since v0.0.1
     * @author Jim Barnes
     * return string
     */
    private function get_field_input( $field, $command ) {
        $name      = isset( $field['name'] ) ? $field['name'] : '';
        $id        = isset( $field['id'] ) ? $field['id'] : '';
        $help_text = isset( $field['help_text'] ) ? $field['help_text'] : '';
        $type      = isset( $field['type'] ) ? $field['type'] : 'text';
        $default   = isset( $field['default'] ) ? $field['default'] : '';
        $template  = isset( $field['template'] ) ? $tempalte['template'] : '';

        $retval = '<h4>' . $name . '</h4>';
        if ( $help_text ) {
            $retval .= '<p class="help">' . $help_text . '</p>';
        }
        switch( $type ) {
            case 'text':
            case 'date':
            case 'email':
            case 'url':
            case 'number':
            case 'color':
                $retval .= '<input type="' . $type . '" name="' . $command . '-' . $id . '" value="" default-value="' . $default . '" data-parameter="' . $id . '">';
                break;
            case 'dropdown':
                $choices = is_array( $field['choices'] ) ? $field['choices'] : array();
                $retval .= '<select type="text" name="' . $command . '-' . $id . '" value="" default-value="' . $default . '" data-parameter="' . $id . '">';
                foreach ( $choices as $choice ) {
                    $retval .= '<option value="' . $choice['value'] . '">' . $choice['name'] . '</option>';
                }
                $retval .= '</select>';
                break;
        }

        return $retval;
    }
}

class CallToActionSC extends Shortcode {
	public
        $name        = 'Call to Action', // The name of the shortcode.
        $command     = 'call_to_action', // The command used to call the shortcode.
        $description = 'Displays a call to action image and text.', // The description of the shortcode.
        $params      = array(
        	array(
        		'name'      => 'Call to Action Object',
        		'id'        => 'cta_id',
        		'help_text' => 'Choose the call to action to display',
        		'type'      => 'dropdown',
        		'choices'   => array()
        	)
        ), // The parameters used by the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    public function __construct() {
    	$this->params[0]['choices'] = $this->get_choices();
    }

	private function get_choices() {
    	$posts = get_posts( array( 'post_type' => 'call_to_action' ) );
    	$retval = array( array( 'name' => '--- Choose ---', 'value' => null ) );
    	foreach( $posts as $post ) {
    		$retval[] = array(
    			'name'  => $post->post_title,
    			'value' => $post->ID
    		);
    	}

    	return $retval;
    }

    public static function callback( $attr, $content='' ) {
    	$attr = shortcode_atts( array(
    			'cta_id' => null
    		), $attr
    	);

    	ob_start();
    	if ( $attr['cta_id'] ) {
    		$post = get_post( $attr['cta_id'] );
    		echo CallToAction::toHTML( $post );
    	}
		return ob_get_clean();
    }
}

class SectionSC extends Shortcode {
    public
        $name        = 'Section', // The name of the shortcode.
        $command     = 'section', // The command used to call the shortcode.
        $description = 'Displays section markup', // The description of the shortcode.
        $params      = array(
            array(
                'name'      => 'Section Object',
                'id'        => 'section_id',
                'help_text' => 'Choose the section to display',
                'type'      => 'dropdown',
                'choices'   => array()
            )
        ), // The parameters used by the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    public function __construct() {
        $this->params[0]['choices'] = $this->get_choices();
    }

    private function get_choices() {
        $posts = get_posts( array( 'post_type' => 'section' ) );
        $retval = array( array( 'name' => '-- Choose Section --', 'value' => '' ) );
        foreach( $posts as $post ) {
            $retval[] = array(
                'name'  => $post->post_title,
                'value' => $post->ID
            );
        }

        return $retval;
    }

    public static function callback( $attr, $content='' ) {
        $attr = shortcode_atts( array(
                'section_id' => ''
            ), $attr
        );

        if ( isset( $attr['section_id'] ) ) {
            $post = get_post( $attr['section_id'] );
            return Section::toHTML( $post );
        } else {
            return '';
        }
    }
}

class MapSearchSC extends Shortcode {
    public
        $name        = 'MapSearch', // The name of the shortcode.
        $command     = 'map-search', // The command used to call the shortcode.
        $description = 'Displays map stripe', // The description of the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    public static function callback( $attr, $content='' ) {
        ob_start();
?>

<section id="map" class="map-search">
    <div class="search-box-container">
        <div class="section-header-text-wrapper">
            <h2 class="section-header-text">Locate Student Services on Campus</h2>
            <form class="search-form" action="<?php echo get_theme_mod_or_default( 'map_search_url' ) ?>">
                <input class="search-term" type="text" name="s" placeholder="First Year Experience, Career Services, Etc.">
                <button class="search-button" type="submit"><span class="fa fa-search"></span><span class="fa fa-spinner fa-spin"></span></button>
            </form>
        </div>
    </div>
</section>

<?php
        return ob_get_clean();
    }
}

function sc_search_form() {
    ob_start();
?>
    <?php get_search_form(); ?>
<?php
    return ob_get_clean();
}
add_shortcode( 'search_form', 'sc_search_form' );

class CalloutSC extends Shortcode {
    public
        $name        = 'Callout', // The name of the shortcode.
        $command     = 'callout', // The command used to call the shortcode.
        $description = 'Creates a callout box', // The description of the shortcode.
        $params      = array(
            array(
                'name'      => 'Color',
                'id'        => 'color',
                'help_text' => 'The color of the callout box',
                'type'      => 'color',
                'default'   => '#ffcc00'
            )
        ), // The parameters used by the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    public static function callback( $attr, $content='' ) {
        $attr = shortcode_atts( array(
                'color' => '#ffcc00'
            ),
            $attr
        );

        ob_start();
?>
        <aside class="callout"<?php echo !empty( $attr['color'] ) ? ' style="background: ' . $attr['color'] . '"' : ''; ?>>
            <div class="container">
                <?php echo apply_filters( 'the_content', $content ); ?>
            </div>
        </aside>
<?php
        return ob_get_clean();
    }
}

/**
 * Create a full-width box with icon_links centered inside.
 **/
class IconLinkSC extends Shortcode {
    public
        $name        = 'Icon Link', // The name of the shortcode.
        $command     = 'icon_link', // The command used to call the shortcode.
        $description = 'Displays the specified icon link', // The description of the shortcode.
        $params      = array(
            array(
                'name'      => 'Icon Link',
                'id'        => 'icon_link_id',
                'help_text' => 'The icon link you want to display',
                'type'      => 'dropdown',
                'choices'   => array()
            )
        ), // The parameters used by the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True; // Whether to add it to the shortcode Wysiwyg modal.

    public function __construct() {
        $this->params[0]['choices'] = $this->get_choices();
    }

    private function get_choices() {
        $posts = get_posts( array( 'post_type' => 'icon_link' ) );
        $retval = array( array( 'name' => '-- Choose Icon Link --', 'value' => '' ) );
        foreach( $posts as $post ) {
            $retval[] = array(
                'name'  => $post->post_title,
                'value' => $post->ID
            );
        }

        return $retval;
    }

    public static function callback( $attr, $content='' ) {
        $attr = shortcode_atts( array(
                'icon_link_id' => ''
            ), $attr
        );

        if ( isset( $attr['icon_link_id'] ) ) {
            $post = get_post( $attr['icon_link_id'] );
            return IconLink::toHTML( $post );
        } else {
            return '';
        }
    }
}

?>