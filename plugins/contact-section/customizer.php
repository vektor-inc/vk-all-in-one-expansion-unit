<?php
/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
// カスタマイザーで「ExUnit設定」のパネルが表示されるようにする
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_contact' );
function veu_customize_panel_activation_contact(){
	return true;
}

// カスタマイズ関数を実行
// if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_contact' );
// }

function veu_customize_register_contact( $wp_customize ) {

 	/*-------------------------------------------*/
 	/*	SNS Settings
 	/*-------------------------------------------*/
 	$wp_customize->add_section( 'veu_contact_setting', array(
 		'title'    => __('Contact Settings', 'vkExUnit'),
 		'priority' => 1000,
 		'panel'    => 'veu_setting',
 	) );

   // Message
 	$wp_customize->add_setting( 'vkExUnit_contact[contact_txt]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

	$wp_customize->add_control( 'contact_txt', array(
 		'label'    => __( 'Message', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[contact_txt]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

   // Tel
 	$wp_customize->add_setting( 'vkExUnit_contact[tel_number]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'tel_number', array(
 		'label'    => __( 'Tel number', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[tel_number]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

   // Business hours
 	$wp_customize->add_setting( 'vkExUnit_contact[contact_time]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'contact_time', array(
 		'label'    => __( 'Business hours', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[contact_time]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

   // Contact URL
 	$wp_customize->add_setting( 'vkExUnit_contact[contact_link]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'contact_link', array(
 		'label'    => __( 'Contact URL', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[contact_link]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

   // Text to display on the inquiry button
 	$wp_customize->add_setting( 'vkExUnit_contact[button_text]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'button_text', array(
 		'label'    => __( 'Text to display on the inquiry button', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[button_text]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

   // Text to display on the inquiry button 2(optional)
 	$wp_customize->add_setting( 'vkExUnit_contact[button_text_small]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'button_text_small', array(
 		'label'    => __( 'Text to display on the inquiry button 2(optional)', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[button_text_small]',
 		'type'     => 'textarea',
 		'priority' => 1,
 	) );

   // Text to display in the inquiry button widget
 	$wp_customize->add_setting( 'vkExUnit_contact[short_text]', array(
 		'default'           => false,
    'type'              => 'option', // 保存先 option or theme_mod
 		'capability'        => 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_text_field',
 	) );

 	$wp_customize->add_control( 'short_text', array(
 		'label'    => __( 'Text to display in the inquiry button widget', 'vkExUnit' ),
 		'section'  => 'veu_contact_setting',
 		'settings' => 'vkExUnit_contact[short_text]',
 		'type'     => 'text',
 		'priority' => 1,
 	) );

	//画像をアップロードする関数
function set_image_cutomizer($wp_customize){
 // Inquiry Banner image
 $wp_customize->add_section( 'vkExUnit_contact[contact_image]', array(
	'default'           => false,
	'type'              => 'option', // 保存先 option or theme_mod
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_text_field',
 ));

 //テーマ設定のグループ
 $wp_customize->add_setting( 'contact_image' );

 //テーマカスタマイザー画面に表示されるフォームの入力要素
 $wp_customize->add_control( 'contact_image', array(
	'label'    => __( 'Inquiry Banner image', 'vkExUnit' ),
	'section'  => 'veu_contact_setting',
	'settings' => 'vkExUnit_contact[contact_image]',
	'type'     => 'text',
	'priority' => 1,
 ));
}

//カスタマイザーに登録
add_action('customize_register', 'set_image_cutomizer');

//セットした画像のURLを取得
function get_image_url(){
  return esc_url(get_theme_mod(contact_image));
}

// HTML to display as inquiry information
$wp_customize->add_setting( 'vkExUnit_contact[contact_html]', array(
 'default'           => false,
 'type'              => 'option', // 保存先 option or theme_mod
 'capability'        => 'edit_theme_options',
 'sanitize_callback' => 'esc_textarea',
) );

$wp_customize->add_control( 'contact_html', array(
 'label'    => __( 'HTML to display as inquiry information', 'vkExUnit' ),
 'section'  => 'veu_contact_setting',
 'settings' => 'vkExUnit_contact[contact_html]',
 'type'     => 'textarea',
 'priority' => 1,
) );


   // Btn color
  //  $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_color]', array(
 	// 	'default'			=> false,
  //    'type'				=> 'option', // 保存先 option or theme_mod
 	// 	'capability'		=> 'edit_theme_options',
 	// 	'sanitize_callback' => 'sanitize_hex_color',
 	// ) );
  //
  //  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'snsBtn_color', array(
  //    'label'    => __('Btn color', 'vkExUnit'),
  //    'section'  => 'veu_contact_setting',
  //    'settings' => 'vkExUnit_sns_options[snsBtn_color]',
  //    'priority' => 2,
  //  )));

   // $wp_customize->get_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]' )->transport        = 'postMessage';

   /*-------------------------------------------*/
 	/*	Add Edit Customize Link Btn
 	/*-------------------------------------------*/
   // $wp_customize->selective_refresh->add_partial( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
   //   'selector' => '.veu_socialSet',
   //   'render_callback' => '',
   // ) );
 }
