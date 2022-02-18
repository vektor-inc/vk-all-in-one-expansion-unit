<?php
/**
 * Test for PHP Fatal Error
 *
 * 各ページで致命的なエラーがないか確認するためのテストです.
 *
 * @package VK WP Unit Test Tools
 */

require_once dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/autoload.php';

use VK_WP_Unit_Test_Tools\VkWpUnitTestHelpers;

/**
 * PHP Fatal Error
 */
class Test_PHP_Fatal_Error extends WP_UnitTestCase {

	/**
	 * Check Fatal Error
	 *
	 * @return void
	 */
	public function test_run_php_fatal_error() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'PHP Fatal Error Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		/******************************************
		 * テスト用の投稿を作成 */

		$test_posts = VkWpUnitTestHelpers::create_test_posts();

		/******************************************
		 * 確認するURLと設定の配列 */

		$test_array = array(

			array(
				'target_url' => home_url(),
			),

			// 404ページ
			array(
				'target_url' => home_url( '/?name=aaaaa' ),
			),

			// 検索結果（検索キーワードなし）.
			array(
				'target_url' => home_url( '/?s=' ),
			),

			// 検索結果（検索キーワード:aaa）.
			array(
				'target_url' => home_url( '/?s=aaa' ),
			),

			// 固定ページ
			// HOME > 固定ページ名.
			array(
				'target_url' => get_permalink( $test_posts['parent_page_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 固定ページ
			// トップに指定した固定ページ名 > 固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['parent_page_id'] ),
			),

			// 固定ページの子ページ
			// トップに指定した固定ページ名 > 親ページ > 子ページ.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['child_page_id'] ),
			),

			// トップページ未指定 / 投稿トップ未指定 / 固定ページの子ページ
			// HOME > 親ページ > 子ページ.
			array(
				'target_url' => get_permalink( $test_posts['child_page_id'] ),
			),

			// トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー
			// HOME > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_term_link( $test_posts['child_category_id'], 'category' ),
			),

			// トップページに最新の投稿 / 投稿トップページ無指定 / 投稿ページ
			// HOME > 親カテゴリー > 子カテゴリー > 記事タイトル.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_permalink( $test_posts['post_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// HOME > 投稿トップの固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['home_page_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_term_link( $test_posts['child_category_id'], 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 投稿のないカテゴリーアーカイブページ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 投稿のないカテゴリー名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_term_link( $test_posts['no_post_category_id'], 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > アーカイブ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => home_url() . '/?post_type=post&year=' . gmdate( 'Y' ),
			),

			// トップページに固定ページ / 投稿トップページ無指定 / 年別アーカイブ
			// HOME > アーカイブ名.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => home_url() . '/?post_type=post&year=' . gmdate( 'Y' ),
			),

			// カスタム投稿タイプトップ
			// HOME > 投稿タイプ名.
			array(
				'target_url' => home_url() . '/?post_type=event',
			),

			// カスタム投稿タイプ / カスタム分類アーカイブ
			// HOME > 投稿タイプ名 > カスタム分類.
			array(
				'target_url' => get_term_link( $test_posts['event_term_id'] ),
			),

			// カスタム投稿タイプ / 年別アーカイブ
			// HOME > 投稿タイプ名 > アーカイブ名.
			array(
				'target_url' => home_url() . '/?post_type=event&year=' . gmdate( 'Y' ),
			),

			// カスタム投稿タイプ / 記事詳細
			// HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル.
			array(
				'target_url' => get_permalink( $test_posts['event_post_id'] ),
			),
		);

		$test_array = apply_filters( 'php_fatal_error_test_array', $test_array );

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			print PHP_EOL;
			print '-------------------' . PHP_EOL;
			print esc_url( $value['target_url'] ) . PHP_EOL;
			print '-------------------' . PHP_EOL;
			print PHP_EOL;

			// Move to test page.
			$this->go_to( $value['target_url'] );

			// 実際にファイルを読み込む（ これをしないとFatal Errorを検出できない ）.
			require get_theme_file_path( 'index.php' );

			/**
			 * このテストはPHPのFatalエラーが発生するかどうかのチェックなので本当は不要なのだが、
			 *「OK, but incomplete, skipped, or risky tests!」をくらってしまうのでダミーで実行している.  */
			$this->assertEquals( true, true );

			// 設定したオプション値を削除してリセット.
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}
	}
}
