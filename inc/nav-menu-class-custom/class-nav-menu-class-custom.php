<?php
/**
 * VkNavMenuClassCustom
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

if ( ! class_exists( 'VkNavMenuClassCustom' ) ) {

	class VkNavMenuClassCustom {

		public static function init() {
			// Classic Navigation custom
			add_filter( 'nav_menu_css_class', array( __CLASS__, 'add_current_class_to_classic_navi' ), 10, 2 );
			// Navigation Block custom
			add_filter( 'render_block', array( __CLASS__, 'add_current_class_to_navigation_item_block' ), 10, 2 );
		}

		/**
		 * Add current class to classic navigation item
		 *
		 * @param array  $classes : メニューのクラス配列
		 * @param object $item : メニューオブジェクト
		 * @return array $classes : メニューのクラス配列
		 */
		public static function add_current_class_to_classic_navi( $classes, $item ) {
			// 付与するカレントクラス名
			$add_current_class_name = 'current-menu-ancestor';

			if ( self::is_active_menu_item( $item->url ) ) {
				$classes[] = $add_current_class_name;
			} else {
				// 投稿のトップのメニューアイテムは、カスタム投稿タイプのページを表示していても
				// 勝手に current クラスが付与されるので、
				// アクティブでないと判断された項目に current クラスがあった場合は削除する必要がある

				// 現在配列に入っているclassをループ
				for ( $i = 1; $i <= count( $classes ); $i++ ) {
					if ( isset( $classes[ $i ] ) ) {
						// currentがあった場合
						if ( $classes[ $i ] == $add_current_class_name || $classes[ $i ] == 'current_page_parent' ) {
							// そのクラスをキーごと削除
							unset( $classes[ $i ] );
						}
					}
				}
			}

			// キーのフリなおし
			$classes = array_values( $classes );
			return $classes;
		}

		/**
		 * Add current class to navigation block item
		 *
		 * @param string $block_content : ブロックのコンテンツ
		 * @param array  $block : ブロックの属性
		 * @return string : カレントクラスを追加したブロックのコンテンツ
		 */
		public static function add_current_class_to_navigation_item_block( $block_content, $block ) {
			// ナビゲーションアイテムブロックに対して処理を適用
			if ( 'core/navigation-link' === $block['blockName'] || 'core/navigation-submenu' === $block['blockName'] ) {
				if ( self::is_active_menu_item( $block['attrs']['url'] ) ) {
					// $block_content の中の class=" 中に current-menu-item という文字列がない場合に current-menu-ancestor を追加する
					if ( strpos( $block_content, 'current-menu-item' ) === false ) {
						$block_content = preg_replace(
							'/class="([^"]*)"/',
							'class="$1 current-menu-item"',
							$block_content,
							1
						);
					}
				}
			}
			return $block_content;
		}

		/**
		 * Get post type from URL
		 *
		 * @param string $url : URL
		 * @return string : post type name
		 */
		public static function get_post_type_from_url( $url ) {
			$menu_url_post_type = '';

			// Get rewrite rules
			$rewrite_rules = get_option( 'rewrite_rules' );

			if ( ! $rewrite_rules || ! is_array( $rewrite_rules ) ) {

				// In case of default permalink structure

				$pattern = '/.*post_type=(.*)/';
				$subject = $url;
				preg_match( $pattern, $subject, $matches );

				// メニューの投稿タイプが取得できたら
				if ( isset( $matches[1] ) ) {
					$menu_url_post_type = $matches[1];
				} else {
					$menu_url_post_type = '';
				}// if ( isset( $matches[1] ) ) {
			} else {

				// リライトルールが普通に保存されている場合

				// リライトルールをループ
				foreach ( $rewrite_rules as $key => $value ) {

					// メニューに記載されているURLから投稿タイプ名を判別する

					// ループ中のりライトルールがメニューのURLと合致するか正規表現で検出
					$pattern = '{' . $key . '}';
					$subject = $url;
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
							$menu_url_post_type = $matches[1];
							// 最初にマッチしてクラスを付与したら抜ける
							break;
						} // if ( isset( $matches[1] ) ) {
					} // if ( $matches ) {
				} // foreach ( $rewrite_rules as $key => $value ) {
			}
			return $menu_url_post_type;
		}

		/**
		 * カレントメニューアイテムかどうかを判定する
		 *
		 * @param array $item_src : メニューアイテムの属性
		 * @return bool : カレントメニューアイテムかどうか
		 */
		public static function is_active_menu_item( $item_src ) {
			$return = false;

			// 今表示しているページが属する投稿タイプを取得
			if ( function_exists( 'vk_get_post_type' ) ) {
				$displaying_page_post_type_info = vk_get_post_type();
				$displaying_page_post_type_slug = $displaying_page_post_type_info['slug'];
			} else {
				$displaying_page_post_type_slug = get_post_type();
			}

			/*
				投稿アーカイブの指定された固定ページメニューアイテムの処理
			/*-------------------------------------------*/
			$post_top_id  = get_option( 'page_for_posts' );
			$post_top_url = get_the_permalink( $post_top_id );

			// 投稿トップのメニューアイテム
			if ( $post_top_url === $item_src ) {
				if ( $displaying_page_post_type_slug === 'post' ) {
					// 今表示しているページの投稿タイプが post の場合
					$return = true;
				}
			}

			// メニュー項目のリンク先のページの投稿タイプを取得
			$menu_url_post_type = self::get_post_type_from_url( $item_src );

			if ( ! empty( $menu_url_post_type ) && ! empty( $displaying_page_post_type_slug ) ) {
				// 今表示しているページの投稿タイプとメニューに記入されているURLのページの投稿タイプが同じ場合
				if ( $displaying_page_post_type_slug === $menu_url_post_type ) {
					$return = true;
				}
			}

			return $return;
		}
	}

}
