<?php
/**
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

class InsertItemMetaBoxDisplayTest extends WP_UnitTestCase {

	/**
	 * SNSタイトル書き換えのテスト
	 */
	function test_veu_is_insert_item_metabox_display() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_is_insert_item_metabox_display' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'vkExUnit_common_options' => array(
					'active_childPageIndex'    => '',
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
				),
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => false,
			),
			array(
				'vkExUnit_common_options' => array(
					'active_childPageIndex'    => '',
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => true,
				),
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => true,
			),
			array(
				'vkExUnit_common_options' => array(
					'active_childPageIndex'    => true,
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
				),
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => true,
			),
			array(
				'vkExUnit_common_options' => array(
					'active_childPageIndex'    => true,
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
				),
				'post'                    => array(
					'ID'           => 99999,
					'post_type'    => 'page',
					'post_title'   => 'Test Title',
					'post_content' => 'Test Content',
				),
				'test_url'                => admin_url( '/post.php?post=99999&action=edit' ),
				'correct'                 => true,
			),

		);

		$before_vkExUnit_common_options = get_option( 'vkExUnit_common_options' );

		foreach ( $test_array as $key => $test_value ) {

			// Set site name
			update_option( 'vkExUnit_common_options', $test_value['vkExUnit_common_options'] );

			if ( ! empty( $test_value['post'] ) && is_array( $test_value['post'] ) ) {
				$post_id = wp_update_post( $test_value['post'] );
			}

			$this->go_to( $test_value['test_url'] );

			$return = veu_is_insert_item_metabox_display();

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			// $this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;

			print $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;

		}

		// もとの値に戻す
		update_option( 'vkExUnit_common_options', $before_vkExUnit_common_options );
		wp_delete_post( 99999, true );
	}
}
