<?php
/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
// カスタマイザーで「ExUnit設定」のパネルが表示されるようにする
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_contact' );
function veu_customize_panel_activation_contact() {
	return true;
}

// カスタマイズ関数を実行
// if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_contact' );
// }

function veu_customize_register_contact( $wp_customize ) {

	/*-------------------------------------------*/
	/*    Contact Settings
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_contact_setting', array(
			'title'    => __( 'Contact Settings', 'vkExUnit' ),
			'priority' => 1000,
			'panel'    => 'veu_setting',
		)
	);

	// Message
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_txt]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_txt', array(
			'label'    => __( 'Message', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[contact_txt]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Phone number
	$wp_customize->add_setting(
		'vkExUnit_contact[tel_number]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'tel_number', array(
			'label'    => __( 'Phone number', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[tel_number]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Office hours
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_time]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_time', array(
			'label'    => __( 'Office hours', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[contact_time]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// The contact page URL
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_link]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_link', array(
			'label'    => __( 'The contact page URL', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[contact_link]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Contact button Text
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text', array(
			'label'    => __( 'Contact button Text', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[button_text]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Contact button text( sub )
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text_small]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text_small', array(
			'label'    => __( 'Contact button text( sub )', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[button_text_small]',
			'type'     => 'textarea',
			'priority' => 1,
		)
	);

	// Contact button short text for side widget
	$wp_customize->add_setting(
		'vkExUnit_contact[short_text]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'short_text', array(
			'label'    => __( 'Contact button short text for side widget', 'vkExUnit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[short_text]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	//画像をアップロードする関数
	function set_image_cutomizer( $wp_customize ) {
		// Inquiry Banner image
		$wp_customize->add_section(
			'vkExUnit_contact[contact_image]', array(
				'default'           => false,
				'type'              => 'option', // 保存先 option or theme_mod
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		//テーマ設定のグループ
		$wp_customize->add_setting( 'contact_image' );

		//テーマカスタマイザー画面に表示されるフォームの入力要素
		$wp_customize->add_control(
			'contact_image', array(
				'label'    => __( 'Inquiry Banner image', 'vkExUnit' ),
				'section'  => 'veu_contact_setting',
				'settings' => 'vkExUnit_contact[contact_image]',
				'type'     => 'text',
				'priority' => 1,
			)
		);
	}

	//カスタマイザーに登録
	add_action( 'customize_register', 'set_image_cutomizer' );

	//セットした画像のURLを取得
	function get_image_url() {
		return esc_url( get_theme_mod( contact_image ) );
	}

	// image up load
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_image]', array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'contact_image',
			array(
				'label'       => __( 'Inquiry Banner image', 'vkExUnit' ),
				'section'     => 'veu_contact_setting',
				'settings'    => 'vkExUnit_contact[contact_image]',
				'priority'    => 1,
				'description' => __( 'Display the image instead of the above inquiry information', 'vkExUnit' ),
			)
		)
	);

	// Display HTML message instead of the standard
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_html]', array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_textarea',
		)
	);

	$wp_customize->add_control(
		'contact_html', array(
			'label'       => __( 'Display HTML message instead of the standard', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_html]',
			'type'        => 'textarea',
			'priority'    => 1,
			'description' => __( 'HTML takes precedence over image', 'vkExUnit' ),
		)
	);

	/*-------------------------------------------*/
	/*	Add Edit Customize Link Btn
	/*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_contact[contact_txt]', array(
			'selector'        => '.veu_contact',
			'render_callback' => '',
		)
	);
}
