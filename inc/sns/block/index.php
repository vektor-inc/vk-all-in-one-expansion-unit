<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Child Page Index Block
 */
function veu_register_share_button_block() {

	$asset_file = include plugin_dir_path( __FILE__ ) . '/build/block.asset.php';

	wp_register_style( 'veu-block-share-button-editor', VEU_DIRECTORY_URI . '/assets/css/vkExUnit_sns_editor_style.css', array(), VEU_VERSION, 'all' );

	wp_register_script(
		'veu-block-share-button',
		plugin_dir_url( __FILE__ ) . '/build/block.js',
		$asset_file['dependencies'],
		VEU_VERSION,
		true
	);

	register_block_type(
		__DIR__,
		array(
			'attributes'      => array_merge(
				array(
					'position'  => array(
						'type'    => 'string',
						'default' => 'after',
					),
					'className' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				veu_common_attributes()
			),
			'editor_script'   => 'veu-block-share-button',
			'editor_style'    => 'veu-block-share-button-editor',
			'render_callback' => 'veu_share_button_block_callback',
			'supports'        => array(),
		)
	);
}
add_action( 'init', 'veu_register_share_button_block', 15 );

/**
 * 翻訳を設定
 */
function veu_share_button_block_translation() {
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'veu-block-share-button', 'vk-all-in-one-expansion-unit' );
	}
}
add_action( 'init', 'veu_share_button_block_translation', 15 );

/**
 * Render callback for the share button block ( manually placed by the editor ).
 * シェアボタンブロック（編集者が手動配置するブロック）の描画コールバック。
 *
 * @param array  $attributes Block attributes ( position, className, common attributes, etc. ).
 * @param string $content    Block inner content ( unused; this block has no InnerBlocks ).
 * @return string Rendered share button HTML, an editor-only notice, or an empty string.
 */
function veu_share_button_block_callback( $attributes, $content ) {

	$attributes = wp_parse_args(
		$attributes,
		array(
			'position'  => 'after',
			'className' => '',
		)
	);

	// この呼び出しがシェアボタンブロック（手動配置）からのものであることを veu_get_sns_btns() に伝える.
	// これにより「シェアボタンブロックを常に表示する」設定の例外と、編集画面専用の通知が適用できる.
	// Tell veu_get_sns_btns() this call comes from the share button block ( manual placement ), so
	// the "always display" block-only exception and the editor-only notice can be applied.
	$attributes['context'] = 'block';

	$r = veu_get_sns_btns( $attributes );

	return $r;
}

function veu_sns_icon_enqueue_block_assets() {
	if ( ! is_admin() ) {
		return;
	}
	wp_enqueue_style( 'veu-block-share-icon', VEU_DIRECTORY_URI . '/inc/sns/icons/style.css', array(), VEU_VERSION, 'all' );
}
add_action( 'enqueue_block_assets', 'veu_sns_icon_enqueue_block_assets' );
