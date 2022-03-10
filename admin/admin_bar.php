<?php
/**
 * VkExUnit admin_bar.php
 * admin_bar button.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    28/Aug/2015
 */


add_action( 'admin_bar_menu', 'vkExUnit_adminbar_link', 40 );
function vkExUnit_adminbar_link( $wp_admin_bar ) {

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return; }

	$args = array(
		'id'    => 'veu_adminlink',
		'title' => '<span class="ab-veu-icon"></span>' . vkExUnit_get_little_short_name(),
		'href'  => admin_url() . 'admin.php?page=vkExUnit_main_setting',
		'meta'  => array(),
	);
	$wp_admin_bar->add_node( $args );

	// 「有効化設定」は edit_theme_options 権限にはアクセスさせない
	if ( current_user_can( 'activate_plugins' ) ) {

		$wp_admin_bar->add_node(
			array(
				'parent' => 'veu_adminlink',
				'id'     => 'veu_adminlink_active',
				'title'  => __( 'Active Setting', 'vk-all-in-one-expansion-unit' ),
				'href'   => admin_url() . 'admin.php?page=vkExUnit_setting_page',
			)
		);

	}

	$wp_admin_bar->add_node(
		array(
			'parent' => 'veu_adminlink',
			'id'     => 'veu_adminlink_main',
			'title'  => __( 'Main Setting', 'vk-all-in-one-expansion-unit' ),
			'href'   => admin_url() . 'admin.php?page=vkExUnit_main_setting',
		)
	);

	do_action( 'vkExUnit_action_adminbar', $wp_admin_bar );
}
