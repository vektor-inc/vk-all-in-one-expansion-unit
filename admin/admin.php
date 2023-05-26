<?php

/*
  Add Parent menu
  Load master setting page
  Print admin js
*/
use VektorInc\VK_Admin\VkAdmin;
VkAdmin::init();

// VK Admin 0.1.0 ではメディアアップローダーの js は指定しないと読み込まないため手動で指定
// VK Admin 側で問答無用で読み込むようにした場合は削除可
// https://github.com/vektor-inc/vk-all-in-one-expansion-unit/pull/972
$admin_pages = array(
	'toplevel_page_vkExUnit_setting_page',
	'exunit_page_vkExUnit_main_setting',
	'widgets-php',
	'index.php',
);
VkAdmin::admin_scripts( $admin_pages );

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

require_once dirname( __FILE__ ) . '/admin-main-setting-page.php';

/*
  Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'veu_setting_menu_parent', 10 );
function veu_setting_menu_parent() {
	global $menu;

	$page_title = veu_get_little_short_name();
	$menu_title = veu_get_little_short_name();
	$capability_required = 'activate_plugins';
	$menu_slug = 'vkExUnit_setting_page';
	$callback_function = 'veu_add_setting_page';
	$icon_url = 'none';	

	$custom_page = add_menu_page(
		$page_title,
		$menu_title,
		$capability_required,
		$menu_slug,
		$callback_function,
		$icon_url		
	);

	if ( ! $custom_page ) {
		return; 
	}
}

add_action( 'admin_menu', 'veu_active_setting_menu', 10 );
function veu_active_setting_menu() {
	// $capability_required = veu_get_capability_required();
	add_submenu_page(
		// parent_menu_slug
		'vkExUnit_setting_page', 
		 // sub_menu_page_title
		__( 'Active Setting', 'vk-all-in-one-expansion-unit' ),
		// sub_menu_label
		__( 'Active Setting', 'vk-all-in-one-expansion-unit' ), 
		// capability_required
		// edit_theme_optionsのユーザーにもアクセスさせないため
		'activate_plugins',
		// sub_menu_slug
		'vkExUnit_setting_page',
		// callback_function
		'veu_add_setting_page'
	);
}

// ブロックを有効化する際、プラグインが有効になっていたらこれを無効にする
add_filter('pre_update_option_vkExUnit_common_options', function( $new_option, $old_value, $option ){
	if (
		!empty($new_option['active_vk-blocks']) &&
		empty($old_value['active_vk-blocks'])
	) {
		foreach( get_option( 'active_plugins' ) as $plugin ) {
			if (
				strpos($plugin, 'vk-blocks-pro/') === 0
				|| strpos($plugin, 'vk-blocks/') === 0
			) {
				$new_option['active_vk-blocks'] = false;
				break;
			}
		}
	}
	return $new_option;
},10, 3);


/*
  Load master setting page
/*-------------------------------------------*/
function veu_add_setting_page() {
	require dirname( __FILE__ ) . '/admin-active-setting-page.php';
}

/*
  Print admin js
/*-------------------------------------------*/
// add_action( 'admin_print_scripts-exunit_page_vkExUnit_main_setting', 'veu_admin_add_js' );
add_action( 'admin_enqueue_scripts', 'veu_admin_add_js' );
function veu_admin_add_js( $hook_suffix ) {
	// wp_enqueue_media(); // WelCart でアイキャッチ画像の操作ができなくなる。が、そもそもこれ不要では？ 2022.11.30以降もコメントアウトされたままなら削除
	wp_register_script( 'vkExUnit_admin_js', VEU_DIRECTORY_URI . '/assets/js/vkExUnit_admin.js', array( 'jquery' ), VEU_VERSION );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'vkExUnit_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'veu_admin_css' );
function veu_admin_css() {
	wp_enqueue_style( 'veu_admin_css', VEU_DIRECTORY_URI . '/assets/css/vkExUnit_admin.css', array(), VEU_VERSION, 'all' );
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
