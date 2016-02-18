<?php
/*
 * Template Name: Sidebar Left
 */
?>
<?php disallow_direct_load( 'two-column-sidebar-left.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row page-wrap">
			<div class="col-md-4">
			<?php 
			if ( is_active_sidebar( 'sidebar' ) ) {
				dynamic_sidebar( 'sidebar' );
			}
			?>
			</div>
			<div class="col-md-8">
				<h2><?php echo the_title(); ?></h2>
				<?php echo the_content(); ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
