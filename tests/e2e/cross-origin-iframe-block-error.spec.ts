/**
 * クロスオリジン iframe があるページでブロックがエラー表示になる不具合 - e2e テスト (#1452)
 *
 * 【不具合の概要】
 * 本文に YouTube・X などクロスオリジン（別ドメイン）の iframe 埋め込みがあるページで、
 * シェアボタン等のブロックを配置すると、編集画面で
 * 「このブロックでエラーが発生したためプレビューできません」というエラー表示に
 * 差し替わり、ブロックの中身を確認できなくなる。
 *
 * 【原因】
 * 影響を受ける各ブロックの edit.js は useEffect 内で
 *   document.querySelector( '.block-editor__container iframe' )
 * を使って最初に見つかった iframe を掴み、その contentWindow.document を読もうと
 * していた。プレビュー領域（エディターキャンバス）が iframe 化されていない環境
 * （WordPress のバージョンやサイトの条件による）では、このセレクタが本文中の
 * クロスオリジン埋め込み iframe を先に掴んでしまい、contentWindow.document への
 * アクセスがブラウザの Same-Origin Policy により SecurityError を投げる。
 * この例外が useEffect 内で発生するため React のエラー境界に捕捉され、
 * ブロック全体がエラー表示に差し替わる。
 *
 * 【修正内容】
 * - セレクタを `iframe[name="editor-canvas"]`（WordPress がエディターキャンバスに
 *   付与する name 属性）に限定し、本文中の埋め込み iframe を掴まないようにした
 * - `contentWindow.document` ではなく `contentDocument` を使用（cross-origin の
 *   iframe に対して例外を投げず null を返す）
 * - 念のため try/catch でも保護し、失敗時は document にフォールバックする
 *
 * 【再現条件を作る: エディターキャンバスの非 iframe 化】
 * Gutenberg のブロックエディタは、編集中の投稿にあるすべてのブロックが
 * apiVersion 3 以上の場合にのみキャンバスを iframe 化する（新規投稿は既定の
 * 空段落ブロック 1 つだけのため、何もしなければ常に iframe 化された状態になる）。
 * このため、素の wp-env 環境では原因の欄で説明した「本文中の埋め込みを先に掴む」
 * 状況を再現できず、テストが常に green のまま何も検出できなくなってしまう。
 *
 * この e2e 専用の mu-plugin ( tests/e2e/mu-plugins/veu-e2e-force-non-iframed-canvas.php )
 * が、apiVersion 2 のダミーブロック（VEU E2E Legacy Canvas Marker）をクライアント側に
 * 登録する。テスト冒頭でこのダミーブロックを 1 つ挿入すると上記の判定が false になり、
 * キャンバスが非 iframe 化される。以降、本文（タイトル・ブロック一覧）は iframe の
 * 外側、メインドキュメントへ直接描画されるため、本テストの操作はすべて通常の `page`
 * ロケータで行う（editorFrame のような frameLocator は使わない）。
 *
 * 【このテストの内容】
 * 上記の方法で非 iframe 化したキャンバスの本文に、クロスオリジンを指す <iframe>
 * （127.0.0.1 のポート 9 = discard サービス宛て。実サイトへの外部通信を避けつつ、
 * src が自オリジンと異なる時点でブラウザは cross-origin frame として扱うため、
 * 接続が成立しなくても再現条件は満たす）を含む Custom HTML ブロックを挿入したうえで、
 * 影響対象の 6 ブロックすべてを挿入し、
 * 1. 「このブロックでエラーが発生したためプレビューできません」相当の
 *    エラー境界表示が出ないこと
 * 2. ブラウザのコンソールに SecurityError が出ないこと
 * を確認する。
 */
import { test, expect, type Page } from '@playwright/test';
import { login } from './utils/auth';
import { runWpCli } from './utils/wp-cli';

// このスペックが作成するテスト用投稿のタイトル。
const TEST_POST_TITLE = 'Cross-origin iframe block error test (#1452)';

// エディターキャンバスの非 iframe 化を強制する e2e 専用クエリパラメータ
// （tests/e2e/mu-plugins/veu-e2e-force-non-iframed-canvas.php 参照）。
const FORCE_NON_IFRAMED_CANVAS_PARAM = 'veu_e2e_force_non_iframed_canvas';

// 上記 mu-plugin が登録するダミーブロック（apiVersion 2）のタイトル。
const LEGACY_CANVAS_MARKER_BLOCK_TITLE = 'VEU E2E Legacy Canvas Marker';

// issue #1452 で影響を受けた 6 ブロック。
// title は各ブロックの block.json の "title"（Block Inserter の検索・選択に使う）、
// blockClass はブロックの edit.js が useBlockProps に渡しているクラス名
// （挿入後、キャンバス内にブロックが描画されたことを確認するために使う）。
const TARGET_BLOCKS: ReadonlyArray< { title: string; blockClass: string } > = [
	{ title: 'Share button', blockClass: '.veu_share_button_block' },
	{ title: 'HTML Sitemap', blockClass: '.veu_sitemap_block' },
	{
		title: 'Page List Ancestor',
		blockClass: '.veu_post_list_ancestor_block',
	},
	{ title: 'Child Page Index', blockClass: '.veu_child_page_list_block' },
	{ title: 'Contact Section', blockClass: '.veu_contact_section_block' },
	{ title: 'CTA', blockClass: '.veu-cta-block-edit' },
];

// このスペックが作ったテスト用投稿だけを強制削除して初期化する。
// セットアップ失敗が flake の原因にならないよう、wp-cli の失敗は throw で表面化する。
const resetPosts = (): void => {
	let ids: string;
	try {
		ids = runWpCli( [
			'post',
			'list',
			'--post_type=post',
			`--title=${ TEST_POST_TITLE }`,
			'--post_status=any',
			'--format=ids',
		] ).trim();
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`resetPosts: failed to list "${ TEST_POST_TITLE }" via tests-cli: ${ message }`
		);
	}
	if ( ! ids ) {
		return;
	}
	try {
		runWpCli( [ 'post', 'delete', '--force', ...ids.split( /\s+/ ) ] );
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`resetPosts: failed to delete "${ TEST_POST_TITLE }" posts via tests-cli: ${ message }`
		);
	}
};

// 編集画面で Welcome guide modal が表示されたら閉じる。
const closeWelcomeModalIfPresent = async ( page: Page ): Promise< void > => {
	const modal = page.locator( '.components-modal__frame' );
	if ( await modal.isVisible() ) {
		await page.click( 'button[aria-label="Close"]' );
		// 固定スリープではなく、モーダルが実際に閉じるまで待つ。
		await modal.waitFor( { state: 'hidden', timeout: 5000 } );
	}
};

// Block Editor が操作可能になるまで待つ。ロード直後はキャンバスが iframe 化されて
// いる場合とそうでない場合の両方があり得るため、キャンバスの外側（メインドキュメント）
// に常に存在する Block Inserter ボタンの表示を待機の基準にする。
const waitForBlockEditorReady = async ( page: Page ): Promise< void > => {
	await page
		.getByRole( 'button', { name: 'Block Inserter' } )
		.waitFor( { timeout: 15000 } );
};

// Block Inserter からタイトル完全一致でブロックを挿入する。
// Block Inserter パネルはキャンバスの外側（メインドキュメント）に表示されるため、
// キャンバスが iframe 化されているかどうかに関わらずこのヘルパーは共通で使える。
// WP 6.x の Block Inserter は docked サイドバー型で、検索結果に
// "Blocks" listbox と "Block patterns" listbox の両方が現れるため、
// "Blocks" listbox 内に絞り、完全一致でタイトルを選択する。
const insertBlockByTitle = async (
	page: Page,
	title: string
): Promise< void > => {
	await page.getByRole( 'button', { name: 'Block Inserter' } ).click();
	await page.getByRole( 'searchbox', { name: 'Search' } ).fill( title );
	await page
		.getByRole( 'listbox', { name: 'Blocks' } )
		.getByRole( 'option', { name: title, exact: true } )
		.click();
	// 挿入後、Block Inserter を閉じる（docked のため自動で閉じない）。
	const closeBtn = page.getByRole( 'button', {
		name: 'Close Block Inserter',
	} );
	if ( ( await closeBtn.count() ) > 0 ) {
		await closeBtn.click();
	}
};

// ダミーブロックを挿入してキャンバスの非 iframe 化を待つ（#1452 の再現条件）。
const forceNonIframedCanvas = async ( page: Page ): Promise< void > => {
	await insertBlockByTitle( page, LEGACY_CANVAS_MARKER_BLOCK_TITLE );

	// 挿入直後は React の再描画が完了するまで一瞬 iframe が残るため、
	// editor-canvas iframe が実際に無くなるまで待ってから先へ進む。
	await expect( page.locator( 'iframe[name="editor-canvas"]' ) ).toHaveCount(
		0
	);
};

// 本文に「クロスオリジン（別ドメイン）を指す iframe」を含む Custom HTML ブロックを
// 挿入する。実サイトの埋め込み（YouTube 等）への外部通信を避けるため、到達不可能な
// ローカルの discard ポート（127.0.0.1:9）を指すだけの <iframe> を使う（ブラウザは
// 接続の成否に関わらず、src が自オリジンと異なる時点で cross-origin frame として
// 扱うため、実際に読み込めなくても本不具合の再現条件は満たす。127.0.0.1 宛てのため
// DNS 解決も外部への実通信も発生しない）。
const insertCrossOriginIframeHtmlBlock = async (
	page: Page
): Promise< void > => {
	await insertBlockByTitle( page, 'Custom HTML' );

	// Custom HTML ブロックの入力欄（PlainText。aria-label="HTML"）にタグを書き込む。
	const htmlTextbox = page.getByRole( 'textbox', { name: 'HTML' } );
	await htmlTextbox.click();
	await htmlTextbox.fill(
		'<iframe src="http://127.0.0.1:9/" width="300" height="150" title="cross-origin embed"></iframe>'
	);
};

test.describe( '編集画面：クロスオリジン iframe があるとブロックがエラー表示になる不具合 (#1452)', () => {
	test.describe.configure( { mode: 'serial' } );
	test.setTimeout( 90 * 1000 );

	test.beforeAll( () => {
		resetPosts();
	} );

	test.afterAll( () => {
		resetPosts();
	} );

	test.beforeEach( async ( { page } ) => {
		// ログイン ( id ベースのロケータを使う共通ヘルパー。utils/auth.ts 参照 )。
		await login( page );
	} );

	test( '本文にクロスオリジン iframe がある状態で対象 6 ブロックを挿入してもエラー表示にならず、コンソールに SecurityError も出ない', async ( {
		page,
	} ) => {
		// --- コンソールエラー・ページ内未捕捉例外を収集する ---
		// 元の不具合は useEffect 内の例外を React が拾いブロックをエラー表示に
		// 差し替えるものだが、環境によっては拾われずコンソールにのみ SecurityError が
		// 出るケースもあり得るため、表示確認とは別に必ずコンソールも監視する。
		const consoleErrors: string[] = [];
		page.on( 'console', ( msg ) => {
			if ( msg.type() === 'error' ) {
				consoleErrors.push( msg.text() );
			}
		} );
		page.on( 'pageerror', ( err ) => {
			consoleErrors.push( err.message );
		} );

		// --- 新規投稿を作成（キャンバス非 iframe 化用のクエリパラメータ付き） ---
		await page.goto(
			`/wp-admin/post-new.php?${ FORCE_NON_IFRAMED_CANVAS_PARAM }=1`
		);
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		// --- ダミーブロックでキャンバスを非 iframe 化（#1452 の再現条件） ---
		await forceNonIframedCanvas( page );

		// --- タイトル入力・本文にクロスオリジン iframe を追加 ---
		// キャンバスは既に非 iframe 化されているため、以降はメインドキュメントへ
		// 直接描画された要素を通常の page ロケータで操作する。
		const postTitle = page.getByLabel( 'Add title' );
		await postTitle.click();
		await postTitle.fill( TEST_POST_TITLE );

		await insertCrossOriginIframeHtmlBlock( page );

		// --- 影響対象の 6 ブロックを順に挿入し、それぞれエラー表示にならないことを確認 ---
		for ( const { title, blockClass } of TARGET_BLOCKS ) {
			await insertBlockByTitle( page, title );

			// キャンバス内に挿入したブロックが描画されるまで待つ。
			await page
				.locator( blockClass )
				.first()
				.waitFor( { timeout: 15000 } );

			// 「このブロックでエラーが発生したためプレビューできません」相当の
			// エラー境界表示が出ていないこと。
			await expect(
				page.getByText(
					'This block has encountered an error and cannot be previewed.'
				)
			).toHaveCount( 0 );
		}

		// --- ブラウザのコンソールに SecurityError が出ていないこと ---
		const securityErrors = consoleErrors.filter( ( message ) =>
			/SecurityError/i.test( message )
		);
		expect(
			securityErrors,
			`Unexpected SecurityError in console: ${ JSON.stringify(
				securityErrors
			) }`
		).toEqual( [] );
	} );
} );
