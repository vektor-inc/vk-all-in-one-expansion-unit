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
 * class/interface/trait, and not anonymous functions/closures).
 *
 * PHP ファイルがファイルスコープで宣言している関数名（クラス／インターフェース／トレイトの
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
 * @param string $file_path Absolute path to the PHP file to inspect.
 * @return string[] Function names, in declaration order, without duplicates.
 */
function veu_extract_top_level_function_names( $file_path ) {
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

	$function_names = array();
	$brace_depth    = 0;
	// Stack of brace depths at which a class/interface/trait body began, so methods (declared
	// while this stack is non-empty) are not mistaken for file-scope functions.
	// クラス／インターフェース／トレイトの本体が始まった時点の波括弧の深さを積むスタック。
	// これが空でない間に宣言された関数はメソッドなので、ファイルスコープの関数として扱わない.
	$class_body_start   = array();
	$expect_class_brace = false;

	$token_count = count( $tokens );
	for ( $i = 0; $i < $token_count; $i++ ) {
		$token = $tokens[ $i ];

		if ( is_array( $token ) ) {
			$token_id = $token[0];

			if ( in_array( $token_id, array( T_CLASS, T_INTERFACE, T_TRAIT ), true ) ) {
				$expect_class_brace = true;
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
				continue;
			}
			// Single-character tokens (braces etc.) are returned as plain strings (not arrays) by
			// token_get_all(), which is why they are handled in the elseif branches below rather
			// than here.
		} elseif ( '{' === $token ) {
			if ( $expect_class_brace ) {
				$class_body_start[] = $brace_depth;
				$expect_class_brace = false;
			}
			++$brace_depth;
		} elseif ( '}' === $token ) {
			--$brace_depth;
			if ( ! empty( $class_body_start ) && end( $class_body_start ) === $brace_depth ) {
				array_pop( $class_body_start );
			}
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
function veu_get_relative_path_from_abspath( $file_path ) {
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
function veu_find_plugin_by_file( $file_path ) {
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

	$plugin = veu_find_plugin_by_file( $defined_file );
	if ( null !== $plugin ) {
		return array(
			'type'     => 'plugin',
			'name'     => $plugin['name'],
			'version'  => $plugin['version'],
			'basename' => $plugin['basename'],
		);
	}

	$relative_path = veu_get_relative_path_from_abspath( $defined_file );
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
		$function_names = veu_extract_top_level_function_names( $file_path );

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
 * / --format=csv don't need to parse a joined string back apart.
 *
 * サイトヘルス「情報」タブ（同一ファイルの複数採用元を "A / B" のように1つの文字列へ結合して
 * 表示する）とは異なり、ここでは採用元ごとに行を分ける。--format=json / --format=csv を使う
 * スクリプト側が結合済み文字列を再度分解しなくて済むようにするため。
 *
 * @param array|null $status Pre-computed status from veu_get_shared_template_tags_status(), or
 *                            null to compute it. Accepting an explicit $status keeps this
 *                            function testable with synthetic data (e.g. fallback branches that
 *                            are hard to reproduce with real plugins installed).
 * @return array<int, array{file:string, product:string, version:string, plugin:string}>
 */
function veu_get_shared_template_tags_status_rows( $status = null ) {
	if ( null === $status ) {
		$status = veu_get_shared_template_tags_status();
	}

	$rows = array();

	foreach ( $status as $file_status ) {
		if ( empty( $file_status['sources'] ) ) {
			$rows[] = array(
				'file'    => $file_status['file'],
				'product' => 'Not loaded',
				'version' => '',
				'plugin'  => '',
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
					);
					break;

				case 'unidentified_file':
					$rows[] = array(
						'file'    => $file_status['file'],
						'product' => 'Could not identify the plugin (defined in ' . $source['relative_path'] . ')',
						'version' => '',
						'plugin'  => '',
					);
					break;

				default:
					$rows[] = array(
						'file'    => $file_status['file'],
						'product' => 'Could not identify the plugin',
						'version' => '',
						'plugin'  => '',
					);
					break;
			}
		}
	}

	return $rows;
}
