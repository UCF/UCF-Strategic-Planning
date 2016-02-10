<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?php wp_head(); ?>
		<script>
			var Webcom = {
				baseUrl: '<?php echo get_site_url(); ?>',
				eventsProxy: '<?php echo get_site_url(); ?>/wp-json/events/v1'
			};
		</script>
	</head>
	<body ontouchstart <?php echo body_class(); ?>>
		<header class="site-header">
			<div class="header-image" style="background-image: url(<?php echo header_image(); ?>);">
				<nav class="site-nav">
					<?php display_header_menu(); ?>
				</nav>
				<div class="header-center">
					<div class="title-wrapper">
						<div class="title-header-container">
							<?php echo display_site_title(); ?>
							<?php $weather = get_weather_data(); ?>
							<?php if ( $weather ) : ?>
								<div class="weather">
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
				</div>
			</div>
		</header>
		<main class="site-main">
