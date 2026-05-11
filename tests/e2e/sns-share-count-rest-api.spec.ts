import { test, expect, request } from '@playwright/test';

/**
 * PR #1328 / issue #1299 の確認テスト
 *
 * SNS シェア数取得用 REST API（はてな・Facebook）の URL 検証バグ修正の確認。
 *
 * 修正前は `inc/sns/function-sns-btns.php` で `strpos(...) < 0` を使用していたが、
 * `strpos()` は needle が見つからないとき false を返し、PHP では `false < 0` は false と評価される。
 * このため URL 検証が常にスキップされ、外部 URL でも 200/503 が返ってしまっていた。
 *
 * 修正後は `=== false` で比較しており、自サイト以外の URL は 403 で正しく拒否される。
 *
 * 注意: 本テストは REST API 動作確認のみで UI 操作は行わない。
 *       Facebook 側は fbAccessToken 未設定環境では URL 検証通過後に 503 が返るため、
 *       「自サイト URL → 200」の確認ははてな側のみで検証する。
 */

// REST API のベース URL は環境変数優先、未指定なら wp-env のデフォルトを使用。
const baseURL = process.env.WP_BASE_URL || 'http://localhost:3465';

test.describe('PR #1328: SNS シェア数取得 REST API の URL 検証', () => {
	test('はてな: 自サイト URL を渡すと 200 が返る', async () => {
		// 環境ごとに baseRequestContext を生成
		const apiContext = await request.newContext({ baseURL });

		// パーマリンク設定の影響を避けるため ?rest_route= 形式でアクセスする。
		// linkurl は site_url（baseURL の origin 部分）を含む URL を渡す。
		const linkurl = encodeURIComponent(`${baseURL}/?p=1`);
		const res = await apiContext.get(
			`/?rest_route=/vk_ex_unit/v1/hatena_entry/${linkurl}`
		);

		// URL 検証を通過し、外部 API（はてな）からカウント取得が試みられて 200 が返るはず。
		expect(res.status()).toBe(200);

		await apiContext.dispose();
	});

	test('はてな: 外部 URL（example.com）を渡すと 403 が返る', async () => {
		const apiContext = await request.newContext({ baseURL });

		// 自サイト以外の URL を渡す。修正前はバグで 200 が返っていた。
		const linkurl = encodeURIComponent('https://example.com/');
		const res = await apiContext.get(
			`/?rest_route=/vk_ex_unit/v1/hatena_entry/${linkurl}`
		);

		// 修正後は URL 検証で弾かれ、外部 API には飛ばずに 403 が返る。
		expect(res.status()).toBe(403);

		await apiContext.dispose();
	});

	test('Facebook: 外部 URL（example.com）を渡すと 403 が返る', async () => {
		const apiContext = await request.newContext({ baseURL });

		// Facebook 側も同様に修正前は URL 検証がスキップされていた。
		// 自サイト URL を渡した場合は fbAccessToken 未設定で 503 が返るため、
		// 純粋に URL 検証ロジックを確認する観点では「外部 URL → 403」のみで十分。
		const linkurl = encodeURIComponent('https://example.com/');
		const res = await apiContext.get(
			`/?rest_route=/vk_ex_unit/v1/facebook_entry/${linkurl}`
		);

		// 修正後は URL 検証で弾かれ、403 が返る。
		expect(res.status()).toBe(403);

		await apiContext.dispose();
	});
});
