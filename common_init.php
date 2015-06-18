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
	$options			= get_option( 'vkExUnit_common_options', vkExUnit_get_common_options_default() );
	$options_dafault	= vkExUnit_get_common_options_default();
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
		'active_metaDescription'	=> 'true',
		'active_sns' 				=> 'true',
		// 'active_ga'					=> 'true',
		// 'active_relatedPosts'		=> 'true',
		// 'active_widget_newPosts'	=> 'true',

	);
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

/*-------------------------------------------*/
/*	validate
/*-------------------------------------------*/

function vkExUnit_common_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_common_options_default();
	$output['active_metaDescription']	= (isset($input['active_metaDescription'])) ? 'true':'false';
	$output['active_sns']				= (isset($input['active_sns'])) ? 'true':'false';
	// $output['active_ga']				= $input['active_ga'];
	// $output['active_relatedPosts']		= $input['active_relatedPosts'];
	// $output['active_widget_newPosts']	= $input['active_widget_newPosts'];

	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}