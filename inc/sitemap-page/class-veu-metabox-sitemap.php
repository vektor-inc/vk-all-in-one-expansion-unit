<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_Sitemap extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'       => 'veu_sitemap',
			'cf_name'    => 'sitemap_hide',
			'title'      => __( 'Hide setting of HTML sitemap', 'vk-all-in-one-expansion-unit' ),
			'priority'   => 50,
			'post_types' => array( 'page' => 'page' ),
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

		$label = __( 'Hide this page to HTML Sitemap.', 'vk-all-in-one-expansion-unit' );

		$form  = '';
		$form .= '<ul>';
		$form .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

} // class VEU_Metabox_Sitemap {

$veu_metabox_sitemap = new VEU_Metabox_Sitemap();
