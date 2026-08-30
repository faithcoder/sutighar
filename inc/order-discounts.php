<?php
/**
 * Admin-managed order discounts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUTIGHAR_CUSTOM_DISCOUNT_META = '_sg_custom_discount_amount';
const SUTIGHAR_CUSTOM_DISCOUNT_ITEM_META = '_sg_custom_discount';

add_action( 'add_meta_boxes', 'sutighar_register_order_discount_meta_box' );
function sutighar_register_order_discount_meta_box() {
	foreach ( array( 'shop_order', 'woocommerce_page_wc-orders' ) as $screen ) {
		add_meta_box(
			'sutighar-order-discount',
			__( 'Custom Discount', 'sutighar' ),
			'sutighar_render_order_discount_meta_box',
			$screen,
			'side',
			'default'
		);
	}
}

function sutighar_render_order_discount_meta_box( $post_or_order ) {
	$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $post_or_order->ID ?? 0 );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$amount = (float) $order->get_meta( SUTIGHAR_CUSTOM_DISCOUNT_META );
	wp_nonce_field( 'sutighar_save_order_discount', 'sutighar_order_discount_nonce' );
	?>
	<p>
		<label for="sutighar_custom_discount_amount"><?php esc_html_e( 'Discount amount', 'sutighar' ); ?></label>
		<input
			type="number"
			class="widefat"
			id="sutighar_custom_discount_amount"
			name="sutighar_custom_discount_amount"
			value="<?php echo esc_attr( $amount > 0 ? wc_format_localized_price( $amount ) : '' ); ?>"
			min="0"
			step="0.01"
			placeholder="0"
		>
	</p>
	<p class="description"><?php esc_html_e( 'Enter a fixed amount. It will appear as Custom Discount in order totals, emails, invoices, and customer order details.', 'sutighar' ); ?></p>
	<?php
}

add_action( 'woocommerce_process_shop_order_meta', 'sutighar_save_order_discount_meta_box', 70, 2 );
function sutighar_save_order_discount_meta_box( $order_id, $post_or_order = null ) {
	if ( empty( $_POST['sutighar_order_discount_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sutighar_order_discount_nonce'] ) ), 'sutighar_save_order_discount' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_shop_order', $order_id ) && ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $order_id );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$raw_amount = isset( $_POST['sutighar_custom_discount_amount'] ) ? wc_clean( wp_unslash( $_POST['sutighar_custom_discount_amount'] ) ) : '';
	$amount     = '' === $raw_amount ? 0.0 : (float) wc_format_decimal( $raw_amount );

	sutighar_apply_order_custom_discount( $order, max( 0, $amount ) );
}

function sutighar_apply_order_custom_discount( WC_Order $order, $amount ) {
	foreach ( $order->get_fees() as $item_id => $item ) {
		if ( $item->get_meta( SUTIGHAR_CUSTOM_DISCOUNT_ITEM_META ) ) {
			$order->remove_item( $item_id );
		}
	}

	if ( $amount > 0 ) {
		$item = new WC_Order_Item_Fee();
		$item->set_name( __( 'Custom Discount', 'sutighar' ) );
		$item->set_total( -1 * wc_format_decimal( $amount ) );
		$item->set_tax_status( 'none' );
		$item->add_meta_data( SUTIGHAR_CUSTOM_DISCOUNT_ITEM_META, '1', true );
		$order->add_item( $item );
		$order->update_meta_data( SUTIGHAR_CUSTOM_DISCOUNT_META, wc_format_decimal( $amount ) );
	} else {
		$order->delete_meta_data( SUTIGHAR_CUSTOM_DISCOUNT_META );
	}

	$order->calculate_totals( false );
	$order->save();
}
