<?php

/*
  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_ga' );
function veu_customize_panel_activation_ga() {
	return true;
}

if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
	add_action( 'customize_register', 'veu_customize_register_ga', 20 );
}

function veu_customize_register_ga( $wp_customize ) {

	/*
	  ga Settings セクション、テーマ設定、コントロールを追加
	 /*-------------------------------------------*/
	// 1. テーマカスタマイザー上に新しいセクションを追加
	$wp_customize->add_section(
		'veu_ga_setting',
		array(
			'title'    => __( 'Google Analtics Settings', 'vk-all-in-one-expansion-unit' ),
			'priority' => 1,
			'panel'    => 'veu_setting',
		)
	);

	// 2. WPデータベースに新しいテーマ設定を追加
	// Google Analytics ID (GA4)
	$wp_customize->add_setting(
		'vkExUnit_ga_options[gaId-GA4]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ExUnit_Custom_Text_Control(
			$wp_customize,
			'gaId-GA4',
			array(
				'label'        => __( 'Google Analytics ID ( GA4 )', 'vk-all-in-one-expansion-unit' ),
				'section'      => 'veu_ga_setting',
				'settings'     => 'vkExUnit_ga_options[gaId-GA4]',
				'type'         => 'text',
				'description'  => __( 'Please fill in the Google Analytics ID ( GA4 ) from the Analytics embed code used in the site.<br>ex) XXXXXXXXXX', 'vk-all-in-one-expansion-unit' ),
				'input_before' => 'UA-',
			)
		)
	);

	// Google Analytics ID (UA)
	$wp_customize->add_setting(
		'vkExUnit_ga_options[gaId-UA]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ExUnit_Custom_Text_Control(
			$wp_customize,
			'gaId-UA',
			array(
				'label'        => __( 'Google Analytics ID ( UA )', 'vk-all-in-one-expansion-unit' ),
				'section'      => 'veu_ga_setting',
				'settings'     => 'vkExUnit_ga_options[gaId-UA]',
				'type'         => 'text',
				'description'  => __( 'Please fill in the Google Analytics ID ( UA ) from the Analytics embed code used in the site.<br>ex) UA-XXXXXXXX-XX', 'vk-all-in-one-expansion-unit' ),
				'input_before' => 'UA-',
			)
		)
	);

	// Disable Logged in
	$wp_customize->add_setting(
		'vkExUnit_ga_options[disableLoggedin]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableOGTags',
		array(
			'label'    => __( 'Disable tracking of logged in user', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_ga_setting',
			'settings' => 'vkExUnit_ga_options[disableLoggedin]',
			'type'     => 'checkbox',
		)
	);

} // function veu_customize_register_ga( $wp_customize )
