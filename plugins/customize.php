<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'vkExUnit_add_customizer' );
function vkExUnit_add_customizer() {
  add_action( 'customize_register', 'vkExUnit_customize_register' );
}

/*	Add sanitize checkbox
/*-------------------------------------------*/
function vkExUnit_sanitize_boolean( $input ){
	if( $input == true ){
		return true;
	} else {
		return false;
	}
}

function vkExUnit_sanitize_radio($input){
	return esc_attr( $input );
}

function vkExUnit_customize_register( $wp_customize ) {
	/*-------------------------------------------*/
	/*	ExUnit Panel
	/*-------------------------------------------*/
	$wp_customize->add_panel( 'ex_unit_setting', array(
	   	'priority'       => 1000,
	   	'capability'     => 'edit_theme_options',
	   	'theme_supports' => '',
	   	'title'          => __( 'ExUnit Settings', 'vkExUnit' ),
	));

	/*-------------------------------------------*/
	/*	Design setting
	/*-------------------------------------------*/
	$wp_customize->add_section( 'ex_unit_sns_setting', array(
		'title'				=> __('SNS Settings', 'vkExUnit'),
		'priority'			=> 1000,
		'panel'				=> 'ex_unit_setting',
	) );

  // Bin bg fill
	$wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
		'default'			=> false,
    'type'				=> 'option', // 保存先 option or theme_mod
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'vkExUnit_sanitize_boolean',
	) );

	$wp_customize->add_control( 'snsBtn_bg_fill_not', array(
		'label'		=> __( 'No background', 'vkExUnit' ),
		'section'	=> 'ex_unit_sns_setting',
		'settings'  => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
		'type'		=> 'checkbox',
		'priority'	=> 1,
	) );

  // Btn color
  $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_color]', array(
		'default'			=> false,
    'type'				=> 'option', // 保存先 option or theme_mod
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'snsBtn_color', array(
    'label'    => __('Btn color', 'vkExUnit'),
    'section'  => 'ex_unit_sns_setting',
    'settings' => 'vkExUnit_sns_options[snsBtn_color]',
    'priority' => 2,
  )));

  // $wp_customize->get_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]' )->transport        = 'postMessage';

  /*-------------------------------------------*/
	/*	Add Edit Customize Link Btn
	/*-------------------------------------------*/
  $wp_customize->selective_refresh->add_partial( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
    'selector' => '.veu_socialSet',
    'render_callback' => '',
  ) );
	// if( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ){
	// 	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key', array(
	// 		'label'    => _x('Key color', 'lightning theme-customizer', 'lightning'),
	// 		'section'  => 'lightning_design',
	// 		'settings' => 'lightning_theme_options[color_key]',
	// 		'priority' => 502,
	// 	)));

}
