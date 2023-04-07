<?php
/**
 * Register Child Page Index Block
 */

function veu_register_child_page_index_block() {

    $asset_file = include plugin_dir_path( __FILE__ ) . '/build/block.asset.php';

    wp_register_script(
        'veu-block-child-page-index',
        plugin_dir_url( __FILE__ )  . '/build/block.js',
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
                    'postId'    => array(
                        'type'    => 'number',
                        'default' => -1,
                    ),
                ),
                veu_common_attributes()
            ),
            'editor_script'   => 'veu-block-child-page-index',
            'editor_style'    => 'veu-block-editor',
            'render_callback' => 'veu_child_page_index_block_callback',
            'supports'        => array(),
        )
    );
	
}
add_action( 'init', 'veu_register_child_page_index_block', 15 );

/**
 * 翻訳を設定
 */
function veu_child_page_index_block_translation() {
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'veu-block-child-page-index', 'vk-all-in-one-expansion-unit' );
	}	
}
add_action( 'init', 'veu_child_page_index_block_translation', 15 );

function veu_child_page_index_block_callback( $attributes, $content ) {

    $attributes = wp_parse_args(
		$attributes,
		array(
			'postId'    => -1,
			'className' => '',
		)
	);

	$classes = 'veu_childPageIndex_block';

	if ( isset( $attributes['className'] ) ) {
		$classes .= ' ' . $attributes['className'];
	}

	if ( function_exists( 'veu_add_common_attributes_class' ) ) {
		$classes = veu_add_common_attributes_class( $classes, $attributes );
	}

	$postId = ( $attributes['postId'] > 0 ) ? $attributes['postId'] : get_the_ID();

	$r = vkExUnit_childPageIndex_shortcode( $postId, $classes );

	if ( empty( $r ) ) {
		if ( isset( $_GET['context'] ) ) {
			return '<div class="alert alert-warning text-center ' . esc_attr( $classes ) . '">' . __( 'No Child Pages.', 'vk-all-in-one-expansion-unit' ) . '</div>';
		}
		return '';
	}
	return $r;
}