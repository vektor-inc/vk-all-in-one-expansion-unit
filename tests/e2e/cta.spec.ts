/**
 * CTA - e2e テスト
 *
 * Call to Action 機能の主要シナリオを検証する:
 *
 * 1. CTA 未登録の状態で投稿に CTA ブロックを挿入すると
 *    「No CTA registered.」というアラートが出る
 * 2. CTA 投稿を 1 件作成（Test CTA）
 * 3. 通常投稿に CTA ブロックを追加し、Test CTA を選択すると
 *    本文がプレビュー表示される
 * 4. CTA を一括削除して別の CTA を 1 件作っておく
 * 5. 既存の "Post with CTA" を再オープンすると
 *    「Specified CTA does not exist.」エラーが表示される
 *
 * WordPress 6.x のブロックエディタは編集領域が
 * `editor-canvas` iframe 内に描画されるため、本文・タイトル等の
 * 操作はすべて `editorFrame(page)` 経由で行う。
 * ブロック追加ボタンの aria-label は WP の更新により
 * "Add block" → "Block Inserter" に変わっている。
 */
import { test, expect, type Page, type FrameLocator } from '@playwright/test';
import { execFileSync } from 'child_process';

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'password';

// wp-env の tests-cli コンテナ経由で wp-cli を実行するヘルパー。
// e2e は testsPort (デフォルト 8889) のテスト用 WordPress を使うため、
// データ初期化も tests-cli コンテナで行う必要がある。
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

// Block Editor 本文の iframe (editor-canvas) ロケータ。
const editorFrame = ( page: Page ): FrameLocator =>
	page.frameLocator( '[name="editor-canvas"]' );

// 投稿 / CTA 投稿をすべて強制削除して初期化する。
// テスト用の Test CTA / Post with CTA 等の残骸を取り除く。
// セットアップ失敗が flake の原因にならないよう、wp-cli の失敗は throw で表面化する。
// （wp-env が立っていない・コンテナ取得不能などの致命的な状況は早期に検出したい）
const resetPosts = (): void => {
	for ( const postType of [ 'post', 'cta' ] ) {
		let ids: string;
		try {
			ids = runWpCli( [
				'post',
				'list',
				`--post_type=${ postType }`,
				'--post_status=any',
				'--format=ids',
			] ).trim();
		} catch ( e ) {
			const message = e instanceof Error ? e.message : String( e );
			throw new Error(
				`resetPosts: failed to list ${ postType } via tests-cli: ${ message }`
			);
		}
		if ( ! ids ) {
			continue;
		}
		try {
			runWpCli( [
				'post',
				'delete',
				'--force',
				...ids.split( /\s+/ ),
			] );
		} catch ( e ) {
			const message = e instanceof Error ? e.message : String( e );
			throw new Error(
				`resetPosts: failed to delete ${ postType } posts via tests-cli: ${ message }`
			);
		}
	}
};

// 編集画面で Welcome guide modal が表示されたら閉じる。
const closeWelcomeModalIfPresent = async ( page: Page ): Promise<void> => {
	const modal = page.locator( '.components-modal__frame' );
	if ( await modal.isVisible() ) {
		await page.click( 'button[aria-label="Close"]' );
		// 固定スリープではなく、モーダルが実際に閉じるまで待つ。
		await modal.waitFor( { state: 'hidden', timeout: 5000 } );
	}
};

// Block Editor の編集領域 (iframe) が初期化されるまで待つ。
const waitForBlockEditorReady = async ( page: Page ): Promise<void> => {
	await editorFrame( page )
		.locator( '[contenteditable="true"]' )
		.first()
		.waitFor( { timeout: 15000 } );
};

// CTA ブロックを Block Inserter から挿入する。
// WP 6.x の Block Inserter は docked サイドバー型で、検索結果に
// "Blocks" listbox と "Block patterns" listbox の両方が現れるため、
// "Blocks" listbox 内に絞り、完全一致 "CTA" を選択する。
const insertCtaBlock = async ( page: Page ): Promise<void> => {
	await page.getByRole( 'button', { name: 'Block Inserter' } ).click();
	await page.getByRole( 'searchbox', { name: 'Search' } ).fill( 'cta' );
	await page
		.getByRole( 'listbox', { name: 'Blocks' } )
		.getByRole( 'option', { name: 'CTA', exact: true } )
		.click();
	// 挿入後、Block Inserter を閉じる（docked のため自動で閉じない）。
	const closeBtn = page.getByRole( 'button', {
		name: 'Close Block Inserter',
	} );
	if ( ( await closeBtn.count() ) > 0 ) {
		await closeBtn.click();
	}
};

// 投稿を公開する。Block Editor の Publish ボタン → 確認パネルの Publish。
// WP の更新でボタン構成が変わっても極力動作するよう、複数の locator を試す。
const publishPost = async ( page: Page ): Promise<void> => {
	// 1) Top bar の Publish ボタン（公開パネルを開く）。
	await page
		.getByRole( 'region', { name: 'Editor top bar' } )
		.getByRole( 'button', { name: 'Publish', exact: true } )
		.click();

	// 2) 公開パネル内の確定 Publish ボタンを探す。
	//    role=region の名前は WP のバージョンで異なるため、
	//    まずは "Editor publish" 領域内、見つからなければページ全体から
	//    最後に現れた Publish ボタンを使う。
	const publishRegion = page.getByRole( 'region', {
		name: 'Editor publish',
	} );
	let confirmBtn = publishRegion.getByRole( 'button', {
		name: 'Publish',
		exact: true,
	} );
	if ( ( await confirmBtn.count() ) === 0 ) {
		// fallback: ページ全体の Publish ボタン (top bar 以外)
		confirmBtn = page.getByRole( 'button', {
			name: 'Publish',
			exact: true,
		} );
	}
	// 公開パネルが完全に表示されてからクリックする（固定スリープ廃止）。
	const target = confirmBtn.last();
	await target.waitFor( { state: 'visible', timeout: 5000 } );
	await target.click();

	// 公開完了を UI 状態で待つ。Block Editor は公開後に top bar の
	// Publish ボタンが "Saved" / "Update" 表示に切替わるか、"Post published"
	// スナックバーが表示されるため、いずれかを待機する。
	const snackbar = page.locator( '.components-snackbar' ).filter( {
		hasText: /Published|published/i,
	} );
	const savedBtn = page
		.getByRole( 'region', { name: 'Editor top bar' } )
		.getByRole( 'button', { name: /^(Saved|Save|Update)$/, exact: false } );
	await Promise.race( [
		snackbar.first().waitFor( { state: 'visible', timeout: 10000 } ),
		savedBtn.first().waitFor( { state: 'visible', timeout: 10000 } ),
	] ).catch( () => {
		// どちらも掴めなくても致命ではないため握りつぶす（後続の goto で
		// ページ遷移すれば自然に確定する）。
	} );
};

test.describe( 'CTA', () => {
	test.describe.configure( { mode: 'serial' } );
	test.setTimeout( 90 * 1000 );

	test.beforeAll( () => {
		resetPosts();
	} );

	test.afterAll( () => {
		resetPosts();
	} );

	test.beforeEach( async ( { page } ) => {
		// ログイン。
		await page.goto( '/wp-login.php' );
		await page.getByLabel( 'Username or Email Address' ).fill( ADMIN_USER );
		await page
			.getByLabel( 'Password', { exact: true } )
			.fill( ADMIN_PASS );
		await page
			.getByLabel( 'Password', { exact: true } )
			.press( 'Enter' );
		await page.waitForURL( /wp-admin\// );
	} );

	test( 'CTA 未登録 → ブロック追加で No CTA registered メッセージ', async ( {
		page,
	} ) => {
		// --- 通常投稿で CTA ブロックを追加（CTA 未登録の状態） ---
		await page.goto( '/wp-admin/post-new.php' );
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		await insertCtaBlock( page );

		// CTA が登録されていないメッセージ（ブロック本文は iframe 内）。
		await expect(
			editorFrame( page ).locator(
				'.veu-cta-block-edit-alert .alert-title'
			)
		).toContainText( 'No CTA registered.' );
	} );

	test( 'CTA 投稿を作成 → 通常投稿で選択 → 本文プレビュー / 後で削除すると Specified CTA does not exist', async ( {
		page,
	} ) => {
		// --- CTA 投稿 "Test CTA" を作成 ---
		await page.goto( '/wp-admin/post-new.php?post_type=cta' );
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		// タイトルと本文は iframe 内。
		const ctaTitle = editorFrame( page ).getByLabel( 'Add title' );
		await ctaTitle.click();
		await ctaTitle.fill( 'Test CTA' );

		// 本文プレースホルダー "Type / to choose a block" をクリック → タイプ。
		// 最新の Block Editor では空の本文は textbox role を持たず、
		// 「Add default block」ボタンとして表示されるためそれにフォーカスする。
		await editorFrame( page )
			.getByRole( 'button', { name: 'Add default block' } )
			.click();
		await page.keyboard.type( 'This is Test CTA' );

		// 公開（Editor top bar の Publish ボタンクリック → 公開パネルが開く）。
		await publishPost( page );

		// --- 通常投稿で CTA ブロックを追加し Test CTA を選択 ---
		await page.goto( '/wp-admin/post-new.php' );
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		const postTitle = editorFrame( page ).getByLabel( 'Add title' );
		await postTitle.click();
		await postTitle.fill( 'Post with CTA' );

		await insertCtaBlock( page );

		// CTA を選択するように促すメッセージ確認。
		await expect(
			editorFrame( page ).locator( '.veu-cta-block-edit-alert' )
		).toContainText( 'Please select CTA from Setting sidebar.' );

		// CTA ブロックを選択状態にする（Inspector を Block tab に切替え可能にするため）。
		await editorFrame( page ).locator( '.veu-cta-block-edit' ).click();

		// 右サイドバーの "Block" tab に切替え。CTA SelectControl は
		// InspectorControls 内のため、ブロック選択中に "Block" tab を開かないと
		// レンダリングされない。
		await page
			.getByRole( 'region', { name: 'Editor settings' } )
			.getByRole( 'tab', { name: 'Block' } )
			.click();

		// セレクトボックスから Test CTA を選択（メインフレーム側）。
		await page
			.locator( '#veu-cta-block-select' )
			.selectOption( { label: 'Test CTA' } );

		// 選択後、本文プレビューに本文文字列が現れること（プレビューは iframe 内の ServerSideRender）。
		await expect(
			editorFrame( page ).locator( '.veu-cta-block' )
		).toContainText( 'This is Test CTA' );

		// 投稿を公開。
		await publishPost( page );

		// --- 既存 CTA をすべてゴミ箱へ ---
		await page.goto( '/wp-admin/edit.php?post_type=cta' );
		await page.locator( '#cb-select-all-1' ).check();
		await page
			.locator( '#bulk-action-selector-top' )
			.selectOption( 'trash' );
		await page.locator( '#doaction' ).click();

		// --- 「CTA がまったく無い状態」を避けるためダミー CTA を 1 件作成 ---
		// CTA が完全に 0 件だと CTA ブロック側のアラートが
		// 「No CTA registered.」に切り替わってしまうため、別ケースを再現する。
		await page.goto( '/wp-admin/post-new.php?post_type=cta' );
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		const cta2Title = editorFrame( page ).getByLabel( 'Add title' );
		await cta2Title.click();
		await cta2Title.fill( 'Test CTA 2' );
		await editorFrame( page )
			.getByRole( 'button', { name: 'Add default block' } )
			.click();
		await page.keyboard.type( 'This is Test CTA 2' );

		await publishPost( page );

		// --- 削除済み CTA を参照している投稿を再オープン ---
		await page.goto( '/wp-admin/edit.php' );
		// アクセシブルネームに含まれる引用符は WordPress / ロケールにより
		// "…" / "..." / "&quot;" など揺らぐため、タイトル部分のみで部分一致させる。
		await page
			.getByRole( 'link', { name: /Post with CTA/i } )
			.first()
			.click();
		await waitForBlockEditorReady( page );
		await closeWelcomeModalIfPresent( page );

		// 指定 CTA が存在しないメッセージ確認。
		await expect(
			editorFrame( page ).locator( '.alert-title' )
		).toContainText( 'Specified CTA does not exist.' );
	} );
} );
