<?php
/**
 * CTA ブロックを追加
 */


 /**
  * CTA ブロックを追加
  */
function veu_register_page_list_ancestor_block() {

	$asset_file = include plugin_dir_path( __FILE__ ) . '/build/block.asset.php';

	wp_register_script(
		'veu-block-page-list-ancestor',
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
					'className' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				veu_common_attributes()
			),
			'editor_script'   => 'veu-block-page-list-ancestor',
			'editor_style'    => 'veu-block-editor',
			'render_callback' => 'veu_page_list_ancestor_block_callback',
			'supports'        => array(),
		)
	);

}
add_action( 'init', 'veu_register_page_list_ancestor_block', 15 );

/**
 * 翻訳を設定
 */
function veu_page_list_ancestor_block_translation() {
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'veu-block-page-list-ancestor', 'vk-all-in-one-expansion-unit' );
	}	
}
add_action( 'init', 'veu_page_list_ancestor_block_translation', 15 );

function veu_page_list_ancestor_block_callback( $attributes, $content ) {

	$attributes = wp_parse_args(
		$attributes,
		array(
			'className' => '',
		)
	);

	$classes = 'veu_childPageIndex_block';

	if ( ! empty( ( $attributes['className'] ) ) ) {
		$classes .= ' ' . $attributes['className'];
	}

	if( function_exists( 'veu_add_common_attributes_class' ) ){
		$classes = veu_add_common_attributes_class($classes, $attributes);
	}
	
	$r = vkExUnit_pageList_ancestor_shortcode( $classes, true );

	if ( empty($r) ) {
		if ( isset($_GET['context']) ) {
			return '<div class="alert alert-warning text-center">' . __('No Child Pages.', 'vk-all-in-one-expansion-unit') . '</div>';
		}
		return '';
	}
	return $r;

}
