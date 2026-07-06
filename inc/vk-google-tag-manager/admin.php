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
	$output = array();
	// メイン設定の一括保存時、当該オプション群が $_POST に無いと $input は null で渡るため、
	// offset-on-null 警告と sanitize_text_field(null) の非推奨警告を防ぐ。
	// $input arrives as null when this option group is absent from $_POST during the bulk save, so guard against offset-on-null and the deprecated sanitize_text_field(null) call.
	$output['gtm_id'] = ! empty( $input['gtm_id'] ) ? sanitize_text_field( $input['gtm_id'] ) : '';
	return $output;
}

/*
	Add setting page
/*-------------------------------------------*/

function veu_add_gtm_options_page() {
	require __DIR__ . '/admin-page.php';
}
