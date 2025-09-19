<?php
/**
 * WP Title の書き換え
 *
 * @package WP Title
 */
require __DIR__ . '/package/wp-title.php';
require_once __DIR__ . '/package/class-veu-title-form-helper.php';

/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できず、
カスタム投稿タイプの投稿の編集画面で設定欄が表示されないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu',
	function () {
		require_once __DIR__ . '/package/class-veu-metabox-head-title.php';
		$VEU_Metabox_Head_Title = new VEU_Metabox_Head_Title();
	}
);

// タクソノミー用タイトルタグ設定を初期化
add_action(
	'init',
	function () {
		require_once __DIR__ . '/package/class-veu-taxonomy-head-title.php';
		$VEU_Taxonomy_Head_Title = new VEU_Taxonomy_Head_Title();
	}
);
