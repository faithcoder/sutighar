<?php
/**
 * Shared helpers for templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sutighar_asset( $path ) {
	return SUTIGHAR_URI . '/' . ltrim( $path, '/' );
}

function sutighar_option( $key, $default = '' ) {
	$value = get_option( 'sutighar_' . $key, $default );
	return '' === $value ? $default : $value;
}

function sutighar_default_whatsapp_number() {
	return '8801616322116';
}

function sutighar_whatsapp_number( $with_plus = false ) {
	$raw    = trim( sutighar_option( 'whatsapp_number', sutighar_default_whatsapp_number() ) );
	$number = preg_replace( '/\D+/', '', $raw );
	if ( strlen( $number ) < 8 ) {
		$number = $raw ? $raw : sutighar_default_whatsapp_number();
	}

	return $with_plus ? '+' . $number : $number;
}

function sutighar_phone_display() {
	$number = sutighar_whatsapp_number();
	if ( 0 === strpos( $number, '880' ) && strlen( $number ) >= 11 ) {
		return '+880 ' . substr( $number, 3, 4 ) . ' ' . substr( $number, 7 );
	}

	return '+' . $number;
}

function sutighar_whatsapp_url( $message = '' ) {
	$url = 'https://wa.me/' . rawurlencode( sutighar_whatsapp_number() );
	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return $url;
}

function sutighar_plain_price( $amount, $args = array() ) {
	$defaults = array(
		'decimals' => 0,
	);
	$price    = wp_strip_all_tags( wc_price( $amount, wp_parse_args( $args, $defaults ) ) );
	$price    = html_entity_decode( $price, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
	$price    = str_replace( "\xc2\xa0", ' ', $price );

	return trim( preg_replace( '/\s+/', ' ', $price ) );
}

function sutighar_plain_text( $text ) {
	$text = wp_strip_all_tags( (string) $text );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
	$text = str_replace( "\xc2\xa0", ' ', $text );

	return trim( preg_replace( '/[ \t]+/', ' ', $text ) );
}

function sutighar_product_description_html( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$content = $product->get_short_description();
	if ( '' === trim( $content ) ) {
		$content = get_post_field( 'post_content', $product->get_id() );
	}

	if ( '' === trim( $content ) ) {
		return '';
	}

	$content = html_entity_decode( $content, ENT_QUOTES, get_bloginfo( 'charset' ) );
	$content = do_shortcode( $content );
	$content = wpautop( $content );
	$allowed = wp_kses_allowed_html( 'post' );

	$allowed['video'] = array(
		'autoplay'    => true,
		'class'       => true,
		'controls'    => true,
		'height'      => true,
		'loop'        => true,
		'muted'       => true,
		'playsinline' => true,
		'poster'      => true,
		'preload'     => true,
		'src'         => true,
		'style'       => true,
		'width'       => true,
	);
	$allowed['source'] = array(
		'src'  => true,
		'type' => true,
	);
	$allowed['track']  = array(
		'default' => true,
		'kind'    => true,
		'label'   => true,
		'src'     => true,
		'srclang' => true,
	);
	$allowed['iframe'] = array(
		'allow'           => true,
		'allowfullscreen' => true,
		'class'           => true,
		'height'          => true,
		'loading'         => true,
		'referrerpolicy'  => true,
		'src'             => true,
		'style'           => true,
		'title'           => true,
		'width'           => true,
	);

	return wp_kses( $content, $allowed );
}

function sutighar_logo_image( $key, $class ) {
	$logo_id = absint( sutighar_option( $key, 0 ) );
	if ( ! $logo_id && 'header_logo' === $key ) {
		$logo_id = absint( get_theme_mod( 'custom_logo' ) );
	}
	if ( ! $logo_id && 'footer_logo' === $key ) {
		$logo_id = absint( sutighar_option( 'header_logo', 0 ) );
		$logo_id = $logo_id ? $logo_id : absint( get_theme_mod( 'custom_logo' ) );
	}

	if ( ! $logo_id ) {
		return '';
	}

	return wp_get_attachment_image(
		$logo_id,
		'full',
		false,
		array(
			'class' => $class,
			'alt'   => get_bloginfo( 'name' ),
		)
	);
}

function sutighar_categories() {
	$defaults = array(
		'all'          => array( 'label' => __( 'Browse All', 'sutighar' ), 'image' => 'nav-browse-all.png', 'url' => wc_get_page_permalink( 'shop' ) ),
		'solid'        => array( 'label' => __( 'Solid', 'sutighar' ), 'image' => 'nav-solid.png' ),
		'stripe-check' => array( 'label' => __( 'Stripe & Check', 'sutighar' ), 'image' => 'nav-stripe.png' ),
		'jacquard'     => array( 'label' => __( 'Jacquard', 'sutighar' ), 'image' => 'nav-jacquard.png' ),
		'batik-print'  => array( 'label' => __( 'Batik Print', 'sutighar' ), 'image' => 'nav-batik.png' ),
		'handloom'     => array( 'label' => __( 'Handloom', 'sutighar' ), 'image' => 'nav-handloom.png' ),
	);

	foreach ( $defaults as $slug => &$item ) {
		if ( 'all' !== $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$item['label'] = $term->name;
				$item['url']   = get_term_link( $term );
				$thumb_id      = get_term_meta( $term->term_id, 'thumbnail_id', true );
				if ( $thumb_id ) {
					$item['thumb_id'] = (int) $thumb_id;
				}
			} else {
				$item['url'] = add_query_arg( 'product_cat', $slug, wc_get_page_permalink( 'shop' ) );
			}
		}
	}
	unset( $item );

	return $defaults;
}

function sutighar_category_thumb_url( $item ) {
	if ( ! empty( $item['thumb_id'] ) ) {
		return wp_get_attachment_image_url( $item['thumb_id'], 'thumbnail' );
	}

	return sutighar_asset( 'assets/images/' . $item['image'] );
}

function sutighar_menu_item_slug( $item ) {
	if ( ! $item instanceof WP_Post ) {
		return '';
	}

	if ( 'taxonomy' === $item->type && 'product_cat' === $item->object ) {
		$term = get_term( (int) $item->object_id, 'product_cat' );
		return ( $term && ! is_wp_error( $term ) ) ? $term->slug : '';
	}

	if ( ! empty( $item->url ) ) {
		parse_str( (string) wp_parse_url( $item->url, PHP_URL_QUERY ), $qs );
		if ( ! empty( $qs['product_cat'] ) ) {
			return sanitize_title( $qs['product_cat'] );
		}
	}

	if ( ! empty( $item->object_id ) && (int) $item->object_id === (int) wc_get_page_id( 'shop' ) ) {
		return 'all';
	}

	return '';
}

function sutighar_menu_item_thumb_url( $item ) {
	if ( $item instanceof WP_Post && 'taxonomy' === $item->type && 'product_cat' === $item->object ) {
		$thumb_id = get_term_meta( (int) $item->object_id, 'thumbnail_id', true );
		if ( $thumb_id ) {
			return wp_get_attachment_image_url( (int) $thumb_id, 'thumbnail' );
		}
	}

	if ( $item instanceof WP_Post && 'post_type' === $item->type && 'product' === $item->object && has_post_thumbnail( (int) $item->object_id ) ) {
		return get_the_post_thumbnail_url( (int) $item->object_id, 'thumbnail' );
	}

	$slug = sutighar_menu_item_slug( $item );
	$map  = array(
		'all'          => 'nav-browse-all.png',
		'solid'        => 'nav-solid.png',
		'stripe-check' => 'nav-stripe.png',
		'jacquard'     => 'nav-jacquard.png',
		'batik-print'  => 'nav-batik.png',
		'handloom'     => 'nav-handloom.png',
	);
	if ( $slug && isset( $map[ $slug ] ) ) {
		return sutighar_asset( 'assets/images/' . $map[ $slug ] );
	}

	return sutighar_asset( 'assets/images/nav-browse-all.png' );
}

function sutighar_is_active_category( $slug ) {
	if ( 'all' === $slug ) {
		return is_shop() || is_front_page();
	}

	return is_product_category( $slug ) || ( isset( $_GET['product_cat'] ) && $slug === sanitize_title( wp_unslash( $_GET['product_cat'] ) ) );
}

function sutighar_is_active_menu_item( $item ) {
	if ( ! $item instanceof WP_Post ) {
		return false;
	}

	$classes = isset( $item->classes ) ? (array) $item->classes : array();
	return in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true ) || in_array( 'current-product_cat-ancestor', $classes, true );
}

function sutighar_primary_menu_items() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $items ) {
			return array_values(
				array_filter(
					$items,
					function ( $item ) {
						return 0 === (int) $item->menu_item_parent;
					}
				)
			);
		}
	}

	return array();
}

function sutighar_render_plain_menu_links( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return false;
	}

	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return false;
	}

	foreach ( $items as $item ) {
		if ( 0 !== (int) $item->menu_item_parent ) {
			continue;
		}
		?>
		<a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
		<?php
	}

	return true;
}

function sutighar_render_category_nav_items( $context = 'desktop' ) {
	$menu_items = sutighar_primary_menu_items();
	if ( $menu_items ) {
		foreach ( $menu_items as $item ) {
			$classes = 'desktop' === $context ? 'sg-catnav__item' : '';
			if ( sutighar_is_active_menu_item( $item ) ) {
				$classes .= ' is-active';
			}
			?>
			<a class="<?php echo esc_attr( trim( $classes ) ); ?>" href="<?php echo esc_url( $item->url ); ?>">
				<span class="<?php echo 'desktop' === $context ? 'sg-catnav__thumb' : ''; ?>" role="img" aria-label="<?php echo esc_attr( $item->title ); ?>" style="background-image:url('<?php echo esc_url( sutighar_menu_item_thumb_url( $item ) ); ?>')"></span>
				<span><?php echo esc_html( $item->title ); ?></span>
			</a>
			<?php
		}
		return;
	}

	foreach ( sutighar_categories() as $slug => $item ) {
		$classes = 'desktop' === $context ? 'sg-catnav__item' : '';
		if ( sutighar_is_active_category( $slug ) ) {
			$classes .= ' is-active';
		}
		?>
		<a class="<?php echo esc_attr( trim( $classes ) ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
			<span class="<?php echo 'desktop' === $context ? 'sg-catnav__thumb' : ''; ?>" role="img" aria-label="<?php echo esc_attr( $item['label'] ); ?>" style="background-image:url('<?php echo esc_url( sutighar_category_thumb_url( $item ) ); ?>')"></span>
			<span><?php echo esc_html( $item['label'] ); ?></span>
		</a>
		<?php
	}
}

function sutighar_drawer_nav_labels() {
	return array(
		'all'          => __( 'Browse All Lungi', 'sutighar' ),
		'solid'        => __( 'Solid Color', 'sutighar' ),
		'stripe-check' => __( 'Stripe & Check Design', 'sutighar' ),
		'jacquard'     => __( 'Jacquard Style', 'sutighar' ),
		'batik-print'  => __( 'Batik Print', 'sutighar' ),
		'handloom'     => __( 'Handloom Lungi', 'sutighar' ),
	);
}

function sutighar_render_drawer_nav_items() {
	$menu_items = sutighar_primary_menu_items();
	if ( $menu_items ) {
		$labels = sutighar_drawer_nav_labels();
		foreach ( $menu_items as $item ) {
			$classes = 'sg-drawer__link';
			if ( sutighar_is_active_menu_item( $item ) ) {
				$classes .= ' is-active';
			}
			$slug  = sutighar_menu_item_slug( $item );
			$label = ( $slug && isset( $labels[ $slug ] ) ) ? $labels[ $slug ] : $item->title;
			?>
			<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $item->url ); ?>">
				<span class="sg-drawer__thumb" role="img" aria-label="<?php echo esc_attr( $item->title ); ?>" style="background-image:url('<?php echo esc_url( sutighar_menu_item_thumb_url( $item ) ); ?>')"></span>
				<span class="sg-drawer__label"><?php echo esc_html( $label ); ?></span>
			</a>
			<?php
		}
		return;
	}

	$labels = sutighar_drawer_nav_labels();
	foreach ( sutighar_categories() as $slug => $item ) {
		$classes = 'sg-drawer__link';
		if ( sutighar_is_active_category( $slug ) ) {
			$classes .= ' is-active';
		}
		?>
		<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
			<span class="sg-drawer__thumb" role="img" aria-label="<?php echo esc_attr( $item['label'] ); ?>" style="background-image:url('<?php echo esc_url( sutighar_category_thumb_url( $item ) ); ?>')"></span>
			<span class="sg-drawer__label"><?php echo esc_html( isset( $labels[ $slug ] ) ? $labels[ $slug ] : $item['label'] ); ?></span>
		</a>
		<?php
	}
}

function sutighar_inline_icon( $name ) {
	$icons = array(
		'cart'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"></path></svg>',
		'heart'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1L12 21l7.7-7.6 1.1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>',
		'user'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.6"></circle><path d="M4.5 20.5c1.4-3.7 4.2-5.5 7.5-5.5s6.1 1.8 7.5 5.5"></path></svg>',
		'check'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
		'filter'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M8 12h8"></path><path d="M10 17h4"></path></svg>',
		'minus'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path></svg>',
		'plus'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>',
		'menu'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>',
		'close'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>',
		'arrow'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"></path></svg>',
		'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>',
		'messenger' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12c1.325 0 2.589-.221 3.768-.629l3.465 1.081a1 1 0 001.107-1.507l-.99-1.98A11.94 11.94 0 0024 12c0-6.627-5.373-12-12-12zm5.556 15.111l-2.952-3.132-3.667 3.143-4.011-6.566 4.182.99 2.922 3.098 3.667-3.143 1.859 7.61z"></path></svg>',
		'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>',
		'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"></path></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function sutighar_price( $price ) {
	return wc_price(
		$price,
		array(
			'currency'           => 'BDT',
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
			'decimals'           => 0,
		)
	);
}

function sutighar_bdt( $amount ) {
	return number_format_i18n( (float) $amount, 0 ) . ' BDT';
}

function sutighar_product_card( $product = null ) {
	if ( ! $product ) {
		global $product;
	}
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	wc_get_template_part( 'content', 'product' );
}

function sutighar_product_size_measurements( $product ) {
	if ( $product instanceof WC_Product ) {
		$meta_size   = $product->get_meta( '_sg_size' );
		$meta_height = $product->get_meta( '_sg_height' );
		$meta_waist  = $product->get_meta( '_sg_waist' );
		if ( $meta_size || $meta_height || $meta_waist ) {
			return array(
				'size'   => $meta_size ? $meta_size : __( 'One size', 'sutighar' ),
				'height' => $meta_height ? $meta_height : '—',
				'waist'  => $meta_waist ? $meta_waist : '—',
			);
		}
	}

	$size = $product instanceof WC_Product ? $product->get_attribute( 'pa_size' ) : '';
	$size = $size ? $size : $product->get_meta( '_sg_size' );
	$map  = array(
		'Kids'     => array( 42, 72 ),
		'5 Haat'   => array( 51, 98 ),
		'5.5 Haat' => array( 51, 98 ),
		'6 Haat'   => array( 54, 104 ),
	);

	foreach ( $map as $label => $values ) {
		if ( false !== stripos( $size, $label ) || false !== stripos( $product->get_name(), $label ) ) {
			return array( 'size' => $label, 'height' => $values[0], 'waist' => $values[1] );
		}
	}

	return array( 'size' => $size ? $size : __( 'One size', 'sutighar' ), 'height' => '—', 'waist' => '—' );
}

function sutighar_product_spec_value( $product, $label ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$meta_key = '_sg_spec_' . sanitize_key( str_replace( ' ', '_', strtolower( $label ) ) );
	$value    = $product->get_meta( $meta_key );
	if ( $value ) {
		return $value;
	}

	$value = $product->get_attribute( sanitize_title( $label ) );
	if ( ! $value && 'Brand' === $label ) {
		$terms = wp_get_post_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );
		$value = ! is_wp_error( $terms ) ? implode( ', ', $terms ) : '';
	}

	if ( $value ) {
		return $value;
	}

	$defaults = sutighar_default_specs();
	return $defaults[ $label ] ?? '';
}

function sutighar_default_specs() {
	return array(
		'Brand'      => 'Sutighar',
		'Fabric'     => '100% Cotton',
		'Mercerized' => 'Yes',
		'Loom Type'  => 'Mechanical',
		'Border'     => 'Woven',
		'Wash Type'  => 'Regular',
	);
}

function sutighar_product_plain_price( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$price = $product->get_price();
	if ( '' === $price && $product->is_type( 'variable' ) ) {
		$price = $product->get_variation_price( 'min', true );
	}

	return '' === $price ? '' : number_format_i18n( (float) $price, 0 );
}

function sutighar_size_chart_modal() {
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;
	?>
	<div class="sg-modal" data-sg-size-modal hidden>
		<div class="sg-modal__backdrop" data-sg-size-close></div>
		<div class="sg-modal__panel" role="dialog" aria-modal="true" aria-labelledby="sg-size-title">
			<div class="sg-modal__head">
				<h2 id="sg-size-title"><?php esc_html_e( 'Size Chart', 'sutighar' ); ?></h2>
				<button type="button" data-sg-size-close aria-label="<?php esc_attr_e( 'Close size chart', 'sutighar' ); ?>">×</button>
			</div>
			<div class="sg-size-chart">
				<?php foreach ( array( 'Kids' => array( 42, 72 ), '5 Haat' => array( 51, 98 ), '5.5 Haat' => array( 51, 98 ), '6 Haat' => array( 54, 104 ) ) as $label => $row ) : ?>
					<div><strong><?php echo esc_html( $label ); ?></strong><span><?php echo esc_html( sprintf( __( 'Height %1$s″ · Waist %2$s″', 'sutighar' ), $row[0], $row[1] ) ); ?></span></div>
				<?php endforeach; ?>
			</div>
			<p><?php esc_html_e( 'Measurements in inches, taken flat before wash. Allow about 1″ shrinkage on first wash.', 'sutighar' ); ?></p>
		</div>
	</div>
	<?php
}

function sutighar_whatsapp_order_url( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return '#';
	}

	$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
	$shipping      = (float) $order->get_shipping_total();
	$fee_total     = 0.0;

	foreach ( $order->get_fees() as $fee ) {
		$fee_total += (float) $fee->get_total();
	}

	$delivery_charge = $shipping + $fee_total;
	$lines         = array(
		'*New order from Sutighar*',
		'Order ref: #' . $order->get_order_number(),
		'Date: ' . wc_format_datetime( $order->get_date_created(), 'd M Y' ),
		'',
		'*Items*',
	);
	$item_count    = 1;

	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		$size    = $product ? $product->get_attribute( 'pa_size' ) : '';
		$item_name = sutighar_plain_text( $item->get_name() );
		$lines[]   = $item_count . '. ' . $item_name . ( $size ? ' - ' . sutighar_plain_text( $size ) : '' );
		$lines[] = '   Qty: ' . $item->get_quantity() . ' | Total: ' . sutighar_plain_price( $item->get_total() );
		$item_count++;
	}

	$lines = array_merge(
		$lines,
		array(
			'',
			'*Order Summary*',
			'Subtotal: ' . sutighar_plain_price( $order->get_subtotal() ),
			'Delivery charge: ' . ( $delivery_charge > 0 ? sutighar_plain_price( $delivery_charge ) : 'Free' ),
			'Total: ' . sutighar_plain_price( $order->get_total() ),
			'',
			'*Customer Details*',
			'Name: ' . ( $customer_name ? sutighar_plain_text( $customer_name ) : '-' ),
			'Phone: ' . ( $order->get_billing_phone() ? sutighar_plain_text( $order->get_billing_phone() ) : '-' ),
			'Email: ' . ( $order->get_billing_email() ? sanitize_email( $order->get_billing_email() ) : '-' ),
			'District: ' . ( $order->get_billing_state() ? sutighar_plain_text( $order->get_billing_state() ) : '-' ),
			'Thana: ' . ( $order->get_billing_city() ? sutighar_plain_text( $order->get_billing_city() ) : '-' ),
			'Address: ' . ( $order->get_billing_address_1() ? sutighar_plain_text( $order->get_billing_address_1() ) : '-' ),
		)
	);

	if ( $order->get_customer_note() ) {
		$lines[] = 'Notes: ' . sutighar_plain_text( $order->get_customer_note() );
	}

	$payment = $order->get_payment_method_title();
	$trx     = $order->get_meta( '_sg_transaction_id' );
	$lines[] = '';
	$lines[] = '*Payment*';
	$lines[] = sutighar_plain_text( $payment ? $payment : '-' );
	if ( $trx ) {
		$lines[] = 'Transaction ID: ' . sutighar_plain_text( $trx );
	}

	return sutighar_whatsapp_url( implode( "\n", $lines ) );
}
