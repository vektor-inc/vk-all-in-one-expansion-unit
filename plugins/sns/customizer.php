<?php

/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_sns' );
function veu_customize_panel_activation_sns(){
	return true;
}

if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_sns' ,20);
}

function veu_customize_register_sns( $wp_customize ) {

 	/*-------------------------------------------*/
 	/*	SNS Settings
 	/*-------------------------------------------*/
  //1. テーマカスタマイザー上に新しいセクションを追加
 	$wp_customize->add_section( 'veu_sns_setting',
		array(
	 		'title'				=> __('SNS Settings', 'vkExUnit'),
	 		'priority'		=> 1,
	 		'panel'				=> 'veu_setting',
		)
	);

	//2. WPデータベースに新しいテーマ設定を追加
  // Bin bg fill
 	$wp_customize->add_setting('vkExUnit_sns_options[snsBtn_bg_fill_not]',
		array(
	 		'default'			      => false,
	    'type'				      => 'option', // 保存先 option or theme_mod
	 		'capability'		    => 'edit_theme_options',
	 		'sanitize_callback' => 'veu_sanitize_boolean',
	 	)
	);

 	$wp_customize->add_control( 'snsBtn_bg_fill_not',
		array(
	 		'label'		  => __( 'No background', 'vkExUnit' ),
	 		'section'	  => 'veu_sns_setting',
	 		'settings'  => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
	 		'type'		  => 'checkbox',
	 		'priority'	=> 1,
	 	)
	);

  // Btn color
  $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_color]',
		array(
	 		'default'			      => false,
	    'type'				      => 'option', // 保存先 option or theme_mod
	 		'capability'		    => 'edit_theme_options',
	 		'sanitize_callback' => 'sanitize_hex_color',
	 	)
	);

   $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'snsBtn_color',
	 	array(
			'label'    => __('Btn color', 'vkExUnit'),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_color]',
			'priority' => 1,
   	)
	 ));

   // $wp_customize->get_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]' )->transport        = 'postMessage';

	// Facebook application ID
 	$wp_customize->add_setting( 'vkExUnit_sns_options[fbAppId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
 	);

 	$wp_customize->add_control( 'fbAppId',
		array(
			'label'    => __( 'Facebook application ID', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbAppId]',
			'type'     => 'text',
			'priority' => 1,
		)
 	);

	// Facebook Page URL
 	$wp_customize->add_setting( 'vkExUnit_sns_options[fbPageUrl]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
 	);

 	$wp_customize->add_control( 'fbPageUrl',
		array(
			'label'    => __( 'Facebook Page URL', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbPageUrl]',
			'type'     => 'text',
			'priority' => 1,
		)
 	);

	// OG default image
	$wp_customize->add_setting( 'vkExUnit_sns_options[ogImage]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'ogImage',
			array(
				'label'       => __( 'OG default image', 'vkExUnit' ),
				'section'     => 'veu_sns_setting',
				'settings'    => 'vkExUnit_sns_options[ogImage]',
				'priority'    => 1,
				'description' => __( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.<br>If a featured image is specified for the page, it takes precedence.<br>ex) https://www.vektor-inc.co.jp/images/ogImage.png<br>* Picture sizes are 300x300 pixels or more and picture ratio 16:9 is recommended.', 'vkExUnit' ),
			)
		)
	);

	// Twitter ID
	$wp_customize->add_setting( 'vkExUnit_sns_options[twitterId]',
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
				'label'    => __( 'Twitter ID', 'vkExUnit' ),
				'section'  => 'veu_sns_setting',
				'settings' => 'vkExUnit_sns_options[twitterId]',
				'type'     => 'text',
				'priority' => 1,
				'description' => '',
				'input_before' => '@',
			)
		)
	);

	// Print the OG tags
	$wp_customize->add_setting('vkExUnit_sns_options[enableOGTags]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableOGTags',
		array(
			'label'		    => __( 'Print the OG tags', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableOGTags]',
			'type'		    => 'checkbox',
			'description' => __( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	// Twitter Card tags
	$wp_customize->add_setting('vkExUnit_sns_options[enableTwitterCardTags]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableTwitterCardTags',
		array(
			'label'		    => __( 'Twitter Card tags', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableTwitterCardTags]',
			'type'		    => 'checkbox',
			'description' => __( 'Print the Twitter Card tags', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	// Contact Description
	$wp_customize->add_setting( 'share_button_title', array( 'sanitize_callback' => 'sanitize_text_field' ) );

	$custom_html  = '<h4></h4>';

	$wp_customize->add_control(
		new ExUnit_Custom_Html(
			$wp_customize, 'share_button_title', array(
				// 'label'       => __( '', 'vkExUnit' ),
				'section'     => 'veu_contact_setting',
				'type'        => 'text',
				'priority'    => 1,
				'custom_html' => $custom_html,
			)
		)
	);

	// Social bookmark buttons
	$wp_customize->add_setting('vkExUnit_sns_options[enableSnsBtns]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableSnsBtns',
		array(
			'label'		    => __( 'Social bookmark buttons', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableSnsBtns]',
			'type'		    => 'checkbox',
			'description' => __( 'Print the social bookmark buttons', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	$args = array(
		'public'   => true,
	);
	$post_types = get_post_types($args,'object');
	foreach ($post_types as $key => $value) {
		if ( $key != 'attachment' ) {
			// Exclude Post Types(post,page)
			$wp_customize->add_setting('vkExUnit_sns_options[snsBtn_exclude_post_types]['.$key.']',
				array(
					'default'			      => false,
					'type'				      => 'option', // 保存先 option or theme_mod
					'capability'		    => 'edit_theme_options',
					'sanitize_callback' => 'veu_sanitize_boolean',
				)
			);

			$wp_customize->add_control( 'snsBtn_exclude_post_types_'.$key,
				array(
					'label'		    => esc_html( $value->label ),
					'section'	    => 'veu_sns_setting',
					'settings'    => 'vkExUnit_sns_options[snsBtn_exclude_post_types]['.$key.']',
					'type'		    => 'checkbox',
					'priority'	  => 1,
				)
			);
		}
	}

	// // Exclude Post Types(post,page)
	// $wp_customize->add_setting('vkExUnit_sns_options[snsBtn_exclude_post_types][page]',
	// 	array(
	// 		'default'			      => false,
	// 		'type'				      => 'option', // 保存先 option or theme_mod
	// 		'capability'		    => 'edit_theme_options',
	// 		'sanitize_callback' => 'veu_sanitize_boolean',
	// 	)
	// );
	//
	// $wp_customize->add_control( 'snsBtn_exclude_post_types_page',
	// 	array(
	// 		'label'		    => __( 'Page', 'vkExUnit' ),
	// 		'section'	    => 'veu_sns_setting',
	// 		'settings'    => 'vkExUnit_sns_options[snsBtn_exclude_post_types][page]',
	// 		'type'		    => 'checkbox',
	// 		'priority'	  => 1,
	// 	)
	// );


	// // Exclude Post ID(いらない)
	// $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_ignorePosts]',
	// 	array(
	// 		'default'           => '',
	// 		'type'              => 'option', // 保存先 option or theme_mod
	// 		'capability'        => 'edit_theme_options',
	// 		'sanitize_callback' => 'sanitize_text_field',
	// 	)
	// );
	//
	// $wp_customize->add_control( 'snsBtn_ignorePosts',
	// 	array(
	// 		'label'    => __( 'Exclude Post ID', 'vkExUnit' ),
	// 		'section'  => 'veu_sns_setting',
	// 		'settings' => 'vkExUnit_sns_options[snsBtn_ignorePosts]',
	// 		'type'     => 'text',
	// 		'description' => __( 'If you need filtering by post_ID, add the ignore post_ID separate by ",".<br>If empty this area, I will do not filtering.<br>example(12,31,553)', 'vkExUnit' ),
	// 		'priority' => 1,
	// 	)
	// );

	// Follow me box
	$wp_customize->add_setting('vkExUnit_sns_options[enableFollowMe]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableFollowMe',
		array(
			'label'		    => __( 'Print the Follow me box', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableFollowMe]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box title
	$wp_customize->add_setting( 'vkExUnit_sns_options[followMe_title]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control( 'followMe_title',
		array(
			'label'    => __( 'Follow me box title', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[followMe_title]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Follow me box(Facebook)
	$wp_customize->add_setting('vkExUnit_sns_options[useFacebook]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useFacebook',
		array(
			'label'		    => __( 'Share button for display( Facebook )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useFacebook]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Twitter)
	$wp_customize->add_setting('vkExUnit_sns_options[useTwitter]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useTwitter',
		array(
			'label'		    => __( 'Share button for display( Twitter )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useTwitter]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Hatena)
	$wp_customize->add_setting('vkExUnit_sns_options[useHatena]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useHatena',
		array(
			'label'		    => __( 'Share button for display( Hatena )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useHatena]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Pocket)
	$wp_customize->add_setting('vkExUnit_sns_options[usePocket]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'usePocket',
		array(
			'label'		    => __( 'Share button for display( Pocket )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[usePocket]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(LINE)
	$wp_customize->add_setting('vkExUnit_sns_options[useLine]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useLine',
		array(
			'label'		    => __( 'Share button for display( LINE )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useLine]',
			'type'		    => 'checkbox',
			'description' => __( '(mobile only)', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);







   /*-------------------------------------------*/
 	/*	Add Edit Customize Link Btn
 	/*-------------------------------------------*/
   $wp_customize->selective_refresh->add_partial( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
     'selector' => '.veu_socialSet',
     'render_callback' => '',
   ) );
 }
