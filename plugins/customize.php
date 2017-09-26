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

	$wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_bg_fill]', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'vkExUnit_sanitize_boolean',
	) );

	$wp_customize->add_control( 'vkExUnit_sns_options_snsBtn_bg_fill', array(
		'label'		=> _x( '背景を塗りつぶさない' ,'lightning theme-customizer', 'vkExUnit' ),
		'section'	=> 'ex_unit_sns_setting',
		'settings'  => 'vkExUnit_sns_options[snsBtn_bg_fill]',
		'type'		=> 'checkbox',
		'priority'	=> 1,
	));
	// if( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ){
	// 	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key', array(
	// 		'label'    => _x('Key color', 'lightning theme-customizer', 'lightning'),
	// 		'section'  => 'lightning_design',
	// 		'settings' => 'lightning_theme_options[color_key]',
	// 		'priority' => 502,
	// 	)));

}
