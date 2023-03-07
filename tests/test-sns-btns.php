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
	 * SNSボタンの表示しないのチェックボックスを表示するかしないかのテスト
	 */
	function test_sns_is_display_hide_chekbox() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_sns_is_display_hide_chekbox' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'vkExUnit_sns_options__enableSnsBtns' => true,
				'vkExUnit_sns_options__snsBtn_exclude_post_types' => null,
				'post_type'                           => 'post',
				'correct'                             => true,
			),
			array(
				'vkExUnit_sns_options__enableSnsBtns' => false,
				'vkExUnit_sns_options__snsBtn_exclude_post_types' => null,
				'post_type'                           => 'post',
				'correct'                             => false,
			),
			array(
				'vkExUnit_sns_options__enableSnsBtns' => true,
				'vkExUnit_sns_options__snsBtn_exclude_post_types' => array( 'post' => true ),
				'post_type'                           => 'post',
				'correct'                             => false,
			),
			array(
				'vkExUnit_sns_options__enableSnsBtns' => true,
				'vkExUnit_sns_options__snsBtn_exclude_post_types' => array( 'page' => true ),
				'post_type'                           => 'post',
				'correct'                             => true,
			),

		);

		$before_vkExUnit_sns_options = get_option( 'vkExUnit_sns_options' );

		foreach ( $test_array as $key => $test_value ) {

			$vkExUnit_sns_options                              = $before_vkExUnit_sns_options;
			$vkExUnit_sns_options['enableSnsBtns']             = $test_value['vkExUnit_sns_options__enableSnsBtns'];
			$vkExUnit_sns_options['snsBtn_exclude_post_types'] = $test_value['vkExUnit_sns_options__snsBtn_exclude_post_types'];
			update_option( 'vkExUnit_sns_options', $vkExUnit_sns_options );

			$return = veu_sns_is_sns_btns_meta_chekbox_hide( $test_value['post_type'] );

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;
			print 'correct :' . $test_value['correct'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
		}

		update_option( 'vkExUnit_sns_options', $before_vkExUnit_sns_options );
	}

	/**
	 * SNSボタンを本文欄やフックで自動挿入するかしないかのテスト
	 */
	function test_veu_is_sns_btns_auto_insert(){
		$test_array = array(
			// 初期で自動挿入になっている。ブロックテーマが主流になったらこちらはデフォルトでオフに切り替えたい.
			'null' => [
				'vkExUnit_sns_options' => null,
				'expected' => true,
			],
			'enableSnsBtns_false' => [
				'vkExUnit_sns_options' => ['enableSnsBtns' => null],
				'expected' => false,
			],
			'enableSnsBtns_false' => [
				'vkExUnit_sns_options' => ['enableSnsBtns' => true],
				'expected' => true,
			]
		);
		foreach ( $test_array as $key => $test_value ) {
			update_option( 'vkExUnit_sns_options', $test_value['vkExUnit_sns_options'] );
			$actual = veu_is_sns_btns_auto_insert();
			$this->assertEquals( $test_value['expected'], $actual );
		}
	}
}
