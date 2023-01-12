<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package ExUnit
 */

 use VektorInc\VK_CSS_Optimize\VkCssOptimize;
 new VkCssOptimize();

function veu_css_tree_shaking_handles( $vk_css_tree_shaking_handles ) {

	$vk_css_tree_shaking_handles = array_merge(
		$vk_css_tree_shaking_handles,
		array(
			'veu-cta',
			'vkExUnit_common_style'
		)
	);
	return $vk_css_tree_shaking_handles;
}
add_filter( 'vk_css_tree_shaking_handles', 'veu_css_tree_shaking_handles' );

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
