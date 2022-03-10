<?php
/**
 * VK Blocks Font Awesome
 *
 * @package vk_blocks
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

/*
 * Font Awesome Load modules
 */
if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
	new VkFontAwesomeVersions();
	global $font_awesome_directory_uri;
	// phpcs:ignore
	$font_awesome_directory_uri = VEU_DIRECTORY_URI . 'vendor/vektor-inc/font-awesome-versions/src/';
}