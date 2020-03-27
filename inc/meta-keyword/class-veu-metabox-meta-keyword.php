<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_Meta_Keyword extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_meta_keyword',
			'cf_name'  => 'vkExUnit_metaKeyword',
			'title'    => __( 'Meta Keywords', 'vk-all-in-one-expansion-unit' ),
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
		$keyword = get_post_meta( get_the_ID(), 'vkExUnit_common_keywords', true );
		if ( ! empty( $keyword ) ) {
			$cf_value = $keyword;
		}

		$theme_option_seo_link = '<a href="' . get_admin_url() . '/admin.php?page=vkExUnit_main_setting#vkExUnit_common_keywords" target="_blank">' . vkExUnit_get_name() . ' ' . __( 'Main setting', 'vk-all-in-one-expansion-unit' ) . '</a>';

		$form  = '';
		$form .= '<input type=text name="' . esc_attr( $this->args['cf_name'] ) . '" value="' . esc_attr( $cf_value ) . '" size=50 />';
		$form .= '<p>' . __( 'To distinguish between individual keywords, please enter a , delimiter (optional).', 'vk-all-in-one-expansion-unit' ) . '<br />';
		$form .= sprintf( __( '* keywords common to the entire site can be set from %s.', 'vk-all-in-one-expansion-unit' ), $theme_option_seo_link );
		$form .= '</p>';

		return $form;
	}

	public function save_custom_field( $post_id ) {
		parent::save_custom_field( $post_id );
		delete_post_meta( $post_id, 'vkExUnit_common_keywords' );
	}
}

// If delete following 'new' that don't display metabox on post edit page.
$veu_metabox_meta_keyword = new VEU_Metabox_Meta_Keyword();