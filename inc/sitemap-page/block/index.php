<?php
/**
 * Register Child Page Index Block
 */

function veu_register_sitemap_block() {

    $asset_file = include plugin_dir_path( __FILE__ ) . '/build/block.asset.php';

    wp_register_script(
        'veu-block-sitemap',
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
                ),
                veu_common_attributes()
            ),
            'editor_script'   => 'veu-block-sitemap',
            'editor_style'    => 'veu-block-editor',
            'render_callback' => 'veu_sitemap_block_callback',
            'supports'        => array(),
        )
    );
	
}
add_action( 'init', 'veu_register_sitemap_block', 15 );

/**
 * 翻訳を設定
 */
function veu_sitemap_block_translation() {
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'veu-block-sitemap', 'vk-all-in-one-expansion-unit' );
	}	
}
add_action( 'init', 'veu_sitemap_block_translation', 15 );

function veu_sitemap_block_callback( $attributes, $content ) {

    $attributes = wp_parse_args(
		$attributes,
		array(
			'className' => '',
		)
	);

	$r = vkExUnit_sitemap( $attributes );

	return $r;
}