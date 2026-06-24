<?php
/**
 * Class WidgetShowInstanceInRestTest
 *
 * ExUnit のクラシックウィジェットに widget オプション show_instance_in_rest が
 * true で付与されていることを検証するテスト。
 * これによりブロックウィジェット編集画面でインスタンス設定をブロック内に
 * 自己完結で保持・編集でき、参照ウィジェットの解決失敗による非表示を防ぐ。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetShowInstanceInRest test case.
 */
class WidgetShowInstanceInRestTest extends WP_UnitTestCase {

	/**
	 * 検証対象となる ExUnit ウィジェットクラス名の一覧。
	 *
	 * @return string[] ウィジェットクラス名の配列。
	 */
	public function widget_class_provider() {
		return array(
			array( 'WP_Widget_vkExUnit_fbPagePlugin' ),
			array( 'VK_Twitter_Widget' ),
			array( 'WP_Widget_vkExUnit_post_list' ),
			array( 'WP_Widget_VK_taxonomy_list' ),
			array( 'WP_Widget_vkExUnit_profile' ),
			array( 'WP_Widget_vkExUnit_PR_Blocks' ),
			array( 'WP_Widget_vkExUnit_ChildPageList' ),
			array( 'WidgetBanner' ),
			array( 'WP_Widget_Button' ),
			array( 'WP_Widget_VK_archive_list' ),
			array( 'WP_Widget_vkExUnit_3PR_area' ),
			array( 'WP_Widget_vkExUnit_widget_page' ),
			array( 'WP_Widget_VkExUnit_Contact_Button' ),
			array( 'WP_Widget_VkExUnit_Contact_Section' ),
			array( 'Widget_CTA' ),
		);
	}

	/**
	 * 各ウィジェットの widget_options に show_instance_in_rest => true が
	 * 含まれていることを確認する。
	 *
	 * @dataProvider widget_class_provider
	 *
	 * @param string $class_name 検証対象のウィジェットクラス名。
	 */
	public function test_show_instance_in_rest_is_enabled( $class_name ) {
		// 対象クラスが読み込まれていることを前提とする。
		$this->assertTrue( class_exists( $class_name ), $class_name . ' クラスが存在しません。' );

		// ウィジェットをインスタンス化し、コンストラクタで設定された widget_options を取得する。
		$widget = new $class_name();

		// show_instance_in_rest が宣言されていることを確認する。
		$this->assertArrayHasKey(
			'show_instance_in_rest',
			$widget->widget_options,
			$class_name . ' に show_instance_in_rest が宣言されていません。'
		);

		// 値が true であることを確認する。
		$this->assertTrue(
			$widget->widget_options['show_instance_in_rest'],
			$class_name . ' の show_instance_in_rest が true ではありません。'
		);
	}
}
