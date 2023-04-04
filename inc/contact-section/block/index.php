<?php
/**
 * CTA ブロックを追加
 */


 /**
  * CTA ブロックを追加
  */
function veu_register_contact_section_block() {

	$asset_file = include plugin_dir_path( __FILE__ ) . '/build/block.asset.php';

	wp_register_script(
		'veu-block-contact-section',
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
					'vertical' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
				veu_common_attributes()
			),
			'editor_script'   => 'veu-block-contact-section',
			'editor_style'    => 'veu-block-editor',
			'render_callback' => 'veu_contact_section_block_callback',
			'supports'        => array(),
		)
	);

}
add_action( 'init', 'veu_register_contact_section_block', 15 );

/**
 * 翻訳を設定
 */
function veu_contact_section_block_translation() {
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'veu-block-contact-section', 'vk-all-in-one-expansion-unit' );
	}	
}
add_action( 'init', 'veu_contact_section_block_translation', 15 );

function veu_contact_section_block_callback( $attributes, $content ) {

    $attributes = wp_parse_args(
		$attributes,
		array(
			'vertical'  => false,
			'className' => '',
		)
	);

	$classes = 'veu_contact_section_block';
	if ( empty( $attributes['vertical'] ) ) {
		$classes .= ' veu_contact-layout-horizontal';
	}
	if ( isset($attributes['className']) ) {
		$classes .= ' ' . $attributes['className'];
	}
	if ( function_exists( 'veu_add_common_attributes_class' ) ) {
		$classes = veu_add_common_attributes_class( $classes, $attributes );
	}

	$r = VkExUnit_Contact::render_contact_section_html( $classes, false );

	if ( empty($r) ) {
		if ( isset($_GET['context']) ) {
			return '<div class="disabled ' . esc_attr($classes) .'">' . __('No Contact Page Setting.', 'vk-all-in-one-expansion-unit') . '</div>';
		}
		return '';
	}
	return $r;

}
