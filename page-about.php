<?php
/**
 * Template Name: Sutighar About
 */

get_header();
?>
<section class="sg-page sg-page--read sg-container sg-about-page">
	<p class="sg-eyebrow"><?php esc_html_e( 'About Us', 'sutighar' ); ?></p>
	<h1 class="entry-title"><?php esc_html_e( "We don't sell lungi. We sell comfort.", 'sutighar' ); ?></h1>
	<p class="sg-page-lead"><?php esc_html_e( 'Sutighar — the home of quality lungi. Hand-picked cotton, chosen for everyday comfort, not just for the shelf.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'How it started', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'It started with something small. A friend picked up six lungis from the local market, and when they got home, three of them turned out to be polyester, not the cotton the shopkeeper had promised hand on heart at the time of sale.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( "We don't even think the salesman was lying. He probably didn't know either. That's how far removed most sellers are from what they're actually selling. And that's the real problem: not one dishonest shop, but a market where nobody along the chain can tell you what's in your hands.", 'sutighar' ); ?></p>
	<p><?php esc_html_e( "That one bad batch led to a bigger question: lungi is one of the most worn pieces of clothing for men across Bangladesh, at home, at prayer, at rest. Almost every man owns one. So why doesn't this country have enough lungi brands people can actually trust?", 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'We could not find a good answer. So we started Sutighar instead — a place where "100% cotton" means exactly that, every single time.', 'sutighar' ); ?></p>

	<h2><?php esc_html_e( 'Who we are', 'sutighar' ); ?></h2>
	<p><?php esc_html_e( 'Sutighar is built by two co-founders, Emran and Shoikot, based in Dhaka. We both come from the tech side, years spent building software products, and we were looking for a new kind of challenge. Not another app, but a real, everyday problem in our own local market that nobody seemed to be solving properly.', 'sutighar' ); ?></p>
	<p><?php esc_html_e( 'Lungi turned out to be exactly that: something almost every household already buys, with almost no brand behind it. That gap is what pulled us in.', 'sutighar' ); ?></p>

	<div class="sg-founder-grid sg-founder-grid--quotes">
		<div class="sg-founder-quote">
			<h3><?php esc_html_e( 'Emran', 'sutighar' ); ?></h3>
			<span><?php esc_html_e( 'Co-founder', 'sutighar' ); ?></span>
			<blockquote><?php esc_html_e( "We didn't want to build another app. We wanted to fix something people actually touch every day.", 'sutighar' ); ?></blockquote>
		</div>
		<div class="sg-founder-quote">
			<h3><?php esc_html_e( 'Shoikot', 'sutighar' ); ?></h3>
			<span><?php esc_html_e( 'Co-founder', 'sutighar' ); ?></span>
			<blockquote><?php esc_html_e( "If we can't guarantee it's real cotton, we don't sell it. That's the whole rule.", 'sutighar' ); ?></blockquote>
		</div>
	</div>

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
