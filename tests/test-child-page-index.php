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

	function test_childPageIndex_shortcode() {
		$parent_id = wp_insert_post(
			array(
				'post_title'   => 'Parent Page',
				'post_content' => 'parent page content',
				'post_excerpt' => 'parent page excerpt',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		$child_id = wp_insert_post(
			array(
				'post_title'   => 'Child Page',
				'post_content' => 'child page content',
				'post_excerpt' => 'child page excerpt',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_parent'  => $parent_id,
			)
		);

		$test_array = array(
			array(
				'class'    => 'abcde',
				'expected' => '<div class="veu_childPage_list abcde"><a href="http://localhost:8889/?page_id=' . $child_id . '" id="post-' . $child_id . '" class="childPage_list_box veu_card post-' . $child_id . ' page type-page status-publish hentry"><div class="childPage_list_box_inner veu_card_inner"><h3 class="childPage_list_title veu_card_title">Child Page</h3><div class="childPage_list_body"><p class="childPage_list_text">child page excerpt</p><span class="childPage_list_more btn btn-primary btn-sm">Read more</span></div></div></a></div><!-- [ /.veu_childPage_list ] -->',
			),
			array(
				'class'    => 'abcde" onmouseover="alert(123)"',
				'expected' => '<div class="veu_childPage_list abcde&quot; onmouseover=&quot;alert(123)&quot;"><a href="http://localhost:8889/?page_id=' . $child_id . '" id="post-' . $child_id . '" class="childPage_list_box veu_card post-' . $child_id . ' page type-page status-publish hentry"><div class="childPage_list_box_inner veu_card_inner"><h3 class="childPage_list_title veu_card_title">Child Page</h3><div class="childPage_list_body"><p class="childPage_list_text">child page excerpt</p><span class="childPage_list_more btn btn-primary btn-sm">Read more</span></div></div></a></div><!-- [ /.veu_childPage_list ] -->',
			),
			array(
				'parrent_id'    => array(),
				'class'    => '',
				'expected' => '<div class="veu_childPage_list "><a href="http://localhost:8889/?page_id=' . $child_id . '" id="post-' . $child_id . '" class="childPage_list_box veu_card post-' . $child_id . ' page type-page status-publish hentry"><div class="childPage_list_box_inner veu_card_inner"><h3 class="childPage_list_title veu_card_title">Child Page</h3><div class="childPage_list_body"><p class="childPage_list_text">child page excerpt</p><span class="childPage_list_more btn btn-primary btn-sm">Read more</span></div></div></a></div><!-- [ /.veu_childPage_list ] -->',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'Child Page Index' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {
			$test_parent_id = $parent_id;

			if ( isset( $value['parrent_id'] ) ) {
				// parrent_id が渡されなかった場合用のテスト ///////////////////////////////////////
				// $this->go_to で $post がうまくセットできなかったため、
				// global $post; をセットするために一旦 get_post で現在のページ（親ページ）のデータを取得する
				$parent_post = get_post( $parent_id );
				global $post;
				$post = $parent_post;
				// テスト用に作った投稿の親ページのIDではなく、テスト対象の親ページのID（空の場合など）を代入する
				$test_parent_id = $value['parrent_id'];
			}

			$return = vkExUnit_childPageIndex_shortcode( $test_parent_id, $value['class'] );
			// delete before after space
			$return = trim( $return );
			// convert tab and br to space
			$return = preg_replace( '/[\n\r\t]/', '', $return );
			// Change multiple spaces to single space
			$return = preg_replace( '/\s(?=\s)/', '', $return );
			$expected = $value['expected'];

			print  PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $expected  . PHP_EOL;

			// 返ってきた抜粋値と期待する結果が同じかどうかテスト
			$this->assertEquals( $expected , $return );
			wp_reset_postdata();
		}

	}
}
