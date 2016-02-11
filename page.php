<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="page-wrap">
					<h2><?php echo the_title(); ?></h2>
					<div class="content">
						<?php
							the_content();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>
<?php get_footer(); ?>