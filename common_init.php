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
		'active_pageList_ancestor'	=> true,
		'active_childPageIndex'			=> true,
		'delete_options_at_deactivate' => false,
		'delete_options_with_bizvektors_common' => true,
		'content_filter_state'      => 'content',
	);
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_common_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_common_options_default();
	$output['active_bootstrap']         = ( !empty( $input['active_bootstrap'] ) ) ? true:false;
	$output['active_fontawesome']       = ( !empty( $input['active_fontawesome'] ) ) ? true:false;
	$output['active_metaDescription']   = ( !empty( $input['active_metaDescription'] ) ) ? true:false;
	$output['active_metaKeyword']       = ( !empty( $input['active_metaKeyword'] ) ) ? true:false;
	$output['active_icon']              = ( !empty( $input['active_icon'] ) ) ? true:false;
	$output['active_wpTitle']           = ( !empty( $input['active_wpTitle'] ) ) ? true:false;
	$output['active_sns']               = ( !empty( $input['active_sns'] ) ) ? true:false;
	$output['active_ga']                = ( !empty( $input['active_ga'] ) ) ? true:false;
	$output['active_relatedPosts']      = ( !empty( $input['active_relatedPosts'] ) ) ? true:false;
	$output['active_otherWidgets']      = ( !empty( $input['active_otherWidgets'] ) ) ? true:false;
	$output['active_css_customize']     = ( !empty( $input['active_css_customize'] ) ) ? true:false;
	$output['active_call_to_action']    = ( !empty( $input['active_call_to_action'] ) ) ? true:false;
	$output['delete_options_at_deactivate'] = ( !empty( $input['delete_options_at_deactivate'] )) ? true:false;
	$output['content_filter_state']     = ( !empty( $input['content_filter_state'] ) ) ? 'loop_end': 'content';
	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}
