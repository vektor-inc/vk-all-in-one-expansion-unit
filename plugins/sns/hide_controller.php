<?php
/*-------------------------------------------*/
/*  SNS非表示用のメタボックスを追加
/*-------------------------------------------*/
add_action( 'admin_menu', 'veu_sns_add_hide_meta_box' );

// add meta_box
function veu_sns_add_hide_meta_box() {
	$options = veu_get_sns_options();
	// ExUnitのメイン設定画面で「シェアボタンを表示する」にチェックが入っていない場合
	if ( empty( $options['enableSnsBtns'] ) ) {
		// 何も表示しないで終了
		return;
	}

	// 現在存在する投稿タイプを取得
	/*-------------------------------------------*/
	// 取得する条件
	$args = array(
		'public' => true,
	);
	// タイプの取得を実行
	$post_types = get_post_types( $args );

	foreach ( (array) $post_types as $post_type ) {
		add_meta_box(
			'sns', // metaboxのID
			veu_get_little_short_name() . ' ' . __( 'Share bottons', 'vkExUnit' ), // metaboxの表示名
			'veu_sns_share_botton_hide_meta_box_body', // このメタボックスに表示する中身の関数名
			$post_type, // このメタボックスをどの投稿タイプで表示するのか？
			'side' // 表示する位置
		);
	} // foreach ( (array) $post_types as $post_type ) {

}

/*-------------------------------------------*/
/*  入力フィールドの生成
/*-------------------------------------------*/
function veu_sns_is_display_hide_chekbox( $post_type ) {
	// SNS設定のオプション値を取得
	$options = veu_get_sns_options();

	// 表示する にチェックが入っていない場合は 投稿詳細画面でボタン非表示のチェックボックスを表示しない
	if ( empty( $options['enableSnsBtns'] ) ) {
		return false;
	}

	// シェアボタンを表示しない投稿タイプが配列で指定されている場合（チェックが入ってたら）
	if ( isset( $options['snsBtn_exclude_post_types'] ) && is_array( $options['snsBtn_exclude_post_types'] ) ) {
		foreach ( $options['snsBtn_exclude_post_types'] as $key => $value ) {
			// 非表示チェックが入っている場合
			if ( $value ) {
				// 今の投稿タイプと比較。同じだったら...
				if ( $post_type == $key ) {
					return false;
				}
			}
		}
	}
	return true;
}
function veu_sns_share_botton_hide_meta_box_body() {

	//今編集している投稿の投稿タイプを取得
	$post_type = get_post_type();

	  // 編集中のページの投稿タイプ が シェアボタンを表示しない投稿タイプに含まれている場合
	if ( ! veu_sns_is_display_hide_chekbox( $post_type ) ) {

		// 「この投稿タイプではシェアボタンを表示しないように設定されています。」を表示
		echo __( 'This post type is not set to display the share button.', 'vkExUnit' ) . '<br>';
		echo' <a href="' . admin_url( '/admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options' ) . '" target="_blank">シェアボタンの表示設定</a>';

	} else {

		// シェアボタンを表示しない設定をするチェックボックスを表示

		//CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__sns_share_botton_hide」として設定）
		wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__sns_share_botton_hide' );

		global $post;
		// カスタムフィールド 'sns_share_botton_hide' の値を取得
		$sns_share_botton_hide = get_post_meta( $post->ID, 'sns_share_botton_hide', true );

		// チェックが入っている場合（ 表示しない ）
		if ( $sns_share_botton_hide ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Don\'t display share bottons.', 'vkExUnit' );
		echo '<ul>';
		echo '<li><label>' . '<input type="checkbox" id="sns_share_botton_hide" name="sns_share_botton_hide" value="true"' . $checked . '> ' . $label . '</label></li>';
		echo '</ul>';

	}

}

/*-------------------------------------------*/
/*  入力された値の保存
/*-------------------------------------------*/
add_action( 'save_post', 'sns_save_hide_items' );

function sns_save_hide_items( $post_id ) {
	global $post;

	//設定したnonce を取得（CSRF対策）
	$noncename__sns_share_botton_hide = isset( $_POST['noncename__sns_share_botton_hide'] ) ? $_POST['noncename__sns_share_botton_hide'] : null;

	//nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
	if ( ! wp_verify_nonce( $noncename__sns_share_botton_hide, wp_create_nonce( __FILE__ ) ) ) {
		return $post_id;
	}

	//自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$field       = 'sns_share_botton_hide';
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

/*-------------------------------------------*/
/*  非表示を実行
/*-------------------------------------------*/
