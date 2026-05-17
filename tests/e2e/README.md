# e2e テスト ガイドライン

VK All in One Expansion Unit の e2e テスト（Playwright）を書く / メンテナンスする開発者向けのガイドです。プロジェクト固有の運用と、過去に踏んだ落とし穴をまとめてあります。

vk-agents 共通ルール（`rules/testing/e2e.md`）と合わせて参照してください。共通ルールに書かれている「ベースURLは相対パス」「ポート指定は `.wp-env.override.json`」「テストコードにはコメントを多めに」などはここでは繰り返さないので、必ず一度目を通すこと。

---

## 1. e2e テストの実行方法

### 前提

- wp-env で WordPress 環境が起動していること
- Node.js / npm 依存パッケージがインストール済みであること

### 環境の準備

```bash
# 依存パッケージのインストール（初回 or package.json 更新時）
npm install

# wp-env を起動
npx wp-env start
```

並列で別タスクが wp-env を動かしている場合は、ポート衝突を避けるため worktree ルートに `.wp-env.override.json` を置いて `port` / `testsPort` をずらしてください（直接 `.wp-env.json` を書き換えないこと）。

```json
{
    "port": 9108,
    "testsPort": 9109
}
```

ベース URL は `playwright.config.ts` の `baseURL` か `WP_BASE_URL` 環境変数で切り替えられます。ポートを変えた場合は実行時に `WP_BASE_URL` を渡すと安全です。

```bash
WP_BASE_URL=http://localhost:9109 npx playwright test
```

### 基本コマンド

```bash
# 全テスト実行（ヘッドレス）
npx playwright test

# UI モードで実行（テストを選択しながら確認できる）
npx playwright test --ui

# 単一ファイルのみ実行
npx playwright test tests/e2e/pagetop-btn-image.spec.ts

# 単一テストのみ実行（タイトル部分一致）
npx playwright test -g 'デフォルト（画像未設定）'

# 直近の HTML レポートを開く
npx playwright show-report
```

---

## 2. 待機戦略のベストプラクティス

### `waitForLoadState('networkidle')` は使わない

Playwright 公式が非推奨にしています（「Avoid waiting for `networkidle`」）。WordPress 管理画面は heartbeat API や非同期トラッキング等で常に通信が走るため、`networkidle` は不安定な待機になりやすく、CI で flaky な失敗を生みます。

「画面上で何が起きてほしいか」を起点に、その状態を表す要素を `waitFor()` / `expect(...).toBeVisible()` で待ってください。

### 代替パターン

| 場面 | 推奨の待機 |
|---|---|
| ログイン直後の管理画面 | `await page.locator('#wpadminbar').waitFor()` |
| 設定保存後の通知 | `await page.locator('.notice-success').waitFor()` |
| 任意の要素の出現 | `await expect(locator).toBeVisible()` |
| URL 遷移 | `await page.waitForURL(/wp-admin\//)`（正規表現で寛容に） |

### NG / OK の例

ログイン直後:

```ts
// NG: 通信が止まるのを待つだけで、wp-admin が描画されたかは保証されない
await page.locator('#wp-submit').click();
await page.waitForLoadState('networkidle');

// OK: 管理バーが出るまで待つ。i18n に依存しない id ベース
await page.locator('#wp-submit').click();
await page.waitForURL(/wp-admin\//);
await page.locator('#wpadminbar').waitFor();
```

設定保存後:

```ts
// NG: networkidle では「保存できた」状態かが不明
await page.locator('#submit').click();
await page.waitForLoadState('networkidle');

// OK: 成功通知が表示されるのを待つ
await page.locator('#submit').click();
await page.locator('.notice-success').waitFor();
```

---

## 3. Mobile UA / `wp_is_mobile()` 検証

`wp_is_mobile()` の挙動を e2e で検証する場合、**UA 文字列だけを差し替える方式はデスクトップ判定されて失敗します**。

### 問題

WordPress Core の `wp_is_mobile()` は Client Hints の `Sec-CH-UA-Mobile` ヘッダーを最優先で見ます。Playwright で UA 文字列だけ差し替えても、Chromium は `Sec-CH-UA-Mobile: ?0`（デスクトップ）を送り続けるため、`wp_is_mobile()` は false を返します。

### NG: UA 文字列のみ差し替え

```ts
// 動かない。Sec-CH-UA-Mobile: ?0 が送られて wp_is_mobile() が false になる
const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) ...',
});
```

### OK: `devices` プリセットを使う

```ts
import { test, expect, devices } from '@playwright/test';

test.use({ ...devices['iPhone 12'] });

test('wp_is_mobile() が true として扱われる UI が出る', async ({ page }) => {
    await page.goto('/');
    // ...
});
```

`devices['iPhone 12']` には `isMobile: true` が含まれており、これにより `Sec-CH-UA-Mobile: ?1` が自動で送られます。`wp_is_mobile()` も期待通り true を返します。

ファイル全体ではなく `test.describe` ブロック内だけで切り替えたい場合も `test.use({ ...devices['iPhone 12'] })` を `describe` の中に書けば OK です。

### 参考

- 関連 issue: [#1349](https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1349)
- 知見の元になった PR: [#1345](https://github.com/vektor-inc/vk-all-in-one-expansion-unit/pull/1345)

---

## 4. その他 best practices

### i18n 耐性のあるセレクタを使う

WordPress 管理画面は言語設定でラベル文字列が変わります。テキストではなく **id / 安定的に付与される CSS クラス** をセレクタに使ってください。

```ts
// NG: 日本語環境で "Username or Email Address" は "ユーザー名またはメールアドレス" に変わる
await page.getByLabel('Username or Email Address').fill('admin');

// OK: id は Core で安定して付与される
await page.locator('#user_login').fill('admin');
```

### state 初期化は wp-cli 経由で

UI から事前条件を作るとセットアップで落ちて本質が見えなくなります。option / 投稿 / ユーザー等の初期化は wp-cli を `execFileSync` で叩いてください（シェル解釈を経由しないため、JSON にクォートや空白が含まれていても安全に渡せます）。

コンテナは必ず **`tests-cli`** を指定してください。Playwright のテスト対象は wp-env の **tests** サイト（デフォルト 8889）を向いているため、`cli` コンテナ（development サイト）で option を書き換えてもテスト側 DB には反映されません。

```ts
import { execFileSync } from 'child_process';

const runWpCli = ( args: string[] ): string =>
    execFileSync(
        'npx',
        [ 'wp-env', 'run', 'tests-cli', 'wp', ...args ],
        { encoding: 'utf-8', stdio: [ 'ignore', 'pipe', 'pipe' ] }
    );

// 例: vkExUnit_pagetop オプションを削除
runWpCli( [ 'option', 'delete', 'vkExUnit_pagetop' ] );
```

`execSync` で文字列連結すると、引数のクォート崩しや空白で壊れます。必ず `execFileSync` + 引数配列で渡してください。

### URL アサートは正規表現で寛容に

```ts
// NG: クエリパラメーターが付くと落ちる
await page.waitForURL('/wp-admin/index.php');

// OK: パスの一部だけ一致を要求
await page.waitForURL(/wp-admin\//);
```

### 例外を握りつぶさない

`try/catch` で例外を完全に握りつぶすと、wp-env が落ちている等の本質的な障害も「スキップ」になって気付けません。**握りつぶすケースは「ここでこのメッセージが返ることが想定内」と確証が持てる範囲だけ**にしてください（`pagetop-btn-image.spec.ts` の `resetPagetopOption` を参照）。

### 固定 sleep は使わない

`page.waitForTimeout(5000)` 等の固定待機は flaky の温床です。要素 / URL / 状態を待つ API（`waitFor` / `toBeVisible` / `waitForURL`）に置き換えてください。

---

## 5. 参考リンク

- Playwright 公式ベストプラクティス: <https://playwright.dev/docs/best-practices>
- Playwright devices プリセット: <https://playwright.dev/docs/emulation>
- wp-env コマンドリファレンス: <https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/>
- vk-agents 共通ルール（e2e）: `rules/testing/e2e.md`
- 関連 issue: [#1349](https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1349)
- 関連 PR: [#1345](https://github.com/vektor-inc/vk-all-in-one-expansion-unit/pull/1345)
