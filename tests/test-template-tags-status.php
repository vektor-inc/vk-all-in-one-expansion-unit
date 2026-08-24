<?php
/**
 * Class TemplateTagsStatusTest
 *
 * Tests for inc/template-tags-status/collector.php and inc/template-tags-status/site-health.php:
 * the "which plugin's copy of ExUnit's shared template-tag files is currently in effect" feature.
 *
 * inc/template-tags-status/collector.php と inc/template-tags-status/site-health.php
 * （「ExUnit の共有テンプレートタグファイルについて、どのプラグインのコピーが現在採用されているか」
 * を可視化する機能）のテスト。
 *
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 * @package Vk_All_In_One_Expansion_Unit
 */

class TemplateTagsStatusTest extends WP_UnitTestCase {

	/**
	 * veu_get_shared_template_tags_status() returns one entry per shared file, in the same order
	 * inc/template-tags/template-tags-config.php require_once's them.
	 *
	 * veu_get_shared_template_tags_status() が、inc/template-tags/template-tags-config.php が
	 * require_once している順序と同じ順序で、共有ファイルごとに1件のエントリを返すことを検証する。
	 */
	public function test_veu_get_shared_template_tags_status_order_and_count() {
		$status = veu_get_shared_template_tags_status();

		$this->assertCount( 3, $status, '共有ファイルは3件（template-tags.php / template-tags-veu.php / template-tags-veu-old.php）のはず' );

		$expected_order = array( 'template-tags.php', 'template-tags-veu.php', 'template-tags-veu-old.php' );
		$actual_order   = wp_list_pluck( $status, 'file' );

		$this->assertSame( $expected_order, $actual_order, 'require_once の順序（template-tags.php → template-tags-veu.php → template-tags-veu-old.php）と一致するはず' );
	}

	/**
	 * In this test environment only ExUnit itself is loaded (no other plugin bundles a copy of
	 * the same shared files), so every function from every shared file should resolve to exactly
	 * one source: ExUnit's own plugin file.
	 *
	 * このテスト環境では ExUnit 自身しか読み込まれていない（同じ共有ファイルを同梱する他プラグイン
	 * が存在しない）ため、どの共有ファイルの関数も採用元は ExUnit 自身の1つだけに解決されるはず。
	 */
	public function test_veu_get_shared_template_tags_status_detects_loaded_exunit_copy() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$exunit_main_file  = VEU_DIRECTORY_PATH . '/vkExUnit.php';
		$expected_basename = plugin_basename( $exunit_main_file );

		$all_plugins = get_plugins();
		$this->assertArrayHasKey( $expected_basename, $all_plugins, '前提: get_plugins() が ExUnit 自身をプラグインとして検出できていること' );

		$status = veu_get_shared_template_tags_status();

		foreach ( $status as $file_status ) {
			$this->assertNotEmpty( $file_status['sources'], $file_status['file'] . ' は少なくとも1件の採用元を持つはず（何も定義されていない = Not loaded は本テスト環境では想定外）' );

			foreach ( $file_status['sources'] as $source ) {
				$this->assertSame( 'plugin', $source['type'], $file_status['file'] . ' の採用元はプラグインとして特定できるはず' );
				$this->assertSame( $expected_basename, $source['basename'], $file_status['file'] . ' の採用元は ExUnit 自身であるはず（他プラグインが同梱コピーを読み込んでいないため）' );
				$this->assertSame( $all_plugins[ $expected_basename ]['Name'], $source['name'] );
				$this->assertSame( $all_plugins[ $expected_basename ]['Version'], $source['version'] );
			}
		}
	}

	/**
	 * veu_extract_top_level_function_names() reads a shared file with token_get_all() (not a
	 * hardcoded list) and returns the function names it declares at file scope.
	 *
	 * veu_extract_top_level_function_names() が（ハードコードした一覧ではなく）token_get_all() で
	 * 共有ファイルを解析し、ファイルスコープで宣言している関数名を返すことを検証する。
	 */
	public function test_veu_extract_top_level_function_names() {
		$package_dir = VEU_DIRECTORY_PATH . '/inc/template-tags/package';

		$test_cases = array(
			array(
				'test_condition_name' => 'template-tags.php => vk_is_template_tags_exist() と vk_the_taxonomy_check_list() を含む（正常系）',
				'file_path'           => $package_dir . '/template-tags.php',
				'must_contain'        => array( 'vk_is_template_tags_exist', 'vk_the_taxonomy_check_list' ),
			),
			array(
				// veu_get_common_options() は function_exists() ガードの外（ファイル直下）で宣言、
				// veu_get_name() はガードの内側（if ( ! function_exists( ... ) ) { function ... } ）で
				// 宣言されている。両方を含めることで、抽出処理がガードの有無に関係なく
				// ファイルスコープの宣言を拾えていることを検証する.
				'test_condition_name' => 'template-tags-veu.php => veu_get_common_options()（ガード外）と veu_get_name()（ガード内）を含む（正常系）',
				'file_path'           => $package_dir . '/template-tags-veu.php',
				'must_contain'        => array( 'veu_get_common_options', 'veu_get_name' ),
			),
			array(
				// このファイルの関数は全て function_exists() ガードの内側で宣言されている.
				'test_condition_name' => 'template-tags-veu-old.php => vkExUnit_get_common_options() と vkExUnit_get_name() を含む（正常系）',
				'file_path'           => $package_dir . '/template-tags-veu-old.php',
				'must_contain'        => array( 'vkExUnit_get_common_options', 'vkExUnit_get_name' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_extract_top_level_function_names( $case['file_path'] );

			$this->assertIsArray( $actual, $case['test_condition_name'] );
			foreach ( $case['must_contain'] as $expected_function_name ) {
				$this->assertContains( $expected_function_name, $actual, $case['test_condition_name'] );
			}
			// 重複が排除されていることも確認する.
			$this->assertSame( array_values( array_unique( $actual ) ), array_values( $actual ), $case['test_condition_name'] . ' / 重複のない配列であるはず' );
		}

		// 読み込めないファイルパスを渡した場合 => 空配列（境界値・異常系）.
		$unreadable = veu_extract_top_level_function_names( VEU_DIRECTORY_PATH . '/this-file-does-not-exist.php' );
		$this->assertSame( array(), $unreadable, '存在しないファイルパスの場合は空配列を返すはず（異常系）' );
	}

	/**
	 * veu_identify_template_tags_source_from_file() is a pure function of its $defined_file
	 * argument, so the "could not identify the plugin" fallback branches (which are hard to
	 * reproduce with only real, currently-installed plugins) can be exercised directly here.
	 *
	 * veu_identify_template_tags_source_from_file() は $defined_file 引数だけに依存する純粋関数
	 * のため、「どのプラグインか特定できない」場合のフォールバック分岐（実際にインストール済みの
	 * プラグインだけでは再現しにくい）をここで直接検証できる。
	 *
	 * Also verifies that no branch ever leaks a server absolute path: the "unidentified_file"
	 * branch must return an ABSPATH-relative path, and a file path located entirely outside
	 * ABSPATH must fall back to "unidentified" rather than exposing that absolute path.
	 *
	 * また、どの分岐もサーバーの絶対パスを漏らさないことも検証する。「unidentified_file」分岐は
	 * ABSPATH からの相対パスを返す必要があり、ABSPATH の外側にあるファイルパスは、その絶対パスを
	 * 露出させるのではなく「unidentified」にフォールバックする必要がある。
	 */
	public function test_veu_identify_template_tags_source_from_file() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$exunit_main_file  = VEU_DIRECTORY_PATH . '/vkExUnit.php';
		$expected_basename = plugin_basename( $exunit_main_file );
		$all_plugins       = get_plugins();

		$test_cases = array(
			array(
				'test_condition_name' => 'ExUnit 自身のメインファイル => type: plugin として特定できる（正常系）',
				'defined_file'        => $exunit_main_file,
				'expected'            => array(
					'type'     => 'plugin',
					'name'     => $all_plugins[ $expected_basename ]['Name'],
					'version'  => $all_plugins[ $expected_basename ]['Version'],
					'basename' => $expected_basename,
				),
			),
			array(
				'test_condition_name' => 'ABSPATH 配下だがどのプラグインディレクトリにも属さない WordPress コア側のファイル => type: unidentified_file、relative_path は ABSPATH からの相対パス（境界値）',
				'defined_file'        => ABSPATH . 'wp-includes/version.php',
				'expected'            => array(
					'type'          => 'unidentified_file',
					'relative_path' => 'wp-includes/version.php',
				),
			),
			array(
				'test_condition_name' => 'ABSPATH の外側を指すファイルパス => 絶対パスを漏らさず type: unidentified にフォールバックする（異常系・境界値）',
				'defined_file'        => '/this/path/is/outside/abspath/fake-file.php',
				'expected'            => array(
					'type' => 'unidentified',
				),
			),
			array(
				'test_condition_name' => 'ファイルパスが取得できない（false）=> type: unidentified（異常系）',
				'defined_file'        => false,
				'expected'            => array(
					'type' => 'unidentified',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_identify_template_tags_source_from_file( $case['defined_file'] );

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			// どのケースでも、結果を JSON 化した文字列中にサーバーの絶対パス（ABSPATH）が
			// 含まれないことを確認する.
			$serialized = wp_json_encode( $actual );
			$this->assertStringNotContainsString( rtrim( ABSPATH, '/' ), $serialized, $case['test_condition_name'] . ' / 結果にサーバーの絶対パスが含まれてはいけない' );
		}
	}

	/**
	 * veu_get_shared_template_tags_status_rows() normalizes the collected status into a flat,
	 * one-row-per-source table for the WP-CLI command, accepting synthetic $status data so the
	 * multi-source expansion can be verified deterministically (real environments installing a
	 * second plugin that bundles the same files are not required just to test this).
	 *
	 * veu_get_shared_template_tags_status_rows() が、収集済み状態を WP-CLI コマンド向けの
	 * 「1行1採用元」のフラットな表へ正規化することを検証する。合成した $status を渡せる設計により、
	 * 複数採用元の展開を決定的に検証できる（このテストのためだけに同じファイルを同梱する2つ目の
	 * プラグインを実際にインストールする必要はない）。
	 */
	public function test_veu_get_shared_template_tags_status_rows() {
		$synthetic_status = array(
			array(
				'file'    => 'template-tags.php',
				'sources' => array(
					array(
						'type'     => 'plugin',
						'name'     => 'VK All in One Expansion Unit',
						'version'  => '9.999.0',
						'basename' => 'vk-all-in-one-expansion-unit/vkExUnit.php',
					),
					array(
						'type'     => 'plugin',
						'name'     => 'VK Post Author Display',
						'version'  => '1.2.3',
						'basename' => 'vk-post-author-display/vk-post-author-display.php',
					),
				),
			),
			array(
				'file'    => 'template-tags-veu.php',
				'sources' => array(
					array(
						'type'          => 'unidentified_file',
						'relative_path' => 'wp-includes/version.php',
					),
				),
			),
			array(
				'file'    => 'template-tags-veu-old.php',
				'sources' => array(),
			),
		);

		$rows = veu_get_shared_template_tags_status_rows( $synthetic_status );

		$expected_rows = array(
			array(
				'file'    => 'template-tags.php',
				'product' => 'VK All in One Expansion Unit',
				'version' => '9.999.0',
				'plugin'  => 'vk-all-in-one-expansion-unit/vkExUnit.php',
			),
			array(
				'file'    => 'template-tags.php',
				'product' => 'VK Post Author Display',
				'version' => '1.2.3',
				'plugin'  => 'vk-post-author-display/vk-post-author-display.php',
			),
			array(
				'file'    => 'template-tags-veu.php',
				'product' => 'Could not identify the plugin (defined in wp-includes/version.php)',
				'version' => '',
				'plugin'  => '',
			),
			array(
				'file'    => 'template-tags-veu-old.php',
				'product' => 'Not loaded',
				'version' => '',
				'plugin'  => '',
			),
		);

		$this->assertSame( $expected_rows, $rows, '2件の採用元を持つファイルは2行に展開され、file 列は同じ値を繰り返すはず' );
	}

	/**
	 * veu_format_shared_template_tags_status_value() (used to build the Site Health "Info" tab
	 * row) joins multiple sources with " / ", falls back to "Not loaded" when empty, and always
	 * escapes plugin-supplied name/version before formatting.
	 *
	 * veu_format_shared_template_tags_status_value()（サイトヘルス「情報」タブの行を組み立てる
	 * のに使う）が、複数の採用元を " / " で連結すること、空の場合は "Not loaded" になること、
	 * プラグイン由来の name / version を整形前に必ずエスケープすることを検証する。
	 */
	public function test_veu_format_shared_template_tags_status_value() {
		$test_cases = array(
			array(
				'test_condition_name' => '採用元が0件 => "Not loaded"（境界値）',
				'sources'             => array(),
				'expected'            => 'Not loaded',
			),
			array(
				'test_condition_name' => '採用元が1件（plugin） => "Name Version (basename)" 形式（正常系）',
				'sources'             => array(
					array(
						'type'     => 'plugin',
						'name'     => 'VK All in One Expansion Unit',
						'version'  => '9.122.0',
						'basename' => 'vk-all-in-one-expansion-unit/vkExUnit.php',
					),
				),
				'expected'            => 'VK All in One Expansion Unit 9.122.0 (vk-all-in-one-expansion-unit/vkExUnit.php)',
			),
			array(
				'test_condition_name' => '採用元が2件（plugin混在） => " / " で連結される（正常系・Issue #1479 の実測ケース相当）',
				'sources'             => array(
					array(
						'type'     => 'plugin',
						'name'     => 'VK All in One Expansion Unit',
						'version'  => '9.122.0',
						'basename' => 'vk-all-in-one-expansion-unit/vkExUnit.php',
					),
					array(
						'type'     => 'plugin',
						'name'     => 'VK Post Author Display',
						'version'  => '1.2.3',
						'basename' => 'vk-post-author-display/vk-post-author-display.php',
					),
				),
				'expected'            => 'VK All in One Expansion Unit 9.122.0 (vk-all-in-one-expansion-unit/vkExUnit.php) / VK Post Author Display 1.2.3 (vk-post-author-display/vk-post-author-display.php)',
			),
			array(
				'test_condition_name' => '採用元が unidentified_file => "Could not identify the plugin (defined in ...)"（正常系）',
				'sources'             => array(
					array(
						'type'          => 'unidentified_file',
						'relative_path' => 'wp-includes/version.php',
					),
				),
				'expected'            => 'Could not identify the plugin (defined in wp-includes/version.php)',
			),
			array(
				'test_condition_name' => '採用元が unidentified => "Could not identify the plugin"（正常系）',
				'sources'             => array(
					array( 'type' => 'unidentified' ),
				),
				'expected'            => 'Could not identify the plugin',
			),
			array(
				'test_condition_name' => 'プラグイン名に HTML を含む場合 => エスケープされて出力される（異常系・セキュリティ）',
				'sources'             => array(
					array(
						'type'     => 'plugin',
						'name'     => '<script>alert(1)</script>',
						'version'  => '1.0.0"><b>',
						'basename' => 'evil-plugin/evil.php',
					),
				),
				'expected'            => '&lt;script&gt;alert(1)&lt;/script&gt; 1.0.0&quot;&gt;&lt;b&gt; (evil-plugin/evil.php)',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_format_shared_template_tags_status_value( $case['sources'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * The Site Health "Info" tab section added by veu_add_shared_template_tags_site_health_section()
	 * never leaks a server absolute path, and always sets 'value' and 'debug' to the same plain
	 * text for each field (so the on-screen table and the "copy site info" output match).
	 *
	 * veu_add_shared_template_tags_site_health_section() が追加するサイトヘルス「情報」タブの
	 * セクションが、サーバーの絶対パスを一切含まないこと、そして各フィールドの 'value' と 'debug'
	 * に常に同じプレーンテキストが入っていること（画面表示と「サイト情報をコピー」の出力が
	 * 一致すること）を検証する。
	 */
	public function test_veu_add_shared_template_tags_site_health_section_no_absolute_path_leak() {
		$info = veu_add_shared_template_tags_site_health_section( array() );

		$this->assertArrayHasKey( 'vk-exunit-shared-template-tags', $info );

		$section = $info['vk-exunit-shared-template-tags'];
		$this->assertNotEmpty( $section['label'] );
		$this->assertNotEmpty( $section['description'] );
		$this->assertCount( 3, $section['fields'], 'ファイル3件分のフィールドがあるはず' );

		$serialized_section = wp_json_encode( $section );
		$this->assertStringNotContainsString( rtrim( ABSPATH, '/' ), $serialized_section, 'サイトヘルスのセクション全体にサーバーの絶対パス（ABSPATH）が含まれてはいけない' );

		foreach ( $section['fields'] as $field ) {
			$this->assertSame( $field['value'], $field['debug'], $field['label'] . ' は value と debug が同一のプレーンテキストであるはず' );
			$this->assertNotSame( '', trim( (string) $field['value'] ), $field['label'] . ' の value は空文字であってはいけない（書式が壊れたのか値が無いのか判断できなくなるため）' );
		}
	}
}
