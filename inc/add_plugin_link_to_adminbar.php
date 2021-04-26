<?php

add_action( 'admin_bar_menu', 'veu_plugin_link_to_adminbar',100 );
function veu_plugin_link_to_adminbar( $wp_admin_bar ) {

	// 「有効化設定」は edit_theme_options 権限にはアクセスさせない
	if ( current_user_can( 'activate_plugins' ) ) {

		$wp_admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'veu_plugin',
				'title'  => __( 'Plugin', 'vk-all-in-one-expansion-unit' ),
				'href'   => admin_url() . 'plugins.php',
			)
		);
		wp_admin_bar_appearance_menu( $wp_admin_bar );

	}
}
