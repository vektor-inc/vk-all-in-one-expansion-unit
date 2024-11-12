<?php
/**
 * VkExUnit customize.php
 *
 * @package  VkExUnit
 * @author   Kurudrive<kurudrive@gmail.com>
 * @since    28/Sep/2017
 */

/**
 * Add Customize Panel
 */

// カスタマイズパネルを出力
function veu_add_customize_panel() {
	// 基本的にはカスタマイズ画面で「ExUnit設定」パネルは表示されない
	// if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
		// 各機能からカスタマイザー機能を有効化する指定がされてたら、親パネルである「ExUnit設定」を出力する関数を実行する
		add_action( 'customize_register', 'veu_customize_register' );
	// }
}
add_action( 'after_setup_theme', 'veu_add_customize_panel' );

/**
 * 「ExUnit設定」パネルを出力する関数
 */
function veu_customize_register( $wp_customize ) {

	// パネルを表示する = カスタマイザーが利用されるので、独自のコントロールクラスを追加
	require_once VEU_DIRECTORY_PATH . '/admin/class-exunit-custom-html.php';
	require_once VEU_DIRECTORY_PATH . '/admin/class-exunit-custom-text-control.php';

	/*
	  Add ExUnit Panel
	 /*-------------------------------------------*/
	$wp_customize->add_panel(
		'veu_setting',
		array(
			'priority'       => 1000,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => veu_get_prefix_customize_panel() . ' ' . __( 'Settings', 'vk-all-in-one-expansion-unit' ),
		)
	);

}
