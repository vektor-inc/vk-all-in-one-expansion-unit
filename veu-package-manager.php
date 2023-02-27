<?php
/**
 * VkExUnit package_manager.php
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    6/Aug/2015
 */

require VEU_DIRECTORY_PATH . '/veu-packages.php';

veu_package_initilate();


function veu_package_initilate() {
	global $vkExUnit_packages;
	if ( ! is_array( $vkExUnit_packages ) ) {
		$vkExUnit_packages = array();
	}
}


add_action( 'init', 'veu_package_init' );
function veu_package_init() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	do_action( 'veu_package_init' );
}


function veu_package_is_enable( $package_name ) {
	// パッケージ情報を取得
	global $vkExUnit_packages;

	// パッケージ情報に パッケージ名 が存在しなかった場合はnullを返す
	if ( ! isset( $vkExUnit_packages[ $package_name ] ) ) {
		return null; }

	// 共通設定（有効化情報）を読み込む
	$options = veu_get_common_options();

	// 保存されている共通設定データにパッケージ名が存在しない場合
	if ( ! isset( $options[ 'active_' . $package_name ] ) ) {
		// 初期情報のデータを返す
		return $vkExUnit_packages[ $package_name ]['default'];
	}
	return $options[ 'active_' . $package_name ];
}

function veu_package_register( $args ) {
	$defaults = veu_package_default();
	$args     = wp_parse_args( $args, $defaults );

	global $vkExUnit_packages;
	$vkExUnit_packages[ $args['name'] ] = $args;
}


function veu_package_include() {
	global $vkExUnit_packages;
	if ( ! count( $vkExUnit_packages ) || ! is_array( $vkExUnit_packages ) ) {
		return $output; }
	$options      = veu_get_common_options();
	$include_base = VEU_DIRECTORY_PATH . '/inc/';

	$use_ex_blocks = false;

	foreach ( $vkExUnit_packages as $package ) {
		if (
			$package['include'] and
			(
				( isset( $options[ 'active_' . $package['name'] ] ) and $options[ 'active_' . $package['name'] ] ) or
				( ! isset( $options[ 'active_' . $package['name'] ] ) and $package['default'] )
			)
		) {
			require_once $include_base . $package['include'];

			if ( $package['use_ex_blocks'] ) {
				$use_ex_blocks = true;
			}
		}
	}

	if ( $use_ex_blocks ) {
		// ver5.8.0 block_categories_all
		if ( function_exists( 'get_default_block_categories' ) && function_exists( 'get_block_editor_settings' ) ) {
			add_filter( 'block_categories_all', 'veu_add_block_category', 10, 2 );
		} else {
			add_filter( 'block_categories', 'veu_add_block_category', 10, 2 );
		}
	}
}



function veu_add_block_category( $categories, $post ) {
	$categories = array_merge(
		$categories,
		array(
			array(
				'slug'  => 'veu-block',
				'title' => veu_get_prefix() . __( 'ExUnit Blocks', 'vk-all-in-one-expansion-unit' ),
				// 'icon'  => 'layout',
			),
		)
	);
	return $categories;
}

function veu_package_default() {
	return array(
		'name'          => null,
		'title'         => 'noting',
		'description'   => 'noting',
		'attr'          => array(),
		'default'       => null,
		'include'       => false,
		'use_ex_blocks' => false,
		'hidden'        => false,
	);
}


add_filter( 'vkExUnit_common_options_validate', 'veu_common_package_options_validate', 10, 2 );
function veu_common_package_options_validate( $output, $input ) {
	global $vkExUnit_packages;
	if ( ! count( $vkExUnit_packages ) || ! is_array( $vkExUnit_packages ) ) {
		return $output; }
	foreach ( $vkExUnit_packages as $package ) {
		if (
			isset( $output[ 'active_' . $package['name'] ] ) &&
			$output[ 'active_' . $package['name'] ] == ( isset( $input[ 'active_' . $package['name'] ] ) && $input[ 'active_' . $package['name'] ] ) ? true : false
		) {
			continue; }
		$output[ 'active_' . $package['name'] ] = ( isset( $input[ 'active_' . $package['name'] ] ) ) ? true : false;
	}
	return $output;
}

foreach ( $required_packages as $package ) {
	veu_package_register( $package );
}
