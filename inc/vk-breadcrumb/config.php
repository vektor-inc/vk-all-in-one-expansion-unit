<?php
/**
 * VK Bread Crumb Setting.
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 * @since 9.71.0
 */

use VektorInc\VK_Breadcrumb\VkBreadcrumb;

function veu_print_breadcrumb_scheme() {
	$vk_breadcrumb = new VkBreadcrumb();
	$vk_breadcrumb::the_scheme_script();
}
add_action( 'wp_head', 'veu_print_breadcrumb_scheme' );
