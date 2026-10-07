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
	 * 関連記事の1件分HTMLに必要なアクセシビリティ属性と出力エスケープのテスト。
	 * Test the accessibility attributes and output escaping in the single related-post item HTML.
	 *
	 * @return void
	 */
	public function test_veu_add_related_posts_item_html() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attribute does not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		$original_permalink_structure = get_option( 'permalink_structure' );
		$special_character_title      = 'Related <Post> & "Test"';

		// 標準パーマリンクで URL に & を含む投稿を作れるよう、テスト用のカスタム投稿タイプを登録する。
		// Register a test post type whose default permalink contains an ampersand.
		register_post_type(
			'veu_related_test',
			array(
				'public'    => true,
				'query_var' => false,
				'rewrite'   => false,
			)
		);

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
			array(
				'test_condition_name' => '標準パーマリンクのカスタム投稿 => URLがHTMLエスケープされたリンクを出力する',
				'post'                => array(
					'post_title'  => 'Related Custom Post Test',
					'post_type'   => 'veu_related_test',
					'post_status' => 'publish',
				),
				'permalink_structure' => '',
				'url_contains'        => '&p=',
				'set_thumbnail'       => true,
				'expected_title'      => 'Related Custom Post Test',
				'expected_link_count' => 2,
			),
			array(
				'test_condition_name' => 'HTML特殊文字を含むタイトル => タイトルがHTMLエスケープされる',
				'post'                => array(
					'post_title'  => $special_character_title,
					'post_type'   => 'post',
					'post_status' => 'publish',
				),
				'output_post_title'   => $special_character_title,
				'set_thumbnail'       => false,
				'expected_title'      => esc_html( $special_character_title ),
				'expected_link_count' => 1,
			),
		);

		try {
			foreach ( $test_cases as $case ) {
				// ケースで指定したパーマリンク構造へ切り替え、WordPress の内部状態にも反映する。
				// Apply the permalink structure specified by the case to the WordPress rewrite state.
				$permalink_structure = array_key_exists( 'permalink_structure', $case ) ? $case['permalink_structure'] : $original_permalink_structure;
				update_option( 'permalink_structure', $permalink_structure );
				$GLOBALS['wp_rewrite']->init();

				// テスト用の投稿を作成 / Create a test post.
				$post_id       = wp_insert_post( $case['post'] );
				$attachment_id = 0;

				try {
					// 指定されたケースだけアイキャッチ画像を設定 / Set a featured image only for the specified case.
					if ( $case['set_thumbnail'] ) {
						$attachment_id = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $post_id );
						set_post_thumbnail( $post_id, $attachment_id );
					}

					// 投稿保存時のサニタイズと切り分け、関数へ渡すタイトルをケースどおりに設定する。
					// Set the title passed to the function independently of post-save sanitization.
					$post = get_post( $post_id );
					if ( isset( $case['output_post_title'] ) ) {
						$post->post_title = $case['output_post_title'];
					}

					// 関連記事1件分の HTML を取得 / Get the single related-post item HTML.
					$html = veu_add_related_posts_item_html( $post );

					// 日付前のカレンダーアイコンに aria-hidden="true" が付いている事を確認。
					// Check the calendar icon before the date has aria-hidden="true".
					$this->assertStringContainsString( '<i class="fa fa-calendar" aria-hidden="true"></i>', $html, $case['test_condition_name'] );

					// 投稿タイトルが出力に含まれる事を確認（ケースごとの差分）。
					// Check the post title is present in the output ( the per-case difference ).
					$this->assertStringContainsString( $case['expected_title'], $html, $case['test_condition_name'] );

					// URL は esc_url() の実際の出力を期待値に使い、同じ投稿へ移動するリンク数を確認する。
					// Use the actual esc_url() output to count links to the same post.
					$permalink          = get_the_permalink( $post_id );
					$expected_permalink = esc_url( $permalink );
					$link_prefix        = '<a href="' . $expected_permalink . '"';
					if ( isset( $case['url_contains'] ) ) {
						// URL のテストケースがエスケープ対象の & を実際に含む事を確認する。
						// Confirm that the URL test case actually contains an ampersand to escape.
						$this->assertStringContainsString( $case['url_contains'], $permalink, $case['test_condition_name'] );
						$this->assertNotSame( $permalink, $expected_permalink, $case['test_condition_name'] );
						$this->assertSame( $permalink, html_entity_decode( $expected_permalink, ENT_QUOTES, 'UTF-8' ), $case['test_condition_name'] );
					}
					$this->assertSame( $case['expected_link_count'], substr_count( $html, $link_prefix ), $case['test_condition_name'] );

					// タイトル側のリンクが URL とタイトルを各出力コンテキストでエスケープしている事を確認する。
					// Check that the title link escapes the URL and title for their output contexts.
					$expected_title_link = $link_prefix . '>' . $case['expected_title'] . '</a>';
					$this->assertStringContainsString( $expected_title_link, $html, $case['test_condition_name'] );

					if ( $case['set_thumbnail'] ) {
						// サムネイル側のリンクを支援技術とキーボード操作から外している事を確認。
						// Check that the thumbnail link is excluded from assistive technologies and keyboard navigation.
						$this->assertStringContainsString( $link_prefix . ' aria-hidden="true" tabindex="-1">', $html, $case['test_condition_name'] );
					}
				} finally {
					// 後片付け / Clean up.
					if ( $attachment_id ) {
						wp_delete_attachment( $attachment_id, true );
					}
					wp_delete_post( $post_id, true );
					update_option( 'permalink_structure', $original_permalink_structure );
					$GLOBALS['wp_rewrite']->init();
				}
			}
		} finally {
			update_option( 'permalink_structure', $original_permalink_structure );
			$GLOBALS['wp_rewrite']->init();
			unregister_post_type( 'veu_related_test' );
		}
	}
}
