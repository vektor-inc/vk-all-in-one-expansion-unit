<?php

class VEU_Metabox_Noindex extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_noindex',
			'cf_name'  => '_vk_print_noindex',
			'title'    => __( 'Noindex setting', 'vk-all-in-one-expansion-unit' ),
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

		$form = '';

		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Print noindex tag that to be do not display on search result.', 'vk-all-in-one-expansion-unit' );

		$form .= '<ul>';
		$form .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

} // class VEU_Metabox_Noindex {

$veu_metabox_noindex = new VEU_Metabox_Noindex();
