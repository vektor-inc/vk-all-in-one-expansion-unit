<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetBtnTest extends WP_UnitTestCase {

	function test_get_btn_options() {
		$tests = array(
			array(
				'maintext' => 'メインテキスト',
				'title'    => null,
				'correct'  => 'メインテキスト',
			),
			array(
				'maintext' => 'メインテキスト',
				'title'    => '',
				'correct'  => '',
			),
			array(
				'maintext' => 'メインテキスト',
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
			array(
				'maintext' => null,
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
			array(
				'maintext' => '',
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
		);

		foreach ( $tests as $key => $test_value ) {
			$return = WP_Widget_Button::get_btn_options( $test_value );
			$this->assertEquals( $test_value['correct'], $return['title'] );

		}
	}

}
