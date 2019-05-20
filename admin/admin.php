<?php
/*
  Add Parent menu
  Load master setting page
  Print admin js
*/

add_action( 'plugins_loaded', 'veu_admin_loadfile' );
function veu_admin_loadfile() {
	require dirname( __FILE__ ) . '/admin-common-init.php';
	require dirname( __FILE__ ) . '/admin-main-setting-page.php';
	require dirname( __FILE__ ) . '/disable_guide.php';
	require dirname( __FILE__ ) . '/vk-admin/vk-admin-config.php';
	require dirname( __FILE__ ) . '/customizer.php';
}

/*
plugins_loaded の位置だとmetaboxを統合しない設定にしても個別のmetaboxが表示されない
 */
require dirname( __FILE__ ) . '/admin-post-metabox.php';

/*
  Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'veu_setting_menu_parent' );
function veu_setting_menu_parent() {
	global $menu;
	$parent_name         = veu_get_little_short_name();
	$Capability_required = 'activate_plugins';

	$custom_page = add_menu_page(
		$parent_name,               // Name of page
		$parent_name,               // Label in menu
		$Capability_required,
		'vkExUnit_setting_page',    // ユニークなこのサブメニューページの識別子
		'vkExUnit_add_setting_page' // メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) {
		return; }
}

/*
  Load master setting page
/*-------------------------------------------*/
function vkExUnit_add_setting_page() {
	require dirname( __FILE__ ) . '/admin-active-setting-page.php';
}

/*
  Print admin js
/*-------------------------------------------*/
// add_action( 'admin_print_scripts-exunit_page_vkExUnit_main_setting', 'veu_admin_add_js' );
add_action( 'admin_enqueue_scripts', 'veu_admin_add_js' );
function veu_admin_add_js( $hook_suffix ) {
	global $vkExUnit_version;
	wp_enqueue_media();
	wp_register_script( 'vkExUnit_admin_js', vkExUnit_get_directory_uri() . '/js/vkExUnit_admin.js', array( 'jquery' ), $vkExUnit_version );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'vkExUnit_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'veu_admin_css' );
function veu_admin_css() {
	global $vkExUnit_version;
	wp_enqueue_style( 'veu_admin_css', vkExUnit_get_directory_uri() . '/css/vkExUnit_admin.css', array(), $vkExUnit_version, 'all' );
}

/*
  管理画面_admin_head JavaScriptのデバッグコンソールにhook_suffixの値を出力
/*-------------------------------------------*/
// add_action("admin_head", 'vkExUnit_suffix2console');
// function vkExUnit_suffix2console() {
// global $hook_suffix;
// if (is_user_logged_in()) {
// $str = "<script type=\"text/javascript\">console.log('%s')</script>";
// printf($str, $hook_suffix);
// }
// }

add_action( 'admin_bar_menu', 'vkExUnit_package_adminbar', 43 );
function vkExUnit_package_adminbar( $wp_admin_bar ) {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return; }

	global $vkExUnit_options;
	if ( ! isset( $vkExUnit_options ) || ! count( $vkExUnit_options ) ) {
		return;
	}

	foreach ( $vkExUnit_options as $opt ) {
		$wp_admin_bar->add_node(
			array(
				'parent' => 'veu_adminlink_main',
				'title'  => $opt['tab_label'],
				'id'     => 'vew_configbar_' . $opt['option_name'],
				'href'   => admin_url() . 'admin.php?page=vkExUnit_main_setting#' . $opt['option_name'],
			)
		);
	}
}
