<?php

/*
  Add Parent menu
  Load master setting page
  Print admin js
*/

function veu_common_options_init() {
	register_setting(
		'vkExUnit_common_options_fields',   //  Immediately following form tag of edit page.
		'vkExUnit_common_options',          // name attr
		'veu_common_options_validate'
	);
}
add_action( 'admin_init', 'veu_common_options_init' );

require dirname( __FILE__ ) . '/disable-guide.php';
require dirname( __FILE__ ) . '/customizer.php';

// plugins_loaded の位置ではmetaboxを統合しない設定にしても個別のmetaboxが表示されない
// 統合親メタボックスの読み込み
require dirname( __FILE__ ) . '/admin-post-metabox.php';

// 親メニューが出力される前に フックを通さずに直接読み込むとページが表示されなくなる
add_action( 'plugin_loaded', 'veu_load_main_setting_page' );
function veu_load_main_setting_page() {
	require_once dirname( __FILE__ ) . '/admin-main-setting-page.php';
	require_once dirname( __FILE__ ) . '/vk-admin/vk-admin-config.php';
}

/*
  Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'veu_setting_menu_parent' );
function veu_setting_menu_parent() {
	global $menu;

	$parent_name = veu_get_little_short_name();

	$capability_required = 'activate_plugins';

	$custom_page = add_menu_page(
		$parent_name,               // Name of page
		$parent_name,               // Label in menu
		$capability_required,
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
	wp_register_script( 'vkExUnit_admin_js', veu_get_directory_uri() . '/assets/js/vkExUnit_admin.js', array( 'jquery' ), $vkExUnit_version );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'vkExUnit_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'veu_admin_css' );
function veu_admin_css() {
	global $vkExUnit_version;
	wp_enqueue_style( 'veu_admin_css', veu_get_directory_uri() . '/assets/css/vkExUnit_admin.css', array(), $vkExUnit_version, 'all' );
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
