<?php
function sc_search_form() {
    ob_start();
?>
    <?php get_search_form(); ?>
<?php
    return ob_get_clean();
}
add_shortcode( 'search_form', 'sc_search_form' );

?>