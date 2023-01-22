import { test, expect } from '@playwright/test';

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
  // 最初のダイアログを閉じる
  await page.getByRole('button', { name: 'Close dialog' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).click();
  await page.getByRole('textbox', { name: 'Add title' }).fill('Test CTA');
  await page.getByRole('textbox', { name: 'Add title' }).press('Enter');
  await page.getByRole('document', { name: 'Empty block; start writing or type forward slash to choose a block' }).first().fill('This is Test CTA');
  await page.getByRole('region', { name: 'Editor top bar' }).getByRole('button', { name: 'Publish' }).click();
  await page.getByRole('button', { name: 'Publish' }).nth(1).click();

  // Create New Post and Add CTA ///////////////////////////////////////////.
  await page.goto('http://localhost:8889/wp-admin/post-new.php');
  await page.getByRole('button', { name: 'Add block' }).click();
  await page.getByPlaceholder('Search').fill('cta');
  await page.getByRole('option', { name: 'CTA' }).click();
  const locator = page.locator('.veu-cta-block-edit-alert');
  await expect(locator).toContainText('Please select CTA from Setting sidebar.');
});

// npx playwright codegen "http://localhost:8889/wp-admin/post-new.php?post_type=cta" 