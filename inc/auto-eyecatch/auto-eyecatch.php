<?php
/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'admin_menu',
	function () {
		require_once __DIR__ . '/class-veu-metabox-eyecatch.php';
	}
);

require_once __DIR__ . '/class-veu-auto-eyecatch.php';
