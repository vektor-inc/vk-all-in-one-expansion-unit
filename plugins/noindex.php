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
	admin_metabox_activate
/*-------------------------------------------*/
add_filter( 'veu_content_meta_box_activation', 'veu_noindex_metabox_activate', 10, 1 );
function veu_noindex_metabox_activate( $flag ) {
	return true;
}


 /*
   metaboxの内容
 /*-------------------------------------------*/
add_action( 'veu_content_meta_box_content', 'veu_noindex_metabox_body' );

function veu_noindex_metabox_body() {
	global $post;

	echo '<h3 class="admin-custom-h3">' . __( 'Noindex setting', 'vk-all-in-one-expansion-unit' ) . '</h3>';

	// CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__noindex_print」として設定）
	wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__noindex_print' );

	// カスタムフィールド '_vk_print_noindex' の値を取得
	$vk_print_noindex = get_post_meta( $post->ID, '_vk_print_noindex', true );

	// チェックが入っている場合（ 出力する ）
	if ( $vk_print_noindex ) {
		$checked = ' checked';
	} else {
		$checked = '';
	}

	$label = __( 'Print noindex tag.', 'vk-all-in-one-expansion-unit' );
	echo '<ul>';
	echo '<li><label>' . '<input type="checkbox" id="_vk_print_noindex" name="_vk_print_noindex" value="true"' . $checked . '> ' . $label . '</label></li>';
	echo '</ul>';
}

/*
  入力された値の保存
/*-------------------------------------------*/
add_action( 'save_post', 'veu_noindex_save_custom_fields' );

function veu_noindex_save_custom_fields( $post_id ) {
	global $post;

	// 設定したnonce を取得（CSRF対策）
	$noncename__noindex_print = isset( $_POST['noncename__noindex_print'] ) ? $_POST['noncename__noindex_print'] : null;

	// nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
	if ( ! wp_verify_nonce( $noncename__noindex_print, wp_create_nonce( __FILE__ ) ) ) {
		return $post_id;
	}

	// 自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$field       = '_vk_print_noindex';
	$field_value = ( isset( $_POST[ $field ] ) ) ? $_POST[ $field ] : '';
	// データが空だったら入れる
	if ( get_post_meta( $post_id, $field ) == '' ) {
		add_post_meta( $post_id, $field, $field_value, true );
		// 今入ってる値と違ってたらアップデートする
	} elseif ( $field_value != get_post_meta( $post_id, $field, true ) ) {
		update_post_meta( $post_id, $field, $field_value );
		// 入力がなかったら消す
	} elseif ( $field_value == '' ) {
		delete_post_meta( $post_id, $field, get_post_meta( $post_id, $field, true ) );
	}

}

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
