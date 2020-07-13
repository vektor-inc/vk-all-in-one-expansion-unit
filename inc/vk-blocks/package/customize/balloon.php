<?php
/**
 * FaceList Customize.
 */
function vkblocks_balloon_meta_customize( $wp_customize ) {
	$max_faces = 15;
	$max_faces = apply_filters( 'vkblocks_max_faces', $max_faces );
	global $vk_blocks_prefix;

	$wp_customize->add_section(
		'vk_blocks_balloon_default_icons',
		array(
			'title' =>  __( 'Balloon: Default Icons', 'vk-blocks-pro' ),
			'panel' => 'vk_blocks_setting',
		)
	);

	for ( $i = 1; $i <= $max_faces; $i++ ) {
		// image.
		$wp_customize->add_setting(
			'vk_blocks_balloon_meta[default_icons]['. $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'vk_blocks_balloon_meta[default_icons]['. $i . ']',
				array(
					'label'       => __( 'Default Icon ', 'vk-blocks-pro' ) . '[' . $i . '] ',
					'section'     => 'vk_blocks_balloon_default_icons',
					'settings'    => 'vk_blocks_balloon_meta[default_icons]['. $i . ']',
				)
			)
		);
	}

}
add_action( 'customize_register', 'vkblocks_balloon_meta_customize' );
