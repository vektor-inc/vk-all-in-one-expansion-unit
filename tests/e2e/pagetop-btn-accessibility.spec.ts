/**
 * Page Top Button - アクセシビリティ（a11y）挙動の e2e テスト
 *
 * issue #1381 / feature/pagetop-btn-accessibility で行った
 * 「ページトップへ戻るボタンの a11y 改善」を検証する。
 *
 * 検証する挙動:
 *  1. フロントの `a#page_top` 内に `span.screen-reader-text` があり、
 *     読み上げ名（アクセシブルネーム）が取得できること。
 *     旧実装の素の `>PAGE TOP</a>`（裸のラベルテキスト）が無いこと。
 *  2. 未スクロール状態（body に `scrolled` クラスが付いていない状態）では
 *     `.page_top_btn` が `visibility:hidden` でタブ順から外れており、
 *     キーボードフォーカスを受け取れないこと。
 *  3. スクロール後（body に `scrolled` クラスが付く状態）に
 *     `.page_top_btn` が表示され、フォーカス可能になること。
 *
 * 設計メモ:
 *  - 既存の pagetop-btn-image.spec.ts は「画像機能」専用で関心が異なるため、
 *    a11y は独立した spec として切り出した（describe を混ぜると serial 実行の
 *    意図がぼやけ、画像 option 初期化と a11y の前提条件が混在するため）。
 *  - 画像未設定（デフォルトアイコン）状態を前提に検証したいので、テスト前に
 *    既存テストと同じ `resetPagetopOption()` で `vkExUnit_pagetop` を
 *    既知の初期状態（option 削除）に戻す。これにより has-image が付かない
 *    素のボタン出力で a11y を確認できる。
 *  - スクロール状態の作り方:
 *    フロントの pagetop-btn.js は `window.addEventListener('scroll', ...)` で
 *    `window.pageYOffset > 0` のとき body に `scrolled` クラスを付ける実装。
 *    実 DOM にスクロール可能な高さが無いと `scrollTo` しても pageYOffset が
 *    0 のままになり flaky になりうるため、ここでは「JS の責務（scrolled 付与）」
 *    ではなく「CSS の責務（scrolled 時に表示・フォーカス可になる）」を検証する
 *    目的に絞り、body に `scrolled` クラスを直接付与して表示状態を作る。
 *    （実装上 JS が行うのは `scrolled` の付け外しのみで、表示制御は CSS が
 *    一元管理している。SCSS のコメントにも明記されている。）
 */
import { test, expect } from '@playwright/test';
import { execFileSync } from 'child_process';

// このファイルは `vkExUnit_pagetop` オプションを書き換える前提なので、
// 念のためファイル単位でシリアル実行する（playwright.config.js は
// fullyParallel: true のため、describe 単位指定だけでは別 describe 間の
// 並列を抑制できない、という既存 spec と同じ理由）。
test.describe.configure( { mode: 'serial' } );

// wp-env の `tests-cli` コンテナ経由で wp-cli を実行するヘルパー。
// Playwright のテスト対象 ( WP_BASE_URL ) は wp-env の **tests** サイトを
// 向いているため、必ず `tests-cli`（development の `cli` ではない）を使う。
// shell ではなく execFileSync で引数を配列のまま渡し、シェル解釈を経由しない。
// （pagetop-btn-image.spec.ts の流儀を踏襲）
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

// vkExUnit_pagetop オプションを削除して既知の初期状態（画像未設定）に戻す。
// wp-cli が「option がそもそも存在しない」と返すケースのみ握りつぶし、
// それ以外の予期しないエラー（wp-env が動いていない等）は throw する。
// （pagetop-btn-image.spec.ts の resetPagetopOption と同じ実装）
const resetPagetopOption = (): void => {
	// 実機サイト（例: Local の vk-x-t9.local）に WP_BASE_URL を向けて
	// a11y 挙動だけを確認したい場合、wp-env の tests-cli は存在しないため
	// `VEU_E2E_SKIP_WPCLI=1` を渡すと option リセットをスキップする。
	// このとき対象サイトが画像未設定（デフォルトアイコン）状態であることは
	// 実行者の前提とする。CI（wp-env）はこのフラグを設定しないため、
	// 従来どおり tests-cli で option をリセットする（挙動に影響しない）。
	if ( '1' === process.env.VEU_E2E_SKIP_WPCLI ) {
		return;
	}
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_pagetop' ] );
	} catch ( e ) {
		// 「`vkExUnit_pagetop` が未存在」を示す文言に限定して握りつぶし、
		// option delete 以外の障害メッセージは誤って握りつぶさず再 throw する。
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

test.describe( 'Page Top Button accessibility (#1381)', () => {
	// 全テストで同じ vkExUnit_pagetop オプション状態を前提とするため、
	// テスト間の競合を避けてシリアル実行する。
	test.describe.configure( { mode: 'serial' } );

	// 各テスト前に option をリセットして画像未設定（素のボタン）状態にする。
	// a11y 検証はフロントのみで完結するため、ここでは管理画面ログインは不要。
	test.beforeEach( () => {
		resetPagetopOption();
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	test( '読み上げ名は span.screen-reader-text 経由で提供され、裸の "PAGE TOP" テキストは出力されない', async ( {
		page,
	} ) => {
		// wp_footer フックで全フロントページに共通出力されるため、トップで検証。
		await page.goto( '/' );

		const btn = page.locator( 'a#page_top.page_top_btn' );
		// ボタン自体は DOM に存在すること（未スクロールでも要素自体は出力される）。
		await expect( btn ).toHaveCount( 1 );

		// `<a>` の直下に screen-reader-text の span があること。
		const srText = btn.locator( 'span.screen-reader-text' );
		await expect( srText ).toHaveCount( 1 );

		// 読み上げ名（span テキスト）が空でないこと。
		// wp-env のデフォルトロケールは en_US なので "Back to top" になるが、
		// ロケール変動に過度に依存しないよう、まずは「非空であること」を確認する。
		const label = ( await srText.textContent() )?.trim() ?? '';
		expect( label.length ).toBeGreaterThan( 0 );
		// en_US 環境では翻訳元文字列がそのまま出る想定。
		expect( label ).toBe( 'Back to top' );

		// アクセシブルネームが span のテキストから取得できること。
		// `getByRole('link', { name })` は accessible name 一致でリンクを引けるかを見る。
		// （hidden 要素も role セレクタの対象になるよう includeHidden を付ける。
		//  未スクロール時は visibility:hidden で aria 的にも hidden 扱いになりうるため）
		const linkByName = page.getByRole( 'link', {
			name: 'Back to top',
			includeHidden: true,
		} );
		await expect( linkByName ).toHaveCount( 1 );

		// 旧実装の「裸のラベルテキスト」回帰防止:
		// `<a ...>PAGE TOP</a>` のように span で包まれない直書きテキストが
		// 無いこと（screen-reader-text 化されていることの裏取り）。
		// `<a>` の直接の子テキストノードを集計して、span 外に可視テキストが
		// 無いことを確認する。
		const directText = await btn.evaluate( ( el ) => {
			// 子ノードのうち、テキストノード（nodeType 3）だけを連結。
			// span などの要素ノードのテキストは含めない。
			return Array.from( el.childNodes )
				.filter( ( n ) => n.nodeType === Node.TEXT_NODE )
				.map( ( n ) => ( n.textContent ?? '' ).trim() )
				.join( '' );
		} );
		expect( directText ).toBe( '' );
		// 旧文言 "PAGE TOP" が `<a>` 全体のどこにも出ていないこと（保険）。
		const innerText = ( await btn.innerText().catch( () => '' ) ) ?? '';
		expect( innerText ).not.toContain( 'PAGE TOP' );
	} );

	test( '未スクロール状態ではボタンが visibility:hidden でタブ順から外れ、キーボードフォーカスを受け取れない', async ( {
		page,
	} ) => {
		await page.goto( '/' );

		const btn = page.locator( 'a#page_top.page_top_btn' );
		await expect( btn ).toHaveCount( 1 );

		// 未スクロール時は body に scrolled クラスが付いていないこと（前提確認）。
		await expect( page.locator( 'body' ) ).not.toHaveClass( /scrolled/ );

		// computed style で visibility:hidden / pointer-events:none を確認。
		// visibility:hidden の要素はタブ順から外れ、キーボードフォーカスを
		// 受け取れない（WCAG 2.4.3 / 2.4.7 の意図）。
		const visibility = await btn.evaluate(
			( el ) => getComputedStyle( el ).visibility
		);
		expect( visibility ).toBe( 'hidden' );

		const pointerEvents = await btn.evaluate(
			( el ) => getComputedStyle( el ).pointerEvents
		);
		expect( pointerEvents ).toBe( 'none' );

		// Playwright の toBeVisible() は visibility:hidden を「不可視」と判定する。
		// （opacity:0 だけなら visible 判定になるが、本実装は visibility:hidden を
		//  併用しているため hidden 判定になる）
		await expect( btn ).toBeHidden();

		// 念のため、プログラム的に focus() を試みても実際にはフォーカスが
		// 乗らない（visibility:hidden 要素はフォーカス不可）ことを確認する。
		await btn.evaluate( ( el ) => ( el as HTMLElement ).focus() );
		await expect( btn ).not.toBeFocused();
	} );

	test( 'スクロール後（body.scrolled）はボタンが表示され、フォーカス可能になる', async ( {
		page,
	} ) => {
		await page.goto( '/' );

		const btn = page.locator( 'a#page_top.page_top_btn' );
		await expect( btn ).toHaveCount( 1 );

		// スクロール状態を作る。
		// 実装上、表示制御は CSS（`.scrolled .page_top_btn`）が一元管理しており、
		// JS は body への `scrolled` クラス付与のみを担当する。テスト DOM の
		// 高さに依存して pageYOffset が 0 のままになる flaky を避けるため、
		// ここでは body に `scrolled` クラスを直接付与して表示状態を再現する。
		await page.evaluate( () =>
			document.body.classList.add( 'scrolled' )
		);

		// body に scrolled クラスが付いたことを確認。
		await expect( page.locator( 'body' ) ).toHaveClass( /scrolled/ );

		// 表示状態（visibility:visible / pointer-events:auto / opacity:1）になること。
		const visibility = await btn.evaluate(
			( el ) => getComputedStyle( el ).visibility
		);
		expect( visibility ).toBe( 'visible' );

		const pointerEvents = await btn.evaluate(
			( el ) => getComputedStyle( el ).pointerEvents
		);
		expect( pointerEvents ).toBe( 'auto' );

		// Playwright 的にも可視と判定されること。
		await expect( btn ).toBeVisible();

		// 表示状態ではキーボード相当の focus() でフォーカスが乗ること。
		await btn.evaluate( ( el ) => ( el as HTMLElement ).focus() );
		await expect( btn ).toBeFocused();
	} );
} );
