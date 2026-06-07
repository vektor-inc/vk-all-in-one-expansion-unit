/**
 * Page Top Button - アクセシビリティ（a11y）挙動の e2e テスト
 *
 * issue #1381 / feature/pagetop-btn-accessibility で行った
 * 「ページトップへ戻るボタンの a11y 改善」を検証する。
 * （PR #1387 由来の検証観点 — フォーカス除外の Tab 巡回 / :focus-visible /
 *   reduced-motion / has-image の drop-shadow / Enter・クリックでのスクロール —
 *   をこの 1 ファイルに統合している。spec は機能ごとに増やさない方針。）
 *
 * 検証する挙動:
 *  1. 未スクロール時のフォーカス除外:
 *     未スクロール状態（body に `scrolled` クラスが無い状態）では
 *     `.page_top_btn` が `visibility:hidden` + `pointer-events:none` で
 *     タブ順・クリック対象から外れ、キーボードフォーカスを受け取れないこと。
 *  2. スクロール後のフォーカス視認性:
 *     スクロール後に `.page_top_btn` が表示・操作可能になり、Tab で
 *     `#page_top` にフォーカスが回って `:focus-visible` の青い矩形 outline
 *     （2px solid #4f94d4）が出ること。
 *  3. クラスレス span による visually-hidden ラベル:
 *     `#page_top > span`（クラスレス）が visually-hidden（1px / clip-path）で
 *     隠されつつ、アクセシブルネーム「Back to top」が取得できること。
 *     旧実装の素の `>PAGE TOP</a>`（裸のラベルテキスト）や
 *     `color:transparent` 方式ではないこと。
 *  4. reduced-motion:
 *     `prefers-reduced-motion: reduce` 下で transition が無効化されること。
 *  5. デグレ確認:
 *     スクロール後にボタンをクリックでページ先頭へ戻る従来動作が壊れないこと。
 *  6. has-image のフォーカス:
 *     画像アップロード時（`has-image`）の `:focus-visible` は矩形 outline では
 *     なく `filter: drop-shadow(...)` でシルエットに追従すること。ただし
 *     forced-colors（ハイコントラスト）では outline が復帰すること。
 *
 * 設計メモ:
 *  - 表示トグルは body に付く `.scrolled` クラス（inc/pagetop-btn/js/pagetop-btn.js）
 *    で制御され、表示状態（visibility / pointer-events / opacity）は CSS が一元管理
 *    する。Playwright では実際に `window.scrollTo` でスクロールイベントを発火させて
 *    `.scrolled` を付与する（scrollDown ヘルパー）。
 *  - フォーカスリング（:focus-visible）はマウスクリックでは発火せずキーボード操作で
 *    のみマッチするため、フォーカス系の検証では必ず `keyboard.press('Tab')` で
 *    フォーカスを移す。
 *  - 画像なしを前提にするテスト群と、画像 URL を書き込む has-image describe が
 *    tests サイトの共有 DB（vkExUnit_pagetop オプション）を競合しないよう、
 *    ファイル単位でシリアル実行し、各テスト前に option を既知の状態へ戻す。
 */
import { test, expect, type Page } from '@playwright/test';
import { execFileSync } from 'child_process';

// このファイル内のテストは全て同じ `vkExUnit_pagetop` オプション（tests サイトの
// 共有 DB）を読む / 書き換えるため、ファイル単位でシリアル実行する。
// 特に「未スクロール時にボタンが隠れている / has-image が付いていない」ことを見る
// テストと、has-image describe（option に画像 URL を書き込む）が並列に走ると、
// 前者がフロントを読むタイミングで後者の書き込みが反映されてレースで落ちる。
// fullyParallel: true（playwright.config.ts）なので describe 単位の serial だけでは
// 別 describe 間の並列を抑えられないため、ファイル先頭で serial を宣言する。
// （pagetop-btn-image.spec.ts も同じ理由で serial 運用）。
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

// body に `.scrolled` を付与させるため、実際にスクロールイベントを発火させる。
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

test.describe( 'Page Top Button accessibility (#1381)', () => {
	const btnSelector = 'a#page_top.page_top_btn';

	// 各テスト前に option をリセットして画像未設定（素のボタン）状態にする。
	// has-image describe が（serial 実行で）先に走って option に画像 URL を
	// 残していても、ここで未設定状態へ確実に戻してからフロントを検証する。
	// a11y 検証はフロントのみで完結するため、管理画面ログインは不要。
	test.beforeEach( () => {
		resetPagetopOption();
	} );

	test.afterAll( () => {
		resetPagetopOption();
	} );

	// 観点1: 未スクロール時はタブ順・クリック対象から除外される
	test( '未スクロール時はボタンが visibility:hidden + pointer-events:none で隠れており、Tab フォーカスが当たらない', async ( {
		page,
	} ) => {
		// wp_footer フックで全フロントページに共通出力されるため、トップで検証。
		await page.goto( '/' );
		const btn = page.locator( btnSelector );
		// 要素自体は DOM に存在する（未スクロールでも出力はされる）。
		await expect( btn ).toHaveCount( 1 );

		// 未スクロール時は body に scrolled クラスが付いていないこと（前提確認）。
		await expect( page.locator( 'body' ) ).not.toHaveClass( /scrolled/ );

		// computed style が visibility:hidden / pointer-events:none であること。
		const computed = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				visibility: s.visibility,
				pointerEvents: s.pointerEvents,
			};
		} );
		expect( computed.visibility ).toBe( 'hidden' );
		expect( computed.pointerEvents ).toBe( 'none' );

		// visibility:hidden の要素は Playwright 的にも「不可視」と判定される。
		// = タブ順・クリック対象から除外されている、という要件1の中核。
		await expect( btn ).toBeHidden();

		// 念のため、プログラム的に focus() を試みても実際にはフォーカスが
		// 乗らない（visibility:hidden 要素はフォーカス不可）ことを確認する。
		await btn.evaluate( ( el: HTMLElement ) => el.focus() );
		await expect( btn ).not.toBeFocused();

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

	// 観点2: スクロール後は表示・操作可能になり、Tab で #page_top に到達して
	// :focus-visible の青い矩形 outline が出る
	test( 'スクロール後は表示・操作可能になり、Tab で #page_top にフォーカスが回って :focus-visible の青い outline が出る', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		await scrollDown( page );

		const btn = page.locator( btnSelector );
		// スクロール後は表示され操作可能（visibility:visible / pointer-events:auto）。
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
		// :focus-visible（青い矩形 outline 1 本）が当たっているはず。
		// 画像なし状態は outline のみで box-shadow は足さない方針なので、
		// outline が solid / 2px で出ていることを検証する（色は環境差で
		// rgb 表記が揺れうるため style / width に絞る）。
		const focusStyle = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				outlineStyle: s.outlineStyle,
				outlineWidth: s.outlineWidth,
			};
		} );
		expect( focusStyle.outlineStyle ).toBe( 'solid' );
		expect( focusStyle.outlineWidth ).toBe( '2px' );
	} );

	// 観点3: クラスレス span による visually-hidden ラベルとアクセシブルネーム
	test( '#page_top のラベルはクラスレス span で visually-hidden（1px/clip-path）かつアクセシブルネーム "Back to top" が取得できる', async ( {
		page,
	} ) => {
		await page.goto( '/' );

		const btn = page.locator( btnSelector );
		await expect( btn ).toHaveCount( 1 );

		// `<a>` の直下にクラスレスの span が 1 つだけあること。
		const span = page.locator( `${ btnSelector } > span` );
		await expect( span ).toHaveCount( 1 );
		await expect( span ).toHaveText( 'Back to top' );
		// span にクラスが付いていないこと（screen-reader-text 等を足さない方針）。
		const spanClass = ( await span.getAttribute( 'class' ) ) ?? '';
		expect( spanClass ).toBe( '' );

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

		// 旧実装の「裸のラベルテキスト」回帰防止: `<a>` 直下のテキストノードに
		// 可視テキストが無いこと（span 化されていることの裏取り）。
		const directText = await btn.evaluate( ( el ) => {
			return Array.from( el.childNodes )
				.filter( ( n ) => n.nodeType === Node.TEXT_NODE )
				.map( ( n ) => ( n.textContent ?? '' ).trim() )
				.join( '' );
		} );
		expect( directText ).toBe( '' );
		// 旧文言 "PAGE TOP" が `<a>` 全体のどこにも出ていないこと（保険）。
		const innerText = ( await btn.innerText().catch( () => '' ) ) ?? '';
		expect( innerText ).not.toContain( 'PAGE TOP' );

		// アクセシブルネームの検証はスクロール後に行う。
		// 未スクロール時はボタンが visibility:hidden のためアクセシビリティツリー
		// から除外されており、role=link としては露出しない（観点1の裏返し）。
		// 表示状態になって初めて支援技術にラベルが届くことを確認する。
		await scrollDown( page );

		// アクセシビリティツリー上でリンクのアクセシブルネームが "Back to top"
		// として取得できること。getByRole はアクセシブルネームでマッチする。
		const linkByName = page.getByRole( 'link', { name: 'Back to top' } );
		await expect( linkByName ).toHaveCount( 1 );
		// 取得したリンクが #page_top 本体であることを id で確認する。
		await expect( linkByName ).toHaveAttribute( 'id', 'page_top' );
	} );

	// 観点4: reduced-motion で transition が無効化される
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

	// 観点5: デグレ確認 - クリックでページ先頭へ戻る
	test( 'スクロール後に #page_top をクリックするとページ先頭へ戻る（従来動作）', async ( {
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

// 観点6: 画像アップロード時（has-image）のフォーカス
// 画像設定は vkExUnit_pagetop オプションを書き換えるため、他テストと競合しない
// よう独立した describe にしてシリアル実行し、前後で初期化する。
test.describe( 'Page Top Button accessibility - has-image focus (#1381)', () => {
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

	test( 'has-image 時の :focus-visible は outline:none かつ filter:drop-shadow(...) でシルエットに追従する', async ( {
		page,
	} ) => {
		await page.goto( '/' );
		const btn = page.locator( btnSelector );
		// has-image クラスが付いていること。
		await expect( btn ).toHaveClass( /has-image/ );

		// スクロールして表示・操作可能にする。
		await scrollDown( page );
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
				filter: s.filter,
			};
		} );
		// outline: none
		expect( style.outlineStyle ).toBe( 'none' );
		// filter に drop-shadow が当たっていること（シルエット追従の縁取り）。
		expect( style.filter ).toContain( 'drop-shadow' );
	} );

	test( 'forced-colors（ハイコントラスト）では has-image でも outline が復帰する', async ( {
		page,
	} ) => {
		// forced-colors をエミュレート（ハイコントラストモード相当）。
		// このモードでは filter:drop-shadow が剥がされうるため、
		// フォーカス指標が消えないよう outline が復帰する必要がある。
		await page.emulateMedia( { forcedColors: 'active' } );
		await page.goto( '/' );
		const btn = page.locator( btnSelector );
		await expect( btn ).toHaveClass( /has-image/ );

		await scrollDown( page );
		await expect( btn ).toBeVisible();

		// キーボード操作で #page_top にフォーカス。
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

		// forced-colors 下では @media (forced-colors: active) により
		// has-image でも矩形 outline が復帰する（solid / 2px）。
		const focusStyle = await btn.evaluate( ( el ) => {
			const s = window.getComputedStyle( el );
			return {
				outlineStyle: s.outlineStyle,
				outlineWidth: s.outlineWidth,
			};
		} );
		expect( focusStyle.outlineStyle ).toBe( 'solid' );
		expect( focusStyle.outlineWidth ).toBe( '2px' );
	} );
} );
