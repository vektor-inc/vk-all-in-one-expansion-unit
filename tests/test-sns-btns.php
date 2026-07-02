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
		add_post_meta( $data['post_id_02'], 'sns_share_botton_hide', false );

		// 固定ページ「テスト01」を追加
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
		add_post_meta( $data['page_id_02'], 'sns_share_botton_hide', false );

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
		add_post_meta( $data['event_id_02'], 'sns_share_botton_hide', false );

		$test_array = array(
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
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

	/**
	 * シェアボタンの HTML 出力（各 SNS の表示 ON/OFF）のテスト
	 * Test for the share button HTML output ( display ON/OFF for each SNS ).
	 * ここでは Threads ボタンがオプションに応じて出力される事を確認する
	 * Here we check that the Threads button is output according to the option.
	 */
	public function test_veu_get_sns_btns() {

		// テスト用の投稿を作成し、その投稿ページに遷移する（veu_is_sns_btns_display() を true にするため）
		// Create a test post and go to its page ( to make veu_is_sns_btns_display() return true ).
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Threads Button Test',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Threads Button Test',
			)
		);
		$this->go_to( get_permalink( $post_id ) );

		// Threads の intent URL の先頭部分（この文字列が出力に含まれれば Threads ボタンが出ている）
		// The beginning part of the Threads intent URL ( if the output contains this string, the Threads button is rendered ).
		$threads_intent = 'https://www.threads.net/intent/post?text=';

		$test_cases = array(
			array(
				'test_condition_name' => 'useThreads が true の場合 => Threads ボタン（sb_threads と intent URL）が出力に含まれる',
				'options'             => array( 'useThreads' => true ),
				'expected_contains'   => true,
			),
			array(
				'test_condition_name' => 'useThreads が false の場合 => Threads ボタンは出力に含まれない',
				'options'             => array( 'useThreads' => false ),
				'expected_contains'   => false,
			),
			array(
				'test_condition_name' => 'useThreads キーが未設定の場合 => デフォルト値（ON）が適用され Threads ボタンが出力に含まれる',
				'options'             => array(),
				'expected_contains'   => true,
			),
		);

		foreach ( $test_cases as $case ) {
			// オプション値を設定 / Set option value.
			update_option( 'vkExUnit_sns_options', $case['options'] );

			// シェアボタンの HTML を取得 / Get the share button HTML.
			$actual = veu_get_sns_btns();

			if ( $case['expected_contains'] ) {
				// Threads ボタンの li クラスと intent URL の両方が含まれる事を確認
				// Check that both the li class of the Threads button and the intent URL are included.
				$this->assertStringContainsString( 'sb_threads', $actual, $case['test_condition_name'] );
				$this->assertStringContainsString( $threads_intent, $actual, $case['test_condition_name'] );
				$this->assertStringContainsString( 'fa-threads', $actual, $case['test_condition_name'] );
			} else {
				// Threads ボタンが含まれない事を確認（li クラス・intent URL・アイコンの全てが出力されない）
				// Check that the Threads button is not included ( none of the li class, intent URL, or icon are output ).
				$this->assertStringNotContainsString( 'sb_threads', $actual, $case['test_condition_name'] );
				$this->assertStringNotContainsString( $threads_intent, $actual, $case['test_condition_name'] );
				$this->assertStringNotContainsString( 'fa-threads', $actual, $case['test_condition_name'] );
			}

			// オプション値をクリーンアップ / Clean up the option value.
			delete_option( 'vkExUnit_sns_options' );
		}
	}
}
