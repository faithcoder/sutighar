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
<section class="sg-page sg-page--read sg-container sg-policy sg-policy-page">
	<p class="sg-eyebrow"><?php esc_html_e( 'Customer Care', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Return & Exchange Policy', 'sutighar' ); ?></h1>
	<p class="sg-page-lead"><?php esc_html_e( 'Please check your product carefully at delivery. We want every customer to receive the right piece, in the right size, and in good condition.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Check your product at delivery', 'sutighar' ); ?></h2>
	<ul class="sg-policy-list">
		<li><?php esc_html_e( 'Open and inspect the product while the delivery person is present whenever possible.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Check the size, color, fabric condition, and quantity before accepting the parcel.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'If anything looks wrong, take clear photos and message us before using or washing the product.', 'sutighar' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'If the issue is our fault', 'sutighar' ); ?></h2>
	<p><?php echo esc_html( sprintf( __( 'If you receive a wrong, damaged, or defective product, contact us within %s days of delivery.', 'sutighar' ), $fault ) ); ?></p>
	<p><?php esc_html_e( 'After checking the issue, we will arrange a return or exchange. If the mistake is from our side, you do not pay the extra delivery charge.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Exchange policy', 'sutighar' ); ?></h2>
	<p><?php echo esc_html( sprintf( __( 'For size or preference exchange, contact us within %s days of delivery.', 'sutighar' ), $exchange ) ); ?></p>
	<ul class="sg-policy-list">
		<li><?php esc_html_e( 'The product must be unused, unwashed, and in sellable condition.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Original packaging, tags, or invoice should be kept where possible.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Exchange depends on stock availability for the requested size or design.', 'sutighar' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'When delivery charges apply', 'sutighar' ); ?></h2>
	<div class="sg-contact-grid sg-policy-fees">
		<div class="sg-info-card">
			<span><?php esc_html_e( 'Inside Dhaka', 'sutighar' ); ?></span>
			<strong><?php echo esc_html( '৳ ' . $inside ); ?></strong>
			<p><?php esc_html_e( 'Exchange delivery charge when the product is not faulty.', 'sutighar' ); ?></p>
		</div>
		<div class="sg-info-card">
			<span><?php esc_html_e( 'Outside Dhaka', 'sutighar' ); ?></span>
			<strong><?php echo esc_html( '৳ ' . $outside ); ?></strong>
			<p><?php esc_html_e( 'Courier charge may vary slightly by location and parcel size.', 'sutighar' ); ?></p>
		</div>
	</div>

	<h2><?php esc_html_e( 'Important notes', 'sutighar' ); ?></h2>
	<ul class="sg-policy-list">
		<li><?php esc_html_e( 'Products that are used, washed, altered, or damaged after delivery cannot be returned or exchanged.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Color may vary slightly because of screen settings and photography light.', 'sutighar' ); ?></li>
		<li><?php esc_html_e( 'Please keep your order reference and photos ready when contacting us.', 'sutighar' ); ?></li>
	</ul>

	<div class="sg-sand-panel sg-contact-note">
		<p><?php esc_html_e( 'Need help with a return or exchange? Message us on WhatsApp with your order reference.', 'sutighar' ); ?></p>
		<a class="sg-btn" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Message us on WhatsApp', 'sutighar' ); ?></a>
	</div>
</section>
<?php
get_footer();
