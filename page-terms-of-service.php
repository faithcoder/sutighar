<?php
/**
 * Terms of Service page template.
 */

get_header();
?>
<section class="sg-page sg-page--read sg-container sg-terms-page">
	<p class="sg-eyebrow"><?php esc_html_e( 'Legal', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( 'Terms & Conditions', 'sutighar' ); ?></h1>
	<p class="sg-page-lead"><?php esc_html_e( 'Welcome to Sutighar - the home of quality lungi. By browsing this site or placing an order, you accept the terms below. "You" means anyone using the site; "we" means Sutighar, run by its two co-founders in Dhaka.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'Last updated August 2026', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Products and descriptions', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We describe every lungi as accurately as we can - fabric, size, brand, and finish. Colours may still look slightly different on your screen than in your hands, because of lighting and display settings. That variation is not a defect. If a product arrives genuinely wrong or faulty, our Return & Exchange Policy applies.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Prices and availability', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'All prices are in Bangladeshi Taka and include the price of the product only; delivery is charged separately at checkout. Stock counts are kept as current as we can, but if an item sells out after you order we will contact you to arrange a replacement or a refund.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Orders', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'After you place an order you will get a confirmation, and we will reach out on WhatsApp - normally within a few hours - to confirm it. You can change or cancel your order any time before it has been handed to the courier; just message us. Once it is with the courier it cannot be cancelled, but you still have your rights on delivery under the returns policy.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'We do not cancel orders without a reason, but we may decline or cancel one - for example if stock is genuinely unavailable, the delivery address is unreachable, or we believe the order is fraudulent. If you have already paid, we refund in full.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Payment', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We accept cash on delivery, bKash, and Nagad. For mobile payments, send the amount to the merchant number shown at checkout and enter the transaction ID so we can match it to your order. Please keep exact change ready for cash on delivery.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Delivery', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We deliver nationwide through third-party courier partners. Delivery windows are estimates: couriers can be delayed by weather, strikes, or their own routing, and those delays are outside our control. We will keep you updated on WhatsApp if your order is running late.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Returns and exchanges', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'Covered in full by our Return & Exchange Policy, which forms part of these terms. In short: inspect your order at delivery, tell us within 3 days if something is our fault, and within 2 days if you want an exchange.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Using this site', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'The photographs, text, and branding on this site belong to Sutighar. Please do not reuse them commercially without asking. Do not attempt to disrupt the site or place orders you do not intend to accept.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Changes to these terms', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'We may update these terms, and the new version takes effect once published here. Continuing to use the site means you accept it. The terms that applied when you placed an order are the ones that govern that order.', 'sutighar' ); ?></p>
</section>
<?php
get_footer();
