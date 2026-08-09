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
			'vkExUnit_common_style',
		)
	);
	return $vk_css_tree_shaking_handles;
}
add_filter( 'vk_css_tree_shaking_handles', 'veu_css_tree_shaking_handles' );

/**
 * CSS Tree Shaking Exclude
 *
 * @param array $jsaddlist CSS Tree Shaking Exclude Paramator.
 */
function veu_css_tree_shaking_js_added_class_class( $jsaddlist ) {
	$exclude_classes_array = array(
		'page_top_btn',
		'scrolled',
	);
	$jsaddlist      = array_merge( $jsaddlist, $exclude_classes_array );

	return $jsaddlist;
}
add_filter( 'css_tree_shaking_js_added_class', 'veu_css_tree_shaking_js_added_class_class' );
