<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

/**
 * ExUnit固有の関数だが、ExUnitの機能を複製している他のプラグインにも使用されるものもある
 */

if ( ! function_exists( 'veu_get_capability_required' ) ) {
	function veu_get_capability_required() {
		return add_filter( 'veu_get_capability_required', 'edit_theme_options' );
	}
}

if ( ! function_exists( 'veu_get_systemlogo_html' ) ) {
	function veu_get_systemlogo_html() {
		$logo  = '<div class="logo_exUnit">';
		$logo .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', veu_get_directory_uri( '/images/head_logo_ExUnit.png' ) ) . '" alt="VK ExUnit" />';
		$logo .= '</div>';
		$logo  = apply_filters( 'veu_get_systemlogo_html', $logo );
		return $logo;
	}
}

if ( ! function_exists( 'veu_content_filter_state' ) ) {
	function veu_content_filter_state() {
		// $opt = veu_get_common_options();
		// return empty( $opt['content_filter_state'] )? 'content' : $opt['content_filter_state'];
		// コンテンツループ下部に出力すると誤動作が多いので、一旦コンテンツ下部出力に強制変更
		return 'content';
	}
}

if ( ! function_exists( 'veu_get_name' ) ) {
	function veu_get_name() {
		$system_name = apply_filters( 'veu_get_name', 'VK All in one Expansion Unit' );
		return $system_name;
	}
}

if ( ! function_exists( 'veu_get_little_short_name' ) ) {
	function veu_get_little_short_name() {
			$little_short_name = apply_filters( 'veu_get_little_short_name', 'ExUnit' );
			return $little_short_name;
	}
}

if ( ! function_exists( 'veu_get_short_name' ) ) {
	function veu_get_short_name() {
		$short_name = apply_filters( 'veu_get_short_name', 'VK' );
		return $short_name;
	}
}
if ( ! function_exists( 'veu_get_prefix' ) ) {
	function veu_get_prefix() {
		$prefix = apply_filters( 'veu_get_prefix', 'VK' );
		if ( $prefix ) {
			$prefix .= ' ';
		}
		return $prefix;
	}
}
if ( ! function_exists( 'veu_get_prefix_customize_panel' ) ) {
	function veu_get_prefix_customize_panel() {
		$prefix = apply_filters( 'veu_get_prefix_customize_panel', 'ExUnit' );
		if ( $prefix ) {
			$prefix .= ' ';
		}
		return $prefix;
	}
}

if ( ! function_exists( 'veu_is_cta_active' ) ) {
	function veu_is_cta_active() {
		if ( vk_is_plugin_active( 'vk-all-in-one-expansion-unit/vkExUnit.php' ) ) {
			$veu_common_options = get_option( 'vkExUnit_common_options' );
			if ( isset( $veu_common_options['active_call_to_action'] ) && $veu_common_options['active_call_to_action'] ) {
				return true;
			}
		}
	}
}

require_once( 'template-tags-veu-old.php' );
