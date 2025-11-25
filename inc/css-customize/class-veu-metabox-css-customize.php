<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_CSS_Customize extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_custom_css',
			'cf_name'  => '_veu_custom_css',
			'title'    => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
			'priority' => 100,
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

		$form = '';

		$form .= '<textarea name="' . esc_attr( $this->args['cf_name'] ) . '" id="' . esc_attr( $this->args['cf_name'] ) . '" rows="5" cols="30" style="width:100%;">' . esc_textarea( $cf_value ) . '</textarea>';

		return $form;
	}

	/**
	 * Override parent save to sanitize CSS payloads before persisting.
	 *
	 * @param int $post_id Current post ID.
	 * @return int
	 */
	public function save_custom_field( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$nonce_key   = 'noncename__' . $this->args['cf_name'];
		$nonce_value = isset( $_POST[ $nonce_key ] ) ? $_POST[ $nonce_key ] : null;

		if ( ! wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, $this->args['cf_name'] );

		if ( empty( $_POST[ $this->args['cf_name'] ] ) ) {
			return $post_id;
		}

		$raw_css       = $_POST[ $this->args['cf_name'] ];
		$sanitized_css = veu_sanitize_custom_css_input( $raw_css );
		if ( '' !== $sanitized_css ) {
			add_post_meta( $post_id, $this->args['cf_name'], $sanitized_css );
		}

		return $post_id;
	}
} // class VEU_Metabox_CSS_Customize {

$veu_metabox_css_customize = new VEU_Metabox_CSS_Customize();
