<?php
/*
  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
	require_once( 'package/class-vk-font-awesome-versions.php' );

	global $font_awesome_directory_uri;
	$font_awesome_directory_uri = plugins_url( '', __FILE__ ) . '/package/';

	global $vk_font_awesome_version_prefix_customize_panel;
	$vk_font_awesome_version_prefix_customize_panel = veu_get_prefix();

	global $set_enqueue_handle_style;
	$set_enqueue_handle_style = 'vkExUnit_common_style';
}
