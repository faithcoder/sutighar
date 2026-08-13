<?php
/**
 * SVG upload support for trusted branding assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sutighar_user_can_upload_svg() {
	return current_user_can( 'upload_files' ) && current_user_can( 'edit_theme_options' );
}

function sutighar_is_svg_file( $file ) {
	if ( ! is_readable( $file ) ) {
		return false;
	}

	$contents = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $contents ) {
		return false;
	}

	return false !== stripos( $contents, '<svg' );
}

function sutighar_sanitize_svg_markup( $svg ) {
	$blocked_patterns = array(
		'/<\s*script\b/i',
		'/<\s*(iframe|object|embed|foreignObject)\b/i',
		'/\son[a-z]+\s*=/i',
		'/javascript\s*:/i',
		'/data\s*:\s*text\/html/i',
	);

	foreach ( $blocked_patterns as $pattern ) {
		if ( preg_match( $pattern, $svg ) ) {
			return '';
		}
	}

	$svg = preg_replace( '/<\?xml-stylesheet\b.*?\?>/is', '', $svg );
	$svg = preg_replace( '/<!DOCTYPE\b.*?>/is', '', $svg );

	return trim( $svg );
}

add_filter( 'upload_mimes', 'sutighar_allow_svg_uploads' );
function sutighar_allow_svg_uploads( $mimes ) {
	if ( sutighar_user_can_upload_svg() ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
}

add_filter( 'wp_check_filetype_and_ext', 'sutighar_validate_svg_filetype', 10, 5 );
function sutighar_validate_svg_filetype( $data, $file, $filename, $mimes, $real_mime ) {
	unset( $mimes, $real_mime );

	if ( ! sutighar_user_can_upload_svg() || 'svg' !== strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		return $data;
	}

	if ( ! sutighar_is_svg_file( $file ) ) {
		return $data;
	}

	$data['ext']  = 'svg';
	$data['type'] = 'image/svg+xml';

	return $data;
}

add_filter( 'wp_handle_upload_prefilter', 'sutighar_sanitize_svg_upload' );
function sutighar_sanitize_svg_upload( $file ) {
	if ( empty( $file['tmp_name'] ) || empty( $file['name'] ) || 'svg' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
		return $file;
	}

	if ( ! sutighar_user_can_upload_svg() ) {
		$file['error'] = __( 'SVG uploads are only allowed for site administrators.', 'sutighar' );
		return $file;
	}

	if ( ! sutighar_is_svg_file( $file['tmp_name'] ) ) {
		$file['error'] = __( 'The uploaded SVG file is not valid.', 'sutighar' );
		return $file;
	}

	$svg       = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$sanitized = is_string( $svg ) ? sutighar_sanitize_svg_markup( $svg ) : '';
	if ( '' === $sanitized ) {
		$file['error'] = __( 'The uploaded SVG contains unsupported or unsafe markup.', 'sutighar' );
		return $file;
	}

	file_put_contents( $file['tmp_name'], $sanitized ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	$file['type'] = 'image/svg+xml';

	return $file;
}
