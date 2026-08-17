<?php
defined( 'ABSPATH' ) || exit;

global $product;

$ids = $product->get_gallery_image_ids();
$main_id = $product->get_image_id();
$fallback_full = sutighar_asset( 'assets/images/product-placeholder.svg' );
$gallery_items = array();
if ( ! $main_id ) {
	$main_full = $fallback_full;
	$main_src  = $fallback_full;
	$main_alt  = $product->get_name();
	$main      = sprintf( '<img src="%s" width="600" height="750" alt="%s" data-sg-main-gallery-image>', esc_url( $main_src ), esc_attr( $main_alt ) );
} else {
	$main_full = wp_get_attachment_image_url( $main_id, 'full' );
	$main_full = $main_full ? $main_full : $fallback_full;
	$main_src  = wp_get_attachment_image_url( $main_id, 'woocommerce_single' );
	$main_src  = $main_src ? $main_src : $main_full;
	$main_alt  = get_post_meta( $main_id, '_wp_attachment_image_alt', true );
	$main_alt  = $main_alt ? $main_alt : $product->get_name();
	$main      = wp_get_attachment_image( $main_id, 'woocommerce_single', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'data-sg-main-gallery-image' => '' ) );
}
$gallery_items[] = array(
	'display' => $main_src,
	'full'    => $main_full,
	'alt'     => $main_alt,
);
foreach ( array_filter( $ids ) as $gallery_id ) {
	$item_full = wp_get_attachment_image_url( $gallery_id, 'full' );
	$item_src  = wp_get_attachment_image_url( $gallery_id, 'woocommerce_single' );
	$item_alt  = get_post_meta( $gallery_id, '_wp_attachment_image_alt', true );
	$gallery_items[] = array(
		'display' => $item_src ? $item_src : ( $item_full ? $item_full : $fallback_full ),
		'full'    => $item_full ? $item_full : $fallback_full,
		'alt'     => $item_alt ? $item_alt : $product->get_name(),
	);
}
$thumbs = array_slice( array_filter( $ids ), 0, 2 );
?>
<div class="sg-pdp__gallery" data-sg-product-gallery data-sg-gallery-items="<?php echo esc_attr( wp_json_encode( $gallery_items ) ); ?>">
	<script type="application/json" data-sg-gallery-json><?php echo wp_json_encode( $gallery_items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
	<div class="sg-pdp__thumbs">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<?php
			$thumb_index = $i + 1;
			$thumb_full = $fallback_full;
			$thumb_alt  = $product->get_name();
			$thumb_src  = $fallback_full;
			$thumb_img  = sprintf( '<img src="%s" width="600" height="750" loading="lazy" decoding="async" alt="%s">', esc_url( $fallback_full ), esc_attr( $product->get_name() ) );
			if ( isset( $thumbs[ $i ] ) ) {
				$thumb_full = wp_get_attachment_image_url( $thumbs[ $i ], 'full' );
				$thumb_full = $thumb_full ? $thumb_full : $fallback_full;
				$thumb_src  = wp_get_attachment_image_url( $thumbs[ $i ], 'woocommerce_single' );
				$thumb_src  = $thumb_src ? $thumb_src : $thumb_full;
				$thumb_alt  = get_post_meta( $thumbs[ $i ], '_wp_attachment_image_alt', true );
				$thumb_alt  = $thumb_alt ? $thumb_alt : $product->get_name();
				$thumb_img  = wp_get_attachment_image( $thumbs[ $i ], 'woocommerce_thumbnail', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
			}
			?>
			<button class="sg-pdp__thumb" type="button" data-sg-gallery-open data-sg-gallery-index="<?php echo esc_attr( $thumb_index ); ?>" data-sg-gallery-display="<?php echo esc_url( $thumb_src ); ?>" data-sg-gallery-full="<?php echo esc_url( $thumb_full ); ?>" data-sg-gallery-alt="<?php echo esc_attr( $thumb_alt ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Open %s gallery image', 'sutighar' ), $product->get_name() ) ); ?>">
				<?php echo $thumb_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		<?php endfor; ?>
	</div>
	<button class="sg-pdp__main" type="button" data-sg-gallery-open data-sg-gallery-index="0" data-sg-gallery-full="<?php echo esc_url( $main_full ); ?>" data-sg-gallery-alt="<?php echo esc_attr( $main_alt ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Open %s image gallery', 'sutighar' ), $product->get_name() ) ); ?>"><?php echo $main; ?></button>
</div>
<div class="sg-gallery-modal" data-sg-gallery-modal hidden>
	<div class="sg-gallery-modal__backdrop" data-sg-gallery-close></div>
	<div class="sg-gallery-modal__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Product image gallery', 'sutighar' ); ?>">
		<button class="sg-gallery-modal__close" type="button" data-sg-gallery-close aria-label="<?php esc_attr_e( 'Close image gallery', 'sutighar' ); ?>">×</button>
		<button class="sg-gallery-modal__nav sg-gallery-modal__nav--prev" type="button" data-sg-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'sutighar' ); ?>"></button>
		<button class="sg-gallery-modal__nav sg-gallery-modal__nav--next" type="button" data-sg-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'sutighar' ); ?>"></button>
		<div class="sg-gallery-modal__counter" data-sg-gallery-counter></div>
		<div class="sg-gallery-modal__tools" aria-label="<?php esc_attr_e( 'Image zoom controls', 'sutighar' ); ?>">
			<button type="button" data-sg-gallery-zoom="out" aria-label="<?php esc_attr_e( 'Zoom out', 'sutighar' ); ?>">−</button>
			<button type="button" data-sg-gallery-zoom="reset"><?php esc_html_e( '100%', 'sutighar' ); ?></button>
			<button type="button" data-sg-gallery-zoom="in" aria-label="<?php esc_attr_e( 'Zoom in', 'sutighar' ); ?>">+</button>
		</div>
		<img src="" alt="" data-sg-gallery-image>
	</div>
</div>
