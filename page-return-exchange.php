<?php
/**
 * Template Name: Sutighar Return & Exchange
 */

get_header();

$inside   = sutighar_option( 'return_inside_dhaka_fee', '120' );
$outside  = sutighar_option( 'return_outside_dhaka_fee', '200' );
$fault    = sutighar_option( 'fault_window_days', '3' );
$exchange = sutighar_option( 'exchange_window_days', '2' );
?>
<section class="sg-page sg-page--read sg-container sg-policy">
	<p class="sg-eyebrow"><?php esc_html_e( 'Customer Care', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Return & Exchange Policy', 'sutighar' ); ?></h1>
	<p><?php esc_html_e( 'Please check your product carefully at delivery. We want every customer to receive the right product in good condition.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Check your product at delivery', 'sutighar' ); ?></h2>
	<ul>
		<li><?php esc_html_e( 'Open and inspect the product while the delivery person is present whenever possible.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Check the size, color, fabric condition, and quantity before accepting.', 'sutighar' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'If the issue is our fault', 'sutighar' ); ?></h2>
	<ul>
		<li><?php echo wp_kses_post( sprintf( __( 'If you receive a wrong, damaged, or defective product, contact us within <strong>%s days</strong>.', 'sutighar' ), esc_html( $fault ) ) ); ?></li>
		<li><?php esc_html_e( 'We will arrange a return or exchange after checking the issue.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'If the mistake is from our side, you do not pay the extra delivery charge.', 'sutighar' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'Exchange policy', 'sutighar' ); ?></h2>
	<ul>
		<li><?php echo wp_kses_post( sprintf( __( 'For size or preference exchange, contact us within <strong>%s days</strong> of delivery.', 'sutighar' ), esc_html( $exchange ) ) ); ?></li>
		<li><?php esc_html_e( 'The product must be unused, unwashed, and in sellable condition.', 'sutighar' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'When delivery charges apply', 'sutighar' ); ?></h2>
	<div class="sg-founder-grid">
		<div class="sg-sand-panel"><span class="sg-meta"><?php esc_html_e( 'Inside Dhaka', 'sutighar' ); ?></span><strong><?php echo esc_html( '৳' . $inside ); ?></strong></div>
		<div class="sg-sand-panel"><span class="sg-meta"><?php esc_html_e( 'Outside Dhaka', 'sutighar' ); ?></span><strong><?php echo esc_html( '৳' . $outside ); ?></strong></div>
	</div>

	<h2><?php esc_html_e( 'Important notes', 'sutighar' ); ?></h2>
	<ul>
		<li><?php esc_html_e( 'Products that are used, washed, altered, or damaged after delivery cannot be returned or exchanged.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Please keep your order reference and photos ready when contacting us.', 'sutighar' ); ?></li>
	</ul>

	<div class="sg-sand-panel">
		<p><?php esc_html_e( 'Need help with a return or exchange? Message us on WhatsApp with your order reference.', 'sutighar' ); ?></p>
		<a class="sg-btn" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Message on WhatsApp', 'sutighar' ); ?></a>
	</div>
</section>
<?php
get_footer();
