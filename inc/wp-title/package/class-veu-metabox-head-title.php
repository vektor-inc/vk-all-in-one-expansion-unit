<?php
/**
 * SEO Title
 *
 * @package VK All in One Expansion Unit
 */
if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_Head_Title extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_head_title',
			'cf_name'  => 'veu_head_title',
			'title'    => __( 'Head Title', 'vk-all-in-one-expansion-unit' ),
			'priority' => 50,
		);

		parent::__construct( $this->args );
	}

	/**
	 * metabox_body_form
	 * Form inner
	 *
	 * @return [type] [description]
	 */
	public function metabox_body_form( $cf_value ) {
		global $post;
		$post_meta = get_post_meta( $post->ID, $this->args['cf_name'], true );

		// 共通ヘルパーを使用
		return VEU_Title_Form_Helper::render_post_form( $this->args['cf_name'], $post_meta );
	}
} // class VEU_Metabox_Head_Title {
