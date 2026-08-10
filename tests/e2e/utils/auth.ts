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
	await page.waitForURL( /wp-admin\// );
};
