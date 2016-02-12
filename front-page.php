<?php disallow_direct_load( 'home.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<div class="row">
			<div class="col-md-7 col-sm-6 col-xs-12">
				<div class="page-wrap">
					<h2><?php echo the_title(); ?></h2>
					<div class="content">
						<?php
							$message = get_field( 'page_message' );
							echo apply_filters( 'the_content', $message );
						?>
					</div>
				</div>
			</div>
			<div class="col-md-5 col-sm-6 col-xs-12">
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
	<nav id="sections-navbar" class="navbar navbar-gold center">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sections-menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="sections-menu">
					<ul class="nav navbar-nav">

					</ul>
				<?php $weather = get_weather_data(); ?>
				<?php if ( $weather ) : ?>
				<div class="weather navbar-right">
					<?php if ( $weather->icon ) : ?>
						<span class="icon" title="<?php echo $weather->condition; ?>">
							<span class="<?php echo $weather->icon; ?>"></span>
						</span>
					<?php endif; ?>
					<span class="location">Orlando, FL</span>
					<span class="vertical-rule"></span>
					<span class="temp"><?php echo $weather->tempN; ?>&deg;F</span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</nav>
<?php
	the_content();
?>
</article>
<?php get_footer(); ?>