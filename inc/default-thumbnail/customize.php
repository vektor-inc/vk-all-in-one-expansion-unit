<?php 
/*
  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_default_thumbnail' );
function veu_customize_panel_activation_default_thumbnail() {
	return true;
}

if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
	add_action( 'customize_register', 'veu_customize_register_default_thumbnail', 20 );
}

function veu_customize_register_default_thumbnail( $wp_customize ) {
	/*
	  Defualt Thumbnail Settings
	 /*-------------------------------------------*/
	// 1. テーマカスタマイザー上に新しいセクションを追加
	$wp_customize->add_section(
		'veu_default_thumbnail_setting',
		array(
			'title'    => __( 'Defualt Thumbnail Settings', 'vk-all-in-one-expansion-unit' ),
			'panel'    => 'veu_setting',
		)
	);

	// defualt list image.
	$wp_customize->add_setting(
		'veu_defualt_thumbnail[default_thumbnail_image]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'vk_sanitize_number',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'veu_defualt_thumbnail[default_thumbnail_image]',
			array(
				'label'       =>  __( 'Default List Image', 'lightning' ),
				'section'     => 'veu_default_thumbnail_setting',
				'settings'    => 'veu_defualt_thumbnail[default_thumbnail_image]',
				'description' => '',
				'mime_type' => 'image',
				'priority'    => 700,
			)
		)
	);
}
