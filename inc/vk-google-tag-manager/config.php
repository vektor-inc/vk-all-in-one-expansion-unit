<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Goole_Tag_Manager' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-google-tag-manager.php';
	global $vk_gtm_prefix;
	$vk_gtm_prefix = veu_get_prefix_customize_panel();

	global $vk_gtm_priority;
	$vk_gtm_priority = 556;
}
