/**
 * e2e テスト共通のログイン処理・認証情報。
 *
 * wp-env が作成する既定の管理者アカウント ( admin / password ) でログインする。
 * 複数の spec ( cta.spec.ts / cta-image-position-xss.spec.ts 等 ) で重複していた
 * ログイン処理をここへ集約する。
 */
import { type Page } from '@playwright/test';

// wp-env の既定の管理者アカウント。
export const ADMIN_USER = 'admin';
export const ADMIN_PASS = 'password';

/**
 * 管理者としてログインする。
 *
 * ラベル文言 ( "Username or Email Address" 等 ) は WordPress のロケール・バージョンで
 * 変わるため使わない。`#user_login` / `#user_pass` / `#wp-submit` は WP core のログイン
 * フォームで安定して付与される id なので、これらのみでロケータを組む。
 *
 * @param page Playwright の Page.
 */
export const login = async ( page: Page ): Promise<void> => {
	await page.goto( '/wp-login.php' );
	await page.locator( '#user_login' ).fill( ADMIN_USER );
	await page.locator( '#user_pass' ).fill( ADMIN_PASS );
	await page.locator( '#wp-submit' ).click();
	// waitForURL の既定は "load" イベントまで待つ。管理画面は外部リソースや重い
	// 初期処理で load が遅れることがあり（一式実行で 45〜60 秒のテスト時間切れに
	// なった原因 / #1536）、ログイン完了の判定としては過剰。URL が /wp-admin/ に
	// 遷移した時点（DOMContentLoaded）で十分なので、その後は管理バー
	// （id ベース・i18n 非依存）の描画で「ログイン済みの管理画面が出た」ことを確認する。
	await page.waitForURL( /wp-admin\//, { waitUntil: 'domcontentloaded' } );
	await page.locator( '#wpadminbar' ).waitFor( { timeout: 30000 } );
};
