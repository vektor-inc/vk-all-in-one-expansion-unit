<?php
/**
 * Class WpTitleTest
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

/**
 * wp title test case.
 */
class WpTitleTest extends WP_UnitTestCase {

	/**
	 * vkExUnit_wp_title_validate() は extend_frontTitle をエスケープし、
	 * 入力欠落や null（メイン設定の一括保存で当該グループが未送信）でも Notice/Deprecation を出さず空文字を返す。
	 */
	public function test_vkExUnit_wp_title_validate() {
		$test_cases = array(
			array(
				'test_condition_name' => 'extend_frontTitle が指定されている場合 => 値を返す（正常系）',
				'input'               => array( 'extend_frontTitle' => 'My Front Title' ),
				'expected'            => array( 'extend_frontTitle' => 'My Front Title' ),
			),
			array(
				'test_condition_name' => 'HTML特殊文字を含む場合 => htmlspecialchars でエスケープされる（正常系）',
				'input'               => array( 'extend_frontTitle' => 'Site<Title>' ),
				'expected'            => array( 'extend_frontTitle' => 'Site&lt;Title&gt;' ),
			),
			array(
				'test_condition_name' => 'キーを持たない配列 => 空文字を返す（Undefined array key を出さない・境界値）',
				'input'               => array(),
				'expected'            => array( 'extend_frontTitle' => '' ),
			),
			array(
				'test_condition_name' => 'null => 空文字を返す（offset-on-null / htmlspecialchars(null) を出さない・異常系）',
				'input'               => null,
				'expected'            => array( 'extend_frontTitle' => '' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = vkExUnit_wp_title_validate( $case['input'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * PHP Unit テストにあたって、各種投稿やカスタム投稿タイプ、カテゴリーを登録します。
	 *
	 * @return array $test_posts : 作成した投稿の記事idなどを配列で返します。
	 */
	public static function create_test_posts() {

		$test_posts = array();

		/******************************************
		 * テスト用投稿の登録 */

		// 通常の投稿 Test Post を投稿.
		$post                  = array(
			'post_title'   => 'Test Post',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['post_id'] = wp_insert_post( $post );

		// 固定ページ Parent Page を投稿.
		$post                         = array(
			'post_title'   => 'Parent Page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['parent_page_id'] = wp_insert_post( $post );

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

		// カスタム投稿タイプ event の投稿 Test Event を投稿.
		$post                   = array(
			'post_title'   => 'Test Event',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Test Event',
		);
		$test_posts['event_id'] = wp_insert_post( $post );

		/******************************************
		 * テスト用タクソノミーの登録 */

		// カテゴリー Test Category を登録
		$category_result = wp_insert_term( 'Test Category', 'category' );
		if ( ! is_wp_error( $category_result ) ) {
			$test_posts['category_id'] = $category_result['term_id'];
		}

		// タグ Test Tag を登録
		$tag_result = wp_insert_term( 'Test Tag', 'post_tag' );
		if ( ! is_wp_error( $tag_result ) ) {
			$test_posts['tag_id'] = $tag_result['term_id'];
		}

		// テスト用カスタムタクソノミー event_category を登録
		if ( ! taxonomy_exists( 'event_category' ) ) {
			register_taxonomy(
				'event_category',
				'event',
				array(
					'label'        => 'Test Taxonomy',
					'public'       => true,
					'rewrite'      => false,
					'hierarchical' => false,
				)
			);
		}

		// カスタムタクソノミー event_category のタームを登録
		$custom_tax_result = wp_insert_term( 'Test Event Category', 'event_category' );
		if ( ! is_wp_error( $custom_tax_result ) ) {
			$test_posts['custom_tax_id'] = $custom_tax_result['term_id'];
		}

		return $test_posts;
	}

	/**
	 * test_vkExUnit_get_wp_head_title
	 */
	public function test_vkExUnit_get_wp_head_title() {

		$test_posts = self::create_test_posts();
		$sep        = ' | ';

		/*
		Test Array
		/*--------------------------------*/
		$test_array = array(

			// 投稿ページ / カスタムタイトル : 指定あり / サイト名追加 : 無し
			// Return : カスタムタイトル
			array(
				'target_url'    => get_permalink( $test_posts['post_id'] ),
				'target_id'     => $test_posts['post_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Post Custom Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Post Custom Title',
			),
			// 投稿ページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_permalink( $test_posts['post_id'] ),
				'target_id'     => $test_posts['post_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Post Custom Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Post Custom Title' . $sep . 'Site name',
			),
			// 投稿ページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : 投稿タイトル + セパレータ + サイト名
			array(
				'target_url'    => get_permalink( $test_posts['post_id'] ),
				'target_id'     => $test_posts['post_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'Test Post' . $sep . 'Site name',
			),
			// 固定ページ / カスタムタイトル : 指定あり / サイト名追加 : なし
			// Return : カスタムタイトル
			array(
				'target_url'    => get_permalink( $test_posts['parent_page_id'] ),
				'target_id'     => $test_posts['parent_page_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Custom Page Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Custom Page Title',
			),
			// 固定ページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_permalink( $test_posts['parent_page_id'] ),
				'target_id'     => $test_posts['parent_page_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Custom Page Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Custom Page Title' . $sep . 'Site name',
			),
			// トップページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : メイン設定画面でのカスタムタイトル
			array(
				'target_url'    => home_url( '/' ),
				'target_id'     => $test_posts['front_page_id'],
				'options'       => array(
					'blogname'          => 'Site name',
					'vkExUnit_wp_title' => array(
						'extend_frontTitle' => 'ExUnit Front Page Title',
					),
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'ExUnit Front Page Title',
			),
			// トップページ / サイトの説明が空
			// Return : サイト名のみ（セパレータなし）
			array(
				'target_url'    => home_url( '/' ),
				'target_id'     => $test_posts['front_page_id'],
				'options'       => array(
					'blogname'          => 'Site name',
					'blogdescription'   => '',
					'show_on_front'     => 'posts',
					'vkExUnit_wp_title' => array(),
				),
				'custom_fields' => array(),
				'expected'      => 'Site name',
			),
			/***********************************************************
			 * トップページ仕様変更
			 *
			 * @since 9.84.0.0
			 */
			// トップページ / カスタムタイトル : 指定あり / サイト名追加 : なし
			// Return : トップページに指定した固定ページで指定したカスタムタイトル
			array(
				'target_url'    => home_url( '/' ),
				'target_id'     => $test_posts['front_page_id'],
				'options'       => array(
					'blogname'          => 'Site name',
					'page_on_front'     => $test_posts['front_page_id'],
					'show_on_front'     => 'page',
					'page_for_posts'    => $test_posts['home_page_id'],
					'vkExUnit_wp_title' => array(
						'extend_frontTitle' => 'ExUnit Front Page Title',
					),
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Front Page Custom Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Front Page Custom Title',
			),

			// eventの投稿ページ / カスタムタイトル : 指定あり / サイト名追加 : 無し
			// Return : カスタムタイトル
			array(
				'target_url'    => get_permalink( $test_posts['event_id'] ),
				'target_id'     => $test_posts['event_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Event Custom Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Event Custom Title',
			),
			// eventの投稿ページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_permalink( $test_posts['event_id'] ),
				'target_id'     => $test_posts['event_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => 'Event Custom Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Event Custom Title' . $sep . 'Site name',
			),
			// eventの投稿ページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : 投稿タイトル + セパレータ + サイト名
			array(
				'target_url'    => get_permalink( $test_posts['event_id'] ),
				'target_id'     => $test_posts['event_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_head_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'Test Event' . $sep . 'Site name',
			),
			// カテゴリーアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : なし
			// Return : カスタムタイトル
			array(
				'target_url'    => get_term_link( $test_posts['category_id'] ),
				'target_id'     => $test_posts['category_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Category Custom Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Category Custom Title',
			),
			// カテゴリーアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['category_id'] ),
				'target_id'     => $test_posts['category_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Category Custom Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Category Custom Title' . $sep . 'Site name',
			),
			// カテゴリーアーカイブページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : カテゴリー名 + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['category_id'] ),
				'target_id'     => $test_posts['category_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'Test Category' . $sep . 'Site name',
			),
			// タグアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : なし
			// Return : カスタムタイトル
			array(
				'target_url'    => get_term_link( $test_posts['tag_id'] ),
				'target_id'     => $test_posts['tag_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Tag Custom Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Tag Custom Title',
			),
			// タグアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['tag_id'] ),
				'target_id'     => $test_posts['tag_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Tag Custom Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Tag Custom Title' . $sep . 'Site name',
			),
			// タグアーカイブページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : タグ名 + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['tag_id'] ),
				'target_id'     => $test_posts['tag_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'Test Tag' . $sep . 'Site name',
			),
			// カスタムタクソノミーアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : なし
			// Return : カスタムタイトル
			array(
				'target_url'    => get_term_link( $test_posts['custom_tax_id'] ),
				'target_id'     => $test_posts['custom_tax_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Custom Taxonomy Title',
						'add_site_title' => false,
					),
				),
				'expected'      => 'Custom Taxonomy Title',
			),
			// カスタムタクソノミーアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : あり
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['custom_tax_id'] ),
				'target_id'     => $test_posts['custom_tax_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => 'Custom Taxonomy Title',
						'add_site_title' => true,
					),
				),
				'expected'      => 'Custom Taxonomy Title' . $sep . 'Site name',
			),
			// カスタムタクソノミーアーカイブページ / カスタムタイトル : 指定なし / サイト名追加 : なし
			// Return : タクソノミー名 + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['custom_tax_id'] ),
				'target_id'     => $test_posts['custom_tax_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'          => null,
						'add_site_title' => false,
					),
				),
				'expected'      => 'Test Event Category' . $sep . 'Site name',
			),
			// Category archive / custom title: set / add site title: off / reflect heading: ON.
			// Return: custom title (the reflect heading flag does not affect <title>).
			// カテゴリーアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : なし / 見出し反映 : ON
			// Return : カスタムタイトル（見出し反映フラグは <title> に影響しない）
			array(
				'target_url'    => get_term_link( $test_posts['category_id'] ),
				'target_id'     => $test_posts['category_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'           => 'Category Custom Title',
						'add_site_title'  => false,
						'reflect_heading' => 1,
					),
				),
				'expected'      => 'Category Custom Title',
			),
			// Tag archive / custom title: set / add site title: on / reflect heading: ON.
			// Return: custom title + separator + site name.
			// タグアーカイブページ / カスタムタイトル : 指定あり / サイト名追加 : あり / 見出し反映 : ON
			// Return : カスタムタイトル + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['tag_id'] ),
				'target_id'     => $test_posts['tag_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'           => 'Tag Custom Title',
						'add_site_title'  => true,
						'reflect_heading' => 1,
					),
				),
				'expected'      => 'Tag Custom Title' . $sep . 'Site name',
			),
			// Custom taxonomy archive / custom title: empty / reflect heading: ON.
			// Return: term name + separator + site name.
			// カスタムタクソノミーアーカイブページ / カスタムタイトル : 指定なし / 見出し反映 : ON
			// Return : タクソノミー名 + セパレータ + サイト名
			array(
				'target_url'    => get_term_link( $test_posts['custom_tax_id'] ),
				'target_id'     => $test_posts['custom_tax_id'],
				'options'       => array(
					'blogname' => 'Site name',
				),
				'custom_fields' => array(
					'veu_taxonomy_title' => array(
						'title'           => '',
						'add_site_title'  => false,
						'reflect_heading' => 1,
					),
				),
				'expected'      => 'Test Event Category' . $sep . 'Site name',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_head_title' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			if ( ! empty( $value['custom_fields'] ) && is_array( $value['custom_fields'] ) ) {
				foreach ( $value['custom_fields'] as $cf_key => $cf_value ) {
					if ( $cf_key === 'veu_taxonomy_title' ) {
						// タクソノミーのメタデータを設定
						update_term_meta( $value['target_id'], $cf_key, $cf_value );
					} else {
						// 投稿のメタデータを設定
						update_post_meta( $value['target_id'], $cf_key, $cf_value );
					}
				}
			}

			// Move to test page
			$this->go_to( $value['target_url'] );

			$actual = vkExUnit_get_wp_head_title();

			// print PHP_EOL;
			// print $value['target_url'] . PHP_EOL;
			// print 'expected::::' . $value['expected'] . PHP_EOL;
			// print 'actual  ::::' . $actual . PHP_EOL;

			$this->assertEquals( $value['expected'], $actual );

			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
			if ( ! empty( $value['custom_fields'] ) && is_array( $value['custom_fields'] ) ) {
				foreach ( $value['custom_fields'] as $cf_key => $cf_value ) {
					if ( $cf_key === 'veu_taxonomy_title' ) {
						// タクソノミーのメタデータを削除
						delete_term_meta( $value['target_id'], $cf_key );
					} else {
						// 投稿のメタデータを削除
						delete_post_meta( $value['target_id'], $cf_key );
					}
				}
			}
		}
	}

	/**
	 * Reproduces a theme filter that rebuilds the archive title at priority 10.
	 * テーマが優先度 10 でアーカイブタイトルを作り直すフィルターを再現する（Lightning / X-T9 相当）。
	 *
	 * @return string
	 */
	public static function filter_theme_rebuilt_archive_title() {
		return 'Theme Rebuilt Title';
	}

	/**
	 * Tests veu_filter_term_archive_title().
	 * veu_filter_term_archive_title() のテスト
	 * Checks that the archive heading is replaced only when the term's reflect heading flag is ON and a title is entered.
	 * タームの見出し反映フラグが ON かつタイトル入力済みのときだけ、アーカイブ見出しが置き換わることを確認する。
	 */
	public function test_veu_filter_term_archive_title() {

		$test_posts = self::create_test_posts();

		// Another category without the setting.
		// 見出し反映の対象にしない別カテゴリー
		$other_category = wp_insert_term( 'Other Category', 'category' );
		$other_cat_id   = $other_category['term_id'];

		$test_cases = array(
			array(
				'test_condition_name' => 'カテゴリー / ON + タイトルあり => 入力値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Category Heading',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON + タイトルあり + サイト名追加あり => 入力値のみ（サイト名は付かない）',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 1,
					'reflect_heading' => 1,
				),
				'expected'            => 'Category Heading',
			),
			array(
				'test_condition_name' => 'タグ / ON + タイトルあり => 入力値',
				'target_url'          => get_term_link( $test_posts['tag_id'] ),
				'term_id'             => $test_posts['tag_id'],
				'term_meta'           => array(
					'title'           => 'Tag Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Tag Heading',
			),
			array(
				'test_condition_name' => 'カスタムタクソノミー / ON + タイトルあり => 入力値',
				'target_url'          => get_term_link( $test_posts['custom_tax_id'] ),
				'term_id'             => $test_posts['custom_tax_id'],
				'term_meta'           => array(
					'title'           => 'Custom Tax Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Custom Tax Heading',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON + HTML・& を含むタイトル => 一度だけエスケープされる',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'A & B <b>x</b>',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'A &amp; B &lt;b&gt;x&lt;/b&gt;',
			),
			array(
				'test_condition_name' => 'カテゴリー / OFF + タイトルあり => 元の値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 0,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'タグ / フラグキー無しの既存データ => 元の値',
				'target_url'          => get_term_link( $test_posts['tag_id'] ),
				'term_id'             => $test_posts['tag_id'],
				'term_meta'           => array(
					'title'          => 'Tag Heading',
					'add_site_title' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カスタムタクソノミー / フラグキー無しの既存データ => 元の値',
				'target_url'          => get_term_link( $test_posts['custom_tax_id'] ),
				'term_id'             => $test_posts['custom_tax_id'],
				'term_meta'           => array(
					'title'          => 'Custom Tax Heading',
					'add_site_title' => 0,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON + タイトル空 => 元の値（境界値）',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => '',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'term meta 自体が無い => 元の値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => null,
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => '別カテゴリーが ON でも、設定の無いカテゴリーのアーカイブ => 元の値',
				'target_url'          => get_term_link( $other_cat_id ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カテゴリーが ON でも日付アーカイブ => 元の値',
				'target_url'          => get_year_link( 2026 ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON でもフィード => 元の値',
				'target_url'          => get_category_feed_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'term meta が文字列 foo（異常値） => 元の値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => 'foo',
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'reflect_heading が文字列 0（異常値） => 元の値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => '0',
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'title が配列（DB 破損などの異常値） + ON => 元の値（Array to string conversion を出さない）',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => array( 'broken' ),
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON でも管理画面 => 元の値',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'admin_screen'        => 'edit',
				'expected'            => 'Theme Rebuilt Title',
			),
			array(
				'test_condition_name' => 'カテゴリー / ON + テーマの作り直しフィルター無し => 入力値（WP 標準のタイトルも接頭辞ごと置き換わる）',
				'target_url'          => get_term_link( $test_posts['category_id'] ),
				'term_id'             => $test_posts['category_id'],
				'term_meta'           => array(
					'title'           => 'Category Heading',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'without_theme'       => true,
				'expected'            => 'Category Heading',
			),
		);

		foreach ( $test_cases as $case ) {
			// Reproduce a theme filter (e.g. Lightning) that discards the value and rebuilds it at priority 10.
			// テーマ（Lightning 等）が優先度 10 で値を捨てて作り直すフィルターを再現
			if ( empty( $case['without_theme'] ) ) {
				add_filter( 'get_the_archive_title', array( __CLASS__, 'filter_theme_rebuilt_archive_title' ), 10 );
			}

			// Set the term meta.
			// term meta を設定
			if ( null !== $case['term_meta'] ) {
				update_term_meta( $case['term_id'], 'veu_taxonomy_title', $case['term_meta'] );
			}

			// Go to the target page (and switch to the admin screen if needed).
			// テスト対象ページへ移動（必要なら管理画面状態にする）
			$this->go_to( $case['target_url'] );
			if ( ! empty( $case['admin_screen'] ) ) {
				set_current_screen( $case['admin_screen'] );
			}

			$actual = get_the_archive_title();

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			// Clean up.
			// 後片付け
			if ( ! empty( $case['admin_screen'] ) ) {
				set_current_screen( 'front' );
			}
			remove_filter( 'get_the_archive_title', array( __CLASS__, 'filter_theme_rebuilt_archive_title' ), 10 );
			delete_term_meta( $case['term_id'], 'veu_taxonomy_title' );
		}
	}

	/**
	 * Tests VEU_Taxonomy_Head_Title::save_title_field().
	 * VEU_Taxonomy_Head_Title::save_title_field() のテスト
	 * Checks saving the reflect heading flag (0 when unchecked) and deleting the meta when every value is empty.
	 * 見出し反映フラグの保存（未チェック時 0）と、全項目が空の場合の削除を確認する。
	 */
	public function test_save_title_field() {
		$test_posts = self::create_test_posts();
		$term_id    = $test_posts['category_id'];

		// Log in as a user with the capability.
		// 権限のあるユーザーでログイン
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$test_cases = array(
			array(
				'test_condition_name' => 'タイトルあり + 見出し反映チェックあり => reflect_heading 1 で保存',
				'post'                => array(
					'title'           => 'Saved Title',
					'reflect_heading' => 'on',
				),
				'expected'            => array(
					'title'           => 'Saved Title',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
			),
			array(
				'test_condition_name' => 'タイトルあり + サイト名追加あり + 見出し反映未チェック => reflect_heading 0 で保存',
				'post'                => array(
					'title'          => 'Saved Title',
					'add_site_title' => 'on',
				),
				'expected'            => array(
					'title'           => 'Saved Title',
					'add_site_title'  => 1,
					'reflect_heading' => 0,
				),
			),
			array(
				'test_condition_name' => 'タイトル空 + 見出し反映チェックのみ => 削除されずに保存',
				'post'                => array(
					'title'           => '',
					'reflect_heading' => 'on',
				),
				'expected'            => array(
					'title'           => '',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
			),
			array(
				'test_condition_name' => 'タイトル空 + サイト名追加のみチェック => 削除されずに保存（従来挙動）',
				'post'                => array(
					'title'          => '',
					'add_site_title' => 'on',
				),
				'expected'            => array(
					'title'           => '',
					'add_site_title'  => 1,
					'reflect_heading' => 0,
				),
			),
			array(
				'test_condition_name' => 'veu_taxonomy_title 自体が送信されない => 既存の meta がそのまま残る（異常系）',
				'post'                => null,
				'expected'            => array(
					'title'           => 'Before',
					'add_site_title'  => 1,
					'reflect_heading' => 1,
				),
			),
			array(
				'test_condition_name' => '全項目が空 => term meta を削除（境界値）',
				'post'                => array(
					'title' => '',
				),
				'expected'            => '',
			),
		);

		$instance = new VEU_Taxonomy_Head_Title();

		foreach ( $test_cases as $case ) {
			// Store a different value first to check it is overwritten or deleted.
			// 事前に別の値を入れておき、上書き・削除されることを確認する
			update_term_meta(
				$term_id,
				'veu_taxonomy_title',
				array(
					'title'           => 'Before',
					'add_site_title'  => 1,
					'reflect_heading' => 1,
				)
			);

			// Reproduce the submission from the edit screen (null means the field group is not submitted).
			// 編集画面からの送信を再現（null はフィールド自体が送信されない場合）
			$_POST['_wpnonce'] = wp_create_nonce( 'update-tag_' . $term_id );
			if ( null !== $case['post'] ) {
				$_POST['veu_taxonomy_title'] = $case['post'];
			}

			$instance->save_title_field( $term_id );

			$actual = get_term_meta( $term_id, 'veu_taxonomy_title', true );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			// Clean up.
			// 後片付け
			unset( $_POST['_wpnonce'], $_POST['veu_taxonomy_title'] );
			delete_term_meta( $term_id, 'veu_taxonomy_title' );
		}
	}

	/**
	 * Tests VEU_Title_Form_Helper::render_post_form().
	 * VEU_Title_Form_Helper::render_post_form() のテスト
	 * Checks that the post form output (meta box and block editor panel) is identical to before #1490.
	 * 投稿用フォーム（メタボックス・ブロックエディタパネル）の出力が #1490 の変更前と同一であることを確認する。
	 */
	public function test_render_post_form() {
		$test_cases = array(
			array(
				'test_condition_name' => 'タイトルあり + サイト名追加あり => 変更前と同一の HTML',
				'post_meta'           => array(
					'title'          => 'Post Title',
					'add_site_title' => 1,
				),
				'expected'            => '<input type="text" name="veu_head_title[title]" value="Post Title" /><p>If there is any input here, the input will be reflected in the title tag.</p><p>Please note that the notation on the page will not be rewritten.</p><label><input type="checkbox" name="veu_head_title[add_site_title]"  checked=\'checked\' />Add Separator and Site Title</label>',
			),
			array(
				'test_condition_name' => 'タイトルなし + サイト名追加なし => 変更前と同一の HTML',
				'post_meta'           => array(
					'title'          => '',
					'add_site_title' => 0,
				),
				'expected'            => '<input type="text" name="veu_head_title[title]" value="" /><p>If there is any input here, the input will be reflected in the title tag.</p><p>Please note that the notation on the page will not be rewritten.</p><label><input type="checkbox" name="veu_head_title[add_site_title]"  />Add Separator and Site Title</label>',
			),
			array(
				'test_condition_name' => 'meta に reflect_heading が入っていても => 見出し反映チェックボックスは出ない',
				'post_meta'           => array(
					'title'           => 'Post Title',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => '<input type="text" name="veu_head_title[title]" value="Post Title" /><p>If there is any input here, the input will be reflected in the title tag.</p><p>Please note that the notation on the page will not be rewritten.</p><label><input type="checkbox" name="veu_head_title[add_site_title]"  />Add Separator and Site Title</label>',
			),
			array(
				'test_condition_name' => 'meta が空文字（未保存の投稿） => 変更前と同一の HTML（異常系）',
				'post_meta'           => '',
				'expected'            => '<input type="text" name="veu_head_title[title]" value="" /><p>If there is any input here, the input will be reflected in the title tag.</p><p>Please note that the notation on the page will not be rewritten.</p><label><input type="checkbox" name="veu_head_title[add_site_title]"  />Add Separator and Site Title</label>',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = VEU_Title_Form_Helper::render_post_form( 'veu_head_title', $case['post_meta'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Tests VEU_Title_Form_Helper::render_taxonomy_form_row().
	 * VEU_Title_Form_Helper::render_taxonomy_form_row() のテスト
	 * Checks that the term edit screen shows the reflect heading checkbox and not the page notation note.
	 * タームの編集画面に見出し反映チェックボックスが出て、ページ表記の注意書きが出ないことを確認する。
	 */
	public function test_render_taxonomy_form_row() {
		$checkbox_unchecked = '<input type="checkbox" id="veu_taxonomy_title_reflect_heading" name="veu_taxonomy_title[reflect_heading]" value="1" aria-describedby="veu_taxonomy_title_reflect_heading_description veu_taxonomy_title_reflect_heading_og_title_description" />';
		$checkbox_checked   = '<input type="checkbox" id="veu_taxonomy_title_reflect_heading" name="veu_taxonomy_title[reflect_heading]" value="1" aria-describedby="veu_taxonomy_title_reflect_heading_description veu_taxonomy_title_reflect_heading_og_title_description" checked=\'checked\' />';

		$test_cases = array(
			array(
				'test_condition_name' => 'reflect_heading 1 => チェック済みで出力',
				'term_meta'           => array(
					'title'           => 'Term Title',
					'add_site_title'  => 0,
					'reflect_heading' => 1,
				),
				'expected'            => array(
					'checkbox'     => $checkbox_checked,
					'has_old_note' => false,
				),
			),
			array(
				'test_condition_name' => 'reflect_heading キー無しの既存データ => 未チェックで出力',
				'term_meta'           => array(
					'title'          => 'Term Title',
					'add_site_title' => 1,
				),
				'expected'            => array(
					'checkbox'     => $checkbox_unchecked,
					'has_old_note' => false,
				),
			),
			array(
				'test_condition_name' => 'meta が文字列 foo（異常値） => 未チェックで出力・warning なし',
				'term_meta'           => 'foo',
				'expected'            => array(
					'checkbox'     => $checkbox_unchecked,
					'has_old_note' => false,
				),
			),
			array(
				'test_condition_name' => 'meta が空文字（未設定のターム） => 未チェックで出力（異常系）',
				'term_meta'           => '',
				'expected'            => array(
					'checkbox'     => $checkbox_unchecked,
					'has_old_note' => false,
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$html = VEU_Title_Form_Helper::render_taxonomy_form_row( 'veu_taxonomy_title', $case['term_meta'] );

			$actual = array(
				'checkbox'     => false !== strpos( $html, $case['expected']['checkbox'] ) ? $case['expected']['checkbox'] : $html,
				'has_old_note' => false !== strpos( $html, 'Please note that the notation on the page will not be rewritten.' ),
			);
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			// The description ids referenced by aria-describedby must exist.
			// 説明文の id が aria-describedby の参照先として存在すること
			$this->assertStringContainsString( '<p id="veu_taxonomy_title_reflect_heading_description" class="description">', $html, $case['test_condition_name'] );
			$this->assertStringContainsString( '<p id="veu_taxonomy_title_reflect_heading_og_title_description" class="description">', $html, $case['test_condition_name'] );
			$this->assertStringContainsString( '<label for="veu_taxonomy_title_reflect_heading">', $html, $case['test_condition_name'] );

			// The og:title note must say it uses the entered text (a site name may be appended depending on the SNS settings).
			// og:title の説明が「入力内容を使う」になっていること（SNS 設定によってサイト名が付くため「同じ」とは書かない）
			$this->assertStringContainsString( 'The title used when shared on social media (og:title) will also use the entered text.', $html, $case['test_condition_name'] );
		}
	}
}
