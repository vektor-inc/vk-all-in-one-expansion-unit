<?php
/**
 * Class Author_Structure_Test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

class Author_Structure_Test extends WP_UnitTestCase {
	function test_get_cta_post() {
		$test_value['correct'] = '' ;
		$return = '';
		$this->assertEquals( $test_value['correct'], $return );
	}
}