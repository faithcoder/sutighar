<?php
defined( 'ABSPATH' ) || exit;

global $product;

$wanted = array( 'Brand', 'Fabric', 'Mercerized', 'Loom Type', 'Border', 'Wash Type' );
$specs  = array();
foreach ( $wanted as $label ) {
	$value = sutighar_product_spec_value( $product, $label );
	if ( '' !== trim( (string) $value ) ) {
		$specs[ $label ] = $value;
	}
}

if ( ! $specs ) {
	return;
}
?>
<div class="sg-spec">
	<span class="sg-meta"><?php esc_html_e( 'Specification', 'sutighar' ); ?></span>
	<?php foreach ( $specs as $label => $value ) : ?>
		<div class="sg-spec__row">
			<span><?php echo esc_html( $label ); ?></span>
			<strong><?php echo esc_html( $value ); ?></strong>
		</div>
	<?php endforeach; ?>
</div>
