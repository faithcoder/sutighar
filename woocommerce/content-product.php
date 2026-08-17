<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$saved = in_array( $product->get_id(), sutighar_wishlist_ids(), true );
$permalink = $product->get_permalink();
?>
<li <?php wc_product_class( 'sg-card', $product ); ?>>
	<a class="sg-card__image" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php
		if ( has_post_thumbnail( $product->get_id() ) ) {
			echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
		} else {
			printf(
				'<img src="%s" width="600" height="750" loading="lazy" decoding="async" alt="%s">',
				esc_url( sutighar_asset( 'assets/images/product-placeholder.svg' ) ),
				esc_attr( $product->get_name() . ' — cotton lungi by Sutighar' )
			);
		}
		?>
	</a>
	<button class="sg-heart <?php echo $saved ? 'is-saved' : ''; ?>" type="button" data-sg-wishlist-toggle data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-pressed="<?php echo $saved ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'Save to wishlist', 'sutighar' ); ?>">
		<?php echo sutighar_inline_icon( 'heart' ); ?>
	</button>
	<a href="<?php echo esc_url( $permalink ); ?>" style="display:grid;gap:8px">
		<h2 class="woocommerce-loop-product__title"><?php echo esc_html( $product->get_name() ); ?></h2>
		<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
	</a>
</li>
