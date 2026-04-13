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
	$all_features = array(
		'sns',
		'noindex',
		'sitemap_page',
		'wpTitle',
		'auto_eyecatch',
		'css_customize',
		'promotion_alert',
		'page_exclude_from_list_pages',
		'call_to_action',
		'childPageIndex',
		'pageList_ancestor',
		'contact_section',
	);
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
			array(
				'key'       => 'vkExUnit_sitemap',
				'type'      => 'string',
				'post_type' => 'page',
			),
		),
		// Note: 'veu_head_title' is stored as a serialized array (title + add_site_title).
		// It is registered as type=string to preserve existing data, and exposed as an object
		// via register_rest_field below so the block editor panel can read/write it.
		// veu_head_title は配列としてシリアライズ保存されている。既存データを壊さないため
		// type=string で登録し、下部の register_rest_field でオブジェクトとして露出させる。
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
		'call_to_action'               => array(
			array(
				'key'  => 'vkexunit_cta_each_option',
				'type' => 'string',
			),
		),
		'childPageIndex'               => array(
			array(
				'key'       => 'vkExUnit_childPageIndex',
				'type'      => 'string',
				'post_type' => 'page',
			),
		),
		'pageList_ancestor'            => array(
			array(
				'key'       => 'vkExUnit_pageList_ancestor',
				'type'      => 'string',
				'post_type' => 'page',
			),
		),
		'contact_section'              => array(
			array(
				'key'       => 'vkExUnit_contact_enable',
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
					'auth_callback' => function ( $allowed, $meta_key, $object_id ) {
						return current_user_can( 'edit_post', $object_id );
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
				'auth_callback'     => function ( $allowed, $meta_key, $object_id ) {
					return current_user_can( 'edit_post', $object_id );
				},
			)
		);
	}
}
add_action( 'init', 'veu_register_active_feature_meta' );

/**
 * Register REST field for veu_head_title (array meta).
 * 配列型メタ veu_head_title を REST API にオブジェクトとして露出する。
 *
 * The old metabox stores this as a serialized array with 'title' and 'add_site_title' keys.
 * We cannot use register_post_meta(type=object) because WP treats existing serialized
 * data as invalid. Instead we expose it as a separate REST field that the block editor
 * panel reads and writes, while the underlying meta key remains untouched.
 *
 * 旧メタボックスは title と add_site_title をシリアライズ配列として保存している。
 * register_post_meta(type=object) では既存データが無効と扱われるため、
 * 別の REST field として露出し、ブロックエディタパネルが読み書きする。
 * 実体のメタキーは変更しない。
 */
function veu_register_head_title_rest_field() {
	$active_features = veu_get_active_panel_features();
	if ( ! in_array( 'wpTitle', $active_features, true ) ) {
		return;
	}

	$post_types = get_post_types( array( 'public' => true ) );

	register_rest_field(
		$post_types,
		'veu_head_title_object',
		array(
			'get_callback'    => function ( $object ) {
				$value = get_post_meta( $object['id'], 'veu_head_title', true );
				if ( ! is_array( $value ) ) {
					$value = array();
				}
				return array(
					'title'          => isset( $value['title'] ) ? (string) $value['title'] : '',
					'add_site_title' => isset( $value['add_site_title'] ) ? (string) $value['add_site_title'] : '',
				);
			},
			'update_callback' => function ( $value, $object ) {
				if ( ! current_user_can( 'edit_post', $object->ID ) ) {
					return new WP_Error( 'rest_cannot_update', __( 'Sorry, you are not allowed to edit this post.', 'vk-all-in-one-expansion-unit' ), array( 'status' => rest_authorization_required_code() ) );
				}
				if ( ! is_array( $value ) ) {
					return false;
				}
				$sanitized = array(
					'title'          => isset( $value['title'] ) ? sanitize_text_field( $value['title'] ) : '',
					'add_site_title' => isset( $value['add_site_title'] ) ? sanitize_text_field( $value['add_site_title'] ) : '',
				);
				update_post_meta( $object->ID, 'veu_head_title', $sanitized );
				return true;
			},
			'schema'          => array(
				'type'       => 'object',
				'properties' => array(
					'title'          => array(
						'type' => 'string',
					),
					'add_site_title' => array(
						'type' => 'string',
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'veu_register_head_title_rest_field' );


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

	$asset_path = VEU_DIRECTORY_PATH . '/build/editor-panel/index.asset.php';
	if ( ! file_exists( $asset_path ) ) {
		return;
	}
	$asset_file = include $asset_path;
	wp_enqueue_script(
		'veu-block-editor-panels',
		VEU_DIRECTORY_URI . '/build/editor-panel/index.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);

	// Panel spacing styles for the VK ExUnit sidebar (PluginSidebar 'veu-settings').
	// 新パネル内のフィールド間に余白を確保し、help テキストの行間を WordPress 標準に揃える。
	wp_add_inline_style(
		'wp-components',
		'#veu-settings-panel\\:veu-settings .components-panel__body .components-base-control + .components-base-control,
		 #veu-settings-panel\\:veu-settings .components-panel__body .components-base-control + .components-checkbox-control,
		 #veu-settings-panel\\:veu-settings .components-panel__body .components-checkbox-control + .components-base-control,
		 #veu-settings-panel\\:veu-settings .components-panel__body .components-checkbox-control + .components-checkbox-control,
		 #veu-settings-panel\\:veu-settings .components-panel__body .components-base-control + p,
		 #veu-settings-panel\\:veu-settings .components-panel__body p + .components-base-control {
			margin-top: 16px;
		}
		#veu-settings-panel\\:veu-settings .components-base-control__help {
			line-height: 1.5;
		}'
	);

	// Get active features list.
	// 有効な機能リストを取得する。
	$active_features = veu_get_active_panel_features();

	// Build CTA options list (for regular posts, not just CTA post type).
	// 通常投稿用のCTA選択肢を構築する。
	$cta_options = array();
	if ( in_array( 'call_to_action', $active_features, true ) && class_exists( 'Vk_Call_To_Action' ) ) {
		$cta_options[] = array(
			'value' => '0',
			'label' => __( 'Follow common setting', 'vk-all-in-one-expansion-unit' ),
		);
		$cta_options[] = array(
			'value' => 'disable',
			'label' => __( 'Disable display', 'vk-all-in-one-expansion-unit' ),
		);
		$cta_options[] = array(
			'value' => 'random',
			'label' => __( 'Random', 'vk-all-in-one-expansion-unit' ),
		);
		$ctas          = Vk_Call_To_Action::get_ctas( true, '  - ' );
		foreach ( $ctas as $cta ) {
			$cta_options[] = array(
				'value' => (string) $cta['key'],
				'label' => $cta['label'],
			);
		}
	}

	// CTA setting page URLs for help links.
	// CTA設定ページのリンク。
	$cta_setting_url = ( in_array( 'call_to_action', $active_features, true ) && class_exists( 'Vk_Call_To_Action' ) ) ? Vk_Call_To_Action::setting_page_url() : '';
	$cta_index_url   = admin_url( 'edit.php?post_type=cta' );

	wp_localize_script(
		'veu-block-editor-panels',
		'veuPanelData',
		array(
			'panelTitle'     => veu_get_name() ? veu_get_name() : 'VK ExUnit',
			'activeFeatures' => $active_features,
			'ctaOptions'     => $cta_options,
			'ctaSettingUrl'  => $cta_setting_url,
			'ctaIndexUrl'    => $cta_index_url,
			'i18n'           => array(
				// Section titles (match legacy metabox labels) / セクションタイトル（旧メタボックスと統一）
				'promotionSection'   => __( 'Promotion Disclosure Setting', 'vk-all-in-one-expansion-unit' ),
				'insertItemsSection' => __( 'Setting of insert items', 'vk-all-in-one-expansion-unit' ),
				'snsTitleSection'    => __( 'OGP Title', 'vk-all-in-one-expansion-unit' ),
				'snsHideSection'     => __( 'Hide setting of share button', 'vk-all-in-one-expansion-unit' ),
				'noindexSection'     => __( 'Noindex setting', 'vk-all-in-one-expansion-unit' ),
				'sitemapHideSection' => __( 'Hide setting of HTML sitemap', 'vk-all-in-one-expansion-unit' ),
				'headTitleSection'   => __( 'Head Title (Title tag)', 'vk-all-in-one-expansion-unit' ),
				'eyecatchSection'    => __( 'Automatic EyeCatch', 'vk-all-in-one-expansion-unit' ),
				'ctaSection'         => __( 'Call to Action setting', 'vk-all-in-one-expansion-unit' ),
				'pageExcludeSection' => __( 'Exclusion settings from the page list', 'vk-all-in-one-expansion-unit' ),
				'cssSection'         => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),

				// Field labels / フィールドラベル
				'snsHide'            => __( "Don't display share bottons.", 'vk-all-in-one-expansion-unit' ),
				'snsTitle'           => __( 'SNS Title', 'vk-all-in-one-expansion-unit' ),
				'noindex'            => __( 'Print noindex tag that to be do not display on search result.', 'vk-all-in-one-expansion-unit' ),
				'sitemapHide'        => __( 'Hide this page to HTML Sitemap.', 'vk-all-in-one-expansion-unit' ),
				'headTitle'          => __( 'Head Title', 'vk-all-in-one-expansion-unit' ),
				'addSiteTitle'       => __( 'Add Separator and Site Title', 'vk-all-in-one-expansion-unit' ),
				'eyecatchHide'       => __( 'Do not set eyecatch image automatic.', 'vk-all-in-one-expansion-unit' ),
				'customCss'          => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
				'pageExclude'        => __( 'Exclude from displaying Page List (wp_list_pages)', 'vk-all-in-one-expansion-unit' ),

				// Promotion alert select options / 広告開示設定の3択ラベル
				'promotionCommon'    => __( 'Apply common settings', 'vk-all-in-one-expansion-unit' ),
				'promotionDisplay'   => __( 'Display', 'vk-all-in-one-expansion-unit' ),
				'promotionHide'      => __( 'Hide', 'vk-all-in-one-expansion-unit' ),

				// Insert items (page only) / 挿入アイテム（固定ページのみ）
				'sitemapInsert'      => __( 'Display a HTML sitemap', 'vk-all-in-one-expansion-unit' ),
				'childPageIndex'     => __( 'Display a child page index', 'vk-all-in-one-expansion-unit' ),
				'pageListAncestor'   => __( 'Display a page list from ancestor', 'vk-all-in-one-expansion-unit' ),
				'contactEnable'      => __( 'Display Contact Section', 'vk-all-in-one-expansion-unit' ),

				// Help texts / 説明文
				'snsTitleHelp'       => __( 'if filled this area then override title of OGP and Twitter Card', 'vk-all-in-one-expansion-unit' ),
				'headTitleHelp'      => __( 'If there is any input here, the input will be reflected in the title tag.', 'vk-all-in-one-expansion-unit' ),
				'headTitleHelp2'     => __( 'Please note that the notation on the page will not be rewritten.', 'vk-all-in-one-expansion-unit' ),

				// CTA links / CTAリンク
				'ctaCommonSetting'   => __( 'CTA common setting', 'vk-all-in-one-expansion-unit' ),
				'ctaIndexPage'       => __( 'Show CTA index page', 'vk-all-in-one-expansion-unit' ),
			),
			'ctaI18n'        => array(
				'panelTitle'    => __( 'CTA Contents', 'vk-all-in-one-expansion-unit' ),
				'useClassic'    => __( 'Use following data (Do not use content data)', 'vk-all-in-one-expansion-unit' ),
				'ctaImage'      => __( 'CTA image', 'vk-all-in-one-expansion-unit' ),
				'addImage'      => __( 'Add image', 'vk-all-in-one-expansion-unit' ),
				'changeImage'   => __( 'Change image', 'vk-all-in-one-expansion-unit' ),
				'removeImage'   => __( 'Remove image', 'vk-all-in-one-expansion-unit' ),
				'imgPosition'   => __( 'Image position', 'vk-all-in-one-expansion-unit' ),
				'posNormal'     => __( 'Normal', 'vk-all-in-one-expansion-unit' ),
				'posRight'      => __( 'Right', 'vk-all-in-one-expansion-unit' ),
				'buttonSection' => __( 'Button', 'vk-all-in-one-expansion-unit' ),
				'buttonText'    => __( 'Button text', 'vk-all-in-one-expansion-unit' ),
				'ctaUrl'        => __( 'URL', 'vk-all-in-one-expansion-unit' ),
				'urlBlank'      => __( 'Open link in new window', 'vk-all-in-one-expansion-unit' ),
				'textSection'   => __( 'Text', 'vk-all-in-one-expansion-unit' ),
				'ctaText'       => __( 'CTA text', 'vk-all-in-one-expansion-unit' ),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'veu_enqueue_block_editor_panels' );

/**
 * Remove the legacy metabox on block editor screens.
 * ブロックエディタ画面では旧メタボックスを非表示にする。
 *
 * The new sidebar panel replaces the metabox in the block editor.
 * Classic Editor users will still see the original metabox.
 * 新しいサイドバーパネルがブロックエディタでメタボックスの代わりになる。
 * クラシックエディタのユーザーには従来のメタボックスがそのまま表示される。
 *
 * @return void
 */
function veu_remove_legacy_metabox_on_block_editor() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor ) {
		return;
	}
	remove_meta_box( 'veu_parent_post_metabox', $screen->post_type, 'normal' );
}
add_action( 'add_meta_boxes', 'veu_remove_legacy_metabox_on_block_editor', 20 );
