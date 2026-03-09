# PHPUnit　テストの書き方について

## テスト関数名は基本的に、test_テスト対象関数・メソッド名 とする

どの関数、あるいはメソッドのテストなのかをわかりやすくするために、
test_テスト対象関数・メソッド名 としてください。

例）

関数 aaa() のテストの場合は test_aaa()
NameClass::aaa() のテストの場合も test_aaa()
※クラスのメソッドの場合、クラス単位でテストファイル・テストクラスが分かれるので違うクラスで同じメソッド名があろうが関係ない

NG例）

test_aaa_cases()

aaa() という関数のテストなのか aaa_cases() という関数のテストなのかわかりにくくなるためNGとする。
そもそもいろんなケースの配列でテストする事をデフォルトとするので、"cases" は不要

## 条件と期待値をセットで配列に入れてループしながら処理する

条件毎にテストメソッドをわけると、一覧性が低く、どの条件までテストしてあるのか把握するのが難しい。
そのため、テスト条件と期待する結果を配列で登録し、その配列をループしながらテストを実行する形式で書いてください。
これにより、配列だけを見ればどういう条件でどういう結果を返す仕様なのか把握できると共に、
条件と結果の組み合わせも後から簡単に増やす事もできます。

例）

function_name() という関数あるいはメソッドのテストをするケース

```
function test_function_name(){

	// テスト用の投稿（id=1）を作成 or 作成するメソッドを読み込み

	// テストの配列
	$test_cases = array(
		array(
			'test_condition_name'     => 'トップページ で option が apple で 個別指定が pen の場合 => pen',
			'conditions'    => array(
				'options' => array(
					'test_option_name1' => 'apple',
				),
				'post_id' => 1,
				'post_meta' => array(
					'test_post_meta_name' => 'pen',
				),
			),
			'target_url' => home_url( '/' ) . '?p=1',
			'expected' => 'pen',
		),
		array(
			'test_condition_name'     => 'トップページ で option が apple で個別指定なしの場合 => apple',
			'conditions'    => array(
				'options' => array(
					'test_option_name1' => 'apple',
				),
				'post_id' => 1,
				'post_meta' => array(
					'test_post_meta_name' => '',
				),
			),
			'target_url' => home_url( '/' ) . '?p=1',
			'expected' => 'apple',
		),
	);

	foreach ( $test_cases as $case ) {
		// オプション値を設定
		if ( isset( $case['conditions']['options'] ) && is_array( $case['conditions']['options'] ) ) {
			foreach($case['conditions']['options'] as $option_name => $option_value){
				update_option( $option_name, $option_value );
			}
		}
		// カスタムフィールドを設定
		if ( ! empty( $case['conditions']['post_id'] ) && $case['conditions']['post_id'] ){
			if ( isset( $case['conditions']['post_meta'] ) && is_array( $case['conditions']['post_meta'] ) ){
				foreach( $case['conditions']['post_meta'] as $meta_name => $meta_value ){
					update_post_meta( $case['conditions']['post_id'], $meta_name,$meta_value );
				}
			}
		}

		// テストURLに移動
		$this->go_to( $case['target_url'] );

		// テスト関数実行
		$actual = function_name();

		// 期待値テスト
		$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );

		// オプション値を削除
		if ( isset( $case['conditions']['options'] ) && is_array( $case['conditions']['options'] ) ) {
			foreach($case['conditions']['options'] as $option_name => $option_value){
				delete_option( $option_name );
			}
		}

		// カスタムフィールドを削除
		if ( ! empty( $case['conditions']['post_id'] ) && $case['conditions']['post_id'] ){
			if ( isset( $case['conditions']['post_meta'] ) && is_array( $case['conditions']['post_meta'] ) ){
				foreach( $case['conditions']['post_meta'] as $meta_name => $meta_value ){
					delete_post_meta( $case['conditions']['post_id'], $meta_name );
				}
			}
		}
	}
}
```

## その他のルール

### expected にはメソッド（callable）を割り当てない

各条件とその返り値の期待値を一覧できるように配列で登録しているのに、
`expected` をメソッドにしたら何が期待値なのかわかりにくくなってしまいます。
`'expected' =>` の値は callable を使用せず、リテラルや変数（条件に渡した変数の値を期待値に含む場合など）で直書きしてください。

```php
// NG
'expected' => array( $this, 'get_expected_value' ),

// OK（リテラル）
'expected' => true,
'expected' => 'published',
'expected' => 123,
'expected' => array( 'key' => 'value' ),
'expected' => null,

// OK（条件に渡した変数をそのまま期待値に使う場合）
'conditions' => array( 'post_title' => $item['title'] ),
'expected'   => 'タイトル : ' . $item['title'],
```
