<?php
/**
 * WP Title の書き換え
 *
 * @package WP Title
 */
require dirname( __FILE__ ) . '/package/wp-title.php';
/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できず、
カスタム投稿タイプの投稿の編集画面で設定欄が表示されないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu',
	function() {
		require_once dirname( __FILE__ ) . '/package/class-veu-metabox-head-title.php';
		$VEU_Metabox_Head_Title = new VEU_Metabox_Head_Title();
	}
);

