<?php
/**
 * Class TemplateTagsTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
 /*
 cd /app
 bash setup-phpunit.sh
 source ~/.bashrc
 cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
 phpunit
 */


class TemplateTagsTest extends WP_UnitTestCase {

	function test_vk_the_post_type_check_list_saved_array_convert() {

		$tests = array(
			array(
				'option'  => array(
					'post' => true,
					'info' => '',
				),
				'correct' => array( 'post' ),
			),
			array(
				'option'  => array(
					'post' => true,
					'info' => true,
				),
				'correct' => array( 'post', 'info' ),
			),
			array(
				'option'  => array(
					'post' => 'true',
					'info' => true,
				),
				'correct' => array( 'post', 'info' ),
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_the_post_type_check_list_saved_array_convert' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_Ads', $test_value['option'] );

			$return = vk_the_post_type_check_list_saved_array_convert( $test_value['option'] );

			// PHPunit
			$this->assertEquals( $test_value['correct'], $return );
			print PHP_EOL;
			// 帰り値が配列だから print してもエラーになるだけなのでコメントアウト
			// print 'return    :' . $return. PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
		}
	}


	function test_vk_is_block_category_exist(){

		$tests = array(
			array(
				'categories' => array(
					array(
						'slug' => 'common',
						'title' => '一般ブロック',
						'icon' => ''
					),
					array(
						'slug' => 'formatting',
						'title' => 'フォーマット',
						'icon' => ''
					),
				),
				'slug' => 'vk-block-cat',
				'correct' => false,
			),
			array(
				'categories' => array(
					array(
						'slug' => 'common',
						'title' => '一般ブロック',
						'icon' => ''
					),
					array(
						'slug' => 'vk-block-cat',
						'title' => 'VK Block',
						'icon' => ''
					),
				),
				'slug' => 'vk-block-cat',
				'correct' => true,
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_is_block_category_exist' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {

			$return = vk_is_block_category_exist( $test_value['categories'],$test_value['slug'] );

			// PHPunit
			$this->assertEquals( $test_value['correct'], $return );
			print PHP_EOL;
			print 'return    :' . $return. PHP_EOL;
			print 'correct   :' . $test_value['correct'] . PHP_EOL;
		}
	}


}
