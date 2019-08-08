<?php disallow_direct_load( 'search.php' ); ?>
<?php get_header(); ?>
<?php $query = isset( $_GET['s'] ) ? $_GET['s'] : ''; ?>

<div class="container margin-bottom-forty">
	<h1>Search Results</h1>

	<?php if ( have_posts() ): ?>
	<ul class="result-list">
		<?php while ( have_posts() ): the_post(); ?>
		<li class="item">
			<article>
				<h2>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a>
				<div class="snippet">
					<?php the_excerpt(); ?>
				</div>
			</article>
		</li>
		<?php endwhile; ?>
	</ul>
	<?php else: ?>
	<p>No results found for "<?php echo htmlentities( $query ); ?>".</p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
