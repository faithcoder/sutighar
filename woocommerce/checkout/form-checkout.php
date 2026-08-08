<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'sutighar' ) ) );
	return;
}
?>
<section class="sg-page sg-container sg-checkout-page">
	<h1 class="entry-title"><?php esc_html_e( 'Checkout', 'sutighar' ); ?></h1>
	<form name="checkout" method="post" class="checkout woocommerce-checkout sg-checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<div class="sg-checkout-grid">
			<div>
				<?php if ( $checkout->get_checkout_fields() ) : ?>
					<h3><?php esc_html_e( 'Delivery Details', 'sutighar' ); ?></h3>
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				<?php endif; ?>
				<h3><?php esc_html_e( 'Payment Method', 'sutighar' ); ?></h3>
				<?php woocommerce_checkout_payment(); ?>
				<span class="sg-checkout-note"><?php esc_html_e( "We'll confirm your order on WhatsApp within a few hours.", 'sutighar' ); ?></span>
			</div>
			<aside class="sg-cart-summary">
				<h2><?php esc_html_e( 'Order Summary', 'sutighar' ); ?></h2>
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php wc_get_template( 'checkout/review-order.php', array( 'checkout' => $checkout ) ); ?>
				</div>
				<a class="sg-edit-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Edit cart', 'sutighar' ); ?></a>
				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</aside>
		</div>
	</form>
</section>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
