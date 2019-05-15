<?php

add_action( 'admin_bar_menu', 'vkExUnit_package_adminbar', 43 );
function vkExUnit_package_adminbar( $wp_admin_bar ) {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return; }

	global $vkExUnit_options;
	if ( ! isset( $vkExUnit_options ) || ! count( $vkExUnit_options ) ) {
		return;
	}

	foreach ( $vkExUnit_options as $opt ) {
		$wp_admin_bar->add_node(
			array(
				'parent' => 'veu_adminlink_main',
				'title'  => $opt['tab_label'],
				'id'     => 'vew_configbar_' . $opt['option_name'],
				'href'   => admin_url() . 'admin.php?page=vkExUnit_main_setting#' . $opt['option_name'],
			)
		);
	}
}

/**
 * [veu_admin_css]
 *
 * @return [type] [description]
 */
function veu_admin_css() {
	global $vkExUnit_version;
	wp_enqueue_style( 'veu_admin_css', plugin_dir_url( __FILE__ ) . '/css/vkExUnit_admin.css', array(), $vkExUnit_version, 'all' );
}
add_action( 'admin_enqueue_scripts', 'veu_admin_css' );
