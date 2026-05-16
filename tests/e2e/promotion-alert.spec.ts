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

	test.beforeEach( async ( { page } ) => {
		// 既存の Promotion 設定をクリーンアップ。
		resetPromotionAlertState();

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
		await page.waitForURL( /post=\d+/ );

		// --- メイン設定で Promotion Disclosure 対象に CPT を追加 ---
		await page.goto( '/wp-admin/admin.php?page=vkExUnit_main_setting' );
		await page
			.locator( '#vkExUnit_PA' )
			.getByLabel( ' Test Post Type' )
			.check();
		await page
			.locator( '#on_setting' )
			.getByRole( 'button', { name: 'Save Changes' } )
			.click();
		await page.waitForLoadState( 'domcontentloaded' );

		// チェック保存後、もう一度開いて check 状態を確認。
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
			.waitFor( { timeout: 15000 } );

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
