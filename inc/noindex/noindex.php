<?php
/**
 * VkExUnit noindex.php
 * insert noindex tag for head.
 *
 * @package  VkExUnit
 * @author   Hidekazu IShikawa <ishikawa@vektor-inc.co.jp>
 * @since    13/May/2019
 */

/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu', function() {
		require_once( dirname( __FILE__ ) . '/class-veu-metabox-noindex.php' );
	}
);

/*
  noindex出力処理
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_noindex_print_head' );
function veu_noindex_print_head() {
	global $post;
	if ( is_singular() ) {
		$vk_print_noindex = get_post_meta( $post->ID, '_vk_print_noindex', true );
		if ( $vk_print_noindex ) {
			echo '<meta name=”robots” content=”noindex,follow” />';
		}
	}
}
