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
	// CTA meta keys (cta post type only).
	$cta_metas = array(
		'vkExUnit_cta_use_type',
		'vkExUnit_cta_img',
		'vkExUnit_cta_img_position',
		'vkExUnit_cta_button_text',
		'vkExUnit_cta_button_icon',
		'vkExUnit_cta_button_icon_before',
		'vkExUnit_cta_button_icon_after',
		'vkExUnit_cta_url',
		'vkExUnit_cta_url_blank',
		'vkExUnit_cta_text',
	);
	foreach ( $cta_metas as $cta_key ) {
		$sanitize = 'sanitize_text_field';
		if ( in_array( $cta_key, array( 'vkExUnit_cta_url', 'vkExUnit_cta_img' ), true ) ) {
			$sanitize = 'sanitize_text_field';
		}
		register_post_meta(
			'cta',
			$cta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => $sanitize,
				'show_in_rest'      => true,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'veu_register_active_feature_meta' );

/**
 * Enqueue block editor panel scripts.
 * ブロックエディタ用パネルスクリプトを読み込む。
 */
function veu_enqueue_block_editor_panels() {
	// Only load on post editor, not Site Editor or Widgets Editor.
	// 投稿エディタのみで読み込む（サイトエディタ・ウィジェットエディタでは不要）。
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor || empty( $screen->post_type ) ) {
		return;
	}

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

	// Panel spacing styles.
	wp_add_inline_style(
		'wp-components',
		'.plugin-document-setting-panel-veu-settings .components-base-control,
		 .plugin-document-setting-panel-veu-cta-contents .components-base-control {
			margin-bottom: 12px;
		}'
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
				'snsHide'        => __( "Don't display share bottons.", 'vk-all-in-one-expansion-unit' ),
				'snsTitle'       => __( 'SNS Title', 'vk-all-in-one-expansion-unit' ),
				'noindex'        => __( 'Print noindex tag that to be do not display on search result.', 'vk-all-in-one-expansion-unit' ),
				'sitemapHide'    => __( 'Hide this page to HTML Sitemap.', 'vk-all-in-one-expansion-unit' ),
				'headTitle'      => __( 'Head Title', 'vk-all-in-one-expansion-unit' ),
				'eyecatchHide'   => __( 'Do not set eyecatch image automatic.', 'vk-all-in-one-expansion-unit' ),
				'customCss'      => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
				'promotionAlert' => __( 'Promotion Disclosure Setting', 'vk-all-in-one-expansion-unit' ),
				'pageExclude'    => __( 'Exclude from displaying Page List (wp_list_pages)', 'vk-all-in-one-expansion-unit' ),
			),
			'ctaI18n'        => array(
				'panelTitle'  => __( 'CTA Contents', 'vk-all-in-one-expansion-unit' ),
				'useClassic'  => __( 'Use following data (Do not use content data)', 'vk-all-in-one-expansion-unit' ),
				'ctaImage'    => __( 'CTA image', 'vk-all-in-one-expansion-unit' ),
				'addImage'    => __( 'Add image', 'vk-all-in-one-expansion-unit' ),
				'changeImage' => __( 'Change image', 'vk-all-in-one-expansion-unit' ),
				'removeImage' => __( 'Remove image', 'vk-all-in-one-expansion-unit' ),
				'imgPosition' => __( 'Image position', 'vk-all-in-one-expansion-unit' ),
				'buttonText'  => __( 'Button text', 'vk-all-in-one-expansion-unit' ),
				'ctaUrl'      => __( 'URL', 'vk-all-in-one-expansion-unit' ),
				'urlBlank'    => __( 'Open link in new window', 'vk-all-in-one-expansion-unit' ),
				'ctaText'     => __( 'CTA text', 'vk-all-in-one-expansion-unit' ),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'veu_enqueue_block_editor_panels' );
