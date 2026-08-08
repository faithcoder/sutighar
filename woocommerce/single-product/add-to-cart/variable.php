<?php
defined( 'ABSPATH' ) || exit;

global $product;

$available_variations = $product->get_available_variations();
$attributes           = $product->get_variation_attributes();
$selected_attributes  = $product->get_default_attributes();
$chosen_variation     = ! empty( $available_variations[0] ) ? $available_variations[0] : array();

foreach ( $available_variations as $variation ) {
	if ( empty( $variation['attributes'] ) ) {
		continue;
	}
	$matches = true;
	foreach ( $selected_attributes as $key => $value ) {
		$attribute_key = 'attribute_' . sanitize_title( $key );
		if ( $value && isset( $variation['attributes'][ $attribute_key ] ) && $variation['attributes'][ $attribute_key ] !== $value ) {
			$matches = false;
			break;
		}
	}
	if ( $matches ) {
		$chosen_variation = $variation;
		break;
	}
}

$variation_obj = ! empty( $chosen_variation['variation_id'] ) ? wc_get_product( $chosen_variation['variation_id'] ) : null;
$stock_qty     = $variation_obj ? $variation_obj->get_stock_quantity() : $product->get_stock_quantity();
$in_stock      = $variation_obj ? $variation_obj->is_in_stock() : $product->is_in_stock();
?>
<form class="variations_form cart sg-cart-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo wc_esc_json( wp_json_encode( $available_variations ) ); ?>">
	<div hidden>
		<?php foreach ( $attributes as $attribute_name => $options ) : ?>
			<?php
			$attribute_key = 'attribute_' . sanitize_title( $attribute_name );
			$value         = $chosen_variation['attributes'][ $attribute_key ] ?? ( $selected_attributes[ sanitize_title( $attribute_name ) ] ?? reset( $options ) );
			?>
			<input type="hidden" name="<?php echo esc_attr( $attribute_key ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php endforeach; ?>
	</div>
	<div class="sg-quantity-block">
		<span class="sg-meta"><?php esc_html_e( 'Quantity', 'sutighar' ); ?></span>
		<?php woocommerce_quantity_input( array( 'min_value' => 1, 'input_value' => 1 ) ); ?>
		<div class="sg-stock-row">
			<span class="sg-stock-line"><?php echo $in_stock ? esc_html( $stock_qty ? sprintf( __( '%d Item Left', 'sutighar' ), $stock_qty ) : __( 'In stock', 'sutighar' ) ) : esc_html__( 'Sold out', 'sutighar' ); ?></span>
			<button type="button" class="sg-size-chart-link" data-sg-size-chart><?php esc_html_e( 'Size Chart', 'sutighar' ); ?></button>
		</div>
	</div>
	<div class="sg-buy-row">
		<button type="submit" name="sg_buy_now" value="1" class="sg-btn" <?php disabled( ! $in_stock ); ?>><?php esc_html_e( 'Buy Now', 'sutighar' ); ?></button>
		<button type="button" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" class="sg-add-to-cart-button button alt" data-sg-ajax-cart <?php disabled( ! $in_stock ); ?>><?php esc_html_e( 'Add to Cart', 'sutighar' ); ?></button>
	</div>
	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
	<input type="hidden" name="variation_id" class="variation_id" value="<?php echo esc_attr( $chosen_variation['variation_id'] ?? 0 ); ?>">
</form>
<?php sutighar_size_chart_modal(); ?>
