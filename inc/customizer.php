<?php
/**
 * Customizer settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sutighar_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true == $checked );
}

add_action( 'customize_register', 'sutighar_customize_register' );
function sutighar_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'sutighar_panel',
		array(
			'title'    => __( 'Sutighar Settings', 'sutighar' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section(
		'sutighar_store',
		array(
			'title' => __( 'Store Settings', 'sutighar' ),
			'panel' => 'sutighar_panel',
		)
	);

	$wp_customize->add_section(
		'sutighar_branding',
		array(
			'title' => __( 'Header & Footer Logos', 'sutighar' ),
			'panel' => 'sutighar_panel',
		)
	);

	$logo_fields = array(
		'header_logo' => __( 'Header logo', 'sutighar' ),
		'footer_logo' => __( 'Footer logo', 'sutighar' ),
	);

	foreach ( $logo_fields as $key => $label ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => 0,
				'type'              => 'option',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'sutighar_' . $key,
				array(
					'label'     => $label,
					'section'   => 'sutighar_branding',
					'mime_type' => 'image',
				)
			)
		);
	}

	$store_fields = array(
		'whatsapp_number'         => array( __( 'WhatsApp number', 'sutighar' ), sutighar_default_whatsapp_number() ),
		'shipping_fee'            => array( __( 'Shipping fee', 'sutighar' ), '80' ),
		'free_shipping_threshold' => array( __( 'Free shipping threshold', 'sutighar' ), '3000' ),
		'return_inside_dhaka_fee' => array( __( 'Inside Dhaka exchange fee', 'sutighar' ), '120' ),
		'return_outside_dhaka_fee' => array( __( 'Outside Dhaka exchange fee', 'sutighar' ), '200' ),
		'fault_window_days'       => array( __( 'Fault return window days', 'sutighar' ), '3' ),
		'exchange_window_days'    => array( __( 'Exchange window days', 'sutighar' ), '2' ),
	);

	foreach ( $store_fields as $key => $field ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => $field[1],
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'sutighar_store',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_payments',
		array(
			'title'       => __( 'Payment Methods', 'sutighar' ),
			'description' => __( 'Use these switches to show or hide the manual mobile payment methods at checkout. Merchant numbers are still configured in WooCommerce > Settings > Payments.', 'sutighar' ),
			'panel'       => 'sutighar_panel',
		)
	);

	$payment_fields = array(
		'enable_bkash' => __( 'Enable bKash at checkout', 'sutighar' ),
		'enable_nagad' => __( 'Enable Nagad at checkout', 'sutighar' ),
	);

	foreach ( $payment_fields as $key => $label ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => true,
				'type'              => 'option',
				'sanitize_callback' => 'sutighar_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'   => $label,
				'section' => 'sutighar_payments',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_footer',
		array(
			'title' => __( 'Footer', 'sutighar' ),
			'panel' => 'sutighar_panel',
		)
	);

	$footer_fields = array(
		'footer_text'       => array( __( 'Footer description', 'sutighar' ), __( 'Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort.', 'sutighar' ), 'textarea' ),
		'footer_copyright'  => array( __( 'Copyright text', 'sutighar' ), __( 'Sutighar. All rights reserved.', 'sutighar' ), 'text' ),
		'footer_credit'     => array( __( 'Footer credit', 'sutighar' ), __( 'Made in Bangladesh.', 'sutighar' ), 'text' ),
	);

	foreach ( $footer_fields as $key => $field ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => $field[1],
				'type'              => 'option',
				'sanitize_callback' => 'textarea' === $field[2] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'sutighar_footer',
				'type'    => $field[2],
			)
		);
	}
}
