<?php
get_header();

if ( have_posts() ) {
	the_post();
	$content = trim( get_the_content() );
	if ( $content ) {
		the_content();
	} else {
		echo do_blocks( sutighar_default_home_blocks() );
	}
} else {
	echo do_blocks( sutighar_default_home_blocks() );
}

get_footer();
