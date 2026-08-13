<?php
/**
 * Template Name: Sutighar Contact
 */

get_header();

$contact_email = sutighar_contact_email();
$address       = sutighar_contact_address();
?>
<section class="sg-page sg-page--read sg-container sg-contact-page">
	<p class="sg-eyebrow"><?php esc_html_e( 'Customer Care', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Talk to us.', 'sutighar' ); ?></h1>
	<p class="sg-page-lead"><?php esc_html_e( "WhatsApp is the fastest way to reach us — it's where we confirm every order. Message us any time and we'll reply within a few hours.", 'sutighar' ); ?></p>
	<a class="sg-btn sg-contact-primary" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Message us on WhatsApp', 'sutighar' ); ?></a>

	<div class="sg-contact-grid">
		<div class="sg-info-card">
			<span><?php esc_html_e( 'WhatsApp', 'sutighar' ); ?></span>
			<strong><?php echo esc_html( sutighar_phone_display() ); ?></strong>
			<p><?php esc_html_e( 'Orders, sizing questions, delivery updates.', 'sutighar' ); ?></p>
		</div>
		<div class="sg-info-card">
			<span><?php esc_html_e( 'Based in', 'sutighar' ); ?></span>
			<strong><?php echo esc_html( $address ? $address : __( 'Shiddhirganj, Narayanganj', 'sutighar' ) ); ?></strong>
			<p><?php esc_html_e( 'Dhaka, Bangladesh. We ship nationwide.', 'sutighar' ); ?></p>
		</div>
		<?php if ( $contact_email ) : ?>
			<div class="sg-info-card sg-info-card--email">
				<span><?php esc_html_e( 'Email', 'sutighar' ); ?></span>
				<strong><?php echo esc_html( $contact_email ); ?></strong>
				<p><?php esc_html_e( 'For anything longer than a message.', 'sutighar' ); ?></p>
			</div>
		<?php endif; ?>
		<div class="sg-online-panel">
			<h2><?php esc_html_e( 'Find us online', 'sutighar' ); ?></h2>
			<ul class="sg-link-list">
				<li><a href="https://instagram.com/sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'Instagram', 'sutighar' ); ?></a></li>
				<li><a href="https://facebook.com/sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'Facebook', 'sutighar' ); ?></a></li>
				<li><a href="https://tiktok.com/@sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'TikTok', 'sutighar' ); ?></a></li>
				<li><a href="https://youtube.com/@sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'YouTube', 'sutighar' ); ?></a></li>
			</ul>
		</div>
	</div>

	<h2><?php esc_html_e( 'Before you write', 'sutighar' ); ?></h2>
	<ul class="sg-policy-list">
		<li><?php echo wp_kses_post( sprintf( __( 'Have an order to return or exchange? The terms are on our <a href="%s">Return & Exchange Policy</a> page.', 'sutighar' ), esc_url( home_url( '/return-exchange/' ) ) ) ); ?></li>
		<li><?php esc_html_e( 'Unsure about sizing? Every product page carries a size chart with height and waist measurements.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Messaging about an existing order? Include your order reference so we can find it quickly.', 'sutighar' ); ?></li>
	</ul>

	<div class="sg-sand-panel sg-contact-note">
		<p><?php esc_html_e( "Sutighar is built by two co-founders, Emran and Shoikot, based in Dhaka. You're writing to us directly, not to a call centre.", 'sutighar' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Read more about us', 'sutighar' ); ?></a>
	</div>
</section>
<?php
get_footer();
