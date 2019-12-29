<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetNewPostsTest extends WP_UnitTestCase {

	/**
	 * アイコンカラー出力CSSのテスト
	 */
	function test_WP_Widget_vkExUnit_post_list__more_link_html() {
		// テスト用の投稿を追加する

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_WP_Widget_vkExUnit_post_list__more_link_html' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			// URLも表記テキストも未定義の場合（既存ユーザー） → 何も出力しない
			array(
				'correct_more_link_html' => '',
			),
			// どちらも定義されている → リンクテキストを表示
			array(
				'more_url'               => 'https://vektor-inc.co.jp',
				'more_text'              => '一覧を見る ≫',
				'correct_more_link_html' => '<div class="postList_more"><a href="https://vektor-inc.co.jp">一覧を見る ≫</a></div>',
			),
			// 表記テキストは入力されている URLが入力されていない → 何も出力しない
			array(
				'more_url'               => '',
				'more_text'              => '一覧を見る ≫',
				'correct_more_link_html' => '',
			),
			// URLは入力されている 表記テキストは入力されていない → 何も出力しない
			array(
				'more_url'               => 'https://vektor-inc.co.jp',
				'more_text'              => '',
				'correct_more_link_html' => '',
			),
			// どちらも定義されていない
			array(
				'more_url'               => '',
				'more_text'              => '',
				'correct_more_link_html' => '',
			),
		);

		foreach ( $test_array as $key => $test_value ) {

			// 一覧へリンクのHTMLを取得
			$more_link_html = WP_Widget_vkExUnit_post_list::more_link_html( $test_value );

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $test_value['correct_more_link_html'], $more_link_html );

			print PHP_EOL;
			print 'correct_more_link_html :' . $test_value['correct_more_link_html'] . PHP_EOL;
			print 'more_link_html         :' . $more_link_html . PHP_EOL;
		}

		$this->assertTrue( true );
	}
}
