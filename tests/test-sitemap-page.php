<?php
/**
 * Class SitemapPageTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * HTML サイトマップの「除外タクソノミー」機能のテスト。
 * Test for the HTML sitemap "exclude taxonomy" feature.
 */
class SitemapPageTest extends WP_UnitTestCase {

	/**
	 * テスト用に登録した投稿タイプ・タクソノミー名を保持する。
	 * Holds the post type / taxonomy names registered for this test.
	 *
	 * @var string[]
	 */
	private $registered_post_types = array();

	/**
	 * @var string[]
	 */
	private $registered_taxonomies = array();

	/**
	 * veu_get_sitemap_post_types() のテスト。
	 * フィルターとオプション（excludePostTypes）による除外が正しく反映される事、
	 * public でない投稿タイプはそもそも対象にならない事を検証する。
	 *
	 * Test for veu_get_sitemap_post_types().
	 * Verifies that exclusion via the filter and the excludePostTypes option is
	 * reflected correctly, and that non-public post types are never included.
	 */
	function test_veu_get_sitemap_post_types() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_sitemap_post_types' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テスト用の投稿タイプを登録（cpt_a: 公開・除外対象外 / cpt_b: 非公開 / cpt_c: 公開・除外設定でオプション除外する対象）。
		// Register test post types ( cpt_a: public, not excluded / cpt_b: non-public / cpt_c: public, excluded via the option in some cases ).
		$this->register_test_post_type( 'veu_test_cpt_a', array( 'public' => true ) );
		$this->register_test_post_type( 'veu_test_cpt_b', array( 'public' => false ) );
		$this->register_test_post_type( 'veu_test_cpt_c', array( 'public' => true ) );

		$test_cases = array(
			array(
				'test_condition_name'       => '除外設定なしの場合 => 公開の投稿タイプのみが一覧に含まれる',
				'option_exclude_post_types' => array(),
				'expected_included'         => array( 'veu_test_cpt_a', 'veu_test_cpt_c' ),
				'expected_excluded'         => array( 'veu_test_cpt_b' ),
			),
			array(
				'test_condition_name'       => 'excludePostTypes で true を指定した投稿タイプが除外される',
				'option_exclude_post_types' => array( 'veu_test_cpt_c' => 'true' ),
				'expected_included'         => array( 'veu_test_cpt_a' ),
				'expected_excluded'         => array( 'veu_test_cpt_b', 'veu_test_cpt_c' ),
			),
			array(
				'test_condition_name'       => '境界値: excludePostTypes の値が空文字（falsy）の場合は除外されない',
				'option_exclude_post_types' => array( 'veu_test_cpt_c' => '' ),
				'expected_included'         => array( 'veu_test_cpt_a', 'veu_test_cpt_c' ),
				'expected_excluded'         => array( 'veu_test_cpt_b' ),
			),
		);

		foreach ( $test_cases as $case ) {
			update_option(
				'vkExUnit_sitemap_options',
				array( 'excludePostTypes' => $case['option_exclude_post_types'] )
			);

			$actual = veu_get_sitemap_post_types();

			foreach ( $case['expected_included'] as $post_type ) {
				$this->assertArrayHasKey( $post_type, $actual, $case['test_condition_name'] );
			}
			foreach ( $case['expected_excluded'] as $post_type ) {
				$this->assertArrayNotHasKey( $post_type, $actual, $case['test_condition_name'] );
			}

			delete_option( 'vkExUnit_sitemap_options' );
		}
	}

	/**
	 * veu_get_sitemap_available_taxonomies() のテスト。
	 * show_in_menu が有効で、サイトマップに出力される投稿タイプに紐づくタクソノミーだけが
	 * 一覧に含まれる事、投稿フォーマット等の内部タクソノミーが自動的に除外される事を検証する。
	 *
	 * Test for veu_get_sitemap_available_taxonomies().
	 * Verifies that only taxonomies with show_in_menu enabled and attached to a post type
	 * output on the sitemap are included, and that internal taxonomies such as post_format
	 * are automatically excluded.
	 */
	function test_veu_get_sitemap_available_taxonomies() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_sitemap_available_taxonomies' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_post_type( 'veu_test_cpt_a', array( 'public' => true ) );
		$this->register_test_post_type( 'veu_test_cpt_c', array( 'public' => true ) );

		// show_in_menu が有効なタクソノミーと無効なタクソノミーを cpt_a に紐付ける。
		// Attach a show_in_menu-enabled taxonomy and a disabled one to cpt_a.
		$this->register_test_taxonomy( 'veu_test_tax_shown', array( 'veu_test_cpt_a' ), array( 'show_in_menu' => true ) );
		$this->register_test_taxonomy( 'veu_test_tax_hidden', array( 'veu_test_cpt_a' ), array( 'show_in_menu' => false ) );
		// cpt_c にだけ紐付いたタクソノミー（cpt_c を除外設定した場合に一覧から消える事を確認するため）。
		// A taxonomy attached only to cpt_c ( to verify it disappears when cpt_c itself is excluded ).
		$this->register_test_taxonomy( 'veu_test_tax_on_c', array( 'veu_test_cpt_c' ), array( 'show_in_menu' => true ) );

		$test_cases = array(
			array(
				'test_condition_name'       => '除外設定なしの場合 => show_in_menu が有効なタクソノミーだけが一覧に含まれる',
				'option_exclude_post_types' => array(),
				'expected_included'         => array( 'veu_test_tax_shown', 'veu_test_tax_on_c' ),
				'expected_excluded'         => array( 'veu_test_tax_hidden' ),
			),
			array(
				'test_condition_name'       => '投稿タイプ自体が excludePostTypes で除外されると、紐づくタクソノミーも一覧から外れる',
				'option_exclude_post_types' => array( 'veu_test_cpt_c' => 'true' ),
				'expected_included'         => array( 'veu_test_tax_shown' ),
				'expected_excluded'         => array( 'veu_test_tax_hidden', 'veu_test_tax_on_c' ),
			),
			array(
				'test_condition_name'       => '境界値: 投稿フォーマット（post_format）のような内部タクソノミーは自動的に一覧から除外される',
				'option_exclude_post_types' => array(),
				'expected_included'         => array(),
				'expected_excluded'         => array( 'post_format' ),
			),
		);

		foreach ( $test_cases as $case ) {
			update_option(
				'vkExUnit_sitemap_options',
				array( 'excludePostTypes' => $case['option_exclude_post_types'] )
			);

			$actual = veu_get_sitemap_available_taxonomies();

			foreach ( $case['expected_included'] as $taxonomy ) {
				$this->assertArrayHasKey( $taxonomy, $actual, $case['test_condition_name'] );
			}
			foreach ( $case['expected_excluded'] as $taxonomy ) {
				$this->assertArrayNotHasKey( $taxonomy, $actual, $case['test_condition_name'] );
			}

			delete_option( 'vkExUnit_sitemap_options' );
		}
	}

	/**
	 * vkExUnit_sitemap() のテスト。
	 * excludeTaxonomies で指定したタクソノミーは、見出し（h5）ごとサイトマップから消える事、
	 * 無関係なキーが保存されていても他のタクソノミーの表示に影響しない事を検証する。
	 *
	 * Test for vkExUnit_sitemap().
	 * Verifies that a taxonomy specified in excludeTaxonomies disappears together with its
	 * heading ( h5 ), and that an unrelated stale key in the option does not affect the
	 * display of other taxonomies.
	 */
	function test_vkExUnit_sitemap() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vkExUnit_sitemap' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_post_type( 'veu_test_cpt_a', array( 'public' => true ) );
		$this->register_test_taxonomy( 'veu_test_tax_shown', array( 'veu_test_cpt_a' ), array( 'show_in_menu' => true ) );

		// タームを持つ公開投稿を1件作成し、サイトマップの投稿タイプループから除外されないようにする。
		// Create one published post with a term so it is not skipped by the sitemap's post type loop.
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => 'veu_test_cpt_a',
				'post_status' => 'publish',
			)
		);
		$term    = wp_insert_term( 'VEU Test Term', 'veu_test_tax_shown' );
		wp_set_object_terms( $post_id, array( $term['term_id'] ), 'veu_test_tax_shown' );

		$test_cases = array(
			array(
				'test_condition_name' => '除外設定なしの場合 => タクソノミーの見出しとターム一覧が表示される',
				'exclude_taxonomies'  => array(),
				'expect_visible'      => true,
			),
			array(
				'test_condition_name' => 'excludeTaxonomies に指定すると、見出しごとサイトマップから消える',
				'exclude_taxonomies'  => array( 'veu_test_tax_shown' => 'true' ),
				'expect_visible'      => false,
			),
			array(
				'test_condition_name' => '境界値: 無関係なタクソノミー名が excludeTaxonomies に入っていても表示に影響しない',
				'exclude_taxonomies'  => array( 'veu_test_tax_not_exists' => 'true' ),
				'expect_visible'      => true,
			),
		);

		foreach ( $test_cases as $case ) {
			update_option(
				'vkExUnit_sitemap_options',
				array( 'excludeTaxonomies' => $case['exclude_taxonomies'] )
			);

			$html = vkExUnit_sitemap( array() );

			if ( $case['expect_visible'] ) {
				$this->assertStringContainsString( 'sitemap-taxonomy-veu_test_tax_shown', $html, $case['test_condition_name'] );
			} else {
				$this->assertStringNotContainsString( 'sitemap-taxonomy-veu_test_tax_shown', $html, $case['test_condition_name'] );
			}

			delete_option( 'vkExUnit_sitemap_options' );
		}
	}

	/**
	 * テスト用の投稿タイプを登録し、後片付け対象として記録する。
	 * Register a test post type and remember it for cleanup.
	 *
	 * @param string $post_type 投稿タイプ名 / Post type slug.
	 * @param array  $args      register_post_type() に渡す引数 / Args passed to register_post_type().
	 * @return void
	 */
	private function register_test_post_type( $post_type, $args = array() ) {
		if ( post_type_exists( $post_type ) ) {
			return;
		}
		register_post_type( $post_type, wp_parse_args( $args, array( 'label' => $post_type ) ) );
		$this->registered_post_types[] = $post_type;
	}

	/**
	 * テスト用のタクソノミーを登録し、後片付け対象として記録する。
	 * Register a test taxonomy and remember it for cleanup.
	 *
	 * @param string $taxonomy    タクソノミー名 / Taxonomy slug.
	 * @param array  $object_type 紐付ける投稿タイプ名の配列 / Post types to attach to.
	 * @param array  $args        register_taxonomy() に渡す引数 / Args passed to register_taxonomy().
	 * @return void
	 */
	private function register_test_taxonomy( $taxonomy, $object_type, $args = array() ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			return;
		}
		register_taxonomy( $taxonomy, $object_type, wp_parse_args( $args, array( 'label' => $taxonomy ) ) );
		$this->registered_taxonomies[] = $taxonomy;
	}

	/**
	 * 各テストの後に、登録した投稿タイプ・タクソノミー・オプションを片付ける。
	 * Clean up the registered post types, taxonomies and options after each test.
	 *
	 * @return void
	 */
	public function tear_down() {
		foreach ( $this->registered_taxonomies as $taxonomy ) {
			unregister_taxonomy( $taxonomy );
		}
		$this->registered_taxonomies = array();

		foreach ( $this->registered_post_types as $post_type ) {
			unregister_post_type( $post_type );
		}
		$this->registered_post_types = array();

		delete_option( 'vkExUnit_sitemap_options' );

		parent::tear_down();
	}
}
