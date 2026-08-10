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
	 * veu_get_sitemap_public_post_types() のテスト。
	 * veu_sitemap_exclude_post_types フィルターによる除外は反映される事、
	 * ユーザー設定（excludePostTypes オプション）は反映されない事を検証する。
	 *
	 * Test for veu_get_sitemap_public_post_types().
	 * Verifies that exclusion via the veu_sitemap_exclude_post_types filter is reflected,
	 * while the user-configurable excludePostTypes option is intentionally NOT applied here.
	 */
	function test_veu_get_sitemap_public_post_types() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_sitemap_public_post_types' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_post_type( 'veu_test_cpt_a', array( 'public' => true ) );
		$this->register_test_post_type( 'veu_test_cpt_b', array( 'public' => false ) );
		$this->register_test_post_type( 'veu_test_cpt_c', array( 'public' => true ) );

		$exclude_via_filter = static function ( $exclude_post_types ) {
			$exclude_post_types[] = 'veu_test_cpt_c';
			return $exclude_post_types;
		};

		$test_cases = array(
			array(
				'test_condition_name'       => '除外設定なしの場合 => 公開の投稿タイプのみが含まれる',
				'apply_filter'              => false,
				'option_exclude_post_types' => array( 'veu_test_cpt_a' => 'true' ),
				'expected_included'         => array( 'veu_test_cpt_a', 'veu_test_cpt_c' ),
				'expected_excluded'         => array( 'veu_test_cpt_b' ),
			),
			array(
				'test_condition_name'       => 'veu_sitemap_exclude_post_types フィルターで指定した投稿タイプは除外される',
				'apply_filter'              => true,
				'option_exclude_post_types' => array(),
				'expected_included'         => array( 'veu_test_cpt_a' ),
				'expected_excluded'         => array( 'veu_test_cpt_b', 'veu_test_cpt_c' ),
			),
			array(
				'test_condition_name'       => '境界値: excludePostTypes オプションで除外しても、ここでは除外されない（フィルター専用の集合のため）',
				'apply_filter'              => false,
				'option_exclude_post_types' => array( 'veu_test_cpt_c' => 'true' ),
				'expected_included'         => array( 'veu_test_cpt_a', 'veu_test_cpt_c' ),
				'expected_excluded'         => array( 'veu_test_cpt_b' ),
			),
		);

		foreach ( $test_cases as $case ) {
			update_option(
				'vkExUnit_sitemap_options',
				array( 'excludePostTypes' => $case['option_exclude_post_types'] )
			);
			if ( $case['apply_filter'] ) {
				add_filter( 'veu_sitemap_exclude_post_types', $exclude_via_filter );
			}

			$actual = veu_get_sitemap_public_post_types();

			foreach ( $case['expected_included'] as $post_type ) {
				$this->assertArrayHasKey( $post_type, $actual, $case['test_condition_name'] );
			}
			foreach ( $case['expected_excluded'] as $post_type ) {
				$this->assertArrayNotHasKey( $post_type, $actual, $case['test_condition_name'] );
			}

			if ( $case['apply_filter'] ) {
				remove_filter( 'veu_sitemap_exclude_post_types', $exclude_via_filter );
			}
			delete_option( 'vkExUnit_sitemap_options' );
		}
	}

	/**
	 * veu_get_sitemap_available_taxonomies() のテスト。
	 * show_in_menu が有効なタクソノミーだけが一覧に含まれる事、投稿フォーマット等の内部タクソノミーが
	 * 自動的に除外される事、そして excludePostTypes オプションでは絞り込まれない事
	 * （安藤さんレビュー MEDIUM 指摘の回帰テスト）を検証する。
	 *
	 * Test for veu_get_sitemap_available_taxonomies().
	 * Verifies that only taxonomies with show_in_menu enabled are included, that internal
	 * taxonomies such as post_format are automatically excluded, and that the list is NOT
	 * narrowed by the excludePostTypes option ( regression test for the reviewer-reported bug
	 * where a taxonomy checkbox silently disappeared, and its saved exclusion was then lost on
	 * the next save, merely because an admin also excluded its post type ).
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
		// cpt_c にだけ紐付いたタクソノミー（event / event_cat の再現用）。
		// A taxonomy attached only to cpt_c ( reproduces the reviewer's event / event_cat scenario ).
		$this->register_test_taxonomy( 'veu_test_tax_on_c', array( 'veu_test_cpt_c' ), array( 'show_in_menu' => true ) );

		$exclude_via_filter = static function ( $exclude_post_types ) {
			$exclude_post_types[] = 'veu_test_cpt_c';
			return $exclude_post_types;
		};

		$test_cases = array(
			array(
				'test_condition_name'       => '除外設定なしの場合 => show_in_menu が有効なタクソノミーだけが一覧に含まれる',
				'apply_filter'              => false,
				'option_exclude_post_types' => array(),
				'expected_included'         => array( 'veu_test_tax_shown', 'veu_test_tax_on_c' ),
				'expected_excluded'         => array( 'veu_test_tax_hidden' ),
			),
			array(
				'test_condition_name'       => '回帰テスト: 投稿タイプを excludePostTypes オプションで除外しても、紐づくタクソノミーは一覧から消えない（保存済み設定を次回保存時に失わないため）',
				'apply_filter'              => false,
				'option_exclude_post_types' => array( 'veu_test_cpt_c' => 'true' ),
				'expected_included'         => array( 'veu_test_tax_shown', 'veu_test_tax_on_c' ),
				'expected_excluded'         => array( 'veu_test_tax_hidden' ),
			),
			array(
				'test_condition_name'       => '投稿タイプが veu_sitemap_exclude_post_types フィルターで除外される場合は、紐づくタクソノミーも一覧から外れる',
				'apply_filter'              => true,
				'option_exclude_post_types' => array(),
				'expected_included'         => array( 'veu_test_tax_shown' ),
				'expected_excluded'         => array( 'veu_test_tax_hidden', 'veu_test_tax_on_c' ),
			),
			array(
				'test_condition_name'       => '境界値: 投稿フォーマット（post_format）のような内部タクソノミーは自動的に一覧から除外される',
				'apply_filter'              => false,
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
			if ( $case['apply_filter'] ) {
				add_filter( 'veu_sitemap_exclude_post_types', $exclude_via_filter );
			}

			$actual = veu_get_sitemap_available_taxonomies();

			foreach ( $case['expected_included'] as $taxonomy ) {
				$this->assertArrayHasKey( $taxonomy, $actual, $case['test_condition_name'] );
			}
			foreach ( $case['expected_excluded'] as $taxonomy ) {
				$this->assertArrayNotHasKey( $taxonomy, $actual, $case['test_condition_name'] );
			}

			if ( $case['apply_filter'] ) {
				remove_filter( 'veu_sitemap_exclude_post_types', $exclude_via_filter );
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
		$this->assertNotWPError( $term, 'テスト用タームの作成に失敗した場合、後続のアサーションが無意味になるため先に検証する。' );
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
	 * veu_sitemap_options_validate() のテスト。
	 * 登録済みのタクソノミー名は保存され、未登録のタクソノミー名は保存されない事
	 * （安藤さんレビュー LOW 指摘の入力検証）を検証する。
	 *
	 * Test for veu_sitemap_options_validate().
	 * Verifies that a registered taxonomy name is saved, and that an unregistered
	 * taxonomy name is dropped ( input validation gap pointed out in the code review ).
	 */
	function test_veu_sitemap_options_validate() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_sitemap_options_validate' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_cases = array(
			array(
				'test_condition_name'         => '登録済みのタクソノミー名（category）は excludeTaxonomies にそのまま保存される',
				'input'                       => array(
					'excludePostTypes'  => array( 'post' => 'true' ),
					'excludeTaxonomies' => array( 'category' => 'true' ),
				),
				'expected_exclude_post_types' => array( 'post' => 'true' ),
				'expected_exclude_taxonomies' => array( 'category' => 'true' ),
			),
			array(
				'test_condition_name'         => '未登録のタクソノミー名は excludeTaxonomies から除かれる（登録済みの分は保存される）',
				'input'                       => array(
					'excludeTaxonomies' => array(
						'category'                => 'true',
						'veu_test_not_a_taxonomy' => 'true',
					),
				),
				'expected_exclude_post_types' => array(),
				'expected_exclude_taxonomies' => array( 'category' => 'true' ),
			),
			array(
				'test_condition_name'         => '境界値: excludeTaxonomies が送信されない場合は何も保存されない',
				'input'                       => array(
					'excludePostTypes' => array( 'post' => 'true' ),
				),
				'expected_exclude_post_types' => array( 'post' => 'true' ),
				'expected_exclude_taxonomies' => array(),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_sitemap_options_validate( $case['input'] );

			// 未送信のキーは $output に存在しない場合があるため、空配列として正規化してから比較する。
			// A key that was never submitted may be entirely absent from $output, so normalize to an empty array before comparing.
			$actual_exclude_post_types = isset( $actual['excludePostTypes'] ) ? $actual['excludePostTypes'] : array();
			$actual_exclude_taxonomies = isset( $actual['excludeTaxonomies'] ) ? $actual['excludeTaxonomies'] : array();

			$this->assertEquals( $case['expected_exclude_post_types'], $actual_exclude_post_types, $case['test_condition_name'] );
			$this->assertEquals( $case['expected_exclude_taxonomies'], $actual_exclude_taxonomies, $case['test_condition_name'] );
		}
	}

	/**
	 * vk_the_taxonomy_check_list() のテスト。
	 * name 属性・checked の有無が正しく出力される事、対象タクソノミーが0件の場合に
	 * 空の一覧ではなくフォールバック文言が出力される事（植草さんレビュー UX-2 指摘）を検証する。
	 *
	 * Test for vk_the_taxonomy_check_list().
	 * Verifies the name attribute and the checked state are output correctly, and that a
	 * fallback message is shown instead of an empty list when there are no taxonomies to
	 * display ( UX review finding ).
	 */
	function test_vk_the_taxonomy_check_list() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_the_taxonomy_check_list' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_taxonomy( 'veu_test_tax_shown', array(), array( 'label' => 'VEU Test Tax Shown' ) );
		$taxonomy_object = get_taxonomy( 'veu_test_tax_shown' );

		$test_cases = array(
			array(
				'test_condition_name'   => 'チェック済みの場合 => name 属性と checked が両方出力される',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array( 'veu_test_tax_shown' => 'true' ),
					'taxonomies' => array( 'veu_test_tax_shown' => $taxonomy_object ),
				),
				'expected_contains'     => array(
					'name="vkExUnit_sitemap_options[excludeTaxonomies][veu_test_tax_shown]"',
					'checked',
				),
				'expected_not_contains' => array(),
			),
			array(
				'test_condition_name'   => '未チェックの場合 => name 属性は出力されるが checked は出力されない',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array(),
					'taxonomies' => array( 'veu_test_tax_shown' => $taxonomy_object ),
				),
				'expected_contains'     => array(
					'name="vkExUnit_sitemap_options[excludeTaxonomies][veu_test_tax_shown]"',
				),
				'expected_not_contains' => array( 'checked' ),
			),
			array(
				'test_condition_name'   => '境界値: 対象タクソノミーが0件の場合、空の一覧ではなくフォールバック文言が出力される',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array(),
					'taxonomies' => array(),
				),
				'expected_contains'     => array( 'No taxonomies are available to exclude.' ),
				'expected_not_contains' => array( '<ul class="no-style">' ),
			),
		);

		foreach ( $test_cases as $case ) {
			ob_start();
			vk_the_taxonomy_check_list( $case['args'] );
			$html = ob_get_clean();

			foreach ( $case['expected_contains'] as $expected ) {
				$this->assertStringContainsString( $expected, $html, $case['test_condition_name'] );
			}
			foreach ( $case['expected_not_contains'] as $unexpected ) {
				$this->assertStringNotContainsString( $unexpected, $html, $case['test_condition_name'] );
			}
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
