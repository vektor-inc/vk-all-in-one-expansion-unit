<?php
/**
 * SnsBtnsStyle
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

require_once VEU_DIRECTORY_PATH . '/inc/icon-accessibility/icon-accessibility.php';

/**
 * Share button test
 */
class IconAccessibilityTest extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
	}

	/**
	 * シェアボタンの色
	 *
	 * @return void
	 */
	public function test_add_aria_hidden_to_fontawesome() {


		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_add_aria_hidden_to_fontawesome' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_cases = array(
			array(
				'content' => '<div><i class="fa fa-home"></i></div>',
				'correct' => '<div><i class="fa fa-home" aria-hidden="true"></i></div>'
			),
			array(
				'content' => '<p>Text <i class="fa fa-envelope"></i> more text.</p>',
				'correct' => '<p>Text <i class="fa fa-envelope" aria-hidden="true"></i> more text.</p>'
			),
			array(
				'content' => '<div><i class="fa fa-car" aria-hidden="true"></i></div>',
				'correct' => '<div><i class="fa fa-car" aria-hidden="true"></i></div>'
			),
			array(
				'content' => '<ul><li><i class="fa fa-check"></i> Item 1</li><li><i class="fa fa-check"></i> Item 2</li></ul>',
				'correct' => '<ul><li><i class="fa fa-check" aria-hidden="true"></i> Item 1</li><li><i class="fa fa-check" aria-hidden="true"></i> Item 2</li></ul>'
			),
			array(
				'content' => '<footer><i class="fa fa-twitter"></i> Follow us!</footer>',
				'correct' => '<footer><i class="fa fa-twitter" aria-hidden="true"></i> Follow us!</footer>'
			),
			array(
				'content' => '<footer><i aria-hidden="true" class="fa fa-twitter"></i> Follow us!</footer>',
				'correct' => '<footer><i aria-hidden="true" class="fa fa-twitter"></i> Follow us!</footer>'
			),			
		);

		foreach ( $test_cases as $key => $test_value ) {

			$return = VEU_Icon_Accessibility::add_aria_hidden_to_fontawesome( $test_value['content'] );

			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;
			print 'correct :' . $test_value['correct'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
		}
	}


}
