/**
 * Page Top Button - 画像アップロード機能の e2e テスト
 *
 * issue #1342 / PR #1345 で追加された
 * 「ページトップへ戻るボタンに画像を指定できる機能」を検証する。
 *
 * - 画像未設定時のデフォルト出力に変化がないこと
 * - URL テキストフィールドへ直接 URL を貼り付けた時に、プレビュー画像が同期されること
 *   （CodeRabbit 指摘3 の修正点）
 * - 「画像の指定を解除」ボタンで URL が空になり、保存後フロントから style 属性が消えること
 * - 画像設定時にフロントで `<a class="page_top_btn has-image" style="--veu_page_top_button_url:url(...);">`
 *   が出力されること
 * - XSS / CSS injection ペイロードがサニタイザーで空文字に正規化され、
 *   フロントの `<a>` に style 属性が付かないこと
 *
 * テスト前後で wp-cli を使い `vkExUnit_pagetop` オプションを直接初期化する。
 */
import { test, expect, type Page } from '@playwright/test';
import { execFileSync } from 'child_process';

// このファイル内の 2 つの describe ブロックは同じ `vkExUnit_pagetop`
// オプションを書き換えるため、ファイル単位でシリアル実行する。
// （playwright.config.js は fullyParallel: true なので、describe 単位の
// `mode: 'serial'` 指定だけでは別 describe 間の並列を抑制できない）
test.describe.configure( { mode: 'serial' } );

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'password';

// wp-env の `tests-cli` コンテナ経由で wp-cli を実行するためのヘルパー。
// `tests-cli` を使うのは、Playwright のテスト対象 ( WP_BASE_URL ) が
// wp-env の **tests** サイト（デフォルト 8889 / override 後 9109）を
// 向いているため。`cli` ( development サイト ) で option を書き換えても
// テスト側 DB には反映されないので、必ず `tests-cli` を使うこと。
// shell 経由ではなく execFileSync で引数を配列のまま渡すことで、
// JSON 等にクォートや空白が含まれてもシェル解釈を経由しない。
const runWpCli = ( args: string[] ): string => {
	return execFileSync(
		'npx',
		[ 'wp-env', 'run', 'tests-cli', 'wp', ...args ],
		{
			encoding: 'utf-8',
			stdio: [ 'ignore', 'pipe', 'pipe' ],
		}
	);
};

// vkExUnit_pagetop オプションを削除して既知の初期状態に戻す。
// wp-cli が「option がそもそも存在しない」と返すケースのみ握りつぶし、
// それ以外の予期しないエラー（wp-env が動いていない等）は throw する。
const resetPagetopOption = (): void => {
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_pagetop' ] );
	} catch ( e ) {
		// wp-cli は存在しない option を delete しようとすると
		// "Could not delete 'vkExUnit_pagetop' option. Does it exist?"
		// 形式のメッセージで終了コード非0を返す。これだけは想定内なので
		// 握りつぶし、それ以外（wp-env が動いていない等）は再 throw する。
		// 「`vkExUnit_pagetop` が未存在」を示す文言に限定し、
		// option delete 以外の障害メッセージを誤って握りつぶさないようにする。
		const stderr =
			e && typeof e === 'object' && 'stderr' in e
				? String( ( e as { stderr?: unknown } ).stderr ?? '' )
				: '';
		const message = e instanceof Error ? e.message : String( e );
		const haystack = `${ stderr }\n${ message }`;
		const isMissingPagetopOption =
			/Could not delete\s+'vkExUnit_pagetop'\s+option\.\s*Does it exist\?/i.test(
				haystack
			) ||
			( /vkExUnit_pagetop/.test( haystack ) &&
				/does(?:\s+not)?\s+exist/i.test( haystack ) );
		if ( isMissingPagetopOption ) {
			return;
		}
		throw e;
	}
};

// body に `.scrolled` を付与させるため、実際にスクロールイベントを発火させる。
// issue #1381 以降、ページトップボタンは未スクロール時 `visibility:hidden` +
// `pointer-events:none` でタブ順・表示から除外されるため、`toBeVisible()` で
// 検証する前に一度スクロールして `body.scrolled` を付ける必要がある。
// JS（pagetop-btn.js）は window の scroll イベントで pageYOffset > 0 を見て
// body.scrolled を付け外しする。ページ高が足りないと scrollTo しても
// pageYOffset が 0 のままになるため、十分な高さを保証してからスクロールする。
const scrollDown = async ( page: Page ): Promise< void > => {
	await page.evaluate( () => {
		// スクロールできるよう、最低でもビューポート 3 画面分の高さを確保する。
		document.body.style.minHeight = '3000px';
		window.scrollTo( 0, 1000 );
		// scroll イベントを明示発火（scrollTo だけでは発火しない環境対策）。
		window.dispatchEvent( new Event( 'scroll' ) );
	} );
	// body に .scrolled が付くまで待つ。
	await expect( page.locator( 'body.scrolled' ) ).toHaveCount( 1 );
};

test.describe( 'Page Top Button image upload (#1342)', () => {
	// 全テストで同じ vkExUnit_pagetop オプションを書き換えるため、
	// テスト間の競合を避けてシリアル実行する。
	test.describe.configure( { mode: 'serial' } );

	// 各テスト前に option をリセット。
	test.beforeEach( async ( { page } ) => {
		resetPagetopOption();

		// 管理画面にログインしておく。
		// wp-admin の言語設定（日本語等）でラベルテキストが変わってもテストが
		// 落ちないよう、Core が安定的に提供している input id をセレクタに使う。
		await page.goto( '/wp-login.php' );
		await page.locator( '#user_login' ).fill( ADMIN_USER );
		await page.locator( '#user_pass' ).fill( ADMIN_PASS );
		await page.locator( '#wp-submit' ).click();
		// ダッシュボードのロード完了まで待つ。
		// networkidle は WP の heartbeat 等で安定しないため、
		// 管理バー（id ベース・i18n 非依存）が描画されたことで判断する。
		await page.waitForURL( /wp-admin\// );
		await page.locator( '#wpadminbar' ).waitFor();
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	test( 'デフォルト（画像未設定）ではフロントに has-image クラスも style 属性も出力されない', async ( {
		page,
	} ) => {
		// `/?p=1` 固定はテスト DB の状態に依存して落ちるため、トップページ
		// で page_top_btn を検証する（wp_footer フックで全フロントページに
		// 共通で出力されるため）。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		// issue #1381: 未スクロール時は visibility:hidden のため、
		// 表示を検証する前にスクロールして body.scrolled を付ける。
		await scrollDown( page );
		await expect( btn ).toBeVisible();
		// has-image クラスが付いていないこと。
		await expect( btn ).not.toHaveClass( /has-image/ );
		// style 属性が無いこと（あっても空であること）を確認。
		const style = await btn.getAttribute( 'style' );
		expect( style === null || style === '' ).toBeTruthy();
	} );

	test( '管理画面で画像 URL を保存 → フロントに has-image と style 属性が付与される', async ( {
		page,
	} ) => {
		// メイン設定ページを開く。本プラグインのスラッグは `vkExUnit_main_setting`。
		// 複数セクションが並ぶ縦長ページなので、対象セクションを # で指定する。
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		// ページトップセクションが描画され、操作対象の入力欄が DOM に存在することで
		// 画面ロード完了を判定する（networkidle は管理画面の常時通信で不安定）。
		await page.locator( '#pagetop_image_url' ).waitFor();

		// 画像 URL を直接テキストフィールドに入力する
		// （メディアライブラリ操作は wp-env の挙動が不安定なため URL 直入力で検証）。
		// worktree 名や localhost ポートに依存しないよう、固定 URL を使う。
		const sampleUrl = 'https://example.com/icon.svg';
		const urlInput = page.locator( '#pagetop_image_url' );
		// セクションが視界外でも fill 可能。scrollIntoViewIfNeeded で
		// admin の sticky bar 等の影響を受けないようにスクロールする。
		await urlInput.scrollIntoViewIfNeeded();
		await urlInput.fill( sampleUrl );
		// change イベントを発火させてプレビュー同期を起こす。
		await urlInput.blur();

		// プレビュー画像の表示確認（CodeRabbit 指摘3 の修正点）。
		const thumb = page.locator( '#thumb_pagetop_image_url' );
		await expect( thumb ).toHaveAttribute( 'src', sampleUrl );
		const previewWrapper = page.locator( '.veu_pagetop_image_preview' );
		// 親要素の display が none でないこと。
		await expect( previewWrapper ).toBeVisible();

		// 設定を保存。
		// `#submit` は各セクションごとに重複しているので
		// `#pagetopSetting` セクション内のものを明示的に指定する。
		await page.locator( '#pagetopSetting #submit' ).click();
		// VK ExUnit のメイン設定ページは保存後に `.notice-success` を
		// 出さず、同じページに POST して再描画されるだけ。そのため
		// 「フォームが再描画され、URL 入力欄に保存した値が反映されている」
		// ことを toHaveValue() の retry で待つことで保存完了とみなす。
		// networkidle は WP の heartbeat 等で安定しないため使わない。
		await expect( page.locator( '#pagetop_image_url' ) ).toHaveValue(
			sampleUrl
		);

		// フロントで出力を確認（トップページで page_top_btn を検証）。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		// issue #1381: 未スクロール時は visibility:hidden のため、
		// 表示を検証する前にスクロールして body.scrolled を付ける。
		await scrollDown( page );
		await expect( btn ).toBeVisible();
		await expect( btn ).toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		expect( style ).not.toBeNull();
		// `--veu_page_top_button_url:url("...")` を style 属性が含むこと。
		// 出力時にエスケープされて HTML 上は &quot; になるが、 getAttribute は
		// デコード後の `"` を返す。
		expect( style ).toContain( '--veu_page_top_button_url:url("' );
		expect( style ).toContain( sampleUrl );
	} );

	test( '「画像の指定を解除」ボタンで URL 入力欄が空になり、保存後フロントから style 属性が消える', async ( {
		page,
	} ) => {
		// 事前に option へ画像 URL を直接保存しておく。
		// worktree 名や localhost ポートに依存しないよう、固定 URL を使う。
		const sampleUrl = 'https://example.com/icon.svg';
		// PHP serialize で保存するため wp option update --format=json を使う。
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
		} );
		// execFileSync + 引数配列なので JSON にクォートや空白が含まれていても
		// シェル解釈を経由せず安全にそのまま wp-cli へ渡る。
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		// 設定画面で値が反映されていることを確認。
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		// 画像 URL 入力欄の描画を待ってから値を検証する。
		await page.locator( '#pagetop_image_url' ).waitFor();
		const urlInput = page.locator( '#pagetop_image_url' );
		await urlInput.scrollIntoViewIfNeeded();
		await expect( urlInput ).toHaveValue( sampleUrl );

		// 「画像の指定を解除」ボタンをクリック。
		await page.locator( '#veu_pagetop_image_clear' ).click();

		// URL が空になり、プレビューが非表示になること。
		await expect( urlInput ).toHaveValue( '' );
		const previewWrapper = page.locator( '.veu_pagetop_image_preview' );
		await expect( previewWrapper ).toBeHidden();

		// 保存（pagetop セクション内の submit ボタン）。
		await page.locator( '#pagetopSetting #submit' ).click();
		// 保存完了は「URL 入力欄が空のまま再描画されていること」で判定する。
		// `.notice-success` は VK ExUnit のメイン設定ページでは表示されない。
		await expect( page.locator( '#pagetop_image_url' ) ).toHaveValue( '' );

		// フロントで style 属性が消えていることを確認。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		// issue #1381: 未スクロール時は visibility:hidden のため、
		// 表示を検証する前にスクロールして body.scrolled を付ける。
		await scrollDown( page );
		await expect( btn ).toBeVisible();
		await expect( btn ).not.toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		expect( style === null || style === '' ).toBeTruthy();
	} );

	test( 'XSS / CSS injection ペイロードを保存しても空文字に正規化される', async ( {
		page,
	} ) => {
		// 設定画面を開く。
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		// 画像 URL 入力欄の描画を待ってからペイロード入力に進む。
		await page.locator( '#pagetop_image_url' ).waitFor();

		// CSS injection を試す payload（クォート・括弧・空白を含む）。
		const payload =
			'https://example.com/a.png");}body{background:red;//';
		const urlInput = page.locator( '#pagetop_image_url' );
		await urlInput.scrollIntoViewIfNeeded();
		await urlInput.fill( payload );
		await urlInput.blur();

		// 保存（pagetop セクション内の submit ボタン）。
		await page.locator( '#pagetopSetting #submit' ).click();
		// 保存完了 + サニタイズ結果（空文字）が UI に反映されたことを
		// toHaveValue() の retry で待つ。サニタイザーが空文字を返す想定なので、
		// この assertion 自体が「保存後にフォームが再描画され、値が反映された」
		// 状態の判定を兼ねている。
		await expect( page.locator( '#pagetop_image_url' ) ).toHaveValue( '' );

		// フロント側にも style 属性が付かないこと。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		const style = await btn.getAttribute( 'style' );
		expect( style === null || style === '' ).toBeTruthy();
		await expect( btn ).not.toHaveClass( /has-image/ );
	} );
} );

/**
 * Page Top Button - 画像サイズ（幅・高さ）機能の e2e テスト
 *
 * issue #1361 / PR #1363 で追加された
 * 「画像アップロード時に表示サイズを px で指定できる機能」を検証する。
 *
 * - 画像未設定時はサイズ入力欄が非表示
 * - 画像 URL を入れるとサイズ入力欄が表示される（MutationObserver による同期）
 * - 幅・高さを保存するとフロントの `<a class="page_top_btn has-image">` の
 *   style に `--veu_page_top_button_width:Npx;` / `--veu_page_top_button_height:Npx;`
 *   が追加される
 * - 999 を入れて保存すると 500 にクランプされる（上限ガード）
 * - 0 / 空欄の場合はカスタムプロパティが出力されず、SCSS fallback の
 *   40 × 38 px が効く
 * - 「画像の指定を解除」を押すと URL と一緒にサイズ入力欄も非表示になる
 *
 * テスト前後で wp-cli を使い `vkExUnit_pagetop` オプションを直接初期化する。
 */
test.describe( 'Page Top Button image size (#1361)', () => {
	// 全テストで同じ vkExUnit_pagetop オプションを書き換えるため、
	// テスト間の競合を避けてシリアル実行する。
	test.describe.configure( { mode: 'serial' } );

	// 各テスト前に option をリセットし、admin にログインしておく。
	test.beforeEach( async ( { page } ) => {
		resetPagetopOption();

		await page.goto( '/wp-login.php' );
		await page.locator( '#user_login' ).fill( ADMIN_USER );
		await page.locator( '#user_pass' ).fill( ADMIN_PASS );
		await page.locator( '#wp-submit' ).click();
		await page.waitForURL( /wp-admin\// );
		await page.locator( '#wpadminbar' ).waitFor();
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	test( '画像未設定の状態ではサイズ入力欄（.veu_pagetop_image_size）が非表示', async ( {
		page,
	} ) => {
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		// セクション描画を待つ。
		await page.locator( '#pagetop_image_url' ).waitFor();

		// サイズ入力欄ブロックは display:none（hidden 扱い）であること。
		const sizeBlock = page.locator( '.veu_pagetop_image_size' );
		await expect( sizeBlock ).toBeHidden();
	} );

	test( '画像 URL を直接入力するとサイズ入力欄が表示される', async ( {
		page,
	} ) => {
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		await page.locator( '#pagetop_image_url' ).waitFor();

		// 初期状態では非表示。
		const sizeBlock = page.locator( '.veu_pagetop_image_size' );
		await expect( sizeBlock ).toBeHidden();

		// 画像 URL を直接入力 → プレビュー画像 src が更新されると
		// MutationObserver が走り、size ブロックも表示されるはず。
		const sampleUrl = 'https://example.com/icon.svg';
		const urlInput = page.locator( '#pagetop_image_url' );
		await urlInput.scrollIntoViewIfNeeded();
		await urlInput.fill( sampleUrl );
		await urlInput.blur();

		// プレビューが表示されること（既存挙動）。
		await expect( page.locator( '.veu_pagetop_image_preview' ) ).toBeVisible();
		// サイズ入力欄ブロックも同期して表示されること（PR #1363 の新挙動）。
		await expect( sizeBlock ).toBeVisible();
		await expect( page.locator( '#pagetop_image_width' ) ).toBeVisible();
		await expect( page.locator( '#pagetop_image_height' ) ).toBeVisible();
	} );

	test( '幅・高さを指定して保存するとフロントの style に CSS カスタムプロパティが追加される', async ( {
		page,
	} ) => {
		// 画像 URL を option に事前投入しておき、メイン設定で size のみ入力する。
		const sampleUrl = 'https://example.com/icon.svg';
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		// メイン設定を開く。
		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		await page.locator( '#pagetop_image_width' ).waitFor();

		// サイズ入力欄ブロックが（option 復元で）表示されていること。
		await expect( page.locator( '.veu_pagetop_image_size' ) ).toBeVisible();

		// 幅 60、高さ 60 を入力。
		await page.locator( '#pagetop_image_width' ).fill( '60' );
		await page.locator( '#pagetop_image_height' ).fill( '60' );

		// 保存。
		await page.locator( '#pagetopSetting #submit' ).click();

		// 保存後に値が反映されていることを retry で待つ。
		await expect( page.locator( '#pagetop_image_width' ) ).toHaveValue( '60' );
		await expect( page.locator( '#pagetop_image_height' ) ).toHaveValue( '60' );

		// フロントで確認。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		// issue #1381: 未スクロール時は visibility:hidden のため、
		// 表示を検証する前にスクロールして body.scrolled を付ける。
		await scrollDown( page );
		await expect( btn ).toBeVisible();
		await expect( btn ).toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		expect( style ).not.toBeNull();
		// 画像 URL カスタムプロパティ + width/height カスタムプロパティが出る。
		expect( style ).toContain( '--veu_page_top_button_url:url("' );
		expect( style ).toContain( '--veu_page_top_button_width:60px' );
		expect( style ).toContain( '--veu_page_top_button_height:60px' );

		// 実際の computed style でも 60×60 になっていること。
		// CSS カスタムプロパティが SCSS の var() から拾われていることを確認。
		const box = await btn.boundingBox();
		expect( box ).not.toBeNull();
		if ( box ) {
			expect( box.width ).toBeCloseTo( 60, 0 );
			expect( box.height ).toBeCloseTo( 60, 0 );
		}
	} );

	test( '上限超過（999）を入力して保存すると 500 にクランプされる', async ( {
		page,
	} ) => {
		// 画像 URL を事前投入。
		const sampleUrl = 'https://example.com/icon.svg';
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		await page.locator( '#pagetop_image_width' ).waitFor();

		// HTML5 input の max=500 を回避して 999 を入力するため
		// `evaluate` で値を直接書き換える（サーバー側クランプの検証が目的）。
		// さらに、max 属性自体を外しておかないとブラウザの constraint
		// validation で form submit がブロックされ POST が走らない。
		// 同様に form の novalidate も立てて二重ガード。
		await page
			.locator( '#pagetop_image_width' )
			.evaluate( ( el ) => {
				const input = el as HTMLInputElement;
				input.removeAttribute( 'max' );
				input.value = '999';
			} );
		await page
			.locator( '#pagetop_image_height' )
			.evaluate( ( el ) => {
				const input = el as HTMLInputElement;
				input.removeAttribute( 'max' );
				input.value = '999';
			} );
		// pagetop セクションの form は #pagetopSetting の親にある。
		await page
			.locator( '#pagetopSetting' )
			.evaluate( ( section ) => {
				const form = ( section as HTMLElement ).closest( 'form' );
				if ( form ) {
					form.setAttribute( 'novalidate', 'novalidate' );
				}
			} );

		await page.locator( '#pagetopSetting #submit' ).click();

		// サーバー側でクランプされて 500 に保存される。
		await expect( page.locator( '#pagetop_image_width' ) ).toHaveValue( '500' );
		await expect( page.locator( '#pagetop_image_height' ) ).toHaveValue( '500' );

		// フロントの style 属性も 500px になっていること。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		const style = await btn.getAttribute( 'style' );
		expect( style ).toContain( '--veu_page_top_button_width:500px' );
		expect( style ).toContain( '--veu_page_top_button_height:500px' );
	} );

	test( '0 を入力して保存するとサイズ用カスタムプロパティが出力されずデフォルト 40×38 px に戻る', async ( {
		page,
	} ) => {
		// 事前に画像 + サイズ 60×60 を保存しておく。
		const sampleUrl = 'https://example.com/icon.svg';
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
			image_width: 60,
			image_height: 60,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		await page.locator( '#pagetop_image_width' ).waitFor();

		// 初期値 60 が反映されていることを確認してから上書き。
		await expect( page.locator( '#pagetop_image_width' ) ).toHaveValue( '60' );
		await expect( page.locator( '#pagetop_image_height' ) ).toHaveValue( '60' );

		// 0 を入れて保存（HTML5 input の min=1 を回避するため evaluate で直接書き換え）。
		// min 属性自体も外しておかないとブラウザ validation で submit がブロックされる。
		await page
			.locator( '#pagetop_image_width' )
			.evaluate( ( el ) => {
				const input = el as HTMLInputElement;
				input.removeAttribute( 'min' );
				input.value = '0';
			} );
		await page
			.locator( '#pagetop_image_height' )
			.evaluate( ( el ) => {
				const input = el as HTMLInputElement;
				input.removeAttribute( 'min' );
				input.value = '0';
			} );
		await page
			.locator( '#pagetopSetting' )
			.evaluate( ( section ) => {
				const form = ( section as HTMLElement ).closest( 'form' );
				if ( form ) {
					form.setAttribute( 'novalidate', 'novalidate' );
				}
			} );

		await page.locator( '#pagetopSetting #submit' ).click();

		// 0 → 未指定（空欄表示）に戻る。
		// PHP 側で image_width > 0 のときだけ value 属性を出すため、
		// 保存後の input の value は空文字になる。
		await expect( page.locator( '#pagetop_image_width' ) ).toHaveValue( '' );
		await expect( page.locator( '#pagetop_image_height' ) ).toHaveValue( '' );

		// フロントでは has-image は付くが width/height カスタムプロパティは出ない。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		await expect( btn ).toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		expect( style ).not.toBeNull();
		expect( style ).toContain( '--veu_page_top_button_url:url("' );
		// width/height カスタムプロパティは含まれない。
		expect( style ).not.toContain( '--veu_page_top_button_width' );
		expect( style ).not.toContain( '--veu_page_top_button_height' );

		// SCSS fallback で 40 × 38 px が効く。
		const box = await btn.boundingBox();
		expect( box ).not.toBeNull();
		if ( box ) {
			expect( box.width ).toBeCloseTo( 40, 0 );
			expect( box.height ).toBeCloseTo( 38, 0 );
		}
	} );

	test( '幅のみ指定時は width だけ反映され height は fallback (38px) のまま', async ( {
		page,
	} ) => {
		// 画像 URL + width=100 のみ事前投入。
		const sampleUrl = 'https://example.com/icon.svg';
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
			image_width: 100,
			image_height: 0,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		// フロントを確認。
		await page.goto( '/' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		await expect( btn ).toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		expect( style ).toContain( '--veu_page_top_button_width:100px' );
		// height カスタムプロパティは出ない（fallback 38px が効く）。
		expect( style ).not.toContain( '--veu_page_top_button_height' );

		// 実際のサイズも 100 × 38 になっていること。
		const box = await btn.boundingBox();
		expect( box ).not.toBeNull();
		if ( box ) {
			expect( box.width ).toBeCloseTo( 100, 0 );
			expect( box.height ).toBeCloseTo( 38, 0 );
		}
	} );

	test( '「画像の指定を解除」を押すと URL 入力欄と一緒にサイズ入力欄も非表示になる', async ( {
		page,
	} ) => {
		// 事前に画像 URL + サイズを保存。
		const sampleUrl = 'https://example.com/icon.svg';
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: sampleUrl,
			image_width: 60,
			image_height: 60,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		await page.goto(
			'/wp-admin/admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop'
		);
		await page.locator( '#pagetop_image_url' ).waitFor();

		// サイズ入力欄は表示済み。
		await expect( page.locator( '.veu_pagetop_image_size' ) ).toBeVisible();

		// 「画像の指定を解除」を押す。
		await page.locator( '#veu_pagetop_image_clear' ).click();

		// URL 入力欄が空、プレビューが非表示、サイズ入力欄も非表示になる。
		await expect( page.locator( '#pagetop_image_url' ) ).toHaveValue( '' );
		await expect( page.locator( '.veu_pagetop_image_preview' ) ).toBeHidden();
		await expect( page.locator( '.veu_pagetop_image_size' ) ).toBeHidden();
	} );

	test( '画像未指定で size 値だけが保存されていてもフロントには size カスタムプロパティが出力されない', async ( {
		page: _page,
	} ) => {
		// `image_url=''` のまま image_width/image_height だけが残った状態を作る
		// （例えば画像を解除した直後に保存しなかった等）。`veu_pagetop_render()` は
		// 画像未指定なら has-image を付けない仕様なので、サイズも出力されないはず。
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: '',
			image_width: 60,
			image_height: 60,
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );

		await _page.goto( '/' );
		const btn = _page.locator( 'a#page_top.page_top_btn' );
		// issue #1381: 未スクロール時は visibility:hidden のため、
		// 表示を検証する前にスクロールして body.scrolled を付ける。
		await scrollDown( _page );
		await expect( btn ).toBeVisible();
		await expect( btn ).not.toHaveClass( /has-image/ );
		const style = await btn.getAttribute( 'style' );
		// style 属性自体が出ない or 空文字。
		expect( style === null || style === '' ).toBeTruthy();
	} );
} );
