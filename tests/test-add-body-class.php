<?php
/**
 * Class AddBodyClassTest
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

/**
 * Test case for veu_add_body_class().
 * veu_add_body_class() のテストケース.
 */
class AddBodyClassTest extends WP_UnitTestCase {

	/**
	 * Test for veu_add_body_class().
	 * veu_add_body_class() のテスト.
	 *
	 * 主目的は、カテゴリー（タクソノミー）アーカイブのメインクエリに
	 * post_type を「配列」で指定し、かつ該当投稿が無い場合に、
	 * vk_get_post_type() が配列の slug を返してしまい
	 * 'post-type-' . $slug で PHP の "Array to string conversion" Warning が
	 * 発生する不具合を再発防止することです。
	 * .phpunit.xml で convertWarningsToExceptions="true" のため、
	 * Warning が発生すると例外に変換されてテストが失敗します。
	 *
	 * @return void
	 */
	public function test_veu_add_body_class() {

		// テスト用のカスタム投稿タイプ event を登録する.
		register_post_type(
			'event',
			array(
				'has_archive' => true,
				'public'      => true,
				'label'       => 'Event',
			)
		);

		// テスト用のカテゴリーを作成する.
		$category_id = self::factory()->category->create(
			array(
				'slug' => 'no-post-category',
				'name' => 'No Post Category',
			)
		);

		// テストの配列.
		$test_cases = array(
			array(
				'test_condition_name'   => 'カテゴリーアーカイブ + メインクエリ post_type が配列 + 該当投稿なし => Array to string conversion の Warning が出ず post-type クラスは付与される',
				// メインクエリに post_type を配列で指定した状況を再現する.
				'query_vars_post_type'  => array( 'event', 'page' ),
				'input_class'           => array( 'foo' ),
				// 配列の先頭要素 'event' が slug として採用され、文字列クラスになることを期待する.
				'expected_contains'     => array( 'foo', 'post-type-event' ),
				// クラスに 'post-type-Array' が混入していないこと（配列が文字列化されていないこと）.
				'expected_not_contains' => 'post-type-Array',
			),
			array(
				'test_condition_name'   => 'カテゴリーアーカイブ + メインクエリ post_type が単一文字列 + 該当投稿なし => その投稿タイプの post-type クラスが付与される',
				// 配列ではなく単一文字列を指定した正常系（従来どおりの挙動が維持されること）.
				'query_vars_post_type'  => 'event',
				'input_class'           => array( 'baz' ),
				'expected_contains'     => array( 'baz', 'post-type-event' ),
				'expected_not_contains' => 'post-type-Array',
			),
			array(
				'test_condition_name'   => 'カテゴリーアーカイブ + post_type 指定なし + 該当投稿なし => タクソノミーから解決した post-type クラスが付与される',
				'query_vars_post_type'  => null,
				'input_class'           => array( 'bar' ),
				// post_type 未指定時はタクソノミー category の object_type[0] = 'post' になる.
				'expected_contains'     => array( 'bar', 'post-type-post' ),
				'expected_not_contains' => 'post-type-Array',
			),
		);

		foreach ( $test_cases as $case ) {

			// カテゴリーアーカイブに移動する（is_archive() / is_category() を真にする）.
			// go_to() はグローバル $wp_query を新しいオブジェクトに差し替えるため、移動後に取得し直す.
			$this->go_to( get_category_link( $category_id ) );

			// 該当投稿が無い状況のため get_post_type() は false になる.
			// メインクエリの post_type を上書きして再現シナリオを作る（移動後の $wp_query を直接操作する）.
			if ( null !== $case['query_vars_post_type'] ) {
				$GLOBALS['wp_query']->query_vars['post_type'] = $case['query_vars_post_type'];
			}

			// テスト対象関数を実行（Warning が出れば convertWarningsToExceptions により失敗する）.
			$actual = veu_add_body_class( $case['input_class'] );

			// 期待するクラスが含まれていること.
			foreach ( $case['expected_contains'] as $expected_class ) {
				$this->assertContains( $expected_class, $actual, $case['test_condition_name'] );
			}

			// 配列が文字列化された 'post-type-Array' が含まれていないこと.
			$this->assertNotContains( $case['expected_not_contains'], $actual, $case['test_condition_name'] );
		}

		// 後始末.
		wp_delete_category( $category_id );
		unregister_post_type( 'event' );
	}
}
