<?php
defined( 'ABSPATH' ) || exit;

global $product;

$wanted = array( 'Brand', 'Fabric', 'Mercerized', 'Loom Type', 'Border', 'Wash Type' );
?>
<div class="sg-spec">
	<span class="sg-meta"><?php esc_html_e( 'Specification', 'sutighar' ); ?></span>
	<?php foreach ( $wanted as $label ) : ?>
		<?php
		$value = sutighar_product_spec_value( $product, $label );
		?>
		<div class="sg-spec__row">
			<span><?php echo esc_html( $label ); ?></span>
			<strong><?php echo esc_html( $value ? $value : '—' ); ?></strong>
		</div>
	<?php endforeach; ?>
</div>
