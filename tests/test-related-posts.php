<?php
/**
 * Class RelatedPostsTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * 関連記事の1件分HTML（veu_add_related_posts_item_html）のテスト。
 * Test for the single related-post item HTML ( veu_add_related_posts_item_html ).
 */
class RelatedPostsTest extends WP_UnitTestCase {

	/**
	 * 関連記事の1件分HTMLに必要なアクセシビリティ属性が付く事のテスト。
	 * Test the accessibility attributes in the single related-post item HTML.
	 *
	 * @return void
	 */
	function test_veu_add_related_posts_item_html() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attribute does not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		// テスト条件（投稿データ・アイキャッチ画像の有無）と期待する結果の組み合わせ。
		// Combinations of the post data and expected result.
		// Include posts both with and without a featured image.
		$test_cases = array(
			array(
				'test_condition_name' => 'アイキャッチ画像がない投稿 => サムネイル側のリンクが出力されずリンクはタイトルの1本だけ',
				'post'                => array(
					'post_title'  => 'Related Post Test A',
					'post_type'   => 'post',
					'post_status' => 'publish',
				),
				'set_thumbnail'       => false,
				'expected_title'      => 'Related Post Test A',
				'expected_link_count' => 1,
			),
			array(
				'test_condition_name' => '別タイトル・別日付でアイキャッチ画像がない投稿 => サムネイル側のリンクが出力されずリンクはタイトルの1本だけ',
				'post'                => array(
					'post_title'  => 'Related Post Test B',
					'post_type'   => 'post',
					'post_status' => 'publish',
					'post_date'   => '2020-01-02 10:00:00',
				),
				'set_thumbnail'       => false,
				'expected_title'      => 'Related Post Test B',
				'expected_link_count' => 1,
			),
			array(
				'test_condition_name' => 'アイキャッチ画像がある投稿 => サムネイル側のリンクに aria-hidden="true" と tabindex="-1" が付く',
				'post'                => array(
					'post_title'  => 'Related Post Test With Thumbnail',
					'post_type'   => 'post',
					'post_status' => 'publish',
				),
				'set_thumbnail'       => true,
				'expected_title'      => 'Related Post Test With Thumbnail',
				'expected_link_count' => 2,
			),
		);

		foreach ( $test_cases as $case ) {
			// テスト用の投稿を作成 / Create a test post.
			$post_id       = wp_insert_post( $case['post'] );
			$attachment_id = 0;

			// 指定されたケースだけアイキャッチ画像を設定 / Set a featured image only for the specified case.
			if ( $case['set_thumbnail'] ) {
				$attachment_id = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $post_id );
				set_post_thumbnail( $post_id, $attachment_id );
			}

			// 関連記事1件分の HTML を取得 / Get the single related-post item HTML.
			$html = veu_add_related_posts_item_html( get_post( $post_id ) );

			// 日付前のカレンダーアイコンに aria-hidden="true" が付いている事を確認。
			// Check the calendar icon before the date has aria-hidden="true".
			$this->assertStringContainsString( '<i class="fa fa-calendar" aria-hidden="true"></i>', $html, $case['test_condition_name'] );

			// 投稿タイトルが出力に含まれる事を確認（ケースごとの差分）。
			// Check the post title is present in the output ( the per-case difference ).
			$this->assertStringContainsString( $case['expected_title'], $html, $case['test_condition_name'] );

			// 同じ投稿へ移動するリンク数を確認し、アイキャッチ画像がない場合にサムネイル側のリンクが出ない事を検証する。
			// Check the number of links to the post so no thumbnail link is output without a featured image.
			$link_prefix = '<a href="' . get_the_permalink( $post_id ) . '"';
			$this->assertSame( $case['expected_link_count'], substr_count( $html, $link_prefix ), $case['test_condition_name'] );

			// タイトル側のリンクには属性を足していない（＝読み上げ・キーボード操作の対象として残っている）事を確認。
			// Check the title link keeps no extra attributes ( it remains available to screen readers and keyboard users ).
			$this->assertSame( 1, substr_count( $html, $link_prefix . '>' ), $case['test_condition_name'] );

			if ( $case['set_thumbnail'] ) {
				// サムネイル側のリンクを支援技術とキーボード操作から外している事を確認。
				// Check that the thumbnail link is excluded from assistive technologies and keyboard navigation.
				$this->assertStringContainsString( $link_prefix . ' aria-hidden="true" tabindex="-1">', $html, $case['test_condition_name'] );
			}

			// 後片付け / Clean up.
			if ( $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
			wp_delete_post( $post_id, true );
		}
	}
}
