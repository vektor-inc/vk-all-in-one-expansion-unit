<?php
/**
 * Call Page Block
 * 
 * @package VK All in One Expansion Unit
 */

class Call_Page_Block {
	
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
		echo 'aaaa';


		$veu_page_id = ! empty( $attributes['TargetPost'] ) ? $attributes['TargetPost'] : -1;

		$page_html = '';
		if ( -1 !== $veu_page_id ) {
			$page_html = apply_filters( 'veu_page_content', get_post( $veu_page_id )->post_content );
		}
		return $page_html;
	}
}