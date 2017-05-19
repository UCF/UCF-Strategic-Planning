<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?php wp_head(); ?>
		<?php echo google_tag_manager(); ?>
	</head>
	<body ontouchstart <?php echo body_class(); ?>>
		<header class="site-header">
			<div class="header-image" style="background-image: url(<?php echo get_custom_header_image(); ?>);">
				<?php display_header_menu(); ?>
				<div class="container">
					<div class="row">
						<div class="col-md-12 header-container">
							<div class="header-center">
								<div class="title-wrapper">
									<div class="title-header-container">
										<?php echo display_site_title(); ?>
										<div class="header-sub-title">Creating our Collective Impact</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<nav class="navbar site-navbar header-sub-nav" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-sub-menu">
							<span class="navbar-toggle-text"><span class="sr-only">Toggle Navigation</span> Menu</span>
							<span class="fa fa-bars" aria-hidden="true"></span>
						</button>
					</div>
					<div class="collapse navbar-collapse" id="header-sub-menu">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'header-menu',
							'depth'  => 2,
							'container' => false,
							'menu_class' => 'nav navbar-nav header-menu-nav',
							'walker' => new Bootstrap_Walker_Nav_Menu()
						) );
						?>
					</div>
				</div>
			</nav>
		</header>
		<main class="site-main">
		<?php if (wp_installing()) : 
			// `wp_installing` will return true if this file is being loaded on
			// wp-activate.php. The container is needed to properly format the output
		?>
			<div class="container">
		<?php endif; ?>
