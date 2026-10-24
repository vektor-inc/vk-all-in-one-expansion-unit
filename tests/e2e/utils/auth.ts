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

// 45 秒のテスト上限に後処理の余裕を 7 秒残し、全段階で一つの締め切りを共有する。
const LOGIN_TOTAL_TIMEOUT_MS = 38000;
const LOGIN_GOTO_TIMEOUT_MS = 10000;
const LOGIN_RESPONSE_TIMEOUT_MS = 12000;
const LOGIN_ERROR_TIMEOUT_MS = 5000;

/**
 * ログイン全体の締め切りまでに使える待ち時間を返す。
 *
 * @param deadline ログイン処理の締め切り時刻.
 * @param upperLimit 段階ごとの待ち時間の上限.
 * @return この段階で利用できる待ち時間.
 */
const remainingTimeout = ( deadline: number, upperLimit = Infinity ): number =>
	Math.max( 1, Math.min( upperLimit, deadline - Date.now() ) );

/**
 * ログイン画面のエラー文を、全体の締め切りの範囲内で取得する。
 *
 * @param page Playwright の Page.
 * @param deadline ログイン処理の締め切り時刻.
 * @return 表示されたエラー文。取得できない場合は空文字列.
 */
const getLoginErrorText = async (
	page: Page,
	deadline: number
): Promise<string> =>
	page
		.locator( '#login_error' )
		.innerText( {
			timeout: remainingTimeout( deadline, LOGIN_ERROR_TIMEOUT_MS ),
		} )
		.catch( () => '' );

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
	const deadline = Date.now() + LOGIN_TOTAL_TIMEOUT_MS;
	let lastReason = '';
	let loginSucceeded = false;

	for ( let attempt = 1; attempt <= maxAttempts; attempt++ ) {
		try {
			// フォームは HTML だけで完結するため load ( 画像・外部リソース ) までは待たない。
			await page.goto( '/wp-login.php', {
				waitUntil: 'domcontentloaded',
				timeout: remainingTimeout( deadline, LOGIN_GOTO_TIMEOUT_MS ),
			} );

			// 既にログイン済みなら、再送信せず管理画面の描画待ちへ進む。
			if ( /\/wp-admin\//.test( new URL( page.url() ).pathname ) ) {
				loginSucceeded = true;
				break;
			}

			await page.locator( '#user_login' ).fill( ADMIN_USER, {
				timeout: remainingTimeout( deadline ),
			} );
			await page.locator( '#user_pass' ).fill( ADMIN_PASS, {
				timeout: remainingTimeout( deadline ),
			} );

			// ログイン POST の応答 ( 成功なら 302 ) を捕まえてから送信する。
			const responseTimeout = remainingTimeout(
				deadline,
				LOGIN_RESPONSE_TIMEOUT_MS
			);
			const loginResponse = page.waitForResponse(
				( response ) =>
					response.request().method() === 'POST' &&
					new URL( response.url() ).pathname.endsWith(
						'/wp-login.php'
					),
				{ timeout: responseTimeout }
			);
			const [ , response ] = await Promise.all( [
				page
					.locator( '#wp-submit' )
					.click( { timeout: responseTimeout } ),
				loginResponse,
			] );

			// 認証失敗は 200 でログイン画面を返すため、管理画面への遷移を待たない。
			if ( response.status() < 300 || response.status() >= 400 ) {
				const errorText = await getLoginErrorText( page, deadline );
				lastReason = `HTTP ${ response.status() } ( url: ${ page.url() }${
					errorText ? `, #login_error: ${ errorText.trim() }` : ''
				} )`;
				continue;
			}

			// POST が通ったら、URL が管理画面へコミットされるまで待つ ( 描画完了は待たない )。
			await page.waitForURL(
				( url ) => /\/wp-admin\//.test( url.pathname ),
				{
					waitUntil: 'commit',
					timeout: remainingTimeout( deadline ),
				}
			);
			loginSucceeded = true;
			break;
		} catch ( error ) {
			const errorText = await getLoginErrorText( page, deadline );
			lastReason = `${
				error instanceof Error
					? error.message.split( '\n' )[ 0 ]
					: String( error )
			} ( url: ${ page.url() }${
				errorText ? `, #login_error: ${ errorText.trim() }` : ''
			} )`;
			continue;
		}
	}

	if ( ! loginSucceeded ) {
		throw new Error(
			`login(): ${ maxAttempts } 回試してもログインできませんでした: ${ lastReason }`
		);
	}

	// 送信のやり直しでは描画は速くならないため、残り時間を全て管理バーの出現に使う。
	try {
		await page
			.locator( '#wpadminbar' )
			.waitFor( { timeout: remainingTimeout( deadline ) } );
	} catch ( error ) {
		const reason =
			error instanceof Error
				? error.message.split( '\n' )[ 0 ]
				: String( error );
		throw new Error(
			`login(): ログイン後に管理バーが表示されませんでした: ${ reason } ( url: ${ page.url() } )`
		);
	}
};
