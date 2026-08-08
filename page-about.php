<?php
/**
 * Template Name: Sutighar About
 */

get_header();
?>
<section class="sg-page sg-page--read sg-container">
	<p class="sg-eyebrow"><?php esc_html_e( 'About Sutighar · সুতিঘর', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Home of Quality Lungi.', 'sutighar' ); ?></h1>
	<p><?php esc_html_e( 'Sutighar exists to make everyday cotton lungi easier to choose, easier to trust, and easier to buy from anywhere in Bangladesh.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Why Sutighar exists', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'A good lungi is practical, comfortable, and personal. We hand-pick cotton pieces across solid, stripe, jacquard, batik print, and handloom collections so customers can buy with confidence.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'Each product is checked for fabric feel, color, stitching, border quality, and everyday usability before it reaches the catalogue.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'The goal is simple: dependable cotton lungi, clear sizing, fair prices, and responsive customer care.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'The people', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'Sutighar is run closely by its founders, from sourcing and quality checks through customer communication and fulfillment.', 'sutighar' ); ?></p>
	<div class="sg-founder-grid">
		<div class="sg-sand-panel">
			<h3><?php esc_html_e( 'Emran Hossain', 'sutighar' ); ?></h3>
			<span><?php esc_html_e( 'Co-founder · Field Lead', 'sutighar' ); ?></span>
			<p><?php esc_html_e( 'Sourcing, supplier relationships, stock QC, fulfillment, and business direction.', 'sutighar' ); ?></p>
		</div>
		<div class="sg-sand-panel">
			<h3><?php esc_html_e( 'Shoikot', 'sutighar' ); ?></h3>
			<span><?php esc_html_e( 'Co-founder · Studio Lead', 'sutighar' ); ?></span>
			<p><?php esc_html_e( 'Content, photography direction, customer communication, and digital presence.', 'sutighar' ); ?></p>
		</div>
	</div>
	<p><a class="sg-btn" href="<?php echo esc_url( sutighar_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Talk on WhatsApp', 'sutighar' ); ?></a></p>
</section>
<?php
get_footer();
