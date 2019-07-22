<?php
function vkblocks_active() {
	return true;
}

function vkblocks_blocks_assets() {

	wp_register_style( 'vk-blocks-build-css', VK_BLOCKS_URL . 'build/block-build.css', array(), VK_BLOCKS_VERSION );
	wp_register_style( 'vk-blocks-build-editor-css', VK_BLOCKS_URL . 'build/block-build-editor.css', array(), VK_BLOCKS_VERSION );
	wp_register_script(
		'vk-blocks-build-js', VK_BLOCKS_URL . 'build/block-build.js', array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-editor',
		), VK_BLOCKS_VERSION, true
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'vk-blocks-build-js', 'vk-blocks', plugin_dir_path( __FILE__ ) . 'build/languages' );
	}

	$theme = wp_get_theme();
	if ( $theme->exists() ) {
		// 親テーマのテンプレートを取得
		// 親テーマが lightning-pro か テーマ名が Lightning Pro の時
		if ( $theme->get( 'Template' ) == 'lightning-pro' || $theme->get( 'Name' ) == 'Lightning Pro' ) {
			wp_localize_script( 'vk-blocks-build-js', 'vk_blocks_check', array( 'is_pro' => true ) );
		} else {
			wp_localize_script( 'vk-blocks-build-js', 'vk_blocks_check', array( 'is_pro' => false ) );
		}
	}

	global $wp_version;
	if ( defined( 'GUTENBERG_VERSION' ) || version_compare( $wp_version, '5.0', '>=' ) ) {

		// $arr = array( 'alert', 'balloon', 'button', 'faq', 'flow', 'pr-blocks', 'pr-content', 'outer', 'spacer', 'heading', 'staff', 'table-of-contents', 'simple-table', 'tr', 'th', 'td' ,'highlighter');//REPLACE-FLAG : このコメントは削除しないで下さい。wp-create-gurten-template.shで削除する基準として左の[//REPLACE-FLAG]を使っています。
		$arr = array( 'alert', 'balloon', 'button', 'faq', 'flow', 'pr-blocks', 'pr-content', 'outer', 'spacer', 'heading', 'staff', 'table-of-contents' ,'highlighter');//REPLACE-FLAG : このコメントは削除しないで下さい。wp-create-gurten-template.shで削除する基準として左の[//REPLACE-FLAG]を使っています。
		foreach ( $arr as $value ) {

			if ( $value === 'table-of-contents' ) {

				register_block_type(
					'vk-blocks/' . $value, array(
						'style'           => 'vk-blocks-build-css',
						'editor_style'    => 'vk-blocks-build-editor-css',
						'editor_script'   => 'vk-blocks-build-js',
						'attributes'      => [
							'style'      => [
								'type'    => 'string',
								'default' => '',
							],
							'renderHtml' => [
								'type'    => 'string',
								'default' => '',
							],
						],
						'render_callback' => function ( $attributes ) {
							return $attributes['renderHtml'];
						},
					)
				);

				if ( ! is_admin() ) {
					wp_enqueue_script( 'vk-blocks-toc-helper-js', VK_BLOCKS_URL . 'build/viewHelper.js', array(), VK_BLOCKS_VERSION, true );
				}
			} else {

				register_block_type(
					'vk-blocks/' . $value, array(
						'style'         => 'vk-blocks-build-css',
						'editor_style'  => 'vk-blocks-build-editor-css',
						'editor_script' => 'vk-blocks-build-js',
					)
				);

			}
		}
	}
}
add_action( 'init', 'vkblocks_blocks_assets' );

// Add Block Category,
if ( ! function_exists( 'vkblocks_blocks_categories' ) ) {

	function vkblocks_blocks_categories( $categories, $post ) {
		global $vk_blocks_prefix;

		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'vk-blocks-cat',
					'title' => $vk_blocks_prefix . __( 'Blocks（Beta）', 'vk-blocks' ),
					'icon'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M19 13H5v-2h14v2z" /></svg>',
				),
				array(
					'slug'  => 'vk-blocks-cat-layout',
					'title' => $vk_blocks_prefix . __( 'Blocks Layout（Beta）', 'vk-blocks' ),
					'icon'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M19 13H5v-2h14v2z" /></svg>',
				),
			)
		);
	}

	add_filter( 'block_categories', 'vkblocks_blocks_categories', 10, 2 );
}
