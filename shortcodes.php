<?php
function sc_search_form() {
    ob_start();
?>
    <?php get_search_form(); ?>
<?php
    return ob_get_clean();
}
add_shortcode( 'search_form', 'sc_search_form' );


/**
 * Create a full-width callout box.
 **/
function sc_callout( $attr, $content ) {
	$bgcolor          = isset( $attr['bgcolor'] ) ? $attr['bgcolor'] : '#f0f0f0';
	$background_image = isset( $attr['background_image'] ) ? $attr['background_image'] : '';
	$textcolor        = isset( $attr['textcolor'] ) ? $attr['textcolor'] : '#000';
	$content_align    = isset( $attr['content_align'] ) ? 'text-' . $attr['content_align'] : '';
	$extra_classes    = isset( $attr['class'] ) ? ' ' . $attr['class'] : '';

	// Generate 'style="..."' attribute for .callout element
	$style_str = '';
	$extra_classes = '';

	if ( $textcolor ) {
		$style_str .= 'color: ' . $textcolor . '; ';
		$style_str .= 'background-color: ' . $bgcolor . '; ';
	}

	// Append .text-left/center/right to $extra_classes
	if ( $content_align ) {
		$extra_classes .= ' ' . $content_align;
	}

	ob_start();
?>

	<div style="<?php echo $style_str; ?>" class="callout
<?php if ($extra_classes !== ''): ?>
	<?php echo $extra_classes; ?>
<?php endif; ?>
	">
		<?php echo $content; ?>
	</div>

<?php
	return ob_get_clean();
}
add_shortcode( 'callout', 'sc_callout' );

?>