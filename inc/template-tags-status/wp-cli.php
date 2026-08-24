<?php
/**
 * Registers `wp exunit template-tags status`, a WP-CLI command that reports which plugin's
 * bundled copy of each of ExUnit's shared template-tag files is currently in effect.
 *
 * `wp exunit template-tags status` WP-CLI コマンドを登録する。ExUnit の共有テンプレートタグ
 * ファイルごとに、現在どのプラグインの同梱コピーが採用されているかを報告する。
 *
 * This reports the same file-level facts as the Site Health "Info" tab section (see
 * site-health.php) -- it does not go down to individual function names, and it makes no
 * pass/fail judgement about mixed-source files; ExUnit itself does not decide whether a given
 * combination is "a problem".
 *
 * サイトヘルス「情報」タブのセクション（site-health.php）と同じファイル単位の事実を報告する
 * だけで、関数名までは掘り下げず、採用元が混在しているファイルについて良し悪しの判定も行わない。
 * ある組み合わせが「問題かどうか」は ExUnit 自身は判定しない。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/package/class-veu-cli-template-tags-command.php';

	WP_CLI::add_command( 'exunit template-tags', 'VEU_CLI_Template_Tags_Command' );
}
