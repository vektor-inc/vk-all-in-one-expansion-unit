<?php

class VEU_Metabox_Eyecatch extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'       => 'veu_eyecatch',
			'cf_name'    => 'vkExUnit_EyeCatch_disable',
			'title'      => __( 'Automatic EyeCatch', 'vk-all-in-one-expansion-unit' ),
			'priority'   => 50,
			'post_types' => apply_filters( 'veu_auto_eye_catch_post_types', array( 'post', 'page' ) ),
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

		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Do not set eyecatch image automatic.', 'vk-all-in-one-expansion-unit' );

		$form  = '';
		$form .= '<ul>';
		$form .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

} // class VEU_Metabox_Eyecatch {

$veu_metabox_eyecatch = new VEU_Metabox_Eyecatch();
