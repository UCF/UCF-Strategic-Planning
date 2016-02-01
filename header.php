<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?php wp_head(); ?>
	</head>
	<body ontouchstart <?php echo body_class(); ?>>
		<header class="site-header">
			<div class="header-image" style="background: url(<?php echo header_image(); ?>">
				<?php echo display_site_title(); ?>
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
