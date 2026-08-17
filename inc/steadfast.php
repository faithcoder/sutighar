<?php
/**
 * SteadFast courier integration.
 *
 * Credentials are stored in WordPress options or constants, never in templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'sutighar_steadfast_admin_menu' );
function sutighar_steadfast_admin_menu() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_submenu_page(
		'woocommerce',
		__( 'Sutighar SteadFast', 'sutighar' ),
		__( 'Sutighar SteadFast', 'sutighar' ),
		'manage_woocommerce',
		'sutighar-steadfast',
		'sutighar_steadfast_settings_page'
	);
}

add_action( 'admin_init', 'sutighar_steadfast_register_settings' );
function sutighar_steadfast_register_settings() {
	register_setting( 'sutighar_steadfast', 'sutighar_steadfast_enabled', array( 'sanitize_callback' => 'absint' ) );
	register_setting( 'sutighar_steadfast', 'sutighar_steadfast_api_key', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'sutighar_steadfast', 'sutighar_steadfast_secret_key', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'sutighar_steadfast', 'sutighar_steadfast_base_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
}

function sutighar_steadfast_settings_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sutighar SteadFast', 'sutighar' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'sutighar_steadfast' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Auto-create consignment', 'sutighar' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="sutighar_steadfast_enabled" value="1" <?php checked( sutighar_steadfast_enabled() ); ?>>
							<?php esc_html_e( 'Send new WooCommerce orders to SteadFast after checkout.', 'sutighar' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sutighar_steadfast_api_key"><?php esc_html_e( 'API Key', 'sutighar' ); ?></label></th>
					<td><input class="regular-text" type="password" id="sutighar_steadfast_api_key" name="sutighar_steadfast_api_key" value="<?php echo esc_attr( get_option( 'sutighar_steadfast_api_key', '' ) ); ?>" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sutighar_steadfast_secret_key"><?php esc_html_e( 'Secret Key', 'sutighar' ); ?></label></th>
					<td><input class="regular-text" type="password" id="sutighar_steadfast_secret_key" name="sutighar_steadfast_secret_key" value="<?php echo esc_attr( get_option( 'sutighar_steadfast_secret_key', '' ) ); ?>" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sutighar_steadfast_base_url"><?php esc_html_e( 'API Base URL', 'sutighar' ); ?></label></th>
					<td><input class="regular-text" type="url" id="sutighar_steadfast_base_url" name="sutighar_steadfast_base_url" value="<?php echo esc_attr( sutighar_steadfast_base_url() ); ?>"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function sutighar_steadfast_enabled() {
	return (bool) get_option( 'sutighar_steadfast_enabled', 0 );
}

function sutighar_steadfast_base_url() {
	return untrailingslashit( get_option( 'sutighar_steadfast_base_url', 'https://portal.packzy.com/api/v1' ) );
}

function sutighar_steadfast_credentials() {
	$api_key    = defined( 'SUTIGHAR_STEADFAST_API_KEY' ) ? SUTIGHAR_STEADFAST_API_KEY : get_option( 'sutighar_steadfast_api_key', '' );
	$secret_key = defined( 'SUTIGHAR_STEADFAST_SECRET_KEY' ) ? SUTIGHAR_STEADFAST_SECRET_KEY : get_option( 'sutighar_steadfast_secret_key', '' );

	return array(
		'api_key'    => trim( (string) $api_key ),
		'secret_key' => trim( (string) $secret_key ),
	);
}

add_action( 'woocommerce_checkout_order_processed', 'sutighar_steadfast_maybe_create_consignment', 40 );
function sutighar_steadfast_maybe_create_consignment( $order_id ) {
	if ( ! sutighar_steadfast_enabled() ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	if ( $order->get_meta( '_sg_steadfast_synced_at' ) ) {
		return;
	}

	$credentials = sutighar_steadfast_credentials();
	if ( '' === $credentials['api_key'] || '' === $credentials['secret_key'] ) {
		$order->add_order_note( __( 'SteadFast sync skipped: missing API credentials.', 'sutighar' ) );
		return;
	}

	$response = wp_remote_post(
		sutighar_steadfast_base_url() . '/create_order',
		array(
			'timeout' => 20,
			'headers' => array(
				'Api-Key'      => $credentials['api_key'],
				'Secret-Key'   => $credentials['secret_key'],
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( sutighar_steadfast_order_payload( $order ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		$order->update_meta_data( '_sg_steadfast_error', $response->get_error_message() );
		$order->save();
		$order->add_order_note( sprintf( __( 'SteadFast sync failed: %s', 'sutighar' ), $response->get_error_message() ) );
		return;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 || ! is_array( $body ) ) {
		$order->update_meta_data( '_sg_steadfast_error', wp_remote_retrieve_body( $response ) );
		$order->save();
		$order->add_order_note( sprintf( __( 'SteadFast sync failed with HTTP %d.', 'sutighar' ), $code ) );
		return;
	}

	$data = isset( $body['consignment'] ) && is_array( $body['consignment'] ) ? $body['consignment'] : $body;
	foreach ( array( 'consignment_id', 'tracking_code', 'status' ) as $key ) {
		if ( ! empty( $data[ $key ] ) ) {
			$order->update_meta_data( '_sg_steadfast_' . $key, sanitize_text_field( (string) $data[ $key ] ) );
		}
	}
	$order->update_meta_data( '_sg_steadfast_synced_at', current_time( 'mysql' ) );
	$order->update_meta_data( '_sg_steadfast_response', wp_json_encode( $body ) );
	$order->save();
	$order->add_order_note( __( 'SteadFast consignment created.', 'sutighar' ) );
}

function sutighar_steadfast_order_payload( WC_Order $order ) {
	$address_parts = array_filter(
		array(
			$order->get_billing_address_1(),
			$order->get_billing_city(),
			$order->get_billing_state(),
		)
	);

	return array(
		'invoice'           => (string) $order->get_order_number(),
		'recipient_name'    => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
		'recipient_phone'   => $order->get_billing_phone(),
		'recipient_address' => implode( ', ', $address_parts ),
		'cod_amount'        => (float) $order->get_total(),
		'note'              => $order->get_customer_note(),
	);
}
