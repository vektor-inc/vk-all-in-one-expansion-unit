<?php
/**
 * Class TemplateTagsTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
phpunit
 */


class TemplateTagsTest extends WP_UnitTestCase {

	/**
	 * vk_sanitize_array() は配列を wp_kses_post でサニタイズし、配列以外は空配列を返す。
	 * （配列以外を渡した際に未定義変数 $return の Notice を出さない事を含めて検証）
	 */
	public function test_vk_sanitize_array() {
		$test_cases = array(
			array(
				'test_condition_name' => '文字列要素の連想配列 => キー構造を保ったまま返す（正常系）',
				'input'               => array(
					'k1' => 'plain text',
					'k2' => 'safe value',
				),
				'expected'            => array(
					'k1' => 'plain text',
					'k2' => 'safe value',
				),
			),
			array(
				'test_condition_name' => '許可タグを含む配列 => 許可タグは保持される（正常系）',
				'input'               => array(
					'html' => '<strong>bold</strong>',
				),
				'expected'            => array(
					'html' => '<strong>bold</strong>',
				),
			),
			array(
				'test_condition_name' => '配列でない文字列 => 空配列を返す（未定義変数 Notice を出さない・境界値）',
				'input'               => 'not an array',
				'expected'            => array(),
			),
			array(
				'test_condition_name' => 'null => 空配列を返す（未定義変数 Notice を出さない・異常系）',
				'input'               => null,
				'expected'            => array(),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = vk_sanitize_array( $case['input'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * vk_get_post_type() は $_SERVER['REQUEST_URI'] が未設定（WP-CLI / cron 相当）でも
	 * Undefined array key / strpos(null) を出さず、slug を含む配列を返す。
	 */
	public function test_vk_get_post_type() {
		// フロントページに移動して $wp_query を用意する。
		$this->go_to( home_url( '/' ) );

		$original_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;

		$test_cases = array(
			array(
				'test_condition_name' => 'REQUEST_URI が通常どおり設定されている場合 => slug を含む配列を返す（正常系）',
				'request_uri'         => '/',
				'unset_request_uri'   => false,
			),
			array(
				'test_condition_name' => '任意のパスが REQUEST_URI に設定されている場合 => slug を含む配列を返す（正常系）',
				'request_uri'         => '/sample-page/',
				'unset_request_uri'   => false,
			),
			array(
				'test_condition_name' => 'REQUEST_URI が未設定（WP-CLI / cron 相当）=> Undefined array key / strpos(null) を出さず slug を含む配列を返す（境界値）',
				'request_uri'         => null,
				'unset_request_uri'   => true,
			),
		);

		try {
			foreach ( $test_cases as $case ) {
				if ( $case['unset_request_uri'] ) {
					unset( $_SERVER['REQUEST_URI'] );
				} else {
					$_SERVER['REQUEST_URI'] = $case['request_uri'];
				}

				$actual = vk_get_post_type();

				$this->assertIsArray( $actual, $case['test_condition_name'] );
				$this->assertArrayHasKey( 'slug', $actual, $case['test_condition_name'] );
				$this->assertIsString( $actual['slug'], $case['test_condition_name'] );
			}
		} finally {
			// アサーション失敗（例外）時も含め、REQUEST_URI を必ず復元して後続テストへの影響を防ぐ。
			// Always restore REQUEST_URI (even when an assertion throws) so later tests stay isolated.
			if ( null !== $original_request_uri ) {
				$_SERVER['REQUEST_URI'] = $original_request_uri;
			} else {
				unset( $_SERVER['REQUEST_URI'] );
			}
		}
	}

	/**
	 * vk_get_post_type() の管理画面での分岐 — 投稿の情報がまだ無い新規作成画面
	 * （例: post-new.php?post_type=xxx）では、URL のクエリ（$_GET['post_type']）から
	 * 投稿タイプを拾う。
	 * On the admin "new post" screen (e.g. post-new.php?post_type=xxx), where no post data
	 * exists yet, vk_get_post_type() falls back to reading the post type from the
	 * $_GET['post_type'] query var.
	 */
	public function test_vk_get_post_type_admin_new_post_screen() {
		global $post;

		$original_post        = $post;
		$original_get         = $_GET;
		$original_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;

		$test_cases = array(
			array(
				'test_condition_name' => '新規固定ページ作成画面（post-new.php?post_type=page）=> slug は page（正常系）',
				'query_post_type'     => 'page',
				'expected_slug'       => 'page',
			),
			array(
				'test_condition_name' => '新規投稿作成画面（post-new.php?post_type=post）=> slug は post（正常系）',
				'query_post_type'     => 'post',
				'expected_slug'       => 'post',
			),
			array(
				'test_condition_name' => '管理画面だが投稿情報も post_type も post の GET パラメータも無い場合 => slug は false のまま（境界値）',
				'query_post_type'     => null,
				'expected_slug'       => false,
			),
		);

		try {
			foreach ( $test_cases as $case ) {
				// 投稿データがまだ無い新規作成画面を再現するため $post と $_GET をクリアする.
				// Reproduce the "no post data yet" new-post screen by clearing $post and $_GET.
				$post = null;
				$_GET = array();
				if ( null !== $case['query_post_type'] ) {
					$_GET['post_type'] = $case['query_post_type'];
				}
				// is_admin() は PHPUnit 環境下では効かないことがあるため、
				// package/template-tags.php 同様 REQUEST_URI に 'wp-admin' を含めて分岐させる.
				// is_admin() is not reliable under PHPUnit, so include 'wp-admin' in
				// REQUEST_URI to enter the admin branch, matching package/template-tags.php.
				$_SERVER['REQUEST_URI'] = '/wp-admin/post-new.php';

				$actual = vk_get_post_type();

				$this->assertIsArray( $actual, $case['test_condition_name'] );
				$this->assertArrayHasKey( 'slug', $actual, $case['test_condition_name'] );
				$this->assertSame( $case['expected_slug'], $actual['slug'], $case['test_condition_name'] );
			}
		} finally {
			// アサーション失敗（例外）時も含め、グローバル状態を必ず復元して後続テストへの影響を防ぐ。
			// Always restore global state (even when an assertion throws) so later tests stay isolated.
			$post = $original_post;
			$_GET = $original_get;
			if ( null !== $original_request_uri ) {
				$_SERVER['REQUEST_URI'] = $original_request_uri;
			} else {
				unset( $_SERVER['REQUEST_URI'] );
			}
		}
	}

	/**
	 * メインクエリの投稿タイプが配列で渡された場合の正規化（#1375）。
	 * pre_get_posts 等でメインクエリに post_type を配列で set した場合でも、
	 * vk_get_post_type() は "Array to string conversion" Warning を出さず、
	 * 配列の先頭要素を文字列 slug として返す。
	 * Normalization when the main query's post type is passed as an array (#1375). Even when
	 * post_type is set to an array on the main query (e.g. via pre_get_posts), vk_get_post_type()
	 * must not trigger an "Array to string conversion" warning and must return the array's first
	 * element as a string slug.
	 */
	public function test_vk_get_post_type_main_query_post_type_array_normalization() {

		// メインクエリが投稿を1件もヒットさせない投稿タイプ（未使用のカスタム投稿タイプ）を用意する。
		// get_post_type() は $GLOBALS['post']（=クエリが実際にヒットした先頭の投稿）を見るため、
		// 何かヒットしてしまうと $wp_query->query_vars['post_type'] を見る分岐まで到達できない。
		// Register post types with zero posts so the main query matches nothing. get_post_type()
		// (no args) reads $GLOBALS['post'] (the first post the query actually matched); if
		// anything matches, execution never reaches the branch that reads
		// $wp_query->query_vars['post_type'], which is the branch under test here.
		register_post_type(
			'veu_1375_type_a',
			array(
				'public' => true,
				'label'  => 'VEU 1375 Type A',
			)
		);
		register_post_type(
			'veu_1375_type_b',
			array(
				'public' => true,
				'label'  => 'VEU 1375 Type B',
			)
		);
		register_post_type(
			'veu_1375_type_c',
			array(
				'public' => true,
				'label'  => 'VEU 1375 Type C',
			)
		);

		$test_cases = array(
			array(
				'test_condition_name' => 'post_type が array( "veu_1375_type_a", "veu_1375_type_b" ) の場合 => 先頭要素に正規化される（#1375, 正常系）',
				'post_type'           => array( 'veu_1375_type_a', 'veu_1375_type_b' ),
				'expected_slug'       => 'veu_1375_type_a',
			),
			array(
				'test_condition_name' => 'post_type が array( "veu_1375_type_b", "veu_1375_type_a" ) の場合 => 先頭要素に正規化される（#1375, 正常系）',
				'post_type'           => array( 'veu_1375_type_b', 'veu_1375_type_a' ),
				'expected_slug'       => 'veu_1375_type_b',
			),
			array(
				'test_condition_name' => 'post_type が要素1件の配列 array( "veu_1375_type_c" ) の場合 => その要素に正規化される（#1375, 境界値）',
				'post_type'           => array( 'veu_1375_type_c' ),
				'expected_slug'       => 'veu_1375_type_c',
			),
		);

		foreach ( $test_cases as $case ) {
			// pre_get_posts でメインクエリの post_type を配列で set し、
			// #1375 が対象としていた「pre_get_posts で配列を set するケース」を再現する.
			// Use pre_get_posts to set the main query's post_type to an array, reproducing the
			// "post_type set as an array via pre_get_posts" scenario that #1375 targets.
			$post_type_override = $case['post_type'];
			$set_post_type      = function ( $query ) use ( $post_type_override ) {
				if ( $query->is_main_query() ) {
					$query->set( 'post_type', $post_type_override );
				}
			};
			add_filter( 'pre_get_posts', $set_post_type );
			$this->go_to( home_url( '/' ) );
			remove_filter( 'pre_get_posts', $set_post_type );

			$actual = vk_get_post_type();

			$this->assertIsArray( $actual, $case['test_condition_name'] );
			$this->assertIsString( $actual['slug'], $case['test_condition_name'] . ' / slug は配列ではなく文字列に正規化されていること' );
			$this->assertSame( $case['expected_slug'], $actual['slug'], $case['test_condition_name'] );
		}
	}

	public static function setup_data() {

		/**
		 * カスタム投稿タイプを設置
		 */
		register_post_type(
			'event',
			array(
				'has_archive' => true,
				'public'      => true,
				'label'       => 'Event',
			)
		);

		/**
		 * カスタム投稿タイプにカスタム分類を関連付け
		 */
		register_taxonomy(
			'genre',
			'event',
			array(
				'label'        => 'Genre',
				'rewrite'      => array( 'slug' => 'genre' ),
				'hierarchical' => true,
			)
		);

		/**
		 * Test Category 01 を作成
		 */
		$catarr             = array(
			'cat_name'             => 'test_category_01',
			'category_description' => 'test_category_01',
			'category_nicename'    => 'Test Category 01',
		);
		$data['cate_id_01'] = wp_insert_category( $catarr );

		/**
		 * Test Category 02 を作成
		 */
		$catarr             = array(
			'cat_name'          => 'test_category_02',
			'category_nicename' => 'Test Category 02',
		);
		$data['cate_id_02'] = wp_insert_category( $catarr );

		/**
		 * Test Tag 01 を作成
		 */
		$args              = array(
			'slug'        => 'test_tag_01',
			'description' => 'test_tag_01',
		);
		$term_info         = wp_insert_term( 'test_tag_01', 'post_tag', $args );
		$data['tag_id_01'] = $term_info['term_id'];

		/**
		 * Test Tag 02 を作成
		 */
		$args              = array(
			'slug' => 'test_tag_02',
		);
		$term_info         = wp_insert_term( 'test_tag_02', 'post_tag', $args );
		$data['tag_id_02'] = $term_info['term_id'];

		/**
		 * Test Genre 01 を作成
		 */
		$args                = array(
			'slug'        => 'test_genre_01',
			'description' => 'test_genre_01',
		);
		$term_info           = wp_insert_term( 'test_genre_01', 'genre', $args );
		$data['genre_id_01'] = $term_info['term_id'];

		/**
		 * Test Genre 02 を作成
		 */
		$args                = array(
			'slug' => 'test_genre_02',
		);
		$term_info           = wp_insert_term( 'test_genre_02', 'genre', $args );
		$data['genre_id_02'] = $term_info['term_id'];

		/**
		 * Front Page 01 を作成
		 */
		$post                     = array(
			'post_name'     => 'front-page',
			'post_title'    => 'front-page',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'front-page-content',
			'post_excerpt'  => 'front-page-excerpt',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['front_page_id_01'] = wp_insert_post( $post );

		/**
		 * Front Page 02 を作成
		 */
		$post                     = array(
			'post_name'     => 'front-page',
			'post_title'    => 'front-page',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'front-page-content',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['front_page_id_02'] = wp_insert_post( $post );

		/**
		 * Home Page 01 を作成
		 */
		$post                    = array(
			'post_name'     => 'blog',
			'post_title'    => 'Blog',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'blog-content',
			'post_excerpt'  => 'blog-excerpt',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['home_page_id_01'] = wp_insert_post( $post );

		/**
		 * Home Page 02 を作成
		 */
		$post                    = array(
			'post_name'     => 'blog',
			'post_title'    => 'Blog',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'blog-content',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['home_page_id_02'] = wp_insert_post( $post );

		/**
		 * Test Post 01 を作成
		 */
		$post               = array(
			'post_name'     => 'test-post',
			'post_title'    => 'test-post',
			'post_status'   => 'publish',
			'post_content'  => 'test-post-content',
			'post_excerpt'  => 'test-post-excerpt',
			'post_category' => array( $data['cate_id_01'], $data['cate_id_02'] ),
			'post_date'     => '2021-11-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['post_id_01'] = wp_insert_post( $post );
		wp_set_object_terms( $data['post_id_01'], array( $data['tag_id_01'], $data['tag_id_02'] ), 'post_tag' );

		/**
		 * Test Post 02 を作成
		 */
		$post               = array(
			'post_name'     => 'test-post',
			'post_title'    => 'test-post',
			'post_status'   => 'publish',
			'post_content'  => 'test-post-content',
			'post_category' => array( $data['cate_id_01'], $data['cate_id_02'] ),
			'post_date'     => '2021-11-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['post_id_02'] = wp_insert_post( $post );
		wp_set_object_terms( $data['post_id_02'], array( $data['tag_id_01'], $data['tag_id_02'] ), 'post_tag' );

		/**
		 * Test Post 03 を作成
		 */
		$post               = array(
			'post_name'     => 'test-post',
			'post_title'    => 'test-post',
			'post_status'   => 'publish',
			'post_content'  => 'test-post-content',
			'post_category' => array( $data['cate_id_01'], $data['cate_id_02'] ),
			'post_password' => 'test-password',
			'post_date'     => '2021-11-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['post_id_03'] = wp_insert_post( $post );
		wp_set_object_terms( $data['post_id_03'], array( $data['tag_id_01'], $data['tag_id_02'] ), 'post_tag' );

		/**
		 * Test Page 01 を作成
		 */
		$post               = array(
			'post_name'     => 'test-page',
			'post_title'    => 'test-page',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'test-page-content',
			'post_excerpt'  => 'test-page-excerpt',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['page_id_01'] = wp_insert_post( $post );

		/**
		 * Test Page 02 を作成
		 */
		$post               = array(
			'post_name'     => 'test-page',
			'post_title'    => 'test-page',
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'test-page-content',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['page_id_02'] = wp_insert_post( $post );

		/**
		 * Test Page 03 を作成
		 */
		$post               = array(
			'post_name'     => 'test-page',
			'post_title'    => 'test-page',
			'post_type'     => 'page',
			'post_password' => 'test-password',
			'post_status'   => 'publish',
			'post_content'  => 'test-page-content',
			'post_date'     => '2020-07-01 00:00:00',
			'post_modified' => '2022-01-01 00:00:00',
		);
		$data['page_id_03'] = wp_insert_post( $post );

		/**
		 * Test Event 01 を作成.
		 */
		$post                = array(
			'post_name'     => 'test-event',
			'post_title'    => 'test-event',
			'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_content'  => 'test-event-content',
			'post_excerpt'  => 'test-event-excerpt',
			'post_date'     => '2021-12-01 00:00:00',
			'post_modified' => '2021-11-01 12:00:00',
		);
		$data['event_id_01'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_01'], array( $data['genre_id_01'], $data['genre_id_02'] ), 'genre' );

		/**
		 * Test Event 02 を作成.
		 */
		$post                = array(
			'post_name'     => 'test-event',
			'post_title'    => 'test-event',
			'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_content'  => 'test-event-content',
			'post_date'     => '2021-12-01 00:00:00',
			'post_modified' => '2021-11-01 12:00:00',
		);
		$data['event_id_02'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_02'], array( $data['genre_id_01'], $data['genre_id_02'] ), 'genre' );

		/**
		 * Test Event 02 を作成.
		 */
		$post                = array(
			'post_name'     => 'test-event',
			'post_title'    => 'test-event',
			'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_password' => 'test-password',
			'post_content'  => 'test-event-content',
			'post_date'     => '2021-12-01 00:00:00',
			'post_modified' => '2021-11-01 12:00:00',
		);
		$data['event_id_03'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_03'], array( $data['genre_id_01'], $data['genre_id_02'] ), 'genre' );

		update_option( 'blogname', 'PHP Unit Test' ); // 抜粋
		update_option( 'blogdescription', 'This test is checker for PHP.' ); // 抜粋

		return $data;
	}

	public function test_vk_the_post_type_check_list_saved_array_convert() {

		$tests = array(
			array(
				'option'  => array(
					'post' => true,
					'info' => '',
				),
				'correct' => array( 'post' ),
			),
			array(
				'option'  => array(
					'post' => true,
					'info' => true,
				),
				'correct' => array( 'post', 'info' ),
			),
			array(
				'option'  => array(
					'post' => 'true',
					'info' => true,
				),
				'correct' => array( 'post', 'info' ),
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_the_post_type_check_list_saved_array_convert' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_Ads', $test_value['option'] );

			$return = vk_the_post_type_check_list_saved_array_convert( $test_value['option'] );

			// PHPunit
			$this->assertEquals( $test_value['correct'], $return );
			print PHP_EOL;
			// 帰り値が配列だから print してもエラーになるだけなのでコメントアウト
			// print 'return    :' . $return. PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
		}
	}

	/**
	 * 抜粋のテスト
	 */
	public function test_vk_get_page_description() {
		$data = self::setup_data();
		print PHP_EOL;
		print '---------------------------------------' . PHP_EOL;
		print ' VK Get Page Description Test' . PHP_EOL;
		print '---------------------------------------' . PHP_EOL;
		$test_array = array(
			array(
				'test_name'  => 'Home Page',
				'target_url' => home_url( '/' ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Front Page Description',
				'target_url' => home_url( '/' ),
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => $data['front_page_id_01'],
					'page_for_posts' => $data['home_page_id_01'],
				),
				'correct'    => 'front-page-excerpt',
			),
			array(
				'test_name'  => 'Front Page no Description',
				'target_url' => home_url( '/' ),
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => $data['front_page_id_02'],
					'page_for_posts' => $data['home_page_id_02'],
				),
				'correct'    => 'This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Page for Posts Description',
				'target_url' => get_permalink( $data['home_page_id_01'] ),
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => $data['front_page_id_01'],
					'page_for_posts' => $data['home_page_id_01'],
				),
				'correct'    => 'blog-excerpt',
			),

			// https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1076
			array(
				'test_name'  => 'Page for Posts Description( PHP Error test )',
				'target_url' => home_url() . '/?author=0',
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => $data['front_page_id_01'],
					'page_for_posts' => $data['home_page_id_01'],
				),
				'correct'    => 'blog-excerpt',
			),

			array(
				'test_name'  => 'Page for Posts no Description',
				'target_url' => get_permalink( $data['home_page_id_02'] ),
				'options'    => array(
					'show_on_front'  => 'page',
					'page_on_front'  => $data['front_page_id_02'],
					'page_for_posts' => $data['home_page_id_02'],
				),
				'correct'    => 'Article of Blog. PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Event Archive',
				'target_url' => get_post_type_archive_link( 'event' ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'Article of Event. PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Category Archive Description',
				'target_url' => get_term_link( $data['cate_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test_category_01',
			),
			array(
				'test_name'  => 'Category Archive no Description',
				'target_url' => get_term_link( $data['cate_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'About test_category_02 PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Tag Archive Description',
				'target_url' => get_term_link( $data['tag_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test_tag_01',
			),
			array(
				'test_name'  => 'Tag Archive no Description',
				'target_url' => get_term_link( $data['tag_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'About test_tag_02 PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Genre Archive Description',
				'target_url' => get_term_link( $data['genre_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test_genre_01',
			),
			array(
				'test_name'  => 'Genre Archive no Description',
				'target_url' => get_term_link( $data['genre_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'About test_genre_02 PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Yearly Archive',
				'target_url' => home_url( '/' ) . '?year=2021',
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'Article of 2021. PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Monthly Archive',
				'target_url' => home_url( '/' ) . '?m=202111',
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'Article of November 2021. PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Dayly Archive',
				'target_url' => home_url( '/' ) . '?d=20211101',
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Author Archive',
				'target_url' => get_author_posts_url( 1 ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'Article of admin. PHP Unit Test This test is checker for PHP.',
			),
			array(
				'test_name'  => 'Page Description',
				'target_url' => get_permalink( $data['page_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-page-excerpt',
			),
			array(
				'test_name'  => 'Page no Description',
				'target_url' => get_permalink( $data['page_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-page-content',
			),
			array(
				'test_name'  => 'Page has Password',
				'target_url' => get_permalink( $data['page_id_03'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'This article is protected by a password.',
			),
			array(
				'test_name'  => 'Post Description',
				'target_url' => get_permalink( $data['post_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-post-excerpt',
			),
			array(
				'test_name'  => 'Post no Description',
				'target_url' => get_permalink( $data['post_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-post-content',
			),
			array(
				'test_name'  => 'Post has Password',
				'target_url' => get_permalink( $data['post_id_03'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'This article is protected by a password.',
			),
			array(
				'test_name'  => 'Event Description',
				'target_url' => get_permalink( $data['event_id_01'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-event-excerpt',
			),
			array(
				'test_name'  => 'Event no Description',
				'target_url' => get_permalink( $data['event_id_02'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'test-event-content',
			),
			array(
				'test_name'  => 'Event has Password',
				'target_url' => get_permalink( $data['event_id_03'] ),
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => 'This article is protected by a password.',
			),
			array(
				'test_name'  => 'Search Result',
				'target_url' => home_url( '/' ) . '?s=test',
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => '',
			),
			array(
				'test_name'  => '404',
				'target_url' => home_url( '/' ) . '?s=aaa',
				'options'    => array(
					'show_on_front' => 'posts',
				),
				'correct'    => '',
			),
		);

		foreach ( $test_array as $test ) {
			foreach ( $test['options'] as $key => $value ) {
				update_option( $key, $value );
			}

			// Move to test page
			$this->go_to( $test['target_url'] );
			$return  = vk_get_page_description();
			$correct = $test['correct'];
			// print PHP_EOL;
			// print 'Name    : ' . $test['test_name'] . PHP_EOL;
			// print 'url     : ' . $test['target_url'] . PHP_EOL;
			// print 'return  : ' . $return . PHP_EOL;
			// print 'correct : ' . $correct . PHP_EOL;

			$this->assertEquals( $correct, $return );
		}
	}

	/**
	 * 投稿タイプの埋め込み可能判定テスト
	 */
	public function test_is_post_type_embeddable() {
		// Create a post and set the meta value for 'veu_is_embeddable'.
		$factory = new WP_UnitTest_Factory();
		$post_id = $factory->post->create();

		// Test when 'veu_is_embeddable' is not set (default should be true).
		$this->assertTrue( VK_Post_Type_Manager::is_post_type_embeddable( $post_id ) );

		// Test when 'veu_is_embeddable' is set to 'true'.
		update_post_meta( $post_id, 'veu_is_embeddable', 'true' );
		$this->assertTrue( VK_Post_Type_Manager::is_post_type_embeddable( $post_id ) );

		// Test when 'veu_is_embeddable' is set to 'false'.
		update_post_meta( $post_id, 'veu_is_embeddable', 'false' );
		$this->assertFalse( VK_Post_Type_Manager::is_post_type_embeddable( $post_id ) );
	}

	/**
	 * 複数のカスタム投稿タイプで同一のタクソノミーが登録されている場合のテスト
	 */
	public function test_taxonomy_integration_with_metadata() {
		// カスタム投稿タイプ 'event' と 'voice' に同一タクソノミーを設定
		register_post_type( 'event', array( 'public' => true ) );
		register_post_type( 'voice', array( 'public' => true ) );

		// タクソノミー設定を再登録し、最新の設定を取得
		$refresh_taxonomy = function ( $taxonomy, $args ) {
			register_taxonomy( $taxonomy, array( 'event', 'voice' ), $args );
			return get_taxonomy( $taxonomy );
		};

		// タクソノミーの初期設定
		$taxonomy = $refresh_taxonomy(
			'genre',
			array(
				'label'        => 'Genre',
				'hierarchical' => true,
				'show_in_rest' => true,
				'rest_base'    => 'genre-api',
			)
		);
		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( $taxonomy->show_in_rest );

		// メタデータを使用して階層化設定を変更
		update_option( 'veu_taxonomy_hierarchy', array( 'genre' => false ) );

		// 設定を反映
		$taxonomy = $refresh_taxonomy(
			'genre',
			array(
				'label'        => 'Genre',
				'hierarchical' => false,
				'show_in_rest' => true,
				'rest_base'    => 'genre-api',
			)
		);
		$this->assertFalse( $taxonomy->hierarchical );

		// メタデータを使用してREST API設定を変更
		update_option( 'veu_taxonomy_rest_api', array( 'genre' => false ) );

		// 設定を反映
		$taxonomy = $refresh_taxonomy(
			'genre',
			array(
				'label'        => 'Genre',
				'hierarchical' => false,
				'show_in_rest' => false,
				'rest_base'    => 'genre-api',
			)
		);
		$this->assertFalse( $taxonomy->show_in_rest );

		// メタデータを使用して設定を元に戻す
		update_option( 'veu_taxonomy_hierarchy', array( 'genre' => true ) );
		update_option( 'veu_taxonomy_rest_api', array( 'genre' => true ) );

		// 設定を反映
		$taxonomy = $refresh_taxonomy(
			'genre',
			array(
				'label'        => 'Genre',
				'hierarchical' => true,
				'show_in_rest' => true,
				'rest_base'    => 'genre-api',
			)
		);
		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( $taxonomy->show_in_rest );
	}

	/**
	 * Regression test for the include guard in inc/template-tags/template-tags-config.php.
	 * template-tags-config.php の読み込みガードに関する回帰テスト。
	 *
	 * 背景・経緯の詳細は読み込み元である template-tags-config.php 側の docblock を正とする。
	 * The background and rationale are documented in the docblock of
	 * template-tags-config.php, which is the source of truth.
	 *
	 * @see inc/template-tags/template-tags-config.php
	 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1450
	 */
	public function test_template_tags_config_include_guard() {

		// exec() が disable_functions 等で無効な実行環境（phpdbg 経由の実行を含む）では
		// このテストの前提が成立しないため、失敗ではなくスキップとして扱う。
		// On environments where exec() is disabled (e.g. via disable_functions, or when
		// running under phpdbg), the premise of this test cannot hold, so skip rather than fail.
		if ( ! function_exists( 'exec' ) ) {
			$this->markTestSkipped( 'exec() が無効なためスキップ' );
		}

		$config_path = VEU_DIRECTORY_PATH . '/inc/template-tags/template-tags-config.php';

		$test_cases = array(
			array(
				'test_condition_name' => '何も事前定義されていない通常の読み込み => vk_the_taxonomy_check_list() が定義される（正常系）',
				'prelude'             => '',
			),
			array(
				'test_condition_name' => 'ExUnit 自身の関数が既に全て定義済みの二重読み込み => 再宣言エラーにならず vk_the_taxonomy_check_list() が定義されたまま（正常系）',
				// var_export() はデバッグ出力ではなく、生成する一時 PHP スクリプト内に埋め込むための
				// リテラルなファイルパス文字列を組み立てるために使用している。
				// var_export() here is not debug output; it builds a literal file path string to
				// embed inside the generated temporary PHP script.
				'prelude'             => 'require ' . var_export( VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags.php', true ) . ';', // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			),
			array(
				'test_condition_name' => '他プラグイン同梱の共有パッケージの古いコピー相当（vk_get_post_type() のみ先に定義）が読み込まれた場合 => vk_the_taxonomy_check_list() が定義される（Issue #1450 の境界値）',
				'prelude'             => 'function vk_get_post_type() { return array( "slug" => "stub" ); }',
			),
		);

		foreach ( $test_cases as $case ) {
			// 各ケースを独立した PHP プロセスで実行し、テストプロセス側で既に定義済みの
			// 関数群の影響を受けずに「まだ何も読み込まれていない状態」を再現する。
			// Run each case in an isolated PHP process so it is unaffected by functions
			// already defined in the test process, reproducing a "nothing loaded yet" state.
			//
			// このブロックはテスト実行用の一時 PHP スクリプトをローカルファイルシステムに
			// 書き出して CLI 実行するためのもので、WordPress のリクエスト処理経路では
			// 使われないため、WP_Filesystem や wp_delete_file() 等の代替を必須にする
			// WordPress 標準の直接ファイル操作／システムコール制限を意図的に無視する。
			// This block writes a temporary PHP script to the local filesystem and runs it
			// via CLI purely for test isolation; it is never reached through a WordPress
			// request, so the WordPress-standard restriction against direct file operations
			// and system calls (which exists to protect production request handling) is
			// intentionally bypassed here.
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export, WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents, WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec, WordPress.WP.AlternativeFunctions.unlink_unlink

			// 子プロセス側は "[RESULT:YES]" / "[RESULT:NO]" のマーカー付きで結果を出力する。
			// 警告・Deprecated 等が標準エラーに1行でも混ざると完全一致比較は容易に壊れるため、
			// 親側は完全一致ではなくこのマーカーの包含判定で結果を見る。
			// The child process emits its result wrapped in a "[RESULT:YES]" / "[RESULT:NO]"
			// marker. Since even a single warning/deprecated notice on stderr would break an
			// exact-match comparison, the parent checks for this marker's presence instead.
			$script  = '<?php' . PHP_EOL;
			$script .= $case['prelude'] . PHP_EOL;
			$script .= 'require ' . var_export( $config_path, true ) . ';' . PHP_EOL;
			$script .= 'echo "[RESULT:" . ( function_exists( "vk_the_taxonomy_check_list" ) ? "YES" : "NO" ) . "]";' . PHP_EOL;

			$script_path = tempnam( sys_get_temp_dir(), 'veu-template-tags-config-' );
			$this->assertNotFalse( $script_path, $case['test_condition_name'] . ' / tempnam() が一時ファイルパスの発行に失敗しました' );

			$write_result = file_put_contents( $script_path, $script );
			$this->assertNotFalse( $write_result, $case['test_condition_name'] . ' / file_put_contents() が一時スクリプトの書き込みに失敗しました' );

			try {
				$output    = array();
				$exit_code = 0;
				exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $script_path ) . ' 2>&1', $output, $exit_code );

				$this->assertSame( 0, $exit_code, $case['test_condition_name'] . ' / stderr: ' . implode( PHP_EOL, $output ) );
				$this->assertStringContainsString( '[RESULT:YES]', implode( PHP_EOL, $output ), $case['test_condition_name'] );
			} finally {
				unlink( $script_path );
			}
			// phpcs:enable
		}
	}
}
