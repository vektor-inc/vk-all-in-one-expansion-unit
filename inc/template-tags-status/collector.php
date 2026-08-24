<?php
/**
 * Collects, for each of ExUnit's shared template-tag files, which plugin's bundled copy is
 * currently the one actually running.
 *
 * ExUnit と一部の Vektor 製プラグイン（VK Post Author Display 等）は、同じ共有テンプレートタグ
 * ファイル（inc/template-tags/package/ 配下）をそれぞれ同梱している。ファイル内の各関数は
 * function_exists() で個別にガードされているため、先に読み込まれた側の実装が採用され、後から
 * 読み込まれた側は無視される。このファイルは、共有ファイルごとに「どのプラグインのコピーが
 * 現在採用されているか」を機械的に集計する事実収集レイヤーで、表示用の整形は行わない。
 * サイトヘルス「情報」タブ（inc/template-tags-status/site-health.php）と WP-CLI コマンド
 * （inc/template-tags-status/wp-cli.php）の両方がこの層の関数を共有する。
 *
 * This file is the fact-collection layer only; it never formats output for display. Both the
 * Site Health "Info" tab integration (site-health.php) and the WP-CLI command (wp-cli.php)
 * build on top of the functions defined here.
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * List the shared template-tag package files, in the same order they are require_once'd by
 * inc/template-tags/template-tags-config.php.
 *
 * 共有テンプレートタグパッケージファイルの一覧を、inc/template-tags/template-tags-config.php
 * が require_once している順序と同じ順序で返す。
 *
 * inc/template-tags/template-tags-config.php is the source of truth: it is the file that
 * actually loads ExUnit's shared package copies. This list is a separate, hand-written copy of
 * that -- there is no code-level link between them -- so if a shared file is ever added to or
 * removed from template-tags-config.php, THIS function must be updated to match by hand. That
 * match (same files, same order) is enforced by
 * tests/test-template-tags-status.php::test_veu_get_shared_template_tags_files_matches_template_tags_config(),
 * which reads template-tags-config.php's own require_once lines and fails the suite if this list
 * ever drifts from them, so an update here left out is caught immediately rather than silently
 * dropping a file from the Site Health / WP-CLI report.
 *
 * 正（source of truth）は inc/template-tags/template-tags-config.php 側であり、実際に ExUnit の
 * 共有パッケージのコピーを読み込んでいるのはそちらである。この一覧はそれとは別に手書きされた
 * コピーで、コード上の連動は無いため、template-tags-config.php に共有ファイルが追加・削除された
 * 場合はこの関数側も手動で追随させる必要がある。両者が一致していること（対象・順序とも）は
 * tests/test-template-tags-status.php の
 * test_veu_get_shared_template_tags_files_matches_template_tags_config() が保証している。同テストは
 * template-tags-config.php 自身の require_once 行を読み取り、この一覧とずれた瞬間に失敗するため、
 * ここでの更新漏れは、サイトヘルス／WP-CLI のレポートから静かにファイルが抜け落ちるのではなく、
 * その場でテストの失敗として検知できる。
 *
 * @return string[] Absolute file paths.
 */
function veu_get_shared_template_tags_files() {
	return array(
		VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags.php',
		VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags-veu.php',
		VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags-veu-old.php',
	);
}

/**
 * Extract the names of the functions a PHP file declares at file scope (i.e. not methods on a
 * class/interface/trait/enum, and not anonymous functions/closures).
 *
 * PHP ファイルがファイルスコープで宣言している関数名（クラス／インターフェース／トレイト／enum の
 * メソッドではなく、無名関数／クロージャでもないもの）を抽出する。
 *
 * The file is read with token_get_all() rather than any hardcoded function list, so newly added
 * functions are picked up automatically. This is intentional: if this list had to be maintained
 * by hand, a missed update would recreate exactly the kind of invisible "which copy actually
 * runs" ambiguity this feature exists to surface.
 *
 * このファイルは関数名をハードコードした配列を持たず、token_get_all() でファイル自体を解析する。
 * 手動更新が必要な一覧にすると、更新漏れがまさにこの機能が可視化しようとしている
 * 「気づかないうちにどちらが採用されているか分からなくなる」問題を再現してしまうため。
 *
 * Brace-depth tracking has three deliberate edge cases handled below (see
 * tests/fixtures/template-tags-status-bracket-tracking-fixture.php for a regression fixture):
 * - String interpolation ("{$var}") opens with the special T_CURLY_OPEN token instead of a plain
 *   '{' string token, but always closes with a plain '}' string token. Left uncounted, this would
 *   desync $brace_depth from the real nesting level and could make a later class method look like
 *   a file-scope function.
 * - T_ENUM (PHP 8.1+) is treated the same as class/interface/trait, so an enum's methods are not
 *   mistaken for file-scope functions. On PHP < 8.1 (where T_ENUM does not exist as a token/
 *   constant), the token simply cannot appear, so this is a no-op there rather than a fatal error.
 * - `Foo::class` also tokenizes its `class` keyword as T_CLASS. Only tokens NOT immediately
 *   preceded by `::` are treated as an actual class/interface/trait/enum declaration.
 *
 * 波括弧の深さの追跡には、意図的に対応している3つの境界ケースがある
 * （回帰用フィクスチャは tests/fixtures/template-tags-status-bracket-tracking-fixture.php を参照）。
 * - 文字列内変数展開（"{$var}"）は通常の '{' 文字列トークンではなく専用の T_CURLY_OPEN トークンで
 *   開始するが、閉じは通常の '}' 文字列トークンで来る。数えないと $brace_depth が実際のネストと
 *   ずれ、後続のクラスのメソッドをファイルスコープの関数と誤認しうる。
 * - T_ENUM（PHP 8.1以降）もクラス／インターフェース／トレイトと同様に扱い、enum のメソッドを
 *   ファイルスコープの関数と誤認しないようにする。PHP 8.1未満では T_ENUM はトークン／定数として
 *   存在せずこのトークン自体が出現しえないため、単に無効化されるだけで Fatal Error にはならない。
 * - `Foo::class` の `class` も T_CLASS としてトークン化される。直前が `::` でないものだけを
 *   実際のクラス／インターフェース／トレイト／enum 宣言として扱う。
 *
 * @param string $file_path Absolute path to the PHP file to inspect.
 * @return string[] Function names, in declaration order, without duplicates.
 */
function veu_template_tags_status_extract_function_names( $file_path ) {
	if ( ! is_readable( $file_path ) ) {
		return array();
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading one of this plugin's own bundled PHP files for static analysis, not remote/user input.
	$source = file_get_contents( $file_path );
	if ( false === $source ) {
		return array();
	}

	$tokens = token_get_all( $source );
	if ( ! is_array( $tokens ) ) {
		return array();
	}

	// T_ENUM only exists from PHP 8.1 onward; referencing the bare constant on an older runtime
	// would itself fatal, so it is added defensively.
	// T_ENUM は PHP 8.1 以降にのみ存在する。古いランタイムで裸の定数を参照するとそれ自体が
	// Fatal Error になるため、防御的に追加する.
	$class_like_token_ids = array( T_CLASS, T_INTERFACE, T_TRAIT );
	if ( defined( 'T_ENUM' ) ) {
		$class_like_token_ids[] = T_ENUM;
	}

	$function_names = array();
	$brace_depth    = 0;
	// Stack of brace depths at which a class/interface/trait/enum body began, so methods
	// (declared while this stack is non-empty) are not mistaken for file-scope functions.
	// クラス／インターフェース／トレイト／enum の本体が始まった時点の波括弧の深さを積むスタック。
	// これが空でない間に宣言された関数はメソッドなので、ファイルスコープの関数として扱わない.
	$class_body_start   = array();
	$expect_class_brace = false;
	// The last non-whitespace/non-comment token seen so far, used only to tell an actual class
	// declaration's `class` keyword apart from the `class` in `Foo::class`.
	// これまでに見た最後の空白／コメント以外のトークン。実際のクラス宣言の `class` キーワードと
	// `Foo::class` の `class` を区別する用途にのみ使う.
	$previous_token_id = null;

	$token_count = count( $tokens );
	for ( $i = 0; $i < $token_count; $i++ ) {
		$token = $tokens[ $i ];

		if ( is_array( $token ) ) {
			$token_id = $token[0];

			if ( in_array( $token_id, array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				// Does not count as "the previous token" for the ::class lookbehind below.
				continue;
			}

			if ( in_array( $token_id, array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) {
				// String interpolation ("{$var}" / "${var}") opens with one of these special
				// tokens but always closes with a plain '}' string token below, so it must be
				// counted here to keep $brace_depth in sync with those closing braces.
				++$brace_depth;
				$previous_token_id = $token_id;
				continue;
			}

			if ( in_array( $token_id, $class_like_token_ids, true ) ) {
				// `Foo::class` also tokenizes its `class` as T_CLASS, so only treat this as an
				// actual class/interface/trait/enum declaration when it is not preceded by `::`.
				if ( T_DOUBLE_COLON !== $previous_token_id ) {
					$expect_class_brace = true;
				}
				$previous_token_id = $token_id;
				continue;
			}

			if ( T_FUNCTION === $token_id ) {
				if ( empty( $class_body_start ) ) {
					// Look ahead, skipping whitespace/comments, for the function name. Anonymous
					// functions/closures have '(' immediately after `function` (no name token in
					// between), so they are naturally excluded here.
					// 空白・コメントを読み飛ばしながら関数名を探す。無名関数／クロージャは
					// `function` の直後が '(' になり名前トークンが挟まらないため、
					// ここでは自然に除外される.
					for ( $j = $i + 1; $j < $token_count; $j++ ) {
						$next = $tokens[ $j ];
						if ( is_array( $next ) ) {
							if ( in_array( $next[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
								continue;
							}
							if ( T_STRING === $next[0] ) {
								$function_names[] = $next[1];
							}
						}
						break;
					}
				}
				$previous_token_id = $token_id;
				continue;
			}

			$previous_token_id = $token_id;
			// Single-character tokens (braces etc.) are returned as plain strings (not arrays) by
			// token_get_all(), which is why they are handled in the elseif branches below rather
			// than here.
		} elseif ( '{' === $token ) {
			if ( $expect_class_brace ) {
				$class_body_start[] = $brace_depth;
				$expect_class_brace = false;
			}
			++$brace_depth;
			$previous_token_id = $token;
		} elseif ( '}' === $token ) {
			--$brace_depth;
			if ( ! empty( $class_body_start ) && end( $class_body_start ) === $brace_depth ) {
				array_pop( $class_body_start );
			}
			$previous_token_id = $token;
		} else {
			$previous_token_id = $token;
		}
	}

	return array_values( array_unique( $function_names ) );
}

/**
 * Express a file path relative to ABSPATH, without ever leaking the server's absolute path.
 *
 * ファイルパスを ABSPATH からの相対パスとして表現する。サーバー上の絶対パスは出さない。
 *
 * @param string $file_path Absolute file path.
 * @return string|null Relative path (no leading slash), or null when $file_path is not located
 *                      under ABSPATH and therefore cannot be safely relativized.
 */
function veu_template_tags_status_relative_path( $file_path ) {
	$normalized_file    = wp_normalize_path( $file_path );
	$normalized_abspath = wp_normalize_path( ABSPATH );

	if ( 0 !== strpos( $normalized_file, $normalized_abspath ) ) {
		return null;
	}

	return substr( $normalized_file, strlen( $normalized_abspath ) );
}

/**
 * Find which installed plugin a given file belongs to, using plugin header data (Name/Version)
 * rather than any filter-modifiable value such as veu_get_name().
 *
 * 指定されたファイルがどのインストール済みプラグインに属するかを、プラグインヘッダのデータ
 * （Name / Version）を使って特定する。apply_filters() でサイト側から書き換え可能な
 * veu_get_name() のような値は使わない。
 *
 * @param string $file_path Absolute path of the file that defines the function in question.
 * @return array{name:string,version:string,basename:string}|null Plugin info, or null when no
 *                                                                  installed plugin's directory
 *                                                                  contains this file.
 */
function veu_template_tags_status_find_plugin( $file_path ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$normalized_file = wp_normalize_path( $file_path );
	$plugins_dir     = trailingslashit( wp_normalize_path( WP_PLUGIN_DIR ) );

	$all_plugins = get_plugins();

	$matched_basename   = null;
	$matched_dir_length = -1;

	foreach ( $all_plugins as $plugin_basename => $plugin_data ) {
		$plugin_dir_relative = dirname( $plugin_basename );
		if ( '.' === $plugin_dir_relative ) {
			// Single-file plugins living directly under wp-content/plugins/ cannot bundle a
			// sub-path like inc/template-tags/package/*.php, so they are not candidates here.
			continue;
		}

		$plugin_dir_abs = $plugins_dir . $plugin_dir_relative . '/';

		// Prefer the most specific (longest) matching plugin directory, in case one plugin's
		// folder name happens to be a prefix of another's.
		if ( 0 === strpos( $normalized_file, $plugin_dir_abs ) && strlen( $plugin_dir_abs ) > $matched_dir_length ) {
			$matched_dir_length = strlen( $plugin_dir_abs );
			$matched_basename   = $plugin_basename;
		}
	}

	if ( null === $matched_basename ) {
		return null;
	}

	return array(
		'name'     => $all_plugins[ $matched_basename ]['Name'],
		'version'  => $all_plugins[ $matched_basename ]['Version'],
		'basename' => $matched_basename,
	);
}

/**
 * Turn a "defined in this file" fact into a source descriptor: which plugin (if identifiable),
 * or a documented fallback when it cannot be identified.
 *
 * 「このファイルで定義されている」という事実を、採用元の記述（特定できればどのプラグインか、
 * できなければ既定のフォールバック）に変換する。
 *
 * This function is intentionally a pure function of its $defined_file argument (rather than
 * reaching for ReflectionFunction itself) so the "could not identify" fallback branches can be
 * exercised directly and deterministically from tests.
 *
 * この関数はあえて $defined_file 引数だけに依存する純粋関数にしている（内部で
 * ReflectionFunction を呼ばない）。これにより「特定できない」場合のフォールバック分岐を、
 * テストから直接かつ決定的に検証できるようにしている。
 *
 * @param string|false $defined_file Absolute path of the defining file, or false when unknown
 *                                    (e.g. an internal/core PHP function, or reflection failed).
 * @return array Source descriptor. One of:
 *               - array{type:'plugin',name:string,version:string,basename:string}
 *               - array{type:'unidentified_file',relative_path:string}
 *               - array{type:'unidentified'}
 */
function veu_identify_template_tags_source_from_file( $defined_file ) {
	if ( empty( $defined_file ) || ! is_string( $defined_file ) ) {
		return array( 'type' => 'unidentified' );
	}

	$plugin = veu_template_tags_status_find_plugin( $defined_file );
	if ( null !== $plugin ) {
		return array(
			'type'     => 'plugin',
			'name'     => $plugin['name'],
			'version'  => $plugin['version'],
			'basename' => $plugin['basename'],
		);
	}

	$relative_path = veu_template_tags_status_relative_path( $defined_file );
	if ( null === $relative_path ) {
		// Cannot safely express this path relative to ABSPATH (e.g. it points outside the
		// WordPress install entirely) -- never fall back to leaking the absolute path.
		return array( 'type' => 'unidentified' );
	}

	return array(
		'type'          => 'unidentified_file',
		'relative_path' => $relative_path,
	);
}

/**
 * Resolve which source (plugin, or a fallback descriptor) currently provides a given function.
 *
 * 指定した関数を現在どの採用元（プラグイン、またはフォールバック記述）が提供しているかを解決する。
 *
 * @param string $function_name Function name to look up.
 * @return array|null Source descriptor (see veu_identify_template_tags_source_from_file()), or
 *                     null when the function is not currently defined at all.
 */
function veu_identify_shared_template_tags_source( $function_name ) {
	if ( ! function_exists( $function_name ) ) {
		return null;
	}

	$defined_file = false;
	try {
		$reflection   = new ReflectionFunction( $function_name );
		$defined_file = $reflection->getFileName();
	} catch ( \Throwable $e ) {
		$defined_file = false;
	}

	return veu_identify_template_tags_source_from_file( $defined_file );
}

/**
 * Build a deduplication key for a source descriptor, so the same source is never listed twice
 * for one file.
 *
 * 採用元記述の重複排除キーを組み立てる。1つのファイルに対して同じ採用元が2回以上表示されない
 * ようにするために使う。
 *
 * @param array $source Source descriptor.
 * @return string
 */
function veu_get_template_tags_source_dedupe_key( array $source ) {
	switch ( $source['type'] ) {
		case 'plugin':
			return 'plugin:' . $source['basename'];
		case 'unidentified_file':
			return 'unidentified_file:' . $source['relative_path'];
		default:
			return 'unidentified';
	}
}

/**
 * Collect, for each shared template-tag file, the set of sources currently providing its
 * functions.
 *
 * 共有テンプレートタグファイルごとに、その関数を現在提供している採用元の集合を収集する。
 *
 * This is the single fact-collection entry point shared by the Site Health "Info" tab and the
 * `wp exunit template-tags status` WP-CLI command; both only format this data differently.
 *
 * これがサイトヘルス「情報」タブと `wp exunit template-tags status` WP-CLI コマンドの両方が
 * 共有する唯一の事実収集の入口であり、両者はこのデータを異なる形式に整形するだけである。
 *
 * Note: a source is identified purely by which file currently defines a given function name --
 * there is no check that the defining file is actually a copy of ExUnit's shared package. If
 * another plugin happens to declare an unrelated function that shares one of these names in a
 * completely unrelated file, that plugin would be listed as a source here too. This is accepted
 * as within scope: the goal is "which currently-defined copy of this function name is running",
 * not "which file is a verified copy of the shared package".
 *
 * 注意: 採用元は「この関数名を現在どのファイルが定義しているか」だけで特定しており、その定義元が
 * 実際に ExUnit の共有パッケージのコピーであるかどうかまでは確認していない。他プラグインが
 * たまたま無関係なファイルでこれらと同名の関数を宣言していた場合、そのプラグインもここに採用元
 * として並ぶ。これは許容範囲としている。目的は「この関数名を現在定義しているのはどれか」であって
 * 「共有パッケージの正当なコピーであることの検証」ではないため.
 *
 * @return array<int, array{file:string, sources:array}> One entry per shared file, in
 *               require_once order, each with:
 *               - 'file'    Bare file name (e.g. "template-tags.php").
 *               - 'sources' De-duplicated list of source descriptors (see
 *                            veu_identify_template_tags_source_from_file()); empty when none of
 *                            the file's functions are currently defined by anything.
 */
function veu_get_shared_template_tags_status() {
	$status = array();

	foreach ( veu_get_shared_template_tags_files() as $file_path ) {
		$function_names = veu_template_tags_status_extract_function_names( $file_path );

		$sources   = array();
		$seen_keys = array();

		foreach ( $function_names as $function_name ) {
			$source = veu_identify_shared_template_tags_source( $function_name );
			if ( null === $source ) {
				// Not currently defined by any loaded copy of this shared file.
				continue;
			}

			$dedupe_key = veu_get_template_tags_source_dedupe_key( $source );
			if ( isset( $seen_keys[ $dedupe_key ] ) ) {
				continue;
			}
			$seen_keys[ $dedupe_key ] = true;
			$sources[]                = $source;
		}

		$status[] = array(
			'file'    => basename( $file_path ),
			'sources' => $sources,
		);
	}

	return $status;
}

/**
 * Normalize the collected status into a flat, one-row-per-source table suitable for
 * `WP_CLI\Utils\format_items()`.
 *
 * 収集した状態を、`WP_CLI\Utils\format_items()` にそのまま渡せる「1行1採用元」のフラットな
 * 表に正規化する。
 *
 * Unlike the Site Health "Info" tab (which joins multiple sources for the same file into one
 * "A / B" display string), each source gets its own row here so scripts consuming --format=json
 * / --format=csv don't need to parse a joined string back apart. For the same reason, 'product'
 * never has a file path folded into it (e.g. never "Could not identify the plugin (defined in
 * ...)") -- the path lives in its own 'path' column, so a script never has to pull it back out of
 * a sentence.
 *
 * サイトヘルス「情報」タブ（同一ファイルの複数採用元を "A / B" のように1つの文字列へ結合して
 * 表示する）とは異なり、ここでは採用元ごとに行を分ける。--format=json / --format=csv を使う
 * スクリプト側が結合済み文字列を再度分解しなくて済むようにするため。同じ理由で、'product' には
 * ファイルパスを文中に埋め込まない（"Could not identify the plugin (defined in ...)" のような形に
 * しない）。パスは独立した 'path' 列に持たせ、スクリプト側が文中からパスを取り出し直す必要が
 * ないようにしている。
 *
 * @param array|null $status Pre-computed status from veu_get_shared_template_tags_status(), or
 *                            null to compute it. Accepting an explicit $status keeps this
 *                            function testable with synthetic data (e.g. fallback branches that
 *                            are hard to reproduce with real plugins installed).
 * @return array<int, array{file:string, product:string, version:string, plugin:string, path:string}>
 *               Columns, in order: file, product, version, plugin, path.
 *               - Identified: product/version/plugin filled in, path empty.
 *               - Defining file known but plugin not identified: product is the fixed
 *                 "Could not identify the plugin" text, version/plugin empty, path holds the
 *                 file's path relative to the WordPress root.
 *               - Defining file also unknown, or the shared file is not loaded at all: product is
 *                 "Could not identify the plugin" or "Not loaded" respectively, version/plugin/
 *                 path all empty.
 */
function veu_get_shared_template_tags_status_rows( $status = null ) {
	if ( null === $status ) {
		$status = veu_get_shared_template_tags_status();
	}

	$rows = array();

	foreach ( $status as $file_status ) {
		if ( empty( $file_status['sources'] ) ) {
			// This handles the same "not loaded" case as veu_format_shared_template_tags_status_value()
			// (site-health.php); update that one too when this one changes. They are two
			// separate literals on purpose -- this one is a fixed English string so
			// --format=json/csv output stays stable regardless of site locale, while the Site
			// Health one goes through __() so the admin screen can be translated. Sharing one
			// literal would either break that translation or leak translated text into scripted
			// CLI output. The text happens to read identically ("Not loaded") today, but that is
			// not the point being guarded here -- only the case is, not the wording.
			// これは veu_format_shared_template_tags_status_value()（site-health.php）と同じ
			// 「読み込まれていない」ケースを扱っている。片方を変えたらもう片方も直すこと。あえて
			// 別々のリテラルにしている。こちらはサイトのロケールに関係なく --format=json/csv の
			// 出力を安定させるための固定の英語文字列で、サイトヘルス側は管理画面を翻訳できるよう
			// __() を通す。1つに共通化すると翻訳が効かなくなるか、翻訳済み文言が CLI 出力に
			// 混ざるかのどちらかになる。現状は文言もたまたま同じ（"Not loaded"）だが、ここで
			// 揃えたいのはケースであって文言そのものではない.
			$rows[] = array(
				'file'    => $file_status['file'],
				'product' => 'Not loaded',
				'version' => '',
				'plugin'  => '',
				'path'    => '',
			);
			continue;
		}

		foreach ( $file_status['sources'] as $source ) {
			switch ( $source['type'] ) {
				case 'plugin':
					$rows[] = array(
						'file'    => $file_status['file'],
						'product' => $source['name'],
						'version' => $source['version'],
						'plugin'  => $source['basename'],
						'path'    => '',
					);
					break;

				case 'unidentified_file':
					// This handles the same "defining file known, plugin not identified" case as
					// the 'unidentified_file' branch of veu_format_template_tags_source_label()
					// (site-health.php) -- update that one too when this one changes. See the
					// note in the "Not loaded" branch above for why they are not shared code.
					// UNLIKE that "Not loaded" pair, the text here is INTENTIONALLY NOT the same:
					// this row keeps 'product' as the fixed "Could not identify the plugin" and
					// puts the path in its own 'path' column (see this function's docblock),
					// while the Site Health string embeds the path inline as "...(defined in
					// %s)". Do not "fix" this by folding the path back into 'product' to make the
					// wording match -- that would undo the column split requested in the Issue
					// #1479 UX review specifically so scripts don't have to parse it back out.
					// これは site-health.php の veu_format_template_tags_source_label() の
					// 'unidentified_file' 分岐と同じ「定義元ファイルは分かるがプラグインは特定
					// できない」ケースを扱っている。片方を変えたらもう片方も直すこと。共通化しない
					// 理由は上の "Not loaded" 分岐のコメントを参照。ただしその "Not loaded" の
					// ペアとは違い、ここは文言をあえて揃えていない。この行は 'product' を固定文言
					// "Could not identify the plugin" のままにし、パスは独立した 'path' 列に
					// 入れる（この関数の docblock 参照）。一方サイトヘルス側は "...(defined in
					// %s)" のようにパスを文中に埋め込む。文言を合わせようとして 'product' へ
					// パスを埋め戻してはいけない。それは Issue #1479 の UX レビューで
					// スクリプト側が文字列を分解し直さずに済むよう指示されて分離した列構成を
					// 巻き戻す変更になる.
					$rows[] = array(
						'file'    => $file_status['file'],
						'product' => 'Could not identify the plugin',
						'version' => '',
						'plugin'  => '',
						'path'    => $source['relative_path'],
					);
					break;

				default:
					// This handles the same "nothing identifiable at all" case as the
					// 'unidentified' branch of veu_format_template_tags_source_label()
					// (site-health.php) -- update that one too when this one changes. See the
					// note in the "Not loaded" branch above for why they are not shared code.
					// The text happens to read identically ("Could not identify the plugin")
					// today, same as the "Not loaded" case -- but, as with that case, what must
					// stay in sync is the case being handled, not necessarily the wording.
					// これは site-health.php の veu_format_template_tags_source_label() の
					// 'unidentified' 分岐と同じ「何も手がかりがない」ケースを扱っている。片方を
					// 変えたらもう片方も直すこと。共通化しない理由は上の "Not loaded" 分岐の
					// コメントを参照。現状は文言もたまたま同じ（"Could not identify the
					// plugin"）だが、"Not loaded" のケースと同様、揃えるべきなのは扱うケースで
					// あって、必ずしも文言そのものではない.
					$rows[] = array(
						'file'    => $file_status['file'],
						'product' => 'Could not identify the plugin',
						'version' => '',
						'plugin'  => '',
						'path'    => '',
					);
					break;
			}
		}
	}

	return $rows;
}
