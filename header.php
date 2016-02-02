<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?php wp_head(); ?>
	</head>
	<body ontouchstart <?php echo body_class(); ?>>
		<header class="site-header">
			<div class="header-image" style="background: url(<?php echo header_image(); ?>">
				<div class="header-center">
					<?php echo display_site_title(); ?>
					<?php $weather = get_weather_data(); ?>
					<?php if ( $weather ) : ?>
						<div class="weather">
							<span class="icon"><i class="fa fa-<?php echo $weather->icon; ?>"></i></span>
							<span class="location">Orlando, FL</span>
							<span class="vertical-rule"></span>
							<span class="temp"><?php echo $weather->temp; ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<nav class="site-nav">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'header-menu',
					'container' => false,
					'menu_class' => 'list-inline site-header-menu',
					'walker' => new Bootstrap_Walker_Nav_Menu()
				) );
				?>
			</nav>
		</header>
		<main class="site-main">
