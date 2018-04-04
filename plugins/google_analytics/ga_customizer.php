<?php

/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_ga' );
function veu_customize_panel_activation_ga() {
	return true;
}

if ( apply_filters( 'veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_ga' , 20 );
}

function veu_customize_register_ga( $wp_customize ) {

 	/*-------------------------------------------*/
 	/*	ga Settings セクション、テーマ設定、コントロールを追加
 	/*-------------------------------------------*/
  //1. テーマカスタマイザー上に新しいセクションを追加
 	$wp_customize->add_section( 'veu_ga_setting',
		array(
	 		'title'				=> __( 'Google Analtics Settings', 'vkExUnit' ),
	 		'priority'		=> 1,
	 		'panel'				=> 'veu_setting',
		)
	);

	//2. WPデータベースに新しいテーマ設定を追加
	// Google Analytics ID
	$wp_customize->add_setting( 'vkExUnit_ga_options[gaId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new Custom_Text_Control(
			$wp_customize, 'gaId', array(
				'label'    => __( 'Google Analytics ID', 'vkExUnit' ),
				'section'  => 'veu_ga_setting',
				'settings' => 'vkExUnit_ga_options[gaId]',
				'type'     => 'text',
				'description' => __( 'Please fill in the Google Analytics ID from the Analytics embed code used in the site.<br>ex) XXXXXXXX-X', 'vkExUnit' ),
				'input_before' => 'UA-',
			)
		)
	);

	// Select the type of Analytics code
	$wp_customize->add_setting( 'vkExUnit_ga_options[gaType]',
	array(
			'default'           => 'gaType_universal',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_attr',
		)
	);
	$wp_customize->add_control( 'gaType',
	array(
			'label'    => __( 'Select the type of Analytics code', 'vkExUnit' ),
			'section'  => 'veu_ga_setting',
			'settings' => 'vkExUnit_ga_options[gaType]',
			'type'     => 'radio',
			// 'priority' => $priority,
			'choices'  => array(
				'gaType_universal' => __( 'To output the Universal Analytics code (default)', 'vkExUnit' ),
				'gaType_normal'    => __( 'To output only normal code', 'vkExUnit' ),
				'gaType_both'      => __( 'To output both types', 'vkExUnit' ),
			),
			'description' => __( 'Print the select the type of Analytics code.<br>(If you are unsure you can skip this.)', 'vkExUnit' ),
		)
	);

} // function veu_customize_register_ga( $wp_customize )
