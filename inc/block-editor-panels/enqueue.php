<?php
/**
 * Enqueue block editor panel scripts.
 *
 * @package vk-all-in-one-expansion-unit
 */
function veu_enqueue_block_editor_panels() {
	$asset_path = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';
	if ( ! file_exists( $asset_path ) ) {
		return;
	}
	$asset_file = include $asset_path;
	wp_enqueue_script(
		'veu-block-editor-panels',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
	wp_localize_script(
		'veu-block-editor-panels',
		'veuPanelI18n',
		array(
			'panelTitle'     => veu_get_name() ?: 'VK ExUnit',
			'snsHide'        => __( 'Don\'t display sns share button on this page', 'vk-all-in-one-expansion-unit' ),
			'snsTitle'       => __( 'SNS Title', 'vk-all-in-one-expansion-unit' ),
			'snsTitleHelp'   => __( 'If you want to change the title of the SNS share, please enter the title here.', 'vk-all-in-one-expansion-unit' ),
			'noindex'        => __( 'noindex', 'vk-all-in-one-expansion-unit' ),
			'noindexLabel'   => __( 'No print noindex to this page', 'vk-all-in-one-expansion-unit' ),
			'sitemapHide'    => __( 'Don\'t display on the sitemap', 'vk-all-in-one-expansion-unit' ),
			'headTitle'      => __( 'Head Title', 'vk-all-in-one-expansion-unit' ),
			'headTitleHelp'  => __( 'If you want to change the title tag, please enter the title here.', 'vk-all-in-one-expansion-unit' ),
			'eyecatchHide'   => __( 'Don\'t display eyecatch on this page', 'vk-all-in-one-expansion-unit' ),
			'customCss'      => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
			'promotionAlert' => __( 'Display promotion alert', 'vk-all-in-one-expansion-unit' ),
			'pageExclude'    => __( 'Exclude from page list', 'vk-all-in-one-expansion-unit' ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'veu_enqueue_block_editor_panels' );
