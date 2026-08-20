<?php
/**
 * CTA ブロックを追加
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
					'vertical'  => array(
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
	if ( isset( $attributes['className'] ) ) {
		$classes .= ' ' . $attributes['className'];
	}
	if ( function_exists( 'veu_add_common_attributes_class' ) ) {
		$classes = veu_add_common_attributes_class( $classes, $attributes );
	}

	$r = VkExUnit_Contact::render_contact_section_html( $classes, false );

	if ( empty( $r ) ) {
		// エディタでのプレビュー時にだけ「お問い合わせページ未設定」の注意書きを表示するための読み取り専用の分岐。
		// $_GET['context'] の値そのものは使わず isset() の有無だけを見ており、保存や状態変更を一切行わないため、
		// ノンス検証は不要と判断した（追加すると通常のプレビュー時に検証が失敗し、注意書きが出せなくなる）。
		// Read-only branch that shows the "No Contact Page Setting" notice only while previewing in the
		// editor. It only checks whether $_GET['context'] isset() and never reads its value, and it never
		// saves or changes state, so nonce verification is intentionally omitted here ( adding one would
		// fail during normal preview requests and prevent the notice from being shown ).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the render context; only isset() is used and the value itself is never read, so no state is read from or written to.
		if ( isset( $_GET['context'] ) ) {
			return '<div class="disabled ' . esc_attr( $classes ) . '">' . __( 'No Contact Page Setting.', 'vk-all-in-one-expansion-unit' ) . '</div>';
		}
		return '';
	}
	return $r;
}
