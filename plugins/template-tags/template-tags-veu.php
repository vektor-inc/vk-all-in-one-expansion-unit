<?php

if ( ! function_exists( 'veu_content_filter_state' ) )
{
	function veu_content_filter_state(){
		// $opt = vkExUnit_get_common_options();
		// return empty( $opt['content_filter_state'] )? 'content' : $opt['content_filter_state'];
		// コンテンツループ下部に出力すると誤動作が多いので、一旦コンテンツ下部出力に強制変更
		return 'content';
	}
}

if ( ! function_exists( 'veu_get_name' ) ){
	function veu_get_name() {
		$system_name = apply_filters( 'vkExUnit_get_name_custom','VK All in one Expansion Unit' );
		return $system_name;
	}
	// old function
	function vkExUnit_get_name() {
		return veu_get_name();
	}
}

if ( ! function_exists( 'veu_get_little_short_name' ) ){
		function veu_get_little_short_name(){
				$little_short_name = apply_filters( 'vkExUnit_get_little_short_name_custom','VK ExUnit' );
				return $little_short_name;
		}
		// old function
		function vkExUnit_get_little_short_name(){
				return veu_get_little_short_name();
		}
}

if ( ! function_exists( 'veu_get_short_name' ) ){
	function veu_get_short_name(){
	 $short_name = apply_filters( 'vkExUnit_get_short_name_custom','VK' );
	 return $short_name;
	}
	// old function
	function vkExUnit_get_short_name(){
		return veu_get_short_name();
	}
}
