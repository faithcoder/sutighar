<?php
/**
 * Footer template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>
<footer class="sg-footer">
	<div class="sg-container">
		<div class="sg-footer__grid">
			<div>
				<a class="sg-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Sutighar home', 'sutighar' ); ?>">
					<?php $footer_logo = sutighar_logo_image( 'footer_logo', 'sg-footer__logo-custom' ); ?>
					<?php if ( $footer_logo ) : ?>
						<?php echo $footer_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<span class="sg-footer__logo-icon" aria-hidden="true"></span>
						<span class="sg-footer__logo-word" aria-hidden="true"></span>
					<?php endif; ?>
				</a>
				<p><?php echo esc_html( sutighar_option( 'footer_text', __( 'Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort.', 'sutighar' ) ) ); ?></p>
			</div>
			<div class="sg-footer__menus">
				<div>
					<?php if ( is_active_sidebar( 'footer_1' ) ) : ?>
						<?php dynamic_sidebar( 'footer_1' ); ?>
					<?php else : ?>
						<h3><?php esc_html_e( 'Shop', 'sutighar' ); ?></h3>
						<?php sutighar_footer_menu( 'footer_shop', sutighar_default_shop_links() ); ?>
					<?php endif; ?>
				</div>
				<div>
					<?php if ( is_active_sidebar( 'footer_2' ) ) : ?>
						<?php dynamic_sidebar( 'footer_2' ); ?>
					<?php else : ?>
						<h3><?php esc_html_e( 'Company', 'sutighar' ); ?></h3>
						<?php sutighar_footer_menu( 'footer_company', sutighar_default_company_links() ); ?>
					<?php endif; ?>
				</div>
				<div>
					<?php if ( is_active_sidebar( 'footer_3' ) ) : ?>
						<?php dynamic_sidebar( 'footer_3' ); ?>
					<?php else : ?>
						<h3><?php esc_html_e( 'Connect', 'sutighar' ); ?></h3>
						<?php $footer_social_links = sutighar_social_links(); ?>
						<?php if ( $footer_social_links ) : ?>
							<div class="sg-footer__socials">
								<?php foreach ( array( 'instagram', 'messenger', 'facebook', 'whatsapp' ) as $key ) : ?>
									<?php if ( empty( $footer_social_links[ $key ] ) ) : ?>
										<?php continue; ?>
									<?php endif; ?>
									<a class="sg-footer__social" href="<?php echo esc_url( $footer_social_links[ $key ]['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $footer_social_links[ $key ]['label'] ); ?>">
										<img src="<?php echo esc_url( $footer_social_links[ $key ]['icon'] ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<div class="sg-footer__contact">
							<?php
							$footer_phone      = sutighar_contact_phone();
							$footer_phone_href = sutighar_contact_phone_href();
							$footer_email      = sutighar_contact_email();
							$footer_address    = sutighar_contact_address();
							?>
							<?php if ( $footer_phone && $footer_phone_href ) : ?>
								<a href="<?php echo esc_url( $footer_phone_href ); ?>"><?php echo esc_html( $footer_phone ); ?></a>
							<?php endif; ?>
							<?php if ( $footer_email ) : ?>
								<a href="<?php echo esc_url( 'mailto:' . $footer_email ); ?>"><?php echo esc_html( $footer_email ); ?></a>
							<?php endif; ?>
							<?php if ( $footer_address ) : ?>
								<p><?php echo nl2br( esc_html( $footer_address ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="sg-footer__legal">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( sutighar_option( 'footer_copyright', __( 'Sutighar. All rights reserved.', 'sutighar' ) ) ); ?></span>
			<span><?php echo esc_html( sutighar_option( 'footer_credit', __( 'Made in Bangladesh.', 'sutighar' ) ) ); ?></span>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
