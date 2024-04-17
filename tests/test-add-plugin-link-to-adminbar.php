<?php
/**
 * Class InsertPluginLinkToAdminbarTest
 *
 * @package vektor-inc\vk-all-in-one-expansion-unit
 */

/**
 * 
 */
class addPluginLinkToAdminbarTest extends WP_UnitTestCase {

	function test_veu_is_add_plugin_link_to_adminbar() {

		$tests = array(
			array(
				'title'  => '6.5 / 公開画面',
				'wp_version' => '6.5',
				'is_admin' => false,
				'expected' => false,
			),
			array(
				'title'  => '6.5 / 管理画面',
				'wp_version' => '6.5',
				'is_admin' => true,
				'expected' => true,
			),
			array(
				'title'  => '6.4 / 公開画面',
				'wp_version' => '6.4',
				'is_admin' => false,
				'expected' => true,
			),

		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_is_add_plugin_link_to_adminbar' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {

			// 現在表示中のURLを取得
			$acutual = veu_is_add_plugin_link_to_adminbar( $test_value['wp_version'], $test_value['is_admin'] );

			print PHP_EOL;
			print 'title    :' . $test_value['title'] . PHP_EOL;
			print 'actual   :' . $acutual . PHP_EOL;
			print 'expected :' . $test_value['expected'] . PHP_EOL;

			// PHPunit
			$this->assertEquals( $test_value['expected'], $acutual );
		}
	}
}
