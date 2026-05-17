<?php
/**
 * Tests for the page top button (inc/pagetop-btn/pagetop-btn.php).
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Class Test_Pagetop_Btn
 */
class Test_Pagetop_Btn extends WP_UnitTestCase {

	/**
	 * Reset option between tests so cases stay independent.
	 * テスト間でオプションをクリアして独立性を保つ。
	 */
	public function tear_down() {
		delete_option( 'vkExUnit_pagetop' );
		parent::tear_down();
	}

	/**
	 * Test veu_pagetop_sanitize_image_url() with various inputs.
	 *
	 * 画像 URL サニタイザーの境界値・XSS ペイロードを網羅的にテストする。
	 * 配列形式で条件と期待値をセットにし、ループで一括検証する。
	 */
	public function test_veu_pagetop_sanitize_image_url() {

		// テストケース配列。
		$test_cases = array(
			array(
				'test_condition_name' => '空文字を渡した場合は空文字を返す',
				'input'               => '',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '配列など文字列以外を渡した場合は空文字を返す',
				'input'               => array( 'http://example.com/a.png' ),
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'null を渡した場合は空文字を返す',
				'input'               => null,
				'expected'            => '',
			),
			array(
				'test_condition_name' => '前後の空白付き png URL は trim して許可',
				'input'               => '  https://example.com/image.png  ',
				'expected'            => 'https://example.com/image.png',
			),
			array(
				'test_condition_name' => 'png URL（正常系1）はそのまま返す',
				'input'               => 'https://example.com/image.png',
				'expected'            => 'https://example.com/image.png',
			),
			array(
				'test_condition_name' => 'svg URL（正常系2）はそのまま返す',
				'input'               => 'https://example.com/path/to/icon.svg',
				'expected'            => 'https://example.com/path/to/icon.svg',
			),
			array(
				'test_condition_name' => 'jpeg URL（正常系3）はそのまま返す',
				'input'               => 'https://example.com/photo.jpeg',
				'expected'            => 'https://example.com/photo.jpeg',
			),
			array(
				'test_condition_name' => 'クエリ文字列付き png URL も拡張子判定して許可',
				'input'               => 'https://example.com/image.png?ver=1.2.3',
				'expected'            => 'https://example.com/image.png?ver=1.2.3',
			),
			array(
				'test_condition_name' => '大文字拡張子 .PNG も小文字判定で許可',
				'input'               => 'https://example.com/IMAGE.PNG',
				'expected'            => 'https://example.com/IMAGE.PNG',
			),
			array(
				'test_condition_name' => '相対パスの png URL も拡張子判定して許可',
				'input'               => '/wp-content/uploads/2026/05/icon.png',
				'expected'            => '/wp-content/uploads/2026/05/icon.png',
			),
			array(
				'test_condition_name' => 'PHP 拡張子は拡張子ホワイトリストで弾く',
				'input'               => 'https://example.com/evil.php',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '拡張子なし URL は弾く',
				'input'               => 'https://example.com/image',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'javascript: スキームは esc_url_raw で除去され空文字になる',
				'input'               => 'javascript:alert(1)//.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'CSS injection ペイロード（クォート + 括弧）は弾く',
				'input'               => 'https://example.com/a.png");}body{background:red;//',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'シングルクォートを含む値は弾く',
				'input'               => "https://example.com/a'.png",
				'expected'            => '',
			),
			array(
				'test_condition_name' => '空白を含む値は弾く（URL中央スペース）',
				'input'               => 'https://example.com/a b.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '括弧を含む値は弾く',
				'input'               => 'https://example.com/a).png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'バックスラッシュを含む値は弾く',
				'input'               => 'https://example.com/a\\.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '改行を含む値は弾く',
				'input'               => "https://example.com/a.png\nfoo",
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'HTML タグ風ペイロードは弾く（クォート/山括弧/空白いずれかでNG）',
				'input'               => '"><script>alert(1)</script>',
				'expected'            => '',
			),
			// 追加の境界値テスト（issue #1347 / PR #1345 のレビュー対応）。
			array(
				'test_condition_name' => 'data: スキームは esc_url_raw で除去され空文字になる',
				'input'               => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUg==',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'URL エンコードされたダブルクォート（%22）を含む値は弾く',
				'input'               => 'https://example.com/a%22.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'URL エンコードされたシングルクォート（%27）を含む値は弾く',
				'input'               => 'https://example.com/a%27.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'URL エンコードされた閉じ括弧（%29）を含む値は弾く',
				'input'               => 'https://example.com/a%29.png',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '多重拡張子（evil.png.php）は最後の拡張子で判定され弾く',
				'input'               => 'https://example.com/evil.png.php',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '大文字スキーム（HTTPS://）は esc_url_raw で小文字化され許可',
				'input'               => 'HTTPS://example.com/image.png',
				'expected'            => 'https://example.com/image.png',
			),
			array(
				'test_condition_name' => 'U+0080 (C1 制御文字) を含む値は弾く',
				'input'               => "https://example.com/a\xC2\x80.png",
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'U+009F (C1 制御文字) を含む値は弾く',
				'input'               => "https://example.com/a\xC2\x9F.png",
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_pagetop_sanitize_image_url( $case['input'] );
			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Test veu_pagetop_render() output for various option states.
	 *
	 * 画像未設定／設定済み／XSSペイロード混入時の出力を検証する。
	 */
	public function test_veu_pagetop_render() {

		$test_cases = array(
			array(
				'test_condition_name' => '画像未設定（デフォルト）は style 属性を出力しない',
				'options'             => array(),
				'expected_contains'   => '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>',
				'expected_not'        => array( 'style=', '--veu_page_top_button_url', 'has-image' ),
			),
			array(
				'test_condition_name' => '画像 URL（正常系1: png）が指定されたら style 属性に CSS 変数が入る',
				'options'             => array( 'image_url' => 'https://example.com/icon.png' ),
				'expected_contains'   => 'style="--veu_page_top_button_url:url(&quot;https://example.com/icon.png&quot;);"',
				'expected_not'        => array(),
			),
			array(
				'test_condition_name' => '画像 URL（正常系2: svg）が指定されたら has-image クラスが付く',
				'options'             => array( 'image_url' => 'https://example.com/icon.svg' ),
				'expected_contains'   => 'class="page_top_btn has-image"',
				'expected_not'        => array(),
			),
			array(
				'test_condition_name' => 'XSS ペイロード入り URL は sanitize されて style 属性も has-image も出ない',
				'options'             => array( 'image_url' => 'https://example.com/a.png");}body{background:red;//' ),
				'expected_contains'   => '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>',
				'expected_not'        => array( 'style=', 'background:red', 'has-image' ),
			),
			array(
				'test_condition_name' => 'PHP 拡張子の URL は sanitize されて style 属性が出ない（境界値）',
				'options'             => array( 'image_url' => 'https://example.com/evil.php' ),
				'expected_contains'   => '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>',
				'expected_not'        => array( 'evil.php', 'style=', 'has-image' ),
			),
			// 防御的プログラミングの検証: 配列以外が渡されても空配列扱いでデフォルト出力に
			// フォールバックする事を確認する（issue #1347 項目3）。
			array(
				'test_condition_name' => '境界値: 配列以外（null）を渡してもデフォルト出力にフォールバックする',
				'options'             => null,
				'expected_contains'   => '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>',
				'expected_not'        => array( 'style=', 'has-image' ),
			),
			array(
				'test_condition_name' => '境界値: 配列以外（文字列）を渡してもデフォルト出力にフォールバックする',
				'options'             => 'invalid string',
				'expected_contains'   => '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>',
				'expected_not'        => array( 'style=', 'has-image' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_pagetop_render( $case['options'] );

			// 期待される部分文字列が含まれるか。
			$this->assertStringContainsString(
				$case['expected_contains'],
				$actual,
				$case['test_condition_name']
			);

			// 含まれてはいけない文字列のチェック。
			foreach ( $case['expected_not'] as $needle ) {
				$this->assertStringNotContainsString(
					$needle,
					$actual,
					$case['test_condition_name'] . ' / NG文字列: ' . $needle
				);
			}
		}
	}

	/**
	 * Test veu_pagetop_sanitize() merges hide_mobile and image_url correctly.
	 *
	 * メイン設定画面サニタイズ関数の戻り値配列を検証する。
	 */
	public function test_veu_pagetop_sanitize() {

		$test_cases = array(
			array(
				'test_condition_name' => '正常系1: 全部入りで両方サニタイズされる（hide_mobile は bool に正規化）',
				'input'               => array(
					'hide_mobile' => 'true',
					'image_url'   => 'https://example.com/a.png',
				),
				'expected'            => array(
					'hide_mobile' => true,
					'image_url'   => 'https://example.com/a.png',
				),
			),
			array(
				'test_condition_name' => '正常系2: image_url のみ。 hide_mobile キーが無ければ image_url のみ返る',
				'input'               => array(
					'image_url' => 'https://example.com/b.svg',
				),
				'expected'            => array(
					'image_url' => 'https://example.com/b.svg',
				),
			),
			array(
				'test_condition_name' => '正常系3: hide_mobile=false 相当の値（空文字）は bool の false に正規化',
				'input'               => array(
					'hide_mobile' => '',
					'image_url'   => 'https://example.com/c.png',
				),
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => 'https://example.com/c.png',
				),
			),
			array(
				'test_condition_name' => '異常系: image_url に XSS ペイロードを渡しても空文字に正規化される',
				'input'               => array(
					'image_url' => 'https://example.com/a.png");}body{background:red;//',
				),
				'expected'            => array(
					'image_url' => '',
				),
			),
			array(
				'test_condition_name' => '境界値: 配列以外（null）は空配列を返す（image_url は空にフォールバック）',
				'input'               => null,
				'expected'            => array(
					'image_url' => '',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_pagetop_sanitize( $case['input'] );
			// 厳密比較で型まで一致しているかを確認する（bool と 'true' などの取り違えを防ぐ）。
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Test veu_pagetop_default() includes image_url key.
	 *
	 * デフォルト配列に image_url キーが追加されているか検証する。
	 */
	public function test_veu_pagetop_default() {

		$test_cases = array(
			array(
				'test_condition_name' => 'デフォルト配列に image_url キーが含まれ、初期値は空文字',
				'key'                 => 'image_url',
				'expected'            => '',
			),
			array(
				'test_condition_name' => 'デフォルト配列に hide_mobile キーが含まれ、初期値は false',
				'key'                 => 'hide_mobile',
				'expected'            => false,
			),
		);

		$defaults = veu_pagetop_default();

		foreach ( $test_cases as $case ) {
			$this->assertArrayHasKey( $case['key'], $defaults, $case['test_condition_name'] );
			$this->assertSame( $case['expected'], $defaults[ $case['key'] ], $case['test_condition_name'] );
		}
	}

	/**
	 * Test veu_pagetop_options() merges saved option with defaults.
	 *
	 * 保存済みオプションがデフォルトとマージされることを確認する。
	 */
	public function test_veu_pagetop_options() {

		$test_cases = array(
			array(
				'test_condition_name' => '正常系1: 何も保存していなければデフォルトが返る',
				'saved'               => null,
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => '',
				),
			),
			array(
				'test_condition_name' => '正常系2: image_url のみ保存している場合はマージされる',
				'saved'               => array( 'image_url' => 'https://example.com/c.png' ),
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => 'https://example.com/c.png',
				),
			),
			array(
				'test_condition_name' => '異常系: option に非配列が保存されていてもデフォルト配列にフォールバックする',
				'saved'               => 'invalid string',
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => '',
				),
			),
			array(
				'test_condition_name' => '異常系: image_url に配列が保存されていても空文字に正規化される',
				'saved'               => array(
					'hide_mobile' => false,
					'image_url'   => array( 'https://example.com/a.png' ),
				),
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => '',
				),
			),
			array(
				'test_condition_name' => '異常系: image_url に null が保存されていても空文字に正規化される',
				'saved'               => array(
					'hide_mobile' => false,
					'image_url'   => null,
				),
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => '',
				),
			),
			array(
				'test_condition_name' => '異常系: image_url に XSS ペイロード文字列が保存されていても空文字に正規化される',
				'saved'               => array(
					'hide_mobile' => false,
					'image_url'   => 'https://example.com/a.png");}body{background:red;//',
				),
				'expected'            => array(
					'hide_mobile' => false,
					'image_url'   => '',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			// クリーンアップ。
			delete_option( 'vkExUnit_pagetop' );

			if ( null !== $case['saved'] ) {
				update_option( 'vkExUnit_pagetop', $case['saved'] );
			}

			$actual = veu_pagetop_options();
			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Test veu_pagetop_partial_render() respects hide_mobile on mobile UA.
	 *
	 * カスタマイザの selective refresh パーシャルが
	 * モバイル非表示設定をフロント側 (veu_add_pagetop) と同じく
	 * 尊重して空文字を返すことを検証する。
	 * `wp_is_mobile()` は `HTTP_USER_AGENT` を見るためテスト中だけ UA を差し替える。
	 */
	public function test_veu_pagetop_partial_render_respects_hide_mobile() {
		// 元の UA を退避（必ず元に戻すため）。
		$original_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;

		$test_cases = array(
			array(
				'test_condition_name' => 'モバイル UA + hide_mobile=true: 空文字を返す（フロントと挙動一致）',
				'user_agent'          => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
				'options'             => array( 'hide_mobile' => true ),
				'expect_empty'        => true,
			),
			array(
				'test_condition_name' => 'モバイル UA + hide_mobile=false: 通常通り <a> を返す',
				'user_agent'          => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
				'options'             => array( 'hide_mobile' => false ),
				'expect_empty'        => false,
			),
			array(
				'test_condition_name' => 'デスクトップ UA + hide_mobile=true: 通常通り <a> を返す（モバイルではないので影響なし）',
				'user_agent'          => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
				'options'             => array( 'hide_mobile' => true ),
				'expect_empty'        => false,
			),
		);

		try {
			foreach ( $test_cases as $case ) {
				// UA を差し替えてからオプションを保存する。
				$_SERVER['HTTP_USER_AGENT'] = $case['user_agent'];
				update_option( 'vkExUnit_pagetop', $case['options'] );

				$actual = veu_pagetop_partial_render();

				if ( $case['expect_empty'] ) {
					$this->assertSame( '', $actual, $case['test_condition_name'] );
				} else {
					// 非モバイル or hide_mobile 無効時は `<a` を含む HTML が返る。
					$this->assertStringContainsString( '<a ', $actual, $case['test_condition_name'] );
					$this->assertStringContainsString( 'id="page_top"', $actual, $case['test_condition_name'] );
				}

				delete_option( 'vkExUnit_pagetop' );
			}
		} finally {
			// UA を元に戻す（アサーション失敗時も必ず実行）。
			if ( null === $original_ua ) {
				unset( $_SERVER['HTTP_USER_AGENT'] );
			} else {
				$_SERVER['HTTP_USER_AGENT'] = $original_ua;
			}
		}
	}
}
