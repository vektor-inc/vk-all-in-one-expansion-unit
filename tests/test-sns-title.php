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
	public static function setup_data() {

		/*** ↓↓ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		// 投稿タイプイベントを作成
		register_post_type(
			'event',
			array(
				'label'       => 'Event',
				'has_archive' => true,
				'public'      => true,
			)
		);

		// イベントにイベントカテゴリーを関連付け
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// カテゴリ「親カテゴリ」を追加
		$catarr             = array(
			'cat_name'          => 'Category Test',
			'category_nicename' => 'category-test',
		);
		$data['category_id'] = wp_insert_category( $catarr );

		// イベントカテゴリ「イベントカテゴリ」を追加
		$args          = array(
			'slug' => 'event-category-test',
		);
		$term_info     = wp_insert_term( 'Event Category Test', 'event_cat', $args );
		$data['event_cat_id'] = $term_info['term_id'];

		// 投稿「テスト01」を追加
		$post    = array(
			'post_title'    => 'Post Test',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id'] = wp_insert_post( $post );

		// Create test Home
		$post         = array(
			'post_title'   => 'Page Test',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test',
		);
		$data['page_id'] = wp_insert_post( $post );

		// Create test Home
		$post         = array(
			'post_title'   => 'Home',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Home',
		);
		$data['home_page_id'] = wp_insert_post( $post );

		// Create test Home
		$post          = array(
			'post_title'   => 'Front Page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Front Page',
		);
		$data['front_page_id'] = wp_insert_post( $post );

		// custom post type.
		$post          = array(
			'post_title'   => 'Event Test',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test',
		);
		$data['event_id'] = wp_insert_post( $post );
		// set event category to event post
		wp_set_object_terms( $data['event_id'], 'event_category_name', 'event_cat' );

		update_option( 'page_on_front', $data['front_page_id'] ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $data['home_page_id'] ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		return $data;

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */
	}

	/**
	 * SNSタイトル書き換えのテスト
	 */
	public function test_veu_get_the_sns_title() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_sns_title' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		$data = self::setup_data();

		$page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		$test_array = array(
			/**
			 * フロントページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'ExUnitCustomFrontTitle',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'ExUnitCustomFrontTitle',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'ExUnitCustomFrontTitle',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Site Name',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'ExUnitCustomFrontTitle',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_front_page',
				'target_url'                               => home_url( '/' ),
				'target_id'                                => $data['front_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Site Name',
			),
			/**
			 * 投稿トップページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_home',
				'target_url'                               => get_permalink( $page_for_posts ),
				'target_id'                                => $data['home_page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Home',
			),
			/**
			 * 投稿タイプ「イベント」のアーカイブページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?post_type=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Archives: Event',
			),
			/**
			 * 404ページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_404',
				'target_url'                               => home_url( '/?name=event' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Not found',
			),
			/**
			 * 検索結果ページ（キーワードなし）
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url( '/?s=' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results',
			),
			/**
			 * 検索結果ページ（キーワードあり）
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_search',
				'target_url'                               => home_url() . '/?s=Test',
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Search Results for : Test',
			),
			/**
			 * カテゴリーアーカイブ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?category_name=category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Category: Category Test',
			),
			/**
			 * イベントカテゴリーアーカイブ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test | Site Name',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_archive',
				'target_url'                               => home_url( '/?event_cat=event-category-test' ),
				'target_id'                                => null,
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Category: Event Category Test',
			),
			/**
			 * 固定ページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Page Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Page Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Page Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Page Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['page_id'] ),
				'target_id'                                => $data['page_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			/**
			 * 投稿詳細ページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Post Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Post Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Post Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Post Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名あなし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['post_id'] ),
				'target_id'                                => $data['post_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			/**
			 * イベント詳細ページ
			 */
			// サイト名あり + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Test | Site Name',
			),
			// サイト名あり + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Test | Site Name',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名あり + SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => false,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Test',
			),
			// サイト名なし + SNS タイトルなし + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => null,
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Event Test',
			),
			// サイト名なし + SNS タイトルあり + タイトル書き換えあり
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => true,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
			// サイト名なし+ SNS タイトルあり + タイトル書き換えなし
			array(
				'target_type'                              => 'is_singular',
				'target_url'                               => get_permalink( $data['event_id'] ),
				'target_id'                                => $data['event_id'],
				'sns_options__snsTitle_use_only_postTitle' => true,
				'vkExUnit_sns_title'                       => 'Custom Title',
				'vkExUnit_wp_title'                        => array( 'extend_frontTitle' => 'ExUnitCustomFrontTitle' ),
				'package_wp_title'                         => false,
				'site_name'                                => 'Site Name',
				'correct'                                  => 'Custom Title',
			),
		);

		$before_blogname                = get_option( 'blogname' );
		$before_vkExUnit_sns_options    = get_option( 'vkExUnit_sns_options' );
		$before_vkExUnit_wp_title       = get_option( 'vkExUnit_wp_title' );
		$before_vkExUnit_common_options = get_option( 'vkExUnit_common_options' );

		foreach ( $test_array as $key => $test_value ) {

			$post_id = $test_value['target_id'];

			// Set Site Name
			update_option( 'blogname', $test_value['site_name'] );

			// サイトタイトルの表示有無
			$vkExUnit_sns_options['snsTitle_use_only_postTitle'] = $test_value['sns_options__snsTitle_use_only_postTitle'];
			update_option( 'vkExUnit_sns_options', $vkExUnit_sns_options );

			// SNS タイトルの設定
			if (
				'is_front_page' === $test_value['target_type'] ||
				'is_home' === $test_value['target_type'] ||
				'is_singular' === $test_value['target_type']
			) {
				if ($test_value['vkExUnit_sns_title'] !== null ) {
					add_post_meta( $post_id, 'vkExUnit_sns_title', $test_value['vkExUnit_sns_title'] );
				} else {
					delete_post_meta( $post_id, 'vkExUnit_sns_title' );
				}
			}
			update_option( 'vkExUnit_wp_title', $test_value['vkExUnit_wp_title'] );

			// タイトル書き換えの設定
			if ( $test_value['package_wp_title'] === false ) {
				$options = get_option( 'vkExUnit_common_options' );
				$options['active_wpTitle'] = false;
				update_option( 'vkExUnit_common_options', $options );
			} elseif ( $test_value['package_wp_title'] === true ) {
				$options = get_option( 'vkExUnit_common_options' );
				$options['active_wpTitle'] = true;
				update_option( 'vkExUnit_common_options', $options );
			}
			// URL に移動
			$this->go_to( $test_value['target_url'] );
			$return = veu_get_the_sns_title();

			// 取得できたHTMLが、意図したHTMLと等しいかテスト
			$this->assertEquals( $test_value['correct'], $return );

			print PHP_EOL;

			print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			print 'return  ::::' . $return . PHP_EOL;


		}

		foreach ( $data as $key => $value ) {
			delete_post_meta( $value, 'vkExUnit_sns_title' );
			wp_delete_post( $value );
		}

		wp_reset_postdata();
		wp_reset_query();

		// もとの値に戻す
		update_option( 'vkExUnit_sns_options', $before_vkExUnit_sns_options );
		update_option( 'blogname', $before_blogname );
		update_option( 'vkExUnit_wp_title', $before_vkExUnit_wp_title );
		update_option( 'vkExUnit_common_options', $before_vkExUnit_common_options );
		update_option( 'page_for_posts', $before_page_for_posts ); // 投稿トップに指定するページ
		update_option( 'page_on_front', $before_page_on_front ); // フロントに指定する固定ページ
		update_option( 'show_on_front', $before_show_on_front ); // トップページ指定するかどうか page or posts

	}
}
