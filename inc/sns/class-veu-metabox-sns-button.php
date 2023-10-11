<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_SNS_Button extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_sns_button_hide',
			'cf_name'  => 'sns_share_botton_hide',
			'title'    => __( 'Hide setting of share button', 'vk-all-in-one-expansion-unit' ),
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
		// 今編集している投稿の投稿タイプを取得
		$post_type = get_post_type();

		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Don\'t display share bottons.', 'vk-all-in-one-expansion-unit' );

		$form .= '<ul>';
		$form .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

} // class VEU_Metabox_SNS_Button {

$veu_metabox_sns_button = new VEU_Metabox_SNS_Button();
