<?php
/*
Plugin Name: VK All in One Expansion Unit
Plugin URI: https://github.com/kurudrive/VK-All-in-one-Expansion-Unit
Description: This plug-in is an integrated plug-in with a variety of features that make it powerful your web site. Example Facebook Page Plugin,Social Bookmarks,Print OG Tags,Print Twitter Card Tags,Print Google Analytics tag,New post widget,Insert Related Posts and more!
Version: 0.1.1.1
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



/*-------------------------------------------*/
/*	Load modules
/*-------------------------------------------*/

require vkExUnit_get_directory() . '/common_init.php';
$options = vkExUnit_get_common_options();
require vkExUnit_get_directory() . '/common_helpers.php';

require vkExUnit_get_directory() . '/plugins/sitemap_page/sitemap_page.php';

if ( isset($options['active_sns']) && $options['active_sns'] )
	require vkExUnit_get_directory() . '/plugins/sns/sns.php';

if ( isset($options['active_ga']) && $options['active_ga'] )
	require vkExUnit_get_directory() . '/plugins/google_analytics/google_analytics.php';

if ( isset($options['active_relatedPosts']) && $options['active_relatedPosts'] )
	require vkExUnit_get_directory() . '/plugins/related_posts/related_posts.php';

if ( isset($options['active_metaDescription']) && $options['active_metaDescription'] )
	require vkExUnit_get_directory() . '/plugins/meta_description/meta_description.php';

if ( isset($options['active_otherWidgets']) && $options['active_otherWidgets'] )
	require vkExUnit_get_directory() . '/plugins/other_widget/other_widget.php';

if ( isset($options['active_css_customize']) && $options['active_css_customize'] )
	require vkExUnit_get_directory() . '/plugins/css_customize/css_customize.php';

/*-------------------------------------------*/
/*	Add vkExUnit css
/*-------------------------------------------*/
// Add vkExUnit css
add_action('wp_enqueue_scripts','vkExUnit_print_css');
function vkExUnit_print_css(){
	$options = vkExUnit_get_common_options();
	if ( isset($options['active_bootstrap']) && $options['active_bootstrap'] ) {
		wp_enqueue_style('vkExUnit_common_style', plugins_url('', __FILE__).'/css/style_in_bs.css', array(), '20150525', 'all');
	} else {
		wp_enqueue_style('vkExUnit_common_style', plugins_url('', __FILE__).'/css/style.css', array(), '20150525', 'all');	
	}
}
/*-------------------------------------------*/
/*	Add vkExUnit js
/*-------------------------------------------*/
add_action('wp_head','vkExUnit_addJs');
function vkExUnit_addJs(){
	$options = vkExUnit_get_common_options();
	if ( isset($options['active_bootstrap']) && $options['active_bootstrap'] ) {
	wp_register_script( 'vkExUnit_master-js' , plugins_url('', __FILE__).'/js/all_in_bs.min.js', array('jquery'), '20150628' );
	} else {
		wp_register_script( 'vkExUnit_master-js' , plugins_url('', __FILE__).'/js/all.min.js', array('jquery'), '20150628' );
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

require_once( 'admin_warpper.php' );

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
