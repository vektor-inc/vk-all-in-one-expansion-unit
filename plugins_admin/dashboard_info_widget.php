<?php
/*--------------------------------------------------*/
/*	Display a Ex_unit information on the dashboard
/*--------------------------------------------------*/

add_filter('vkExUnit_is_plugin_dashboard_info_widget', 'vkExUnit_dash_beacon', 10, 1 );
function vkExUnit_dash_beacon($flag){
	$flag = true;
	return $flag;
}

add_action( 'wp_dashboard_setup', 'vkExUnit_dashboard_widget' );

function vkExUnit_dashboard_widget()
{
	wp_add_dashboard_widget(
		'vkExUnit_dashboard_widget',
		__('News from VK All in One Expansion Unit','vkExUnit'),
		'vkExUnit_news_body'
	);
}