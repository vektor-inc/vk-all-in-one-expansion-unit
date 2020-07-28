<?php
function vkblocks_load_slider_scripts() {

	if ( has_block( 'vk-blocks/slider' ) ) {
		wp_enqueue_style('vkblocks-swiper',  VK_BLOCKS_URL . 'build/swiper.min.css', array(),'5.4.1', 'all');
		wp_enqueue_script( 'vkblocks-swiper-js', VK_BLOCKS_URL . 'build/swiper.min.js', array(),'5.4.1',false );
	}
}
add_action( 'wp_enqueue_scripts', 'vkblocks_load_slider_scripts' );
