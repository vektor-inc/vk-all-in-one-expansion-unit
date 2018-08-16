<?php

/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_sns' );
function veu_customize_panel_activation_sns() {
	return true;
}

if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
	add_action( 'customize_register', 'veu_customize_register_sns', 20 );
}

function veu_customize_register_sns( $wp_customize ) {

	/*-------------------------------------------*/
	/*	SNS Settings
	 /*-------------------------------------------*/
	//1. テーマカスタマイザー上に新しいセクションを追加
	$wp_customize->add_section(
		'veu_sns_setting',
		array(
			'title'    => __( 'SNS Settings', 'vkExUnit' ),
			'priority' => 1,
			'panel'    => 'veu_setting',
		)
	);

	//2. WPデータベースに新しいテーマ設定を追加
	// Facebook_title
	$wp_customize->add_setting( 'Facebook_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Facebook_title', array(
				'label'            => __( 'Facebook Settings', 'vkExUnit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);

	// Facebook application ID
	$wp_customize->add_setting(
		'vkExUnit_sns_options[fbAppId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'fbAppId',
		array(
			'label'    => __( 'Facebook application ID', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbAppId]',
			'type'     => 'text',
		)
	);

	// Facebook Page URL
	$wp_customize->add_setting(
		'vkExUnit_sns_options[fbPageUrl]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'fbPageUrl',
		array(
			'label'    => __( 'Facebook Page URL', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbPageUrl]',
			'type'     => 'text',
		)
	);

	// OG default image
	$wp_customize->add_setting(
		'vkExUnit_sns_options[ogImage]',
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
			'ogImage',
			array(
				'label'       => __( 'OG default image', 'vkExUnit' ),
				'section'     => 'veu_sns_setting',
				'settings'    => 'vkExUnit_sns_options[ogImage]',
				'description' => __( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.<br>If a featured image is specified for the page, it takes precedence.<br>* Picture sizes are 1280x720 pixels or more and picture ratio 16:9 is recommended.', 'vkExUnit' ),
			)
		)
	);

	// Print the OG_title
	$wp_customize->add_setting( 'Print the OG_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Print the OG_title', array(
				'label'            => __( 'OG Settings', 'vkExUnit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);

	// Print the OG tags
	$wp_customize->add_setting(
		'vkExUnit_sns_options[enableOGTags]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableOGTags',
		array(
			'label'       => __( 'Print the OG tags', 'vkExUnit' ),
			'section'     => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableOGTags]',
			'type'        => 'checkbox',
			'description' => __( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vkExUnit' ),
		)
	);

	// Twitter_application_ID_title
	$wp_customize->add_setting( 'Twitter_application_ID_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Twitter_application_ID_title', array(
				'label'            => __( 'Twitter Settings', 'vkExUnit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);

	// Twitter ID
	$wp_customize->add_setting(
		'vkExUnit_sns_options[twitterId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ExUnit_Custom_Text_Control(
			$wp_customize, 'twitterId', array(
				'label'        => __( 'Twitter ID', 'vkExUnit' ),
				'section'      => 'veu_sns_setting',
				'settings'     => 'vkExUnit_sns_options[twitterId]',
				'type'         => 'text',
				'description'  => '',
				'input_before' => '@',
			)
		)
	);

	// Twitter Card tags
	$wp_customize->add_setting(
		'vkExUnit_sns_options[enableTwitterCardTags]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableTwitterCardTags',
		array(
			'label'       => __( 'Twitter Card tags', 'vkExUnit' ),
			'section'     => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableTwitterCardTags]',
			'type'        => 'checkbox',
			'description' => __( 'Print the Twitter Card tags', 'vkExUnit' ),
		)
	);

	/*-------------------------------------------*/
	/*	Share_button
	 /*-------------------------------------------*/

	// share_button_title
	$wp_customize->add_setting( 'share_button_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_title', array(
				'label'            => __( 'Social bookmark buttons', 'vkExUnit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);

	// Social bookmark buttons
	$wp_customize->add_setting(
		'vkExUnit_sns_options[enableSnsBtns]',
		array(
			'default'           => true,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableSnsBtns',
		array(
			'label'    => __( 'Print the social bookmark buttons', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[enableSnsBtns]',
			'type'     => 'checkbox',
		)
	);

	// share_button_title
	$wp_customize->add_setting( 'share_button_exclude_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_exclude_title', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Exclude Post Types', 'vkExUnit' ),
				'custom_html'      => '',
			)
		)
	);

	$args       = array(
		'public' => true,
	);
	$post_types = get_post_types( $args, 'object' );
	foreach ( $post_types as $key => $value ) {
		if ( $key != 'attachment' ) {
			// Exclude Post Types(post,page)
			$wp_customize->add_setting(
				'vkExUnit_sns_options[snsBtn_exclude_post_types][' . $key . ']',
				array(
					'default'           => false,
					'type'              => 'option', // 保存先 option or theme_mod
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'veu_sanitize_boolean',
				)
			);

			$wp_customize->add_control(
				'snsBtn_exclude_post_types_' . $key,
				array(
					'label'    => esc_html( $value->label ),
					'section'  => 'veu_sns_setting',
					'settings' => 'vkExUnit_sns_options[snsBtn_exclude_post_types][' . $key . ']',
					'type'     => 'checkbox',
				)
			);
		}
	}

	// share_button_bg_title
	$wp_customize->add_setting( 'share_button_bg_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_bg_title', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Social button style setting', 'vkExUnit' ),
				'custom_html'      => '',
			)
		)
	);

	// Bin bg fill
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_bg_fill_not]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'snsBtn_bg_fill_not',
		array(
			'label'    => __( 'No background', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
			'type'     => 'checkbox',
		)
	);

	// Btn color
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_color]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize, 'snsBtn_color',
			array(
				'label'    => __( 'Btn color', 'vkExUnit' ),
				'section'  => 'veu_sns_setting',
				'settings' => 'vkExUnit_sns_options[snsBtn_color]',
			)
		)
	);

	 // Follow_me_box_use_title
	 $wp_customize->add_setting( 'Follow_me_box_use_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Follow_me_box_use_title', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Share button for display', 'vkExUnit' ),
				'custom_html'      => '',
			)
		)
	);

	 // Follow me box(Facebook)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useFacebook]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useFacebook',
		array(
			'label'    => __( 'Facebook ', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useFacebook]',
			'type'     => 'checkbox',
		)
	);

	 // Follow me box(Twitter)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useTwitter]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useTwitter',
		array(
			'label'    => __( 'Twitter', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useTwitter]',
			'type'     => 'checkbox',
		)
	);

	 // Follow me box(Hatena)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useHatena]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useHatena',
		array(
			'label'    => __( 'Hatena', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useHatena]',
			'type'     => 'checkbox',
		)
	);

	 // Follow me box(Pocket)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[usePocket]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'usePocket',
		array(
			'label'    => __( 'Pocket', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[usePocket]',
			'type'     => 'checkbox',
		)
	);

	 // Follow me box(LINE)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useLine]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useLine',
		array(
			'label'    => __( 'LINE (mobile only)', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useLine]',
			'type'     => 'checkbox',
		)
	);

	 // Follow_me_box_title
	 $wp_customize->add_setting( 'Follow_me_box_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Follow_me_box_title', array(
				'label'            => __( 'Follow me box', 'vkExUnit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);

	// Follow me box
	$wp_customize->add_setting(
		'vkExUnit_sns_options[enableFollowMe]',
		array(
			'default'           => false,
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	// Follow me box title
	$wp_customize->add_setting(
		'vkExUnit_sns_options[followMe_title]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'followMe_title',
		array(
			'label'    => __( 'Follow me box title', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[followMe_title]',
			'type'     => 'text',
		)
	);
	//	Add Edit Customize Link Btn
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_sns_options[followMe_title]', array(
			'selector'        => '.followSet_title',
			'render_callback' => '',
		)
	);

	/*-------------------------------------------*/
	/*	Add Edit Customize Link Btn
	 /*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
			'selector'        => '.veu_socialSet',
			'render_callback' => '',
		)
	);
}
