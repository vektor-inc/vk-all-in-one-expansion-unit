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
}
