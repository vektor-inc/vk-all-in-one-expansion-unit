<?php
/**
 * Load the "which plugin's copy of ExUnit's shared template-tag files is in effect" feature:
 * the fact-collection layer, its Site Health "Info" tab section, and its WP-CLI command.
 *
 * ExUnit の共有テンプレートタグファイルについて「どのプラグインのコピーが採用されているか」を
 * 可視化する機能一式（事実収集層、サイトヘルス「情報」タブのセクション、WP-CLI コマンド）を
 * 読み込む。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/collector.php';
require_once __DIR__ . '/site-health.php';
require_once __DIR__ . '/wp-cli.php';
