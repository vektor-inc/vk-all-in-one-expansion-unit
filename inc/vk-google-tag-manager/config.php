<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Goole_Tag_Manager' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-google-tag-manager.php';
	require_once dirname( __FILE__ ) . '/admin.php';
	global $vk_gtm_prefix;
	$vk_gtm_prefix = '';

	global $vk_gtm_priority;
	$vk_gtm_priority = 556;

	global $vk_gtm_panel;
	$vk_gtm_panel = 'veu_setting';
}
