<?php
/*
  Delete old function data
  Load modules
  Add vkExUnit css
  Add vkExUnit js
/*-------------------------------------------*/

/*
  Delete old function data
/*-------------------------------------------*/
require VEU_DIRECTORY_PATH . '/veu-package-manager.php';
$options = get_option( 'veu_deprecated' );
if ( empty( $options['9.72.0'] ) ) {
	require VEU_DIRECTORY_PATH . '/delete-old-option-meta.php';
	$options['9.72.0'] = 'done';
	update_option( 'veu_deprecated', $options );
}

/*
  Load modules
/*
-------------------------------------------*/
// template-tags-veuでpackageの関数を使うので package-managerを先に読み込んでいる
require_once VEU_DIRECTORY_PATH . '/inc/template-tags/template-tags-config.php';
require_once VEU_DIRECTORY_PATH . '/inc/common-block.php';
require_once VEU_DIRECTORY_PATH . '/admin/admin.php';
require VEU_DIRECTORY_PATH . '/inc/footer-copyright-change.php';

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
	$options = veu_get_common_options();

	wp_register_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/assets/css/vkExUnit_style.css', array(), VEU_VERSION, 'all' );
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
-------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'veu_print_js' );
function veu_print_js() {
	
	$options = apply_filters( 'vkExUnit_master_js_options', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	wp_register_script( 'vkExUnit_master-js', plugins_url( '', __FILE__ ) . '/assets/js/all.min.js', array(), VEU_VERSION, true );
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

	if ( isset( $option['js_footer'] ) ) {
		unset( $option['js_footer'] );
	}

	update_option( 'vkExUnit_pagespeeding', $option );

}
add_action( 'after_setup_theme', 'change_old_options', 4 );

function veu_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}

function veu_inline_styles() {
	$dynamic_css = ':root {
		--ver_page_top_button_url:url(' . VEU_DIRECTORY_URI . '/assets/images/to-top-btn-icon.svg' . ');
	}
	@font-face {
		font-weight: normal;
		font-style: normal;
		font-family: "vk_sns";
		src: url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.eot?-bq20cj' . '");
		src: url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.eot?#iefix-bq20cj' . '") format("embedded-opentype"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.woff?-bq20cj' . '") format("woff"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.ttf?-bq20cj' . '") format("truetype"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.svg?-bq20cj#vk_sns' . '") format("svg");
	}';

	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'vkExUnit_common_style', $dynamic_css );
}
add_action( 'wp_head', 'veu_inline_styles', 5 );
