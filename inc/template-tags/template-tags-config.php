<?php
/**
 * Load the shared template tag packages used by ExUnit.
 * ExUnit が使用する共有パッケージを読み込む。
 *
 * When another plugin (e.g. VK Post Author Display) bundles the same shared package
 * (package/template-tags.php) and loads it first, vk_get_post_type() becomes already defined.
 * The old file-level guard below then skipped loading ExUnit's own package/template-tags.php
 * entirely, leaving newer functions not present in the other plugin's older copy (e.g.
 * vk_the_taxonomy_check_list()) undefined and causing a fatal error. Every function inside
 * package/template-tags.php is individually guarded with function_exists(), so requiring it
 * unconditionally cannot trigger a redeclaration error. The file-level guard is therefore
 * removed and the file is always required.
 * 他プラグイン（VK Post Author Display 等）が同梱する同名の共有パッケージ（package/template-tags.php）が
 * 先に読み込まれ、vk_get_post_type() が定義済みになっていると、このファイル単位のガードにより
 * ExUnit 側の package/template-tags.php が丸ごと読み込まれず、他プラグインの古いコピーに存在しない
 * 新しい関数（例: vk_the_taxonomy_check_list()）が未定義のまま致命的エラーになる不具合があった。
 * package/template-tags.php 内の全関数は個別に function_exists() ガードされているため、常に
 * require_once しても関数の再宣言エラーは起きない。よってファイル単位のガードは外し、常に読み込む。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1450
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/package/template-tags.php';
require_once __DIR__ . '/package/template-tags-veu.php';
require_once __DIR__ . '/package/template-tags-veu-old.php';
