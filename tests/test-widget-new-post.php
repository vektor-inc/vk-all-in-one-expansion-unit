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
	function test_link() {
		// テスト用の投稿を追加する

		$test_array = array(
			// 既存の人
			array(
				'correct_more_link_html' => '',
			),
			// どちらも定義されている
			array(
				'more_url' => 'https://github.com/vektor-inc/VK-All-in-One-Expansion-Unit/',
				'more_text' => '一覧を見る ≫',
				'correct_more_link_html' => '<div class="postList_more"><a href="https://github.com/vektor-inc/VK-All-in-One-Expansion-Unit/">一覧を見る ≫</a></div>',
			),
			// URLが定義されていない
			array(
				'more_url' => '',
				'more_text' => '一覧を見る ≫',
				'correct_more_link_html' => '',
			),
			// 表記するテキストが定義されていない
			array(
				'more_url' => 'https://github.com/vektor-inc/VK-All-in-One-Expansion-Unit/',
				'more_text' => '',
				'correct_more_link_html' => '',
			),
			// どちらも定義されていない
			array(
				'more_url' => '',
				'more_text' => '',
				'correct_more_link_html' => '',
			),
		);

		foreach ( $test_array as $key => $test_value) {

			// 外枠に付与するCSSを取得
			$more_link_html = WP_Widget_vkExUnit_post_list::more_link_html( $test_value );

			// 取得できたCSSと、想定する正しいCSSが等しいかテスト
			$this->assertEquals( $test_value['correct_more_link_html'], $more_link_html );

			print PHP_EOL;
			print 'correct_more_link_html :'.$test_value['correct_more_link_html'].PHP_EOL;
			print 'more_link_html         :'.$more_link_html.PHP_EOL;
		}

		$this->assertTrue( true );
	}
}
