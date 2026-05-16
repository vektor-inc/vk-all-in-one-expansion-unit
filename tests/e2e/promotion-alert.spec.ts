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
// option / 投稿の不存在エラーは握りつぶす（reset 用なので未存在は OK）。
const resetPromotionAlertState = (): void => {
	try {
		runWpCli( [ 'option', 'delete', 'vkExUnit_PA' ] );
	} catch ( _e ) {
		// option が無い場合は無視
	}
	try {
		// post_type_manage 投稿を全削除（強制削除でゴミ箱経由しない）。
		const ids = runWpCli( [
			'post',
			'list',
			'--post_type=post_type_manage',
			'--post_status=any',
			'--format=ids',
		] ).trim();
		if ( ids ) {
			runWpCli( [
				'post',
				'delete',
				'--force',
				...ids.split( /\s+/ ),
			] );
		}
	} catch ( _e ) {
		// 失敗してもテスト続行
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
		const isModalVisible = await page.isVisible(
			'.components-modal__frame'
		);
		if ( isModalVisible ) {
			await page.click( 'button[aria-label="Close"]' );
			await page.waitForTimeout( 300 );
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

		// --- 後片付け: 作成した CPT 設定投稿を削除 ---
		await page.goto(
			'/wp-admin/edit.php?post_type=post_type_manage'
		);
		await page.locator( '#cb-select-all-1' ).check();
		await page
			.locator( '#bulk-action-selector-top' )
			.selectOption( 'trash' );
		await page.locator( '#doaction' ).click();
	} );
} );
