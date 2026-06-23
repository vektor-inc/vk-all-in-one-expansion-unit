<?php
/**
 * Class WidgetFbPagePluginTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * FB Page Plugin widget test case.
 */
class WidgetFbPagePluginTest extends WP_UnitTestCase {

	/**
	 * Returns the widget output for an instance.
	 *
	 * @param array $instance Widget instance.
	 *
	 * @return string
	 */
	public function get_widget_output( $instance ) {
		$widget = new WP_Widget_vkExUnit_fbPagePlugin();
		$args   = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		);

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		remove_action( 'wp_footer', 'exUnit_print_fbId_script', 100 );

		return $output;
	}

	/**
	 * Tests widget data-tabs output for multiple instance configurations.
	 */
	public function test_widget_data_tabs_cases() {
		$test_cases = array(
			array(
				'test_condition_name' => 'showPosts=true outputs timeline tab',
				'instance'            => array(
					'page_url'  => 'https://www.facebook.com/facebook',
					'height'    => '600',
					'showFaces' => 'false',
					'hideCover' => 'false',
					'showPosts' => 'true',
				),
				'contains'            => array( 'data-tabs="timeline"' ),
				'not_contains'        => array( 'data-show-posts' ),
			),
			array(
				'test_condition_name' => 'legacy instance without showPosts outputs timeline tab',
				'instance'            => array(
					'page_url'  => 'https://www.facebook.com/facebook',
					'height'    => '600',
					'showFaces' => 'false',
					'hideCover' => 'false',
				),
				'contains'            => array( 'data-tabs="timeline"' ),
				'not_contains'        => array( 'data-show-posts' ),
			),
			array(
				'test_condition_name' => 'disabled posts omit data-tabs attribute',
				'instance'            => array(
					'page_url'  => 'https://www.facebook.com/facebook',
					'height'    => '600',
					'showFaces' => 'false',
					'hideCover' => 'false',
					'showPosts' => '',
				),
				'contains'            => array(),
				'not_contains'        => array( 'data-tabs=', 'data-show-posts' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$output = $this->get_widget_output( $case['instance'] );

			foreach ( $case['contains'] as $needle ) {
				$this->assertStringContainsString( $needle, $output, $case['test_condition_name'] );
			}
			foreach ( $case['not_contains'] as $needle ) {
				$this->assertStringNotContainsString( $needle, $output, $case['test_condition_name'] );
			}
		}
	}

	/**
	 * Tests that the SDK script output uses the current SDK version.
	 */
	public function test_facebook_sdk_script_uses_current_sdk_version() {
		ob_start();
		exUnit_print_fbId_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'version=v25.0', $output );
		$this->assertStringContainsString( 'js.crossOrigin = "anonymous"', $output );
		$this->assertStringNotContainsString( 'version=v2.9', $output );
	}
}
