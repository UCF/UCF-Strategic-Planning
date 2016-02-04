<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<h2><?php echo the_title(); ?></h2>
				<?php 
					$message = get_field( 'page_message' );
					echo apply_filters( 'the_content', $message );
				?>
			</div>
			<div class="col-md-5">
				<?php 
					$spotlight = get_field( 'page_spotlight' );
					if ( $spotlight ) {
						$spotlight = get_post( $spotlight );
						echo Spotlight::toHTML( $spotlight );
					}
				?>
			</div>
		</div>
	</div>
<?php 
	the_content();
?>
</article>
<?php get_footer(); ?>
