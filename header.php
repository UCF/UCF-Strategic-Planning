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
				<div class="header-center">
					<div class="title-wrapper">
						<div class="title-header-container">
							<?php echo display_site_title(); ?>
						</div>
					</div>
				</div>
			</div>
		</header>
		<main class="site-main">
