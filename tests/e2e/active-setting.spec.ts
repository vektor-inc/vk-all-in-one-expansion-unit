import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test('Active Setting first save respects selections', async ({ page }) => {
  const wpPath = process.env.WP_PATH || '/var/www/html';
  const wpCli = process.env.WP_CLI || 'wp';
  let canUseCli = true;

  try {
    execSync(`${wpCli} option delete vkExUnit_common_options --path=${wpPath}`, { stdio: 'ignore' });
    execSync(`${wpCli} option delete vkExUnit_enable_widgets --path=${wpPath}`, { stdio: 'ignore' });
  } catch (error) {
    canUseCli = false;
  }

  if (!canUseCli) {
    test.skip(true, 'wp-cli not available');
  }

  // login
  await page.goto('/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Username or Email Address').press('Tab');
  await page.getByLabel('Password', { exact: true }).fill('password');
  await page.getByLabel('Password', { exact: true }).press('Enter');

  // Go to Active Setting page
  await page.goto('/wp-admin/admin.php?page=vkExUnit_setting_page');

  const metaCheckbox = page.locator('#checkbox_active_metaDescription');
  await expect(metaCheckbox).toBeVisible();
  await metaCheckbox.setChecked(false);

  const widgetCheckbox = page.locator('#vew_widget_enable_input_post_list');
  await expect(widgetCheckbox).toBeVisible();
  await widgetCheckbox.setChecked(true);

  await page.locator('#submit').click();
  // Settings API ( form action="options.php" ) は保存後に元の管理画面へ
  // リダイレクトする。下の expect(...).not.toBeChecked() / toBeChecked() の
  // 自動 retry が、リダイレクト後にチェックボックスが再描画され、
  // 設定値が反映されるところまでを待ってくれるため、ここで
  // waitForLoadState('networkidle') を挟む必要はない。
  // networkidle は heartbeat 等で安定しないため使わない
  // （tests/e2e/README.md 参照）。

  const metaCheckboxAfter = page.locator('#checkbox_active_metaDescription');
  const widgetCheckboxAfter = page.locator('#vew_widget_enable_input_post_list');

  await expect(metaCheckboxAfter).not.toBeChecked();
  await expect(widgetCheckboxAfter).toBeChecked();
});
