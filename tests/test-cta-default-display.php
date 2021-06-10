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

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_cta_default_display' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'options'          => null,
				'target_post_type' => 'post',
				'correct'          => false,
			),
			array(
				'options'          => array(
					'post' => 0,
				),
				'target_post_type' => 'post',
				'correct'          => false,
			),
			array(
				'options'          => array(
					'post' => 1,
				),
				'target_post_type' => 'post',
				'correct'          => 1,
			),
		);

		foreach ( $test_array as $key => $test_value ) {

			update_option( 'vkExUnit_cta_settings', $test_value['options'] );

			$return = Vk_Call_To_Action::get_option();

			$this->assertEquals( $test_value['correct'], $return['target_post_type'] );

			print PHP_EOL;

			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;

		}

		// 	$options = Vk_Call_To_Action::get_option();
		// 	$default = Vk_Call_To_Action::get_default_option();

		// 	$posttypes = array_merge(
		// 		array(
		// 			'post' => 'post',
		// 			'page' => 'page',
		// 		),
		// 		get_post_types(
		// 			array(
		// 				'public'   => true,
		// 				'_builtin' => false,
		// 			),
		// 			'names'
		// 		)
		// 	);

		// foreach ( $posttypes  as  $posttype ) {

		// 	$return  = $options[ $posttype ];
		// 	$coreect = $default[ $posttype ];

		// 	// 返ってきた抜粋値と期待する結果が同じかどうかテスト
		// 	$this->assertEquals( $coreect, $return );

		// 	print 'return[' . $posttype . '] :' . $return . PHP_EOL;
		// 	print 'correct[' . $posttype . '] :' . $coreect . PHP_EOL;
		// }

	} // function test_chlild_page_excerpt() {
}
