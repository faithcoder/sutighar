<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$title = woocommerce_page_title( false );
if ( is_shop() ) {
	$title = __( 'All Collection', 'sutighar' );
}
?>
<section class="sg-page sg-container">
	<header class="woocommerce-products-header">
		<div style="display:flex;gap:12px;align-items:baseline;flex-wrap:wrap">
			<h1 class="woocommerce-products-header__title page-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
				<span style="font-size:12px;color:var(--sg-ink-80)"><?php echo esc_html( sprintf( _n( '%s item', '%s items', wc_get_loop_prop( 'total' ), 'sutighar' ), number_format_i18n( wc_get_loop_prop( 'total' ) ) ) ); ?></span>
			<?php endif; ?>
		</div>
		<hr style="border:0;border-top:1px solid var(--sg-line);margin:18px 0 0">
		<?php do_action( 'woocommerce_archive_description' ); ?>
	</header>

	<?php if ( woocommerce_product_loop() ) : ?>
		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
		<?php woocommerce_product_loop_start(); ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php wc_get_template_part( 'content', 'product' ); ?>
		<?php endwhile; ?>
		<?php woocommerce_product_loop_end(); ?>
		<?php do_action( 'woocommerce_after_shop_loop' ); ?>
	<?php else : ?>
		<div class="sg-empty">
			<h2><?php esc_html_e( 'Nothing matches those filters.', 'sutighar' ); ?></h2>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Clear all filters', 'sutighar' ); ?></a>
		</div>
	<?php endif; ?>
</section>
<?php
get_footer( 'shop' );
