<?php
/*
* Plugin Name: VK All in One Expansion Unit
* Plugin URI: http://ex-unit.vektor-inc.co.jp
* Description: This plug-in is an integrated plug-in with a variety of features that make it powerful your web site. Many features can be stopped individually. Example Facebook Page Plugin,Social Bookmarks,Print OG Tags,Print Twitter Card Tags,Print Google Analytics tag,New post widget,Insert Related Posts and more!
* Version: 5.1.1
* Author: Vektor,Inc.
* Text Domain: vkExUnit
* Domain Path: /languages
* Author URI: http://vektor-inc.co.jp
* License: GPL2
*/
/*
Copyright 2015-2016 Vektor,Inc. ( email : kurudrive@gmail.com )

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
/*  Load master setting page
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
/*  Add Parent menu
/*-------------------------------------------*/
/*  Add vkExUnit css
/*-------------------------------------------*/
/*  Add vkExUnit js
/*-------------------------------------------*/

// Get Plugin version
$data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
global $vkExUnit_version;
$vkExUnit_version = $data['version'];

//include('plugins/css_customize/css-customize.php');
load_plugin_textdomain( 'vkExUnit', false, basename( dirname( __FILE__ ) ) . '/languages' );


function vkExUnit_get_directory( $path = '' ) {
	return $dirctory = dirname( __FILE__ ) . $path;
}

function vkExUnit_get_directory_uri( $path = '' ) {
	return plugins_url( $path , __FILE__ );
}


if ( function_exists( 'register_deactivation_hook' ) ) {
    register_deactivation_hook( __FILE__, 'vkExUnit_uninstall_function' );
}

function vkExUnit_uninstall_function() {

    include vkExUnit_get_directory( '/uninstaller.php' );
}


/// PHP Version check
if (version_compare( phpversion(), '5.4.45') >= 0) {
	require_once vkExUnit_get_directory() . '/initialize.php';
	if (version_compare(phpversion(), '5.6') < 0 && is_admin())
		add_filter( 'admin_notices', 'vkExUnit_phpversion_warning_notice');
}else{
	add_filter( 'admin_notices', 'vkExUnit_phpversion_error');
}

function vkExUnit_phpversion_error($val){
	if (!current_user_can('activate_plugins')) return $val;
    ?>
<div class="notice notice-error error is-dismissible"><p>
<?php _e("Current PHP Version is too old", 'vkExUnit'); ?>
 (<?php echo phpversion() ?>).
  <?php _e("VkExUnit's support after PHP5.6", 'vkExUnit'); ?>
</p></div>
    <?php
    return $val;
}

function vkExUnit_phpversion_warning_notice($val){
	if (!current_user_can('activate_plugins')) return $val;
    global $hook_suffix;
    if (strpos($hook_suffix, 'vkExUnit') == false) return;
    ?>
<div class="notice notice-warning is-dismissible"><p>
<?php _e("Current PHP Version is old", 'vkExUnit'); ?>
 (<?php echo phpversion() ?>).
  <?php _e("VkExUnit's support after PHP5.6", 'vkExUnit'); ?>
</p></div>
    <?php
    return $val;
}

