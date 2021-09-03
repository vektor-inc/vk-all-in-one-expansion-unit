<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class CTADiaplayTest extends WP_UnitTestCase {

	function test_cta_display() {

		$test_array = array(
			array(
				'options'          => null,
				'target_post_type' => 'post',
				'correct'          => '0',
			),
			array(
				'options'          => array(
					'post' => '0',
				),
				'target_post_type' => 'post',
				'correct'          => '0',
			),
			array(
				'options'          => array(
					'post' => 1,
				),
				'target_post_type' => 'post',
				'correct'          => 1,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_cta_default_display' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $test_value ) {

			update_option( 'vkExUnit_cta_settings', $test_value['options'] );

			$return = Vk_Call_To_Action::get_option();

			$this->assertEquals( $test_value['correct'], $return[$test_value['target_post_type']] );
			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return[$test_value['target_post_type']] . PHP_EOL;
		}

	} // function test_chlild_page_excerpt() {
}
