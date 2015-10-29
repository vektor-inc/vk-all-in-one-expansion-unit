<?php
function vkExUnit_common_options_init() {
	register_setting(
		'vkExUnit_common_options_fields', 	//  Immediately following form tag of edit page.
		'vkExUnit_common_options',			// name attr
		'vkExUnit_common_options_validate'
	);
}
add_action( 'admin_init', 'vkExUnit_common_options_init' );

function vkExUnit_get_common_options() {
	$options            = get_option( 'vkExUnit_common_options', vkExUnit_get_common_options_default() );
	$options_dafault    = vkExUnit_get_common_options_default();
	return apply_filters( 'vkExUnit_common_options', $options );
}

function vkExUnit_get_common_options_default() {
	// hook vkExUnit_package_is_enable()
	$default_options = array(
		'active_bootstrap'          => false,
		'active_fontawesome'        => false,
		'active_metaDescription'    => true,
		'active_metaKeyword'        => true,
		'active_wpTitle'            => true,
		'active_sns'                => true,
		'active_ga'                 => true,
		'active_relatedPosts'       => true,
		'active_call_to_action'     => true,
		'delete_options_at_deactivate' => false,
		'delete_options_with_bizvektors_common' => true,
	);
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_common_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_common_options_default();
	$output['active_bootstrap']         = (isset( $input['active_bootstrap'] )) ? true:false;
	$output['active_fontawesome']       = (isset( $input['active_fontawesome'] )) ? true:false;
	$output['active_metaDescription']   = (isset( $input['active_metaDescription'] )) ? true:false;
	$output['active_metaKeyword']       = (isset( $input['active_metaKeyword'] )) ? true:false;
	$output['active_icon']              = (isset( $input['active_icon'] )) ? true:false;
	$output['active_wpTitle']           = (isset( $input['active_wpTitle'] )) ? true:false;
	$output['active_sns']               = (isset( $input['active_sns'] )) ? true:false;
	$output['active_ga']                = (isset( $input['active_ga'] )) ? true:false;
	$output['active_relatedPosts']      = (isset( $input['active_relatedPosts'] )) ? true:false;
	$output['active_otherWidgets']      = (isset( $input['active_otherWidgets'] )) ? true:false;
	$output['active_css_customize']     = (isset( $input['active_css_customize'] )) ? true:false;
	$output['active_call_to_action']    = (isset( $input['active_call_to_action'] )) ? true:false;
	$output['delete_options_at_deactivate']             = (isset( $input['delete_options_at_deactivate'] )) ? true:false;
	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}
