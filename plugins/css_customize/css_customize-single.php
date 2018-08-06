<?php
/*
  Custom CSS Widget
/* ------------------------------------------- */

add_action( 'admin_menu', 'veu_custom_css_hooks' );
add_action( 'save_post', 'veu_save_custom_css' );
// </head>タグの直上に出力させたいので第三引数に 50 を設定
add_action( 'wp_head', 'veu_insert_custom_css', 50 );


/*
 メタボックス生成
/* ------------------------------------------------ */
function veu_custom_css_hooks() {

	$post_types = get_post_types( array( 'public' => true ) );

	foreach ( $post_types as $post_type ) {
		add_meta_box(
			'veu_custom_css', // （必須） 編集画面セクションの HTML ID
			__( 'Custom CSS', 'vkExUnit' ), // （必須） 編集画面セクションのタイトル、画面上に表示される
			'veu_custom_css_input', // （必須） 編集画面セクションに HTML 出力する関数.
			$post_type, // （オプション）編集画面セクションを表示する書き込み画面のタイプ（例： 'post'、 'page'、 'dashboard'、 'link'、 'attachment'、 'custom_post_type'、 'comment'）
			'normal', // （オプション） 編集画面セクションが表示される部分 ('normal', 'advanced' または (2.7 以降) 'side')
			'high' // （オプション） ボックスが表示される優先度 ('high', 'core', 'default' または 'low')
		);
	}

} // function veu_custom_css_hooks() {


/*
 入力用テキストエリアを生成
/* ------------------------------------------------ */
function veu_custom_css_input() {
	global $post;
	// wp_create_nonce( 'veu_custom-css' ) の引数は「（オプション） アクションの名前」
	// Nonce はウェブサイトやデータベースを、予期せぬまたは重複したリクエストによって取り返しの付かない変更が起きてしまうことから保護する処理
	echo '<input type="hidden" name="veu_custom_css_noncename" id="veu_custom_css_noncename" value="' . wp_create_nonce( 'veu_custom-css' ) . '" />';
	// 投稿／ページの編集画面でメタ情報をカスタムフィールド欄に表示させないために、
	// get_post_meta の「カスタムフィールドの名前」の前にアンダースコアをつける
	echo '<textarea name="veu_custom_css" id="veu_custom_css" rows="5" cols="30" style="width:100%;">' . get_post_meta( $post->ID, '_veu_custom_css', true ) . '</textarea>';

} // function veu_custom_css_input() {


/*
 入力されたテキストエリアの内容を保存
/* ------------------------------------------------ */
function veu_save_custom_css( $post_id ) {

	// wp_verify_nonce( $_POST['veu_custom_css_noncename'], 'veu_custom-css' )の引数は「（必須） 検証する nonce,（オプション） アクションの名前」
	// nonce が正しいもので有効期限が切れていないことを、指定されたアクションとの関係も含めて確かめる処理
	$noonce = isset( $_POST['veu_custom_css_noncename'] ) ? htmlspecialchars( $_POST['veu_custom_css_noncename'] ) : null;
	if ( ! wp_verify_nonce( $noonce, 'veu_custom-css' ) ) {
		return $post_id;
	}
	// 自動保存時には処理をしないように
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	$custom_css = $_POST['veu_custom_css'];
	// 指定した投稿に存在するカスタムフィールドの値を更新
	update_post_meta( $post_id, '_veu_custom_css', $custom_css );

} // function veu_save_custom_css($post_id) {


/*
 入力された CSS をソースに出力
/* ------------------------------------------------ */
function veu_insert_custom_css() {

	if ( is_singular() ) {
		// if 現在の WordPress クエリにループできる結果があるかどうか
		// while 記事がある間ループして１件ずつ処理する
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
					// preg_replace で改行を削除して wp_kses_post でエスケープする
					echo '<style type="text/css">' . wp_kses_post( preg_replace( '/(?:\n|\r|\r\n)/', '', get_post_meta( get_the_ID(), '_veu_custom_css', true ) ) ) . '</style>';
				endwhile;
		endif;
		// ページ上の別の場所で同じクエリを再利用するために、ループの投稿情報を巻き戻し、前回と同じ順序で先頭の投稿を取得できるように
		rewind_posts();
	}

} // function veu_insert_custom_css() {
