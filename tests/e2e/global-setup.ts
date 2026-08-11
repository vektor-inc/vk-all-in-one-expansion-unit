/**
 * Playwright globalSetup: tests サイトの前提条件を担保する。
 *
 * wp-env の tests サイトは、環境によってテーマ・本プラグインがいずれも未有効化のまま
 * 起動することがあり、その場合フロントエンドが Content-Length: 0（真っ白）を返し、
 * 以降の e2e テストが軒並み意味を失う。全 spec の実行前に一度だけ、
 * 本プラグインと既定テーマが有効であることを保証する。
 *
 * 冪等な操作のみ行うため、既に満たされている環境では何もしない。
 */
import { basename } from 'path';
import { realpathSync } from 'fs';
import { runWpCli } from './utils/wp-cli';

// フォールバック用の既定テーマ。tests サイトに有効テーマが一つも無い場合に使う。
const FALLBACK_THEME = 'twentytwentyfive';

/**
 * Playwright の globalSetup エントリポイント。
 */
export default async function globalSetup(): Promise<void> {
	// worktree ではマウント名が worktree ディレクトリ名になる ( 例: agent-xxxxxxxx ) ため、
	// 固定のプラグインスラッグではなく、実際にマウントされているディレクトリ名から動的に求める。
	const pluginSlug = basename( realpathSync( process.cwd() ) );

	// 本プラグインを有効化する。既に有効な場合は "Plugin already active" の警告と共に
	// 正常終了する（冪等）ため、事前の状態確認は不要。
	runWpCli( [ 'plugin', 'activate', pluginSlug ] );

	// 有効なテーマが無ければ既定テーマを有効化する。既に何かテーマが有効なら変更しない
	// （他 spec が前提にしているテーマ依存の見た目を崩さないため）。
	const activeThemes = runWpCli( [
		'theme',
		'list',
		'--status=active',
		'--field=name',
	] ).trim();

	if ( ! activeThemes ) {
		runWpCli( [ 'theme', 'activate', FALLBACK_THEME ] );
	}
}
