<?php
/**
 * Class HeadTitleTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * SEO title test case.
 */
class HeadTitleTest extends WP_UnitTestCase {

	/**
	 * タイトル書き換えのテスト
	 */
	function test_veu_get_the_sns_title() {

		$sep = ' | ';

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_head_title' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'page_type'      => 'is_singular',
				'post_title'     => 'Post Title',
				'site_name'      => 'Site name',
				'veu_head_title' => array(
					'title'          => 'Custom Title',
					'add_site_title' => false,
				),
				'correct'        => 'Custom Title',
			),
			array(
				'page_type'      => 'is_singular',
				'post_title'     => 'Post Title',
				'site_name'      => 'Site name',
				'veu_head_title' => array(
					'title'          => 'Custom Title',
					'add_site_title' => true,
				),
				'correct'        => 'Custom Title' . $sep . 'Site name',
			),
			array(
				'page_type'      => 'is_singular',
				'post_title'     => 'Post Title',
				'site_name'      => 'Site name',
				'veu_head_title' => array(
					'title'          => '',
					'add_site_title' => false,
				),
				'correct'        => 'Post Title' . $sep . 'Site name',
			),
			array(
				'page_type'      => 'is_page',
				'post_title'     => 'Page Title',
				'site_name'      => 'Site name',
				'veu_head_title' => array(
					'title'          => 'Custom Title',
					'add_site_title' => false,
				),
				'correct'        => 'Custom Title',
			),
			array(
				'page_type'      => 'is_page',
				'post_title'     => 'Page Title',
				'site_name'      => 'Site name',
				'veu_head_title' => array(
					'title'          => 'Custom Title',
					'add_site_title' => true,
				),
				'correct'        => 'Custom Title' . $sep . 'Site name',
			),
		);

		$before_blogname           = get_option( 'blogname' );
		$before_veu_wp_title       = get_option( 'vkExUnit_wp_title' );
		$before_veu_common_options = get_option( 'vkExUnit_common_options' );

		foreach ( $test_array as $key => $test_value ) {

			// Set site name.
			update_option( 'blogname', $test_value['site_name'] );

			// Add sns title.
			if ( $test_value['veu_head_title'] !== null ) {

				// Add test post.
				$post    = array(
					'post_title'   => $test_value['post_title'],
					'post_status'  => 'publish',
					'post_content' => 'content',
				);
				$post_id = wp_insert_post( $post );

				add_post_meta( $post_id, 'veu_head_title', $test_value['veu_head_title'] );
			}

			if ( 'is_singular' === $test_value['page_type'] || 'is_page' === $test_value['page_type'] ) {
				global $wp_query;
				$args = array( 'p' => $post_id );
				// is_singular() の条件分岐を効かせるため
				$wp_query = new WP_Query( $args );
				$post     = get_post( $post_id );
				// setup_postdata() しないと wp_title() の書き換えの所で get_the_id() が拾えないため
				setup_postdata( $post );

				// } elseif ( $test_value['page_type'] == 'is_front_page' ) {
				// Set Custom front title
				// update_option( 'vkExUnit_wp_title', $test_value['vkExUnit_wp_title'] );
				// タイトルの書き換え機能がオフの場合の確認
				// if ( $test_value['package_wp_title'] === false ) {
				// $options = get_option( 'vkExUnit_common_options' );
				// 有効化設定の値をタイトル書き換え無効化に設定
				// $options['active_wpTitle'] = false;
				// update_option( 'vkExUnit_common_options', $options );
				// }
				// トップページの時の値を確認するためにトップに移動
				// $this->go_to( home_url( '/' ) );
			}

			$return = vkExUnit_get_wp_head_title();

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;

			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;

			if ( 'is_singular' === $test_value['page_type'] || 'is_page' === $test_value['page_type'] ) {
				delete_post_meta( $post_id, 'veu_head_title' );
				wp_delete_post( $post_id );
				wp_reset_postdata();
				wp_reset_query();
			}
		}

		// もとの値に戻す.
		update_option( 'blogname', $before_blogname );
		update_option( 'vkExUnit_wp_title', $before_veu_wp_title );
		update_option( 'vkExUnit_common_options', $before_veu_common_options );

	}
}
