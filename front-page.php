<?php disallow_direct_load( 'front-page.php' ); ?>
<?php get_header(); the_post(); ?>

<?php echo the_content(); ?>

<?php get_footer(); ?>
