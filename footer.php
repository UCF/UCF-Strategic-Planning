		<?php if (wp_installing()) : 
			// Closing out the <div class="container"> from header.php
			// for when this is called wp-activate.php
		?>
			</div>
		<?php endif; ?>
		</main>
		<?php wp_footer(); ?>
	</body>
</html>
