<?php
/*
  add page custom field
/*-------------------------------------------*/

require_once( dirname( __FILE__ ) . '/class-veu-metabox.php' );

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

		// Add parent meta box on exists post types
		foreach ( $post_types as $key => $post_type ) {
			add_meta_box( 'veu_parent_post_metabox', $meta_box_name, 'veu_parent_metabox_body', $post_type, 'normal', 'high' );
		}
	}
	/*
	VEU_Metabox 内の get_post_type が実行タイミングによっては
	カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
	admin_menu のタイミングで読み込んでいる
	*/
	// 子ページリストやサイトマップなど「挿入アイテムの設定」を読み込むための子metaboxを読み込む
	if ( veu_is_insert_item_metabox_display() ) {
		require_once( dirname( __FILE__ ) . '/class-veu-metabox-insert-items.php' );
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
