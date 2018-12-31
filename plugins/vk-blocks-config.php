<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! function_exists( 'vkblocks_active' ) ) {
	require_once( 'vk-blocks/vk-blocks-functions.php' );
	// Set asset URL.
	define( 'VK_BLOCKS_URL', plugin_dir_url( __FILE__ ) . 'vk-blocks/' );
	// Set version number.
	define( 'VK_BLOCKS_VERSION', '0.3.0' );

	global $vk_blocks_prefix;
	$vk_blocks_prefix = veu_get_prefix();

	add_action(
		'plugins_loaded', function () {
			//Load language files.
			load_plugin_textdomain( 'vk-blocks', false, 'vk-all-in-one-expansion-unit/plugins/vk-blocks/build/languages' );
		}
	);

}
