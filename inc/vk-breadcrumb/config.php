<?php
/**
 * VK Bread Crumb Setting.
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 * @since 9.70.0
 */

// Load composer autoload.
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/vendor/autoload.php';

use VektorInc\VK_Breadcrumb\VkBreadcrumb;

add_action(
	'lightning_header_after',
	function() {
		$vk_breadcrumb = new VkBreadcrumb();
		$info = $vk_breadcrumb::get_array();
		// print '<pre style="text-align:left">';print_r($info);print '</pre>';
	}
);