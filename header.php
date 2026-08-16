<?php
/**
 * Header template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#122A49">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$cart_drawer_enabled       = sutighar_option_enabled( 'enable_cart_drawer', true );
$floating_cart_enabled     = sutighar_option_enabled( 'enable_floating_cart', true );
$mobile_menu_fab_enabled   = sutighar_option_enabled( 'enable_floating_whatsapp', true );
$header_locked_compact     = sutighar_header_should_lock_compact();
?>
<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'sutighar' ); ?></a>
<header class="<?php echo esc_attr( $header_locked_compact ? 'sg-header is-compact' : 'sg-header' ); ?>" data-sg-header>
	<div class="sg-header__top">
		<a class="sg-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Sutighar home', 'sutighar' ); ?>">
			<?php $header_logo = sutighar_logo_image( 'header_logo', 'sg-logo__custom' ); ?>
			<?php if ( $header_logo ) : ?>
				<?php echo $header_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="sg-logo__icon" aria-hidden="true"></span>
				<span class="sg-logo__wordmark" aria-hidden="true"></span>
			<?php endif; ?>
		</a>

		<nav class="sg-catnav" aria-label="<?php esc_attr_e( 'Product categories', 'sutighar' ); ?>">
			<div class="sg-catnav__inner">
				<?php sutighar_render_category_nav_items( 'desktop' ); ?>
			</div>
		</nav>

		<div class="sg-actions">
			<?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
				<span class="sg-cart-total" data-sg-cart-total><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
			<?php endif; ?>
			<a class="sg-icon-btn sg-cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'sutighar' ); ?>" <?php echo $cart_drawer_enabled ? 'data-sg-cart-open' : ''; ?>>
				<?php echo sutighar_icon_img( 'solar_cart-bold.svg', 'sg-cart-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="sg-badge" data-sg-cart-count data-count="<?php echo esc_attr( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?>"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
			</a>
			<a class="sg-icon-btn sg-wishlist-btn" href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'sutighar' ); ?>">
				<?php echo sutighar_inline_icon( 'heart' ); ?>
				<span class="sg-badge" data-sg-wishlist-count data-count="<?php echo esc_attr( sutighar_wishlist_count() ); ?>"><?php echo esc_html( sutighar_wishlist_count() ); ?></span>
			</a>
			<div class="sg-pop sg-account-pop">
				<button class="sg-icon-btn" type="button" aria-label="<?php esc_attr_e( 'Account menu', 'sutighar' ); ?>" aria-expanded="false" aria-controls="sg-account-menu" data-sg-pop-toggle="account">
					<?php echo sutighar_icon_img( 'user-solid.svg', 'sg-user-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<nav id="sg-account-menu" class="sg-popover" data-sg-popover="account" aria-label="<?php esc_attr_e( 'Account', 'sutighar' ); ?>">
					<?php if ( has_nav_menu( 'account_menu' ) ) : ?>
						<?php sutighar_render_plain_menu_links( 'account_menu' ); ?>
					<?php else : ?>
						<?php if ( is_user_logged_in() ) : ?>
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><?php esc_html_e( 'Profile & details', 'sutighar' ); ?></a>
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'My orders', 'sutighar' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Login / Register', 'sutighar' ); ?></a>
						<?php endif; ?>
						<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>"><?php esc_html_e( 'Wishlist', 'sutighar' ); ?></a>
						<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Cart', 'sutighar' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Sutighar', 'sutighar' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sutighar' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/return-exchange/' ) ); ?>"><?php esc_html_e( 'Return & Exchange', 'sutighar' ); ?></a>
					<?php endif; ?>
				</nav>
			</div>
			<button class="sg-icon-btn sg-menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'sutighar' ); ?>" aria-expanded="false" aria-controls="sg-drawer" data-sg-drawer-toggle>
				<?php echo sutighar_icon_img( 'codex_menu-small.svg', 'sg-menu-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo sutighar_inline_icon( 'close' ); ?>
			</button>
		</div>
	</div>
</header>
<div class="sg-header-spacer" aria-hidden="true"></div>

<div class="sg-drawer" id="sg-drawer" data-sg-drawer aria-hidden="true">
	<div class="sg-drawer__backdrop" data-sg-drawer-close></div>
	<div class="sg-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'sutighar' ); ?>">
		<div class="sg-drawer__head">
			<a class="sg-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Sutighar home', 'sutighar' ); ?>">
				<?php $drawer_logo = sutighar_logo_image( 'header_logo', 'sg-logo__custom' ); ?>
				<?php if ( $drawer_logo ) : ?>
					<?php echo $drawer_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<span class="sg-logo__icon" aria-hidden="true"></span>
					<span class="sg-logo__wordmark" aria-hidden="true"></span>
				<?php endif; ?>
			</a>
			<div class="sg-drawer__icons">
				<a class="sg-icon-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'Account', 'sutighar' ); ?>">
					<?php echo sutighar_icon_img( 'user-solid.svg', 'sg-user-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<a class="sg-icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'sutighar' ); ?>" <?php echo $cart_drawer_enabled ? 'data-sg-cart-open' : ''; ?>>
					<?php echo sutighar_icon_img( 'solar_cart-bold.svg', 'sg-cart-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sg-badge" data-sg-cart-count data-count="<?php echo esc_attr( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?>"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
				</a>
				<button class="sg-icon-btn" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'sutighar' ); ?>" data-sg-drawer-close>
					<?php echo sutighar_inline_icon( 'close' ); ?>
				</button>
			</div>
		</div>

		<nav class="sg-drawer__nav" aria-label="<?php esc_attr_e( 'Product categories', 'sutighar' ); ?>">
			<?php sutighar_render_drawer_nav_items(); ?>
		</nav>

		<div class="sg-drawer__foot">
			<span class="sg-drawer__connect"><?php esc_html_e( 'Connect', 'sutighar' ); ?></span>
			<?php $social_links = sutighar_social_links(); ?>
			<?php if ( $social_links ) : ?>
				<div class="sg-drawer__socials">
					<?php foreach ( $social_links as $key => $social ) : ?>
						<a class="sg-social sg-social--<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>">
							<img src="<?php echo esc_url( $social['icon'] ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php
			$contact_phone      = sutighar_contact_phone();
			$contact_phone_href = sutighar_contact_phone_href();
			$contact_email      = sutighar_contact_email();
			?>
			<?php if ( $contact_phone || $contact_email ) : ?>
				<div class="sg-drawer__contact">
					<?php if ( $contact_phone && $contact_phone_href ) : ?>
						<a href="<?php echo esc_url( $contact_phone_href ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
					<?php endif; ?>
					<?php if ( $contact_email ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if ( $floating_cart_enabled ) : ?>
	<a class="sg-fab" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'sutighar' ); ?>" <?php echo $cart_drawer_enabled ? 'data-sg-cart-open' : ''; ?> data-sg-cart-fab>
		<?php echo sutighar_icon_img( 'solar_cart-bold.svg', 'sg-cart-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="sg-badge" data-sg-cart-count data-count="<?php echo esc_attr( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?>"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
	</a>
<?php endif; ?>
<?php if ( $mobile_menu_fab_enabled ) : ?>
	<button class="sg-fab sg-fab--menu" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'sutighar' ); ?>" aria-expanded="false" aria-controls="sg-drawer" data-sg-drawer-toggle>
		<?php echo sutighar_icon_img( 'codex_menu-small.svg', 'sg-menu-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>
<?php endif; ?>

<?php if ( $cart_drawer_enabled ) : ?>
	<div class="sg-cart-modal" data-sg-cart-modal aria-hidden="true">
		<div class="sg-cart-modal__backdrop" data-sg-cart-modal-close></div>
		<div class="sg-cart-modal__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping cart', 'sutighar' ); ?>">
			<div class="sg-cart-modal__head">
				<h2><?php esc_html_e( 'Shopping Cart', 'sutighar' ); ?></h2>
				<button type="button" data-sg-cart-modal-close aria-label="<?php esc_attr_e( 'Close cart', 'sutighar' ); ?>">×</button>
			</div>
			<div class="sg-cart-modal__body" data-sg-cart-modal-body>
				<?php sutighar_cart_modal_body(); ?>
			</div>
			<div class="sg-cart-modal__foot" data-sg-cart-modal-foot>
				<?php sutighar_cart_modal_footer(); ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<main id="main">
