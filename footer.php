		</main>
		<footer class="site-footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<h2>Student News</h2>
						<?php display_footer_news(); ?>
					</div>
					<div class="col-sm-4">
						<h2>Events</h2>
						<?php display_footer_events(); ?>
					</div>
					<div class="col-sm-4">
						<?php display_contact_info(); ?>
						<h2>Contact Us</h2>
						<?php echo do_shortcode( '[gravityform id="1" title="false" description="false"]' ); ?>
						<h2>Connect with Us</h2>
						<?php display_social(); ?>
					</div>
				</div>
			</div>
			<div class="main-site-footer">
				<div class="container">
					<p class="main-site-title">University of Central Florida</p>
					<?php display_footer_menu() ; ?>
				</div>
			</div>
			<?php wp_footer(); ?>
		</footer>
	</body>
</html>
