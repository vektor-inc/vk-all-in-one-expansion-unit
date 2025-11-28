<?php
/**
 * Class VeuGetCommonOptionsDefaultTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Call to Action test case.
 */
class VeuGetCommonOptionsDefaultTest extends WP_UnitTestCase {

	/**
	 * クラシックテーマとブロックテーマでの初期値テスト
	 */
	function test_veu_get_common_options_default() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_common_options_default' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_array = array(
			array(
				'is_block_theme' => true,
				'correct'        => array(

					'active_fontawesome'                   => false,
					'active_wpTitle'                       => true,
					'active_addReusableBlockMenu'          => false,
					'active_add_plugin_link_to_admin_menu' => true,
					'active_sns'                           => true,
					'active_ga'                            => true,
					'active_google-tag-manager'            => false,
					'active_metaDescription'               => true,
					'active_breadcrumb'                    => false,
					'active_noindex'                       => true,
					'active_otherWidgets'                  => false,
					'active_archive_loop_before_widget_area' => false,
					'active_default_thumbnail'             => true,
					'active_css_customize'                 => true,
					'active_childPageIndex'                => true,
					'active_pageList_ancestor'             => true,
					'active_contact_section'               => false,
					'active_sitemap_page'                  => true,
					'active_call_to_action'                => true,
					'active_insert_ads'                    => false,
					'active_relatedPosts'                  => true,
					'active_disable_ping-back'             => false,
					'active_disable_dashbord'              => false,
					'active_disable_xml_sitemap'           => false,
					'active_disable_emoji'                 => false,
					'active_admin_bar'                     => true,
					'active_post_type_manager'             => true,
					'active_pagetop_button'                => true,
					'active_smooth_scroll'                 => true,
					'active_add_body_class'                => true,
					'active_nav_menu_class_custom'         => true,
					'active_css_optimize'                  => false,
					'active_auto_eyecatch'                 => false,
					'active_Contactform7AssetOptimize'     => false,
					'active_article_structure_data'        => true,
					'active_website_structure_data'        => true,
					'active_icon_accessibility'            => true,
					'active_page_exclude_from_list_pages'  => true,
					'post_metabox_individual'              => false,
					'delete_options_at_deactivate'         => false,
					'content_filter_state'                 => 'content',
					'active_promotion_alert'               => true,
				),
				array(
					'is_block_theme' => false,
					'correct'        => array(
						'active_fontawesome'               => false,
						'active_wpTitle'                   => true,
						'active_addReusableBlockMenu'      => true,
						'active_add_plugin_link_to_admin_menu' => true,
						'active_sns'                       => true,
						'active_ga'                        => true,
						'active_google-tag-manager'        => false,
						'active_metaDescription'           => true,
						'active_breadcrumb'                => false,
						'active_noindex'                   => true,
						'active_otherWidgets'              => true,
						'active_archive_loop_before_widget_area' => false,
						'active_default_thumbnail'         => true,
						'active_css_customize'             => true,
						'active_childPageIndex'            => true,
						'active_pageList_ancestor'         => true,
						'active_contact_section'           => true,
						'active_sitemap_page'              => true,
						'active_call_to_action'            => true,
						'active_insert_ads'                => true,
						'active_relatedPosts'              => true,
						'active_disable_ping-back'         => false,
						'active_disable_dashbord'          => false,
						'active_disable_xml_sitemap'       => false,
						'active_disable_emoji'             => false,
						'active_admin_bar'                 => true,
						'active_post_type_manager'         => true,
						'active_pagetop_button'            => true,
						'active_smooth_scroll'             => true,
						'active_add_body_class'            => true,
						'active_nav_menu_class_custom'     => true,
						'active_css_optimize'              => false,
						'active_auto_eyecatch'             => false,
						'active_Contactform7AssetOptimize' => false,
						'active_article_structure_data'    => true,
						'active_website_structure_data'    => true,
						'active_icon_accessibility'        => true,
						'active_page_exclude_from_list_pages' => true,
						'post_metabox_individual'          => false,
						'delete_options_at_deactivate'     => false,
						'content_filter_state'             => 'content',
					),

				),

			),
		);

		foreach ( $test_array as $key => $test_value ) {
			$return  = veu_get_common_options_default( $test_value['is_block_theme'] );
			$correct = $test_value['correct'];

			// print PHP_EOL;
			// print 'correct :' . PHP_EOL;
			// var_dump( $correct );
			// print 'return  :' . PHP_EOL;
			// var_dump( $return );

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $correct, $return );
		}
	}
}
