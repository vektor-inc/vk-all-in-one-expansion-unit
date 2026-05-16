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
import { test, expect } from '@playwright/test';
import { execFileSync } from 'child_process';

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'password';

// wp-env の cli コンテナ経由で wp-cli を実行するためのヘルパー。
// shell 経由ではなく execFileSync で引数を配列のまま渡すことで、
// JSON 等にクォートや空白が含まれてもシェル解釈を経由しない。
const runWpCli = ( args: string[] ): string => {
	return execFileSync(
		'npx',
		[ 'wp-env', 'run', 'cli', 'wp', ...args ],
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
		await page.waitForURL( /wp-admin\// );
		await page.waitForLoadState( 'networkidle' );
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	test( 'デフォルト（画像未設定）ではフロントに has-image クラスも style 属性も出力されない', async ( {
		page,
	} ) => {
		await page.goto( '/?p=1' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
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
		await page.waitForLoadState( 'networkidle' );

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
		await page.waitForLoadState( 'networkidle' );

		// フロントで出力を確認。
		await page.goto( '/?p=1' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
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
		await page.waitForLoadState( 'networkidle' );
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
		await page.waitForLoadState( 'networkidle' );

		// フロントで style 属性が消えていることを確認。
		await page.goto( '/?p=1' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
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
		await page.waitForLoadState( 'networkidle' );

		// CSS injection を試す payload（クォート・括弧・空白を含む）。
		const payload =
			'https://example.com/a.png");}body{background:red;//';
		const urlInput = page.locator( '#pagetop_image_url' );
		await urlInput.scrollIntoViewIfNeeded();
		await urlInput.fill( payload );
		await urlInput.blur();

		// 保存（pagetop セクション内の submit ボタン）。
		await page.locator( '#pagetopSetting #submit' ).click();
		await page.waitForLoadState( 'networkidle' );

		// 保存後、URL フィールドが空になっていること（サニタイザーが空文字を返したため）。
		const urlAfter = await page
			.locator( '#pagetop_image_url' )
			.inputValue();
		expect( urlAfter ).toBe( '' );

		// フロント側にも style 属性が付かないこと。
		await page.goto( '/?p=1' );
		const btn = page.locator( 'a#page_top.page_top_btn' );
		const style = await btn.getAttribute( 'style' );
		expect( style === null || style === '' ).toBeTruthy();
		await expect( btn ).not.toHaveClass( /has-image/ );
	} );
} );
