# e2e テスト ガイドライン

VK All in One Expansion Unit の e2e テスト（Playwright）を書く / メンテナンスする開発者向けのガイドです。プロジェクト固有の運用と、過去に踏んだ落とし穴をまとめてあります。

vk-agents 共通ルール（`rules/testing/e2e.md`）と合わせて参照してください。共通ルールに書かれている「ベースURLは相対パス」「ポート指定は `.wp-env.override.json`」「テストコードにはコメントを多めに」などはここでは繰り返さないので、必ず一度目を通すこと。

---

## 1. e2e テストの実行方法

### 前提

- wp-env で WordPress 環境が起動していること
- Node.js / npm 依存パッケージがインストール済みであること

テーマ・本プラグインの有効化は `playwright.config.ts` に登録した `globalSetup`（`tests/e2e/global-setup.ts`）が全 spec 実行前に一度だけ自動で保証します。tests サイトでテーマ・プラグインが未有効なまま Content-Length: 0（真っ白）になる、といった手動対応は不要です。

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

`workers` は `playwright.config.ts` で CI・ローカル問わず `1` に固定しています。一部の spec（`cta.spec.ts` の1本目など）が「サイト全体に CTA が1件も無い」といった DB 全体のグローバルな状態を前提にアサートしているため、複数ワーカーで並列実行すると他 spec が並行して作る投稿の存在そのものでその前提が崩れ、どちらが落ちるかは実行順・タイミング依存の race になります（#1439 で実機確認済み）。ローカル実行は直列化により遅くなりますが、「ローカルだけ不定期に落ちる回帰テスト」を作らないことを優先しています。

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

UI から事前条件を作るとセットアップで落ちて本質が見えなくなります。option / 投稿 / ユーザー等の初期化は `tests/e2e/utils/wp-cli.ts` の共通ヘルパー `runWpCli()` を使ってください。新しい spec を書く場合、自前で wp-cli 実行処理を実装せず、この共通ヘルパーを import してください。

```ts
import { runWpCli } from './utils/wp-cli';

// 例: vkExUnit_pagetop オプションを削除
runWpCli( [ 'option', 'delete', 'vkExUnit_pagetop' ] );
```

`runWpCli()` は内部で `docker exec` に引数を配列のまま渡します（`execFileSync` を使い、ホスト側のシェルを経由しません）。**`npx wp-env run tests-cli wp ...` を直接叩かないでください。** `wp-env run` は内部で引数をシェル経由で連結し直すため、引用符・スペース・HTML タグ（`<span style="color:red">` 等）を含む値を安全に渡せません。この問題は #1439 で実際に踏んでおり、`docker exec` + 配列引数（シェル非経由）へ置き換えて解消しています。コンテナの特定も、他プロジェクトの wp-env が同時に起動していても正しい1台だけを掴むよう実装済みです（詳細は `wp-cli.ts` の docblock を参照）。

コンテナは常に **`tests-cli`**（tests サイト）が対象です。Playwright のテスト対象は wp-env の **tests** サイト（デフォルト 8889）を向いているため、`cli` コンテナ（development サイト）で option を書き換えてもテスト側 DB には反映されません。

`wp-cli.ts` は `runWpCli()` 以外に、CTA の e2e 整備（#1439）で踏んだ落とし穴に対応する以下のヘルパーも提供しています。値の読み書きで似た問題に当たったら自作せずまずここを確認してください。

| ヘルパー | 使いどころ |
|---|---|
| `getPostMetaRaw(postId, key)` | 投稿メタの値を `[]`（行が無い）と `[""]`（空文字で存在する）を区別して読みたい時。`wp post meta get` は両者を区別せず非ゼロ終了するため使えない場面がある |
| `updatePostMetaTolerateNoop(postId, key, value)` | `wp post meta update` で書き込みたい時。サニタイズ後の値が現在値と一致すると「Failed to update custom field」で非ゼロ終了する wp-cli の仕様を、書き込み不要なだけの正常系として許容する |
| `deletePostsTolerateMissing(ids)` | 投稿をテスト後片付けで強制削除したい時。対象が既に存在しない場合の失敗（「Failed deleting post」）だけを許容し、それ以外のエラーは伝播させる |
| `runWpCliBypassingCtaSanitize(args)` | CTA 画像位置のサニタイズ迂回 mu-plugin（下記参照）を発動させた状態で wp-cli を実行したい時。「不正値が既に DB に保存されている状態」を意図的に作るための限定用途 |

`execSync` で文字列連結すると、引数のクォート崩しや空白で壊れます。共通ヘルパーを介さず自前で wp-cli を叩く場合も、必ず `execFileSync` + 引数配列で渡してください。

### テスト専用 mu-plugin（`.wp-env.json` の `env.tests.mappings`）

通常操作では作れない前提条件（例: CTA のクラシックエディタ到達、不正値が既に DB にある状態）を再現するための mu-plugin は、`tests/e2e/mu-plugins/` に置き、`.wp-env.json` の `env.tests.mappings` で **tests サイトにだけ**マウントします。

```jsonc
// .wp-env.json（抜粋）
"env": {
    "tests": {
        "mappings": {
            "wp-content/mu-plugins": "./tests/e2e/mu-plugins"
        }
    }
}
```

トップレベルの `mappings` ではなく `env.tests.mappings` を使うのがポイントです。トップレベルに置くと dev サイト（`cli` コンテナ）にも同じ mu-plugin が入ってしまい、本番配布物の健全性を確認するはずの CI のスモークテスト等が意図せずテスト専用の迂回ロジック込みの環境で走ってしまいます（#1439 で実際に指摘・修正）。

CTA のサニタイズ迂回 mu-plugin（`tests/e2e/mu-plugins/veu-e2e-bypass-cta-sanitize.php`）のように、Web リクエストへ絶対に影響させたくないロジックは、`defined('WP_CLI') && WP_CLI` に加えて専用の環境変数を発動条件にしてください。詳細な実装パターンはそのファイルと `runWpCliBypassingCtaSanitize()` を参照してください。

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
- 関連 issue（wp-cli 共通ヘルパー・テスト専用 mu-plugin・workers 固定の経緯）: [#1439](https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1439)
