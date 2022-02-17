# vk-wp-unit-test-tools
## これは何？

### テーマやプラグインの PHP Fatal Error をチェック 

WordPress のサイトの各ページで PHP の Fatal Error が発生しないかをPHPUnitテストで確認するためのツールです。
通常 PHPUnit では特定のクラスや関数などのチェックを行いますが、例えば以下のようなケースは検出できません。

* 関数名を変更して、そのテストは変更したが、利用先の関数名の変更漏れ
* テストを書いてない部分の 読み込み順やメソッド名間違い、SyntaxError などによる Fatal Error
* 特定のページでしか発生しない Fatal Error

そこで、PHPUnit でテスト用のページを投稿して、各ページを巡回してエラーが発生していないかテストします。

## 使い方

### 前提条件

既に wp-env で PHPUnit が動くように設定されているリポジトリでの利用前提です。

### インストールと設定

#### インストール

```
$ composer require vektor-inc/vk-wp-unit-test-tools
```

#### 設定

PHPUnitの設定ファイル phpunit.xml などに 以下を追加

```
<directory prefix="test-" suffix=".php">./vendor/vektor-inc/vk-wp-unit-test-tools/src/tests/</directory>
```

これで、各リポジトリの package.json などで設定してある PHPUnit を走らせるコマンドを叩けば動きます。

ただし、手探りで作ったので、普通のエンジニアの方からしたらツッコミ所満載だと思います。

これおかしいんじゃね？普通はこうじゃね？というプルリクお待ちしております。