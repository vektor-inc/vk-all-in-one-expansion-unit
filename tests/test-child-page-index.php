<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetChildPageIndexTest extends WP_UnitTestCase {

	function test_chlild_page_excerpt() {

		// 要件と期待する結果
		$test_array = array(
			// 値が空の場合（エラーなど吐かれないか？）
			array(
				'content' => '',
				'excerpt' => '',
				'correct' => '',
			),
			array(
				'content' => '<p>１２３４５６７８９０１２３４５６７８９０</p>',
				'excerpt' => '',
				'correct' => '１２３４５６７８９０１２３４５６７８９０',
			),
			// 抜粋の改行が効くかどうか
			array(
				'content' => '<p>１２３４５６７８９０１２３４５６７８９０</p>',
				'excerpt' => "改行\n改行",
				'correct' => "改行<br />\n改行",
			),
			// 抜粋にHTMLタグが入っている場合に除去されるか
			array(
				'content' => '<p>１２３４５６７８９０１２３４５６７８９０</p>',
				'excerpt' => "<p>１２３４５６７８９０１２３４５６７８９０</p>",
				'correct' => "１２３４５６７８９０１２３４５６７８９０",
			),
		);

		foreach ($test_array as $key => $value) {

			// テスト用のデータを投稿する
			$post_data['post_content'] = $value['content'];
			$post_data['post_excerpt'] = $value['excerpt'];
			// 投稿が成功すると投稿IDが返ってくる
			$post_id = wp_insert_post( $post_data );
			print '<pre style="text-align:left">';print_r($post_id);print '</pre>';
			print $key.PHP_EOL;
			print $post_id.PHP_EOL;

			// 実際に投稿されたデータを取得する
			$post = get_post( $post_id );

			// その投稿データの場合の子ページインデックスに表示する抜粋文を取得する
			$return = veu_child_page_excerpt( $post );

			// 返ってきた抜粋値と期待する結果が同じかどうかテスト
			// $this->assertEquals( $value['correct'], $return );

			print PHP_EOL;
			print '------------------------------------'.PHP_EOL;
			print 'test_chlild_page_excerpt'.PHP_EOL;
			print '------------------------------------'.PHP_EOL;
			// print $post->post_excerpt.PHP_EOL;
			print 'return  :'.$return.PHP_EOL;
			print 'correct :'.$value['correct'].PHP_EOL;
		}

	} // function test_image_outer_size_css() {
}
