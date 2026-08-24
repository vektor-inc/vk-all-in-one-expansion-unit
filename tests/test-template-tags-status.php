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
			// 各共有ファイルは 8〜15 個の関数を宣言しているが（inc/template-tags/package/ 参照）、
			// 採用元は全て ExUnit 自身の同じプラグインファイルに解決されるはずなので、重複排除により
			// sources は1件にまとまっているはず。これが崩れる（例: 同じ採用元が関数の数だけ並ぶ）と
			// ここで検知できる.
			$this->assertCount( 1, $file_status['sources'], $file_status['file'] . ' は採用元が1件に重複排除されているはず（同じプラグインの関数が複数あっても1件にまとまる）' );

			foreach ( $file_status['sources'] as $source ) {
				$this->assertSame( 'plugin', $source['type'], $file_status['file'] . ' の採用元はプラグインとして特定できるはず' );
				$this->assertSame( $expected_basename, $source['basename'], $file_status['file'] . ' の採用元は ExUnit 自身であるはず（他プラグインが同梱コピーを読み込んでいないため）' );
				$this->assertSame( $all_plugins[ $expected_basename ]['Name'], $source['name'] );
				$this->assertSame( $all_plugins[ $expected_basename ]['Version'], $source['version'] );
			}
		}
	}

	/**
	 * veu_template_tags_status_extract_function_names() reads a shared file with token_get_all() (not a
	 * hardcoded list) and returns the function names it declares at file scope.
	 *
	 * veu_template_tags_status_extract_function_names() が（ハードコードした一覧ではなく）token_get_all() で
	 * 共有ファイルを解析し、ファイルスコープで宣言している関数名を返すことを検証する。
	 */
	public function test_veu_template_tags_status_extract_function_names() {
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
			$actual = veu_template_tags_status_extract_function_names( $case['file_path'] );

			$this->assertIsArray( $actual, $case['test_condition_name'] );
			foreach ( $case['must_contain'] as $expected_function_name ) {
				$this->assertContains( $expected_function_name, $actual, $case['test_condition_name'] );
			}
			// 重複が排除されていることも確認する.
			$this->assertSame( array_values( array_unique( $actual ) ), array_values( $actual ), $case['test_condition_name'] . ' / 重複のない配列であるはず' );
		}

		// 読み込めないファイルパスを渡した場合 => 空配列（境界値・異常系）.
		$unreadable = veu_template_tags_status_extract_function_names( VEU_DIRECTORY_PATH . '/this-file-does-not-exist.php' );
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
				'path'    => '',
			),
			array(
				'file'    => 'template-tags.php',
				'product' => 'VK Post Author Display',
				'version' => '1.2.3',
				'plugin'  => 'vk-post-author-display/vk-post-author-display.php',
				'path'    => '',
			),
			array(
				'file'    => 'template-tags-veu.php',
				// 「特定できない理由」と「パス」は別々の列にする（product 列にパスを埋め込まない）。
				// スクリプトが --format=json/csv を文字列パースし直さずに済むようにするため。
				'product' => 'Could not identify the plugin',
				'version' => '',
				'plugin'  => '',
				'path'    => 'wp-includes/version.php',
			),
			array(
				'file'    => 'template-tags-veu-old.php',
				'product' => 'Not loaded',
				'version' => '',
				'plugin'  => '',
				'path'    => '',
			),
		);

		$this->assertSame( $expected_rows, $rows, '2件の採用元を持つファイルは2行に展開され、file 列は同じ値を繰り返すはず。特定できない場合は path 列にパスが分離して入り、product 列には理由の文言だけが入るはず' );
	}

	/**
	 * veu_format_shared_template_tags_status_value() (used to build the Site Health "Info" tab
	 * row) joins multiple sources with " / ", falls back to "Not loaded" when empty, and passes
	 * plugin-supplied name/version/basename through UNESCAPED.
	 *
	 * veu_format_shared_template_tags_status_value()（サイトヘルス「情報」タブの行を組み立てる
	 * のに使う）が、複数の採用元を " / " で連結すること、空の場合は "Not loaded" になること、
	 * プラグイン由来の name / version / basename をエスケープせずそのまま通すことを検証する。
	 *
	 * This is intentional, not a gap: WordPress' Site Health "Info" tab itself calls esc_html() on
	 * 'value' when rendering the table and outputs 'debug' completely raw for the "copy site info"
	 * action. Escaping here too would double-escape (e.g. a plugin named "Search & Filter Pro"
	 * would render as "Search &amp;amp; Filter Pro"), which is exactly the bug this function used
	 * to have -- see veu_format_template_tags_source_label()'s docblock in site-health.php for the
	 * full rationale.
	 *
	 * これは漏れではなく意図的な仕様である。WordPress 本体のサイトヘルス「情報」タブは、表を
	 * 描画する際に自ら 'value' へ esc_html() をかけ、「サイト情報をコピー」操作では 'debug' を
	 * 完全に無加工のまま出力する。ここでもエスケープすると二重エスケープになり（例:
	 * "Search & Filter Pro" というプラグイン名が "Search &amp;amp; Filter Pro" になってしまう）、
	 * まさにこの関数がかつて抱えていた不具合そのものになる。詳しい理由は site-health.php の
	 * veu_format_template_tags_source_label() の docblock を参照。
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
				// WordPress 本体の debug_information の契約は「プレーンテキストを渡し、
				// エスケープは本体（画面表示は esc_html()、コピー出力は無加工）に委ねる」なので、
				// ここでは HTML を含む文字列でもエスケープせずそのまま通ることを検証する
				// （二重エスケープ防止の回帰テスト）.
				'test_condition_name' => 'プラグイン名に HTML を含む場合 => 二重エスケープを防ぐため、ここではエスケープせずそのまま通す（異常系・セキュリティ回帰）',
				'sources'             => array(
					array(
						'type'     => 'plugin',
						'name'     => '<script>alert(1)</script>',
						'version'  => '1.0.0"><b>',
						'basename' => 'evil-plugin/evil.php',
					),
				),
				'expected'            => '<script>alert(1)</script> 1.0.0"><b> (evil-plugin/evil.php)',
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

	/**
	 * veu_get_template_tags_source_dedupe_key() returns the same key for source descriptors that
	 * represent the same source, and different keys for descriptors that represent different
	 * sources -- including across types that happen to share an inner string value.
	 *
	 * veu_get_template_tags_source_dedupe_key() が、同じ採用元を表す記述には同じキーを、異なる
	 * 採用元を表す記述には異なるキーを返すことを検証する（type をまたいで内部の文字列値が
	 * 偶然一致するケースも含む）。
	 *
	 * veu_get_shared_template_tags_status() の重複排除ロジックはこのキーの一致判定に依存して
	 * いるため、キー生成そのものを単体で検証する。実データでの重複排除（同じプラグインの15個の
	 * 関数が1件にまとまること）の確認は
	 * test_veu_get_shared_template_tags_status_detects_loaded_exunit_copy() の
	 * assertCount( 1, ... ) を参照。
	 */
	public function test_veu_get_template_tags_source_dedupe_key() {
		$plugin_source_a1            = array(
			'type'     => 'plugin',
			'name'     => 'VK All in One Expansion Unit',
			'version'  => '9.122.0',
			'basename' => 'vk-all-in-one-expansion-unit/vkExUnit.php',
		);
		$plugin_source_a2            = array(
			'type'     => 'plugin',
			// name / version が異なっていても basename が同じなら「同じ採用元」として扱われる
			// べき（同じプラグインの別の関数から見つかった、という状況を想定）.
			'name'     => 'VK All in One Expansion Unit (different label)',
			'version'  => '9.999.0',
			'basename' => 'vk-all-in-one-expansion-unit/vkExUnit.php',
		);
		$plugin_source_b             = array(
			'type'     => 'plugin',
			'name'     => 'VK Post Author Display',
			'version'  => '1.2.3',
			'basename' => 'vk-post-author-display/vk-post-author-display.php',
		);
		$unidentified_file_source_a1 = array(
			'type'          => 'unidentified_file',
			'relative_path' => 'wp-includes/version.php',
		);
		$unidentified_file_source_a2 = array(
			'type'          => 'unidentified_file',
			'relative_path' => 'wp-includes/version.php',
		);
		$unidentified_file_source_b  = array(
			'type'          => 'unidentified_file',
			'relative_path' => 'wp-includes/functions.php',
		);
		$unidentified_source_1       = array( 'type' => 'unidentified' );
		$unidentified_source_2       = array( 'type' => 'unidentified' );
		// basename と relative_path に、あえて同じ文字列を使う（type さえ異なれば衝突しては
		// いけないことを確認するため）.
		$plugin_source_collision     = array(
			'type'     => 'plugin',
			'name'     => 'Coincidentally Named Plugin',
			'version'  => '1.0.0',
			'basename' => 'wp-includes/version.php',
		);
		$unidentified_file_collision = array(
			'type'          => 'unidentified_file',
			'relative_path' => 'wp-includes/version.php',
		);

		$this->assertSame(
			veu_get_template_tags_source_dedupe_key( $plugin_source_a1 ),
			veu_get_template_tags_source_dedupe_key( $plugin_source_a2 ),
			'同じ basename の plugin 採用元は同じキーになるはず（name/version が違っても同一視される）'
		);
		$this->assertNotSame(
			veu_get_template_tags_source_dedupe_key( $plugin_source_a1 ),
			veu_get_template_tags_source_dedupe_key( $plugin_source_b ),
			'basename が異なる plugin 採用元は別のキーになるはず'
		);
		$this->assertSame(
			veu_get_template_tags_source_dedupe_key( $unidentified_file_source_a1 ),
			veu_get_template_tags_source_dedupe_key( $unidentified_file_source_a2 ),
			'同じ relative_path の unidentified_file 採用元は同じキーになるはず'
		);
		$this->assertNotSame(
			veu_get_template_tags_source_dedupe_key( $unidentified_file_source_a1 ),
			veu_get_template_tags_source_dedupe_key( $unidentified_file_source_b ),
			'relative_path が異なる unidentified_file 採用元は別のキーになるはず'
		);
		$this->assertSame(
			veu_get_template_tags_source_dedupe_key( $unidentified_source_1 ),
			veu_get_template_tags_source_dedupe_key( $unidentified_source_2 ),
			'unidentified 採用元は常に同じキーになるはず（区別する情報を持たないため）'
		);
		$this->assertNotSame(
			veu_get_template_tags_source_dedupe_key( $plugin_source_collision ),
			veu_get_template_tags_source_dedupe_key( $unidentified_file_collision ),
			'basename と relative_path が同じ文字列でも、type（plugin / unidentified_file）が異なればキーは衝突しないはず（境界値）'
		);
	}

	/**
	 * Shared regex-based extraction helper for template-tags-config.php-style source, used by
	 * both test_veu_get_shared_template_tags_files_matches_template_tags_config() (against the
	 * real file) and
	 * test_veu_get_shared_template_tags_files_matches_template_tags_config_ignores_non_package_requires()
	 * (against synthetic source), so both tests exercise the exact same regexes and can never
	 * drift apart from each other.
	 *
	 * template-tags-config.php スタイルのソースから読み込み対象を抽出する、正規表現ベースの
	 * 共有ヘルパー。実ファイルに対して検証する
	 * test_veu_get_shared_template_tags_files_matches_template_tags_config() と、合成ソースに
	 * 対して検証する
	 * test_veu_get_shared_template_tags_files_matches_template_tags_config_ignores_non_package_requires()
	 * の両方がこれを使うことで、2つのテストが使う正規表現が食い違うことのないようにしている。
	 *
	 * Both regexes are deliberately scoped to lines that load a file under a `package/`
	 * directory. inc/template-tags/template-tags-config.php is not guaranteed to only ever
	 * require_once the three shared package files it does today -- ExUnit-specific (non-shared)
	 * files could be added there too (see Issue #1478, in progress on a sibling branch, which is
	 * expected to add exactly such a line). This guard exists to catch drift in the shared
	 * *package* file list specifically; a load of some other, non-shared file appearing in the
	 * same config file is not a "shared file list went out of sync" bug and must not fail this
	 * test. Do not remove the `package/` restriction to make the match "more thorough" -- doing
	 * so would make this test count files that veu_get_shared_template_tags_files() was never
	 * meant to list, turning legitimate, unrelated config.php edits into false failures here.
	 *
	 * どちらの正規表現も、`package/` ディレクトリ配下のファイルを読み込んでいる行だけに
	 * 意図的に絞り込んでいる。inc/template-tags/template-tags-config.php が今後も現状の3つの
	 * 共有パッケージファイルしか require_once しない保証はなく、ExUnit 固有の（共有ではない）
	 * ファイルの読み込みが追加されることもありうる（Issue #1478 で、兄弟ブランチにて進行中。
	 * まさにそのような行の追加が見込まれている）。このガードが検知したいのは共有*パッケージ*
	 * ファイルの一覧のずれであって、同じ config ファイル内に共有ではない別のファイルの読み込みが
	 * 増えたこと自体は「共有ファイル一覧がずれた」バグではなく、このテストを失敗させてはいけない。
	 * 「より厳密にする」つもりで `package/` の絞り込みを外さないこと。外すと、
	 * veu_get_shared_template_tags_files() が本来対象にしていないファイルまで数えてしまい、
	 * 無関係な config.php の変更が誤ってこのテストを落とすようになる。
	 *
	 * @param string $config_source template-tags-config.php's source, or a synthetic equivalent.
	 * @return array{
	 *     broad_matched_count: int|false,
	 *     narrow_matched_count: int|false,
	 *     narrow_paths: string[],
	 * }
	 */
	private function extract_template_tags_config_style_requires( $config_source ) {
		// The broad regex: require/include, with or without _once, either quote style, loading
		// any ".php" file under a "package/" directory. Used only to COUNT how many package files
		// the source loads, regardless of exactly how each line is written.
		// 緩い方の正規表現: require / include（_once の有無を問わない）で、シングル・ダブル
		// どちらの引用符でも "package/" 配下の ".php" ファイルを読み込んでいる行に広く一致させ、
		// 件数だけを数える.
		$broad_matched_count = preg_match_all(
			'/\b(?:require|include)(?:_once)?\b[^;]*?([\'"])([^\'"]*package\/[^\'"]+\.php)\1[^;]*;/',
			$config_source,
			$broad_matches
		);

		// The narrow regex: anchored to the `__DIR__ . 'path'` shape the three current lines
		// actually use (also tolerant of require/include, _once, and quote style), restricted the
		// same way to "package/" paths. Used to reconstruct the exact ordered file list.
		// 厳密な方の正規表現: 現状の3行が実際に使っている `__DIR__ . 'パス'` の形に絞り込み
		// （こちらも require/include・_once の有無・引用符の種類は許容する）、同様に "package/"
		// 配下のパスに限定する。絶対パスの一覧を出現順に再構築するために使う.
		$narrow_matched_count = preg_match_all(
			'/\b(?:require|include)(?:_once)?\s*\(?\s*__DIR__\s*\.\s*([\'"])([^\'"]*package\/[^\'"]+)\1\s*\)?\s*;/',
			$config_source,
			$narrow_matches
		);

		return array(
			'broad_matched_count'  => $broad_matched_count,
			'narrow_matched_count' => $narrow_matched_count,
			'narrow_paths'         => isset( $narrow_matches[2] ) ? $narrow_matches[2] : array(),
		);
	}

	/**
	 * veu_get_shared_template_tags_files() must return exactly the package files that
	 * inc/template-tags/template-tags-config.php loads, in the same order.
	 *
	 * veu_get_shared_template_tags_files() は、inc/template-tags/template-tags-config.php が
	 * 読み込んでいる package ファイルと、順序も含めて完全に一致しなければならないことを検証する。
	 *
	 * inc/template-tags/template-tags-config.php is the source of truth here -- it is the file
	 * that actually loads ExUnit's shared package copies. veu_get_shared_template_tags_files() is
	 * a separately hand-written list (see its docblock in collector.php) that must be kept in
	 * sync with it by hand. This test reads template-tags-config.php's own source through
	 * extract_template_tags_config_style_requires() (not a hardcoded expected-files list; see
	 * that helper's docblock for why matching is scoped to `package/` paths and for the broad vs.
	 * narrow regex split) and compares the result against
	 * veu_get_shared_template_tags_files()'s output, so that if a shared package file is ever
	 * added to or removed from template-tags-config.php without updating
	 * veu_get_shared_template_tags_files() to match, this test fails immediately -- catching the
	 * exact "silently drops from the report" problem raised in the review that motivated this
	 * test (Issue #1479), rather than only noticing it later via a missing/wrong report.
	 *
	 * ここでは inc/template-tags/template-tags-config.php を正とする。実際に ExUnit の共有
	 * パッケージのコピーを読み込んでいるのはこのファイルであり、veu_get_shared_template_tags_files()
	 * はそれとは別に手書きされた一覧（collector.php 内の docblock も参照）で、手動で追随させ
	 * 続ける必要がある。このテストは template-tags-config.php 自身のソースを
	 * extract_template_tags_config_style_requires()（ハードコードした期待値一覧ではない。
	 * `package/` パスに絞り込んでいる理由、緩い方／厳密な方の正規表現を分けている理由は
	 * 同ヘルパーの docblock を参照）で読み取り、veu_get_shared_template_tags_files() の出力と
	 * 突き合わせる。これにより、共有パッケージファイルが template-tags-config.php に
	 * 追加・削除されたのに veu_get_shared_template_tags_files() 側の更新が漏れると、このテストが
	 * 即座に失敗する。「レポートから静かに抜ける」問題（この issue の指摘の趣旨）を、レポートの
	 * 欠落として事後的に気づくのではなく、その場で検知できるようにするため。
	 *
	 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479#issuecomment-5394857113
	 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479#issuecomment-5395101355
	 */
	public function test_veu_get_shared_template_tags_files_matches_template_tags_config() {
		$config_path = VEU_DIRECTORY_PATH . '/inc/template-tags/template-tags-config.php';
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading this plugin's own local config file for static verification, not a remote URL.
		$config_source = file_get_contents( $config_path );
		$this->assertNotFalse( $config_source, '前提: template-tags-config.php を読み込めること' );

		$extracted = $this->extract_template_tags_config_style_requires( $config_source );

		$this->assertNotFalse( $extracted['broad_matched_count'], '前提: package/ 配下への require/include の行を抽出できること' );
		$this->assertGreaterThan( 0, $extracted['broad_matched_count'], '前提: template-tags-config.php から package/ 配下への require/include の行を1件以上抽出できること（0件ならこのテスト自体が壊れている）' );
		$this->assertNotFalse( $extracted['narrow_matched_count'], '前提: __DIR__ 形式で package/ 配下へ require/include している行を抽出できること' );

		$expected_files = array();
		foreach ( $extracted['narrow_paths'] as $relative_require ) {
			$expected_files[] = VEU_DIRECTORY_PATH . '/inc/template-tags' . $relative_require;
		}

		$actual_files = veu_get_shared_template_tags_files();

		$this->assertSame(
			$expected_files,
			$actual_files,
			'veu_get_shared_template_tags_files() は template-tags-config.php の __DIR__ 形式の package/ 配下への require/include と対象・順序ともに完全一致するはず（config 側が正）'
		);

		// 緩い方の正規表現で数えた「config が実際に読み込んでいる package/ 配下ファイルの総数」と、
		// 一覧の件数が一致することも確認する。厳密な方の正規表現がパスを再構築しきれない書式で
		// 共有ファイルが追加された場合でも、この件数チェックだけは検知できるようにするため
		// （extract_template_tags_config_style_requires() の docblock 参照）.
		$this->assertSame(
			count( $actual_files ),
			$extracted['broad_matched_count'],
			'veu_get_shared_template_tags_files() の件数は、config 側で実際に require/include されている package/ 配下ファイルの総数と一致するはず（書式に関わらず件数だけは必ず検知する保険）'
		);
	}

	/**
	 * A non-`package/` require/include line added to template-tags-config.php (e.g. an
	 * ExUnit-specific, non-shared file, as Issue #1478 is expected to add) must NOT be counted by
	 * extract_template_tags_config_style_requires(), and therefore must not cause
	 * test_veu_get_shared_template_tags_files_matches_template_tags_config() to fail.
	 *
	 * template-tags-config.php に `package/` 配下ではない require/include の行が追加されても
	 * （例: Issue #1478 で追加見込みの ExUnit 固有の非共有ファイル）、
	 * extract_template_tags_config_style_requires() がそれを数えてはいけないこと、したがって
	 * test_veu_get_shared_template_tags_files_matches_template_tags_config() を失敗させては
	 * いけないことを検証する。
	 *
	 * Uses synthetic source (not the real template-tags-config.php file) so this test does not
	 * depend on Issue #1478's branch having landed, and keeps passing regardless of when it does.
	 *
	 * 実ファイルではなく合成したソースを使うため、このテストは Issue #1478 のブランチが
	 * 取り込まれているかどうかに依存せず、いつ取り込まれても通り続ける。
	 *
	 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
	 */
	public function test_veu_get_shared_template_tags_files_matches_template_tags_config_ignores_non_package_requires() {
		$synthetic_config_source = <<<'PHP'
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/package/template-tags.php';
require_once __DIR__ . '/package/template-tags-veu.php';
require_once __DIR__ . '/package/template-tags-veu-old.php';
// Issue #1478 相当: 共有パッケージではない ExUnit 固有のファイルの読み込み.
require_once __DIR__ . '/exunit-only-helper.php';
PHP;

		$extracted = $this->extract_template_tags_config_style_requires( $synthetic_config_source );

		$this->assertSame( 3, $extracted['broad_matched_count'], 'package/ 配下ではない読み込み行（Issue #1478 相当）は、緩い方の件数に含めてはいけない' );
		$this->assertSame( 3, $extracted['narrow_matched_count'], 'package/ 配下ではない読み込み行は、厳密な方の件数にも含めてはいけない' );
		$this->assertSame(
			array( '/package/template-tags.php', '/package/template-tags-veu.php', '/package/template-tags-veu-old.php' ),
			$extracted['narrow_paths'],
			'抽出されるパスは package/ 配下の3件のみで、非共有ファイルの行は含まれないはず'
		);
	}

	/**
	 * veu_template_tags_status_extract_function_names() correctly tracks brace depth through
	 * three edge cases that could otherwise misclassify a class/enum method as a file-scope
	 * function: string interpolation ("{$var}"), enum declarations (T_ENUM, PHP 8.1+), and
	 * `Foo::class` (which also tokenizes its `class` keyword as T_CLASS).
	 *
	 * veu_template_tags_status_extract_function_names() が、クラス／enum のメソッドを
	 * ファイルスコープの関数と誤認しうる3つの境界ケース（文字列内変数展開 "{$var}"、enum 宣言
	 * （T_ENUM, PHP 8.1以降）、`Foo::class`（`class` キーワードも T_CLASS としてトークン化される
	 * ）を正しく波括弧の深さで追跡できることを検証する。
	 *
	 * Uses a synthetic fixture file
	 * (tests/fixtures/template-tags-status-bracket-tracking-fixture.php) rather than any of the
	 * real shared files, since none of the three real shared files currently use these
	 * constructs -- see the fixture file's own docblock for what each part covers.
	 *
	 * 実際の3つの共有ファイルは現状これらの書き方を使っていないため、実ファイルではなく合成
	 * フィクスチャファイル（tests/fixtures/template-tags-status-bracket-tracking-fixture.php）を
	 * 使う。各部分が何をカバーしているかはフィクスチャファイル自身の docblock を参照。
	 */
	public function test_veu_template_tags_status_extract_function_names_bracket_edge_cases() {
		$fixture_path = __DIR__ . '/fixtures/template-tags-status-bracket-tracking-fixture.php';
		$this->assertFileExists( $fixture_path, '前提: フィクスチャファイルが存在すること' );

		$actual = veu_template_tags_status_extract_function_names( $fixture_path );

		$expected_included = array(
			'veu_bracket_fixture_before_class',
			// `SomeClass::class` の `class` を実際のクラス宣言と誤認しなければ、この if ブロック
			// 内の関数もファイルスコープの関数として正しく検出されるはず.
			'veu_bracket_fixture_inside_class_const_if',
			'veu_bracket_fixture_after_everything',
		);
		foreach ( $expected_included as $function_name ) {
			$this->assertContains( $function_name, $actual, $function_name . ' はファイルスコープの関数として検出されるはず' );
		}

		$expected_excluded = array(
			// 文字列内変数展開を含むメソッド。ここで波括弧の深さがずれると、後続の
			// should_stay_a_method() がファイルスコープの関数として誤検出されてしまう.
			'uses_curly_interpolation',
			'should_stay_a_method',
		);
		// T_ENUM は PHP 8.1 以降にのみ存在する。それより前のランタイムでは enum 構文自体が特別
		// 扱いされず、このケースの検証はそもそも成立しないためスキップする（本体側も T_ENUM を
		// defined() で防御的にガードしているのと対称）.
		if ( defined( 'T_ENUM' ) ) {
			$expected_excluded[] = 'should_stay_an_enum_method';
		}
		foreach ( $expected_excluded as $function_name ) {
			$this->assertNotContains( $function_name, $actual, $function_name . ' はクラス／enum のメソッドなので、ファイルスコープの関数として検出されてはいけない' );
		}
	}
}
