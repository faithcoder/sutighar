<?php
/**
 * Template Name: Sutighar Wishlist
 */

get_header();

$ids = sutighar_wishlist_ids();
?>
<section class="sg-page sg-container">
	<div class="sg-eyebrow"><?php esc_html_e( 'Home / Wishlist', 'sutighar' ); ?></div>
	<h1 class="entry-title"><?php esc_html_e( 'Your Wishlist', 'sutighar' ); ?></h1>

	<?php if ( $ids ) : ?>
		<ul class="products sg-product-grid" style="margin-top:40px">
			<?php
			foreach ( $ids as $id ) {
				$product = wc_get_product( $id );
				if ( $product ) {
					$GLOBALS['product'] = $product;
					$GLOBALS['post']    = get_post( $product->get_id() );
					if ( $GLOBALS['post'] ) {
						setup_postdata( $GLOBALS['post'] );
					}
					wc_get_template_part( 'content', 'product' );
				}
			}
			wp_reset_postdata();
			?>
		</ul>
	<?php else : ?>
		<div class="sg-empty">
			<?php echo sutighar_inline_icon( 'heart' ); ?>
			<h2><?php esc_html_e( 'Your wishlist is empty', 'sutighar' ); ?></h2>
			<p><?php esc_html_e( 'Tap the heart on any piece to save it here.', 'sutighar' ); ?></p>
			<a class="sg-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the collection', 'sutighar' ); ?></a>
		</div>
	<?php endif; ?>
</section>
<?php
get_footer();
