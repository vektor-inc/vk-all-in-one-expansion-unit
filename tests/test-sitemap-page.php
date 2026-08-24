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
	 * 無関係なキーが保存されていても他のタクソノミーの表示に影響しない事、
	 * show_in_menu => false のタクソノミーはそもそも出力されない事（安藤さんレビュー LOW 指摘の回帰テスト。
	 * フロント側の唯一の条件式差し替え箇所であるため）を検証する。
	 *
	 * Test for vkExUnit_sitemap().
	 * Verifies that a taxonomy specified in excludeTaxonomies disappears together with its
	 * heading ( h5 ), that an unrelated stale key in the option does not affect the display of
	 * other taxonomies, and that a taxonomy with show_in_menu => false is never output at all
	 * ( regression test for the front-end's only replaced condition, per code review ).
	 */
	function test_vkExUnit_sitemap() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vkExUnit_sitemap' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_post_type( 'veu_test_cpt_a', array( 'public' => true ) );
		$this->register_test_taxonomy( 'veu_test_tax_shown', array( 'veu_test_cpt_a' ), array( 'show_in_menu' => true ) );
		// show_in_menu => false のタクソノミー（回帰テスト用）。
		// A taxonomy with show_in_menu => false ( for the regression test ).
		$this->register_test_taxonomy( 'veu_test_tax_hidden', array( 'veu_test_cpt_a' ), array( 'show_in_menu' => false ) );

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

		// veu_test_tax_hidden にもタームを1件作成しておく（ターム0件だからではなく、show_in_menu が
		// false だから出力されない事を検証するため）。
		// Also create one term on veu_test_tax_hidden ( so its absence proves the show_in_menu
		// condition, not merely that it has zero terms ).
		$hidden_term = wp_insert_term( 'VEU Test Hidden Term', 'veu_test_tax_hidden' );
		$this->assertNotWPError( $hidden_term, 'テスト用タームの作成に失敗した場合、後続のアサーションが無意味になるため先に検証する。' );
		wp_set_object_terms( $post_id, array( $hidden_term['term_id'] ), 'veu_test_tax_hidden' );

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

			// 回帰テスト: excludeTaxonomies の設定に関わらず、show_in_menu => false のタクソノミーは
			// タームがあってもそもそも出力されない。
			// Regression check: regardless of the excludeTaxonomies setting, a taxonomy with
			// show_in_menu => false is never output even when it has a term.
			$this->assertStringNotContainsString( 'sitemap-taxonomy-veu_test_tax_hidden', $html, $case['test_condition_name'] . '（show_in_menu => false の回帰確認）' );

			delete_option( 'vkExUnit_sitemap_options' );
		}
	}

	/**
	 * veu_sitemap_options_validate() のテスト。
	 * 登録済みのタクソノミー名・投稿タイプ名は保存され、未登録の名前は保存されない事
	 * （安藤さんレビュー LOW 指摘の入力検証。excludePostTypes 側のガードは Issue #1475
	 * 対応時に excludeTaxonomies 側と対称になるよう追加した）を検証する。
	 *
	 * Test for veu_sitemap_options_validate().
	 * Verifies that a registered taxonomy or post type name is saved, and that an
	 * unregistered name is dropped ( input validation gap pointed out in the code review;
	 * the excludePostTypes guard was added during the Issue #1475 work to make it symmetric
	 * with the existing excludeTaxonomies guard ).
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
			array(
				'test_condition_name'         => '登録済みの投稿タイプ名（page）は excludePostTypes にそのまま保存される',
				'input'                       => array(
					'excludePostTypes' => array( 'page' => 'true' ),
				),
				'expected_exclude_post_types' => array( 'page' => 'true' ),
				'expected_exclude_taxonomies' => array(),
			),
			array(
				'test_condition_name'         => '未登録の投稿タイプ名は excludePostTypes から除かれる（登録済みの分は保存される）',
				'input'                       => array(
					'excludePostTypes' => array(
						'post'                     => 'true',
						'veu_test_not_a_post_type' => 'true',
					),
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
	 * vk_the_taxonomy_check_list() / veu_the_taxonomy_check_list() のテスト。
	 * name 属性・checked の有無が正しく出力される事、対象タクソノミーが0件の場合に
	 * 空の一覧ではなくフォールバック文言が出力される事（植草さんレビュー UX-2 指摘）を検証する。
	 * 互換レイヤーの vk_ と、ExUnit 本番が実際に呼ぶ veu_ の両方をループする。
	 * また Issue #1475 対応として、見出しに「ラベル (スラッグ)」の形式でスラッグが併記される事、
	 * 同じラベルで異なるスラッグのタクソノミーが並んでも出力から判別できる事を検証する。
	 *
	 * Test for vk_the_taxonomy_check_list() and veu_the_taxonomy_check_list().
	 * Verifies the name attribute and the checked state are output correctly, and that a
	 * fallback message is shown instead of an empty list when there are no taxonomies to
	 * display ( UX review finding ). Loops both the compatibility layer (vk_) and the name
	 * ExUnit's production code actually calls (veu_). Also covers Issue #1475: the checkbox
	 * label is followed by its slug in "Label (slug)" form, and two taxonomies that share the
	 * same label but have different slugs remain distinguishable in the output.
	 */
	function test_vk_the_taxonomy_check_list() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_the_taxonomy_check_list' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_taxonomy( 'veu_test_tax_shown', array(), array( 'label' => 'VEU Test Tax Shown' ) );
		$taxonomy_object = get_taxonomy( 'veu_test_tax_shown' );

		// Issue #1475: ラベルが同じで slug（スラッグ）だけが異なるタクソノミーを2つ用意し、
		// 出力からそれぞれを判別できる事を検証するためのテスト用データ。
		// Two taxonomies sharing the same label but with different slugs, used to verify
		// that the rendered output still lets the admin tell them apart.
		$this->register_test_taxonomy( 'veu_test_tax_dup_a', array(), array( 'label' => 'Duplicate Label Tax' ) );
		$this->register_test_taxonomy( 'veu_test_tax_dup_b', array(), array( 'label' => 'Duplicate Label Tax' ) );
		$taxonomy_object_dup_a = get_taxonomy( 'veu_test_tax_dup_a' );
		$taxonomy_object_dup_b = get_taxonomy( 'veu_test_tax_dup_b' );

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
					' checked />',
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
				// 属性境界を含む文字列にする事で、将来 checked 系のクラス名等が増えても誤検知しないようにする。
				// Include the attribute boundary so a future class name containing "checked" cannot cause a false negative.
				'expected_not_contains' => array( ' checked />' ),
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
			array(
				'test_condition_name'   => 'Issue #1475: 見出しがラベルとスラッグの併記（ラベル (スラッグ)）になる',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array(),
					'taxonomies' => array( 'veu_test_tax_shown' => $taxonomy_object ),
				),
				'expected_contains'     => array(
					'VEU Test Tax Shown <span class="description">(<code>veu_test_tax_shown</code>)</span>',
				),
				'expected_not_contains' => array(),
			),
			array(
				'test_condition_name'   => 'Issue #1475: ラベルが同じでスラッグが異なる2つのタクソノミーが、出力上のスラッグ表示で判別できる',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array(),
					'taxonomies' => array(
						'veu_test_tax_dup_a' => $taxonomy_object_dup_a,
						'veu_test_tax_dup_b' => $taxonomy_object_dup_b,
					),
				),
				'expected_contains'     => array(
					'Duplicate Label Tax <span class="description">(<code>veu_test_tax_dup_a</code>)</span>',
					'Duplicate Label Tax <span class="description">(<code>veu_test_tax_dup_b</code>)</span>',
				),
				'expected_not_contains' => array(),
			),
			array(
				// 境界値: スラッグに HTML 特殊文字が含まれる場合、name 属性・スラッグ表示の両方がエスケープされる事を検証する。
				// esc_attr() / esc_html() を外すと落ちるケースにする事で、エスケープ処理の退行を検出できるようにしている。
				// Boundary case: a slug containing HTML special characters must be escaped in both the name
				// attribute and the slug display. Removing esc_attr()/esc_html() makes this case fail.
				'test_condition_name'   => '境界値: スラッグに HTML 特殊文字が含まれる場合、name 属性とスラッグ表示の両方がエスケープされる',
				'args'                  => array(
					'name'       => 'vkExUnit_sitemap_options[excludeTaxonomies]',
					'checked'    => array(),
					'taxonomies' => array( 'veu"tax<x' => $taxonomy_object ),
				),
				'expected_contains'     => array(
					'name="vkExUnit_sitemap_options[excludeTaxonomies][veu&quot;tax&lt;x]"',
					'<code>veu&quot;tax&lt;x</code>',
				),
				'expected_not_contains' => array( '[veu"tax<x]' ),
			),
		);

		foreach ( array( 'vk_the_taxonomy_check_list', 'veu_the_taxonomy_check_list' ) as $function_name ) {
			foreach ( $test_cases as $case ) {
				$condition_name = $function_name . '() / ' . $case['test_condition_name'];

				ob_start();
				call_user_func( $function_name, $case['args'] );
				$html = ob_get_clean();

				foreach ( $case['expected_contains'] as $expected ) {
					$this->assertStringContainsString( $expected, $html, $condition_name );
				}
				foreach ( $case['expected_not_contains'] as $unexpected ) {
					$this->assertStringNotContainsString( $unexpected, $html, $condition_name );
				}
			}
		}
	}

	/**
	 * vk_the_post_type_check_list() / veu_the_post_type_check_list() のテスト。両者を
	 * ループして検証する（理由は tests/test-template-tags.php の
	 * test_vk_get_post_type() の docblock を参照）。
	 * Issue #1475 対応として、見出しに「ラベル (スラッグ)」の形式でスラッグが併記される事、
	 * name 属性にスラッグが出力される事、同じラベルで異なるスラッグの投稿タイプが
	 * 並んでも出力から判別できる事を検証する。この Issue #1475 のケースは、まさに今回
	 * ExUnit の設定画面を veu_ へ付け替える際に見落としかけたリグレッション（スラッグ併記の
	 * 欠落）そのものであり、vk_ 側だけでなく veu_ 側でも同じ回帰を検知できる必要がある。
	 * register_post_type() はスラッグを英数字・アンダースコア・ハイフンに制限するため、
	 * HTML 特殊文字を含むスラッグのエスケープ挙動そのものは注入できない。その検証は
	 * taxonomies を任意キーで受け取れる vk_the_taxonomy_check_list() 側で行っている。
	 *
	 * Test for vk_the_post_type_check_list() and veu_the_post_type_check_list(), looping
	 * both names (see test_vk_get_post_type()'s docblock in tests/test-template-tags.php for
	 * why). Covers Issue #1475: the checkbox label is followed by its slug in "Label (slug)"
	 * form, the slug appears in the name attribute, and two post types that share the same
	 * label but have different slugs remain distinguishable in the output. This Issue #1475
	 * coverage is exactly the regression (the missing slug disambiguation) that this project
	 * nearly reintroduced while switching ExUnit's settings screens to veu_, so both the vk_
	 * and veu_ names need the same protection here, not only vk_.
	 * register_post_type() restricts slugs to alphanumeric/underscore/hyphen, so a slug
	 * containing HTML special characters cannot be injected here; that escaping behavior is
	 * covered by vk_the_taxonomy_check_list(), which accepts arbitrary keys.
	 */
	function test_vk_the_post_type_check_list() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vk_the_post_type_check_list' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->register_test_post_type(
			'veu_test_cpt_shown',
			array(
				'public' => true,
				'label'  => 'VEU Test CPT Shown',
			)
		);

		// Issue #1475: ラベルが同じで slug（スラッグ）だけが異なる投稿タイプを2つ用意し、
		// 出力からそれぞれを判別できる事を検証するためのテスト用データ。
		// Two post types sharing the same label but with different slugs, used to verify
		// that the rendered output still lets the admin tell them apart.
		$this->register_test_post_type(
			'veu_test_cpt_dup_a',
			array(
				'public' => true,
				'label'  => 'Duplicate Label CPT',
			)
		);
		$this->register_test_post_type(
			'veu_test_cpt_dup_b',
			array(
				'public' => true,
				'label'  => 'Duplicate Label CPT',
			)
		);

		$test_cases = array(
			array(
				'test_condition_name'   => 'チェック済みの場合 => name 属性がスラッグ付きで出力され、checked も出力される',
				'args'                  => array(
					'name'    => 'vkExUnit_sitemap_options[excludePostTypes]',
					'checked' => array( 'veu_test_cpt_shown' => true ),
				),
				'expected_contains'     => array(
					'name="vkExUnit_sitemap_options[excludePostTypes][veu_test_cpt_shown]"',
					' checked />',
				),
				'expected_not_contains' => array(),
			),
			array(
				'test_condition_name'   => '見出しがラベルとスラッグの併記（ラベル (スラッグ)）になる',
				'args'                  => array(
					'name'    => 'vkExUnit_sitemap_options[excludePostTypes]',
					'checked' => array(),
				),
				'expected_contains'     => array(
					'VEU Test CPT Shown <span class="description">(<code>veu_test_cpt_shown</code>)</span>',
				),
				'expected_not_contains' => array(),
			),
			array(
				'test_condition_name'   => 'ラベルが同じでスラッグが異なる2つの投稿タイプが、出力上のスラッグ表示で判別できる',
				'args'                  => array(
					'name'    => 'vkExUnit_sitemap_options[excludePostTypes]',
					'checked' => array(),
				),
				'expected_contains'     => array(
					'Duplicate Label CPT <span class="description">(<code>veu_test_cpt_dup_a</code>)</span>',
					'Duplicate Label CPT <span class="description">(<code>veu_test_cpt_dup_b</code>)</span>',
				),
				'expected_not_contains' => array(),
			),
			array(
				// 境界値: exclude_post_types で指定した投稿タイプは一覧から除外され、出力に含まれない事を検証する。
				// Boundary case: a post type listed in exclude_post_types is left out of the checklist entirely.
				'test_condition_name'   => '境界値: exclude_post_types で指定した投稿タイプは出力に含まれない',
				'args'                  => array(
					'name'               => 'vkExUnit_sitemap_options[excludePostTypes]',
					'checked'            => array(),
					'exclude_post_types' => array( 'attachment', 'veu_test_cpt_dup_b' ),
				),
				'expected_contains'     => array(
					'<code>veu_test_cpt_dup_a</code>',
				),
				'expected_not_contains' => array(
					'<code>veu_test_cpt_dup_b</code>',
				),
			),
		);

		foreach ( array( 'vk_the_post_type_check_list', 'veu_the_post_type_check_list' ) as $function_name ) {
			foreach ( $test_cases as $case ) {
				$condition_name = $function_name . '() / ' . $case['test_condition_name'];

				ob_start();
				call_user_func( $function_name, $case['args'] );
				$html = ob_get_clean();

				foreach ( $case['expected_contains'] as $expected ) {
					$this->assertStringContainsString( $expected, $html, $condition_name );
				}
				foreach ( $case['expected_not_contains'] as $unexpected ) {
					$this->assertStringNotContainsString( $unexpected, $html, $condition_name );
				}
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
