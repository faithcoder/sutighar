<?php
/**
 * Manual mobile payment gateways and settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_payment_gateways', 'sutighar_register_gateways' );
function sutighar_register_gateways( $gateways ) {
	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		$gateways[] = 'Sutighar_Gateway_Bkash';
		$gateways[] = 'Sutighar_Gateway_Nagad';
	}
	return $gateways;
}

add_action( 'plugins_loaded', 'sutighar_load_gateways' );
add_action( 'woocommerce_loaded', 'sutighar_load_gateways' );
function sutighar_load_gateways() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	if ( class_exists( 'Sutighar_Gateway_Manual_Mobile', false ) ) {
		return;
	}

	abstract class Sutighar_Gateway_Manual_Mobile extends WC_Payment_Gateway {
		protected $method_key = '';
		public $merchant = '';

		public function __construct() {
			$this->has_fields         = true;
			$this->method_title       = $this->title;
			$this->method_description = __( 'Manual mobile payment with transaction ID capture.', 'sutighar' );
			$this->supports           = array( 'products' );

			$this->init_form_fields();
			$this->init_settings();

			$this->enabled     = $this->get_option( 'enabled' );
			$this->title       = $this->get_option( 'title', $this->title );
			$this->description = $this->get_option( 'description', $this->description );
			$this->merchant    = $this->get_option( 'merchant', '01XXXXXXXXX' );

			if ( $this->method_key && ! get_option( 'sutighar_enable_' . $this->method_key, true ) ) {
				$this->enabled = 'no';
			}

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => __( 'Enable/Disable', 'sutighar' ),
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Enable %s payments', 'sutighar' ), $this->method_title ),
					'default' => 'yes',
				),
				'title'       => array(
					'title'   => __( 'Title', 'sutighar' ),
					'type'    => 'text',
					'default' => $this->title,
				),
				'description' => array(
					'title'   => __( 'Description', 'sutighar' ),
					'type'    => 'textarea',
					'default' => $this->description,
				),
				'merchant'    => array(
					'title'   => __( 'Merchant number', 'sutighar' ),
					'type'    => 'text',
					'default' => '01XXXXXXXXX',
				),
			);
		}

		public function payment_fields() {
			$total = WC()->cart ? WC()->cart->get_total() : '';
			?>
			<div class="sg-payment-detail">
				<div class="sg-merchant-box">
					<?php echo esc_html( $this->method_title ); ?> <?php esc_html_e( 'Merchant:', 'sutighar' ); ?>
					<code><?php echo esc_html( $this->merchant ); ?></code>
					<span><?php esc_html_e( 'Amount:', 'sutighar' ); ?> <?php echo wp_kses_post( $total ); ?></span>
				</div>
				<p class="form-row form-row-wide">
					<label for="<?php echo esc_attr( $this->id ); ?>_trx_id"><?php esc_html_e( 'Transaction ID', 'sutighar' ); ?> <span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ); ?>_trx_id" name="<?php echo esc_attr( $this->id ); ?>_trx_id" class="input-text" type="text" autocomplete="off">
				</p>
			</div>
			<?php
		}

		public function validate_fields() {
			$key = $this->id . '_trx_id';
			if ( empty( $_POST[ $key ] ) ) {
				wc_add_notice( sprintf( __( 'Please enter your %s transaction ID.', 'sutighar' ), $this->method_title ), 'error' );
				return false;
			}
			return true;
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			$key   = $this->id . '_trx_id';
			$trx   = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';

			$order->update_meta_data( '_sg_transaction_id', $trx );
			$order->update_status( 'on-hold', sprintf( __( '%s transaction pending manual confirmation. Transaction ID: %s', 'sutighar' ), $this->method_title, $trx ) );
			$order->save();

			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}
	}

	class Sutighar_Gateway_Bkash extends Sutighar_Gateway_Manual_Mobile {
		public function __construct() {
			$this->id                 = 'sutighar_bkash';
			$this->method_key         = 'bkash';
			$this->title              = __( 'bKash', 'sutighar' );
			$this->method_title       = __( 'bKash', 'sutighar' );
			$this->description        = __( 'Send to merchant, then enter transaction ID.', 'sutighar' );
			parent::__construct();
		}
	}

	class Sutighar_Gateway_Nagad extends Sutighar_Gateway_Manual_Mobile {
		public function __construct() {
			$this->id                 = 'sutighar_nagad';
			$this->method_key         = 'nagad';
			$this->title              = __( 'Nagad', 'sutighar' );
			$this->method_title       = __( 'Nagad', 'sutighar' );
			$this->description        = __( 'Send to merchant, then enter transaction ID.', 'sutighar' );
			parent::__construct();
		}
	}
}

sutighar_load_gateways();

add_action( 'woocommerce_admin_order_data_after_billing_address', 'sutighar_show_transaction_admin' );
function sutighar_show_transaction_admin( $order ) {
	$trx = $order->get_meta( '_sg_transaction_id' );
	if ( $trx ) {
		echo '<p><strong>' . esc_html__( 'Transaction ID', 'sutighar' ) . ':</strong> ' . esc_html( $trx ) . '</p>';
	}
}
