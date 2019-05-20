<?php

/*
  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_post_type_manager' ) ) {

	global $vk_post_type_manager_textdomain;
	$vk_post_type_manager_textdomain = 'vk-all-in-one-expansion-unit';

	require( 'package/class.post-type-manager.php' );

	// /*  transrate
	// /*-------------------------------------------*/
	// function XXXX_post_type_manager_translate(){
	// __( 'Color', 'XXXX_plugin_text_domain_XXXX' );
	// }
}
