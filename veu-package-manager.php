<?php
/**
 * VkExUnit package_manager.php
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    6/Aug/2015
 */

vkExUnit_package_initilate();


function vkExUnit_package_initilate() {
	global $vkExUnit_packages;
	if ( ! is_array( $vkExUnit_packages ) ) {
		$vkExUnit_packages = array();
	}
}


add_action( 'init', 'vkExUnit_package_init' );
function vkExUnit_package_init() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	do_action( 'vkExUnit_package_init' );
}


function vkExUnit_package_is_enable( $package_name ) {
	// パッケージ情報を取得
	global $vkExUnit_packages;

	// パッケージ情報に パッケージ名 が存在しなかった場合はnullを返す
	if ( ! isset( $vkExUnit_packages[ $package_name ] ) ) {
		return null; }

	// 共通設定（有効化情報）を読み込む
	$options = vkExUnit_get_common_options();

	// 保存されている共通設定データにパッケージ名が存在しない場合
	if ( ! isset( $options[ 'active_' . $package_name ] ) ) {
		// 初期情報のデータを返す
		return $vkExUnit_packages[ $package_name ]['default'];
	}
	return $options[ 'active_' . $package_name ];
}


function vkExUnit_package_register( $args ) {
	$defaults = vkExUnit_package_default();
	$args     = wp_parse_args( $args, $defaults );

	global $vkExUnit_packages;
	$vkExUnit_packages[ $args['name'] ] = $args;
}


function vkExUnit_package_include() {
	global $vkExUnit_packages;
	if ( ! count( $vkExUnit_packages ) || ! is_array( $vkExUnit_packages ) ) {
		return $output; }
	$options      = vkExUnit_get_common_options();
	$include_base = vkExUnit_get_directory() . '/plugins/';
	foreach ( $vkExUnit_packages as $package ) {
		if (
			$package['include'] and
			(
				( isset( $options[ 'active_' . $package['name'] ] ) and $options[ 'active_' . $package['name'] ] ) or
				( ! isset( $options[ 'active_' . $package['name'] ] ) and $package['default'] )
			)
		) {
			require_once $include_base . $package['include'];
		}
	}
}


function vkExUnit_package_default() {
	return array(
		'name'        => null,
		'title'       => 'noting',
		'description' => 'noting',
		'attr'        => array(),
		'default'     => null,
		'include'     => false,
		'hidden'      => false,
	);
}


add_filter( 'vkExUnit_common_options_validate', 'vkExUnit_common_package_options_validate', 10, 2 );
function vkExUnit_common_package_options_validate( $output, $input ) {
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
