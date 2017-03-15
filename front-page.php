<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row page-wrap">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<h2><?php echo the_title(); ?></h2>
				<div class="content">
					<?php
						$message = get_field( 'homepage_message' );
						echo apply_filters( 'the_content', $message );
					?>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-1 col-sm-6 col-xs-12">
				<?php
					$spotlight = get_field( 'homepage_spotlight' );
					if ( $spotlight ) {
						$spotlight = get_post( $spotlight );
						echo Spotlight::toHTML( $spotlight );
					}
				?>
			</div>
		</div>
	</div>
	<nav id="sections-navbar" class="navbar navbar-gold center">
		<div class="container-fluid">
			<div class="navbar-header">
				<span class="navbar-title">Skip To Section</span>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sections-menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
		</div>
	</nav>
<?php
	the_content();
?>
</article>
<?php get_footer(); ?>