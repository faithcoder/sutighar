<?php
/**
 * Template Name: Sutighar Privacy Policy
 */

get_header();
?>
<section class="sg-page sg-page--read sg-container sg-privacy-page">
	<p class="sg-eyebrow"><?php esc_html_e( 'Legal', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Privacy Policy', 'sutighar' ); ?></h1>
	<p class="sg-page-lead"><?php esc_html_e( 'This policy explains what we collect when you use sutighar.com, why we collect it, and what you can ask us to do with it. It covers this website only, not information you give us in person or over the phone.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'Last updated August 2026', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'What we collect', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We only ask for what an order needs. When you place one, that is your name, phone number, delivery address, and — if you choose to give it — your email. If you pay by bKash or Nagad you also give us a transaction ID. If you message us, we keep the conversation so we can help you.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'Our host also keeps standard server logs: IP address, browser, and the pages visited. These are used to keep the site running and to understand which products people look at. We do not try to tie them back to you.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Why we use it', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We use order details to confirm your order, arrange delivery, collect payment information when needed, and support returns or exchanges. We use basic website data to keep the store secure and improve the shopping experience.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'We do not sell your personal information. We only share what is needed with delivery partners, payment support, or service providers who help us operate the store.', 'sutighar' ); ?></p>

	<div class="sg-page-hello">
		<h2><?php esc_html_e( 'Say hello', 'sutighar' ); ?></h2>
		<p><?php esc_html_e( "We'd genuinely like to hear from you — a question about fabric, a size you're unsure of, or just to say hi.", 'sutighar' ); ?></p>
		<a class="sg-btn" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Message us on WhatsApp', 'sutighar' ); ?></a>
		<p class="sg-small-link-row">
			<?php esc_html_e( 'Or find us on', 'sutighar' ); ?>
			<a href="https://instagram.com/sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'Instagram', 'sutighar' ); ?></a>
			<?php esc_html_e( 'and', 'sutighar' ); ?>
			<a href="https://facebook.com/sutighar" target="_blank" rel="noopener"><?php esc_html_e( 'Facebook', 'sutighar' ); ?></a>.
		</p>
	</div>
</section>
<?php
get_footer();
