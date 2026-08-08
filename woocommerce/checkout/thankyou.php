<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="sg-page sg-container sg-thankyou-page">
	<div class="sg-empty sg-thankyou">
		<?php echo sutighar_inline_icon( $order ? 'check' : 'cart' ); ?>
		<?php if ( $order ) : ?>
			<?php
			$shipping_total = (float) $order->get_shipping_total();
			$delivery_parts = array_filter(
				array(
					$order->get_billing_first_name(),
					$order->get_billing_phone(),
					$order->get_billing_address_1(),
					$order->get_billing_city(),
					$order->get_billing_state(),
					$order->get_billing_postcode(),
				)
			);
			?>
			<h2><?php echo esc_html( sprintf( __( 'Thank you, %s.', 'sutighar' ), $order->get_billing_first_name() ? $order->get_billing_first_name() : __( 'friend', 'sutighar' ) ) ); ?></h2>
			<p><?php esc_html_e( "Your order is placed. Tap below to send it to us on WhatsApp — we'll confirm within a few hours.", 'sutighar' ); ?></p>
			<div class="sg-thankyou-summary">
				<span class="sg-thankyou-kicker"><?php echo esc_html( sprintf( __( 'Order %1$s · %2$s', 'sutighar' ), $order->get_order_number(), wc_format_datetime( $order->get_date_created(), 'd M Y' ) ) ); ?></span>
				<?php foreach ( $order->get_items() as $item ) : ?>
					<div class="sg-summary-line"><span><?php echo esc_html( $item->get_name() . ' × ' . $item->get_quantity() ); ?></span><strong><?php echo wp_kses_post( wc_price( $item->get_total(), array( 'decimals' => 0 ) ) ); ?></strong></div>
				<?php endforeach; ?>
				<div class="sg-thankyou-rule"></div>
				<div class="sg-thankyou-total"><?php echo wp_kses_post( sprintf( __( 'Total %1$s (shipping %2$s)', 'sutighar' ), $order->get_formatted_order_total(), $shipping_total > 0 ? wc_price( $shipping_total, array( 'decimals' => 0 ) ) : __( 'free', 'sutighar' ) ) ); ?></div>
				<div class="sg-thankyou-payment"><?php echo esc_html( sprintf( __( 'Payment: %s', 'sutighar' ), $order->get_payment_method_title() ) ); ?></div>
				<?php if ( $order->get_meta( '_sg_transaction_id' ) ) : ?>
					<div class="sg-thankyou-payment"><?php echo esc_html( sprintf( __( 'Transaction ID: %s', 'sutighar' ), $order->get_meta( '_sg_transaction_id' ) ) ); ?></div>
				<?php endif; ?>
				<div class="sg-thankyou-rule"></div>
				<span class="sg-thankyou-kicker"><?php esc_html_e( 'Delivering to', 'sutighar' ); ?></span>
				<div class="sg-thankyou-address"><?php echo esc_html( $delivery_parts ? implode( ', ', $delivery_parts ) : __( 'Address not available', 'sutighar' ) ); ?></div>
			</div>
			<a class="sg-btn sg-thankyou-whatsapp" href="<?php echo esc_url( sutighar_whatsapp_order_url( $order ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Send order on WhatsApp', 'sutighar' ); ?> →</a>
			<p class="sg-thankyou-fallback"><?php echo esc_html( sprintf( __( "If WhatsApp doesn't open, message us at %s.", 'sutighar' ), sutighar_whatsapp_number( true ) ) ); ?></p>
			<a class="sg-edit-cart" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'sutighar' ); ?></a>
		<?php else : ?>
			<h2><?php esc_html_e( 'Thank you.', 'sutighar' ); ?></h2>
			<p><?php esc_html_e( 'We received your checkout request.', 'sutighar' ); ?></p>
			<a class="sg-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the collection', 'sutighar' ); ?></a>
		<?php endif; ?>
	</div>
</section>
