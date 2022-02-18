<?php
/**
 * PHP Unit Test Helpers for WordPress
 * テストをするにあたって必要な下準備をするための処理
 *
 * @package VK WP Unit Test Tools
 * @version 0.10.0
 */

namespace VK_WP_Unit_Test_Tools;

/**
 * VkWpUnitTestHelpers
 */
class VkWpUnitTestHelpers {

	/**
	 * PHP Unit テストにあたって、各種投稿やカスタム投稿タイプ、カテゴリーを登録します。
	 *
	 * @return array $test_posts : 作成した投稿の記事idなどを配列で返します。
	 */
	public static function create_test_posts() {

		$test_posts = array();

		/******************************************
		 * カテゴリーの登録 */

		// 親カテゴリー parent_category を登録.
		$catarr                           = array(
			'cat_name' => 'parent_category',
		);
		$test_posts['parent_category_id'] = wp_insert_category( $catarr );

		// 子カテゴリー child_category を登録.
		$catarr                          = array(
			'cat_name'        => 'child_category',
			'category_parent' => $test_posts['parent_category_id'],
		);
		$test_posts['child_category_id'] = wp_insert_category( $catarr );

		// 投稿を割り当てないカテゴリー no_post_category を登録.
		$catarr                            = array(
			'cat_name' => 'no_post_category',
		);
		$test_posts['no_post_category_id'] = wp_insert_category( $catarr );

		/******************************************
		 * 投稿タイプ event を追加 */
		register_post_type(
			'event',
			array(
				'label'       => 'Event',
				'has_archive' => true,
				'public'      => true,
			)
		);

		/******************************************
		 * カスタム分類 event_cat を追加 */
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		/******************************************
		 * カスタム分類 の登録 */
		$args                        = array(
			'slug' => 'event_category_name',
		);
		$term_info                   = wp_insert_term( 'event_category_name', 'event_cat', $args );
		$test_posts['event_term_id'] = $term_info['term_id'];

		/******************************************
		 * テスト用投稿の登録 */

		// 通常の投稿 Test Post を投稿.
		$post                  = array(
			'post_title'    => 'Test Post',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $test_posts['parent_category_id'] ),
		);
		$test_posts['post_id'] = wp_insert_post( $post );
		// 投稿にカテゴリー指定.
		wp_set_object_terms( $test_posts['post_id'], 'child_category', 'category' );

		// 固定ページ Parent Page を投稿.
		$post                         = array(
			'post_title'   => 'Parent Page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['parent_page_id'] = wp_insert_post( $post );

		// 固定ページの子ページ Child Page を投稿.
		$post = array(
			'post_title'   => 'Child Page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
			'post_parent'  => $test_posts['parent_page_id'],

		);
		$test_posts['child_page_id'] = wp_insert_post( $post );

		// 投稿トップ用の固定ページ Post Top を投稿.
		$post                       = array(
			'post_title'   => 'Post Top',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['home_page_id'] = wp_insert_post( $post );

		// フロントページ用の固定ページ Front Page を投稿.
		$post                        = array(
			'post_title'   => 'Front Page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['front_page_id'] = wp_insert_post( $post );

		// カスタム投稿タイプ event 用の Event Test Post を投稿.
		$post                        = array(
			'post_title'   => 'Event Test Post',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['event_post_id'] = wp_insert_post( $post );

		// 作成した Event Test Post にイベントカテゴリーを指定.
		wp_set_object_terms( $test_posts['event_post_id'], 'event_category_name', 'event_cat' );

		return $test_posts;
	}

}
