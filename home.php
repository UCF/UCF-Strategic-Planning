<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<primary>
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<h2><?php echo the_title(); ?></h2>
				<?php echo the_content(); ?>
			</div>
			<div class="col-md-4">

			</div>
		</div>
	</div>
</primary>

<?php get_footer(); ?>
