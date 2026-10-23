import { test, expect } from '@playwright/test';
import { runWpCli } from './utils/wp-cli';

/**
 * PR #1325 / issue #1324 の e2e テスト.
 *
 * 投稿タイプマネージャーで作成した CPT のうち veu_taxonomy メタが空文字列で
 * 保存されているレコードがあると、PHP 8 系で foreach Warning が出る不具合の
 * 修正確認.
 *
 *  - A) フロントエンド: 当該 CPT があってもページ HTML に foreach Warning が出ない
 *  - B) wp-login.php: headers already sent エラーが発生せず、admin でログインできる
 *  - C) 配列で正しく保存されている CPT のカスタム分類は従来通り登録される（デグレなし）
 *
 * wp-cli は共通ヘルパー utils/wp-cli.ts の runWpCli を使う。このヘルパーは
 * 「このリポジトリ（worktree）を bind mount している tests-cli コンテナ」を
 * 特定するため、ポート番号（開発用 / テスト用）に依存せず、e2e の接続先である
 * tests サイト（testsPort）の DB に対して正しく書き込める。
 * HTTP のアクセス先は playwright.config.ts の baseURL（WP_BASE_URL）に任せ、
 * URL は相対パスで指定する。
 */

const wpCli = ( args: string[] ): string => runWpCli( args );

// post_type_manage を全削除して綺麗にする.
function cleanupPostTypeManageAll(): void {
  try {
    wpCli([
      'eval',
      'foreach (get_posts(array("post_type" => "post_type_manage", "post_status" => "any", "posts_per_page" => -1, "fields" => "ids")) as $id) { wp_delete_post($id, true); } echo "OK";',
    ]);
  } catch (e) {
    /* noop */
  }
}

test.describe('Post Type Manager - veu_taxonomy 空文字列でも foreach Warning が出ない (issue #1324 / PR #1325)', () => {
  test.beforeEach(() => {
    // 直前のテストの影響を排除するため CPT 設定投稿を全削除.
    cleanupPostTypeManageAll();
  });

  test.afterAll(() => {
    cleanupPostTypeManageAll();
  });

  /**
   * A. veu_taxonomy が空文字列の CPT があっても、トップページ HTML に
   *    foreach Warning が出力されない事.
   */
  test('A: veu_taxonomy が空文字列でもフロント HTML に foreach Warning が出ない', async ({ request }) => {
    // -- 空文字列メタの CPT を作成（再現条件） --.
    wpCli([
      'eval',
      '$post_id = wp_insert_post(array("post_title" => "Reimi Empty Tax CPT", "post_status" => "publish", "post_type" => "post_type_manage")); update_post_meta($post_id, "veu_post_type_id", "reimi_empty_tax"); update_post_meta($post_id, "veu_post_type_items", array("title" => "true", "editor" => "true", "custom-fields" => "true")); update_post_meta($post_id, "veu_taxonomy", ""); echo $post_id;',
    ]);

    // -- フロントトップページを取得 --.
    const res = await request.get('/');
    expect(res.status()).toBe(200);
    const body = await res.text();

    // -- HTML に foreach Warning が含まれていない事 --.
    expect(body).not.toMatch(/foreach\(\) argument must be of type array\|object, string given/);
    // -- class.post-type-manager.php からの Warning メッセージが含まれていない事 --.
    expect(body).not.toMatch(/class\.post-type-manager\.php on line \d+/);
  });

  /**
   * B. wp-login.php で headers already sent が発生せず、admin でログインできる事.
   *    Before（修正前）は本テストの page.locator('#user_login') 経由のログインが
   *    Cookie 保存失敗で詰むため、ここがログイン詰み解消の主要な観点になる.
   */
  test('B: veu_taxonomy 空文字列の CPT があっても wp-admin にログインできる', async ({ page }) => {
    // -- 空文字列メタの CPT を再作成（独立して観点を再現） --.
    wpCli([
      'eval',
      '$post_id = wp_insert_post(array("post_title" => "Reimi Empty Tax CPT 2", "post_status" => "publish", "post_type" => "post_type_manage")); update_post_meta($post_id, "veu_post_type_id", "reimi_empty_tax_2"); update_post_meta($post_id, "veu_post_type_items", array("title" => "true", "editor" => "true", "custom-fields" => "true")); update_post_meta($post_id, "veu_taxonomy", ""); echo $post_id;',
    ]);

    // -- wp-login.php → admin でログイン --.
    await page.goto('/wp-login.php');
    // ログイン画面 HTML 自体に Warning が混入していない事を確認.
    const loginHtml = await page.content();
    expect(loginHtml).not.toMatch(/foreach\(\) argument must be of type array\|object, string given/);
    expect(loginHtml).not.toMatch(/headers already sent/);

    await page.locator('#user_login').fill('admin');
    await page.locator('#user_pass').fill('password');
    await page.locator('#user_pass').press('Enter');

    // -- wp-admin に着地できる事（着地できなければ Cookie が保存できていない=ログイン詰み） --.
    await page.waitForURL(/wp-admin/, { timeout: 15000 });
    expect(page.url()).toMatch(/wp-admin/);
  });

  /**
   * C. デグレ確認: veu_taxonomy が正しく配列で保存されている CPT は
   *    従来通りカスタム分類が登録される事.
   */
  test('C: 配列で保存された veu_taxonomy はカスタム分類として登録される（デグレなし）', async () => {
    const slug = 'reimi_book';
    const taxSlug = 'reimi_book_genre';

    // -- 配列で正しく保存されている CPT を作成 --.
    // veu_taxonomy には連想配列でカスタム分類1件を含める.
    wpCli([
      'eval',
      `$post_id = wp_insert_post(array("post_title" => "Reimi Book", "post_status" => "publish", "post_type" => "post_type_manage")); update_post_meta($post_id, "veu_post_type_id", "${slug}"); update_post_meta($post_id, "veu_post_type_items", array("title" => "true", "editor" => "true", "custom-fields" => "true")); update_post_meta($post_id, "veu_taxonomy", array(array("slug" => "${taxSlug}", "label" => "Reimi Book Genre", "tag" => "", "rest_api" => "true"))); echo $post_id;`,
    ]);

    // -- カスタム分類が登録されている事を確認 --.
    const out = wpCli([
      'eval',
      `echo taxonomy_exists('${taxSlug}') ? 'YES' : 'NO';`,
    ]);
    expect(out).toContain('YES');

    // -- カスタム分類が対象 CPT に紐づいている事 --.
    const taxoboundOut = wpCli([
      'eval',
      `$obj = get_taxonomy('${taxSlug}'); echo $obj && in_array('${slug}', (array) $obj->object_type, true) ? 'BOUND' : 'UNBOUND';`,
    ]);
    // 'UNBOUND' にも 'BOUND' が部分一致してしまうため、行全体で判定する.
    expect(taxoboundOut.trim()).toBe('BOUND');
  });
});
