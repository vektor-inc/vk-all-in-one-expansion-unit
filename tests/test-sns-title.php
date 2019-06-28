<?php
/**
 * Class SnsTitleTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
 /*
 cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
 bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
  */
/**
 * SNS title test case.
 */
class SnsTitleTest extends WP_UnitTestCase {

	/**
	 * SNSタイトル書き換えのテスト
	 */
	function test_veu_get_the_sns_title() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_sns_title' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom title',
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Custom title',
			),
			array(
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Post Title',
			),
			array(
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom title',
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Custom title',
			),
			array(
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Post Title | Site name',
			),
		);

		$before_blogname             = get_option( 'blogname' );
		$before_vkExUnit_sns_options = get_option( 'vkExUnit_sns_options' );

		foreach ( $test_array as $key => $test_value ) {

				// Set site name
				update_option( 'blogname', $test_value['site_name'] );

				// Add test post
				$post    = array(
					'post_title'   => $test_value['post_title'],
					'post_status'  => 'publish',
					'post_content' => 'content',
				);
				$post_id = wp_insert_post( $post );

				// Add sns title
			if ( $test_value['vkExUnit_sns_title'] !== null ) {
				update_post_meta( $post_id, 'vkExUnit_sns_title', $test_value['vkExUnit_sns_title'] );
			}

				global $wp_query;
				$args = array( 'p' => $post_id );
				// is_singlur() の条件分岐を効かせるため
				$wp_query = new WP_Query( $args );

				$post = get_post( $post_id );
				// setup_postdata() しないと wp_title() の書き換えの所で get_the_id() が拾えないため
				setup_postdata( $post );

				$vkExUnit_sns_options                                = $before_vkExUnit_sns_options;
				$vkExUnit_sns_options['snsTitle_use_only_postTitle'] = $test_value['sns_options__snsTitle_use_only_postTitle'];
				update_option( 'vkExUnit_sns_options', $vkExUnit_sns_options );

				$return = veu_get_the_sns_title( $post_id );

				// 取得できたHTMLが、意図したHTMLと等しいかテスト
				$this->assertEquals( $test_value['correct'], $return );

				print PHP_EOL;

				print 'correct :' . $test_value['correct'] . PHP_EOL;
				print 'return  :' . $return . PHP_EOL;

				delete_post_meta( $post_id, 'vkExUnit_sns_title' );
				wp_delete_post( $post_id );
				wp_reset_postdata();
				wp_reset_query();
		}
		// // もとの値に戻す
		update_option( 'vkExUnit_sns_options', $before_vkExUnit_sns_options );
		update_option( 'blogname', $before_blogname );
	}
}
