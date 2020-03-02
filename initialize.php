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
function veu_print_css() {
	global $vkExUnit_version;
	$options = veu_get_common_options();
	if ( isset( $options['active_bootstrap'] ) && $options['active_bootstrap'] ) {
		wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_style_in_bs.css', array(), $vkExUnit_version, 'all' );
	} else {
		wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_style.css', array(), $vkExUnit_version, 'all' );
	}
}

function veu_print_editor_css() {
	add_editor_style( plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_editor_style.css' );
}
add_action( 'after_setup_theme', 'veu_print_editor_css' );


/*
  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_print_js' );
function veu_print_js() {
	global $vkExUnit_version;
	wp_register_script( 'vkExUnit_master-js', plugins_url( '', __FILE__ ) . '/assets/js/all.min.js', array( 'jquery' ), $vkExUnit_version, true );
	wp_localize_script( 'vkExUnit_master-js', 'vkExOpt', apply_filters( 'vkExUnit_localize_options', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) ) );
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


add_action( 'after_setup_theme', 'veu_change_enqueue_point_run_filter', 5 );
function veu_change_enqueue_point_run_filter() {
	$default = array(
		'common' => false,
	);
	$option  = get_option( 'vkExUnit_pagespeeding', $default );
	$option  = wp_parse_args( $option, $default );
	if ( $option['common'] ) {

		// font awesome
		add_filter( 'vkfa_enqueue_point', 'veu_change_enqueue_point_to_footer' );

		// vk blocks css
		add_filter( 'vkblocks_enqueue_point', 'veu_change_enqueue_point_to_footer' );

		// common css
		add_filter( 'veu_enqueue_point_common_css', 'veu_change_enqueue_point_to_footer' );

		// css customize
		add_filter( 'veu_enqueue_point_css_customize_common', 'veu_change_enqueue_point_to_footer' );
		add_filter( 'veu_enqueue_point_css_customize_single', 'veu_change_enqueue_point_to_footer' );

	}

}

function veu_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}
