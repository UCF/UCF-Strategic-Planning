		</main>
		<footer class="site-footer">
			<div class="container">
				<div class="row">
					<div class="wrapper">
						<div class="col-sm-4">
							<div class="left-col">
								<h2>Student News</h2>
								<?php display_footer_news(); ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="center-col">
								<h2>Events</h2>
								<?php display_footer_events(); ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="right-col">
								<?php display_contact_info(); ?>
								<h2>Contact Us</h2>
								<?php display_contact_form(); ?>
								<h2>Connect with Us</h2>
								<?php display_social(); ?>
							</div>
						</div>
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
