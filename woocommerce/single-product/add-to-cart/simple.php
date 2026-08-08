<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

if ( $product->is_in_stock() ) : ?>
	<form class="cart sg-cart-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
		<div class="sg-quantity-block">
			<span class="sg-meta"><?php esc_html_e( 'Quantity', 'sutighar' ); ?></span>
			<?php woocommerce_quantity_input( array( 'min_value' => 1, 'max_value' => $product->get_max_purchase_quantity(), 'input_value' => 1 ) ); ?>
			<div class="sg-stock-row">
				<span class="sg-stock-line"><?php echo $product->get_stock_quantity() ? esc_html( sprintf( __( '%d Item Left', 'sutighar' ), $product->get_stock_quantity() ) ) : esc_html__( 'In stock', 'sutighar' ); ?></span>
				<button type="button" class="sg-size-chart-link" data-sg-size-chart><?php esc_html_e( 'Size Chart', 'sutighar' ); ?></button>
			</div>
		</div>
		<div class="sg-buy-row">
			<button type="submit" name="sg_buy_now" value="1" class="sg-btn"><?php esc_html_e( 'Buy Now', 'sutighar' ); ?></button>
			<button type="button" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="sg-add-to-cart-button button alt" data-sg-ajax-cart><?php esc_html_e( 'Add to Cart', 'sutighar' ); ?></button>
		</div>
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
	</form>
	<?php sutighar_size_chart_modal(); ?>
<?php endif; ?>
