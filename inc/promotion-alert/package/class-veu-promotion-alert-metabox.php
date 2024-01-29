<?php
/**
 * VEU Metabox Promotion Alert
 */

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}


class VEU_Promotion_Alert_Metabox extends VEU_Metabox {

    public function __construct( $args = array() ) {

        $this->args = array(
            'slug'     => 'veu_display_promotion_alert',
            'cf_name'  => 'veu_display_promotion_alert',
            'title'    => __( 'Promotion Alert Setting', 'vk-all-in-one-expansion-unit' ),
            'priority' => 1,
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

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'veu_promotion_alert', 'veu_promotion_alert_nonce' );

        $form .= '<div class="veu_promotion-alert-meta-fields">';
        $form .= '<h4>' . __( 'Promotion Alert Setting', 'vk-all-in-one-expansion-unit' ) . '</h4>';
        $form .= '<select name="veu_display_promotion_alert">';
        $form .= '<option value="common" ' . selected( $cf_value, 'common', false ) . '>' . __( 'Apply common settings', 'vk-all-in-one-expansion-unit' ) . '</option>';
        $form .= '<option value="display" ' .  selected( $cf_value, 'display', false ) . '>' . __( 'Display', 'vk-all-in-one-expansion-unit' ). '</option>';
        $form .= '<option value="hide" ' . selected( $cf_value, 'hide', false ) . '>' . __( 'Hide', 'vk-all-in-one-expansion-unit' ) . '</option>';
        $form .= '</select>';
        $form .= '</div>';

        return $form;
    }

} 