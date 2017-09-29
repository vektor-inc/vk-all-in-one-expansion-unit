<?php

if ( ! function_exists( 'vk_is_excerpt' ) ){
	function vk_is_excerpt() {
		global $wp_current_filter;
		if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) { return true; }
		return false;
	}
	// old function
	function vkExUnit_is_excerpt(){
		return vk_is_excerpt();
	}
}
