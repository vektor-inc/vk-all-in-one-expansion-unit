<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetPage extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */

	 // 子ページインデックス機能がアクティブかどうか
	 function test_is_active_child_page_index(){
		 $tests = array(
			 array(
				 'active_childPageIndex' => null, // 5.7.4 以前を利用で一度も有効化設定を保存していないユーザー
				 'correct' => true,
			 ),
			 array(
				 'active_childPageIndex' => true, // 有効化設定で保存している or 5.7.5以降のユーザー
				 'correct' => true,
			 ),
			 array(
				 'active_childPageIndex' => false, // 無効化しているユーザー
				 'correct' => false,
			 ),
		 );

		 foreach ($tests as $key => $value) {
			 $resurt = WP_Widget_vkExUnit_widget_page::is_active_child_page_index( $value );
			 $this->assertEquals( $value['correct'], $resurt );
		 }
	 }

	 // 先祖階層からのページリスト表示機能がアクティブかどうか
	 function test_is_active_page_list_ancestor(){
		 $tests = array(
			 array(
				 'active_pageList_ancestor' => null, // 5.7.4 以前を利用で一度も有効化設定を保存していないユーザー
				 'correct' => true,
			 ),
			 array(
				 'active_pageList_ancestor' => true, // 有効化設定で保存している or 5.7.5以降のユーザー
				 'correct' => true,
			 ),
			 array(
				 'active_pageList_ancestor' => false, // 無効化しているユーザー
				 'correct' => false,
			 ),
		 );

		 foreach ($tests as $key => $value) {
			 $resurt = WP_Widget_vkExUnit_widget_page::is_active_page_list_ancestor( $value );
			 $this->assertEquals( $value['correct'], $resurt );
		 }
	 }

	function test_widget_page() {
		// テスト用の投稿を追加する

		// 投稿ステータスが「公開」の固定ページを作成
		$post = array(
		  'post_name'      => 'test-page-slug',
		  'post_title'     => '固定ページのタイトルです',
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		);
		$id_publish = wp_insert_post( $post );

		// 投稿ステータスが「非公開」の固定ページを作成
		$post = array(
		  'post_name'      => 'test-page-slug',
		  'post_title'     => '固定ページのタイトルです（非公開）',
		  'post_status'    => 'private',
		  'post_type'      => 'page',
		);
		$id_private = wp_insert_post( $post );

		$test_array = array(

			// versiton - 5.4
			array(
				'title' => null,
				'set_title' => null,
				'page_id' => $id_publish, // いくつでも関係ないはず
				'title_correct' => null,
				'display_correct' => false
			),
			array(
				'title' => null,
				'set_title' => true,
				'page_id' => $id_publish, // いくつでも関係ないはず
				'title_correct' => '固定ページのタイトルです',
				'display_correct' => true
			),

			// versiton 5.4 -
			array(
				'title' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-widget',
				'page_id' => $id_publish, // いくつでも関係ないはず
				'title_correct' => 'ウィジェットに入力されたタイトル',
				'display_correct' => true
			),
			array(
				'title' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-hidden',
				'page_id' => $id_publish, // いくつでも関係ないはず
				'title_correct' => '',
				'display_correct' => false
			),
			array(
				'title' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-page',
				'page_id' => $id_publish,
				'title_correct' => '固定ページのタイトルです',
				'display_correct' => true
			),
			array(
				'title' => '',
				'set_title' => 'title-page',
				'page_id' => $id_publish,
				'title_correct' => '固定ページのタイトルです',
				'display_correct' => true
			),
			array(
				'title' => '',
				'set_title' => 'title-page',
				'page_id' => $id_private,
				'title_correct' => '固定ページのタイトルです（非公開）',
				'display_correct' => true
			),
		);

		print PHP_EOL;
		print '------------------------------------'.PHP_EOL;
		print 'test_widget_page'.PHP_EOL;
		print '------------------------------------'.PHP_EOL;
		foreach ( $test_array as $key => $test_value) {
			// instanceに投げる変数を代入
			$instance['title'] = $test_value['title'];
			$instance['set_title'] = $test_value['set_title'];
			$instance['page_id'] = $test_value['page_id'];

			// widget_title() で タイトル情報を取得
			$widget_title = WP_Widget_vkExUnit_widget_page::widget_title( $instance );
			print PHP_EOL;
			print 'widget_title         :'.$widget_title['title'].PHP_EOL;
			print 'widget_title correct :'.$test_value['title_correct'].PHP_EOL;
			print 'widget_display         :'.$widget_title['display'].PHP_EOL;
			print 'widget_display_correct :'.$test_value['display_correct'].PHP_EOL;
			// 取得できたタイトルの値と、想定する正しいタイトル名が等しいかテスト
			$this->assertEquals( $test_value['title_correct'], $widget_title['title'] );
			$this->assertEquals( $test_value['display_correct'], $widget_title['display'] );
		}

		$this->assertTrue( true );
	}
}
