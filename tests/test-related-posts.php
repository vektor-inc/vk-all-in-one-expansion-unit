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

		// テスト用の投稿を作成 / Create a test post.
		$post_id = wp_insert_post(
			array(
				'post_title'  => 'Related Post Test',
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);

		// 関連記事1件分の HTML を取得 / Get the single related-post item HTML.
		$html = veu_add_related_posts_item_html( get_post( $post_id ) );

		// 日付前のカレンダーアイコンに aria-hidden="true" が付いている事を確認。
		// Check the calendar icon before the date has aria-hidden="true".
		$this->assertStringContainsString( '<i class="fa fa-calendar" aria-hidden="true"></i>', $html );
	}
}
