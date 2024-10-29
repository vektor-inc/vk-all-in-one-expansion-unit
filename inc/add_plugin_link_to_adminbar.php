<?php

add_action( 'admin_bar_menu', 'veu_plugin_link_to_adminbar',100 );
function veu_plugin_link_to_adminbar( $wp_admin_bar ) {

	if ( ! veu_is_add_plugin_link_to_adminbar( get_bloginfo( 'version' ), is_admin() ) ){
		return;
	}

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

/**
 * プラグインページへのリンクを追加するかどうか
 * Whether or not to add a link to the plugin page.
 * 
 * これくらい veu_plugin_link_to_adminbar に書きたいところだが、PHPUnit でテストするために関数化
 * 
 * @param string $wp_version
 * @param bool $is_admin
 *
 * @return bool
 */
function veu_is_add_plugin_link_to_adminbar( $wp_version, $is_admin = false ){

	if ( $is_admin ){
		// 管理画面ではバージョンに関係なく追加
		return true;
	}  else {
		// 公開画面
		if ( version_compare( $wp_version, '6.5', '>=' ) ) {
			// WordPress 6.5 以上の場合はコアが追加してくるので何もしない
			return false;
		} else {
			return true;
		}
	}
}
