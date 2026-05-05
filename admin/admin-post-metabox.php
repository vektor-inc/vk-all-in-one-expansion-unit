<?php
/*
	add page custom field
/*-------------------------------------------*/

require_once __DIR__ . '/class-veu-metabox.php';

function veu_add_parent_metabox() {

	// parent metabox（統合metabox）を出力する

	if ( veu_is_parent_metabox_display() ) {
		$meta_box_name = veu_get_name();

		/*
		Original Brand Unit で 名前を未入力にされた時にメタボックスが表示されなくなってしまうので、
		とりあえずスペースを代入
		 */
		if ( ! $meta_box_name ) {
			$meta_box_name = ' ';
		}

		// Get exists post types
		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args );

		// Add parent meta box on exists post types.
		// 投稿タイプごとに統合メタボックスを登録する。
		foreach ( $post_types as $key => $post_type ) {
			// __back_compat_meta_box => true means "this metabox is replaced by a Block Editor
			// equivalent, so hide it on Block Editor screens (still show on Classic Editor)".
			// We only set this flag for post types that support 'custom-fields', because the
			// new sidebar panel relies on REST API meta which WordPress only exposes when
			// 'custom-fields' is supported. For post types without that support, the sidebar
			// panel cannot save values, so we must NOT mark the legacy metabox as back-compat;
			// it has to remain visible on the Block Editor screen as the working UI.
			//
			// __back_compat_meta_box => true は「ブロックエディタで代替されるので
			// ブロックエディタ画面では非表示（クラシックエディタでは表示）」を意味する。
			// サイドバーパネルは REST API メタに依存しており、これは 'custom-fields' を
			// サポートする投稿タイプでのみ露出される。非対応の投稿タイプではサイドバー
			// パネルから保存できないため、このフラグを付けてはならない（旧メタボックスを
			// ブロックエディタ画面でも実働 UI として表示し続ける必要がある）。
			$callback_args = post_type_supports( $post_type, 'custom-fields' )
				? array( '__back_compat_meta_box' => true )
				: null;
			add_meta_box( 'veu_parent_post_metabox', $meta_box_name, 'veu_parent_metabox_body', $post_type, 'normal', 'high', $callback_args );
		}
	}
	/*
	VEU_Metabox 内の get_post_type が実行タイミングによっては
	カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
	admin_menu のタイミングで読み込んでいる
	*/
	// 子ページリストやサイトマップなど「挿入アイテムの設定」を読み込むための子metaboxを読み込む
	if ( veu_is_insert_item_metabox_display() ) {
		require_once __DIR__ . '/class-veu-metabox-insert-items.php';
	}
}

add_action( 'admin_menu', 'veu_add_parent_metabox' );


/**
 * 統合metaboxの中身
 * この中の add_action で各機能の子メタボックスが表示される
 */
function veu_parent_metabox_body() {
	echo '<div class="veu_metabox_nav">';
	// ▼ Toggle Button
	echo '<p class="veu_metabox_all_section_toggle close">';
	echo '<button class="button button-default veu_metabox_all_section_toggle_btn_open">' . __( 'Open all', 'vk-all-in-one-expansion-unit' ) . ' <i class="fas fa-caret-down"></i></button> ';
	echo '<button class="button button-default veu_metabox_all_section_toggle_btn_close">' . __( 'Close all', 'vk-all-in-one-expansion-unit' ) . ' <i class="fas fa-caret-up"></i></button>';
	echo '</p>';
	// ▲ Toggle Button
	echo '</div>';

	// 各機能の子メタボックスはここに表示される
	do_action( 'veu_post_metabox_body' );

	echo '<div class="veu_metabox_footer">';
	echo veu_get_systemlogo_html();
	echo '</div>';
}
