<?php
/**
 * Class Website_Structure_Test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

class Website_Structure_Test extends WP_UnitTestCase {
	function test_get_website_array() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_get_website_array' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;


		$test_data = array(
			array(
				'target_url'    => home_url( '/' ),
				'options'       => array(
					'blogname' => 'Site name',
				),
				'correct' => array(
					'@context'      => 'https://schema.org/',
					'@type'         => 'WebSite',
					'name'          => 'Site name',
					'url'           => 'http://localhost:8889',
				),
			),
		);

		foreach( $test_data as $test_value) {

			if ( ! empty( $test_value['options'] ) && is_array( $test_value['options'] ) ) {
				foreach ( $test_value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			$this->go_to( $test_value['target_url'] );

			$actual = VK_Website_Srtuctured_Data::get_website_structure_array();
			$correct = $test_value['correct'];

			print PHP_EOL;

			print 'correct ::::' . PHP_EOL;
			var_dump( $correct );
			print 'return  ::::' . PHP_EOL;
			var_dump( $actual );

			$this->assertEquals( $correct, $actual );

			if ( ! empty( $test_value['options'] ) && is_array( $test_value['options'] ) ) {
				foreach ( $test_value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}
	}
}
