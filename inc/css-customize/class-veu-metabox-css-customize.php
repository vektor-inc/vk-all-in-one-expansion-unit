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

		$form .= '<textarea name="' . esc_attr( $this->args['cf_name'] ) . '" id="' . esc_attr( $this->args['cf_name'] ) . '" rows="5" cols="30" style="width:100%;">' . wp_kses_post( $cf_value ) . '</textarea>';

		return $form;
	}

} // class VEU_Metabox_CSS_Customize {

$veu_metabox_css_customize = new VEU_Metabox_CSS_Customize();
