<?php
/*-------------------------------------------*/
/*  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_add_smooth_js' );
function veu_add_smooth_js() {
	global $vkExUnit_version;
	wp_register_script( 'vkExUnit_smooth-js', plugins_url( '', __FILE__ ) . '/js/smooth-scroll.min.js', array( 'jquery' ), $vkExUnit_version, true );
	wp_enqueue_script( 'vkExUnit_smooth-js' );
}
