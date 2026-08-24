<?php
/**
 * Class TemplateTagsParityTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * ExUnit 所有の inc/template-tags/exunit-template-tags.php にある "veu_" 関数群が、
 * 同梱コピーである inc/template-tags/package/template-tags.php の対応する "vk_" 関数の
 * ロジックから静かに乖離していないかを機械的に固定するテスト。
 *
 * 背景: exunit-template-tags.php は「複製」であって「委譲」ではない設計にしている（理由は
 * exunit-template-tags.php 自身の docblock を参照）。この設計最大の保守リスクは、
 * package/template-tags.php が vektor-wp-libraries との同期で更新された際に、複製側の
 * exunit-template-tags.php が追随されず、気づかれないまま中身がずれてしまうことである。
 * 人の注意力に頼らず検知できるよう、両者の PHP トークン列を比較して固定する。
 *
 * 比較方法: コメント・空白トークンを除去した上で、以下の既知の差分だけを正規化してから
 * トークン列を突き合わせる。
 * 1. 関数宣言名そのもの（vk_xxx <-> veu_xxx は当然異なるため）。
 * 2. veu_ 側が内部で package/ の "vk_" ではなく自身の "veu_" 版を呼ぶ設計上の差分
 *    （例: vk_get_page_for_posts() -> veu_get_page_for_posts()）。
 * 3. veu_the_post_type_check_list() 側だけに入れたセキュリティ強化の esc_attr( $key )
 *    （安藤さんのコードレビュー指摘・投稿タイプ名は WordPress 側で英数字・ハイフン・
 *    アンダースコアに制限されるため出力は実質変わらない、レビュー済みの意図的な差分）。
 * 上記以外の差分は原本からの乖離としてテスト失敗にする。
 *
 * A mechanical check that ExUnit's own "veu_" functions in
 * inc/template-tags/exunit-template-tags.php have not silently drifted from the
 * corresponding "vk_" functions in the bundled copy
 * inc/template-tags/package/template-tags.php.
 *
 * Background: exunit-template-tags.php is designed as a full duplicate, not a thin wrapper
 * that delegates to "vk_" (see that file's own docblock for why). The biggest maintenance
 * risk of that design is that when package/template-tags.php is updated by a
 * vektor-wp-libraries sync, the duplicate in exunit-template-tags.php is not updated to
 * match, and the drift goes unnoticed. This test catches that mechanically instead of
 * relying on human attention.
 *
 * Method: strip comment and whitespace tokens, then normalize only the following known,
 * reviewed differences before comparing PHP token streams.
 * 1. The function's own declaration name (vk_xxx vs veu_xxx necessarily differ).
 * 2. The design decision that "veu_" functions call ExUnit's own "veu_" helper instead of
 *    package/'s "vk_" helper internally (e.g. vk_get_page_for_posts() ->
 *    veu_get_page_for_posts()).
 * 3. The esc_attr( $key ) added only on the veu_the_post_type_check_list() side (a
 *    reviewed, deliberate hardening fix; post type slugs are restricted by WordPress to
 *    alphanumerics, hyphens and underscores, so the actual output is unchanged).
 * Any other difference is treated as an unreviewed drift and fails the test.
 *
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1478
 */
class TemplateTagsParityTest extends WP_UnitTestCase {

	/**
	 * 比較対象の "veu_" 関数 => 対応する "vk_" 関数 と、個別の正規化オプションのマップ。
	 * Map of "veu_" function name => its corresponding "vk_" function name, plus
	 * per-pair normalization options.
	 *
	 * @var array
	 */
	const FUNCTION_PAIRS = array(
		'veu_get_page_for_posts'                           => array( 'vk_name' => 'vk_get_page_for_posts' ),
		'veu_get_post_type'                                => array( 'vk_name' => 'vk_get_post_type' ),
		'veu_get_page_description'                         => array( 'vk_name' => 'vk_get_page_description' ),
		'veu_the_post_type_check_list'                     => array(
			'vk_name'            => 'vk_the_post_type_check_list',
			// 意図的な差分（安藤さんのコードレビュー指摘）。詳細はクラス docblock を参照.
			// Deliberate, reviewed difference. See the class docblock for details.
			'strip_esc_attr_key' => true,
		),
		'veu_the_taxonomy_check_list'                      => array( 'vk_name' => 'vk_the_taxonomy_check_list' ),
		'veu_the_post_type_check_list_saved_array_convert' => array( 'vk_name' => 'vk_the_post_type_check_list_saved_array_convert' ),
		'veu_is_checked'                                   => array( 'vk_name' => 'vk_is_checked' ),
		'veu_sanitize_number'                              => array( 'vk_name' => 'vk_sanitize_number' ),
		'veu_is_excerpt'                                   => array( 'vk_name' => 'vk_is_excerpt' ),
	);

	/**
	 * veu_ 側が package/ の "vk_" ではなく自身の "veu_" 版を呼ぶ設計上の内部呼び出しリネーム。
	 * vk_ 側のトークン列にこの左辺の名前が現れたら、比較前に右辺の名前へ正規化する。
	 * Internal-call renames reflecting the design where "veu_" functions call ExUnit's own
	 * "veu_" helper instead of package/'s "vk_" helper. When a left-hand name appears in the
	 * vk_ token stream, it is normalized to the right-hand name before comparison.
	 *
	 * @var array
	 */
	const INTERNAL_CALL_RENAMES = array(
		'vk_get_page_for_posts' => 'veu_get_page_for_posts',
	);

	/**
	 * veu_ 関数の実装が、対応する vk_ 関数からトークン単位で乖離していないことを検証する。
	 * コメント・空白の違いは無視し、既知の意図的な差分（内部呼び出しのリネーム・
	 * esc_attr( $key ) の追加）だけを許容する。
	 *
	 * Verifies each veu_ function's implementation has not drifted from its corresponding
	 * vk_ function at the token level. Comment and whitespace differences are ignored; only
	 * the known, deliberate differences (internal-call renames, the added esc_attr( $key ))
	 * are tolerated.
	 */
	public function test_veu_functions_match_vk_source_of_truth() {
		$vk_file  = VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags.php';
		$veu_file = VEU_DIRECTORY_PATH . '/inc/template-tags/exunit-template-tags.php';

		$this->assertFileExists( $vk_file, '比較対象の package/template-tags.php が見つかりません' );
		$this->assertFileExists( $veu_file, '比較対象の exunit-template-tags.php が見つかりません' );

		foreach ( self::FUNCTION_PAIRS as $veu_name => $spec ) {
			$vk_name = $spec['vk_name'];

			$vk_tokens  = $this->extract_function_tokens( $vk_file, $vk_name );
			$veu_tokens = $this->extract_function_tokens( $veu_file, $veu_name );

			$this->assertNotNull( $vk_tokens, "{$vk_name}() が {$vk_file} 内に見つかりませんでした" );
			$this->assertNotNull( $veu_tokens, "{$veu_name}() が {$veu_file} 内に見つかりませんでした" );

			// vk_ 側のトークン列を正規化: 自分自身の宣言名を veu_ 名へ、
			// 既知の内部呼び出しを veu_ 版へ、それぞれリネームする.
			// Normalize the vk_ side: rename its own declaration name to the veu_ name, and
			// rename known internal calls to their veu_ counterpart.
			$rename_map             = self::INTERNAL_CALL_RENAMES;
			$rename_map[ $vk_name ] = $veu_name;

			$vk_normalized  = $this->rename_tokens( $vk_tokens, $rename_map );
			$veu_normalized = $veu_tokens;

			if ( ! empty( $spec['strip_esc_attr_key'] ) ) {
				$vk_normalized  = $this->strip_esc_attr_key_wrapper( $vk_normalized );
				$veu_normalized = $this->strip_esc_attr_key_wrapper( $veu_normalized );
			}

			$this->assertSame(
				implode( ' ', $vk_normalized ),
				implode( ' ', $veu_normalized ),
				"veu_ 側の {$veu_name}() が、package/template-tags.php の {$vk_name}() から静かに乖離しています。" .
				'vektor-wp-libraries との同期で package/template-tags.php 側が更新された場合、' .
				'exunit-template-tags.php 側にも同じ修正を反映してください。' .
				" (veu_{$veu_name}() has silently drifted from vk_{$vk_name}() in package/template-tags.php. " .
				'If package/template-tags.php was updated by a vektor-wp-libraries sync, apply the ' .
				'same fix to exunit-template-tags.php.)'
			);
		}
	}

	/**
	 * 指定した PHP ファイルから、指定した関数の宣言全体（シグネチャ＋ボディ）を、
	 * コメント・空白トークンを除去した「トークン値の配列」として取り出す。
	 * PHP 自身のトークナイザを使うため、文字列・コメントの中身を波括弧と誤認しない。
	 *
	 * Extract a single function's full declaration (signature + body) from the given PHP
	 * file as an array of token values, with comment/whitespace tokens stripped. Uses PHP's
	 * own tokenizer so brace-counting can never be confused by braces appearing inside a
	 * string or comment.
	 *
	 * @param string $file_path     対象ファイルの絶対パス. Absolute path to the PHP source file.
	 * @param string $function_name 抽出対象の関数名. Function name to extract.
	 * @return array|null 正規化済みトークン値の配列。見つからない場合は null.
	 *                     Normalized token values, or null if not found.
	 */
	private function extract_function_tokens( $file_path, $function_name ) {
		// リモート URL ではなく、プラグイン自身に同梱されたローカル PHP ソースファイルを
		// 静的解析のために読むだけなので wp_remote_get() は使わない.
		// Not a remote URL — this reads a local PHP source file bundled with the plugin
		// itself, purely for static analysis, so wp_remote_get() does not apply here.
		$source = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$tokens = token_get_all( $source );
		$count  = count( $tokens );

		for ( $i = 0; $i < $count; $i++ ) {
			$token = $tokens[ $i ];
			if ( is_array( $token ) && T_FUNCTION === $token[0] ) {
				// 'function' の次の非空白トークンが探している関数名かどうかを見る.
				// Check whether the next non-whitespace token after 'function' is the
				// function name we are looking for.
				$j = $i + 1;
				while ( $j < $count && is_array( $tokens[ $j ] ) && T_WHITESPACE === $tokens[ $j ][0] ) {
					++$j;
				}
				if ( $j < $count && is_array( $tokens[ $j ] ) && T_STRING === $tokens[ $j ][0] && $tokens[ $j ][1] === $function_name ) {
					return $this->collect_function_body_tokens( $tokens, $i, $count );
				}
			}
		}

		return null;
	}

	/**
	 * 'function' トークンの位置から開始し、シグネチャを経て波括弧の深さを追跡しながら
	 * 関数本体の終わりまでトークンを収集する（コメント・空白は除く）。
	 *
	 * Starting at the 'function' token, collect tokens through the signature and the
	 * function body (tracking brace depth) until the matching closing brace, skipping
	 * comment and whitespace tokens.
	 *
	 * @param array $tokens token_get_all() の結果全体. Full result of token_get_all().
	 * @param int   $start  'function' トークンの index. Index of the 'function' token.
	 * @param int   $count  $tokens の要素数. Number of elements in $tokens.
	 * @return array 正規化済みトークン値の配列. Normalized token values.
	 */
	private function collect_function_body_tokens( array $tokens, $start, $count ) {
		$brace_depth  = 0;
		$body_started = false;
		$collected    = array();

		for ( $i = $start; $i < $count; $i++ ) {
			$token = $tokens[ $i ];

			if ( is_array( $token ) ) {
				list( $id, $text ) = $token;
				if ( T_WHITESPACE === $id || T_COMMENT === $id || T_DOC_COMMENT === $id ) {
					continue;
				}
				$collected[] = $text;
			} else {
				// 1文字トークン（'{'・'}'・'('・';' 等）はそのまま文字列として渡ってくる.
				// A single-character token (e.g. '{', '}', '(', ';') arrives as a plain
				// string already.
				$collected[] = $token;
				if ( '{' === $token ) {
					++$brace_depth;
					$body_started = true;
				} elseif ( '}' === $token ) {
					--$brace_depth;
					if ( $body_started && 0 === $brace_depth ) {
						break;
					}
				}
			}
		}

		return $collected;
	}

	/**
	 * トークン値の配列に対し、指定したリネームマップに従って一致するトークンを置き換える。
	 * Replace tokens in a token-value array according to the given rename map.
	 *
	 * @param array $tokens     対象のトークン値配列. Token values to process.
	 * @param array $rename_map 置換前 => 置換後 のマップ. Map of before => after.
	 * @return array 置換後のトークン値配列. Token values after renaming.
	 */
	private function rename_tokens( array $tokens, array $rename_map ) {
		foreach ( $tokens as &$token ) {
			if ( isset( $rename_map[ $token ] ) ) {
				$token = $rename_map[ $token ];
			}
		}
		return $tokens;
	}

	/**
	 * トークン値の配列から `esc_attr ( $key )` という並びを探し、`$key` 単体に畳み込む。
	 * veu_the_post_type_check_list() 側だけに追加したセキュリティ強化差分を、
	 * 比較前に無害化するために使う（詳細はクラス docblock を参照）。
	 *
	 * Find the token sequence `esc_attr ( $key )` in a token-value array and collapse it to
	 * a bare `$key`. Used to neutralize, before comparison, the hardening difference added
	 * only on the veu_the_post_type_check_list() side (see the class docblock for details).
	 *
	 * @param array $tokens 対象のトークン値配列. Token values to process.
	 * @return array 畳み込み後のトークン値配列. Token values after collapsing.
	 */
	private function strip_esc_attr_key_wrapper( array $tokens ) {
		$result = array();
		$count  = count( $tokens );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( 'esc_attr' === $tokens[ $i ]
				&& isset( $tokens[ $i + 1 ] ) && '(' === $tokens[ $i + 1 ]
				&& isset( $tokens[ $i + 2 ] ) && '$key' === $tokens[ $i + 2 ]
				&& isset( $tokens[ $i + 3 ] ) && ')' === $tokens[ $i + 3 ]
			) {
				$result[] = '$key';
				$i       += 3;
				continue;
			}
			$result[] = $tokens[ $i ];
		}

		return $result;
	}
}
