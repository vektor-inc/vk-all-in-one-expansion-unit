/**
 * Promotion Disclosure - e2e テスト
 *
 * カスタム投稿タイプを作成し、メイン設定で「広告開示」の対象に
 * チェックを入れた後、その投稿タイプの新規投稿画面で
 * 広告開示の設定 UI（VK ExUnit 用 PluginSidebar 内の
 * "Promotion Disclosure Setting" パネル）が表示されることを検証する。
 *
 * WordPress 6.x のブロックエディタでは、従来の VEU 統合メタボックスは
 * `__back_compat_meta_box` フラグにより非表示となり、設定 UI は
 * 右上「VK All in One Expansion Unit」アイコンから開く PluginSidebar
 * 内のパネルへ移行している。本テストはその挙動に追従している。
 *
 * テスト前後で wp-cli を使い、テスト用 CPT 作成データと
 * `vkExUnit_PA` オプションを直接初期化する。
 */
import { test, expect } from '@playwright/test';
import { saveAdminSettings } from './utils/admin-settings';
import { login } from './utils/auth';
import { runWpCli } from './utils/wp-cli';

// テスト用 CPT 設定投稿（post_type_manage）と Promotion 設定をリセット。
// option / 投稿が未存在の場合だけ握りつぶし、それ以外の wp-cli 失敗は
// セットアップ起因の flake を防ぐため throw で表面化する。
const resetPromotionAlertState = (): void => {
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_PA' ] );
	} catch ( e ) {
		// wp-cli は未存在 option を delete しようとすると
		// "Could not delete '...' option. Does it exist?" を返す。
		// これだけは想定内として握りつぶし、それ以外は再 throw する。
		const stderr =
			e && typeof e === 'object' && 'stderr' in e
				? String( ( e as { stderr?: unknown } ).stderr ?? '' )
				: '';
		const message = e instanceof Error ? e.message : String( e );
		const haystack = `${ stderr }\n${ message }`;
		const isMissingOption =
			/Could not delete\s+'vkExUnit_PA'\s+option\.\s*Does it exist\?/i.test(
				haystack
			) ||
			( /vkExUnit_PA/.test( haystack ) &&
				/does(?:\s+not)?\s+exist/i.test( haystack ) );
		if ( ! isMissingOption ) {
			throw new Error(
				`resetPromotionAlertState: failed to delete vkExUnit_PA option via tests-cli: ${ message }`
			);
		}
	}

	// post_type_manage 投稿を全削除（強制削除でゴミ箱経由しない）。
	let ids: string;
	try {
		ids = runWpCli( [
			'post',
			'list',
			'--post_type=post_type_manage',
			'--post_status=any',
			'--format=ids',
		] ).trim();
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`resetPromotionAlertState: failed to list post_type_manage via tests-cli: ${ message }`
		);
	}
	if ( ! ids ) {
		return;
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
			`resetPromotionAlertState: failed to delete post_type_manage posts via tests-cli: ${ message }`
		);
	}
};

test.describe( 'Promotion Disclosure', () => {
	// 全テストで同じ option / CPT を書き換えるため、シリアル実行。
	test.describe.configure( { mode: 'serial' } );
	// 既定の 45 秒では足りない。1 テストの中で CPT の作成 → 設定保存 → ブロックエディタの
	// 起動と管理画面を 3 回以上読み込み、SCRIPT_DEBUG 有効 ( 非圧縮スクリプト ) の環境では
	// 一式実行の高負荷時に最初の CPT 作成だけで 40 秒前後かかった ( #1536 )。
	// 待ち方自体は直した上で、他の重い spec ( cta-image-position-xss.spec.ts 等 ) と同様に
	// 長めの上限を明示する。
	test.setTimeout( 120 * 1000 );

	test.beforeEach( async ( { page } ) => {
		// 既存の Promotion 設定をクリーンアップ。
		resetPromotionAlertState();

		// ログイン。
		await login( page );
	} );

	test.afterAll( () => {
		resetPromotionAlertState();
	} );

	test( 'CPT 作成 → 設定対象に追加 → 新規投稿の PluginSidebar に Promotion Disclosure Setting が表示される', async ( {
		page,
	} ) => {
		// --- カスタム投稿タイプ "Test Post Type" を作成 ---
		// post_type_manage は Classic Editor 画面なので Block Editor の
		// iframe 対応は不要。
		await page.goto( '/wp-admin/post-new.php?post_type=post_type_manage' );
		await page.getByLabel( 'Add title' ).fill( 'Test Post Type' );
		await page.locator( '#veu_post_type_id' ).fill( 'test-post-type' );
		// supports の "title" / "editor" を有効化。
		await page.getByText( 'title', { exact: true } ).click();
		await page.getByText( 'editor', { exact: true } ).click();
		await page
			.getByRole( 'button', { name: 'Publish', exact: true } )
			.click();
		// 公開後に編集画面 ( ?post=xxx ) へ遷移するのを待つ。
		await page.waitForURL( /post=\d+/, { waitUntil: 'domcontentloaded' } );

		// --- メイン設定で Promotion Disclosure 対象に CPT を追加 ---
		await page.goto( '/wp-admin/admin.php?page=vkExUnit_main_setting' );
		await page
			.locator( '#vkExUnit_PA' )
			.getByLabel( ' Test Post Type' )
			.check();
		// 送信直後の waitForLoadState( 'domcontentloaded' ) は、遷移が始まる前の（送信元の）
		// ページで既に満たされていて即座に返るため、保存完了の待ちにならない。
		// また保存後も同じ設定画面 ( admin.php?page=vkExUnit_main_setting ) に戻るため、
		// URL だけでは遷移の前後を区別できない。そのため保存 POST の応答を待ち、
		// その後に設定画面を開き直して、チェックが実際に保存されていることを確認する。
		await saveAdminSettings(
			page,
			page
				.locator( '#on_setting' )
				.getByRole( 'button', { name: 'Save Changes' } )
		);

		// 開き直して、保存されたチェック状態を確認。
		await page.goto( '/wp-admin/admin.php?page=vkExUnit_main_setting' );
		const cpTypeCheckbox = page
			.locator( '#vkExUnit_PA' )
			.getByLabel( ' Test Post Type' );
		await expect( cpTypeCheckbox ).toBeChecked();

		// --- CPT 新規投稿画面（Block Editor）で PluginSidebar 確認 ---
		await page.goto( '/wp-admin/post-new.php?post_type=test-post-type' );
		await page.waitForLoadState( 'domcontentloaded' );
		// Block Editor の初期化を待つ（editor-canvas iframe 出現待ち）。
		await page
			.frameLocator( '[name="editor-canvas"]' )
			.locator( '[contenteditable="true"]' )
			.first()
			.waitFor( { timeout: 60000 } );

		// Welcome guide modal が出る場合は閉じる。
		const modal = page.locator( '.components-modal__frame' );
		if ( await modal.isVisible() ) {
			await page.click( 'button[aria-label="Close"]' );
			// 固定スリープではなく、モーダルが実際に閉じるまで待つ。
			await modal.waitFor( { state: 'hidden', timeout: 5000 } );
		}

		// VK ExUnit の PluginSidebar アイコンをクリックして開く。
		await page
			.getByRole( 'button', {
				name: 'VK All in One Expansion Unit',
			} )
			.click();

		// PluginSidebar 内に "Promotion Disclosure Setting" パネルが
		// 表示されることを確認。
		await expect(
			page.getByRole( 'button', {
				name: 'Promotion Disclosure Setting',
			} )
		).toBeVisible();

		// 後片付けは afterAll の resetPromotionAlertState() に任せる。
		// UI 経由のクリーンアップは失敗しても検出できないため、
		// wp-cli ベースの確実な削除に一本化する。
	} );
} );
