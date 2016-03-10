		</main>
		<footer class="site-footer">
			<div class="container">
				<div class="row">
					<div class="wrapper">
						<div class="col-sm-4">
							<div class="footer-col left-col">
								<h2>News</h2>
								<?php display_footer_news(); ?>
							</div>
							<a class="all-link" href="http://today.ucf.edu">More News &rsaquo;</a>
						</div>
						<div class="col-sm-4">
							<div class="footer-col center-col">
								<h2>Events</h2>
								<?php display_footer_events(); ?>
							</div>
							<a class="all-link more-events-link" href="http://events.ucf.edu">More Events &rsaquo;</a>
						</div>
						<div class="col-sm-4">
							<div class="footer-col right-col">
								<h2>Contact Us</h2>
								<?php display_contact_info(); ?>
								<h2>Questions and Comments</h2>
								<?php display_contact_form(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-site-footer">
				<div class="container">
					<p class="main-site-title">University of Central Florida</p>
					<?php display_social(); ?>
					<?php display_footer_menu() ; ?>
				</div>
			</div>
			<?php wp_footer(); ?>
		</footer>
	</body>
</html>
