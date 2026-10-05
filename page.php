<?php
get_header();

while ( have_posts() ) :
	the_post();
	$content = get_the_content();
	$has_full_sutighar_blocks = has_block( 'sutighar/hero' ) || has_block( 'sutighar/feature-cards' ) || has_block( 'sutighar/product-section' );
	$is_account_page          = function_exists( 'is_account_page' ) && is_account_page();
	?>
	<?php if ( $has_full_sutighar_blocks || is_cart() || is_checkout() ) : ?>
		<article <?php post_class( 'sg-block-page' ); ?>>
			<?php the_content(); ?>
		</article>
	<?php else : ?>
		<section class="sg-page sg-container">
			<article <?php post_class(); ?>>
				<?php if ( ! $is_account_page ) : ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
