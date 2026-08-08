<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="sg-page sg-container sg-cart-page">
	<h1 class="entry-title"><?php esc_html_e( 'Your Cart', 'sutighar' ); ?></h1>
	<div class="sg-empty">
		<?php echo sutighar_inline_icon( 'cart' ); ?>
		<h2><?php esc_html_e( 'Your cart is empty', 'sutighar' ); ?></h2>
		<p><?php esc_html_e( 'Add a few carefully woven pieces to get started.', 'sutighar' ); ?></p>
		<a class="sg-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the collection', 'sutighar' ); ?></a>
	</div>
</section>
