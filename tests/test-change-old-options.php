<?php
/**
 * Promotion Alert Test
 *
 * @package VK All in One Expansion Unit
 */

/**
 * Promotion Alert Test
 */
class ChangeOldOptionsTest extends WP_UnitTestCase {

    public function test_change_old_options() {

        $test_array = array(
			array(
				'options' => array(
					'common' => true,
				),
				'correct' => array(
					'css_optimize' => 'tree-shaking',
				)
			),
			array(
				'options' => array(
					'css_exunit' => true,
				),
				'correct' => array(
					'css_optimize' => 'tree-shaking',
				)
			),
			array(
				'options' => array(
					'css_optimize' => 'tree-shaking',
					'js_footer'    => true,
				),
				'correct' => array(
					'css_optimize' => 'tree-shaking',
				)
			),
        );

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'Change Old Options Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		foreach ( $test_array as $test_value ) {

			// Set site name
			update_option( 'vkExUnit_pagespeeding', $test_value['options'] );
			change_old_options();

			$return  = get_option( 'vkExUnit_pagespeeding' );
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return );

			// print PHP_EOL;
			// print 'correct ::::' . PHP_EOL;
			// var_dump( $correct );
			// print 'return  ::::' . PHP_EOL;
			// var_dump( $return );

		}
    }
}