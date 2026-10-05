<?php
/**
 * The template for displaying 404 pages.
 */

get_header();
?>
<main id="main" class="sg-404" role="main">
	<section class="sg-404__content" aria-labelledby="sg-404-title">
		<h1 id="sg-404-title"><?php esc_html_e( '404 Error', 'sutighar' ); ?></h1>
		<p><?php esc_html_e( "The page you're looking for isn't here, but our lungi collection is.", 'sutighar' ); ?></p>
		<a class="sg-btn sg-404__action" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'sutighar' ); ?></a>
	</section>
</main>
<?php
get_footer();
