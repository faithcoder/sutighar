<?php
defined( 'ABSPATH' ) || exit;

global $product;

$measure = sutighar_product_size_measurements( $product );
$measure = array_filter( $measure, 'strlen' );
?>
<section class="sg-page sg-container sg-pdp">
	<?php do_action( 'woocommerce_before_single_product' ); ?>
	<div class="sg-pdp__grid">
		<?php wc_get_template( 'single-product/product-image.php' ); ?>
		<div class="sg-pdp__info">
			<h1 class="product_title entry-title"><?php the_title(); ?></h1>
			<div class="sg-pdp__price">
				<span class="sg-meta"><?php esc_html_e( 'Price', 'sutighar' ); ?></span>
				<div><strong><?php echo esc_html( sutighar_product_plain_price( $product ) ); ?></strong><span>BDT</span></div>
			</div>
			<?php if ( $measure ) : ?>
				<hr>
				<div class="sg-size-row">
					<?php if ( ! empty( $measure['size'] ) ) : ?>
						<div><span class="sg-meta"><?php esc_html_e( 'Size', 'sutighar' ); ?></span><strong><?php echo esc_html( $measure['size'] ); ?></strong></div>
					<?php endif; ?>
					<?php if ( ! empty( $measure['height'] ) ) : ?>
						<div><span class="sg-meta"><?php esc_html_e( 'Height', 'sutighar' ); ?></span><strong><?php echo esc_html( $measure['height'] ); ?></strong></div>
					<?php endif; ?>
					<?php if ( ! empty( $measure['waist'] ) ) : ?>
						<div><span class="sg-meta"><?php esc_html_e( 'Waist', 'sutighar' ); ?></span><strong><?php echo esc_html( $measure['waist'] ); ?></strong></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<hr>
			<?php woocommerce_template_single_add_to_cart(); ?>
			<hr>
			<?php wc_get_template( 'single-product/product-attributes.php' ); ?>
			<div class="sg-pdp__desc">
				<?php echo sutighar_product_description_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
	<?php woocommerce_output_related_products( array( 'posts_per_page' => 4, 'columns' => 4 ) ); ?>
</section>
