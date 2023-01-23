import { test, expect } from '@playwright/test';
// const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test('CTA', async ({ page }) => {
  // login ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Username or Email Address').press('Tab');
  await page.getByLabel('Password').fill('password');
  await page.getByLabel('Password').press('Enter');

  // Activate CTA ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/admin.php?page=vkExUnit_setting_page');
  await page.getByRole('checkbox', { name: 'Automatic Eye Catch insert Call To Action' }).check();
  await page.getByRole('button', { name: 'Save Changes' }).click();

  // Create New CTA ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/post-new.php?post_type=cta');

  // 最初のダイアログを閉じる（ WorkFlow 上以外はダイアログが出ないのでローカルでは状況に応じてコメントアウト ）
//   await page.getByRole('button', { name: 'Close dialog' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).fill('Test CTA');
  await page.getByRole('textbox', { name: 'Add title' }).press('Enter');
  await page.getByRole('document', { name: 'Empty block; start writing or type forward slash to choose a block' }).first().fill('This is Test CTA');
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();

  // 一応少し待つ。待たないとCTAを配置するテストでプルダウンの中に Test CTA が入っていなくて選択できない事がある。
  await page.waitForTimeout(1000);
  // CTAが出来ているか一応確認
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');

  // Create New Post and Add CTA ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/post-new.php');
  // ブロック追加
  await page.getByRole('button', { name: 'Add block' }).click();
  // CTAブロックを検索
  await page.getByPlaceholder('Search').fill('cta');
  // CTAブロックを追加
  await page.getByRole('option', { name: 'CTA' }).click();

  // *** CTAを選択するように促すメッセージが表示されることを確認
  await expect(page.locator('.veu-cta-block-edit-alert')).toContainText('Please select CTA from Setting sidebar.');

  // 作成済みのCTAを選択
  await page.locator('#veu-cta-block-select').selectOption({ label: 'Test CTA' });

  // *** 作成したCTAが表示されることを確認
  await expect(page.locator('.veu-cta-block p')).toContainText('This is Test CTA');

});
