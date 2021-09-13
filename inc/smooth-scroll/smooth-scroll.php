<?php

add_filter( 'vkExUnit_master_js_options', function( $options ){
	$options['enable_smooth_scroll'] = true;
	return $options;
}, 10, 1 );

/**
 * Smooth scroll polyfillの読み込み
 */
function veu_load_smooth_scroll_polyfill(){
	global $vkExUnit_version;
	wp_enqueue_script(
		'smooth-scroll-polyfill-js', 
		plugin_dir_url(__FILE__) . 'js/smooth-scroll-polyfill.js',
		array(),
		$vkExUnit_version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'veu_load_smooth_scroll_polyfill' );
