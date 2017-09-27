<?php
/*-------------------------------------------*/
/*  Add Parent menu
/*-------------------------------------------*/
/*  Load master setting page
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
/*  Add vkExUnit css
/*-------------------------------------------*/
/*  Add vkExUnit js
/*-------------------------------------------*/

/*-------------------------------------------*/
/*  Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'vkExUnit_setting_menu_parent' );
function vkExUnit_setting_menu_parent() {
    global $menu;
    $parent_name = vkExUnit_get_little_short_name();
    $Capability_required = 'activate_plugins';

    $custom_page = add_menu_page(
        $parent_name,               // Name of page
        $parent_name,               // Label in menu
        $Capability_required,
        'vkExUnit_setting_page',    // ユニークなこのサブメニューページの識別子
        'vkExUnit_add_setting_page' // メニューページのコンテンツを出力する関数
    );
    if ( ! $custom_page ) { return; }
}

/*-------------------------------------------*/
/*  Load master setting page
/*-------------------------------------------*/
function vkExUnit_add_setting_page() {
    require dirname( __FILE__ ) . '/vkExUnit_admin.php';
}

require_once( 'admin_wrapper.php' );

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/

require vkExUnit_get_directory() . '/common_init.php';
require vkExUnit_get_directory() . '/package_manager.php';
require vkExUnit_get_directory() . '/packages.php';
$options = vkExUnit_get_common_options();
require vkExUnit_get_directory() . '/common_helpers.php';

require vkExUnit_get_directory() . '/plugins_admin/dashboard_info_widget.php';

require vkExUnit_get_directory() . '/plugins_admin/disable_guide.php';

require vkExUnit_get_directory() . '/plugins/footer_copyright_change.php';

require vkExUnit_get_directory() . '/plugins_admin/vk-admin-config.php';
require vkExUnit_get_directory() . '/plugins_admin/customize-panel.php';
require vkExUnit_get_directory() . '/plugins_admin/content-meta-box.php';

vkExUnit_package_include(); // package_manager.php

/*-------------------------------------------*/
/*  Add vkExUnit css
/*-------------------------------------------*/
// Add vkExUnit css
add_action( 'wp_enqueue_scripts','vkExUnit_print_css' );
function vkExUnit_print_css() {
    global $vkExUnit_version;
    $options = vkExUnit_get_common_options();
    if ( isset( $options['active_bootstrap'] ) && $options['active_bootstrap'] ) {
        wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ).'/css/vkExUnit_style_in_bs.css', array(), $vkExUnit_version, 'all' );
    } else {
        wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ).'/css/vkExUnit_style.css', array(), $vkExUnit_version, 'all' );
    }
    if ( isset( $options['active_fontawesome'] ) && $options['active_fontawesome'] ) {
        wp_enqueue_style( 'font-awesome', vkExUnit_get_directory_uri() . '/libraries/font-awesome/css/font-awesome.min.css', array(), '4.7.0', 'all' );
    }
}

function vkExUnit_print_editor_css() {
    add_editor_style( plugins_url( '', __FILE__ ).'/css/vkExUnit_editor_style.css' );
}
add_action('after_setup_theme', 'vkExUnit_print_editor_css');

/*-------------------------------------------*/
/*  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_head','vkExUnit_addJs' );
function vkExUnit_addJs() {
    global $vkExUnit_version;
    wp_register_script( 'vkExUnit_master-js' , plugins_url( '', __FILE__ ).'/js/all.min.js', array( 'jquery' ), $vkExUnit_version, true );
    wp_localize_script( 'vkExUnit_master-js', 'vkExOpt', apply_filters('vkExUnit_localize_options', array('ajax_url'=>admin_url('admin-ajax.php')) ) );
    wp_enqueue_script( 'vkExUnit_master-js' );
}

/*-------------------------------------------*/
/*  Print admin js
/*-------------------------------------------*/
add_action( 'admin_print_scripts-vk-exunit_page_vkExUnit_main_setting', 'vkExUnit_admin_add_js' );
function vkExUnit_admin_add_js( $hook_suffix ) {
    global $vkExUnit_version;
    wp_enqueue_media();
    wp_register_script( 'vkExUnit_admin_js', plugins_url( '', __FILE__ ).'/js/vkExUnit_admin.js', array( 'jquery' ), $vkExUnit_version );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'vkExUnit_admin_js' );
}

/*-------------------------------------------*/
/*  管理画面_admin_head JavaScriptのデバッグコンソールにhook_suffixの値を出力
/*-------------------------------------------*/

// add_action("admin_head", 'vkExUnit_suffix2console');
// function vkExUnit_suffix2console() {
//     global $hook_suffix;
//     if (is_user_logged_in()) {
//         $str = "<script type=\"text/javascript\">console.log('%s')</script>";
//         printf($str, $hook_suffix);
//     }
// }


if ( function_exists( 'register_activation_hook' ) ) {
    register_activation_hook( __FILE__ , 'vkExUnit_install_function' );
}
function vkExUnit_install_function() {
    $opt = get_option( 'vkExUnit_common_options' );
    if ( ! $opt ) {
        add_option( 'vkExUnit_common_options', vkExUnit_get_common_options_default() );
    }
}

/*
/*  Lightning Charm 1.2.0 での表示崩れ回避用
/*  Lightning Charm 1.4.0 以降になったら削除
*/
add_action( 'wp_head', 'ltg_charm_1_2_fix_function',2 );
function ltg_charm_1_2_fix_function(){
    $theme_opt = wp_get_theme( get_template() );
    $skin = get_option('lightning_design_skin');
    $theme = $theme_opt->Name;

    if ( $skin == 'charm' && $theme == 'Lightning' ){

        $charm_custom_css = "
.veu_postList .postList .postList_item:first-child { border-top:none; }
.mainSection .veu_postList.pt_0 .postList.postList_miniThumb { padding:0;margin-left:0;margin-right:0; }
.mainSection .veu_postList.pt_0 .postList.postList_miniThumb postList_body { display:table-cell; }
@media (max-width: 991px) {
.mainSection .veu_postList.pt_0 .postList.postList_miniThumb .postList_thumbnail { float:none; }
}
@media (min-width: 992px) {
.mainSection .veu_postList.pt_0 .postList_item .postList_thumbnail { width:50%;margin-left:0;margin-right:0;padding-right:30px; }
.mainSection .veu_postList.pt_0 .postList_item.even .postList_thumbnail { margin-right:0; }
}
";
        wp_add_inline_style( 'lightning-design-style', $charm_custom_css );

    } // if ( $skin == 'charm' && $theme == 'Lightning' ){
}
