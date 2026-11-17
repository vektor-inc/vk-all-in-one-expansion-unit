<?php
/**
 * Class veu_css_customize
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * veu_css_customize test case.
 */
class CssCustomizeTest extends WP_UnitTestCase {

	/**
	 * カスタマイズCSSのテスト
	 */
	public function test_css_customize_get_the_css_min() {
		$tests = array(
			array(
				'option'  => 'div > h1 { color:red;   }',
				'correct' => 'div > h1{color:red;}',
			),
			array(
				'option'  => 'div > h1 {
					color:red;
					}',
				'correct' => 'div > h1{color:red;}',
			),
			array(
				'option'  => '<script></script>div > h1 {color:red;}',
				'correct' => 'div > h1{color:red;}',
			),
			// メディアクエリがある状態のテストケース
			array(
				'option'  => '@media (width > 1000px) {p { color: red   ;}}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'option'  => '@media (width > 1000px) {
					p {
						color: red;
					}
				}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'option'  => '<script></script>@media (width > 1000px) {p { color: red;}}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
			),
			// 範囲指定のメディアクエリと子セレクタが同じ CSS にある場合 ( issue #1556 )
			// Media query range syntax and the child combinator in the same CSS ( issue #1556 ).
			array(
				'test_condition_name' => 'issue #1556 の報告どおり <= と子セレクタ > が同じ CSS にある場合 => 欠けずに出力',
				'option'              => '@media (width <= 992px) {
  .global-nav-list { flex-wrap: wrap; }
  .global-nav-list > li { width: auto; }
}',
				'correct'             => '@media (width <= 992px){.global-nav-list{flex-wrap:wrap;}.global-nav-list > li{width:auto;}}',
			),
			array(
				'test_condition_name' => '範囲指定 (400px < width < 800px) と子セレクタ > の場合 => 欠けずに出力',
				'option'              => '@media (400px < width < 800px) { .a > li { color: red; } }',
				'correct'             => '@media (400px < width < 800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '範囲指定 >= と空白なしの子セレクタ a>b の場合 => 欠けずに出力',
				'option'              => '@media (width >= 992px) { a>b { color: red; } }',
				'correct'             => '@media (width >= 992px){a>b{color:red;}}',
			),
			array(
				'test_condition_name' => '空白なしの範囲指定 (400px<width<800px) と子セレクタ > の場合 => 欠けずに出力',
				'option'              => '@media (400px<width<800px) { .a > li { color: red; } }',
				'correct'             => '@media (400px<width<800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) の場合 => < と > の間が消えずに出力',
				'option'              => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'correct'             => '@media (400px<width ) and (width>800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px <width) and (width>800px) の場合 => < と > の間が消えずに出力',
				'option'              => '@media (400px <width) and (width>800px) { a { color: red; } }',
				'correct'             => '@media (400px <width) and (width>800px){a{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px<width) と子セレクタ > の場合 => 欠けずに出力',
				'option'              => '@media (400px<width) { .a > li { color: red; } }',
				'correct'             => '@media (400px<width){.a > li{color:red;}}',
			),
			// HTML コメントは中身ごと除去する
			// HTML comments are removed together with their content.
			array(
				'test_condition_name' => 'HTML コメントの後の行にルールがある場合 => コメントを除去してルールは出力',
				'option'              => '<!-- ヘッダーの調整 -->
.site-header { background: red; }',
				'correct'             => '.site-header{background:red;}',
			),
			array(
				'test_condition_name' => '閉じていない <!-- の後の行にルールがある場合 => その行の末尾まで除去してルールは出力',
				'option'              => '<!-- ヘッダーの調整
.site-header { background: red; }',
				'correct'             => '.site-header{background:red;}',
			),
			// 後方互換: <style> で囲んで貼り付けた場合はタグだけを除去し、中の CSS は残す
			// Backward compatibility: a pasted <style> wrapper is removed, keeping the CSS inside.
			array(
				'test_condition_name' => '<style> で囲んで貼り付けた場合 => タグだけ除去して CSS は出力',
				'option'              => '<style type="text/css">div > h1 { color:red; }</style>',
				'correct'             => 'div > h1{color:red;}',
			),
			// XSS: <style> 要素を抜け出す文字列が出力に残らないこと
			// XSS: nothing that escapes the <style> element may remain in the output.
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグと script を除去',
				'option'              => 'div{color:red;}</style><script>alert(1)</script>',
				'correct'             => 'div{color:red;}',
			),
			array(
				'test_condition_name' => '大文字・空白入りの </STYLE > と img タグを含む場合 => どちらも除去',
				'option'              => 'div{color:red;}</STYLE ><img src=x onerror=alert(1)>',
				'correct'             => 'div{color:red;}',
			),
			// HTML の要素名のタグは除去する。残すとブロックエディタで CSS 全体が捨てられ、前後のルールも効かなくなる
			// Tags with an HTML element name are removed. Left in place, the block editor drops the
			// whole CSS and the rules around the tag stop working.
			array(
				'test_condition_name' => 'ルールの後ろに img タグがある場合 => img タグを除去',
				'option'              => 'div{color:red;}<img src=x onerror=alert(1)>',
				'correct'             => 'div{color:red;}',
			),
			array(
				'test_condition_name' => 'ルールの間に br タグがある場合 => br を除去し、後ろのルールも出力',
				'option'              => '.a{color:red;}<br>.b{color:blue;}',
				'correct'             => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'p タグで囲まれている場合 => p タグを除去して CSS を出力',
				'option'              => '<p>.a{color:red;}</p>',
				'correct'             => '.a{color:red;}',
			),
			array(
				'test_condition_name' => '大文字の <BR> と属性付きの <P class> の場合 => 大文字小文字を区別せず除去',
				'option'              => '<P class="memo">.a{color:red;}<BR>.b{color:blue;}</P>',
				'correct'             => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => '> で閉じない </style の場合 => </ を無効化して出力',
				'option'              => 'div{color:red;}</style
.a{color:blue;}',
				'correct'             => 'div{color:red;}<\\/style.a{color:blue;}',
			),
			array(
				'test_condition_name' => 'コメントで分断した </*x*//style の場合 => コメント除去後にできる </ も無効化',
				'option'              => 'div{color:red;}</*x*//style><script>alert(1)</script>',
				'correct'             => 'div{color:red;}<\\/style>',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'veu_css_customize' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$before_option = get_option( 'vkExUnit_css_customize' );

		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_css_customize', $test_value['option'] );
			$return = veu_css_customize::css_customize_get_the_css_min();
			// print PHP_EOL;
			// print 'return    :' . $return . PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
			$condition_name = isset( $test_value['test_condition_name'] ) ? $test_value['test_condition_name'] : $test_value['option'];
			$this->assertEquals( $test_value['correct'], $return, $condition_name );
			// どのケースでも <style> 要素を抜け出す </style が出力に残らないこと
			// In every case, no </style ( which escapes the <style> element ) remains in the output.
			$this->assertStringNotContainsStringIgnoringCase( '</style', $return, $condition_name );
		} // foreach ( $tests as $key => $test_value ) {
			$before_option = update_option( 'vkExUnit_css_customize', $before_option );
	} // function test_css_customize_get_the_css_min() {

	/**
	 * Singular page css
	 */
	public function test_veu_get_the_custom_css_single() {

		// 要件と期待する結果
		$test_array = array(
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 { color:red;   }',
				'correct'    => 'div > h1{color:red;}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 {
						color:red;
						}',
				'correct'    => 'div > h1{color:red;}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '<script></script>div > h1 {color:red;}',
				'correct'    => 'div > h1{color:red;}',
			), // メディアクエリがある状態のテストケース
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '@media (width > 1000px) {p { color: red   ;}}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '@media (width > 1000px) {
					p {
						color: red;
					}
				}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '<script></script>@media (width > 1000px) {p { color: red;}}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
			// 範囲指定のメディアクエリと子セレクタが同じ CSS にある場合 ( issue #1556 )
			// Media query range syntax and the child combinator in the same CSS ( issue #1556 ).
			array(
				'test_condition_name' => 'issue #1556 の報告どおり <= と子セレクタ > が同じ CSS にある場合 => 欠けずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (width <= 992px) {
  .global-nav-list { flex-wrap: wrap; }
  .global-nav-list > li { width: auto; }
}',
				'correct'             => '@media (width <= 992px){.global-nav-list{flex-wrap:wrap;}.global-nav-list > li{width:auto;}}',
			),
			array(
				'test_condition_name' => '範囲指定 (400px < width < 800px) と子セレクタ > の場合 => 欠けずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (400px < width < 800px) { .a > li { color: red; } }',
				'correct'             => '@media (400px < width < 800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '範囲指定 >= と空白なしの子セレクタ a>b の場合 => 欠けずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (width >= 992px) { a>b { color: red; } }',
				'correct'             => '@media (width >= 992px){a>b{color:red;}}',
			),
			array(
				'test_condition_name' => '空白なしの範囲指定 (400px<width<800px) と子セレクタ > の場合 => 欠けずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (400px<width<800px) { .a > li { color: red; } }',
				'correct'             => '@media (400px<width<800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) の場合 => < と > の間が消えずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'correct'             => '@media (400px<width ) and (width>800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px <width) and (width>800px) の場合 => < と > の間が消えずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (400px <width) and (width>800px) { a { color: red; } }',
				'correct'             => '@media (400px <width) and (width>800px){a{color:red;}}',
			),
			array(
				'test_condition_name' => '(400px<width) と子セレクタ > の場合 => 欠けずに出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '@media (400px<width) { .a > li { color: red; } }',
				'correct'             => '@media (400px<width){.a > li{color:red;}}',
			),
			// HTML コメントは中身ごと除去する
			// HTML comments are removed together with their content.
			array(
				'test_condition_name' => 'HTML コメントの後の行にルールがある場合 => コメントを除去してルールは出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '<!-- ヘッダーの調整 -->
.site-header { background: red; }',
				'correct'             => '.site-header{background:red;}',
			),
			array(
				'test_condition_name' => '閉じていない <!-- の後の行にルールがある場合 => その行の末尾まで除去してルールは出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '<!-- ヘッダーの調整
.site-header { background: red; }',
				'correct'             => '.site-header{background:red;}',
			),
			// 後方互換: <style> で囲んで貼り付けた場合はタグだけを除去し、中の CSS は残す
			// Backward compatibility: a pasted <style> wrapper is removed, keeping the CSS inside.
			array(
				'test_condition_name' => '<style> で囲んで貼り付けた場合 => タグだけ除去して CSS は出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '<style type="text/css">div > h1 { color:red; }</style>',
				'correct'             => 'div > h1{color:red;}',
			),
			// XSS: <style> 要素を抜け出す文字列が出力に残らないこと
			// XSS: nothing that escapes the <style> element may remain in the output.
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグと script を除去',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}</style><script>alert(1)</script>',
				'correct'             => 'div{color:red;}',
			),
			array(
				'test_condition_name' => '大文字・空白入りの </STYLE > と img タグを含む場合 => どちらも除去',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}</STYLE ><img src=x onerror=alert(1)>',
				'correct'             => 'div{color:red;}',
			),
			// HTML の要素名のタグは除去する。残すとブロックエディタで CSS 全体が捨てられ、前後のルールも効かなくなる
			// Tags with an HTML element name are removed. Left in place, the block editor drops the
			// whole CSS and the rules around the tag stop working.
			array(
				'test_condition_name' => 'ルールの後ろに img タグがある場合 => img タグを除去',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}<img src=x onerror=alert(1)>',
				'correct'             => 'div{color:red;}',
			),
			array(
				'test_condition_name' => 'ルールの間に br タグがある場合 => br を除去し、後ろのルールも出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '.a{color:red;}<br>.b{color:blue;}',
				'correct'             => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'p タグで囲まれている場合 => p タグを除去して CSS を出力',
				'post_title'          => 'タイトル',
				'post_meta'           => '<p>.a{color:red;}</p>',
				'correct'             => '.a{color:red;}',
			),
			array(
				'test_condition_name' => '大文字の <BR> と属性付きの <P class> の場合 => 大文字小文字を区別せず除去',
				'post_title'          => 'タイトル',
				'post_meta'           => '<P class="memo">.a{color:red;}<BR>.b{color:blue;}</P>',
				'correct'             => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => '> で閉じない </style の場合 => </ を無効化して出力',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}</style
.a{color:blue;}',
				'correct'             => 'div{color:red;}<\\/style.a{color:blue;}',
			),
			array(
				'test_condition_name' => 'コメントで分断した </*x*//style の場合 => コメント除去後にできる </ も無効化',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}</*x*//style><script>alert(1)</script>',
				'correct'             => 'div{color:red;}<\\/style>',
			),
			array(
				'test_condition_name' => 'HTML エンティティで書いた &lt;/style&gt;&lt;script&gt; の場合 => デコード後に除去',
				'post_title'          => 'タイトル',
				'post_meta'           => 'div{color:red;}&lt;/style&gt;&lt;script&gt;alert(1)&lt;/script&gt;',
				'correct'             => 'div{color:red;}',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_the_custom_css_single' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			// テスト用のデータを投稿する
			$post_data = array(
				'post_title'   => $value['post_title'],
				'post_content' => $value['post_title'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
			);

			// 投稿が成功すると投稿IDが返ってくる
			$post_id = wp_insert_post( $post_data );

			// カスタムCSSをカスタムフィールドに投稿
			add_post_meta( $post_id, '_veu_custom_css', $value['post_meta'] );

			// 実際に投稿されたデータを取得する
			$post = get_post( $post_id );

			// その投稿データの場合のカスタムCSS
			$return = veu_get_the_custom_css_single( $post );

			// 返ってきた値と期待する結果が同じかどうかテスト
			$condition_name = isset( $value['test_condition_name'] ) ? $value['test_condition_name'] : $value['post_meta'];
			$this->assertEquals( $value['correct'], $return, $condition_name );
			// どのケースでも <style> 要素を抜け出す </style が出力に残らないこと
			// In every case, no </style ( which escapes the <style> element ) remains in the output.
			$this->assertStringNotContainsStringIgnoringCase( '</style', $return, $condition_name );

			// print 'return  :' . $return . PHP_EOL;
			// print 'correct :' . $value['correct'] . PHP_EOL;

			// テスト用データを消去
			wp_delete_post( $post_id, true );
		} // foreach ( $test_array as $key => $value ) {
	} // function test_veu_get_the_custom_css_single() {

	/**
	 * Editor styles injection for the block editor.
	 * ブロックエディタへのエディタスタイル注入のテスト。
	 */
	public function test_veu_css_customize_single_editor_styles() {

		// テスト条件と期待する結果
		// with_post       : エディタコンテキストに投稿を含めるか（false = サイト/ウィジェットエディタ相当）
		// custom_css      : 投稿に設定するカスタムCSS（null = カスタムCSS未設定）
		// input           : block_editor_settings_all に渡す既存の editor settings
		// expected_styles : 期待する $settings['styles']（null = 'styles' が付与されず設定は未変更）
		$test_cases = array(
			array(
				'test_condition_name' => 'ケース1: post コンテキストありのとき、圧縮済み CSS がエディタスタイルに追加される',
				'with_post'           => true,
				'custom_css'          => 'div > h1 { color:red;   }',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => 'div > h1{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース2: 既存の styles を保持し、新しい CSS はその後ろに追加される',
				'with_post'           => true,
				'custom_css'          => 'div > h1 { color:red;   }',
				'input'               => array( 'styles' => array( array( 'css' => 'body{}' ) ) ),
				'expected_styles'     => array(
					array( 'css' => 'body{}' ),
					array( 'css' => 'div > h1{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース3: post コンテキストなし（サイト/ウィジェットエディタ）のとき、設定は未変更',
				'with_post'           => false,
				'custom_css'          => null,
				'input'               => array(),
				'expected_styles'     => null,
			),
			array(
				'test_condition_name' => 'ケース5: 範囲指定 <= と子セレクタ > を含む CSS が欠けずにエディタスタイルに追加される ( issue #1556 )',
				'with_post'           => true,
				'custom_css'          => '@media (width <= 992px) { .global-nav-list > li { width: auto; } }',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '@media (width <= 992px){.global-nav-list > li{width:auto;}}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース6: </style><script> を含む CSS では style の終了タグと script が除去される',
				'with_post'           => true,
				'custom_css'          => 'div{color:red;}</style><script>alert(1)</script>',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => 'div{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース7: (400px<width ) and (width>800px) を含む CSS が欠けずにエディタスタイルに追加される',
				'with_post'           => true,
				'custom_css'          => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '@media (400px<width ) and (width>800px){.a > li{color:red;}}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース8: HTML コメントの後の行のルールがエディタスタイルに追加される',
				'with_post'           => true,
				'custom_css'          => "<!-- ヘッダーの調整 -->\n.site-header { background: red; }",
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '.site-header{background:red;}' ),
				),
			),
			// HTML の要素名のタグが styles に残ると、エディタ側の CSS 変換（PostCSS）が構文エラーで
			// CSS 全体を捨てるため、styles に渡す CSS にタグが残らないこと
			// A tag left in styles makes the editor's CSS transform ( PostCSS ) fail and drop
			// the whole CSS, so no tag may remain in the CSS passed to styles.
			array(
				'test_condition_name' => 'ケース9: ルールの後ろに img タグがある場合 => img タグを除去してエディタスタイルに追加',
				'with_post'           => true,
				'custom_css'          => 'div{color:red;}<img src=x onerror=alert(1)>',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => 'div{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース10: ルールの間に br タグがある場合 => br を除去し、後ろのルールもエディタスタイルに追加',
				'with_post'           => true,
				'custom_css'          => '.a{color:red;}<br>.b{color:blue;}',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '.a{color:red;}.b{color:blue;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース11: p タグで囲まれている場合 => p タグを除去してエディタスタイルに追加',
				'with_post'           => true,
				'custom_css'          => '<p>.a{color:red;}</p>',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '.a{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース12: 大文字の <BR> の場合 => 除去してエディタスタイルに追加',
				'with_post'           => true,
				'custom_css'          => '.a{color:red;}<BR>.b{color:blue;}',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => '.a{color:red;}.b{color:blue;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース4: カスタムCSS未設定の投稿では設定は未変更',
				'with_post'           => true,
				'custom_css'          => null,
				'input'               => array(),
				'expected_styles'     => null,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_css_customize_single_editor_styles' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_cases as $case ) {

			// テスト用の投稿とエディタコンテキストを準備する。
			$post_id = 0;
			if ( $case['with_post'] ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => 'タイトル',
						'post_content' => 'content',
						'post_status'  => 'publish',
						'post_type'    => 'post',
					)
				);
				if ( null !== $case['custom_css'] ) {
					add_post_meta( $post_id, '_veu_custom_css', $case['custom_css'] );
				}
				$context = new WP_Block_Editor_Context( array( 'post' => get_post( $post_id ) ) );
			} else {
				$context = new WP_Block_Editor_Context();
			}

			$settings = veu_css_customize_single_editor_styles( $case['input'], $context );

			if ( null === $case['expected_styles'] ) {
				// 設定は未変更のまま返る。
				$this->assertSame( $case['input'], $settings, $case['test_condition_name'] );
			} else {
				$this->assertSame( $case['expected_styles'], $settings['styles'], $case['test_condition_name'] );
			}

			// テスト用データを削除する。
			if ( $post_id ) {
				wp_delete_post( $post_id, true );
			}
		} // foreach ( $test_cases as $case ) {
	} // function test_veu_css_customize_single_editor_styles() {

	/**
	 * CSS から HTML タグを除去する処理のテスト。
	 * Tests for removing HTML tags from CSS.
	 */
	public function test_veu_strip_html_from_css() {

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > を含む場合 => そのまま残る ( issue #1556 )',
				'input'               => '@media (width <= 992px) { .a > li { width: auto; } }',
				'expected'            => '@media (width <= 992px) { .a > li { width: auto; } }',
			),
			array(
				'test_condition_name' => '空白なしの範囲指定 (400px<width<800px) と a>b の場合 => そのまま残る',
				'input'               => '@media (400px<width<800px) { a>b { color: red; } }',
				'expected'            => '@media (400px<width<800px) { a>b { color: red; } }',
			),
			array(
				'test_condition_name' => '空白なしの範囲指定 (400px<width) と (width>800px) が並ぶ場合 => そのまま残る',
				'input'               => '@media (400px<width) and (width>800px) { a { color: red; } }',
				'expected'            => '@media (400px<width) and (width>800px) { a { color: red; } }',
			),
			array(
				'test_condition_name' => '<style type="text/css"> で囲んだ場合 => タグだけ除去して中の CSS は残る',
				'input'               => '<style type="text/css">a { color: red; }</style>',
				'expected'            => 'a { color: red; }',
			),
			array(
				'test_condition_name' => '<script> 要素を含む場合 => 中身ごと除去',
				'input'               => 'a { color: red; }<SCRIPT type="text/javascript">alert(1)</script >',
				'expected'            => 'a { color: red; }',
			),
			array(
				'test_condition_name' => '属性付きの img タグと br タグを含む場合 => HTML の要素名のタグとして除去',
				'input'               => 'a { color: red; }<img src=x onerror=alert(1)><br/>',
				'expected'            => 'a { color: red; }',
			),
			array(
				'test_condition_name' => '大文字の <BR> と <P> の場合 => 大文字小文字を区別せず除去',
				'input'               => '<P>.a { color: red; }<BR>.b { color: blue; }</P >',
				'expected'            => '.a { color: red; }.b { color: blue; }',
			),
			array(
				'test_condition_name' => '<width ...> のように HTML の要素名ではない名前の場合 => タグとみなさず残る',
				'input'               => 'a{}<width foo><height/><color><bold><my-element>',
				'expected'            => 'a{}<width foo><height/><color><bold><my-element>',
			),
			array(
				'test_condition_name' => '要素名の前方一致 ( <scripting> <bx> <pointer> ) の場合 => 要素名の直後が空白・/・> ではないため残る',
				'input'               => 'a{}<scripting><bx><pointer>',
				'expected'            => 'a{}<scripting><bx><pointer>',
			),
			array(
				'test_condition_name' => '要素名の後ろで > より前に { や } がある場合 => タグとみなさず残る',
				'input'               => '@media (400px<p ) { a > b { color: red; } }',
				'expected'            => '@media (400px<p ) { a > b { color: red; } }',
			),
			array(
				'test_condition_name' => 'メディア特性名 ( height / aspect-ratio / resolution / color ) の範囲指定の場合 => タグとみなさずそのまま残る',
				'input'               => '@media (300px<height) and (aspect-ratio>1/1) and (1dppx<resolution) and (8<color) { a>b { color: red; } }',
				'expected'            => '@media (300px<height) and (aspect-ratio>1/1) and (1dppx<resolution) and (8<color) { a>b { color: red; } }',
			),
			array(
				'test_condition_name' => '子セレクタ a > b と a>b の場合 => そのまま残る',
				'input'               => 'a > b { color: red; } a>b { color: blue; }',
				'expected'            => 'a > b { color: red; } a>b { color: blue; }',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) の場合 => タグとみなさずそのまま残る',
				'input'               => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'expected'            => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
			),
			array(
				'test_condition_name' => '(400px <width) and (width>800px) の場合 => タグとみなさずそのまま残る',
				'input'               => '@media (400px <width) and (width>800px) { a { color: red; } }',
				'expected'            => '@media (400px <width) and (width>800px) { a { color: red; } }',
			),
			array(
				'test_condition_name' => '複数行の HTML コメントを含む場合 => 中身ごと除去',
				'input'               => "<!-- メモ\nメモ2 -->\n.a { color: red; }",
				'expected'            => "\n.a { color: red; }",
			),
			array(
				'test_condition_name' => '閉じていない <!-- の場合 => その行の末尾まで除去し、次の行は残す',
				'input'               => "<!-- メモ\n.a { color: red; }",
				'expected'            => "\n.a { color: red; }",
			),
			array(
				'test_condition_name' => '大文字の </STYLE> と </SCRIPT> の単独タグの場合 => タグを除去',
				'input'               => '<STYLE>a { color: red; }</STYLE></SCRIPT>',
				'expected'            => 'a { color: red; }',
			),
			array(
				'test_condition_name' => '> で閉じない </style の場合 => タグとみなさず残る ( 無効化は別処理 )',
				'input'               => "a { color: red; }</style\n.b { color: blue; }",
				'expected'            => "a { color: red; }</style\n.b { color: blue; }",
			),
			array(
				'test_condition_name' => '文字列以外が渡された場合 => 空文字',
				'input'               => null,
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$this->assertSame( $case['expected'], veu_strip_html_from_css( $case['input'] ), $case['test_condition_name'] );
		}
	}

	/**
	 * CSS を <style> 要素内へ出力できる形にする処理のテスト。
	 * Tests for making CSS safe to output inside a <style> element.
	 */
	public function test_veu_escape_css_for_style_element() {

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > の場合 => そのまま',
				'input'               => '@media (width <= 992px){.a > li{width:auto;}}',
				'expected'            => '@media (width <= 992px){.a > li{width:auto;}}',
			),
			array(
				'test_condition_name' => '</style> を含む場合 => </ を <\/ に置き換える',
				'input'               => 'a{}</style><script>',
				'expected'            => 'a{}<\\/style><script>',
			),
			array(
				'test_condition_name' => '大文字の </STYLE と文字列内の </ を含む場合 => すべての </ を置き換える',
				'input'               => 'a{content:"</b>"}</STYLE ',
				'expected'            => 'a{content:"<\\/b>"}<\\/STYLE ',
			),
			array(
				'test_condition_name' => '置き換え済みの <\/ の場合 => 変わらない（2 回通しても同じ）',
				'input'               => 'a{}<\\/style>',
				'expected'            => 'a{}<\\/style>',
			),
			array(
				'test_condition_name' => '文字列以外が渡された場合 => 空文字',
				'input'               => false,
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$this->assertSame( $case['expected'], veu_escape_css_for_style_element( $case['input'] ), $case['test_condition_name'] );
		}
	}

	/**
	 * 投稿ごとのカスタムCSSの入力サニタイズのテスト（保存時・出力時に共通で通る処理）。
	 * Tests for the per-post Custom CSS input sanitizer ( shared by saving and output ).
	 */
	public function test_veu_sanitize_custom_css_input() {

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > を含む場合 => 改行・空白を含めてそのまま残る ( issue #1556 )',
				'input'               => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
				'expected'            => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
			),
			array(
				'test_condition_name' => '範囲指定 (400px < width < 800px) と a>b の場合 => そのまま残る',
				'input'               => '@media (400px < width < 800px) { a>b { color: red; } }',
				'expected'            => '@media (400px < width < 800px) { a>b { color: red; } }',
			),
			array(
				'test_condition_name' => '<style> で囲んだ場合 => タグだけ除去して CSS は残る',
				'input'               => '<style>div { color: red; }</style>',
				'expected'            => 'div { color: red; }',
			),
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグと script を除去',
				'input'               => 'div { color: red; }</style><script>alert(1)</script>',
				'expected'            => 'div { color: red; }',
			),
			array(
				'test_condition_name' => '> で閉じない </Style の場合 => </ を無効化',
				'input'               => "div { color: red; }</Style\n.a { color: blue; }",
				'expected'            => "div { color: red; }<\\/Style\n.a { color: blue; }",
			),
			array(
				'test_condition_name' => '文字列以外が渡された場合 => 空文字',
				'input'               => array( 'div{}' ),
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_sanitize_custom_css_input( $case['input'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
			// 保存時と出力時の両方で通るため、2 回通しても結果が変わらないこと
			// It runs both on save and on output, so a second pass must not change the result.
			$this->assertSame( $actual, veu_sanitize_custom_css_input( $actual ), $case['test_condition_name'] . '（2 回目）' );
		}
	}

	/**
	 * 投稿ごとのカスタムCSSのフロント出力のテスト。
	 * Tests for the front-end output of the per-post Custom CSS.
	 */
	public function test_veu_insert_custom_css() {

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > を含む場合 => 欠けずに style 要素内へ出力 ( issue #1556 )',
				'custom_css'          => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
				'expected_contains'   => '@media (width <= 992px){.global-nav-list > li{width:auto;}}</style>',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) を含む場合 => 欠けずに style 要素内へ出力',
				'custom_css'          => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'expected_contains'   => '@media (400px<width ) and (width>800px){.a > li{color:red;}}</style>',
			),
			array(
				'test_condition_name' => 'HTML コメントの後の行にルールがある場合 => コメントを除去してルールを出力',
				'custom_css'          => "<!-- ヘッダーの調整 -->\n.site-header { background: red; }",
				'expected_contains'   => '*/.site-header{background:red;}</style>',
			),
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグは出力の末尾の 1 つだけ',
				'custom_css'          => 'div{color:red;}</style><script>alert(1)</script>',
				'expected_contains'   => 'div{color:red;}</style>',
			),
			array(
				'test_condition_name' => '大文字の </STYLE と改行入りの > と script を含む場合 => style の終了タグは出力の末尾の 1 つだけ',
				'custom_css'          => "div{color:red;}</STYLE\n><script>alert(1)</script>",
				'expected_contains'   => 'div{color:red;}</style>',
			),
		);

		foreach ( $test_cases as $case ) {
			// テスト用の投稿を作り、その個別ページへ移動する
			// Create a test post and go to its singular page.
			$post_id = self::factory()->post->create( array( 'post_status' => 'publish' ) );
			update_post_meta( $post_id, '_veu_custom_css', $case['custom_css'] );
			$this->go_to( get_permalink( $post_id ) );

			// 出力を取得する
			// Capture the output.
			ob_start();
			veu_insert_custom_css();
			$actual = ob_get_clean();

			// CSS が欠けずに出力され、style の終了タグは出力の末尾の 1 つだけであること
			// The CSS is output intact, and the only style end tag is the one at the end.
			$this->assertStringContainsString( $case['expected_contains'], $actual, $case['test_condition_name'] );
			$this->assertSame( 1, substr_count( strtolower( $actual ), '</style' ), $case['test_condition_name'] );
			$this->assertStringNotContainsStringIgnoringCase( '<script', $actual, $case['test_condition_name'] );

			// テスト用データを削除する
			// Delete the test data.
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * 全体の CSS カスタマイズのフロント出力のテスト。
	 * Tests for the front-end output of the site-wide CSS Customize.
	 */
	public function test_css_customize_push_css() {

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > を含む場合 => 欠けずに style 要素内へ出力 ( issue #1556 )',
				'option'              => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
				'expected_contains'   => '@media (width <= 992px){.global-nav-list > li{width:auto;}}',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) を含む場合 => 欠けずに style 要素内へ出力',
				'option'              => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'expected_contains'   => '@media (400px<width ) and (width>800px){.a > li{color:red;}}',
			),
			array(
				'test_condition_name' => 'HTML コメントの後の行にルールがある場合 => コメントを除去してルールを出力',
				'option'              => "<!-- ヘッダーの調整 -->\n.site-header { background: red; }",
				'expected_contains'   => '*/.site-header{background:red;}/*',
			),
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグは出力の末尾の 1 つだけ',
				'option'              => 'div{color:red;}</style><script>alert(1)</script>',
				'expected_contains'   => 'div{color:red;}',
			),
			array(
				'test_condition_name' => '<style> で囲んで貼り付けた場合 => style の終了タグは出力の末尾の 1 つだけ',
				'option'              => '<style>div{color:red;}</style>',
				'expected_contains'   => 'div{color:red;}',
			),
		);

		$before_option = get_option( 'vkExUnit_css_customize' );

		foreach ( $test_cases as $case ) {
			update_option( 'vkExUnit_css_customize', $case['option'] );

			// 出力を取得する
			// Capture the output.
			ob_start();
			veu_css_customize::css_customize_push_css();
			$actual = ob_get_clean();

			// CSS が欠けずに出力され、style の終了タグは出力の末尾の 1 つだけであること
			// The CSS is output intact, and the only style end tag is the one at the end.
			$this->assertStringContainsString( $case['expected_contains'], $actual, $case['test_condition_name'] );
			$this->assertSame( 1, substr_count( strtolower( $actual ), '</style' ), $case['test_condition_name'] );
			$this->assertStringNotContainsStringIgnoringCase( '<script', $actual, $case['test_condition_name'] );
		}

		update_option( 'vkExUnit_css_customize', $before_option );
	}
}
