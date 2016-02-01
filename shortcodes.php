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
 * Create a full-width box with icon_links centered inside.
 **/
function sc_icon_links( $attr, $content ) {
	$amount          = isset( $attr['amount'] ) ? $attr['amount'] : 3;
	$alignment    = isset( $attr['alignment'] ) ? $attr['content_align'] : 'horizontal';
	$iconlinks = get_posts( array(
		'post_type'        => 'icon_link',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
		'posts_per_page'   => -1
	) );

	$extra_classes = '';

	// Append .text-left/center/right to $extra_classes
	if ( $alignment ) {
		$extra_classes .= ' align-' . $alignment;
	}

	ob_start();
?>

	<div class="icon-links
<?php if ($extra_classes !== ''): ?>
	<?php echo $extra_classes; ?>
<?php endif; ?>
	">

<?php
	if ( $iconlinks ) {
		foreach ( $iconlinks as $post ) {
			$icon = get_post_meta( $post->ID, 'icon_link_icon', true );
			echo '<i class="fa ' . $icon . '"></i><br />';
			echo $post->ID . "<br />";
			echo $post->post_title . "<br />";
			echo $post->post_content . "<br />";
		}
	}
?>

	</div>

<?php
	return ob_get_clean();
}
add_shortcode( 'icon-links', 'sc_icon_links' );

?>