<?php
/**
 * Enqueue block editor panel scripts with feature awareness.
 * 機能の有効/無効に連動してパネルスクリプトを読み込む。
 *
 * @package vk-all-in-one-expansion-unit
 */

/**
 * Get list of active panel features.
 * 有効なパネル機能のリストを返す。
 *
 * @return array Active feature names.
 */
function veu_get_active_panel_features() {
	$options      = veu_get_common_options();
	$all_features = array( 'sns', 'noindex', 'sitemap_page', 'wpTitle', 'auto_eyecatch', 'css_customize', 'promotion_alert', 'page_exclude_from_list_pages' );
	$active       = array();
	foreach ( $all_features as $feature ) {
		if (
			( isset( $options[ 'active_' . $feature ] ) && $options[ 'active_' . $feature ] )
			|| ! isset( $options[ 'active_' . $feature ] )
		) {
			$active[] = $feature;
		}
	}
	return $active;
}

/**
 * Register post meta for active features only.
 * 有効な機能のメタキーのみ登録する。
 */
function veu_register_active_feature_meta() {
	$active_features = veu_get_active_panel_features();
	$post_types      = get_post_types( array( 'public' => true ) );

	$feature_meta_map = array(
		'sns'                          => array(
			array(
				'key'  => 'sns_share_botton_hide',
				'type' => 'string',
			),
			array(
				'key'  => 'vkExUnit_sns_title',
				'type' => 'string',
			),
		),
		'noindex'                      => array(
			array(
				'key'  => '_vk_print_noindex',
				'type' => 'string',
			),
		),
		'sitemap_page'                 => array(
			array(
				'key'  => 'sitemap_hide',
				'type' => 'string',
			),
		),
		'wpTitle'                      => array(
			array(
				'key'  => 'veu_head_title',
				'type' => 'string',
			),
		),
		'auto_eyecatch'                => array(
			array(
				'key'  => 'vkExUnit_EyeCatch_disable',
				'type' => 'string',
			),
		),
		'css_customize'                => array(
			array(
				'key'  => '_veu_custom_css',
				'type' => 'string',
			),
		),
		'promotion_alert'              => array(
			array(
				'key'  => 'veu_display_promotion_alert',
				'type' => 'string',
			),
		),
		'page_exclude_from_list_pages' => array(
			array(
				'key'       => '_exclude_from_list_pages',
				'type'      => 'string',
				'post_type' => 'page',
			),
		),
	);

	foreach ( $feature_meta_map as $feature_name => $metas ) {
		if ( ! in_array( $feature_name, $active_features, true ) ) {
			continue;
		}

		foreach ( $metas as $meta ) {
			$target_post_types = isset( $meta['post_type'] ) ? array( $meta['post_type'] ) : $post_types;
			foreach ( $target_post_types as $post_type ) {
				$args = array(
					'type'          => $meta['type'],
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				);
				if ( 'string' === $meta['type'] ) {
					$args['sanitize_callback'] = ( '_veu_custom_css' === $meta['key'] ) ? 'veu_sanitize_custom_css_input' : 'sanitize_text_field';
				}
				register_post_meta( $post_type, $meta['key'], $args );
			}
		}
	}
}
add_action( 'init', 'veu_register_active_feature_meta' );

/**
 * Enqueue block editor panel scripts.
 * ブロックエディタ用パネルスクリプトを読み込む。
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

	// Get active features list.
	// 有効な機能リストを取得する。
	$active_features = veu_get_active_panel_features();

	wp_localize_script(
		'veu-block-editor-panels',
		'veuPanelData',
		array(
			'panelTitle'     => veu_get_name() ? veu_get_name() : 'VK ExUnit',
			'activeFeatures' => $active_features,
			'i18n'           => array(
				'snsHide'        => __( "Don't display sns share button on this page", 'vk-all-in-one-expansion-unit' ),
				'snsTitle'       => __( 'SNS Title', 'vk-all-in-one-expansion-unit' ),
				'noindex'        => __( 'No print noindex to this page', 'vk-all-in-one-expansion-unit' ),
				'sitemapHide'    => __( "Don't display on the sitemap", 'vk-all-in-one-expansion-unit' ),
				'headTitle'      => __( 'Head Title', 'vk-all-in-one-expansion-unit' ),
				'eyecatchHide'   => __( "Don't display eyecatch on this page", 'vk-all-in-one-expansion-unit' ),
				'customCss'      => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
				'promotionAlert' => __( 'Display promotion alert', 'vk-all-in-one-expansion-unit' ),
				'pageExclude'    => __( 'Exclude from page list', 'vk-all-in-one-expansion-unit' ),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'veu_enqueue_block_editor_panels' );
