<?php
/**
 * Wishlist storage and AJAX endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sutighar_wishlist_ids() {
	if ( is_user_logged_in() ) {
		$ids = get_user_meta( get_current_user_id(), '_sg_wishlist', true );
		return array_values( array_filter( array_map( 'absint', (array) $ids ) ) );
	}

	if ( empty( $_COOKIE['sg_wishlist'] ) ) {
		return array();
	}

	$decoded = json_decode( wp_unslash( $_COOKIE['sg_wishlist'] ), true );
	return array_values( array_filter( array_map( 'absint', (array) $decoded ) ) );
}

function sutighar_wishlist_count() {
	return count( sutighar_wishlist_ids() );
}

function sutighar_wishlist_save( $ids ) {
	$ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $ids ) ) ) );
	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_sg_wishlist', $ids );
	}
	setcookie( 'sg_wishlist', wp_json_encode( $ids ), time() + MONTH_IN_SECONDS * 12, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), false );
	$_COOKIE['sg_wishlist'] = wp_json_encode( $ids );
}

add_action( 'wp_login', 'sutighar_merge_guest_wishlist', 10, 2 );
function sutighar_merge_guest_wishlist( $user_login, $user ) {
	$cookie_ids = array();
	if ( ! empty( $_COOKIE['sg_wishlist'] ) ) {
		$cookie_ids = json_decode( wp_unslash( $_COOKIE['sg_wishlist'] ), true );
	}
	$stored = get_user_meta( $user->ID, '_sg_wishlist', true );
	$ids    = array_unique( array_merge( (array) $stored, (array) $cookie_ids ) );
	update_user_meta( $user->ID, '_sg_wishlist', array_values( array_filter( array_map( 'absint', $ids ) ) ) );
}

add_action( 'wp_ajax_sg_wishlist_add', 'sutighar_ajax_wishlist_add' );
add_action( 'wp_ajax_nopriv_sg_wishlist_add', 'sutighar_ajax_wishlist_add' );
function sutighar_ajax_wishlist_add() {
	check_ajax_referer( 'sg_wishlist', 'nonce' );
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id || ! wc_get_product( $product_id ) ) {
		wp_send_json_error();
	}
	$ids   = sutighar_wishlist_ids();
	$ids[] = $product_id;
	sutighar_wishlist_save( $ids );
	wp_send_json_success( array( 'count' => sutighar_wishlist_count(), 'ids' => sutighar_wishlist_ids() ) );
}

add_action( 'wp_ajax_sg_wishlist_remove', 'sutighar_ajax_wishlist_remove' );
add_action( 'wp_ajax_nopriv_sg_wishlist_remove', 'sutighar_ajax_wishlist_remove' );
function sutighar_ajax_wishlist_remove() {
	check_ajax_referer( 'sg_wishlist', 'nonce' );
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$ids        = array_diff( sutighar_wishlist_ids(), array( $product_id ) );
	sutighar_wishlist_save( $ids );
	wp_send_json_success( array( 'count' => sutighar_wishlist_count(), 'ids' => sutighar_wishlist_ids() ) );
}
