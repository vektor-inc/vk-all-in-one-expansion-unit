import { test, expect } from '@playwright/test';
// const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test('CTA', async ({ page }) => {

  // login ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Username or Email Address').press('Tab');
  await page.getByLabel('Password', { exact: true } ).fill('password');
  await page.getByLabel('Password', { exact: true } ).press('Enter');

  // Activate CTA ///////////////////////////////////////////.

  await page.goto('http://localhost:8889/wp-admin/admin.php?page=vkExUnit_setting_page');
  await page.getByLabel('Call To Action').check();
  await page.getByRole('button', { name: 'Save Changes' }).click();

  // Put CTA ( Not registered ) ///////////////////////////////////////////.

  // Got to New Post
  await page.goto('http://localhost:8889/wp-admin/post-new.php');

  // 最初の投稿ダイアログを閉じる
  // ※ 環境に応じてダイアログが出たり出なかったりするので状況に応じて 有効・コメントアウトを切り替えてください ）
  // ※ ダイアログが出ている場合だけ close ボタンを押す という処理が書ける人はぜひ書いてプルリクください。
  await page.waitForTimeout(500); // ダイアログが出るまで待つ
  await page.getByRole('button', { name: 'Close dialog' }).click();

  // ブロック追加
  await page.getByRole('button', { name: 'Add block' }).click();
  // CTAブロックを検索
  await page.getByPlaceholder('Search').fill('cta');
  // CTAブロックを追加
  await page.getByRole('option', { name: 'CTA' }).click();

  // ******* CTAが登録されていないメッセージが表示されることを確認
  await expect(page.locator('.veu-cta-block-edit-alert .alert-title')).toContainText('No CTA registered.');

  // Save Check
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();
  await page.waitForTimeout(1000);
  await page.getByRole('button', { name: 'Close panel' }).click();
  await page.getByRole('region', { name: 'Editor settings' }).getByRole('button', { name: 'Post' }).click();
  await page.getByRole('button', { name: 'Move to trash' }).click();
  // 完了まで待つ（完了判定の書き方がわかったら書き換え）
  await page.waitForTimeout(1000);

  // Create New CTA ///////////////////////////////////////////.

  // Go to New CTA
  await page.goto('http://localhost:8889/wp-admin/post-new.php?post_type=cta');
  // Input CTA title
  await page.getByRole('textbox', { name: 'Add title' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).fill('Test CTA');
  await page.getByRole('textbox', { name: 'Add title' }).press('Enter');
  // Input CTA content
  await page.getByRole('document', { name: 'Empty block; start writing or type forward slash to choose a block' }).first().fill('This is Test CTA');
  // Publish CTA
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();
  // 一応少し待つ。待たないとCTAを配置するテストでプルダウンの中に Test CTA が入っていなくて選択できない事がある。
  await page.waitForTimeout(1000);
  // Cheack CTA is created
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');


  // Create New Post and Add CTA ///////////////////////////////////////////.

  await page.goto('http://localhost:8889/wp-admin/post-new.php');
  // Input Post with CTA title
  await page.getByRole('textbox', { name: 'Add title' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).fill('Post with CTA');
  // ブロック追加
  await page.getByRole('button', { name: 'Add block' }).click();
  // CTAブロックを検索
  await page.getByPlaceholder('Search').fill('cta');
  // CTAブロックを追加
  await page.getByRole('option', { name: 'CTA' }).click();

  // ******* CTAを選択するように促すメッセージが表示されることを確認
  await expect(page.locator('.veu-cta-block-edit-alert')).toContainText('Please select CTA from Setting sidebar.');

  // 作成済みのCTAを選択
  await page.locator('#veu-cta-block-select').selectOption({ label: 'Test CTA' });

  // ******* 作成したCTAが表示されることを確認
  await expect(page.locator('.veu-cta-block p')).toContainText('This is Test CTA');

  // 作成したCTAが配置された状態で保存
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();

  // 一応少し待つ。待たないとCTAを配置するテストでプルダウンの中に Test CTA が入っていなくて選択できない事がある。
  await page.waitForTimeout(1000);

  // Delete CTA ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');
  await page.locator('#cb-select-all-1').check();
  await page.locator('#bulk-action-selector-top').selectOption('trash');
  await page.locator('#doaction').click();
  // Add CTA 2 ///////////////////////////////////////////.
  // 登録済みのCTAが削除された場合のメッセージの確認用
  // CTAが存在しない状態の場合、CTAブロックは「CTA自体がない」というメッセージになるため、
  // ダミーで適当なCTAを登録しておく
  await page.locator('#wpbody-content').getByRole('link', { name: 'Add New' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).fill('Test CTA 2');
  await page.getByRole('textbox', { name: 'Add title' }).press('Enter');
  // Input CTA content
  await page.getByRole('document', { name: 'Empty block; start writing or type forward slash to choose a block' }).first().fill('This is Test CTA 2');
  // Publish CTA
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();
  // 一応少し待つ。
  await page.waitForTimeout(1000);
  // Cheack CTA is created
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');


  // Deleted CTA Test ///////////////////////////////////////////.

  await page.goto('http://localhost:8889/wp-admin/edit.php');
  await page.getByRole('link', { name: '“Post with CTA” (Edit)' }).click();
  // ******* CTAが登録されていないメッセージが表示されることを確認
  await expect(page.locator('.alert-title')).toContainText('Specified CTA does not exist.');

  // Delete "Post with CTA"
  await page.waitForTimeout(500); // wait the "Move to trash" button
  await page.getByRole('button', { name: 'Move to trash' }).click();

  // Delete "Test CTA 2" ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');
  await page.getByRole('link', { name: '“Test CTA 2” (Edit)' }).click();
  await page.waitForTimeout(500); // wait the "Move to trash" button
  await page.getByRole('button', { name: 'Move to trash' }).click();

});