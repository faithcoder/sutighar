<?php
/**
 * Customizer settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sutighar_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true == $checked ) ? '1' : '0';
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
			'title'    => __( 'Store Contact', 'sutighar' ),
			'panel'    => 'sutighar_panel',
			'priority' => 10,
		)
	);

	$wp_customize->add_section(
		'sutighar_branding',
		array(
			'title'    => __( 'Branding', 'sutighar' ),
			'panel'    => 'sutighar_panel',
			'priority' => 5,
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
		'whatsapp_number' => array( __( 'WhatsApp number', 'sutighar' ), sutighar_default_whatsapp_number() ),
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
		'sutighar_shipping_returns',
		array(
			'title'    => __( 'Shipping & Returns', 'sutighar' ),
			'panel'    => 'sutighar_panel',
			'priority' => 15,
		)
	);

	$shipping_return_fields = array(
		'shipping_fee'                => array( __( 'Inside Dhaka delivery charge', 'sutighar' ), '80' ),
		'outside_dhaka_shipping_fee'  => array( __( 'Outside Dhaka delivery charge', 'sutighar' ), '120' ),
		'free_shipping_threshold'     => array( __( 'Free shipping threshold', 'sutighar' ), '3000' ),
		'return_inside_dhaka_fee'     => array( __( 'Inside Dhaka exchange fee', 'sutighar' ), '120' ),
		'return_outside_dhaka_fee'    => array( __( 'Outside Dhaka exchange fee', 'sutighar' ), '200' ),
		'fault_window_days'           => array( __( 'Fault return window days', 'sutighar' ), '3' ),
		'exchange_window_days'        => array( __( 'Exchange window days', 'sutighar' ), '2' ),
	);

	foreach ( $shipping_return_fields as $key => $field ) {
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
				'section' => 'sutighar_shipping_returns',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_display',
		array(
			'title'    => __( 'Display Options', 'sutighar' ),
			'panel'    => 'sutighar_panel',
			'priority' => 20,
		)
	);

	$display_fields = array(
		'enable_cart_drawer'       => __( 'Enable cart drawer', 'sutighar' ),
		'enable_floating_cart'     => __( 'Enable floating cart button', 'sutighar' ),
		'enable_floating_whatsapp' => __( 'Enable floating mobile menu button', 'sutighar' ),
	);

	foreach ( $display_fields as $key => $label ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => '1',
				'type'              => 'option',
				'sanitize_callback' => 'sutighar_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'   => $label,
				'section' => 'sutighar_display',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_catalog_filters',
		array(
			'title'       => __( 'Catalog Filters', 'sutighar' ),
			'description' => __( 'Show or hide filter groups in the shop and product archive filter panel.', 'sutighar' ),
			'panel'       => 'sutighar_panel',
			'priority'    => 22,
		)
	);

	$catalog_filter_fields = array(
		'enable_filter_category'     => __( 'Show category filter', 'sutighar' ),
		'enable_filter_size'         => __( 'Show size filter', 'sutighar' ),
		'enable_filter_availability' => __( 'Show availability filter', 'sutighar' ),
		'enable_filter_price'        => __( 'Show price filter', 'sutighar' ),
	);

	foreach ( $catalog_filter_fields as $key => $label ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => '1',
				'type'              => 'option',
				'sanitize_callback' => 'sutighar_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'   => $label,
				'section' => 'sutighar_catalog_filters',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_social_links',
		array(
			'title'       => __( 'Social Links', 'sutighar' ),
			'description' => __( 'These links appear under Connect in the responsive header drawer. Leave a URL empty to hide that icon.', 'sutighar' ),
			'panel'       => 'sutighar_panel',
			'priority'    => 25,
		)
	);

	foreach ( sutighar_social_link_definitions() as $key => $social ) {
		$wp_customize->add_setting(
			'sutighar_social_' . $key . '_url',
			array(
				'default'           => $social['url'],
				'type'              => 'option',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'sutighar_social_' . $key . '_url',
			array(
				'label'   => sprintf(
					/* translators: %s: social network name. */
					__( '%s URL', 'sutighar' ),
					$social['label']
				),
				'section' => 'sutighar_social_links',
				'type'    => 'url',
			)
		);
	}

	$social_contact_fields = array(
		'contact_phone' => array(
			'label'    => __( 'Contact phone', 'sutighar' ),
			'default'  => sutighar_phone_display(),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'contact_email' => array(
			'label'    => __( 'Contact email', 'sutighar' ),
			'default'  => sutighar_default_contact_email(),
			'type'     => 'email',
			'sanitize' => 'sanitize_email',
		),
		'contact_address' => array(
			'label'    => __( 'Contact address', 'sutighar' ),
			'default'  => sutighar_default_contact_address(),
			'type'     => 'textarea',
			'sanitize' => 'sanitize_textarea_field',
		),
	);

	foreach ( $social_contact_fields as $key => $field ) {
		$wp_customize->add_setting(
			'sutighar_' . $key,
			array(
				'default'           => $field['default'],
				'type'              => 'option',
				'sanitize_callback' => $field['sanitize'],
			)
		);
		$wp_customize->add_control(
			'sutighar_' . $key,
			array(
				'label'       => $field['label'],
				'description' => __( 'Leave empty to hide this line in the responsive header drawer.', 'sutighar' ),
				'section'     => 'sutighar_social_links',
				'type'        => $field['type'],
			)
		);
	}

	$wp_customize->add_section(
		'sutighar_payments',
		array(
			'title'       => __( 'Payment Methods', 'sutighar' ),
			'description' => __( 'Use these switches to show or hide the manual mobile payment methods at checkout. Merchant numbers are still configured in WooCommerce > Settings > Payments.', 'sutighar' ),
			'panel'       => 'sutighar_panel',
			'priority'    => 30,
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
				'default'           => '1',
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
			'title'    => __( 'Footer', 'sutighar' ),
			'panel'    => 'sutighar_panel',
			'priority' => 35,
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
