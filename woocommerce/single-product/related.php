<?php
defined( 'ABSPATH' ) || exit;

if ( empty( $related_products ) ) {
	return;
}
?>
<section class="sg-similar-section">
	<h2><?php esc_html_e( 'Similar Product', 'sutighar' ); ?></h2>
	<ul class="products sg-product-grid">
		<?php
		foreach ( $related_products as $related_product ) {
			$GLOBALS['product'] = wc_get_product( $related_product->get_id() );
			$GLOBALS['post']    = get_post( $related_product->get_id() );
			if ( $GLOBALS['post'] ) {
				setup_postdata( $GLOBALS['post'] );
			}
			wc_get_template_part( 'content', 'product' );
		}
		wp_reset_postdata();
		?>
	</ul>
</section>
