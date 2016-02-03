<?php
/**
 * Register new ACF Custom fields here
 **/
class acf_field_fa_icon_field extends acf_field {
	var $settings,
		$default;

	/**
	 * __construct
	 * Set name and label needed for actions/filters
	 * @since 1.0.0
	 * @author Jim Barnes
	 **/
	function __construct() {
		$this->name     = 'fa_icon_field';
		$this->label    = __( 'Font-Awesome Icon Field' );
		$this->category = __( 'Basic' );
		$this->defaults = array(

		);

		parent::__construct();
	}

	function create_options( $field ) {
		$key = $field['name'];
	}

	function create_field( $field ) {
?>
	<?php echo $this->icon_field_modal_html(); ?>
	<div class="meta-icon-wrapper">
		<div class="meta-icon-preview">
			<?php if ( $field['value'] ) : ?>
				<i class="fa <?php echo $field['value']; ?> fa-preview"></i>
			<?php endif; ?>
		</div>
		<p class="hide-if-no-js">
			<?php if ( $field['value'] ) : ?>
			<a class="meta-icon-toggle thickbox" href="#TB_inline?width=600&height=550&inlineId=meta-icon-modal">Update Icon</a>
			<?php else: ?>
			<a class="meta-icon-toggle thickbox" href="#TB_inline?width=600&height=550&inlineId=meta-icon-modal">Choose Icon</a>
			<?php endif; ?>
		</p>
		<input class="meta-icon-field" id="<?php echo htmlentities( $field['name'] ); ?>" name="<?php echo htmlentities( $field['name'] ); ?>" type="hidden" value="<?php echo htmlentities( $field['value'] ); ?>">
	</div>
<?php
	}

	function icon_field_modal_html( $field ) {
		ob_start();
?>
		<div id="meta-icon-modal" style="display: none;">
        	<input type="hidden" id="meta-icon-field-id" value>
            <h2>Choose Icon</h2>
            <p>
                <input type="text" placeholder="search" id="meta-icon-search">
            </p>
            <ul class="meta-fa-icons">
            <?php foreach( $this->get_fa_icons() as $icon ) : ?>
                <li class="meta-fa-icon"><i class="fa <?php echo $icon; ?>" data-icon-value="<?php echo $icon; ?>"></i></li> 
            <?php endforeach; ?>
            </ul>
            <div class="meta-icon-modal-footer">
            	<button type="button" id="meta-icon-submit">Submit</button>
            </div>
        </div>
<?php

		return ob_get_clean();
	}

	function get_fa_icons() {
	    $opts = array(
	        'http' => array(
	            'timeout' => 15
	        )  
	    );
	    
	    $context = stream_context_create( $opts );
	    
	    $contents = file_get_contents( THEME_DATA_URL . '/fa-icons.json', false, $context );
	    return json_decode( $contents );
	}
}

new acf_field_fa_icon_field();
?>