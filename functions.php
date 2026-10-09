<?php
/**
 * Sutighar theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUTIGHAR_VERSION', '1.0.0' );
define( 'SUTIGHAR_DIR', get_template_directory() );
define( 'SUTIGHAR_URI', get_template_directory_uri() );

require_once SUTIGHAR_DIR . '/inc/helpers.php';
require_once SUTIGHAR_DIR . '/inc/svg.php';
require_once SUTIGHAR_DIR . '/inc/wishlist.php';
require_once SUTIGHAR_DIR . '/inc/woocommerce.php';
require_once SUTIGHAR_DIR . '/inc/gateways.php';
require_once SUTIGHAR_DIR . '/inc/order-discounts.php';
require_once SUTIGHAR_DIR . '/inc/customizer.php';
require_once SUTIGHAR_DIR . '/inc/blocks.php';

add_action( 'after_setup_theme', 'sutighar_setup' );
function sutighar_setup() {
	load_theme_textdomain( 'sutighar', SUTIGHAR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 72,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_editor_style( array( 'assets/css/main.css', 'assets/css/woocommerce.css' ) );
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 570,
			'single_image_width'    => 1174,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 8,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 6,
			),
		)
	);
	register_nav_menus(
		array(
			'primary'        => __( 'Primary Header Menu', 'sutighar' ),
			'account_menu'   => __( 'Header Account Menu', 'sutighar' ),
			'footer_shop'    => __( 'Footer Shop', 'sutighar' ),
			'footer_company' => __( 'Footer Company', 'sutighar' ),
			'footer_connect' => __( 'Footer Connect', 'sutighar' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'sutighar_assets', 30 );
function sutighar_asset_version( $relative_path ) {
	$file = SUTIGHAR_DIR . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}
	return SUTIGHAR_VERSION;
}
function sutighar_assets() {
	wp_enqueue_style( 'sutighar-style', SUTIGHAR_URI . '/assets/css/main.css', array(), sutighar_asset_version( 'assets/css/main.css' ) );
	wp_enqueue_style( 'sutighar-woocommerce', SUTIGHAR_URI . '/assets/css/woocommerce.css', array( 'sutighar-style' ), sutighar_asset_version( 'assets/css/woocommerce.css' ) );
	wp_enqueue_script( 'sutighar-theme', SUTIGHAR_URI . '/assets/js/theme.js', array(), sutighar_asset_version( 'assets/js/theme.js' ), true );
	wp_localize_script(
		'sutighar-theme',
		'sutighar',
		array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'cartUrl'       => wc_get_cart_url(),
			'checkoutUrl'   => wc_get_checkout_url(),
			'wishlistNonce' => wp_create_nonce( 'sg_wishlist' ),
			'cartNonce'     => wp_create_nonce( 'sg_cart' ),
			'i18n'          => array(
				'added'   => __( 'Added', 'sutighar' ),
				'adding'  => __( 'Adding...', 'sutighar' ),
				'error'   => __( 'Please try again.', 'sutighar' ),
				'saved'   => __( 'Saved', 'sutighar' ),
				'removed' => __( 'Removed', 'sutighar' ),
			),
		)
	);
}

function sutighar_has_seo_meta_provider() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| has_action( 'wp_head', 'wpseo_head' )
		|| has_action( 'wp_head', 'rank_math_head' );
}

function sutighar_open_graph_image_url() {
	$image_id = 0;

	if ( is_singular() ) {
		$image_id = get_post_thumbnail_id( get_queried_object_id() );
	}

	if ( ! $image_id ) {
		$image_id = absint( sutighar_option( 'social_share_image', 0 ) );
	}

	if ( $image_id ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'full' );
		$image_mime_type = get_post_mime_type( $image_id );
		if ( $image_url && in_array( $image_mime_type, array( 'image/jpeg', 'image/png' ), true ) ) {
			return $image_url;
		}
	}

	return sutighar_asset( 'assets/images/hero-1440.png' );
}

function sutighar_open_graph_description() {
	if ( is_singular() ) {
		$description = get_the_excerpt( get_queried_object_id() );
		if ( $description ) {
			return wp_trim_words( wp_strip_all_tags( $description ), 30, '' );
		}
	}

	return get_bloginfo( 'description' );
}

function sutighar_open_graph_tags() {
	if ( is_admin() || is_feed() || is_robots() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || sutighar_has_seo_meta_provider() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = sutighar_open_graph_description();
	$image       = sutighar_open_graph_image_url();
	$url         = is_singular() ? get_permalink( get_queried_object_id() ) : home_url( '/' );
	$type        = is_singular( 'product' ) ? 'product' : 'website';

	printf( "\n<meta property=\"og:locale\" content=\"en_BD\">\n" );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
}
add_action( 'wp_head', 'sutighar_open_graph_tags', 5 );

add_action( 'widgets_init', 'sutighar_widgets_init' );
function sutighar_widgets_init() {
	$footer_widgets = array(
		'footer_1' => __( 'Footer Column 1', 'sutighar' ),
		'footer_2' => __( 'Footer Column 2', 'sutighar' ),
		'footer_3' => __( 'Footer Column 3', 'sutighar' ),
	);

	foreach ( $footer_widgets as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'description'   => __( 'Add Navigation Menu widgets or text blocks for the Sutighar footer.', 'sutighar' ),
				'before_widget' => '<div id="%1$s" class="sg-footer-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}
}

add_filter( 'body_class', 'sutighar_body_class' );
function sutighar_header_should_lock_compact() {
	if ( is_front_page() || is_home() || is_cart() || is_checkout() || is_page_template( 'page-wishlist.php' ) ) {
		return false;
	}

	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return false;
	}

	return true;
}

function sutighar_body_class( $classes ) {
	$classes[] = 'sutighar-theme';
	if ( sutighar_header_should_lock_compact() ) {
		$classes[] = 'sg-header-locked-compact';
	}
	return $classes;
}

add_filter( 'language_attributes', 'sutighar_language_attributes' );
function sutighar_language_attributes( $output ) {
	if ( false === strpos( $output, 'lang=' ) ) {
		return $output . ' lang="en-BD"';
	}

	return preg_replace( '/lang="[^"]*"/', 'lang="en-BD"', $output );
}

add_filter( 'excerpt_more', '__return_empty_string' );
