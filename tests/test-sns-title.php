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
				'page_type'                                => 'is_singlur',
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom title',
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Custom title',
			),
			array(
				'page_type'                                => 'is_singlur',
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Post Title',
			),
			array(
				'page_type'                                => 'is_singlur',
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom title',
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Custom title',
			),
			array(
				'page_type'                                => 'is_singlur',
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'post_title'                               => 'Post Title',
				'site_name'                                => 'Site name',
				'correct'                                  => 'Post Title | Site name',
			),
			// トップページはループ配置された時に対応するためにロジック変更
			// array(
			// 	'page_type'                                => 'is_front_page',
			// 	'sns_options__snsTitle_use_only_postTitle' => false,
			// 	'vkExUnit_sns_title'                       => null,
			// 	'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
			// 	'package_wp_title'                         => true,
			// 	'post_title'                               => 'Post Title',
			// 	'site_name'                                => 'Site name',
			// 	'correct'                                  => 'ExUnitCustomFrontTitle',
			// ),
			// // タイトル書き換え機能が停止されている時はトップのタイトル名が保存されていてもWordPressのタイトル名をそのまま返す
			// array(
			// 	'page_type'                                => 'is_front_page',
			// 	'sns_options__snsTitle_use_only_postTitle' => false,
			// 	'vkExUnit_sns_title'                       => null,
			// 	'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
			// 	'package_wp_title'                         => false,
			// 	'post_title'                               => 'Post Title',
			// 	'site_name'                                => 'Site name',
			// 	'correct'                                  => 'Site name',
			// ),
		);

		$before_blogname                = get_option( 'blogname' );
		$before_vkExUnit_sns_options    = get_option( 'vkExUnit_sns_options' );
		$before_vkExUnit_wp_title       = get_option( 'vkExUnit_wp_title' );
		$before_vkExUnit_common_options = get_option( 'vkExUnit_common_options' );

		foreach ( $test_array as $key => $test_value ) {

			// Set site name
			update_option( 'blogname', $test_value['site_name'] );

			// Add sns title
			if ( $test_value['vkExUnit_sns_title'] !== null ) {

				// Add test post
				$post    = array(
					'post_title'   => $test_value['post_title'],
					'post_status'  => 'publish',
					'post_content' => 'content',
				);
				$post_id = wp_insert_post( $post );

				// Set the post title onry to sns title (ExUnit Main Sessing).
				$vkExUnit_sns_options                                = $before_vkExUnit_sns_options;
				$vkExUnit_sns_options['snsTitle_use_only_postTitle'] = $test_value['sns_options__snsTitle_use_only_postTitle'];
				update_option( 'vkExUnit_sns_options', $vkExUnit_sns_options );
				// Set custom sns title
				update_post_meta( $post_id, 'vkExUnit_sns_title', $test_value['vkExUnit_sns_title'] );
			}

			if ( $test_value['page_type'] == 'is_singlur' ) {
				global $wp_query;
				$args = array( 'p' => $post_id );
				// is_singlur() の条件分岐を効かせるため
				$wp_query = new WP_Query( $args );
				$post     = get_post( $post_id );
				// setup_postdata() しないと wp_title() の書き換えの所で get_the_id() が拾えないため
				setup_postdata( $post );

			// } elseif ( $test_value['page_type'] == 'is_front_page' ) {
			// 	// Set Custom front title
			// 	update_option( 'vkExUnit_wp_title', $test_value['vkExUnit_wp_title'] );
			// 	// タイトルの書き換え機能がオフの場合の確認
			// 	if ( $test_value['package_wp_title'] === false ) {
			// 		$options = get_option( 'vkExUnit_common_options' );
			// 		// 有効化設定の値をタイトル書き換え無効化に設定
			// 		$options['active_wpTitle'] = false;
			// 		update_option( 'vkExUnit_common_options', $options );
			// 	}
			// 	// トップページの時の値を確認するためにトップに移動
			// 	$this->go_to( home_url( '/' ) );
			}

			$return = veu_get_the_sns_title( $post_id );

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;

			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;

			if ( $test_value['page_type'] == 'is_singlur' ) {
				delete_post_meta( $post_id, 'vkExUnit_sns_title' );
				wp_delete_post( $post_id );
				wp_reset_postdata();
				wp_reset_query();
			}
		}

		// もとの値に戻す
		update_option( 'vkExUnit_sns_options', $before_vkExUnit_sns_options );
		update_option( 'blogname', $before_blogname );
		update_option( 'vkExUnit_wp_title', $before_vkExUnit_wp_title );
		update_option( 'vkExUnit_common_options', $before_vkExUnit_common_options );

	}
}
