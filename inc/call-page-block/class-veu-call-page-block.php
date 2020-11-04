<?php
/**
 * Call Page Block
 * 
 * @package VK All in One Expansion Unit
 */

class VEU_Call_Page_Block {
	
	// Constructor
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_block' ), 15 );
		add_filter( 'veu_page_content', 'do_blocks', 9 );
		add_filter( 'veu_page_content', 'wptexturize' );
		add_filter( 'veu_page_content', 'convert_smilies', 20 );
		add_filter( 'veu_page_content', 'shortcode_unautop' );
		add_filter( 'veu_page_content', 'prepend_attachment' );
		add_filter( 'veu_page_content', 'wp_filter_content_tags' );
		add_filter( 'veu_page_content', 'do_shortcode', 11 );
		add_filter( 'veu_page_content', 'capital_P_dangit', 11 );
	}

	public static function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		global $common_attributes;
		register_block_type(
			'vk-blocks/veu-call-page',
			array(
				'editor_script'   => 'veu-block',
				'editor_style'    => 'veu-block-editor',
				'attributes'      => array_merge(
					array(
						'className'      => array(
							'type'    => 'string',
							'default' => '',
						),
						'TargetPost'         => array(
							'type'    => 'number',
							'default' => -1
						),
					),
					$common_attributes
				),
				'supports' => [],
				'render_callback' => array( __CLASS__, 'block_callback'),
			)
		);
	}

	/**
	 * Rendering Call Page Block
	 *
	 * @param array $attributes attributes.
	 * @param html  $content content.
	 */
	public static function block_callback( $attributes, $content = '' ) {

		$veu_page_id = ! empty( $attributes['TargetPost'] ) ? $attributes['TargetPost'] : -1;
		$classes = '';
		$page_html = '';

		if ( -1 !== $veu_page_id ) {
			$classes .= 'veu_call_page';
			if ( isset( $attributes['TargetPost'] ) ) {
				$classes .= ' veu_call_page_id-' . $veu_page_id;
			}
			if ( isset( $attributes['className'] ) ) {
				$classes .= ' ' . $attributes['className'];
			}
			if ( isset( $attributes['vkb_hidden'] ) && $attributes['vkb_hidden'] ) {
				$classes .= ' vk_hidden';
			}
			if ( isset( $attributes['vkb_hidden_xxl'] ) && $attributes['vkb_hidden_xxl'] ) {
				$classes .= ' vk_hidden-xxl';
			}
			if ( isset( $attributes['vkb_hidden_xl_v2'] ) && $attributes['vkb_hidden_xl_v2'] ) {
				$classes .= ' vk_hidden-xl';
			}
			if ( isset( $attributes['vkb_hidden_lg'] ) && $attributes['vkb_hidden_lg'] ) {
				$classes .= ' vk_hidden-lg';
			}
			if ( isset( $attributes['vkb_hidden_md'] ) && $attributes['vkb_hidden_md'] ) {
				$classes .= ' vk_hidden-md';
			}
			if ( isset( $attributes['vkb_hidden_sm'] ) && $attributes['vkb_hidden_sm'] ) {
				$classes .= ' vk_hidden-sm';
			}
			if ( isset( $attributes['vkb_hidden_xs'] ) && $attributes['vkb_hidden_xs'] ) {
				$classes .= ' vk_hidden-xs';
			}

			$page_html .= '<div class="' . $classes . '">';
			$page_html .= apply_filters( 'veu_page_content', get_post( $veu_page_id )->post_content );
			$page_html .= '</div>';
		}
		return $page_html;
	}
}
new VEU_Call_Page_Block();