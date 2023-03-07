<?php

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

/**
 * Add Customize Panel
 * カスタマイザーで「ExUnit設定」のパネルが表示されるようにする
 *
 * @return void
 */
function veu_customize_panel_activation_contact() {
	return true;
}
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_contact' );

// カスタマイズ関数を実行
// if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_contact', 20 );
// }

function veu_customize_register_contact( $wp_customize ) {

	/*
		Contact Settings
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_contact_setting',
		array(
			'title'    => __( 'Contact Settings', 'vk-all-in-one-expansion-unit' ),
			'priority' => 1000,
			'panel'    => 'veu_setting',
		)
	);

	// Contact Description
	$wp_customize->add_setting( 'veu_contact_description', array( 'sanitize_callback' => 'sanitize_text_field' ) );

	$custom_html  = '<p>';
	$custom_html .= __( 'The contents entered here will be reflected in the bottom of each fixed page, the "Contact Section" widget, the "Contact Button" widget, etc.', 'vk-all-in-one-expansion-unit' );
	$custom_html .= '<br>';
	$custom_html .= __( 'When I display it on the page, it is necessary to classify a check into "Display Contact Section" checkbox with the edit page of each page.', 'vk-all-in-one-expansion-unit' );
	$custom_html .= '</p>';

	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize,
			'veu_contact_description',
			array(
				// 'label'       => __( '', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_contact_setting',
				'type'        => 'text',
				'priority'    => 1,
				'custom_html' => $custom_html,
			)
		)
	);

	// Message
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_txt]',
		array(
			'default'           => __( 'Please feel free to inquire.', 'vk-all-in-one-expansion-unit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_txt',
		array(
			'label'       => __( 'Message', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_txt]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . __( 'Please feel free to inquire.', 'vk-all-in-one-expansion-unit' ),
		)
	);

	// Phone number
	$wp_customize->add_setting(
		'vkExUnit_contact[tel_number]',
		array(
			'default'           => '000-000-0000',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'tel_number',
		array(
			'label'       => __( 'Phone number', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[tel_number]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . '000-000-0000',
		)
	);

	// Phone icon
	$wp_customize->add_setting(
		'vkExUnit_contact[tel_icon]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$icon_description = '';
	if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'ex_and_link' ) ) {
		$array            = array(
			'v4.7' => 'fa-phone-square',
			'v5'   => 'fas fa-phone-square-alt',
			'v6'   => 'fa-solid fa-square-phone',
		);
		$icon_description = VkFontAwesomeVersions::ex_and_link( 'class', $array );
	}

	$wp_customize->add_control(
		'tel_icon',
		array(
			'label'       => __( 'Phone icon', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[tel_icon]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => $icon_description,
		)
	);

	// Office hours
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_time]',
		array(
			'default'           => __( 'Office hours 9:00 - 18:00 [ Weekdays except holidays ]', 'vk-all-in-one-expansion-unit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_time',
		array(
			'label'       => __( 'Office hours', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_time]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . __( 'Office hours', 'vk-all-in-one-expansion-unit' ) . ' 9:00 - 18:00 [ ' . __( 'Weekdays except holidays', 'vk-all-in-one-expansion-unit' ) . ' ]',
		)
	);

	// The contact page URL
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_link]',
		array(
			'default'           => home_url( '/contact/' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url',
		)
	);

	$wp_customize->add_control(
		'contact_link',
		array(
			'label'       => __( 'The contact page URL', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_link]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . 'https://www.********.com/contact/ ' . __( 'or', 'vk-all-in-one-expansion-unit' ) . ' /contact/<br>' . __( '* If you fill in the blank, widget\'s contact button does not appear.', 'vk-all-in-one-expansion-unit' ),
		)
	);

	// The contact Target
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_target_blank]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'contact_target',
		array(
			'label'    => __( 'Open in New Tab', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_contact_setting',
			'settings' => 'vkExUnit_contact[contact_target_blank]',
			'type'     => 'checkbox',
			'priority' => 1,
		)
	);

	// Contact button Text
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text]',
		array(
			'default'           => __( 'Contact us', 'vk-all-in-one-expansion-unit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text',
		array(
			'label'       => __( 'Contact button Text', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[button_text]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . __( 'Contact Us from email.', 'vk-all-in-one-expansion-unit' ),
		)
	);

	// Contact button text( sub )
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text_small]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text_small',
		array(
			'label'       => __( 'Contact button text( sub )', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[button_text_small]',
			'type'        => 'textarea',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vk-all-in-one-expansion-unit' ) . __( 'Email contact form', 'vk-all-in-one-expansion-unit' ),
		)
	);

	// Contact button short text for side widget
	$wp_customize->add_setting(
		'vkExUnit_contact[short_text]',
		array(
			'default'           => __( 'Contact us', 'vk-all-in-one-expansion-unit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'short_text',
		array(
			'label'       => __( 'Contact button short text for side widget', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[short_text]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'This will used to "Contact Button" widget.', 'vk-all-in-one-expansion-unit' ),
		)
	);

	// image up load
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_image]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$decription = __( 'Display the image instead of the above inquiry information', 'vk-all-in-one-expansion-unit' );
	$skin       = get_option( 'lightning_design_skin' );
	if ( $skin == 'fort' || $skin == 'pale' ) {
		$decription .= '<br>* ' . __( 'It is not reflected in the header.', 'vk-all-in-one-expansion-unit' );
	}
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'contact_image',
			array(
				'label'       => __( 'Inquiry Banner image', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_contact_setting',
				'settings'    => 'vkExUnit_contact[contact_image]',
				'priority'    => 1,
				'description' => $decription,
			)
		)
	);

	// Display HTML message instead of the standard
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_html]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$decription = __( 'HTML takes precedence over image', 'vk-all-in-one-expansion-unit' );
	$skin       = get_option( 'lightning_design_skin' );
	if ( $skin == 'fort' || $skin == 'pale' ) {
		$decription .= '<br>* ' . __( 'It is not reflected in the header.', 'vk-all-in-one-expansion-unit' );
	}
	$wp_customize->add_control(
		'contact_html',
		array(
			'label'       => __( 'Display HTML message instead of the standard', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_html]',
			'type'        => 'textarea',
			'priority'    => 1,
			'description' => $decription,
		)
	);

	/*
	  Add Edit Customize Link Btn
	/*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_contact[contact_txt]',
		array(
			'selector'        => '.veu_contact',
			'render_callback' => '',
			'supports'        => array(),
		)
	);
}
