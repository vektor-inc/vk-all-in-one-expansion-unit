<?php
// Boostrapの読み込み
function vkblocks_load_vk_components( $hook_suffix ) {

	$template = wp_get_theme()->Template;
	// lightning 系（ベクトル）の場合 vk-components はテーマなどで読み込むので必要ない
	if ( $template == 'lightning' || $template == 'lightning-pro' ) {
		return;
	}

	wp_register_style( 'vk-components-style', VK_BLOCKS_URL . '/build/vk-components.css', false );

	// 管理画面
	if ( is_admin() ) {

		if ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) {
			wp_enqueue_style( 'vk-components-style' );
		}
	} else {
		wp_enqueue_style( 'vk-components-style' );
	}

}
add_action( 'admin_enqueue_scripts', 'vkblocks_load_vk_components' );
add_action( 'wp_enqueue_scripts', 'vkblocks_load_vk_components' );
