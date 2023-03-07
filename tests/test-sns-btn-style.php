<?php
/**
 * SnsBtnsStyle
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Share button test
 */
class SnsBtnsStyle extends WP_UnitTestCase {

	/**
	 * シェアボタンの色
	 *
	 * @return void
	 */
	public function test_veu_sns_outer_css() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_sns_outer_css' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => false,
					'snsBtn_color'       => null,
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => false,
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="border:1px solid #f00;background-color:#f00;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"',
			),
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => null,
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="border:1px solid #f00;background-color:#f00;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"',
			),
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => true,
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="border:1px solid #f00;background:none;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"',
			),
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => 'true',
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="border:1px solid #f00;background:none;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"',
			),
		);

		foreach ( $test_array as $key => $test_value ) {

			$return = veu_sns_outer_css( $test_value['options'] );

			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;
			print 'correct :' . esc_attr( $test_value['correct'] ) . PHP_EOL;
			print 'return  :' . esc_attr( $return ) . PHP_EOL;
		}
	}

	/**
	 * シェアボタン文字の色
	 *
	 * @return void
	 */
	public function test_veu_sns_icon_css() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_sns_icon_css' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => null,
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="color:#fff;"',
			),
			array(
				'options' => array(
					'snsBtn_bg_fill_not' => true,
					'snsBtn_color'       => '#f00',
				),
				'correct' => ' style="color:#f00;"',
			),
		);

		foreach ( $test_array as $key => $test_value ) {

			$return = veu_sns_icon_css( $test_value['options'] );

			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;
			print 'correct :' . esc_attr( $test_value['correct'] ) . PHP_EOL;
			print 'return  :' . esc_attr( $return ) . PHP_EOL;
		}
	}

}
