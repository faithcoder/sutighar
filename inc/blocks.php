<?php
/**
 * Native editor blocks for reusable Sutighar homepage sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'sutighar_register_blocks' );
function sutighar_register_blocks() {
	wp_register_script(
		'sutighar-blocks',
		SUTIGHAR_URI . '/assets/js/blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ),
		SUTIGHAR_VERSION,
		true
	);

	register_block_type(
		'sutighar/hero',
		array(
			'editor_script'   => 'sutighar-blocks',
			'render_callback' => 'sutighar_render_hero_block',
			'supports'        => array(
				'align' => array( 'full' ),
			),
			'attributes'      => array(
				'align'      => array( 'type' => 'string', 'default' => 'full' ),
				'title'      => array( 'type' => 'string', 'default' => 'Home of Quality Lungi' ),
				'buttonText' => array( 'type' => 'string', 'default' => 'Browse All Lungi' ),
				'buttonUrl'  => array( 'type' => 'string', 'default' => '' ),
				'imageId'    => array( 'type' => 'number', 'default' => 0 ),
				'imageUrl'   => array( 'type' => 'string', 'default' => '' ),
			),
		)
	);

	register_block_type(
		'sutighar/feature-cards',
		array(
			'editor_script'   => 'sutighar-blocks',
			'render_callback' => 'sutighar_render_feature_cards_block',
			'supports'        => array(
				'align' => array( 'full' ),
			),
			'attributes'      => array(
				'align'     => array( 'type' => 'string', 'default' => 'full' ),
				'cardOne'   => array( 'type' => 'string', 'default' => 'Hand-picked Collection' ),
				'cardTwo'   => array( 'type' => 'string', 'default' => 'Easy Return' ),
				'cardThree' => array( 'type' => 'string', 'default' => 'National Delivery' ),
				'cardFour'  => array( 'type' => 'string', 'default' => 'Safe Payment' ),
			),
		)
	);

	register_block_type(
		'sutighar/product-section',
		array(
			'editor_script'   => 'sutighar-blocks',
			'render_callback' => 'sutighar_render_product_section_block',
			'supports'        => array(
				'align' => array( 'full' ),
			),
			'attributes'      => array(
				'align'       => array( 'type' => 'string', 'default' => 'full' ),
				'title'       => array( 'type' => 'string', 'default' => 'New Arrival' ),
				'category'    => array( 'type' => 'string', 'default' => '' ),
				'limit'       => array( 'type' => 'number', 'default' => 8 ),
				'orderby'     => array( 'type' => 'string', 'default' => 'date' ),
				'order'       => array( 'type' => 'string', 'default' => 'DESC' ),
				'browseLabel' => array( 'type' => 'string', 'default' => 'Browse All' ),
			),
		)
	);

	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category( 'sutighar', array( 'label' => __( 'Sutighar', 'sutighar' ) ) );
	}

	if ( function_exists( 'register_block_pattern' ) ) {
		register_block_pattern(
			'sutighar/homepage',
			array(
				'title'       => __( 'Sutighar Homepage', 'sutighar' ),
				'description' => __( 'Hero, feature cards, new arrivals, and repeatable product category sections.', 'sutighar' ),
				'categories'  => array( 'sutighar' ),
				'content'     => sutighar_default_home_blocks(),
			)
		);
	}
}

add_filter( 'block_categories_all', 'sutighar_block_category', 10, 2 );
function sutighar_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'sutighar',
				'title' => __( 'Sutighar', 'sutighar' ),
				'icon'  => null,
			),
		)
	);
}

add_action( 'enqueue_block_editor_assets', 'sutighar_block_editor_data' );
function sutighar_block_editor_data() {
	$categories = array( array( 'label' => __( 'All products', 'sutighar' ), 'value' => '' ) );
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[] = array(
					'label' => $term->name,
					'value' => $term->slug,
				);
			}
		}
	}

	wp_add_inline_script(
		'sutighar-blocks',
		'window.sutigharBlockData = ' . wp_json_encode(
			array(
				'categories' => $categories,
				'shopUrl'    => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
				'heroUrl'    => sutighar_asset( 'assets/images/hero-desktop.webp' ),
			)
		) . ';',
		'before'
	);
}

function sutighar_render_hero_block( $attributes ) {
	$title            = ! empty( $attributes['title'] ) ? $attributes['title'] : 'Home of Quality Lungi';
	$subtitle         = isset( $attributes['subtitle'] ) ? trim( (string) $attributes['subtitle'] ) : 'Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort.';
	$button           = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Browse All Lungi';
	$button_url       = ! empty( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) );
	$image_url        = ! empty( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : sutighar_asset( 'assets/images/hero-desktop.webp' );
	$mobile_image_url = ! empty( $attributes['mobileImageUrl'] ) ? $attributes['mobileImageUrl'] : sutighar_asset( 'assets/images/hero-mobile.webp' );
	if ( ! empty( $attributes['imageId'] ) ) {
		$attachment = wp_get_attachment_image_url( (int) $attributes['imageId'], 'full' );
		if ( $attachment ) {
			$image_url = $attachment;
		}
	}
	if ( ! empty( $attributes['mobileImageId'] ) ) {
		$mobile_attachment = wp_get_attachment_image_url( (int) $attributes['mobileImageId'], 'full' );
		if ( $mobile_attachment ) {
			$mobile_image_url = $mobile_attachment;
		}
	}

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sg-hero alignfull' ) ); ?>>
		<picture>
			<source media="(max-width: 719px)" srcset="<?php echo esc_url( $mobile_image_url ); ?>">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" width="1440" height="597" loading="eager" fetchpriority="high" decoding="async">
		</picture>
		<div class="sg-hero__copy">
			<h1>
				<?php if ( 'Home of Quality Lungi' === $title ) : ?>
					<span><?php echo esc_html_x( 'Home of', 'Hero title first line', 'sutighar' ); ?></span>
					<span><?php echo esc_html_x( 'Quality Lungi', 'Hero title second line', 'sutighar' ); ?></span>
				<?php else : ?>
					<?php echo esc_html( $title ); ?>
				<?php endif; ?>
			</h1>
			<?php if ( '' !== $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<a class="sg-btn" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $button ); ?></a>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function sutighar_feature_icon( $index ) {
	$icons = array(
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2l2.1 1.5 2.6-.2.9 2.4 2.2 1.4-.7 2.5.7 2.5-2.2 1.4-.9 2.4-2.6-.2L12 17.4l-2.1 1.5-2.6-.2-.9-2.4-2.2-1.4.7-2.5-.7-2.5 2.2-1.4.9-2.4 2.6.2z"></path><path d="M9.2 10.6l2 2 3.6-3.6"></path></svg>',
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12a8 8 0 0 1-8 8"></path><path d="M4 12a8 8 0 0 1 8-8"></path><path d="M12 20l-2.4-2.4M12 20l2.4-2.4M12 4l2.4 2.4M12 4 9.6 6.4"></path><path d="M12 8.6l3 1.7v3.4l-3 1.7-3-1.7v-3.4z"></path></svg>',
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 17.5h5.5V6.5H6"></path><path d="M14.5 9.5H18l2 2.6v5.4h-1.8"></path><circle cx="7.4" cy="17.8" r="1.7"></circle><circle cx="16.6" cy="17.8" r="1.7"></circle><path d="M2 9h4M1 12h4.4M2.4 15H6"></path></svg>',
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10V7.6a1.6 1.6 0 0 0-1.6-1.6H4.6A1.6 1.6 0 0 0 3 7.6v8.8A1.6 1.6 0 0 0 4.6 18h8.9"></path><path d="M3 10.4h18"></path><path d="M17.6 13.2l3.2 1.1v2.1c0 1.4-1.3 2.5-3.2 3.1-1.9-.6-3.2-1.7-3.2-3.1v-2.1z"></path></svg>',
	);

	return $icons[ $index ] ?? $icons[0];
}

function sutighar_render_feature_cards_block( $attributes ) {
	$cards = array(
		array(
			'label' => $attributes['cardOne'] ?? 'Hand-picked Collection',
			'icon'  => 'solar_hand-heart-linear.svg',
		),
		array(
			'label' => $attributes['cardTwo'] ?? 'Easy Return',
			'icon'  => 'hugeicons_delivery-return-02.svg',
		),
		array(
			'label' => $attributes['cardThree'] ?? 'National Delivery',
			'icon'  => 'carbon_delivery.svg',
		),
		array(
			'label' => $attributes['cardFour'] ?? 'Safe Payment',
			'icon'  => 'bi_cash-coin.svg',
		),
	);
	if ( 'Premium Quality' === $cards[0]['label'] ) {
		$cards[0]['label'] = 'Hand-picked Collection';
	}

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sg-feature-band alignfull' ) ); ?>>
		<nav class="sg-mobile-category-menu sg-container" aria-label="<?php esc_attr_e( 'Product categories', 'sutighar' ); ?>">
			<?php foreach ( sutighar_categories() as $item ) : ?>
				<a class="sg-mobile-category-menu__item" href="<?php echo esc_url( $item['url'] ); ?>">
					<span class="sg-mobile-category-menu__thumb" style="background-image:url('<?php echo esc_url( sutighar_category_thumb_url( $item ) ); ?>')"></span>
					<span><?php echo esc_html( $item['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
		<div class="sg-feature-cards sg-container">
			<?php foreach ( $cards as $card ) : ?>
				<div class="sg-feature-card">
					<span class="sg-feature-card__icon"><?php echo sutighar_icon_img( $card['icon'], 'sg-feature-card__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php echo esc_html( $card['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<hr class="sg-rule">
	<?php
	return ob_get_clean();
}

function sutighar_render_product_section_block( $attributes ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$category = isset( $attributes['category'] ) ? sanitize_title( $attributes['category'] ) : '';
	$args     = array(
		'status'  => 'publish',
		'limit'   => isset( $attributes['limit'] ) ? max( 1, min( 24, absint( $attributes['limit'] ) ) ) : 8,
		'orderby' => sanitize_key( $attributes['orderby'] ?? 'date' ),
		'order'   => 'ASC' === ( $attributes['order'] ?? '' ) ? 'ASC' : 'DESC',
	);
	if ( $category ) {
		$args['category'] = array( $category );
	}

	$query    = new WC_Product_Query( $args );
	$products = $query->get_products();
	if ( ! $products ) {
		return '';
	}

	$title        = ! empty( $attributes['title'] ) ? $attributes['title'] : 'New Arrival';
	$browse_label = ! empty( $attributes['browseLabel'] ) ? $attributes['browseLabel'] : 'Browse All';
	$browse_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	if ( $category ) {
		$term = get_term_by( 'slug', $category, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$browse_url = get_term_link( $term );
		}
	}

	ob_start();
	?>
		<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sg-section sg-product-section-band alignfull' ) ); ?>>
		<div class="sg-container">
			<div class="sg-section__head">
				<h3><?php echo esc_html( $title ); ?></h3>
				<a class="sg-see-all" href="<?php echo esc_url( $browse_url ); ?>"><?php echo esc_html( $browse_label ); ?></a>
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
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function sutighar_default_home_blocks() {
	return '<!-- wp:sutighar/hero {"align":"full","title":"Home of Quality Lungi","subtitle":"Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort.","buttonText":"Browse All Lungi"} /-->' .
		'<!-- wp:sutighar/feature-cards /-->' .
		'<!-- wp:sutighar/product-section {"align":"full","title":"New Arrival","limit":8,"orderby":"date","order":"DESC"} /-->' .
		'<!-- wp:sutighar/product-section {"align":"full","title":"Solid","category":"solid","limit":4} /-->' .
		'<!-- wp:sutighar/product-section {"align":"full","title":"Stripe & Check","category":"stripe-check","limit":4} /-->' .
		'<!-- wp:sutighar/product-section {"align":"full","title":"Batik Print","category":"batik-print","limit":4} /-->';
}
