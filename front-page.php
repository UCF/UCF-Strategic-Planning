<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<div class="page">
					<h2><?php echo the_title(); ?></h2>
					<div class="content">
						<?php echo the_content(); ?>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<?php echo get_call_to_action(); ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
