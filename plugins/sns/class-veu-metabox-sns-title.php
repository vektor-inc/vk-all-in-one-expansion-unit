<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_SNS_Title extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_sns_title',
			'cf_name'  => 'vkExUnit_sns_title',
			'title'    => __( 'SNS Title', 'vk-all-in-one-expansion-unit' ),
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

		$form  = '';
		$form .= '<input type=text name="' . esc_attr( $this->args['cf_name'] ) . '" value="' . esc_attr( $cf_value ) . '" size=50 />';
		$form .= '<p>' . __( 'if filled this area then override title of OGP and Twitter Card', 'vk-all-in-one-expansion-unit' ) . '</p>';

		return $form;
	}

} // class VEU_Metabox_SNS_Title {

$veu_metabox_sns_title = new VEU_Metabox_SNS_Title();
