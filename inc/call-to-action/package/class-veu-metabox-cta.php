<?php

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_CTA extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_noindex',
			'cf_name'  => 'vkexunit_cta_each_option',
			'title'    => __( 'Call to Action setting', 'vk-all-in-one-expansion-unit' ),
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

		global $vk_call_to_action_textdomain;

		$ctas = Vk_Call_To_Action::get_ctas( true, '  - ' );
		// ランダムを先頭に追加
		array_unshift(
			$ctas, array(
				'key'   => 'random',
				'label' => __( 'Random', 'vk-all-in-one-expansion-unit' ),
			)
		);
		array_unshift(
			$ctas, array(
				'key'   => 'disable',
				'label' => __( 'Disable display', 'vk-all-in-one-expansion-unit' ),
			)
		);
		array_unshift(
			$ctas, array(
				'key'   => 0,
				'label' => __( 'Follow common setting', 'vk-all-in-one-expansion-unit' ),
			)
		);

		$form .= '<select name="vkexunit_cta_each_option" id="vkexunit_cta_each_option">';
		foreach ( $ctas as $cta ) {
			$selected = ( $cta['key'] == $cf_value ) ? ' selected' : '';
			$form    .= '<option value="' . $cta['key'] . '"' . $selected . '>' . esc_html( $cta['label'] ) . '</option>';
		}
		$form .= '</select>';
		$form .= '<p>';
		$form .= '<a href="' . esc_url( Vk_Call_To_Action::setting_page_url() ) . '" class="button button-default" target="_blank">' . __( 'CTA common setting', 'vk-all-in-one-expansion-unit' ) . '</a>';
		$form .= '<a href="' . admin_url( 'edit.php?post_type=cta' ) . '" class="button button-default" target="_blank">' . __( 'Show CTA index page', 'vk-all-in-one-expansion-unit' ) . '</a>';
		$form .= '</p>';

		return $form;
	}

} // class VEU_Metabox_CTA {

$veu_metabox_noindex = new VEU_Metabox_CTA();
