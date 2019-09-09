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

class ParentMetaBoxDisplayManualTest extends WP_UnitTestCase {

	/**
	 * SNSタイトル書き換えのテスト
	 */
	function test_veu_is_parent_metabox_display_maual() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_is_parent_metabox_display_maual' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'vkExUnit_common_options' => array(
					'active_sns'               => '',
					'active_css_customize'     => '',
					'active_childPageIndex'    => '',
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
					'active_call_to_action'    => '',
					'active_noindex'           => '',
					'active_auto_eyecatch'     => '',
					'active_metaKeyword'       => '',
				),
				// 'current_page_post_type'  => 'post',
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => false,
			),
			array(
				'vkExUnit_common_options' => array(
					'active_sns'               => '',
					'active_css_customize'     => true,
					'active_childPageIndex'    => '',
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
					'active_call_to_action'    => '',
					'active_noindex'           => '',
					'active_auto_eyecatch'     => '',
					'active_metaKeyword'       => '',
				),
				// 'current_page_post_type'  => 'post',
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => true,
			),
			array(
				'vkExUnit_common_options' => array(
					'active_sns'               => '',
					'active_css_customize'     => '',
					'active_childPageIndex'    => true,
					'active_pageList_ancestor' => '',
					'active_contact_section'   => '',
					'active_sitemap_page'      => '',
					'active_call_to_action'    => '',
					'active_noindex'           => '',
					'active_auto_eyecatch'     => '',
					'active_metaKeyword'       => '',
				),
				// 'current_page_post_type'  => 'post',
				'test_url'                => admin_url( '/post-new.php?post_type=page' ),
				'correct'                 => true,
			),

		);

		$before_vkExUnit_common_options = get_option( 'vkExUnit_common_options' );

		foreach ( $test_array as $key => $test_value ) {

			// Set site name
			update_option( 'vkExUnit_common_options', $test_value['vkExUnit_common_options'] );

			$this->go_to( $test_value['test_url'] );

			$return = veu_is_parent_metabox_display_maual();

			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;

			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;

		}

		// もとの値に戻す
		update_option( 'vkExUnit_common_options', $before_vkExUnit_common_options );

	}
}
