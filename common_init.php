<?php
function vkExUnit_common_options_init() {
	if ( false === vkExUnit_get_common_options() )
		add_option( 'vkExUnit_common_options', vkExUnit_get_common_options_default() );
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
	// foreach ($options_dafault as $key => $value) {
	// 	if (isset($options[$key])) {
	// 		$options[$key] = $options[$key];
	// 	} else {
	// 		$options[$key] = $options_dafault[$key];
	// 	}
	// }
	return apply_filters( 'vkExUnit_common_options', $options );
}

function vkExUnit_get_common_options_default() {
	$default_options = array(
		'active_bootstrap'          => false,
		'active_metaDescription'    => true,
		'active_sns'                => true,
		'active_ga'                 => true,
		'active_relatedPosts'       => true,
		'active_otherWidgets'       => true,
		'active_css_customize'      => true,

	);
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

/*-------------------------------------------*/
/*	validate
/*-------------------------------------------*/

function vkExUnit_common_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_common_options_default();
	$output['active_bootstrap']         = (isset($input['active_bootstrap'])) ? true:false;
	$output['active_metaDescription']   = (isset($input['active_metaDescription'])) ? true:false;
	$output['active_sns']               = (isset($input['active_sns'])) ? true:false;
	$output['active_ga']                = (isset($input['active_ga'])) ? true:false;
	$output['active_relatedPosts']      = (isset($input['active_relatedPosts'])) ? true:false;
	$output['active_otherWidgets']      = (isset($input['active_otherWidgets'])) ? true:false;
	$output['active_css_customize']     = (isset($input['active_css_customize'])) ? true:false;

	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}