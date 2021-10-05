<?php
/*
  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_sns' );
function veu_customize_panel_activation_sns() {
	return true;
}

if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
	add_action( 'customize_register', 'veu_customize_register_sns', 20 );
}

function veu_customize_register_sns( $wp_customize ) {
	$default_options = veu_get_sns_options_default();
	/*
	  SNS Settings
	 /*-------------------------------------------*/
	// 1. テーマカスタマイザー上に新しいセクションを追加
	$wp_customize->add_section(
		'veu_sns_setting',
		array(
			'title'    => __( 'SNS Settings', 'vk-all-in-one-expansion-unit' ),
			'priority' => 1,
			'panel'    => 'veu_setting',
		)
	);

	/*
	  Change OG Title
	/*-------------------------------------------*/
	// Customize inner title
	$wp_customize->add_setting( 'Post_title_custom_for_SNS', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Post_title_custom_for_SNS', array(
				'label'            => __( 'Post title custom for SNS', 'vk-all-in-one-expansion-unit' ),
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
			)
		)
	);
	// Print the OG tags
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsTitle_use_only_postTitle]',
		array(
			'default'           => $default_options['snsTitle_use_only_postTitle'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'snsTitle_use_only_postTitle',
		array(
			'label'       => __( 'For SNS title be composed by post title only.', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[snsTitle_use_only_postTitle]',
			'type'        => 'checkbox',
			'description' => '',
		)
	);

	/*
	  Facebook Settings
	/*-------------------------------------------*/
	// Facebook_title
	$wp_customize->add_setting( 'Facebook_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Facebook_title', array(
				'label'            => __( 'Facebook Settings', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['fbAppId'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'fbAppId',
		array(
			'label'    => __( 'Facebook application ID', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbAppId]',
			'type'     => 'text',
		)
	);

	// Facebook Page URL
	$wp_customize->add_setting(
		'vkExUnit_sns_options[fbPageUrl]',
		array(
			'default'           => $default_options['fbPageUrl'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'fbPageUrl',
		array(
			'label'    => __( 'Facebook Page URL', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbPageUrl]',
			'type'     => 'text',
		)
	);

	// OG default image
	$wp_customize->add_setting(
		'vkExUnit_sns_options[ogImage]',
		array(
			'default'           => $default_options['ogImage'],
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
				'label'       => __( 'OG default image', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_sns_setting',
				'settings'    => 'vkExUnit_sns_options[ogImage]',
				'description' => __( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.<br>If a featured image is specified for the page, it takes precedence.<br>* Picture sizes are 1280x720 pixels or more and picture ratio 16:9 is recommended.', 'vk-all-in-one-expansion-unit' ),
			)
		)
	);

	/*
	  OG Setting
	/*-------------------------------------------*/
	// Print the OG_title
	$wp_customize->add_setting( 'Print the OG_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Print the OG_title', array(
				'label'            => __( 'OG Settings', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['enableOGTags'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableOGTags',
		array(
			'label'       => __( 'Print the OG tags', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableOGTags]',
			'type'        => 'checkbox',
			'description' => __( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vk-all-in-one-expansion-unit' ),
		)
	);

	/*
	  Twitter Settings
	/*-------------------------------------------*/
	// Twitter_application_ID_title
	$wp_customize->add_setting( 'Twitter_application_ID_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Twitter_application_ID_title', array(
				'label'            => __( 'Twitter Settings', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['twitterId'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ExUnit_Custom_Text_Control(
			$wp_customize, 'twitterId', array(
				'label'        => __( 'Twitter ID', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['enableTwitterCardTags'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableTwitterCardTags',
		array(
			'label'       => __( 'Twitter Card tags', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableTwitterCardTags]',
			'type'        => 'checkbox',
			'description' => __( 'Print the Twitter Card tags', 'vk-all-in-one-expansion-unit' ),
		)
	);

	/*
	  Share_button
	/*-------------------------------------------*/

	// share_button_title
	$wp_customize->add_setting( 'share_button_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_title', array(
				'label'            => __( 'Social bookmark buttons', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['enableSnsBtns'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'enableSnsBtns',
		array(
			'label'    => __( 'Print the social bookmark buttons', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[enableSnsBtns]',
			'type'     => 'checkbox',
		)
	);

	// Social button style setting ///////////////////////////
	$wp_customize->add_setting( 'share_button_style', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_style', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Social button style setting', 'vk-all-in-one-expansion-unit' ),
				'custom_html'      => '',
			)
		)
	);

	// Bin bg fill
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_bg_fill_not]',
		array(
			'default'           => $default_options['snsBtn_bg_fill_not'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'snsBtn_bg_fill_not',
		array(
			'label'    => __( 'No background', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
			'type'     => 'checkbox',
		)
	);

	// Btn color
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_color]',
		array(
			'default'           => $default_options['snsBtn_color'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize, 'snsBtn_color',
			array(
				'label'    => __( 'Btn color', 'vk-all-in-one-expansion-unit' ),
				'section'  => 'veu_sns_setting',
				'settings' => 'vkExUnit_sns_options[snsBtn_color]',
			)
		)
	);

	// Share button display Position ///////////////////////////
	$wp_customize->add_setting( 'share_button_position', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_position', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Share button display Position', 'vk-all-in-one-expansion-unit' ),
				'custom_html'      => '',
			)
		)
	);

	// snsBtn_position before
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_position][before]',
		array(
			'default'           => $default_options['snsBtn_position']['before'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'snsBtn_position_before',
		array(
			'label'    => __( 'Before content', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_position][before]',
			'type'     => 'checkbox',
		)
	);

	// snsBtn_position after
	$wp_customize->add_setting(
		'vkExUnit_sns_options[snsBtn_position][after]',
		array(
			'default'           => $default_options['snsBtn_position']['after'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'snsBtn_position_after',
		array(
			'label'    => __( 'After content', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_position][after]',
			'type'     => 'checkbox',
		)
	);

	// Exclude Post Types ///////////////////////////
	$wp_customize->add_setting( 'share_button_exclude_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_exclude_title', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Exclude Post Types', 'vk-all-in-one-expansion-unit' ),
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

	// Share button for display  ///////////////////////////
	$wp_customize->add_setting( 'Follow_me_box_use_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Follow_me_box_use_title', array(
				'label'            => '',
				'section'          => 'veu_sns_setting',
				'type'             => 'text',
				'custom_title_sub' => __( 'Share button for display', 'vk-all-in-one-expansion-unit' ),
				'custom_html'      => '',
			)
		)
	);

	 // SNS Btn (Facebook)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useFacebook]',
		array(
			'default'           => $default_options['useFacebook'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useFacebook',
		array(
			'label'    => __( 'Facebook ', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useFacebook]',
			'type'     => 'checkbox',
		)
	);

	 // SNS Btn (Twitter)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useTwitter]',
		array(
			'default'           => $default_options['useTwitter'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useTwitter',
		array(
			'label'    => __( 'Twitter', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useTwitter]',
			'type'     => 'checkbox',
		)
	);

	 // SNS Btn (Hatena)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useHatena]',
		array(
			'default'           => $default_options['useHatena'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'useHatena',
		array(
			'label'    => __( 'Hatena', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useHatena]',
			'type'     => 'checkbox',
		)
	);

	 // SNS Btn (Pocket)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[usePocket]',
		array(
			'default'           => $default_options['usePocket'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'usePocket',
		array(
			'label'    => __( 'Pocket', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[usePocket]',
			'type'     => 'checkbox',
		)
	);

	 // SNS Btn (LINE)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useLine]',
		array(
			'default'           => $default_options['useLine'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'useLine',
		array(
			'label'    => __( 'LINE (mobile only)', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useLine]',
			'type'     => 'checkbox',
		)
	);

	// SNS Btn (Copy)
	$wp_customize->add_setting(
		'vkExUnit_sns_options[useCopy]',
		array(
			'default'           => $default_options['useCopy'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'useCopy',
		array(
			'label'    => __( 'Copy', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[useCopy]',
			'type'     => 'checkbox',
		)
	);

	/*
	  Follow me box
	/*-------------------------------------------*/
	// Follow_me_box_title
	$wp_customize->add_setting( 'Follow_me_box_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'Follow_me_box_title', array(
				'label'            => __( 'Follow me box', 'vk-all-in-one-expansion-unit' ),
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
			'default'           => $default_options['enableFollowMe'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'enableFollowMe',
		array(
			'label'    => __( 'Print the Follow me box', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[enableFollowMe]',
			'type'     => 'checkbox',
		)
	);

	// Follow me box title
	$wp_customize->add_setting(
		'vkExUnit_sns_options[followMe_title]',
		array(
			'default'           => $default_options['followMe_title'],
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'followMe_title',
		array(
			'label'    => __( 'Follow me box title', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[followMe_title]',
			'type'     => 'text',
		)
	);

	/*
	  Add Edit Customize Link Btn
	/*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_sns_options[followMe_title]', array(
			'selector'        => '.followSet_title',
			'render_callback' => '',
			'supports' => [],
		)
	);

	/*
	  Add Edit Customize Link Btn
	/*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
			'selector'        => '.veu_socialSet',
			'render_callback' => '',
			'supports' => [],
		)
	);

}
