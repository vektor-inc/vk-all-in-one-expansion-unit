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
				// 実装と同じ esc_html() に頼らず、期待値をリテラルで直書きする。
				// Write the expected value as a literal instead of depending on the same esc_html() used by the implementation.
				'expected_title'      => 'Related &lt;Post&gt; &amp; &quot;Test&quot;',
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

	/**
	 * 関連記事セクションの見出し（veu_add_related_posts_html）が出力時に無害化される事のテスト。
	 * Test that the related-posts section heading output by veu_add_related_posts_html() is sanitized on output.
	 *
	 * @return void
	 */
	public function test_veu_add_related_posts_html() {
		// ウィジェット経由の出力ではない事を明示し、未定義変数の警告を避ける（test-page-list-ancestor.php と同じ対処）。
		// Explicitly mark this as not a widget-triggered output to avoid an undefined-variable warning ( same workaround as test-page-list-ancestor.php ).
		global $is_pagewidget;
		$is_pagewidget = false;

		// 関連記事が見つかるよう、同じタグを共有する投稿を2件用意する。
		// Create two posts sharing the same tag so a related post can always be found.
		$current_post_id = wp_insert_post(
			array(
				'post_title'  => 'Related Html Test Current',
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);
		$related_post_id = wp_insert_post(
			array(
				'post_title'  => 'Related Html Test Related',
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);
		wp_set_post_tags( $current_post_id, array( 'veu-related-html-test' ) );
		wp_set_post_tags( $related_post_id, array( 'veu-related-html-test' ) );

		// テスト条件（見出しフィルターが返す値）と期待する結果の組み合わせ。
		// Combinations of the value returned by the heading filter and the expected result.
		$test_cases = array(
			array(
				// wp_kses_post() は許可されないタグ（script）そのものは除去するが、タグ内のテキストノードは残す
				// （スクリプト本文の実行可否はブラウザの話であり、HTMLサニタイズの責務ではないため）。
				// wp_kses_post() strips a disallowed tag ( script ) itself, but keeps the text node inside it
				// ( whether the script body executes is a browser concern, not something HTML sanitization is responsible for ).
				'test_condition_name' => '見出しに許可タグとscriptタグが混在 => scriptタグは落ちるが許可タグとタグ内テキストは残る',
				'filtered_title'      => '<span>OK</span><script>x</script>',
				'expected_heading'    => '<h1 class="mainSection-title relatedPosts_title"><span>OK</span>x</h1>',
				'unexpected'          => '<script>',
			),
			array(
				'test_condition_name' => '見出しが装飾タグのないプレーンテキスト => そのまま出力される',
				'filtered_title'      => 'Plain Related Title',
				'expected_heading'    => '<h1 class="mainSection-title relatedPosts_title">Plain Related Title</h1>',
				'unexpected'          => '<script>',
			),
			array(
				'test_condition_name' => '見出しにイベント属性付きタグと許可されないタグが混在 => イベント属性とタグは落ち、テキストだけ残る（境界値）',
				'filtered_title'      => '<img src="x" onerror="alert(1)">Broken<iframe src="javascript:alert(1)"></iframe>',
				'expected_heading'    => 'Broken',
				'unexpected'          => 'onerror',
			),
		);

		try {
			foreach ( $test_cases as $case ) {
				$filter_heading = function () use ( $case ) {
					return $case['filtered_title'];
				};
				add_filter( 'veu_related_post_title', $filter_heading );

				try {
					// 現在の投稿の単一ページへ移動し、is_single() 判定とグローバル $post を実際の表示と同じ状態にする。
					// Go to the current post's single page so is_single() and the global $post match a real front-end view.
					$this->go_to( get_permalink( $current_post_id ) );
					global $wp_query;
					$wp_query->the_post();

					$html = veu_add_related_posts_html( '' );

					$this->assertStringContainsString( $case['expected_heading'], $html, $case['test_condition_name'] );
					$this->assertStringNotContainsString( $case['unexpected'], $html, $case['test_condition_name'] );
				} finally {
					// アサーション失敗時もフィルターを確実に外し、後続のテストケース・他テストへ見出しの書き換えが波及しないようにする。
					// Ensure the filter is removed even when an assertion fails, so the rewritten heading never leaks into later test cases or other tests.
					remove_filter( 'veu_related_post_title', $filter_heading );
				}
			}
		} finally {
			wp_delete_post( $current_post_id, true );
			wp_delete_post( $related_post_id, true );
			wp_reset_query();
		}
	}
}
