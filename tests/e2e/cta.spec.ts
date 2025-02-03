import { test, expect } from '@playwright/test';
// const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test('CTA', async ({ page }) => {

  // login ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Username or Email Address').press('Tab');
  await page.getByLabel('Password', { exact: true } ).fill('password');
  await page.getByLabel('Password', { exact: true } ).press('Enter');

  // Put CTA ( Not registered ) ///////////////////////////////////////////.

  // Got to New Post
  await page.goto('http://localhost:8889/wp-admin/post-new.php');

  // 最初の投稿ダイアログを閉じる
  await page.waitForTimeout( 1000 );
  // Check if the modal is visible
  const isModalVisible = await page.isVisible( '.components-modal__frame' );
  // If the modal is visible, click the close button
  if ( isModalVisible ) {
	  await page.click( 'button[aria-label="Close"]' );
  }

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
  await page.waitForTimeout(500); // これがないと次の goto が反応しない事がある

  // Delete "Test CTA 2" ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');
  await page.getByRole('link', { name: '“Test CTA 2” (Edit)' }).click();
  await page.waitForTimeout(500); // wait the "Move to trash" button
  await page.getByRole('button', { name: 'Move to trash' }).click();

});

test('CTA with Allowed Iframe', async ({ page }) => {
  // ログイン
  await page.goto('http://localhost:8889/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Password', { exact: true }).fill('password');
  await page.getByLabel('Password', { exact: true }).press('Enter');

  // 新しいCTA作成
  await page.goto('http://localhost:8889/wp-admin/post-new.php?post_type=cta');
  await page.getByRole('textbox', { name: 'Add title' }).fill('Allowed Iframe CTA');
  await page.getByRole('textbox', { name: 'Add title' }).press('Enter');

  // Googleマップのiframeを追加
  const allowed_iframe = `<iframe src="https://www.google.com/maps/embed?pb=!1m0!3m2!1sen!2sus!4v1609459543842!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>`;
  await page.getByRole('document', { name: 'Empty block; start writing or type forward slash to choose a block' }).first().fill(allowed_iframe);

  // CTAを公開
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();
  await page.waitForTimeout(1000);

  // CTA作成を確認
  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');
  await expect(page.locator('table.wp-list-table')).toContainText('Allowed Iframe CTA');

  // 新規投稿を作成し、CTAブロックを追加
  await page.goto('http://localhost:8889/wp-admin/post-new.php');
  await page.getByRole('textbox', { name: 'Add title' }).fill('Post with Allowed Iframe');
  await page.getByRole('button', { name: 'Add block' }).click();
  await page.getByPlaceholder('Search').fill('cta');
  await page.getByRole('option', { name: 'CTA' }).click();

  // CTAを選択
  await page.locator('#veu-cta-block-select').selectOption({ label: 'Allowed Iframe CTA' });

  // CTAが表示されることを確認
  await expect(page.locator('.veu-cta-block')).toContainText('Allowed Iframe CTA');

  // 投稿を公開
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();
  await page.waitForTimeout(1000);

  // フロントエンドで投稿を確認
  const postURL = await page.url();
  await page.goto(postURL);

  // 許可されたiframeが表示されるかチェック
  const iframeLocator = page.frameLocator('iframe[src*="https://www.google.com/maps/embed"]');
  await expect(iframeLocator.locator('body')).toBeVisible();  

  // 許可されていない iframe は存在しないことを確認
  await expect(page.locator('iframe:not([src*="https://www.google.com/maps/embed"])')).toHaveCount(0);

  // テスト完了後に削除
  await page.goto('http://localhost:8889/wp-admin/edit.php');
  await page.getByRole('link', { name: '“Post with Allowed Iframe” (Edit)' }).click();
  await page.getByRole('button', { name: 'Move to trash' }).click();
  await page.waitForTimeout(500);

  await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=cta');
  await page.getByRole('link', { name: '“Allowed Iframe CTA” (Edit)' }).click();
  await page.getByRole('button', { name: 'Move to trash' }).click();
});
