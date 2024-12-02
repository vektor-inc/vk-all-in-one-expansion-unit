<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Call to Action test case.
 */
class SnsBtnsTest extends WP_UnitTestCase {

	/**
	 * SNSボタンを本文欄やフックで自動挿入するかしないかのテスト
	 */
	function test_veu_is_sns_btns_auto_insert() {
		$test_array = array(
			// 初期で自動挿入になっている。ブロックテーマが主流になったらこちらはデフォルトでオフに切り替えたい.
			'null'                => array(
				'vkExUnit_sns_options' => null,
				'expected'             => true,
			),
			'enableSnsBtns_false' => array(
				'vkExUnit_sns_options' => array( 'enableSnsBtns' => null ),
				'expected'             => false,
			),
			'enableSnsBtns_false' => array(
				'vkExUnit_sns_options' => array( 'enableSnsBtns' => true ),
				'expected'             => true,
			),
		);
		foreach ( $test_array as $key => $test_value ) {
			update_option( 'vkExUnit_sns_options', $test_value['vkExUnit_sns_options'] );
			$actual = veu_is_sns_btns_auto_insert();
			$this->assertEquals( $test_value['expected'], $actual );
		}
	}

	public function test_veu_is_sns_btns_display() {

		// 投稿タイプイベントを作成
		register_post_type(
			'event',
			array(
				'label'       => 'Event',
				'has_archive' => true,
				'public'      => true,
			)
		);

		// イベントにイベントカテゴリーを関連付け
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// カテゴリ「テスト」を追加
		$catarr              = array(
			'cat_name'          => 'Category Test',
			'category_nicename' => 'category-test',
		);
		$data['category_id'] = wp_insert_category( $catarr );

		// イベントカテゴリ「テスト」を追加
		$args                 = array(
			'slug' => 'event-category-test',
		);
		$term_info            = wp_insert_term( 'Event Category Test', 'event_cat', $args );
		$data['event_cat_id'] = $term_info['term_id'];

		// 投稿「テスト01」を追加
		$post               = array(
			'post_title'    => 'Post Test 01',
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 01',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_01'] = wp_insert_post( $post );
		add_post_meta( $data['post_id_01'], 'sns_share_botton_hide', true );

		// 投稿「テスト02」を追加
		$post               = array(
			'post_title'    => 'Post Test 02',
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 02',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_02'] = wp_insert_post( $post );
		add_post_meta( $data['post_id_02'], 'sns_share_botton_hide', true );

		// 投稿「テスト03」を追加
		$post               = array(
			'post_title'    => 'Post Test 03',
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 03',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_03'] = wp_insert_post( $post );
		add_post_meta( $data['post_id_03'], 'sns_share_botton_hide', false );

		// 固定ページ「テスト0１」を追加
		$post               = array(
			'post_title'   => 'Page Test 01',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test 01',
		);
		$data['page_id_01'] = wp_insert_post( $post );
		add_post_meta( $data['page_id_01'], 'sns_share_botton_hide', true );

		// 固定ページ「テスト02」を追加
		$post               = array(
			'post_title'   => 'Page Test 02',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test 02',
		);
		$data['page_id_02'] = wp_insert_post( $post );
		add_post_meta( $data['page_id_02'], 'sns_share_botton_hide', true );

		// 固定ページ「テスト03」を追加
		$post               = array(
			'post_title'   => 'Page Test 03',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test 03',
		);
		$data['page_id_03'] = wp_insert_post( $post );
		add_post_meta( $data['page_id_03'], 'sns_share_botton_hide', false );

		// イベント「テスト01」を追加
		$post                = array(
			'post_title'   => 'Event Test 01',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test 01',
		);
		$data['event_id_01'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_01'], 'event_category_name', 'event_cat' );
		add_post_meta( $data['event_id_01'], 'sns_share_botton_hide', true );

		// イベント「テスト02」を追加
		$post                = array(
			'post_title'   => 'Event Test 02',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test 02',
		);
		$data['event_id_02'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_02'], 'event_category_name', 'event_cat' );
		add_post_meta( $data['event_id_02'], 'sns_share_botton_hide', true );

		// イベント「テスト03」を追加
		$post                = array(
			'post_title'   => 'Event Test 03',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test 03',
		);
		$data['event_id_03'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_03'], 'event_category_name', 'event_cat' );
		add_post_meta( $data['event_id_03'], 'sns_share_botton_hide', false );

		$ignore_posts = array(
			$data['post_id_02'],
			$data['page_id_02'],
			$data['event_id_02'],
		);

		$test_array = array(
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_03'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['post_id_03'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_03'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['page_id_03'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_03'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => get_permalink( $data['event_id_03'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
					'snsBtn_ignorePosts'        => json_encode( $ignore_posts ),
				),
				'target_url' => home_url( '/' ) . '?p=9999',
				'correct'    => false,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'SNS Button Display Condition Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		foreach ( $test_array as $test_value ) {

			// Set site name
			update_option( 'vkExUnit_sns_options', $test_value['options'] );
			$this->go_to( $test_value['target_url'] );
			$return  = veu_is_sns_btns_display();
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return );

			// print PHP_EOL;
			// print 'url     ::::' . $test_value['target_url'] . PHP_EOL;
			// print 'correct ::::' . $correct . PHP_EOL;
			// print 'return  ::::' . $return . PHP_EOL;

		}
	}
}
