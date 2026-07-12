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
	 * 各テスト後に、アイコンアクセシビリティのフィルターを元の登録内容で復元する。
	 * Restore the icon accessibility filters ( with the original arguments / priority ) after each test.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		add_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		add_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10, 2 );
		parent::tearDown();
	}

	/**
	 * 関連記事の日付前アイコン（fa-calendar）に aria-hidden="true" が付く事のテスト。
	 * Test that the calendar icon before the related-post date gets aria-hidden="true".
	 *
	 * @return void
	 */
	function test_veu_add_related_posts_item_html() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attribute does not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		// テスト条件（投稿データ）と期待する結果の組み合わせ。
		// カレンダーアイコンは投稿内容に依らず不変なので、投稿データを変えつつ全ケースで検証する。
		// Combinations of the post data and expected result.
		// The calendar icon is invariant regardless of post content, so we verify it across cases with varying post data.
		$test_cases = array(
			array(
				'test_condition_name' => '通常の投稿 => 日付前のカレンダーアイコンに aria-hidden が付く',
				'post'                => array(
					'post_title'  => 'Related Post Test A',
					'post_type'   => 'post',
					'post_status' => 'publish',
				),
				'expected_title'      => 'Related Post Test A',
			),
			array(
				'test_condition_name' => '別タイトル・別日付の投稿 => 日付前のカレンダーアイコンに aria-hidden が付く',
				'post'                => array(
					'post_title'  => 'Related Post Test B',
					'post_type'   => 'post',
					'post_status' => 'publish',
					'post_date'   => '2020-01-02 10:00:00',
				),
				'expected_title'      => 'Related Post Test B',
			),
		);

		foreach ( $test_cases as $case ) {
			// テスト用の投稿を作成 / Create a test post.
			$post_id = wp_insert_post( $case['post'] );

			// 関連記事1件分の HTML を取得 / Get the single related-post item HTML.
			$html = veu_add_related_posts_item_html( get_post( $post_id ) );

			// 日付前のカレンダーアイコンに aria-hidden="true" が付いている事を確認。
			// Check the calendar icon before the date has aria-hidden="true".
			$this->assertStringContainsString( '<i class="fa fa-calendar" aria-hidden="true"></i>', $html, $case['test_condition_name'] );

			// 投稿タイトルが出力に含まれる事を確認（ケースごとの差分）。
			// Check the post title is present in the output ( the per-case difference ).
			$this->assertStringContainsString( $case['expected_title'], $html, $case['test_condition_name'] );

			// 後片付け / Clean up.
			wp_delete_post( $post_id, true );
		}
	}
}
