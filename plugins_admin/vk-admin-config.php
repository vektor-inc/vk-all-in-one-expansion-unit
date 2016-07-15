<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Admin' ) )
{

	require_once( 'vk-admin/class.vk-admin.php' );

	$admin_pages = array( 'toplevel_page_vkExUnit_setting_page', 'vk-exunit_page_vkExUnit_main_setting' );
	Vk_Admin::admin_scripts( $admin_pages );

}

