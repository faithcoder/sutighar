<?php
/**
 * WooCommerce integration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_currency_symbol', 'sutighar_bdt_symbol', 10, 2 );
function sutighar_bdt_symbol( $symbol, $currency ) {
	return 'BDT' === $currency ? '৳' : $symbol;
}

add_filter( 'woocommerce_price_format', 'sutighar_price_format' );
function sutighar_price_format() {
	return '%1$s&nbsp;%2$s';
}

add_filter( 'woocommerce_get_price_decimals', '__return_zero' );
add_filter( 'woocommerce_placeholder_img_src', 'sutighar_woocommerce_placeholder_img_src' );
add_filter( 'woocommerce_product_add_to_cart_text', 'sutighar_add_to_cart_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'sutighar_add_to_cart_text' );
function sutighar_woocommerce_placeholder_img_src() {
	return sutighar_asset( 'assets/images/product-placeholder.svg' );
}
function sutighar_add_to_cart_text() {
	return __( 'Add to Cart', 'sutighar' );
}

add_filter( 'woocommerce_order_button_text', 'sutighar_order_button_text' );
function sutighar_order_button_text() {
	return __( 'Place Order →', 'sutighar' );
}

add_filter( 'woocommerce_checkout_required_field_notice', 'sutighar_checkout_required_field_notice', 10, 2 );
function sutighar_checkout_required_field_notice( $notice, $field_label ) {
	$field_label = preg_replace( '/^(Billing\s+)+/i', '', wp_strip_all_tags( $field_label ) );
	return sprintf(
		/* translators: %s: checkout field label. */
		__( '%s is a required field.', 'sutighar' ),
		'<strong>' . esc_html( $field_label ) . '</strong>'
	);
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
remove_action( 'woocommerce_cart_is_empty', 'woocommerce_output_all_notices', 5 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );

add_filter( 'woocommerce_add_to_cart_redirect', 'sutighar_buy_now_redirect' );
function sutighar_buy_now_redirect( $url ) {
	if ( isset( $_REQUEST['sg_buy_now'] ) ) {
		return wc_get_checkout_url();
	}

	return $url;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'sutighar_cart_fragments' );
function sutighar_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="sg-badge" data-sg-cart-count data-count="<?php echo esc_attr( WC()->cart->get_cart_contents_count() ); ?>"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
	<?php
	$fragments['[data-sg-cart-count]'] = ob_get_clean();
	$fragments['[data-sg-cart-total]'] = '<span class="sg-cart-total" data-sg-cart-total>' . WC()->cart->get_cart_subtotal() . '</span>';

	ob_start();
	sutighar_cart_modal_body();
	$fragments['[data-sg-cart-modal-body]'] = ob_get_clean();

	ob_start();
	sutighar_cart_modal_footer();
	$fragments['[data-sg-cart-modal-foot]'] = ob_get_clean();

	return $fragments;
}

add_filter( 'woocommerce_update_cart_validation', 'sutighar_limit_cart_quantity_to_stock', 10, 4 );
function sutighar_limit_cart_quantity_to_stock( $passed, $cart_item_key, $values, $quantity ) {
	$product = isset( $values['data'] ) ? $values['data'] : null;
	if ( ! $product instanceof WC_Product ) {
		return $passed;
	}

	$max_quantity = $product->get_max_purchase_quantity();
	if ( $max_quantity > 0 && $quantity > $max_quantity && WC()->cart ) {
		WC()->cart->set_quantity( $cart_item_key, $max_quantity, false );
		return false;
	}

	return $passed;
}

function sutighar_cart_modal_body() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		?>
		<div class="sg-cart-modal__empty">
			<p><?php esc_html_e( 'Your cart is currently empty.', 'sutighar' ); ?></p>
			<a class="sg-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse Products', 'sutighar' ); ?></a>
		</div>
		<?php
		return;
	}

	foreach ( WC()->cart->get_cart() as $cart_key => $item ) {
		$product = isset( $item['data'] ) ? $item['data'] : null;
		if ( ! $product instanceof WC_Product ) {
			continue;
		}
		$size         = sutighar_product_cart_size( $product );
		$stock_label  = sutighar_product_stock_label( $product );
		$max_quantity = $product->get_max_purchase_quantity();
		$input_max    = $max_quantity > 0 ? $max_quantity : '';
		$at_max       = $max_quantity > 0 && $item['quantity'] >= $max_quantity;
		?>
		<div class="sg-cart-modal__item" data-cart-key="<?php echo esc_attr( $cart_key ); ?>">
			<div class="sg-cart-modal__thumb"><?php echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<div class="sg-cart-modal__info">
				<div class="sg-cart-modal__top">
					<span class="sg-cart-modal__name"><?php echo esc_html( $product->get_name() ); ?></span>
					<button type="button" class="sg-cart-modal__remove" data-sg-cart-remove="<?php echo esc_attr( $cart_key ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'sutighar' ); ?>">×</button>
				</div>
				<span class="sg-cart-modal__variant"><?php echo esc_html( $size ); ?></span>
				<div class="sg-cart-modal__controls">
					<div class="sg-cart-modal__qty">
						<div class="quantity">
							<button type="button" class="sg-qty-btn" data-sg-qty="minus" data-sg-cart-qty="minus" data-key="<?php echo esc_attr( $cart_key ); ?>" aria-label="<?php esc_attr_e( 'Decrease quantity', 'sutighar' ); ?>">−</button>
							<input type="text" class="qty" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="1" <?php echo '' !== $input_max ? 'max="' . esc_attr( $input_max ) . '"' : ''; ?> readonly>
							<button type="button" class="sg-qty-btn" data-sg-qty="plus" data-sg-cart-qty="plus" data-key="<?php echo esc_attr( $cart_key ); ?>" <?php echo '' !== $input_max ? 'data-max="' . esc_attr( $input_max ) . '"' : ''; ?> aria-label="<?php esc_attr_e( 'Increase quantity', 'sutighar' ); ?>" <?php disabled( $at_max ); ?>>+</button>
						</div>
						<span class="sg-stock-line sg-cart-stock <?php echo esc_attr( $stock_label['class'] ); ?>"><?php echo esc_html( $stock_label['text'] ); ?></span>
					</div>
					<div class="sg-cart-modal__price"><?php echo esc_html( sutighar_bdt( isset( $item['line_subtotal'] ) ? $item['line_subtotal'] : $product->get_price() * $item['quantity'] ) ); ?></div>
				</div>
			</div>
		</div>
		<?php
	}
}

function sutighar_cart_modal_footer() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}

	$total = (float) WC()->cart->get_total( 'edit' );
	?>
	<a class="sg-cart-modal__confirm" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php echo esc_html( sprintf( __( 'Confirm Order - %s', 'sutighar' ), sutighar_bdt( $total ) ) ); ?></a>
	<button type="button" class="sg-cart-modal__viewcart" data-sg-cart-modal-close><?php esc_html_e( 'Continue Shopping', 'sutighar' ); ?></button>
	<?php
}

add_action( 'wp_ajax_sg_add_to_cart', 'sutighar_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_sg_add_to_cart', 'sutighar_ajax_add_to_cart' );
function sutighar_ajax_add_to_cart() {
	check_ajax_referer( 'sg_cart', 'nonce' );
	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$quantity     = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$variation    = array();

	foreach ( $_POST as $key => $value ) {
		if ( 0 === strpos( $key, 'attribute_' ) ) {
			$variation[ sanitize_key( $key ) ] = wc_clean( wp_unslash( $value ) );
		}
	}

	$product = wc_get_product( $variation_id ? $variation_id : $product_id );
	if ( ! $product || ! WC()->cart ) {
		wp_send_json_error();
	}

	$max_quantity = $product->get_max_purchase_quantity();
	if ( $max_quantity > 0 ) {
		$cart_id       = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation );
		$cart_key      = WC()->cart->find_product_in_cart( $cart_id );
		$cart_item     = $cart_key ? WC()->cart->get_cart_item( $cart_key ) : null;
		$cart_quantity = $cart_item && isset( $cart_item['quantity'] ) ? absint( $cart_item['quantity'] ) : 0;
		$remaining     = max( 0, $max_quantity - $cart_quantity );
		if ( 1 > $remaining ) {
			wp_send_json_error();
		}
		$quantity = min( $quantity, $remaining );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
	if ( ! $added ) {
		wp_send_json_error();
	}

	WC_AJAX::get_refreshed_fragments();
}

add_action( 'wp_ajax_sg_update_cart_item', 'sutighar_ajax_update_cart_item' );
add_action( 'wp_ajax_nopriv_sg_update_cart_item', 'sutighar_ajax_update_cart_item' );
function sutighar_ajax_update_cart_item() {
	check_ajax_referer( 'sg_cart', 'nonce' );
	$cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 0;

	if ( ! $cart_key || ! WC()->cart ) {
		wp_send_json_error();
	}

	$cart_item = WC()->cart->get_cart_item( $cart_key );
	$product   = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
	if ( ! $cart_item || ! $product instanceof WC_Product ) {
		wp_send_json_error();
	}

	$max_quantity = $product->get_max_purchase_quantity();
	if ( $max_quantity > 0 ) {
		$quantity = min( $quantity, $max_quantity );
	}

	if ( ! WC()->cart->set_quantity( $cart_key, $quantity ) ) {
		wp_send_json_error();
	}

	WC()->cart->calculate_totals();
	WC_AJAX::get_refreshed_fragments();
}

add_action( 'wp_ajax_sg_remove_cart_item', 'sutighar_ajax_remove_cart_item' );
add_action( 'wp_ajax_nopriv_sg_remove_cart_item', 'sutighar_ajax_remove_cart_item' );
function sutighar_ajax_remove_cart_item() {
	check_ajax_referer( 'sg_cart', 'nonce' );
	$cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';

	if ( ! $cart_key || ! WC()->cart->remove_cart_item( $cart_key ) ) {
		wp_send_json_error();
	}

	WC()->cart->calculate_totals();
	WC_AJAX::get_refreshed_fragments();
}

add_action( 'wp_enqueue_scripts', 'sutighar_enqueue_product_scripts' );
function sutighar_enqueue_product_scripts() {
	if ( is_product() ) {
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}
}

function sutighar_home_products_section( $title, $args, $url = '' ) {
	$defaults = array(
		'status' => 'publish',
		'limit'  => 4,
	);
	$query    = new WC_Product_Query( wp_parse_args( $args, $defaults ) );
	$products = $query->get_products();
	if ( empty( $products ) ) {
		return;
	}
	if ( ! $url ) {
		$url = wc_get_page_permalink( 'shop' );
	}
	?>
	<section class="sg-section sg-container">
		<div class="sg-section__head">
			<h3><?php echo esc_html( $title ); ?></h3>
			<a class="sg-see-all" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Browse All', 'sutighar' ); ?></a>
		</div>
		<ul class="products sg-product-grid">
			<?php
			foreach ( $products as $product ) {
				$GLOBALS['product'] = $product;
				$GLOBALS['post']    = get_post( $product->get_id() );
				if ( $GLOBALS['post'] ) {
					setup_postdata( $GLOBALS['post'] );
				}
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</section>
	<?php
}

add_action( 'woocommerce_before_shop_loop', 'sutighar_catalog_toolbar', 20 );
function sutighar_catalog_toolbar() {
	$has_orderby         = isset( $_GET['orderby'] );
	$orderby             = $has_orderby ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : get_option( 'woocommerce_default_catalog_orderby', 'menu_order' );
	$selected_option     = $has_orderby ? $orderby : '';
	$show_category       = sutighar_option_enabled( 'enable_filter_category', true );
	$show_size           = sutighar_option_enabled( 'enable_filter_size', true );
	$show_availability   = sutighar_option_enabled( 'enable_filter_availability', true );
	$enabled_filter_keys = array_filter(
		array(
			'product_cat'  => $show_category,
			'filter_size'  => $show_size,
			'stock_status' => $show_availability,
		)
	);
	?>
	<div class="sg-toolbar">
		<div class="sg-pop">
			<button class="sg-pill sg-pill--filter" type="button" data-sg-pop-toggle="filter" aria-expanded="false" aria-controls="sg-filter-panel">
				<?php echo sutighar_icon_img( 'mynaui_filter.svg', 'sg-filter-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Filter', 'sutighar' ); ?></span>
			</button>
			<form id="sg-filter-panel" class="sg-filter-panel" data-sg-popover="filter" method="get">
				<?php if ( $show_category ) : ?>
					<fieldset>
						<legend><?php esc_html_e( 'Category', 'sutighar' ); ?></legend>
						<?php foreach ( sutighar_categories() as $slug => $item ) : ?>
							<?php if ( 'all' === $slug ) { continue; } ?>
							<label><input type="checkbox" name="product_cat[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, (array) ( $_GET['product_cat'] ?? array() ), true ) ); ?>><?php echo esc_html( $item['label'] ); ?></label>
						<?php endforeach; ?>
					</fieldset>
				<?php endif; ?>
				<?php if ( $show_category && ( $show_size || $show_availability ) ) : ?>
					<hr>
				<?php endif; ?>
				<?php if ( $show_size ) : ?>
					<fieldset>
						<legend><?php esc_html_e( 'Size', 'sutighar' ); ?></legend>
						<?php foreach ( array( 'kids' => 'Kids', '5-haat' => '5 Haat', '5-5-haat' => '5.5 Haat', '6-haat' => '6 Haat' ) as $slug => $label ) : ?>
							<label><input type="checkbox" name="filter_size[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, (array) ( $_GET['filter_size'] ?? array() ), true ) ); ?>><?php echo esc_html( $label ); ?></label>
						<?php endforeach; ?>
					</fieldset>
				<?php endif; ?>
				<?php if ( $show_size && $show_availability ) : ?>
					<hr>
				<?php endif; ?>
				<?php if ( $show_availability ) : ?>
					<fieldset>
						<legend><?php esc_html_e( 'Availability', 'sutighar' ); ?></legend>
						<label><input type="checkbox" name="stock_status[]" value="instock" <?php checked( in_array( 'instock', (array) ( $_GET['stock_status'] ?? array() ), true ) ); ?>><?php esc_html_e( 'In stock', 'sutighar' ); ?></label>
						<label><input type="checkbox" name="stock_status[]" value="outofstock" <?php checked( in_array( 'outofstock', (array) ( $_GET['stock_status'] ?? array() ), true ) ); ?>><?php esc_html_e( 'Out of stock', 'sutighar' ); ?></label>
					</fieldset>
					<hr>
				<?php endif; ?>
				<fieldset>
					<legend><?php esc_html_e( 'Price', 'sutighar' ); ?></legend>
					<div style="display:flex;gap:8px">
						<input type="number" name="min_price" value="<?php echo esc_attr( $_GET['min_price'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Min', 'sutighar' ); ?>">
						<input type="number" name="max_price" value="<?php echo esc_attr( $_GET['max_price'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Max', 'sutighar' ); ?>">
					</div>
				</fieldset>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
				<button class="sg-btn sg-btn--full" type="submit" style="margin-top:16px"><?php esc_html_e( 'Apply', 'sutighar' ); ?></button>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="display:block;margin-top:12px;font-size:12px"><?php esc_html_e( 'Clear all', 'sutighar' ); ?></a>
			</form>
		</div>
		<div class="sg-density" data-sg-density>
			<button type="button" data-layout="list" aria-label="<?php esc_attr_e( 'List view', 'sutighar' ); ?>">
				<span class="sg-density__list" aria-hidden="true"><span></span><span></span><span></span></span>
			</button>
			<?php foreach ( array( 2, 3, 4, 6, 8 ) as $cols ) : ?>
				<button type="button" data-layout="grid" data-cols="<?php echo esc_attr( $cols ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%d columns', 'sutighar' ), $cols ) ); ?>" class="<?php echo 4 === $cols ? 'is-active' : ''; ?>">
					<span class="sg-density__dots" style="--sg-density-cols: <?php echo esc_attr( $cols ); ?>" aria-hidden="true"><?php echo str_repeat( '<span></span>', $cols * 2 ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
		<form class="sg-toolbar__sort" method="get">
			<?php foreach ( $_GET as $key => $value ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<?php if ( 'orderby' === $key ) { continue; } ?>
				<?php if ( in_array( $key, array( 'product_cat', 'filter_size', 'stock_status' ), true ) && ! isset( $enabled_filter_keys[ $key ] ) ) { continue; } ?>
				<?php if ( is_array( $value ) ) : ?>
					<?php foreach ( $value as $item ) : ?>
						<input type="hidden" name="<?php echo esc_attr( sanitize_key( $key ) ); ?>[]" value="<?php echo esc_attr( wc_clean( wp_unslash( $item ) ) ); ?>">
					<?php endforeach; ?>
				<?php else : ?>
					<input type="hidden" name="<?php echo esc_attr( sanitize_key( $key ) ); ?>" value="<?php echo esc_attr( wc_clean( wp_unslash( $value ) ) ); ?>">
				<?php endif; ?>
			<?php endforeach; ?>
			<label class="screen-reader-text" for="sg-catalog-orderby"><?php esc_html_e( 'Sort products', 'sutighar' ); ?></label>
			<span class="sg-sort-select">
				<select id="sg-catalog-orderby" name="orderby" onchange="this.form.submit()">
					<option value="<?php echo esc_attr( $orderby ); ?>" <?php selected( $selected_option, '' ); ?>><?php esc_html_e( 'Sort by', 'sutighar' ); ?></option>
				<?php foreach ( sutighar_catalog_ordering_options() as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $selected_option, $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
				</select>
				<?php echo sutighar_icon_img( 'group-chevron-down.svg', 'sg-sort-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</form>
	</div>
	<?php
}

function sutighar_catalog_ordering_options() {
	return array(
		'menu_order' => __( 'Featured', 'sutighar' ),
		'popularity' => __( 'Best selling', 'sutighar' ),
		'price'      => __( 'Price, low to high', 'sutighar' ),
		'price-desc' => __( 'Price, high to low', 'sutighar' ),
		'date-asc'   => __( 'Date, old to new', 'sutighar' ),
		'date'       => __( 'Date, new to old', 'sutighar' ),
	);
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'sutighar_catalog_ordering_args', 20, 3 );
function sutighar_catalog_ordering_args( $args, $orderby, $order ) {
	if ( 'date-asc' === $orderby ) {
		$args['orderby'] = 'date';
		$args['order']   = 'ASC';
	}

	return $args;
}

add_action( 'woocommerce_product_query', 'sutighar_apply_catalog_filters' );
function sutighar_apply_catalog_filters( $query ) {
	$tax_query  = (array) $query->get( 'tax_query' );
	$meta_query = (array) $query->get( 'meta_query' );

	if ( sutighar_option_enabled( 'enable_filter_category', true ) && ! empty( $_GET['product_cat'] ) && is_array( $_GET['product_cat'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array_map( 'sanitize_title', wp_unslash( $_GET['product_cat'] ) ),
		);
	}

	if ( sutighar_option_enabled( 'enable_filter_size', true ) && ! empty( $_GET['filter_size'] ) && is_array( $_GET['filter_size'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'pa_size',
			'field'    => 'slug',
			'terms'    => array_map( 'sanitize_title', wp_unslash( $_GET['filter_size'] ) ),
		);
	}

	if ( sutighar_option_enabled( 'enable_filter_availability', true ) && ! empty( $_GET['stock_status'] ) && is_array( $_GET['stock_status'] ) ) {
		$meta_query[] = array(
			'key'     => '_stock_status',
			'value'   => array_map( 'sanitize_key', wp_unslash( $_GET['stock_status'] ) ),
			'compare' => 'IN',
		);
	}

	$query->set( 'tax_query', $tax_query );
	$query->set( 'meta_query', $meta_query );
}

add_filter( 'woocommerce_product_data_tabs', 'sutighar_product_data_tab' );
function sutighar_product_data_tab( $tabs ) {
	$tabs['sutighar'] = array(
		'label'    => __( 'Sutighar Data', 'sutighar' ),
		'target'   => 'sutighar_product_data',
		'class'    => array(),
		'priority' => 21,
	);

	return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'sutighar_product_meta_fields' );
function sutighar_product_meta_fields() {
	echo '<div id="sutighar_product_data" class="panel woocommerce_options_panel hidden">';
	echo '<div class="options_group">';
	woocommerce_wp_text_input(
		array(
			'id'          => '_sg_size',
			'label'       => __( 'Sutighar size', 'sutighar' ),
			'placeholder' => '5 Haat',
			'desc_tip'    => true,
			'description' => __( 'Displayed as read-only Size on the product page.', 'sutighar' ),
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_sg_height',
			'label'       => __( 'Sutighar height', 'sutighar' ),
			'placeholder' => '51',
			'type'        => 'number',
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_sg_waist',
			'label'       => __( 'Sutighar waist', 'sutighar' ),
			'placeholder' => '98',
			'type'        => 'number',
		)
	);
	echo '</div><div class="options_group">';
	$defaults = sutighar_default_specs();
	foreach ( array( 'Brand', 'Fabric', 'Mercerized', 'Loom Type', 'Border', 'Wash Type' ) as $label ) {
		$key = '_sg_spec_' . sanitize_key( str_replace( ' ', '_', strtolower( $label ) ) );
		woocommerce_wp_text_input(
			array(
				'id'          => $key,
				'label'       => sprintf( __( 'Spec: %s', 'sutighar' ), $label ),
				'placeholder' => $defaults[ $label ] ?? '',
				'description' => __( 'Leave blank to hide this row on the product page.', 'sutighar' ),
				'desc_tip'    => true,
			)
		);
	}
	echo '</div></div>';
}

add_action( 'woocommerce_admin_process_product_object', 'sutighar_save_product_meta_fields' );
function sutighar_save_product_meta_fields( $product ) {
	$keys = array( '_sg_size', '_sg_height', '_sg_waist' );
	foreach ( array( 'Brand', 'Fabric', 'Mercerized', 'Loom Type', 'Border', 'Wash Type' ) as $label ) {
		$keys[] = '_sg_spec_' . sanitize_key( str_replace( ' ', '_', strtolower( $label ) ) );
	}

	foreach ( $keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$product->update_meta_data( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}

add_filter( 'woocommerce_checkout_fields', 'sutighar_checkout_fields' );
function sutighar_checkout_fields( $fields ) {
	$fields['billing'] = array(
		'billing_first_name' => array(
			'type'         => 'text',
			'label'        => __( 'Full name', 'sutighar' ),
			'placeholder'  => __( 'Full name', 'sutighar' ),
			'required'     => true,
			'class'        => array( 'form-row-wide' ),
			'autocomplete' => 'name',
			'priority'     => 10,
		),
		'billing_phone'      => array(
			'type'         => 'tel',
			'label'        => __( 'Phone', 'sutighar' ),
			'placeholder'  => __( 'Phone', 'sutighar' ),
			'required'     => true,
			'class'        => array( 'form-row-first' ),
			'autocomplete' => 'tel',
			'priority'     => 20,
		),
		'billing_email'      => array(
			'type'         => 'email',
			'label'        => __( 'Email (optional)', 'sutighar' ),
			'placeholder'  => __( 'Email (optional)', 'sutighar' ),
			'required'     => false,
			'class'        => array( 'form-row-last' ),
			'autocomplete' => 'email',
			'priority'     => 30,
		),
		'billing_address_1'  => array(
			'type'        => 'textarea',
			'label'       => __( 'Delivery address', 'sutighar' ),
			'placeholder' => __( 'Delivery address', 'sutighar' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
			'priority'    => 40,
			'custom_attributes' => array(
				'rows' => 3,
			),
		),
		'billing_city'       => array(
			'type'        => 'text',
			'label'       => __( 'Thana', 'sutighar' ),
			'placeholder' => __( 'Thana', 'sutighar' ),
			'required'    => true,
			'class'       => array( 'form-row-last' ),
			'priority'    => 60,
		),
		'billing_state'      => array(
			'type'        => 'select',
			'label'       => __( 'District', 'sutighar' ),
			'required'    => true,
			'class'       => array( 'form-row-first' ),
			'options'     => sutighar_checkout_district_options(),
			'priority'    => 50,
		),
	);

	$fields['shipping'] = array();
	$fields['account']  = array();
	$fields['order']    = array(
		'order_comments' => array(
			'type'        => 'textarea',
			'label'       => __( 'Additional Notes', 'sutighar' ),
			'placeholder' => __( 'Additional Notes', 'sutighar' ),
			'required'    => false,
			'class'       => array( 'form-row-wide' ),
			'priority'    => 15,
			'custom_attributes' => array(
				'rows' => 2,
			),
		),
	);

	return $fields;
}

function sutighar_checkout_district_options() {
	$districts = array(
		'Bagerhat',
		'Bandarban',
		'Barguna',
		'Barishal',
		'Bhola',
		'Bogura',
		'Brahmanbaria',
		'Chandpur',
		'Chapai Nawabganj',
		'Chattogram',
		'Chuadanga',
		'Cox\'s Bazar',
		'Cumilla',
		'Dhaka',
		'Dinajpur',
		'Faridpur',
		'Feni',
		'Gaibandha',
		'Gazipur',
		'Gopalganj',
		'Habiganj',
		'Jamalpur',
		'Jashore',
		'Jhalokati',
		'Jhenaidah',
		'Joypurhat',
		'Khagrachhari',
		'Khulna',
		'Kishoreganj',
		'Kurigram',
		'Kushtia',
		'Lakshmipur',
		'Lalmonirhat',
		'Madaripur',
		'Magura',
		'Manikganj',
		'Meherpur',
		'Moulvibazar',
		'Munshiganj',
		'Mymensingh',
		'Naogaon',
		'Narail',
		'Narayanganj',
		'Narsingdi',
		'Natore',
		'Netrokona',
		'Nilphamari',
		'Noakhali',
		'Pabna',
		'Panchagarh',
		'Patuakhali',
		'Pirojpur',
		'Rajbari',
		'Rajshahi',
		'Rangamati',
		'Rangpur',
		'Satkhira',
		'Shariatpur',
		'Sherpur',
		'Sirajganj',
		'Sunamganj',
		'Sylhet',
		'Tangail',
		'Thakurgaon',
	);
	$options   = array( '' => __( 'Select District', 'sutighar' ) );

	foreach ( $districts as $district ) {
		$options[ sanitize_title( $district ) ] = $district;
	}

	return $options;
}

function sutighar_selected_shipping_option() {
	$district = '';

	if ( isset( $_POST['billing_state'] ) ) {
		$district = sanitize_title( wp_unslash( $_POST['billing_state'] ) );
	} elseif ( isset( $_POST['post_data'] ) ) {
		parse_str( wp_unslash( $_POST['post_data'] ), $posted );
		if ( isset( $posted['billing_state'] ) ) {
			$district = sanitize_title( $posted['billing_state'] );
		}
	}

	if ( '' === $district ) {
		return '';
	}

	return 'dhaka' === $district ? 'inside_dhaka' : 'outside_dhaka';
}

function sutighar_shipping_option_fee( $option ) {
	if ( 'outside_dhaka' === $option ) {
		return (float) sutighar_option( 'outside_dhaka_shipping_fee', '120' );
	}
	if ( 'inside_dhaka' !== $option ) {
		return 0;
	}

	return (float) sutighar_option( 'shipping_fee', '80' );
}

add_action( 'woocommerce_cart_calculate_fees', 'sutighar_checkout_shipping_option_fee' );
function sutighar_checkout_shipping_option_fee( $cart ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! is_checkout() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! $cart || $cart->is_empty() ) {
		return;
	}

	$option = sutighar_selected_shipping_option();
	$fee    = sutighar_shipping_option_fee( $option );
	$label  = 'outside_dhaka' === $option ? __( 'Shipping - Outside Dhaka', 'sutighar' ) : __( 'Shipping - Inside Dhaka', 'sutighar' );

	if ( $fee > 0 ) {
		$cart->add_fee( $label, $fee, false );
	}
}

add_filter( 'woocommerce_available_payment_gateways', 'sutighar_payment_gateway_copy' );
function sutighar_payment_gateway_copy( $gateways ) {
	if ( isset( $gateways['cod'] ) ) {
		$gateways['cod']->title       = __( 'Cash on Delivery', 'sutighar' );
		$gateways['cod']->description = __( 'Pay in cash when the courier delivers. Please keep exact change ready.', 'sutighar' );
	}
	if ( isset( $gateways['sutighar_bkash'] ) ) {
		$gateways['sutighar_bkash']->description = __( 'Send to merchant, then enter transaction ID.', 'sutighar' );
	}
	if ( isset( $gateways['sutighar_nagad'] ) ) {
		$gateways['sutighar_nagad']->description = __( 'Send to merchant, then enter transaction ID.', 'sutighar' );
	}

	return $gateways;
}

add_action( 'woocommerce_checkout_create_order', 'sutighar_store_checkout_field_snapshot', 20, 2 );
function sutighar_store_checkout_field_snapshot( $order, $data ) {
	if ( ! function_exists( 'WC' ) || ! WC()->checkout() ) {
		return;
	}

	$first_name = isset( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '';
	$address   = isset( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '';
	$city      = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
	$district  = isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '';
	$districts = sutighar_checkout_district_options();
	$district  = isset( $districts[ $district ] ) ? $districts[ $district ] : $district;

	if ( $first_name ) {
		$order->set_shipping_first_name( $first_name );
	}
	if ( $address ) {
		$order->set_shipping_address_1( $address );
	}
	if ( $city ) {
		$order->set_shipping_city( $city );
	}
	if ( $district ) {
		$order->set_billing_state( $district );
		$order->set_shipping_state( $district );
	}
	$order->set_billing_country( 'BD' );
	$order->set_shipping_country( 'BD' );

	$snapshot = array();
	foreach ( WC()->checkout()->get_checkout_fields() as $group => $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			$value = wc_clean( wp_unslash( $_POST[ $key ] ) );
			if ( is_array( $value ) ) {
				$value = implode( ', ', array_filter( array_map( 'sanitize_text_field', $value ) ) );
			}
			if ( 'billing_state' === $key ) {
				$districts = sutighar_checkout_district_options();
				$value     = isset( $districts[ $value ] ) ? $districts[ $value ] : $value;
			}
			if ( '' === $value ) {
				continue;
			}

			$label = ! empty( $field['label'] ) ? $field['label'] : wc_attribute_label( $key );
			$snapshot[ $key ] = array(
				'label' => wp_strip_all_tags( $label ),
				'value' => wp_strip_all_tags( $value ),
				'group' => sanitize_key( $group ),
			);
		}
	}

	if ( $snapshot ) {
		$order->update_meta_data( '_sg_checkout_fields', $snapshot );
	}
}

function sutighar_order_checkout_fields( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return array();
	}

	$fields = $order->get_meta( '_sg_checkout_fields' );
	return is_array( $fields ) ? $fields : array();
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'sutighar_admin_checkout_fields' );
function sutighar_admin_checkout_fields( $order ) {
	$fields = sutighar_order_checkout_fields( $order );
	if ( ! $fields ) {
		return;
	}

	echo '<div class="address"><p><strong>' . esc_html__( 'Checkout fields', 'sutighar' ) . '</strong></p>';
	foreach ( $fields as $field ) {
		printf( '<p><strong>%s:</strong> %s</p>', esc_html( $field['label'] ), esc_html( $field['value'] ) );
	}
	echo '</div>';
}

add_filter( 'woocommerce_email_order_meta_fields', 'sutighar_email_checkout_fields', 10, 3 );
function sutighar_email_checkout_fields( $fields, $sent_to_admin, $order ) {
	foreach ( sutighar_order_checkout_fields( $order ) as $key => $field ) {
		$fields[ 'sg_' . sanitize_key( $key ) ] = array(
			'label' => $field['label'],
			'value' => $field['value'],
		);
	}

	$trx = $order instanceof WC_Order ? $order->get_meta( '_sg_transaction_id' ) : '';
	if ( $trx ) {
		$fields['sg_transaction_id'] = array(
			'label' => __( 'Transaction ID', 'sutighar' ),
			'value' => $trx,
		);
	}

	return $fields;
}

function sutighar_default_shop_links() {
	$links = array();
	foreach ( sutighar_categories() as $item ) {
		$links[] = array( $item['label'], $item['url'] );
	}
	return $links;
}

function sutighar_default_company_links() {
	return array(
		array( __( 'About Us', 'sutighar' ), home_url( '/about/' ) ),
		array( __( 'Contact', 'sutighar' ), home_url( '/contact/' ) ),
		array( __( 'Return & Exchange', 'sutighar' ), home_url( '/return-exchange/' ) ),
		array( __( 'Privacy policy', 'sutighar' ), home_url( '/privacy-policy/' ) ),
		array( __( 'Terms of Service', 'sutighar' ), home_url( '/terms-of-service/' ) ),
	);
}

function sutighar_default_connect_links() {
	return array(
		array( __( 'WhatsApp', 'sutighar' ), sutighar_whatsapp_url() ),
		array( __( 'Instagram', 'sutighar' ), 'https://instagram.com/sutighar' ),
		array( __( 'Facebook', 'sutighar' ), 'https://facebook.com/sutighar' ),
	);
}

function sutighar_footer_menu( $location, $fallback ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'depth'          => 1,
			)
		);
		return;
	}
	echo '<ul>';
	foreach ( $fallback as $link ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $link[1] ), esc_html( $link[0] ) );
	}
	echo '</ul>';
}
