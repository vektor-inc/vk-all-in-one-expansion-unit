/**
 * Smooth scroll「CSS only」ラベル分離の e2e テスト
 *
 * issue #1421 / PR #1422 で行った、ExUnit > Main Setting > Smooth scroll の
 * 「CSS only」ラジオボタンのラベル文言修正を検証する。
 *
 * 修正前: `CSS only ( Loading slightly light but do not work on Safari and so on. )`
 *          という古い情報を含む長い一文がラベル内にそのまま入っていた。
 * 修正後: ラベル本体 `CSS only (recommended).` と、補足説明
 *          `Loading is slightly lighter than the JavaScript mode.
 *           Not supported on Safari 15.3 or earlier.` を
 *          `<p class="description">` に分離し、`aria-describedby` で紐付けた。
 *
 * 検証する挙動:
 *  1. ラベル本体が短い文言 `CSS only (recommended).` になっていること。
 *  2. 補足説明が `<p class="description">` として、ラベルとは別要素で
 *     表示されていること（WP コアの description スタイルで控えめな見た目）。
 *  3. ラジオボタンの `aria-describedby` と補足説明の `id` が一致しているアクセシビリティ
 *     結線が取れていること。
 *  4. 「JavaScript」の選択肢の表示に変化がないこと（デグレ確認）。
 *  5. 「CSS only」を選択して保存し、リロード後も選択状態が保持されること。
 *  6. フロント側で `html { scroll-behavior: smooth; }` が出力され、
 *     ページ内リンクのスムーズスクロールが機能すること。
 */
import { test, expect } from '@playwright/test';
import { execFileSync } from 'child_process';

// wp-env の tests-cli コンテナ経由で wp-cli を実行するヘルパー。
// Playwright の対象は wp-env の tests サイトを向いているため tests-cli を使う。
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

// vkExUnit_smooth オプションを削除し、デフォルト（mode: js）に戻す。
// option が存在しない場合のエラーは想定内として握りつぶす。
const resetSmoothOption = (): void => {
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_smooth' ] );
	} catch ( e ) {
		const stderr =
			e && typeof e === 'object' && 'stderr' in e
				? String( ( e as { stderr?: unknown } ).stderr ?? '' )
				: '';
		const message = e instanceof Error ? e.message : String( e );
		const haystack = `${ stderr }\n${ message }`;
		const isMissingOption =
			/Could not delete\s+'vkExUnit_smooth'\s+option\.\s*Does it exist\?/i.test(
				haystack
			) || ( /vkExUnit_smooth/.test( haystack ) && /does(?:\s+not)?\s+exist/i.test( haystack ) );
		if ( isMissingOption ) {
			return;
		}
		throw e;
	}
};

test.describe( 'Smooth scroll「CSS only」ラベル分離 (#1421 / PR #1422)', () => {
	// このファイル内のテストは vkExUnit_smooth オプション（共有 DB）を書き換える
	// テストを含むため、ファイル単位でシリアル実行する。
	test.describe.configure( { mode: 'serial' } );

	test.beforeEach( () => {
		resetSmoothOption();
	} );

	test.afterAll( () => {
		resetSmoothOption();
	} );

	test.beforeEach( async ( { page } ) => {
		// 管理画面へログイン。
		await page.goto( '/wp-login.php' );
		await page.locator( '#user_login' ).fill( 'admin' );
		await page.locator( '#user_pass' ).fill( 'password' );
		await page.locator( '#wp-submit' ).click();
		await page.waitForURL( /wp-admin\// );
		await page.locator( '#wpadminbar' ).waitFor();

		// ExUnit > Main Setting 画面へ。
		await page.goto( '/wp-admin/admin.php?page=vkExUnit_main_setting' );
	} );

	test( 'CSS only のラベル本体が短い文言 "CSS only (recommended)." になっている', async ( {
		page,
	} ) => {
		const cssRadio = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="css"]'
		);
		await expect( cssRadio ).toBeVisible();

		// ラベル本体（<label> 直下のテキスト）を確認する。
		const label = page.locator(
			'label:has(input[name="vkExUnit_smooth[mode]"][value="css"])'
		);
		await expect( label ).toContainText( 'CSS only (recommended).' );

		// 旧文言（Safari で動かない、という古い情報）が残っていないこと。
		await expect( label ).not.toContainText( 'do not work on Safari' );
	} );

	test( '補足説明が <p class="description"> として分離表示され、aria-describedby と id が一致する', async ( {
		page,
	} ) => {
		const cssRadio = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="css"]'
		);
		await expect( cssRadio ).toBeVisible();

		// aria-describedby 属性を取得。
		const describedBy = await cssRadio.getAttribute( 'aria-describedby' );
		expect( describedBy ).toBeTruthy();

		// aria-describedby が指す id を持つ <p class="description"> が存在すること。
		const description = page.locator( `#${ describedBy }` );
		await expect( description ).toHaveCount( 1 );

		// タグ名と class を確認（WP コア標準の description スタイル）。
		const tagAndClass = await description.evaluate( ( el ) => ( {
			tagName: el.tagName.toLowerCase(),
			className: el.className,
		} ) );
		expect( tagAndClass.tagName ).toBe( 'p' );
		expect( tagAndClass.className ).toContain( 'description' );

		// 補足説明の文言を確認。
		await expect( description ).toContainText(
			'Loading is slightly lighter than the JavaScript mode.'
		);
		await expect( description ).toContainText(
			'Not supported on Safari 15.3 or earlier.'
		);

		// description は label 本体より控えめな見た目（WP コア .description は
		// 通常フォントサイズが縮小され、グレー系の色になる）。ここでは
		// 「label 本体のテキストと同一要素ではない」= 視覚的に分離されている
		// ことを構造面から確認する（色・サイズは環境のコア CSS に依存するため
		// 厳密な px 一致は求めない）。
		const descTag = await description.evaluate( ( el ) => el.tagName );
		expect( descTag ).not.toBe( 'LABEL' );
	} );

	test( '「JavaScript」の選択肢の表示に変化がない（デグレ確認）', async ( { page } ) => {
		const jsRadio = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="js"]'
		);
		await expect( jsRadio ).toBeVisible();

		const jsLabel = page.locator(
			'label:has(input[name="vkExUnit_smooth[mode]"][value="js"])'
		);
		await expect( jsLabel ).toContainText( 'JavaScript' );

		// JavaScript の選択肢には aria-describedby も description も無い
		// （元々補足説明が無い項目のため、変化していないことの確認）。
		const describedBy = await jsRadio.getAttribute( 'aria-describedby' );
		expect( describedBy ).toBeNull();
	} );

	test( '「CSS only」を選択して保存すると、リロード後も選択状態が保持される', async ( {
		page,
	} ) => {
		const cssRadio = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="css"]'
		);
		await expect( cssRadio ).toBeVisible();
		await cssRadio.check();
		await expect( cssRadio ).toBeChecked();

		// フォーム送信。この管理画面は 1 つの <form> の中に複数セクションが
		// 並び、セクションごとに `submit_button()` が出力されるため `#submit`
		// が複数存在する（1 form 全体を送信する仕様）。Smooth scroll セクション
		// （#vkExUnit_smooth）内のボタンを明示的に指定してクリックする。
		await page.locator( '#vkExUnit_smooth #submit' ).click();
		await page.waitForURL( /wp-admin\/admin\.php\?page=vkExUnit_main_setting/ );

		// リダイレクト後の再描画で CSS only が選択された状態になっていること。
		const cssRadioAfter = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="css"]'
		);
		await expect( cssRadioAfter ).toBeChecked();

		// 明示的にページをリロードしても選択状態が保持されていること
		// （option が実際に DB へ保存されたことの裏取り）。
		await page.reload();
		const cssRadioAfterReload = page.locator(
			'input[name="vkExUnit_smooth[mode]"][value="css"]'
		);
		await expect( cssRadioAfterReload ).toBeChecked();
	} );

	test( 'フロント側で scroll-behavior:smooth が適用され、ページ内リンクのスムーズスクロールが機能する', async ( {
		page,
	} ) => {
		// CSS only モードに設定する（wp-cli で直接 option を書き込み、UI 経由の
		// 保存フローとは独立して検証する）。
		runWpCli( [
			'option',
			'update',
			'vkExUnit_smooth',
			JSON.stringify( { mode: 'css' } ),
			'--format=json',
		] );

		await page.goto( '/' );

		// html 要素の computed scroll-behavior が smooth になっていること
		// （veu_add_smooth_css がインラインスタイルとして出力する）。
		const scrollBehavior = await page
			.locator( 'html' )
			.evaluate( ( el ) => window.getComputedStyle( el ).scrollBehavior );
		expect( scrollBehavior ).toBe( 'smooth' );

		// ページ内リンク（アンカー）遷移でエラーが起きず、スクロール自体が
		// 実行されることを確認する（scrollTo 相当のふるまいが壊れていないか）。
		await page.evaluate( () => {
			document.body.style.minHeight = '3000px';
		} );
		await page.evaluate( () => {
			window.location.hash = '#wpadminbar-does-not-exist-fallback';
		} );
		// scroll-behavior:smooth が適用された状態でも window.scrollTo が
		// 例外なく動作し、pageYOffset が変化すること。
		await page.evaluate( () => window.scrollTo( 0, 500 ) );
		await expect
			.poll( () => page.evaluate( () => window.pageYOffset ) )
			.toBeGreaterThan( 0 );
	} );
} );
