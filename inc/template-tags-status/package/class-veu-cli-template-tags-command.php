<?php
/**
 * The `wp exunit template-tags status` WP-CLI command class.
 *
 * `wp exunit template-tags status` WP-CLI コマンドのクラス。
 *
 * Only ever loaded from inc/template-tags-status/wp-cli.php, which already guards on
 * `defined( 'WP_CLI' ) && WP_CLI` before requiring this file.
 *
 * inc/template-tags-status/wp-cli.php からのみ読み込まれる。同ファイル側で
 * `defined( 'WP_CLI' ) && WP_CLI` を確認した上でこのファイルを require しているため、
 * ここでは改めてガードしない。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reports which plugin's bundled copy of ExUnit's shared template-tag files is currently in
 * effect.
 *
 * ExUnit の共有テンプレートタグファイルについて、現在どのプラグインの同梱コピーが
 * 採用されているかを報告する。
 */
class VEU_CLI_Template_Tags_Command {

	/**
	 * Show, per shared template-tag file, which plugin's bundled copy is currently in effect.
	 *
	 * 共有テンプレートタグファイルごとに、現在どのプラグインの同梱コピーが採用されているか
	 * を表示する。
	 *
	 * When a single file's functions come from more than one plugin's copy, that file is
	 * expanded into one row per source (the `file` column repeats) so the output stays a flat
	 * table that's easy to consume from a script, rather than a single cell with multiple
	 * values joined together. Columns, in order: `file`, `product`, `version`, `plugin`, `path`.
	 * `path` (the defining file's path relative to the WordPress root) is only ever filled in
	 * when the plugin could not be identified but its defining file could -- it is a separate
	 * column rather than folded into `product` so scripts never need to parse it back out of a
	 * sentence.
	 *
	 * 1つのファイルの関数が複数のプラグインのコピーに分かれて由来している場合、そのファイルは
	 * 採用元ごとに1行へ展開される（`file` 列は同じ値を繰り返す）。スクリプトから扱いやすい
	 * フラットな表のままにするため、1セルに複数の値を結合した形にはしない。列の並びは
	 * `file`, `product`, `version`, `plugin`, `path` の順。`path`（定義元ファイルの WordPress
	 * ルートからの相対パス）は、プラグインは特定できなかったが定義元ファイルは分かった場合にのみ
	 * 入る。`product` に埋め込まず独立した列にしているのは、スクリプト側が文中からパスを
	 * 取り出し直す必要をなくすため。
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp exunit template-tags status
	 *     wp exunit template-tags status --format=json
	 *
	 * @param array $args       Positional arguments (unused).
	 * @param array $assoc_args Associative arguments; recognizes --format.
	 * @return void
	 */
	public function status( $args, $assoc_args ) {
		$rows = veu_get_shared_template_tags_status_rows();

		$format_args = wp_parse_args( $assoc_args, array( 'format' => 'table' ) );

		WP_CLI\Utils\format_items( $format_args['format'], $rows, array( 'file', 'product', 'version', 'plugin', 'path' ) );
	}
}
