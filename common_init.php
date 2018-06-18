<?php
function vkExUnit_common_options_init() {
	register_setting(
		'vkExUnit_common_options_fields',   //  Immediately following form tag of edit page.
		'vkExUnit_common_options',          // name attr
		'vkExUnit_common_options_validate'
	);
}
add_action( 'admin_init', 'vkExUnit_common_options_init' );

function vkExUnit_get_common_options() {
	$dafault = vkExUnit_get_common_options_default();
	$options = get_option( 'vkExUnit_common_options' );
	$options = wp_parse_args( $options, $dafault );
	return apply_filters( 'vkExUnit_common_options', $options );
}

function vkExUnit_get_common_options_default() {
	// hook vkExUnit_package_is_enable()
	// パッケージの情報を取得してデフォルトの配列を作成
	$defaults = array();
	$packages = vkExUnit_get_packages();
	foreach ( $packages as $key => $value ) {
		$name                                 = $value['name'];
		$default_options[ 'active_' . $name ] = $value['default'];
	}
	$default_options['delete_options_at_deactivate'] = false;
	$default_options['content_filter_state']         = 'content';
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_common_options_validate( $input ) {
	/*
	入力された値の無害化
	ここでは機能の有効化有無に関する項目が殆どで、手動で項目を記載すると機能の増減の際に項目の編集漏れが出るため、
	vkExUnit_get_common_options_default() の中で package に登録してある項目・デフォルト値を読み込み、それをループ処理する
	 */
	$defaults = vkExUnit_get_common_options_default();
	foreach ( $defaults as $key => $default_value ) {
		// 'content_filter_state'　以外は true か false しか返ってこない
		if ( $key != 'content_filter_state' ) {
				$output[ $key ] = ( isset( $input[ $key ] ) ) ? esc_html( $input[ $key ] ) : $default_value;
		} else {
				$output['content_filter_state'] = ( ! empty( $input['content_filter_state'] ) ) ? 'loop_end' : 'content';
		}
	}

	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}
