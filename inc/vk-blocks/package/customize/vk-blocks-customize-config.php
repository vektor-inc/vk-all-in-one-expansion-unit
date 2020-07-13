<?php
/**
 * VK Blocks Customize Config
 */

function vkblocks_customize( $wp_customize ) {
    $vk_blocks_prefix = 'VK Blocks Pro ';

	$wp_customize->add_panel(
		'vk_blocks_setting',
		array(
			'title' => $vk_blocks_prefix . __( 'Setting', 'vk-blocks-pro' ),
		)
	);
}
add_action( 'customize_register', 'vkblocks_customize' );

require_once dirname( __FILE__ ) . '/balloon.php';
