<?php disallow_direct_load( 'page.php' ); ?>
<?php get_header(); the_post(); ?>
<article>
	<div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
	<?php the_content(); ?>
</article>
<?php get_footer(); ?>