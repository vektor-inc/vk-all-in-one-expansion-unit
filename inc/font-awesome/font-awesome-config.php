<?php
/**
 * Load Font Awesome
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;
if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'init' ) ) {
	VkFontAwesomeVersions::init();
}
