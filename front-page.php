<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<primary>
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<h2><?php echo the_title(); ?></h2>
				<?php echo the_content(); ?>
			</div>
			<div class="col-md-5">
				<?php echo get_call_to_action(); ?>
			</div>
		</div>
	</div>
</primary>
<?php
	$args = array(
		'post_type' => 'section'
	);

	$posts = get_posts( $args );

	foreach( $posts as $post ) {
		echo Section::toHTML( $post );
	}
?>

<?php get_footer(); ?>
