import { test, expect } from '@playwright/test';

test('Promotion Notation', async ({ page }) => {

	// login ///////////////////////////////////////////.
	await page.goto('http://localhost:8889/wp-login.php');
	await page.getByLabel('Username or Email Address').fill('admin');
	await page.getByLabel('Username or Email Address').press('Tab');
	await page.getByLabel('Password', { exact: true }).fill('password');
	await page.getByLabel('Password', { exact: true }).press('Enter');

	// Create Test Post Type  ///////////////////////////////////////////.

	await page.goto('http://localhost:8889/wp-admin/post-new.php?post_type=post_type_manage');

	await page.getByLabel('Add title').fill('Test Post Type');
	await page.locator('#veu_post_type_id').fill('test-post-type');
	await page.getByText('title', { exact: true }).click();
	await page.getByText('editor', { exact: true }).click();
	await page.getByRole('button', { name: 'Publish', exact: true }).click();

	// Alert Setting  ///////////////////////////////////////////.

	await page.goto('http://localhost:8889/wp-admin/admin.php?page=vkExUnit_main_setting');

	// テスト対象の投稿タイプにチェック
	await page.locator('#vkExUnit_PA').getByLabel(' Test Post Type').check();
	await page.locator('#on_setting').getByRole('button', { name: 'Save Changes' }).click();

	// Create New Test Post  ///////////////////////////////////////////.

	await page.goto('http://localhost:8889/wp-admin/post-new.php?post_type=test-post-type');

	// 設定メタボックスがあるかどうか
	await expect(page.locator('.veu_display_promotion_alert .veu_metabox_section_title')).toContainText('Promotion Notation Setting');

	// Delete CTA ///////////////////////////////////////////.
	await page.goto('http://localhost:8889/wp-admin/edit.php?post_type=post_type_manage');
	await page.locator('#cb-select-all-1').check();
	await page.locator('#bulk-action-selector-top').selectOption('trash');
	await page.locator('#doaction').click();
});