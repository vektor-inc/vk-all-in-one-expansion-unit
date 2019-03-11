<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
/*
cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
 */
/**
 * WidgetPage test case.
 */
class WP_Widget_vkExUnit_post_list_Test extends WP_UnitTestCase {

	function test_get_widget_title() {
		$tests = array(
			array(
				'label'   => '最新の記事',
				'title'   => null,
				'correct' => '最新の記事',
			),
			// 両方指定がある場合は title 優先
			array(
				'label'   => '最新の記事',
				'title'   => 'タイトルの記事',
				'correct' => 'タイトルの記事',
			),
			// あえて空が指定したあったら空を返す
			array(
				'maintext' => '最新の記事',
				'title'    => '',
				'correct'  => '',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'WP_Widget_vkExUnit_post_list' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			$return = WP_Widget_vkExUnit_post_list::get_widget_title( $test_value );
			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;
			print 'return    :' . $return . PHP_EOL;
			print 'correct   :' . $test_value['correct'] . PHP_EOL;
		}
	}

}
