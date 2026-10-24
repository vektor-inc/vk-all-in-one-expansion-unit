/**
 * WordPress 管理画面の設定保存に使う共通処理。
 */
import { type Locator, type Page } from '@playwright/test';

/**
 * 設定保存ボタンを押し、POST 応答の本文受信が完了するまで待つ。
 *
 * VK ExUnit のメイン設定画面は応答ヘッダー送信後に option を更新するため、
 * waitForResponse() の解決だけでは保存完了を保証できない。
 *
 * @param page Playwright の Page.
 * @param submitButton 保存処理を開始するボタン.
 */
export const saveAdminSettings = async (
	page: Page,
	submitButton: Locator
): Promise<void> => {
	const saveResponsePromise = page.waitForResponse(
		( response ) =>
			response.request().method() === 'POST' &&
			/\/wp-admin\/(options|admin)\.php$/.test(
				new URL( response.url() ).pathname
			)
	);

	const [ , saveResponse ] = await Promise.all( [
		submitButton.click(),
		saveResponsePromise,
	] );
	const loadError = await saveResponse.finished();
	if ( loadError || saveResponse.status() >= 400 ) {
		throw new Error(
			`saveAdminSettings(): 保存に失敗しました ( HTTP ${ saveResponse.status() }${
				loadError ? `, ${ loadError.message }` : ''
			} )`
		);
	}
};
