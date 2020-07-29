<?php
require_once dirname( __FILE__ ) . '/balloon/balloon-admin.php';

$admin_pages = array( 'settings_page_vk_blocks_options' );
Vk_Admin::admin_scripts( $admin_pages );

global $vk_blocks_prefix;
$vk_blocks_prefix = 'VK';

function vk_blocks_setting_menu() {
	global $vk_blocks_prefix;
	$custom_page = add_options_page(
		$vk_blocks_prefix . ' ' . __( 'Blocks setting', 'vk-blocks' ),       // Name of page
		$vk_blocks_prefix . ' ' . _x( 'Blocks', 'label in admin menu', 'vk-blocks' ),                // Label in menu
		'edit_theme_options',               // Capability required　このメニューページを閲覧・使用するために最低限必要なユーザーレベルまたはユーザーの種類と権限。
		'vk_blocks_options',               // ユニークなこのサブメニューページの識別子
		'vk_blocks_setting_page'         // メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) {
		return;
	}
}
add_action( 'admin_menu', 'vk_blocks_setting_menu' );

/*-------------------------------------------*/
/*	Setting Page
/*-------------------------------------------*/
function vk_blocks_setting_page() {
    global $vk_blocks_prefix;
	$get_page_title = $vk_blocks_prefix . ' ' . __( 'Blocks Setting', 'vk-post-author-display' );

	$get_logo_html = '<img src="'.plugin_dir_url( __FILE__ ).'/images/vk-blocks-logo_ol.svg'.'" alt="VK Blocks" />';
	$get_logo_html = apply_filters( 'vk_blocks_logo_html', $get_logo_html );

	$get_menu_html  = '<li><a href="#baloon-image-setting">' . __( 'Baloon Image Setting', 'vk-blocks' ) . '</a></li>';

	Vk_Admin::admin_page_frame( $get_page_title, 'vk_blocks_setting', $get_logo_html, $get_menu_html );
}

function vk_blocks_setting_option_save() {
	if ( isset( $_POST['vk_blocks_balloon_meta'] ) && $_POST['vk_blocks_balloon_meta'] ) {

		if ( check_admin_referer( 'vkb-nonce-key', 'vkb-setting-page' ) ) {
			// 保存処理
			if ( isset( $_POST['vk_blocks_balloon_meta'] ) && $_POST['vk_blocks_balloon_meta'] ) {
				update_option( 'vk_blocks_balloon_meta', $_POST['vk_blocks_balloon_meta'] );
			} else {
				update_option( 'vk_blocks_balloon_meta', '' );
			}

			wp_safe_redirect( menu_page_url( 'vk_blocks_options', false ) );
		}
	}
}
add_action( 'admin_init', 'vk_blocks_setting_option_save', 10, 2 );
