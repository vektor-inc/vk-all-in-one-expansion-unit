<?php

function vkblocks_is_lightning() {

	// テーマがLightning系の場合読み込まない
	$theme_textdomain = wp_get_theme()->get( 'TextDomain' );
	if ( $theme_textdomain == 'lightning' || $theme_textdomain == 'lightning-pro' ) {
		return true;
	}

	$theme_template = wp_get_theme()->get( 'Template' );
	if ( $theme_template == 'lightning' || $theme_template == 'lightning-pro' ) {
		return true;
	}

	return false;

}

function vkblocks_is_load_bootstrap() {

	if ( vkblocks_is_lightning() ) {
		return false;
	}

	if ( get_option( 'vkblocks_load_bootstrap', true ) ) {
		return true;
	} else {
		return false;
	}

}

	// Boostrapの読み込み
function vkblocks_load_bootstrap( $hook_suffix ) {

	if ( ! vkblocks_is_load_bootstrap() ) {
		return;
	}

	wp_register_style( 'vkblocks-bootstrap', VK_BLOCKS_URL . '/build/bootstrap_vk_using.css', false, '4.3.1' );

	// 管理画面
	if ( is_admin() ) {

		if ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) {
			wp_enqueue_style( 'vkblocks-bootstrap' );
		}
	} else {
		wp_enqueue_style( 'vkblocks-bootstrap' );
	}

}
add_action( 'admin_enqueue_scripts', 'vkblocks_load_bootstrap' );
add_action( 'wp_enqueue_scripts', 'vkblocks_load_bootstrap' );


/**
 * Boostrapの読み込み設定をするカスタマイザー
 *
 * @param $wp_customize
 */
function vkblocks_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'vkblocks_load_bootstrap_section', array(
			'title'    => __( 'VK Blocks Bootstrap Setting', 'vk-blocks' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'vkblocks_load_bootstrap', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'vkblocks_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize, 'vkblocks_load_bootstrap', array(
				'label'       => __( 'Loading Bootstrap4', 'vk-blocks' ),
				'description' => __( 'Check here to load Bootstrap4. If your theme or plugins loading Bootstrap4, uncheck here.', 'vk-blocks' ),
				'type'        => 'checkbox',
				'section'     => 'vkblocks_load_bootstrap_section',
				'settings'    => 'vkblocks_load_bootstrap',
			)
		)
	);
}

// Lightning 系じゃない時にカスタマイズパネルを表示
if ( ! vkblocks_is_lightning() ) {
	add_action( 'customize_register', 'vkblocks_customize_register' );
}


function vkblocks_add_setting_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( '/customize.php' ) ) . '">' . __( 'Setting', 'vk-video-unit' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_vk-blocks/vk-blocks.php', 'vkblocks_add_setting_link', 10, 1 );

/**
 * カスタマイザー用のチェックボックス
 *
 * @param $checked
 *
 * @return bool
 */
function vkblocks_sanitize_checkbox( $checked ) {
	if ( isset( $checked ) && $checked ) {
		return true;
	} else {
		return false;
	}
}
