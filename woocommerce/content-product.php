<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$permalink       = $product->get_permalink();
$can_quick_cart  = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
?>
<li <?php wc_product_class( 'sg-card', $product ); ?>>
	<a class="sg-card__image" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php
		if ( has_post_thumbnail( $product->get_id() ) ) {
			echo wp_get_attachment_image(
				$product->get_image_id(),
				'large',
				false,
				array(
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => '(max-width: 719px) 150px, 285px',
					'alt'      => $product->get_name(),
				)
			);
		} else {
			printf(
				'<img src="%s" width="600" height="750" loading="lazy" decoding="async" alt="%s">',
				esc_url( sutighar_asset( 'assets/images/product-placeholder.svg' ) ),
				esc_attr( $product->get_name() . ' — cotton lungi by Sutighar' )
			);
		}
		?>
	</a>
	<?php if ( $can_quick_cart ) : ?>
		<button class="sg-card-cart" type="button" data-sg-card-add-to-cart data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Add to cart', 'sutighar' ); ?>">
			<?php echo sutighar_icon_img( 'solar_cart-bold.svg', 'sg-card-cart__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	<?php else : ?>
		<a class="sg-card-cart" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php esc_attr_e( 'View product options', 'sutighar' ); ?>">
			<?php echo sutighar_icon_img( 'solar_cart-bold.svg', 'sg-card-cart__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( $permalink ); ?>" style="display:grid;gap:5px">
		<h2 class="woocommerce-loop-product__title"><?php echo esc_html( $product->get_name() ); ?></h2>
		<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
	</a>
</li>
