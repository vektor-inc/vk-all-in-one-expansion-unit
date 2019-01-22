<?php
add_filter( 'nav_menu_css_class', 'veu_nav_menu_class_custom', 10, 2 );
function veu_nav_menu_class_custom( $classes, $item ) {

	// 今表示しているページが属する投稿タイプを取得
	if ( function_exists( 'vk_get_post_type' ) ) {
		$post_type_info = vk_get_post_type();
	} else {
		$post_type_info['slug'] = get_post_type();
	}

	// 付与するカレントクラス名
	$add_current_class_name = 'current-menu-ancestor';

	/*  投稿アーカイブの指定された固定ページメニューアイテムの処理
	/*-------------------------------------------*/
	$post_top_id  = get_option( 'page_for_posts' );
	$post_top_url = get_the_permalink( $post_top_id );

	if ( $post_top_url === $item->url ) {

		if ( $post_type_info['slug'] === 'post' ) {
			// 今表示しているページの投稿タイプが post の場合
			// currentクラスを付与
			$classes[] = $add_current_class_name;

		} else {
			// 今表示しているページの投稿タイプが post 以外の場合
			// 現在配列に入っているclassをループ
			for ( $i = 1; $i <= count( $classes ); $i++ ) {

				// currentがあった場合
				if ( $classes[ $i ] == $add_current_class_name || $classes[ $i ] == 'current_page_parent' ) {
					// そのクラスをキーごと削除
					unset( $classes[ $i ] );
				}
			}

			// キーのフリなおし
			$classes = array_values( $classes );

		}
	}

	/*  メニューがカスタムリンクでリンク先がカスタム投稿タイプのアーカイブの時
	/*-------------------------------------------*/
	if ( $item->object == 'custom' && $item->type == 'post_type_archive' ) {

		// リライトルールを取得
		$rewrite_rules = get_option( 'rewrite_rules' );

		// リライトルールをループ
		foreach ( $rewrite_rules as $key => $value ) {

			// メニューに記載されているURLから投稿タイプ名を判別する

			// メニューのURLにマッチするリライトルールを席表現で抽出する。
			// しかし、$key の 中の / がそのまだと正規表現の終了となってしまうので、\/ に一度変換する
			$string      = $key;
			$pattern     = '/(\/)/';
			$replacement = '\/';
			// / を \/ に変換して改めて $key に代入
			$key = preg_replace( $pattern, $replacement, $string );

			// 改めて正規表現で メニューのURLと合致するリライトルールを検出
			$pattern = '/' . $key . '/';
			$subject = $item->url;
			preg_match( $pattern, $subject, $matches );

			// マッチした場合
			if ( $matches ) {

				// マッチした $value の URL （　index.php?post_type=custom　など ） から投稿タイプが判別できる
				// 正規表現で post_type= の値を抽出する

				$pattern = '/index.php\?post_type=(.*)/';
				$subject = $value;
				preg_match( $pattern, $subject, $matches );

				// メニューの投稿タイプが取得できたら
				if ( isset( $matches[1] ) ) {

						// 今表示しているページの投稿タイプとメニューに記入されているURLの投稿タイプっが同じ場合
					if ( isset( $post_type_info['slug'] ) && $post_type_info['slug'] === $matches[1] ) {
						$classes[] = $add_current_class_name;
						// 最初にマッチしてクラスを付与したら抜ける
						break;
					}
				} // if ( isset( $matches[1] ) ) {
			} // if ( $matches ) {
		} // foreach ( $rewrite_rules as $key => $value ) {

	} // if ( $item->object == 'custom' && $item->type == 'post_type_archive' ) {

	return $classes;
}
