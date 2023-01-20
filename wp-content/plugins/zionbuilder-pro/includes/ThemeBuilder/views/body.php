<?php

use ZionBuilderPro\Plugin;

get_header();

if ( is_single() ) {
	while ( have_posts() ) {
		the_post();
		Plugin::$instance->theme_builder->render_template( 'body' );
	}
} else {
	Plugin::$instance->theme_builder->render_template( 'body' );
}

get_footer();
