<?php // phpcs:ignore
/**
 * Plugin Name: VK All in One Expansion Unit
 * Plugin URI: https://ex-unit.nagoya
 * Description: This plug-in is an integrated plug-in with a variety of features that make it powerful your web site. Many features can be stopped individually. Example Facebook Page Plugin,Social Bookmarks,Print OG Tags,Print Twitter Card Tags,Print Google Analytics tag,New post widget,Insert Related Posts and more!
 * Version: 9.99.3.0
 * Requires PHP: 7.4
 * Requires at least: 6.2
 * Author: Vektor,Inc.
 * Text Domain: vk-all-in-one-expansion-unit
 * Domain Path: /languages
 * Author URI: https://vektor-inc.co.jp
 * GitHub Plugin URI: vektor-inc/VK-All-in-One-Expansion-Unit
 * GitHub Plugin URI: https://github.com/vektor-inc/VK-All-in-One-Expansion-Unit
 * License: GPL2
 *
 * @package VK All in One Expansion Unit
 */

/*
Copyright 2015-2024 Vektor,Inc. ( email : kurudrive@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once __DIR__ . '/vendor/autoload.php';

// $sample = new Sample();

define( 'VEU_FONT_AWESOME_DEFAULT_VERSION', 5 );
define( 'VEU_DIRECTORY_PATH', __DIR__ );
define( 'VEU_DIRECTORY_URI', plugins_url( '', __FILE__ ) );

// Set Plugin version.
$data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
define( 'VEU_VERSION', $data['version'] );

require_once VEU_DIRECTORY_PATH . '/initialize.php';

if ( function_exists( 'register_deactivation_hook' ) ) {
	register_deactivation_hook( __FILE__, 'veu_uninstall_function' );
}

/**
 * Uninstall function
 *
 * @return void
 */
function veu_uninstall_function() {
	require_once VEU_DIRECTORY_PATH . '/initialize.php';
	include VEU_DIRECTORY_PATH . '/uninstaller.php';
}

/**
 * Modify the height of a specific CSS class to fix an issue in Chrome 77 with Gutenberg.
 *
 * @see https://github.com/WordPress/gutenberg/issues/17406
 */
add_action(
	'admin_head',
	function () {
		echo '<style>.block-editor-writing-flow { height: auto; }</style>'; // phpcs:ignore
	}
);
