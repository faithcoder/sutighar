<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>
<section class="sg-page sg-container sg-cart-page">
	<h1 class="entry-title"><?php esc_html_e( 'Your Cart', 'sutighar' ); ?></h1>
	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>
		<?php if ( WC()->cart->is_empty() ) : ?>
			<div class="sg-empty">
				<?php echo sutighar_inline_icon( 'cart' ); ?>
				<h2><?php esc_html_e( 'Your cart is empty', 'sutighar' ); ?></h2>
				<p><?php esc_html_e( 'Add a few carefully woven pieces to get started.', 'sutighar' ); ?></p>
				<a class="sg-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the collection', 'sutighar' ); ?></a>
			</div>
		<?php else : ?>
			<div class="sg-cart-layout">
				<div class="sg-cart-items">
					<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
						<?php
						$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
							continue;
						}
						$stock_label = sutighar_product_stock_label( $product );
						?>
						<div class="sg-cart-item">
							<a class="sg-cart-item__image" href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
							</a>
							<div class="sg-cart-item__body">
								<a class="sg-cart-item__name" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
								<div class="sg-cart-item__meta"><?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?> <?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?> <?php esc_html_e( 'each', 'sutighar' ); ?></div>
								<span class="sg-stock-line sg-cart-stock <?php echo esc_attr( $stock_label['class'] ); ?>"><?php echo esc_html( $stock_label['text'] ); ?></span>
								<div class="sg-cart-item__actions">
									<?php
									woocommerce_quantity_input(
										array(
											'input_name'  => "cart[{$cart_item_key}][qty]",
											'input_value' => $cart_item['quantity'],
											'min_value'   => 0,
											'max_value'   => $product->get_max_purchase_quantity(),
										),
										$product
									);
									?>
									<?php
									echo apply_filters(
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="sg-remove" aria-label="%s">%s</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_attr__( 'Remove this item', 'sutighar' ),
											esc_html__( 'Remove', 'sutighar' )
										),
										$cart_item_key
									);
									?>
								</div>
							</div>
							<div class="sg-cart-item__price"><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ) ); ?></div>
						</div>
					<?php endforeach; ?>
					<button type="submit" class="sg-btn sg-cart-update" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'sutighar' ); ?>"><?php esc_html_e( 'Update cart', 'sutighar' ); ?></button>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>
				<?php wc_get_template( 'cart/cart-totals.php' ); ?>
			</div>
		<?php endif; ?>
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>
</section>
<?php do_action( 'woocommerce_after_cart' ); ?>
