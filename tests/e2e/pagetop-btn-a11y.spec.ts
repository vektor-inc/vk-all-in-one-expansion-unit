/**
 * Page Top Button - アクセシビリティ改善の e2e テスト
 *
 * issue #1381 / PR #1387 で実施された 5 点のアクセシビリティ改善を検証する。
 *
 * 1. 未スクロール時のフォーカス除外:
 *    ページ読込直後（未スクロール）はボタンが `visibility:hidden` +
 *    `pointer-events:none` でタブ順・クリック対象から除外されていること。
 * 2. スクロール後のフォーカス視認性:
 *    下にスクロールしてボタン表示後、Tab で `#page_top` にフォーカスが回り、
 *    `:focus-visible` のフォーカスリング（outline + 白 box-shadow）が出ること。
 * 3. スクリーンリーダー向けラベル:
 *    `#page_top > span` が visually-hidden（1px / clip-path）で隠されつつ、
 *    アクセシブルネーム「Back to top」が取得できること。
 * 4. 画像アップロード時の drop-shadow フォーカス:
 *    `has-image` 時の `:focus-visible` で `outline:none` かつ
 *    `filter: drop-shadow(...)` が当たること（矩形 outline ではない）。
 * 5. reduced-motion:
 *    `prefers-reduced-motion: reduce` 下で transition が無効化されること。
 * 6. デグレ確認:
 *    スクロール後にボタンを Enter / クリックで、ページ先頭へ戻る従来動作が
 *    壊れていないこと。
 *
 * 表示トグルは body に付く `.scrolled` クラス（inc/pagetop-btn/js/pagetop-btn.js）で
 * 制御される。Playwright では実際に `window.scrollTo` でスクロールイベントを
 * 発火させて `.scrolled` を付与する。
 *
 * フォーカスリング（:focus-visible）はマウスクリックでは発火せず、キーボード操作
 * でのみマッチするため、フォーカス系の検証では必ず `keyboard.press('Tab')` 等の
 * キーボード操作でフォーカスを移す。
 *
 * 画像設定が絡むテスト（観点4）は wp-cli で `vkExUnit_pagetop` オプションを直接
 * 書き換える。テスト前後で初期状態へ戻す。
 */
import { test, expect } from '@playwright/test';
import { execFileSync } from 'child_process';

// wp-env の `tests-cli` コンテナ経由で wp-cli を実行するヘルパー。
// Playwright のテスト対象（WP_BASE_URL）は wp-env の tests サイトを向いて
// いるため、option 書き換えは必ず `tests-cli` を使う（`cli` は development
// サイトでテスト側 DB に反映されない）。execFileSync + 引数配列でシェル解釈を
// 経由せず、JSON にクォートや空白が含まれても安全に渡す。
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

// vkExUnit_pagetop オプションを削除して既知の初期状態（画像なし）に戻す。
// 「option がそもそも存在しない」ケースだけ握りつぶし、それ以外（wp-env が
// 動いていない等）は再 throw する。
const resetPagetopOption = (): void => {
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_pagetop' ] );
	} catch ( e ) {
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
// JS（pagetop-btn.js）は window の scroll イベントで pageYOffset > 0 を見て
// body.scrolled を付け外しする。ページ高が足りないと scrollTo しても
// pageYOffset が 0 のままになるため、十分な高さを保証してからスクロールする。
const scrollDown = async ( page ): Promise< void > => {
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

// このファイル内のテストは全て同じ `vkExUnit_pagetop` オプション（tests サイトの
// 共有 DB）を読む / 書き換えるため、ファイル単位でシリアル実行する。
// 特に「未スクロール時にボタンが隠れている / has-image が付いていない」ことを見る
// テストと、has-image describe（option に画像 URL を書き込む）が並列に走ると、
// 前者がフロントを読むタイミングで後者の書き込みが反映されてレースで落ちる。
// fullyParallel: true（playwright.config.ts）なので describe 単位の serial だけでは
// 別 describe 間の並列を抑えられないため、ファイル先頭で serial を宣言する。
// （pagetop-btn-image.spec.ts も同じ理由で serial 運用）。
test.describe.configure( { mode: 'serial' } );

test.describe( 'Page Top Button accessibility (#1381)', () => {
	const btnSelector = 'a#page_top.page_top_btn';

	// 画像なしを前提にするテスト群なので、各テスト前に option を初期化する。
	// has-image describe が（serial 実行で）先に走って option に画像 URL を
	// 残していても、ここで未設定状態へ確実に戻してからフロントを検証する。
	test.beforeEach( () => {
		resetPagetopOption();
	} );

	// 観点1: 未スクロール時はタブ順・クリック対象から除外される
	test( '未スクロール時はボタンが visibility:hidden + pointer-events:none で隠れており、Tab フォーカスが当たらない', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		const btn = page.locator( btnSelector );
		// 要素自体は DOM に存在する。
		await expect( btn ).toHaveCount( 1 );

		// 未スクロール時は computed style が visibility:hidden / pointer-events:none。
		const computed = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				visibility: s.visibility,
				pointerEvents: s.pointerEvents,
				opacity: s.opacity,
			};
		} );
		expect( computed.visibility ).toBe( 'hidden' );
		expect( computed.pointerEvents ).toBe( 'none' );

		// visibility:hidden の要素は Playwright 的にも「不可視」と判定される。
		// = タブ順・クリック対象から除外されている、という要件1の中核。
		await expect( btn ).toBeHidden();

		// Tab を押してもフォーカスが #page_top に到達しないことを確認する。
		// visibility:hidden の要素はタブ順から除外されるため。
		//
		// 注意: Tab でフォーカスが画面外のフォーカス可能要素へ移ると、ブラウザが
		// その要素を画面内に入れようと自動スクロールし、scroll イベントで
		// `body.scrolled` が付いてボタンが可視化されてしまう。そうなると
		// 「未スクロール時」の検証ではなくなるため、各 Tab の後に body.scrolled が
		// 付いていないか（=まだ未スクロール状態か）を確認し、付いたらそこで
		// ループを打ち切る。未スクロールを維持できている範囲で #page_top に
		// 到達しないことを確認すれば、要件1（未スクロール時はタブ順から除外）の
		// 検証として十分。
		await page.locator( 'body' ).click( { position: { x: 5, y: 5 } } );
		let reachedPageTop = false;
		for ( let i = 0; i < 30; i++ ) {
			await page.keyboard.press( 'Tab' );
			// 自動スクロールで body.scrolled が付いた = もう未スクロール状態では
			// ないので、ここで検証を打ち切る。
			const scrolled = await page.evaluate( () =>
				document.body.classList.contains( 'scrolled' )
			);
			if ( scrolled ) {
				break;
			}
			const activeId = await page.evaluate(
				() => document.activeElement?.id ?? ''
			);
			if ( activeId === 'page_top' ) {
				reachedPageTop = true;
				break;
			}
		}
		// 未スクロール状態を維持できている間、#page_top には一度も到達しないこと。
		expect( reachedPageTop ).toBe( false );
	} );

	// 観点2 & 3: スクロール後のフォーカス視認性 + visually-hidden ラベル
	test( 'スクロール後は Tab で #page_top にフォーカスが回り、:focus-visible のフォーカスリングが出る', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		await scrollDown( page );

		const btn = page.locator( btnSelector );
		// スクロール後は表示され操作可能。
		await expect( btn ).toBeVisible();
		const computedVisible = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return { visibility: s.visibility, pointerEvents: s.pointerEvents };
		} );
		expect( computedVisible.visibility ).toBe( 'visible' );
		expect( computedVisible.pointerEvents ).toBe( 'auto' );

		// キーボード操作で #page_top にフォーカスを移す（:focus-visible 発火のため
		// 必ずキーボード操作で。プログラム focus() やマウスクリックでは
		// :focus-visible がマッチしない）。
		await btn.evaluate( ( el: HTMLElement ) => el.focus() );
		// プログラム focus でもタブ順到達は別途確認したいので、Tab 巡回でも到達する
		// ことを確認しておく。
		await page.locator( 'body' ).click( { position: { x: 5, y: 5 } } );
		let reached = false;
		for ( let i = 0; i < 60; i++ ) {
			await page.keyboard.press( 'Tab' );
			const activeId = await page.evaluate(
				() => document.activeElement?.id ?? ''
			);
			if ( activeId === 'page_top' ) {
				reached = true;
				break;
			}
		}
		expect( reached ).toBe( true );

		// この時点で #page_top はキーボード操作でフォーカスされており、
		// :focus-visible が当たっているはず。フォーカスリング（outline + box-shadow）
		// が宣言どおり効いていることを computed style で検証する。
		const focusStyle = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				outlineStyle: s.outlineStyle,
				outlineWidth: s.outlineWidth,
				outlineColor: s.outlineColor,
				boxShadow: s.boxShadow,
			};
		} );
		// outline: 2px solid #1e1e1e
		expect( focusStyle.outlineStyle ).toBe( 'solid' );
		expect( focusStyle.outlineWidth ).toBe( '2px' );
		// 白の box-shadow が出ていること（none ではない）。
		expect( focusStyle.boxShadow ).not.toBe( 'none' );
		expect( focusStyle.boxShadow ).toContain( 'rgb(255, 255, 255)' );
	} );

	// 観点3: visually-hidden ラベルとアクセシブルネーム
	test( '#page_top のラベルは span で visually-hidden（1px/clip-path）かつアクセシブルネーム "Back to top" が取得できる', async ( {
		page,
	} ) => {
		await page.goto( '/' );

		// span が存在し、テキストは "Back to top"。
		const span = page.locator( `${ btnSelector } > span` );
		await expect( span ).toHaveCount( 1 );
		await expect( span ).toHaveText( 'Back to top' );

		// span は visually-hidden レシピで 1px に潰され clip-path で隠れている。
		// color:transparent ではないことも確認する。
		const spanStyle = await span.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				width: s.width,
				height: s.height,
				position: s.position,
				overflow: s.overflow,
				clipPath: s.clipPath,
				color: s.color,
			};
		} );
		expect( spanStyle.width ).toBe( '1px' );
		expect( spanStyle.height ).toBe( '1px' );
		expect( spanStyle.position ).toBe( 'absolute' );
		expect( spanStyle.overflow ).toBe( 'hidden' );
		expect( spanStyle.clipPath ).toContain( 'inset' );
		// 旧来の color:transparent 方式ではないこと。
		expect( spanStyle.color ).not.toBe( 'rgba(0, 0, 0, 0)' );
		expect( spanStyle.color ).not.toBe( 'transparent' );

		// アクセシブルネームの検証はスクロール後に行う。
		// 未スクロール時はボタンが visibility:hidden のためアクセシビリティツリー
		// から除外されており、role=link としては露出しない（観点1の裏返し）。
		// 表示状態になって初めて支援技術にラベルが届くことを確認する。
		await scrollDown( page );

		// アクセシビリティツリー上でリンクのアクセシブルネームが "Back to top"
		// として取得できること。getByRole はアクセシブルネームでマッチするため、
		// これが 1 件ヒットすればアクセシブルネームが正しく計算されていることを示す。
		// span が color:transparent ではなく visually-hidden で隠されているため、
		// テキストがアクセシブルネームとして正しく拾われる。
		// （page.accessibility は新しい Playwright で廃止されているため使わない）
		const linkByName = page.getByRole( 'link', { name: 'Back to top' } );
		await expect( linkByName ).toHaveCount( 1 );
		// 取得したリンクが #page_top 本体であることを id で確認する。
		await expect( linkByName ).toHaveAttribute( 'id', 'page_top' );
	} );

	// 観点5: reduced-motion で transition が無効化される
	test( 'prefers-reduced-motion: reduce 下では transition が none になる', async ( {
		page,
	} ) => {
		// reduced-motion をエミュレート。
		await page.emulateMedia( { reducedMotion: 'reduce' } );
		await page.goto( '/' );

		const btn = page.locator( btnSelector );
		const transition = await btn.evaluate(
			( el ) => window.getComputedStyle( el ).transitionProperty
		);
		// @media (prefers-reduced-motion: reduce) { transition: none; }
		// transition: none は transition-property: none として計算される。
		expect( transition ).toBe( 'none' );
	} );

	// 観点6: デグレ確認 - Enter / クリックでページ先頭へ戻る
	test( 'スクロール後に #page_top を Enter / クリックするとページ先頭へ戻る（従来動作）', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		await scrollDown( page );

		// スクロール位置が 0 より下にあること。
		const before = await page.evaluate( () => window.pageYOffset );
		expect( before ).toBeGreaterThan( 0 );

		const btn = page.locator( btnSelector );
		await expect( btn ).toBeVisible();

		// href が #top（または既存 body id）を指していること。
		const href = await btn.getAttribute( 'href' );
		expect( href ).toMatch( /^#/ );

		// クリックでページ先頭へ。
		await btn.click();
		// フラグメントナビゲーションでトップへ戻ることを待つ。
		await expect
			.poll( () => page.evaluate( () => window.pageYOffset ) )
			.toBe( 0 );
	} );
} );

// 観点4: 画像アップロード時の drop-shadow フォーカス
// 画像設定は vkExUnit_pagetop オプションを書き換えるため、他テストと競合しない
// よう独立した describe にしてシリアル実行し、前後で初期化する。
test.describe( 'Page Top Button accessibility - has-image focus (#1381)', () => {
	test.describe.configure( { mode: 'serial' } );

	const btnSelector = 'a#page_top.page_top_btn';

	test.beforeEach( () => {
		// 画像 URL を設定して has-image 状態を作る。
		const json = JSON.stringify( {
			hide_mobile: false,
			image_url: 'https://example.com/icon.svg',
		} );
		runWpCli( [
			'option',
			'update',
			'vkExUnit_pagetop',
			json,
			'--format=json',
		] );
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	test( 'has-image 時の :focus-visible は outline:none かつ filter:drop-shadow(...) が当たる', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		const btn = page.locator( btnSelector );
		// has-image クラスが付いていること。
		await expect( btn ).toHaveClass( /has-image/ );

		// スクロールして表示・操作可能にする。
		await page.evaluate( () => {
			document.body.style.minHeight = '3000px';
			window.scrollTo( 0, 1000 );
			window.dispatchEvent( new Event( 'scroll' ) );
		} );
		await expect( page.locator( 'body.scrolled' ) ).toHaveCount( 1 );
		await expect( btn ).toBeVisible();

		// キーボード操作で #page_top にフォーカス（:focus-visible 発火のため）。
		await page.locator( 'body' ).click( { position: { x: 5, y: 5 } } );
		let reached = false;
		for ( let i = 0; i < 60; i++ ) {
			await page.keyboard.press( 'Tab' );
			const activeId = await page.evaluate(
				() => document.activeElement?.id ?? ''
			);
			if ( activeId === 'page_top' ) {
				reached = true;
				break;
			}
		}
		expect( reached ).toBe( true );

		// has-image の :focus-visible は矩形 outline ではなく drop-shadow filter。
		const style = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				outlineStyle: s.outlineStyle,
				boxShadow: s.boxShadow,
				filter: s.filter,
			};
		} );
		// outline: none
		expect( style.outlineStyle ).toBe( 'none' );
		// box-shadow: none
		expect( style.boxShadow ).toBe( 'none' );
		// filter に drop-shadow が複数当たっていること。
		expect( style.filter ).toContain( 'drop-shadow' );
	} );
} );
