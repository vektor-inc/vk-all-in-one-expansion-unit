<?php

function veu_gtm_options_init() {
	vkExUnit_register_setting(
		__( 'Google Tag Manager Setting', 'vk-all-in-one-expansion-unit' ),    // tab label.
		'vk_google_tag_manager_related_options',         // name attr
		'veu_gtm_options_validate', // sanitaise function name
		'veu_add_gtm_options_page'  // setting_page function name
	);
}
add_action( 'veu_package_init', 'veu_gtm_options_init' );

function veu_gtm_options_validate( $input ) {
    $output['gtm_id'] = sanitize_text_field( $input['gtm_id'] );
    return $output;
}

/*
  Add setting page
/*-------------------------------------------*/

function veu_add_gtm_options_page() {
	require dirname( __FILE__ ) . '/admin-page.php';

}