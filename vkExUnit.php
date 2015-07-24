<?php
/*
Plugin Name: VK All in One Expansion Unit
Plugin URI: https://github.com/kurudrive/VK-All-in-one-Expansion-Unit
Description: This plug-in is an integrated plug-in with a variety of features that make it powerful your web site. Many features can be stopped individually. Example Facebook Page Plugin,Social Bookmarks,Print OG Tags,Print Twitter Card Tags,Print Google Analytics tag,New post widget,Insert Related Posts and more!
Version: 0.1.6.5
Author: Vektor,Inc.
Author URI: http://vektor-inc.co.jp
License: GPL2
*/
/*
Copyright 2015 Hidekazu Ishikawa ( email : kurudrive@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*-------------------------------------------*/
/*	Load master setting page
/*-------------------------------------------*/
/*	Load modules
/*-------------------------------------------*/
/*	Add Parent menu
/*-------------------------------------------*/
/*	Add vkExUnit css
/*-------------------------------------------*/
/*	Add vkExUnit js
/*-------------------------------------------*/

//include('plugins/css_customize/css-customize.php');
load_plugin_textdomain('vkExUnit', false, basename( dirname( __FILE__ ) ) . '/languages' );


function vkExUnit_get_directory(){
	return $dirctory = dirname( __FILE__ );
}

/*-------------------------------------------*/
/*	Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'vkExUnit_setting_menu_parent' );
function vkExUnit_setting_menu_parent() {
	global $menu;
	$parent_name = 'VK Ex Unit';
	$Capability_required = 'activate_plugins';

	$custom_page = add_menu_page(
		$parent_name,				// Name of page
		$parent_name,				// Label in menu
		$Capability_required,
		'vkExUnit_setting_page',	// ユニークなこのサブメニューページの識別子
		'vkExUnit_add_setting_page'	// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) return;
}

/*-------------------------------------------*/
/*	Load master setting page
/*-------------------------------------------*/
function vkExUnit_add_setting_page(){
	require dirname( __FILE__ ) . '/vkExUnit_admin.php';
}

require_once( 'admin_wrapper.php' );

/*-------------------------------------------*/
/*	Load modules
/*-------------------------------------------*/

require vkExUnit_get_directory() . '/common_init.php';
$options = vkExUnit_get_common_options();
require vkExUnit_get_directory() . '/common_helpers.php';

//require vkExUnit_get_directory() . '/plugins/sitemap_page/sitemap_page.php';
require vkExUnit_get_directory() . '/plugins/dashboard_info_widget/dashboard-info-widget.php';


if ( isset($options['active_wpTitle']) && $options['active_wpTitle'] )
	add_filter('wp_title','vkExUnit_get_wp_head_title');

if ( isset($options['active_sns']) && $options['active_sns'] )
	require vkExUnit_get_directory() . '/plugins/sns/sns.php';

if ( isset($options['active_ga']) && $options['active_ga'] )
	require vkExUnit_get_directory() . '/plugins/google_analytics/google_analytics.php';

if ( isset($options['active_relatedPosts']) && $options['active_relatedPosts'] )
	require vkExUnit_get_directory() . '/plugins/related_posts/related_posts.php';

if ( isset($options['active_metaDescription']) && $options['active_metaDescription'] )
	require vkExUnit_get_directory() . '/plugins/meta_description/meta_description.php';

if ( isset($options['active_icon']) && $options['active_icon'] )
	require vkExUnit_get_directory() . '/plugins/icons.php';

if ( isset($options['active_metaKeyword']) && $options['active_metaKeyword'] )
	require vkExUnit_get_directory() . '/plugins/meta_keyword/meta_keyword.php';

if ( isset($options['active_otherWidgets']) && $options['active_otherWidgets'] )
	require vkExUnit_get_directory() . '/plugins/other_widget/other_widget.php';

if ( isset($options['active_css_customize']) && $options['active_css_customize'] )
	require vkExUnit_get_directory() . '/plugins/css_customize/css_customize.php';

if ( isset($options['active_auto_eyecatch']) && $options['active_auto_eyecatch'] )
	require vkExUnit_get_directory() . '/plugins/auto_eyecatch.php';

if ( isset($options['active_childPageIndex']) && $options['active_childPageIndex'] )
	require vkExUnit_get_directory() . '/plugins/child_page_index/child_page_index.php';
	
if ( isset($options['active_sitemap_page']) && $options['active_sitemap_page'] )
	require vkExUnit_get_directory() . '/plugins/sitemap_page/sitemap_page.php';

// page custom field	
if ( isset($options['active_childPageIndex']) && $options['active_childPageIndex'] || isset($options['active_sitemap_page']) && $options['active_sitemap_page'] )
	require vkExUnit_get_directory() . '/plugins/page_custom_field.php';

/*-------------------------------------------*/
/*	Add vkExUnit css
/*-------------------------------------------*/
// Add vkExUnit css
add_action('wp_enqueue_scripts','vkExUnit_print_css');
function vkExUnit_print_css(){
	$options = vkExUnit_get_common_options();
	if ( isset($options['active_bootstrap']) && $options['active_bootstrap'] ) {
		wp_enqueue_style('vkExUnit_common_style', plugins_url('', __FILE__).'/css/style_in_bs.css', array(), '20150708', 'all');
	} else {
		wp_enqueue_style('vkExUnit_common_style', plugins_url('', __FILE__).'/css/style.css', array(), '20150708', 'all');	
	}
}
/*-------------------------------------------*/
/*	Add vkExUnit js
/*-------------------------------------------*/
add_action('wp_head','vkExUnit_addJs');
function vkExUnit_addJs(){
	$options = vkExUnit_get_common_options();
	if ( isset($options['active_bootstrap']) && $options['active_bootstrap'] ) {
	wp_register_script( 'vkExUnit_master-js' , plugins_url('', __FILE__).'/js/all_in_bs.min.js', array('jquery'), '20150708' );
	} else {
		wp_register_script( 'vkExUnit_master-js' , plugins_url('', __FILE__).'/js/all.min.js', array('jquery'), '20150708' );
	}
	wp_enqueue_script( 'vkExUnit_master-js' );
}

/*-------------------------------------------*/
/*	Print admin js
/*-------------------------------------------*/
add_action('admin_print_scripts-vk-ex-unit_page_vkExUnit_main_setting', 'vkExUnit_admin_add_js');
function vkExUnit_admin_add_js( $hook_suffix ) {
	wp_enqueue_media();
	wp_register_script( 'vkExUnit_admin_js', plugins_url('', __FILE__).'/js/vkExUnit_admin.js', array('jquery'), '20150525' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'vkExUnit_admin_js' );
}

/*-------------------------------------------*/
/*	Add fontawesome
/*-------------------------------------------*/
add_action('wp_head','vkExUnit_addfontawesome', 5);
function vkExUnit_addfontawesome(){
	$options = vkExUnit_get_common_options();
	if ( isset($options['active_fontawesome']) && $options['active_fontawesome'] ) {
		echo '<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">'.PHP_EOL;
	}
}

add_action( 'admin_print_styles-toplevel_page_vkExUnit_setting_page', 'vkExUnit_admin_enq');
add_action( 'admin_print_styles-vk-ex-unit_page_vkExUnit_main_setting', 'vkExUnit_admin_enq');
function vkExUnit_admin_enq(){
	wp_enqueue_style('vkexunit-css-admin', plugins_url('/css/admin.css', __FILE__));
}

/*-------------------------------------------*/
/*	管理画面_admin_head JavaScriptのデバッグコンソールにhook_suffixの値を出力
/*-------------------------------------------*/

// add_action("admin_head", 'suffix2console');
// function suffix2console() {
//     global $hook_suffix;
//     if (is_user_logged_in()) {
//         $str = "<script type=\"text/javascript\">console.log('%s')</script>";
//         printf($str, $hook_suffix);
//     }
// }
