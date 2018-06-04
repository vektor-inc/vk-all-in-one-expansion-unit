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
	add_action( 'customize_register', 'veu_customize_register_contact', 20 );
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

	// Contact Description
	$wp_customize->add_setting( 'veu_contact_description', array( 'sanitize_callback' => 'sanitize_text_field' ) );

	$custom_html  = '<p>';
	$custom_html .= __( 'The contents entered here will be reflected in the bottom of each fixed page, the "Contact Section" widget, the "Contact Button" widget, etc.', 'vkExUnit' );
	$custom_html .= '<br>';
	$custom_html .= __( 'When I display it on the page, it is necessary to classify a check into "Display Contact Section" checkbox with the edit page of each page.', 'vkExUnit' );
	$custom_html .= '</p>';

	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'veu_contact_description', array(
				// 'label'       => __( '', 'vkExUnit' ),
				'section'     => 'veu_contact_setting',
				'type'        => 'text',
				'priority'    => 1,
				'custom_html' => $custom_html,
			)
		)
	);

	// Message
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_txt]', array(
			'default'           => __( 'Please feel free to inquire.', 'vkExUnit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_txt', array(
			'label'       => __( 'Message', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_txt]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . __( 'Please feel free to inquire.', 'vkExUnit' ),
		)
	);

	// Phone number
	$wp_customize->add_setting(
		'vkExUnit_contact[tel_number]', array(
			'default'           => '000-000-0000',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'tel_number', array(
			'label'       => __( 'Phone number', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[tel_number]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . '000-000-0000',
		)
	);

	// Office hours
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_time]', array(
			'default'           => __( 'Office hours 9:00 - 18:00 [ Weekdays except holidays ]', 'vkExUnit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_time', array(
			'label'       => __( 'Office hours', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_time]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . __( 'Office hours', 'vkExUnit' ) . ' 9:00 - 18:00 [ ' . __( 'Weekdays except holidays', 'vkExUnit' ) . ' ]',
		)
	);

	// The contact page URL
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_link]', array(
			'default'           => home_url( '/contact/' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'contact_link', array(
			'label'       => __( 'The contact page URL', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_link]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . 'https://www.********.com/contact/ ' . __( 'or', 'vkExUnit' ) . ' /contact/<br>' . __( '* If you fill in the blank, widget\'s contact button does not appear.', 'vkExUnit' ),
		)
	);

	// Contact button Text
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text]', array(
			'default'           => __( 'Contact us', 'vkExUnit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text', array(
			'label'       => __( 'Contact button Text', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[button_text]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . __( 'Contact Us from email.', 'vkExUnit' ),
		)
	);

	// Contact button text( sub )
	$wp_customize->add_setting(
		'vkExUnit_contact[button_text_small]', array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'button_text_small', array(
			'label'       => __( 'Contact button text( sub )', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[button_text_small]',
			'type'        => 'textarea',
			'priority'    => 1,
			'description' => __( 'ex) ', 'vkExUnit' ) . __( 'Email contact form', 'vkExUnit' ),
		)
	);

	// Contact button short text for side widget
	$wp_customize->add_setting(
		'vkExUnit_contact[short_text]', array(
			'default'           => __( 'Contact us', 'vkExUnit' ),
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'short_text', array(
			'label'       => __( 'Contact button short text for side widget', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[short_text]',
			'type'        => 'text',
			'priority'    => 1,
			'description' => __( 'This will used to "Contact Button" widget.', 'vkExUnit' ),
		)
	);

	// image up load
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_image]', array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$decription = __( 'Display the image instead of the above inquiry information', 'vkExUnit' );
	$skin       = get_option( 'lightning_design_skin' );
	if ( $skin == 'fort' || $skin == 'pale' ) {
		$decription .= '<br>* ' . __( 'It is not reflected in the header.', 'vkExUnit' );
	}
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'contact_image',
			array(
				'label'       => __( 'Inquiry Banner image', 'vkExUnit' ),
				'section'     => 'veu_contact_setting',
				'settings'    => 'vkExUnit_contact[contact_image]',
				'priority'    => 1,
				'description' => $decription,
			)
		)
	);

	// Display HTML message instead of the standard
	$wp_customize->add_setting(
		'vkExUnit_contact[contact_html]', array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$decription = __( 'HTML takes precedence over image', 'vkExUnit' );
	$skin       = get_option( 'lightning_design_skin' );
	if ( $skin == 'fort' || $skin == 'pale' ) {
		$decription .= '<br>* ' . __( 'It is not reflected in the header.', 'vkExUnit' );
	}
	$wp_customize->add_control(
		'contact_html', array(
			'label'       => __( 'Display HTML message instead of the standard', 'vkExUnit' ),
			'section'     => 'veu_contact_setting',
			'settings'    => 'vkExUnit_contact[contact_html]',
			'type'        => 'textarea',
			'priority'    => 1,
			'description' => $decription,
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
