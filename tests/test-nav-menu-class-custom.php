<?php
/**
 * Class VkNavMenuClassCustomTest
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

// プロジェクトのルートからの相対パスを確認して修正
// require_once __DIR__ . '/../vendor/vektor-inc/vk-wp-unit-test-tools/src/VkWpUnitTestHelpers.php';
require_once __DIR__ . '/../vendor/autoload.php';



class VkNavMenuClassCustomTest extends WP_UnitTestCase {

	function test_class_name_custom() {

		$tests = array(
			array(
				'test_name'  => 'Add class name',
				'content'    => '<div class="aaa">test</div>',
				'class_name' => 'test_class',
				'add'        => true,
				'expected'   => '<div class="aaa test_class">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最後にあるクラスを削除する場合)',
				'content'    => '<div class="aaa test_class">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最初にあるクラスを削除する場合)',
				'content'    => '<div class="test_class aaa">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最初にあるクラスを削除する場合 / 複数のクラスがある場合)',
				'content'    => '<div class="test_class aaa bbb">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa bbb">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (真ん中にあるクラスを削除する場合 / 複数のクラスがある場合)',
				'content'    => '<div class="aaa test_class bbb">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa bbb">test</div>',
			),
		);

		foreach ( $tests as $key => $test_value ) {
			$return = VkNavMenuClassCustom::class_name_custom( $test_value['content'], $test_value['class_name'], $test_value['add'] );

			$this->assertEquals( $test_value['expected'], $return, $test_value['test_name'] );
		}
	}


	/**
	 * test_get_post_type_from_url
	 * ヘッダーメニューのアクティブラベル用なので、トップメニューに入る項目として、
	 * 投稿トップ / カスタム投稿タイプのトップ の投稿タイプが検出できればよい
	 * （詳細ページの検出は不要）
	 */
	function test_get_post_type_from_url() {
		// create_test_posts を実行して $test_posts に格納
		$test_posts = VK_WP_Unit_Test_Tools\VkWpUnitTestHelpers::create_test_posts();
		$tests      = array(
			array(
				'test_name' => '投稿詳細',
				'options'   => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'url'       => get_permalink( $test_posts['post_id'] ),
				'expected'  => 'post',
			),
			array(
				'test_name' => '投稿トップ',
				'options'   => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'url'       => get_permalink( $test_posts['home_page_id'] ),
				'expected'  => 'post',
			),
			array(
				'test_name' => 'カスタム投稿タイプトップ',
				'url'       => home_url( '/?post_type=event' ),
				'expected'  => 'event',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'VkNavMenuClassCustom::get_post_type_from_url()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $tests as $key => $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}
						// print 'expected::::' . $value['expected'] . PHP_EOL;
			print 'actual  ::::' . $value['url'] . PHP_EOL;
			$return = VkNavMenuClassCustom::get_post_type_from_url( $value['url'] );

			$this->assertEquals( $value['expected'], $return, $value['test_name'] );
		}
	}
}
