<?php
/*
  Load modules
  Add vkExUnit css
  Add vkExUnit js
/*-------------------------------------------*/

/*
  Load modules
/*-------------------------------------------*/
require veu_get_directory() . '/veu-package-manager.php';
// template-tags-veuでpackageの関数を使うので package-managerを先に読み込んでいる
require_once veu_get_directory() . '/inc/template-tags/template-tags-config.php';
require_once veu_get_directory() . '/inc/vk-css-optimize/vk-css-optimize-config.php';

require_once veu_get_directory() . '/admin/admin.php';
require veu_get_directory() . '/inc/footer-copyright-change.php';

veu_package_include(); // package_manager.php

/*
  Add vkExUnit css
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'veu_load_css_action' );
function veu_load_css_action() {
	$hook_point = apply_filters( 'veu_enqueue_point_common_css', 'wp_enqueue_scripts' );
	// priority 5 : possible to overwrite from theme design skin
	add_action( $hook_point, 'veu_print_css', 5 );
}

add_action( 'wp_enqueue_scripts', 'vwu_register_css', 3 );
add_action( 'admin_enqueue_scripts', 'vwu_register_css', 3 );
function vwu_register_css() {
	global $vkExUnit_version;
	$options = veu_get_common_options();

	if ( isset( $options['active_bootstrap'] ) && $options['active_bootstrap'] ) {
		wp_register_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_style_in_bs.css', array(), $vkExUnit_version, 'all' );
	} else {
		wp_register_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_style.css', array(), $vkExUnit_version, 'all' );
	}
}

function veu_print_css() {
	wp_enqueue_style( 'vkExUnit_common_style' );
}

function veu_print_editor_css() {
	add_editor_style( plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_editor_style.css' );
}
add_action( 'after_setup_theme', 'veu_print_editor_css' );

// ブロックエディタ用のCSS読み込み（ ↑ だけだと効かない ）
function veu_print_block_editor_css() {
	wp_register_style( 
		'veu-block-editor', 
		plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_editor_style.css',
		array(), 
		filemtime( plugin_dir_path( __FILE__ ) )
	);
}
add_action( 'init', 'veu_print_block_editor_css' );

/*
  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_print_js' );
function veu_print_js() {
	global $vkExUnit_version;
	$options = apply_filters( 'vkExUnit_master_js_options', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	wp_register_script( 'vkExUnit_master-js', plugins_url( '', __FILE__ ) . '/assets/js/all.min.js', array( 'jquery' ), $vkExUnit_version, true );
	wp_localize_script( 'vkExUnit_master-js', 'vkExOpt', apply_filters( 'vkExUnit_localize_options', $options ) );
	wp_enqueue_script( 'vkExUnit_master-js' );
}

if ( function_exists( 'register_activation_hook' ) ) {
	register_activation_hook( dirname( __FILE__ ) . '/vkExUnit.php', 'veu_install_function' );
}
function veu_install_function() {
	$opt = get_option( 'vkExUnit_common_options' );
	if ( ! $opt ) {
		add_option( 'vkExUnit_common_options', veu_get_common_options_default() );
	}
}

/**
 * change old options
 */
function change_old_options() {
	$option = get_option( 'vkExUnit_pagespeeding' );

	if ( isset( $option['common'] ) ) {
		$option['css_exunit'] = true;
		unset( $option['common'] );
	}

	if ( isset( $option['css_exunit'] ) ) {
		$option['css_optimize'] = 'tree-shaking';
		unset( $option['css_exunit'] );
	}

}
add_action( 'after_setup_theme', 'change_old_options', 4 );

/**
 * Move JavaScripts To Footer
 * https://nelog.jp/header-js-to-footer
 */
function veu_move_scripts_to_footer() {
	$default = array(
		'css_exunit' => false,
		'js_footer'  => false,
	);
	$option  = get_option( 'vkExUnit_pagespeeding', $default );
	$option  = wp_parse_args( $option, $default );
	if ( $option['js_footer'] ) {
		// Remove Header Scripts.
		remove_action( 'wp_head', 'wp_print_scripts' );
		remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
		remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );

		// Remove Footer Scripts.
		add_action( 'wp_footer', 'wp_print_scripts', 5 );
		add_action( 'wp_footer', 'wp_print_head_scripts', 5 );
		add_action( 'wp_footer', 'wp_enqueue_scripts', 5 );
	}
}
add_action( 'wp_enqueue_scripts', 'veu_move_scripts_to_footer' );


function veu_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}
