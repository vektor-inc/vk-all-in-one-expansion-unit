# VK All in One Expansion Unit

## E2E Test

### テストを作成

Playwriteは操作を自動的トラッキングしてコードを出力してくれます。
以下のようにテスト対象のURLを指定して叩くと、ブラウザが起動してトラッキングを開始します。

```
npx playwright codegen "テスト対象のURL"
```

例えばWordPressのログイン画面からの動作テストを作る場合は以下のようになります。
```
npx playwright codegen "http://localhost:8889/wp-login.php"
```

### テストの実行

全てのテストの実行

```
npx playwright test
```

ブラウザは chrome だけで良い場合
```
npx playwright test --project=chromium
```

操作のスクリーンショットが見たい場合 --trace on を追加
```
npx playwright test --trace on
```

```
npx playwright test --trace on --project=chromium
```

## レポートの確認
```
npx playwright show-report
```