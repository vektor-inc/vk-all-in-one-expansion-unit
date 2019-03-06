<?php
/**
 * Class veu_css_customize
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * veu_css_customize test case.
 */
class CssCustomizeTest extends WP_UnitTestCase {

	/**
	 * カスタマイズCSSのテスト
	 */
	function test_css_customize_get_the_css_min() {
			$tests = array(
				array(
					'option'  => 'div > h1 { color:red;   }',
					'correct' => 'div > h1 { color:red; }',
				),
				array(
					'option'  => 'div > h1 {
						color:red;
						}',
					'correct' => 'div > h1 {color:red;}',
				),
				array(
					'option'  => '<script></script>div > h1 {color:red;}',
					'correct' => 'div > h1 {color:red;}',
				),
			);

			print PHP_EOL;
			print '------------------------------------' . PHP_EOL;
			print 'veu_css_customize' . PHP_EOL;
			print '------------------------------------' . PHP_EOL;
			$before_option = get_option( 'vkExUnit_css_customize' );

		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_css_customize', $test_value['option'] );
			$return = veu_css_customize::css_customize_get_the_css_min();
			print PHP_EOL;
			print 'return    :' . $return . PHP_EOL;
			print 'correct   :' . $test_value['correct'] . PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );
		} // foreach ( $tests as $key => $test_value ) {
		$before_option = update_option( 'vkExUnit_css_customize', $before_option );
	} // function test_css_customize_get_the_css_min() {
}
