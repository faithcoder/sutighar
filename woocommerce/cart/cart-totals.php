<?php
defined( 'ABSPATH' ) || exit;
?>
<aside class="cart_totals sg-cart-summary">
	<h2><?php esc_html_e( 'Order Summary', 'sutighar' ); ?></h2>
	<div class="sg-summary-line"><span><?php esc_html_e( 'Subtotal', 'sutighar' ); ?></span><span><?php wc_cart_totals_subtotal_html(); ?></span></div>
	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="sg-summary-line"><span><?php wc_cart_totals_coupon_label( $coupon ); ?></span><span><?php wc_cart_totals_coupon_html( $coupon ); ?></span></div>
	<?php endforeach; ?>
	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
		<div class="sg-summary-line"><span><?php esc_html_e( 'Shipping', 'sutighar' ); ?></span><span><?php wc_cart_totals_shipping_html(); ?></span></div>
	<?php endif; ?>
	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="sg-summary-line"><span><?php echo esc_html( $fee->name ); ?></span><span><?php wc_cart_totals_fee_html( $fee ); ?></span></div>
	<?php endforeach; ?>
	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
			<div class="sg-summary-line"><span><?php echo esc_html( $tax->label ); ?></span><span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span></div>
		<?php endforeach; ?>
	<?php endif; ?>
	<div class="sg-summary-line sg-summary-line--total"><span><?php esc_html_e( 'Total', 'sutighar' ); ?></span><span><?php wc_cart_totals_order_total_html(); ?></span></div>
	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="sg-btn sg-btn--full checkout-button"><?php esc_html_e( 'Checkout', 'sutighar' ); ?> →</a>
	<p><?php echo esc_html( sprintf( __( 'Free shipping on orders above ৳ %s.', 'sutighar' ), number_format_i18n( (float) sutighar_option( 'free_shipping_threshold', '3000' ) ) ) ); ?></p>
</aside>
