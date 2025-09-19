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
	 * @param [string] $cf_value : ここでは使用しないが親クラスで受け取っているため形だけ合わせる
	 * @return [string] form html
	 */
	public function metabox_body_form( $cf_value ) {
		global $post;
		$post_meta_value = get_post_meta( $post->ID, $this->args['cf_name'], true );

		// Render form using the helper class
		return VEU_Title_Form_Helper::render_post_form( $this->args['cf_name'], $post_meta_value );
	}
} // class VEU_Metabox_Head_Title {
