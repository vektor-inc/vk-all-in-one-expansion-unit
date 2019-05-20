<?php
/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu', function() {
		require_once( dirname( __FILE__ ) . '/class-veu-metabox-eyecatch.php' );
	}
);

require_once( dirname( __FILE__ ) . '/class-veu-auto-eyecatch.php' );
