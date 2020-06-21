<?php
/**
 * Class MetaKeywordsTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
 /*
  vccw
 cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
 bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
  */

/*
 Flywheel
  cd /app
  bash setup-phpunit.sh
  source ~/.bashrc
  cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
  phpunit
  */

/**
 * Meta Keywords test case.
 */
class MetaKeywordsTest extends WP_UnitTestCase {

	function test_get_postKeyword() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'get_postKeyword' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// メタキーワードはデフォルトがoffなので有効化
		$options_common                       = get_option( 'vkExUnit_common_options' );
		$options_common['active_metaKeyword'] = true;
		update_option( 'vkExUnit_common_options', $options_common );
		veu_package_include();

		$test_array = array(
			array(
				'vkExUnit_metaKeyword'     => 'aaa',
				'vkExUnit_common_keywords' => false,
				'correct'                  => 'aaa',
			),
			array(
				'vkExUnit_metaKeyword'     => false,
				'vkExUnit_common_keywords' => 'bbb',
				'correct'                  => 'bbb',
			),
			array(
				'vkExUnit_metaKeyword'     => 'aaa',
				'vkExUnit_common_keywords' => 'bbb',
				'correct'                  => 'aaa',
			),
		);

		foreach ( $test_array as $key => $test_value ) {

			// Add test post
			$post    = array(
				'post_title'   => 'title',
				'post_status'  => 'publish',
				'post_content' => 'content',
			);
			$post_id = wp_insert_post( $post );

			if (!empty($test_value['vkExUnit_metaKeyword'])){
				update_post_meta( $post_id, 'vkExUnit_metaKeyword', $test_value['vkExUnit_metaKeyword'] );
			}
			if (!empty($test_value['vkExUnit_common_keywords'])){
				update_post_meta( $post_id, 'vkExUnit_common_keywords', $test_value['vkExUnit_common_keywords'] );
			}

			// Move to target page
			$this->go_to( get_permalink( $post_id ) );

			$return = vExUnit_meta_keywords::get_postKeyword();

			// print 'url     :' . $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $test_value['correct'] . PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );

		}

	}
}
