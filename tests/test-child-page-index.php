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
				'content' => '本文',
				'excerpt' => '本文と抜粋両方入力がある場合に抜粋が出力されるか',
				'correct' => '本文と抜粋両方入力がある場合に抜粋が出力されるか',
			),
			array(
				'content' => '<p>本文にHTMLタグが入っている場合に除去されるか</p>',
				'excerpt' => '',
				'correct' => '本文にHTMLタグが入っている場合に除去されるか',
			),
			array(
				'content' => '',
				'excerpt' => '<p>抜粋にHTMLタグが入っている場合に除去されるか</p>',
				'correct' => '抜粋にHTMLタグが入っている場合に除去されるか',
			),
			array(
				'content' => "本文の改行コードが\n除去されるかどうか",
				'excerpt' => '',
				'correct' => '本文の改行コードが除去されるかどうか',
			),
			array(
				'content' => '',
				'excerpt' => "抜粋では改行コード\nが brタグ に変更されるかどうか",
				'correct' => '抜粋では改行コード<br />が brタグ に変更されるかどうか',
			),
			// 本文欄 90 文字以上の時は ... を付与
			array(
				'content' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'excerpt' => '',
				'correct' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０...',
			),
			// 本文欄 90 文字丁度の時は ... はつかない
			array(
				'content' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'excerpt' => '',
				'correct' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
			),
			// 本文欄 90 文字以上の時は ... を付与
			array(
				'content' => '',
				'excerpt' => '抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない',
				'correct' => '抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない抜粋欄は文字が多くてもトリムしない',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_chlild_page_excerpt' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			// テスト用のデータを投稿する
			$post_data['post_content'] = $value['content'];
			$post_data['post_excerpt'] = $value['excerpt'];
			// 投稿が成功すると投稿IDが返ってくる
			$post_id = wp_insert_post( $post_data );

			// 実際に投稿されたデータを取得する
			$post = get_post( $post_id );

			// その投稿データの場合の子ページインデックスに表示する抜粋文を取得する
			$return = veu_child_page_excerpt( $post );

			// 返ってきた抜粋値と期待する結果が同じかどうかテスト
			$this->assertEquals( $value['correct'], $return );

			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
		}

	} // function test_chlild_page_excerpt() {
}
