<?php
/*
  Load modules
/*-------------------------------------------*/
if ( ! function_exists( 'vkblocks_active' ) ) {
	
	// Set asset URL.
	define( 'VK_BLOCKS_URL', plugin_dir_url( __FILE__ ) . 'package/' );
	// Set version number.
	define( 'VK_BLOCKS_VERSION', '0.38.6' );

	global $vk_blocks_prefix;
	$vk_blocks_prefix = veu_get_prefix();

	require_once 'package/vk-blocks-functions.php';

	add_action(
		'plugins_loaded',
		function () {
			// Load language files.
			load_plugin_textdomain( 'vk-blocks', false, 'vk-all-in-one-expansion-unit/inc/vk-blocks/build/languages' );
		}
	);

}
