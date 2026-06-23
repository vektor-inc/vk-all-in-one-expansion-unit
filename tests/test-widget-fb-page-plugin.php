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
	 * Tests that enabled posts use the current Page Plugin tabs attribute.
	 */
	public function test_widget_uses_data_tabs_for_timeline() {
		$output = $this->get_widget_output(
			array(
				'page_url'  => 'https://www.facebook.com/facebook',
				'height'    => '600',
				'showFaces' => 'false',
				'hideCover' => 'false',
				'showPosts' => 'true',
			)
		);

		$this->assertStringContainsString( 'data-tabs="timeline"', $output );
		$this->assertStringNotContainsString( 'data-show-posts', $output );
	}

	/**
	 * Tests that legacy instances without showPosts keep showing timeline posts.
	 */
	public function test_widget_keeps_timeline_for_legacy_instances() {
		$output = $this->get_widget_output(
			array(
				'page_url'  => 'https://www.facebook.com/facebook',
				'height'    => '600',
				'showFaces' => 'false',
				'hideCover' => 'false',
			)
		);

		$this->assertStringContainsString( 'data-tabs="timeline"', $output );
		$this->assertStringNotContainsString( 'data-show-posts', $output );
	}

	/**
	 * Tests that disabled posts omit the tabs attribute.
	 */
	public function test_widget_omits_data_tabs_when_page_posts_are_disabled() {
		$output = $this->get_widget_output(
			array(
				'page_url'  => 'https://www.facebook.com/facebook',
				'height'    => '600',
				'showFaces' => 'false',
				'hideCover' => 'false',
				'showPosts' => '',
			)
		);

		$this->assertStringNotContainsString( 'data-tabs=', $output );
		$this->assertStringNotContainsString( 'data-show-posts', $output );
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
