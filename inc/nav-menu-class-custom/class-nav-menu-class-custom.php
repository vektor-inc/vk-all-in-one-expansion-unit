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
			add_filter( 'nav_menu_css_class', array( __CLASS__, 'classic_navi_class_custom' ), 10, 2 );
			// Navigation Block custom
			add_filter( 'render_block', array( __CLASS__, 'navigation_item_block_class_custom' ), 10, 2 );
		}

		/**
		 * Add current class to classic navigation item
		 *
		 * @param array  $classes : メニューのクラス配列
		 * @param object $item : メニューオブジェクト
		 * @return array $classes : メニューのクラス配列
		 */
		public static function classic_navi_class_custom( $classes, $item ) {
			// 付与するカレントクラス名
			$add_current_class_name = 'current-menu-ancestor';

			if ( isset( $item->url ) && self::is_active_menu_item( $item->url ) ) {
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
		public static function navigation_item_block_class_custom( $block_content, $block ) {
			// ナビゲーションアイテムブロックに対して処理を適用
			if ( 'core/navigation-link' === $block['blockName'] || 'core/navigation-submenu' === $block['blockName'] ) {
				// 固定ページの時の動作はもともと問題ないので、それ以外の場合のみ処理する
				if ( ! is_page() ) {
					if ( isset( $block['attrs']['url'] ) && self::is_active_menu_item( $block['attrs']['url'] ) ) {
						$block_content = self::class_name_custom( $block_content, 'current-menu-item', true );
					} else {
						// カスタム投稿タイプのページを表示していても、カレントクラスが付与されるので、
						// アクティブでないと判断された項目に current クラスがあった場合は削除
						$block_content = self::class_name_custom( $block_content, 'current-menu-ancestor', false );
					}
				}
			}
			return $block_content;
		}

		/**
		 * クラス名を改変する
		 *
		 * @param string $content : 対象文字列
		 * @param string $class_name : 追加・削除するクラス名
		 * @param bool   $add : true の場合は $class_name を class= の中に追加する、false の場合は削除する
		 * @return string : クラス名を改変したコンテンツ
		 */
		public static function class_name_custom( $content, $class_name, $add = true ) {
			if ( $add ) {
				// $class_name が class= の中に存在しない場合にのみ追加
				if ( strpos( $content, $class_name ) === false ) {
					$content = preg_replace(
						'/class="([^"]*)"/',
						'class="$1 ' . $class_name . '"',
						$content,
						1
					);
				}
			} else {
				// $class_name が class= の中に存在する場合は削除
				$content = preg_replace(
					'/class="([^"]*)\b' . preg_quote( $class_name, '/' ) . '\b([^"]*)"/',
					'class="$1$2"',
					$content,
					1
				);
				// 余分なスペースを削除
				$content = preg_replace(
					'/class="\s*([^"]*?)\s*"/',
					'class="$1"',
					$content
				);
				// 連続するスペースを1つにする
				$content = preg_replace(
					'/\s+/',
					' ',
					$content
				);
			}
			return $content;
		}

		/**
		 * URLに ? を含んでいない場合に末尾に / を追加する
		 */
		public static function ensureTrailingSlash( $url ) {
			// `?` を含んでいない場合
			if ( $url !== null && strpos( $url, '?' ) === false ) {
				// 末尾が `/` で終わっていない場合に追加
				if ( substr( $url, -1 ) !== '/' ) {
					$url .= '/';
				}
			}
			return $url;
		}

		/**
		 * Get post type from URL
		 *
		 * ヘッダーメニューのアクティブラベル用なので、トップメニューに入る項目として、
		 * 投稿トップ / カスタム投稿タイプのトップ の投稿タイプが検出できればよい
		 * （詳細ページの検出は不要）
		 *
		 * @param string $url : URL
		 * @return string : post type name
		 */
		public static function get_post_type_from_url( $url ) {
			$menu_url_post_type = '';

			// Check if the URL is the post top page //////////////////

			// 投稿トップのURLを取得
			$post_top_id  = get_option( 'page_for_posts' );
			$post_top_url = get_the_permalink( $post_top_id );

			// 投稿トップのURLと引数のURL（メニュー項目の参照URL）が一致する場合
			// ※ 片方のURLだけ末尾に / が入っていなかったりする場合があるので、ensureTrailingSlash() で調整
			if ( self::ensureTrailingSlash( $url ) === self::ensureTrailingSlash( $post_top_url ) ) {
				// 投稿トップのメニューアイテムの場合は投稿タイプは post
				return 'post';
			}

			// Other URLs ///////////////////////////////////////////////

			// Get rewrite rules
			$rewrite_rules = get_option( 'rewrite_rules' );

			if ( ! $rewrite_rules || ! is_array( $rewrite_rules ) ) {

				// In case of default permalink structure

				$pattern = '/.*post_type=(.*)/';
				$subject = $url;
				if ( $subject !== null ) {
					preg_match( $pattern, $subject, $matches );
				}

				// メニューの投稿タイプが取得できたら
				if ( isset( $matches[1] ) ) {
					$menu_url_post_type = $matches[1];
				} else {
					// home_url() . /?p=数字 の場合（ "ドメイン/" と "?p=" の間には index.php は不要）は、その数字をget_postに渡して投稿タイプを取得する
					$pattern = '/[?&]p=([^&]+)/';
					$subject = $url;
					if ( $subject !== null ) {
						preg_match( $pattern, $subject, $matches );
					}
					// マッチした場合
					if ( $matches ) {
						// 抽出した数字をget_postに渡して投稿タイプを取得する
						$post_id            = $matches[1];
						$post_type          = get_post_type( $post_id );
						$menu_url_post_type = $post_type;
					}
				}// if ( isset( $matches[1] ) ) {
			} else {

				// リライトルールが普通に保存されている場合

				// リライトルールをループ
				foreach ( $rewrite_rules as $key => $value ) {

					// メニューに記載されているURLから投稿タイプ名を判別する

					// ループ中のりライトルールがメニューのURLと合致するか正規表現で検出
					$pattern = '{' . $key . '}';
					$subject = $url;
					if ( $subject !== null ) {
						preg_match( $pattern, $subject, $matches );
					}

					// マッチした場合
					if ( $matches ) {

						// マッチした $value の URL （　index.php?post_type=custom　など ） から投稿タイプが判別できる
						// 正規表現で post_type= の値を抽出する

						$pattern = '/index.php\?post_type=(.*)/';
						$subject = $value;
						if ( $subject !== null ) {
							preg_match( $pattern, $subject, $matches );
						}

						// メニューの投稿タイプが取得できたら
						if ( isset( $matches[1] ) ) {
							$menu_url_post_type = $matches[1];
							// 最初にマッチしてクラスを付与したら抜ける
							break;
						} // if ( isset( $matches[1] ) ) {
					}
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

			// メニュー項目のリンク先のページの投稿タイプを取得
			$menu_url_post_type = self::get_post_type_from_url( $item_src );

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
