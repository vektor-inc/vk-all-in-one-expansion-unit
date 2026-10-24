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
 * 【待ち方の方針 / #1536】
 * - 「ログインできた」の判定は、ログイン POST が管理画面へのリダイレクトを返し、
 *   ブラウザの URL が /wp-admin/ へコミットされたこと ( waitUntil: 'commit' ) とする。
 *   管理画面の HTML 全体の受信 ( DOMContentLoaded / load ) まで URL の判定に含めると、
 *   高負荷時に管理画面の描画が遅れただけで「ログインに失敗した」ように見える
 *   ( 一式実行で beforeEach が時間切れになった原因 )。描画の完了は管理バーの出現で別に待つ。
 * - ログイン POST が管理画面へ進まなかった場合 ( ログイン画面に留まる ) は、原因を
 *   切り分けられるよう画面上のエラー文 ( #login_error ) を添えて失敗させる。
 *   高負荷でフォーム送信が空振りした場合に備え、ログインフォームの入力から 1 回だけやり直す。
 * - 既にログイン済みの場合 ( wp-login.php が管理画面へリダイレクトする ) は、入力せず
 *   管理バーの出現だけ確認する。
 *
 * @param page Playwright の Page.
 */
export const login = async ( page: Page ): Promise<void> => {
	const maxAttempts = 2;
	let lastReason = '';

	for ( let attempt = 1; attempt <= maxAttempts; attempt++ ) {
		// フォームは HTML だけで完結するため load ( 画像・外部リソース ) までは待たない。
		await page.goto( '/wp-login.php', { waitUntil: 'domcontentloaded' } );

		// 既にログイン済みなら wp-login.php は管理画面へリダイレクトされる。
		if ( /\/wp-admin\//.test( new URL( page.url() ).pathname ) ) {
			await page.locator( '#wpadminbar' ).waitFor( { timeout: 60000 } );
			return;
		}

		await page.locator( '#user_login' ).fill( ADMIN_USER );
		await page.locator( '#user_pass' ).fill( ADMIN_PASS );

		// ログイン POST の応答 ( 成功なら 302 ) を捕まえてから送信する。
		const loginResponse = page.waitForResponse(
			( response ) =>
				response.request().method() === 'POST' &&
				new URL( response.url() ).pathname.endsWith( '/wp-login.php' ),
			{ timeout: 30000 }
		);
		await page.locator( '#wp-submit' ).click();

		try {
			await loginResponse;
			// POST が通ったら、URL が管理画面へコミットされるまで待つ ( 描画完了は待たない )。
			await page.waitForURL(
				( url ) => /\/wp-admin\//.test( url.pathname ),
				{ waitUntil: 'commit', timeout: 30000 }
			);
		} catch ( error ) {
			const errorText = await page
				.locator( '#login_error' )
				.innerText( { timeout: 1000 } )
				.catch( () => '' );
			lastReason = `${
				error instanceof Error ? error.message.split( '\n' )[ 0 ] : String( error )
			} ( url: ${ page.url() }${
				errorText ? `, #login_error: ${ errorText.trim() }` : ''
			} )`;
			continue;
		}

		// 管理バー ( id ベース・i18n 非依存 ) の描画で「ログイン済みの管理画面が出た」ことを確認する。
		await page.locator( '#wpadminbar' ).waitFor( { timeout: 60000 } );
		return;
	}

	throw new Error(
		`login(): ${ maxAttempts } 回試してもログインできませんでした: ${ lastReason }`
	);
};
