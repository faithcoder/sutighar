<?php
/**
 * Template Name: Sutighar Contact
 */

get_header();
?>
<section class="sg-page sg-page--read sg-container">
	<p class="sg-eyebrow"><?php esc_html_e( 'Customer Care', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Talk to us.', 'sutighar' ); ?></h1>
	<p><?php esc_html_e( "We confirm every order on WhatsApp and usually reply within a few hours. Send your question, size concern, or order reference and we'll help.", 'sutighar' ); ?></p>
	<p><a class="sg-btn" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Message on WhatsApp', 'sutighar' ); ?></a></p>
	<div class="sg-founder-grid">
		<div class="sg-sand-panel">
			<span class="sg-meta"><?php esc_html_e( 'WhatsApp', 'sutighar' ); ?></span>
			<strong><?php echo esc_html( sutighar_whatsapp_number( true ) ); ?></strong>
		</div>
		<div class="sg-sand-panel">
			<span class="sg-meta"><?php esc_html_e( 'Location', 'sutighar' ); ?></span>
			<strong><?php esc_html_e( 'Based in Dhaka, Bangladesh — We ship nationwide', 'sutighar' ); ?></strong>
		</div>
	</div>
	<h2><?php esc_html_e( 'Find us online', 'sutighar' ); ?></h2>
	<p><a href="https://instagram.com/sutighar" target="_blank" rel="noopener">Instagram</a> · <a href="https://facebook.com/sutighar" target="_blank" rel="noopener">Facebook</a></p>
	<h2><?php esc_html_e( 'Before you write', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'For returns or exchanges, check the policy first. For fit questions, compare your preferred size with the size chart. For order support, include your order reference.', 'sutighar' ); ?></p>
	<div class="sg-sand-panel">
		<p><?php esc_html_e( 'Sutighar is run by two people — Emran and Shoikot — so your message reaches the same team that manages the collection.', 'sutighar' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Sutighar', 'sutighar' ); ?></a>
	</div>
</section>
<?php
get_footer();
