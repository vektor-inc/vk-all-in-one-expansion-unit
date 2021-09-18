<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package ExUnit
 */

if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-css-optimize.php';
}

function veu_css_tree_shaking_array( $vk_css_tree_shaking_array ){
	$vk_css_tree_shaking_array[] = array(
		'id'      => 'vkExUnit_common_style',
		'url'     => VEU_DIRECTORY_URI . '/assets/css/vkExUnit_style.css',
		'path'    => VEU_DIRECTORY_PATH . '/assets/css/vkExUnit_style.css',
		'version' => VEU_VERSION,
	);
	return $vk_css_tree_shaking_array;
}
add_filter( 'vk_css_tree_shaking_array', 'veu_css_tree_shaking_array' );

/**
 * CSS Tree Shaking Exclude
 *
 * @param array $inidata CSS Tree Shaking Exclude Paramator.
 */
function veu_css_tree_shaking_exclude_class( $inidata ) {
	$exclude_classes_array = array(
		'page_top_btn',
		'scrolled',
	);
	$inidata['class']      = array_merge( $inidata['class'], $exclude_classes_array );

	return $inidata;
}
add_filter( 'css_tree_shaking_exclude', 'veu_css_tree_shaking_exclude_class' );
