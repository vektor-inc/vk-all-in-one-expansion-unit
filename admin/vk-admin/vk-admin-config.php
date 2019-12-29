<?php

/*
  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Admin' ) ) {
	require_once( 'package/class-vk-admin.php' );
}

global $vk_admin_textdomain;
$vk_admin_textdomain = 'vk-all-in-one-expansion-unit';

/*
  $admin_pages の配列にいれる識別値は下記をコメントアウトすればとれる
/*-------------------------------------------*/

// add_action("admin_head", 'suffix2console');
// function suffix2console() {
// global $hook_suffix;
// if (is_user_logged_in()) {
// $str = "<script type=\"text/javascript\">console.log('%s')</script>";
// printf($str, $hook_suffix);
// }
// }
$admin_pages = array(
	'toplevel_page_vkExUnit_setting_page',
	'exunit_page_vkExUnit_main_setting',
	'widgets-php',
	'index.php',
);
Vk_Admin::admin_scripts( $admin_pages );
