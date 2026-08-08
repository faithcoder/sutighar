<?php
get_header();
?>
<section class="sg-page sg-container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<h1 class="entry-title"><?php esc_html_e( 'Nothing found', 'sutighar' ); ?></h1>
	<?php endif; ?>
</section>
<?php
get_footer();
