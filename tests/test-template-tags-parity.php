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
 * 上記以外の差分は原本からの乖離としてテスト失敗にする。
 *
 * レビュー済みの意図的な差分（例: veu_the_post_type_check_list() の esc_attr( $key )
 * 追加）は、比較ロジックそのものを手直しするのではなく、FUNCTION_PAIRS 側に宣言的に
 * 記述する（詳細は FUNCTION_PAIRS の docblock を参照）。関数ごとに使い捨てのトークン
 * 書き換え関数を積み増していく方式は、次に手を入れた人が「なぜ落ちるのか」を追跡でき
 * なくなり、最悪 FUNCTION_PAIRS から関数を削って黙らせる壊れ方をする（安藤さんの
 * コードレビュー指摘）ため、この設計は避けている。
 *
 * このテストが落ちたら: package/template-tags.php 側の対応する vk_ 関数を確認する。
 * 同じ修正が必要なら exunit-template-tags.php 側にも反映する。ExUnit だけの意図的な
 * 変更で vk_ 側とは違えたままにしたい場合は、この比較ロジックをその場で書き換えるの
 * ではなく、FUNCTION_PAIRS へレビュー済みの差分として明記して追加する。
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
 * structural differences before comparing PHP token streams.
 * 1. The function's own declaration name (vk_xxx vs veu_xxx necessarily differ).
 * 2. The design decision that "veu_" functions call ExUnit's own "veu_" helper instead of
 *    package/'s "vk_" helper internally (e.g. vk_get_page_for_posts() ->
 *    veu_get_page_for_posts()).
 * Any other difference is treated as an unreviewed drift and fails the test.
 *
 * Reviewed, deliberate differences (e.g. the esc_attr( $key ) added in
 * veu_the_post_type_check_list()) are declared data-first in FUNCTION_PAIRS instead of
 * patching the comparison logic itself (see FUNCTION_PAIRS's own docblock for the available
 * options). Growing a pile of one-off, per-function token-rewrite helpers was rejected: the
 * next person to touch a function would have no way to tell why the test failed, and the
 * likely failure mode is deleting the function's entry from FUNCTION_PAIRS to silence it
 * (a finding from Ando's code review).
 *
 * If this test fails: check the corresponding vk_ function in package/template-tags.php. If
 * the same fix is needed, apply it to exunit-template-tags.php too. If it is a deliberate
 * ExUnit-only change that should stay different from vk_, do not patch the comparison logic
 * ad hoc — declare it as a reviewed difference in FUNCTION_PAIRS instead.
 *
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1478
 */
class TemplateTagsParityTest extends WP_UnitTestCase {

	/**
	 * 比較対象の "veu_" 関数 => 対応する "vk_" 関数 と、個別の正規化オプションのマップ。
	 * 各エントリで指定できるキー:
	 *
	 * - 'vk_name' (必須) 対応する "vk_" 関数名。
	 * - 'intentional_divergence' (任意) 関数全体の比較を丸ごとスキップする場合の理由文字列。
	 *   **必ず空でない理由を書くこと**（無言でのスキップを防ぐため、理由が空だとテスト自体が
	 *   失敗する）。この関数は "vk_" 側とロジックレベルで大きく異なることが分かっていて、
	 *   トークン比較そのものが意味を持たない場合にのみ使う。現時点でこのフラグを使っている
	 *   関数は無い（唯一の既知差分である veu_the_post_type_check_list() の esc_attr( $key )
	 *   は、丸ごとスキップすると差分そのものの検知力を失う ["esc_attr( $key ) の欠落を
	 *   検知できない"という安藤さんの指摘] ため、代わりに下記 'vk_normalizer' +
	 *   'required_veu_token_sequence' の組み合わせで扱っている）。
	 * - 'vk_normalizer' (任意) vk_ 側のトークン列にのみ適用する正規化メソッド名（$this の
	 *   private メソッド名の文字列）。veu_ 側で意図的に追加した差分の分だけ vk_ 側を
	 *   「あるべき姿」へ寄せてから比較するために使う。片方向の正規化のため、veu_ 側から
	 *   その差分（＝追加した処理）が消えると、両者は再び食い違い MISMATCH になる。
	 * - 'required_veu_token_sequence' (任意) veu_ 側のトークン列に、この配列で指定した
	 *   トークン列が部分列として存在することを追加でアサートする。'vk_normalizer' と
	 *   組み合わせて使うことで、正規化の方向性だけに頼らず、追加した差分そのものの存在も
	 *   直接確認できる（二重の安全網）。
	 *
	 * Map of "veu_" function name => its corresponding "vk_" function name, plus per-pair
	 * options. Available keys per entry:
	 *
	 * - 'vk_name' (required) The corresponding "vk_" function name.
	 * - 'intentional_divergence' (optional) A reason string that skips comparison for the
	 *   whole function entirely. **The reason must not be empty** (the test fails if it is,
	 *   so a divergence can never be silently skipped). Use this only when the function is
	 *   known to differ substantially in logic from its "vk_" counterpart, such that a token
	 *   comparison would not be meaningful at all. No function currently uses this flag (the
	 *   one known difference, veu_the_post_type_check_list()'s esc_attr( $key ), would lose
	 *   its own regression protection if skipped wholesale — Ando's finding that "a missing
	 *   esc_attr( $key ) could not be detected" — so it is handled instead via the
	 *   'vk_normalizer' + 'required_veu_token_sequence' combination below).
	 * - 'vk_normalizer' (optional) Name of a private method (string) applied only to the vk_
	 *   token stream before comparison. Used to bring the vk_ side up to "what it should look
	 *   like" for the one reviewed addition on the veu_ side. Because the normalization is
	 *   one-directional, if the veu_ side ever loses that addition, the two streams diverge
	 *   again and the test fails with MISMATCH.
	 * - 'required_veu_token_sequence' (optional) Additionally asserts that this token
	 *   sequence exists as a contiguous subsequence in the veu_ token stream. Combined with
	 *   'vk_normalizer' so the added difference is verified directly, not only inferred from
	 *   the normalization direction (defense in depth).
	 *
	 * @var array
	 */
	const FUNCTION_PAIRS = array(
		'veu_get_page_for_posts'                           => array( 'vk_name' => 'vk_get_page_for_posts' ),
		'veu_get_post_type'                                => array( 'vk_name' => 'vk_get_post_type' ),
		'veu_get_page_description'                         => array( 'vk_name' => 'vk_get_page_description' ),
		'veu_the_post_type_check_list'                     => array(
			'vk_name'                     => 'vk_the_post_type_check_list',
			// レビュー済みの意図的な差分（安藤さんのコードレビュー指摘）。$key のみ esc_attr()
			// でラップしている。片方向正規化＋存在アサートの両方で守る（クラス docblock 参照）.
			// Deliberate, reviewed difference (Ando's code review finding). $key alone is
			// wrapped in esc_attr(). Guarded by both the one-directional normalizer and an
			// existence assertion (see the class docblock).
			'vk_normalizer'               => 'wrap_bare_key_with_esc_attr',
			'required_veu_token_sequence' => array( 'esc_attr', '(', '$key', ')' ),
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
	 * コメント・空白の違いは無視する。FUNCTION_PAIRS で宣言した差分（内部呼び出しの
	 * リネーム・個別の正規化・意図的スキップ）だけを許容する。
	 *
	 * Verifies each veu_ function's implementation has not drifted from its corresponding
	 * vk_ function at the token level. Comment and whitespace differences are ignored; only
	 * the differences declared in FUNCTION_PAIRS (internal-call renames, per-pair
	 * normalization, intentional skips) are tolerated.
	 */
	public function test_veu_functions_match_vk_source_of_truth() {
		$vk_file  = VEU_DIRECTORY_PATH . '/inc/template-tags/package/template-tags.php';
		$veu_file = VEU_DIRECTORY_PATH . '/inc/template-tags/exunit-template-tags.php';

		$this->assertFileExists( $vk_file, '比較対象の package/template-tags.php が見つかりません' );
		$this->assertFileExists( $veu_file, '比較対象の exunit-template-tags.php が見つかりません' );

		foreach ( self::FUNCTION_PAIRS as $veu_name => $spec ) {
			$vk_name = $spec['vk_name'];

			if ( array_key_exists( 'intentional_divergence', $spec ) ) {
				$reason = trim( (string) $spec['intentional_divergence'] );
				$this->assertNotSame(
					'',
					$reason,
					"{$veu_name}() の 'intentional_divergence' には空でない理由文字列が必須です" .
					'（無言でスキップされることを防ぐため）。' .
					" ({$veu_name}()'s 'intentional_divergence' requires a non-empty reason, " .
					'so a divergence can never be silently skipped.)'
				);
				// レビュー済みの乖離ありとして扱い、トークン比較そのものはスキップする.
				// Treated as a reviewed, intentional divergence; skip the strict token
				// comparison itself.
				continue;
			}

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

			$vk_normalized = $this->rename_tokens( $vk_tokens, $rename_map );

			if ( ! empty( $spec['vk_normalizer'] ) ) {
				$vk_normalized = $this->{$spec['vk_normalizer']}( $vk_normalized );
			}

			$this->assertSame(
				implode( ' ', $vk_normalized ),
				implode( ' ', $veu_tokens ),
				"veu_ 側の {$veu_name}() が、package/template-tags.php の {$vk_name}() から静かに乖離しています。" .
				'vektor-wp-libraries との同期で package/template-tags.php 側が更新された場合、' .
				'exunit-template-tags.php 側にも同じ修正を反映してください。' .
				" (veu_{$veu_name}() has silently drifted from vk_{$vk_name}() in package/template-tags.php. " .
				'If package/template-tags.php was updated by a vektor-wp-libraries sync, apply the ' .
				'same fix to exunit-template-tags.php.)'
			);

			if ( ! empty( $spec['required_veu_token_sequence'] ) ) {
				$this->assertTrue(
					$this->contains_token_sequence( $veu_tokens, $spec['required_veu_token_sequence'] ),
					"veu_{$veu_name}() から必須のトークン列「" . implode( ' ', $spec['required_veu_token_sequence'] ) . '」' .
					'が失われています（レビュー済みの追加差分のはずが消えています）。' .
					" (veu_{$veu_name}() is missing the required token sequence \"" . implode( ' ', $spec['required_veu_token_sequence'] ) . '" ' .
					'— the reviewed, intentional addition appears to have been removed.)'
				);
			}
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
		unset( $token ); // 参照ループの後始末. Clean up the by-reference loop variable.

		return $tokens;
	}

	/**
	 * トークン値の配列から `. $key .` という並び（文字列連結の中で $key が素のまま
	 * 使われている箇所）を探し、`. esc_attr( $key ) .` へ書き換える。片方向の正規化
	 * のため、veu_ 側で既に esc_attr( $key ) になっていればトークン列は一致するが、
	 * veu_ 側から esc_attr() が失われれば再び食い違い、比較は MISMATCH になる。
	 *
	 * `$key` が現れる他の全ての箇所（foreach の変数宣言・配列添字としての利用等）は、
	 * 前後のトークンが '.' と '.' の組にならないため、意図せず書き換わることはない。
	 *
	 * Find the `. $key .` sequence (bare $key used inside a string concatenation) in a
	 * token-value array and rewrite it to `. esc_attr( $key ) .`. Because the normalization
	 * is one-directional, the token streams still match when the veu_ side already has
	 * esc_attr( $key ), but diverge into a MISMATCH again if esc_attr() is ever removed from
	 * the veu_ side.
	 *
	 * Every other place `$key` appears (the foreach variable declaration, array-index usage,
	 * etc.) is never rewritten, because none of those are surrounded by a '.' before and a
	 * '.' after.
	 *
	 * @param array $tokens 対象のトークン値配列. Token values to process.
	 * @return array 書き換え後のトークン値配列. Token values after rewriting.
	 */
	private function wrap_bare_key_with_esc_attr( array $tokens ) {
		$result = array();
		$count  = count( $tokens );

		for ( $i = 0; $i < $count; $i++ ) {
			$token = $tokens[ $i ];

			if ( '$key' === $token
				&& ! empty( $result ) && '.' === end( $result )
				&& isset( $tokens[ $i + 1 ] ) && '.' === $tokens[ $i + 1 ]
			) {
				$result[] = 'esc_attr';
				$result[] = '(';
				$result[] = '$key';
				$result[] = ')';
				continue;
			}

			$result[] = $token;
		}

		return $result;
	}

	/**
	 * トークン値の配列 $haystack の中に、トークン列 $needle が部分列として
	 * 連続して現れるかどうかを判定する。
	 * Whether the token sequence $needle appears as a contiguous subsequence of $haystack.
	 *
	 * @param array $haystack 探索対象のトークン値配列. Token values to search within.
	 * @param array $needle   探す側のトークン列. Token sequence to look for.
	 * @return bool 部分列として存在すれば true. True if found as a contiguous subsequence.
	 */
	private function contains_token_sequence( array $haystack, array $needle ) {
		$haystack_count = count( $haystack );
		$needle_count   = count( $needle );

		if ( 0 === $needle_count || $needle_count > $haystack_count ) {
			return false;
		}

		for ( $i = 0; $i <= $haystack_count - $needle_count; $i++ ) {
			if ( array_slice( $haystack, $i, $needle_count ) === $needle ) {
				return true;
			}
		}

		return false;
	}
}
