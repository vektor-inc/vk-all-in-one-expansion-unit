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
	function test_sample() {
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
			array(
				'label' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-widget',
				'page_id' => '100',
				'correct' => 'ウィジェットに入力されたタイトル'
			),
			array(
				'label' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-hidden',
				'page_id' => 2, // いくでも関係ないはず
				'correct' => ''
			),
			array(
				'label' => 'ウィジェットに入力されたタイトル',
				'set_title' => 'title-page',
				'page_id' => $id_publish,
				'correct' => '固定ページのタイトルです'
			),
			array(
				'label' => '',
				'set_title' => 'title-page',
				'page_id' => $id_publish,
				'correct' => '固定ページのタイトルです'
			),
			array(
				'label' => '',
				'set_title' => 'title-page',
				'page_id' => $id_private,
				'correct' => '固定ページのタイトルです（非公開）'
			),
		);

		foreach ( $test_array as $key => $test_value) {
			// instanceに投げる変数を代入
			$instance['label'] = $test_value['label'];
			$instance['set_title'] = $test_value['set_title'];
			$instance['page_id'] = $test_value['page_id'];
			// widget_title() で タイトル情報を取得
			$widget_title = WP_Widget_vkExUnit_widget_page::widget_title( $instance );
			// 取得できたタイトルの値と、想定する正しいタイトル名が等しいかテスト
			$this->assertEquals( $test_value['correct'], $widget_title['label'] );
		}

		$this->assertTrue( true );
	}
}
