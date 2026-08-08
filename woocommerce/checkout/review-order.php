<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="sg-checkout-summary-lines">
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			$item_data = wc_get_formatted_cart_item_data( $cart_item );
			?>
			<div class="sg-checkout-summary-line">
				<span>
					<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
					<?php echo $item_data ? ' · ' . wp_kses_post( wp_strip_all_tags( $item_data ) ) : ''; ?>
					<?php echo esc_html( ' × ' . $cart_item['quantity'] ); ?>
				</span>
				<strong><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?></strong>
			</div>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
	<div class="sg-checkout-summary-rule"></div>
	<div class="sg-checkout-summary-line sg-checkout-summary-line--subtotal">
		<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
		<strong><?php wc_cart_totals_subtotal_html(); ?></strong>
	</div>
	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="sg-checkout-summary-line">
			<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<strong><?php wc_cart_totals_coupon_html( $coupon ); ?></strong>
		</div>
	<?php endforeach; ?>
	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
		<?php
		$packages = WC()->shipping()->get_packages();
		$shipping_total = WC()->cart->get_shipping_total();
		?>
		<div class="sg-checkout-summary-line">
			<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
			<strong><?php echo $shipping_total > 0 ? wp_kses_post( wc_price( $shipping_total ) ) : esc_html__( 'Free', 'sutighar' ); ?></strong>
		</div>
	<?php endif; ?>
	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="sg-checkout-summary-line">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<strong><?php wc_cart_totals_fee_html( $fee ); ?></strong>
		</div>
	<?php endforeach; ?>
	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php foreach ( WC()->cart->get_tax_totals() as $tax ) : ?>
			<div class="sg-checkout-summary-line">
				<span><?php echo esc_html( $tax->label ); ?></span>
				<strong><?php echo wp_kses_post( $tax->formatted_amount ); ?></strong>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	<div class="sg-checkout-summary-line sg-checkout-summary-line--total">
		<span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
		<strong><?php wc_cart_totals_order_total_html(); ?></strong>
	</div>
</div>
