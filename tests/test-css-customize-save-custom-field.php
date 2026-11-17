<?php
/**
 * Class CssCustomizeSaveCustomFieldTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * VEU_Metabox_CSS_Customize::save_custom_field() のテスト
 *
 * 主に以下の動作を検証する ( issue #1471 で追加した権限チェック・unslash 処理 )：
 * - edit_post 権限が無いユーザーからの保存は拒否されること
 * - edit_post 権限があるユーザーからの保存は許可され、CSS がサニタイズされて保存されること
 * - $_POST の値が二重に unslash されず、本物のバックスラッシュが 1 本だけ残ること
 * - nonce が不正な場合は保存されないこと
 *
 * Tests for VEU_Metabox_CSS_Customize::save_custom_field().
 *
 * Mainly verifies the following behavior ( the permission check / unslash handling
 * added in issue #1471 ):
 * - Saving is rejected for a user without the edit_post capability.
 * - Saving is allowed for a user with the edit_post capability, and the CSS is
 *   sanitized before being saved.
 * - The $_POST value is not double-unslashed; a genuine backslash survives as one.
 * - Saving is rejected when the nonce is invalid.
 */

// VEU_Metabox_CSS_Customize は本体側で admin_menu フック内でのみ require されるため
// （エディタ画面でのタイミング調整が理由）、admin_menu が発火しない PHPUnit 環境では
// 未定義のままになる。親クラス VEU_Metabox は既に読み込み済みの前提で、テスト側から明示的に読み込む。
// VEU_Metabox_CSS_Customize is only required inside the admin_menu hook in the plugin itself
// ( for load-timing reasons on the editor screen ), so it stays undefined in the PHPUnit
// environment where admin_menu never fires. Require it explicitly from the test, assuming the
// parent class VEU_Metabox is already loaded.
if ( ! class_exists( 'VEU_Metabox_CSS_Customize' ) ) {
	require_once dirname( __DIR__ ) . '/inc/css-customize/class-veu-metabox-css-customize.php';
}

class CssCustomizeSaveCustomFieldTest extends WP_UnitTestCase {

	/**
	 * テストごとにログインユーザー状態をリセットする（未ログイン状態に戻す）。
	 * Reset the logged-in user state after each test ( back to logged out ).
	 */
	protected function tearDown(): void {
		parent::tearDown();
		wp_set_current_user( 0 );
		$_POST = array();
	}

	/**
	 * テスト用投稿を作成する
	 * Create a test post.
	 *
	 * @return int 作成した投稿 ID / Created post ID.
	 */
	protected function create_test_post() {
		return wp_insert_post(
			array(
				'post_title'  => 'CSS Customize Test Post',
				'post_status' => 'publish',
				'post_type'   => 'post',
			)
		);
	}

	/**
	 * 管理者ユーザーを作成しログイン状態にする。
	 * save_custom_field() は edit_post 権限を要求するため、多くのテストで必要になる。
	 *
	 * Create an administrator and switch the current user to it.
	 * save_custom_field() requires edit_post capability, so most tests need this.
	 *
	 * @return int 作成した管理者ユーザーの ID / Created administrator user ID.
	 */
	protected function login_as_administrator() {
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );
		return $admin_id;
	}

	/**
	 * $_POST に正規の nonce と CSS 値をセットするヘルパー
	 * Helper that sets a valid nonce and CSS value in $_POST.
	 *
	 * @param VEU_Metabox_CSS_Customize $metabox テスト対象のメタボックスインスタンス / Metabox instance under test.
	 * @param string                    $value   保存する CSS 値 / CSS value to save.
	 */
	protected function set_post_data( $metabox, $value ) {
		$cf_name = $metabox->args['cf_name'];
		// nonce_action は protected のため、VEU_Metabox::__construct() と同じ組み立てルール
		// ( 'veu_metabox_' . cf_name ) をテスト側で再現する。
		// nonce_action is protected, so reproduce the same construction rule used in
		// VEU_Metabox::__construct() ( 'veu_metabox_' . cf_name ) on the test side.
		$nonce_action = 'veu_metabox_' . $cf_name;
		$nonce_key    = 'noncename__' . $cf_name;

		$_POST[ $nonce_key ] = wp_create_nonce( $nonce_action );
		$_POST[ $cf_name ]   = $value;
	}

	/**
	 * test_save_custom_field_requires_edit_post_capability
	 *
	 * edit_post 権限の有無で保存の可否が変わることを検証する。
	 * Verify that whether saving is allowed depends on the edit_post capability.
	 */
	public function test_save_custom_field_requires_edit_post_capability() {

		$test_cases = array(
			array(
				'test_condition_name' => '権限の無いユーザー（購読者）の場合 => 保存されない',
				'role'                => 'subscriber',
				'expected_saved'      => false,
			),
			array(
				'test_condition_name' => '権限のあるユーザー（管理者）の場合 => 保存される',
				'role'                => 'administrator',
				'expected_saved'      => true,
			),
		);

		foreach ( $test_cases as $case ) {
			// 前周に set_post_data() でセットした nonce / CSS 値が $_POST に残ったままだと、
			// 次の create_test_post()（wp_insert_post() -> save_post 発火）の時点で古い nonce が
			// 有効なまま複数の VEU_Metabox_CSS_Customize インスタンス（ファイル末尾のグローバル
			// インスタンス含む）の save_custom_field() が新しい投稿に対して発火してしまい、
			// テストがケースの実行順序に依存してしまう。ループの先頭で必ずクリアする。
			// If the nonce / CSS value set by set_post_data() in the previous iteration is still
			// in $_POST, the next create_test_post() ( wp_insert_post() -> save_post ) fires with
			// that stale nonce still valid, so save_custom_field() on multiple
			// VEU_Metabox_CSS_Customize instances ( including the file's global instance ) runs
			// against the new post. That makes the test order-dependent, so clear $_POST at the
			// top of every iteration.
			$_POST = array();

			$post_id = $this->create_test_post();
			$metabox = new VEU_Metabox_CSS_Customize();

			$user_id = self::factory()->user->create( array( 'role' => $case['role'] ) );
			wp_set_current_user( $user_id );

			$this->set_post_data( $metabox, 'div { color: red; }' );
			$metabox->save_custom_field( $post_id );

			$actual = get_post_meta( $post_id, '_veu_custom_css', true );

			if ( $case['expected_saved'] ) {
				// veu_sanitize_custom_css_input() は HTML タグ除去・trim のみを行い、
				// 空白の圧縮は行わない（圧縮は表示側の veu_get_the_custom_css_single() の役割）。
				// veu_sanitize_custom_css_input() only strips tags and trims; it does not
				// collapse whitespace ( that is done by veu_get_the_custom_css_single() on output ).
				$this->assertSame( 'div { color: red; }', $actual, $case['test_condition_name'] );
			} else {
				$this->assertEmpty( $actual, $case['test_condition_name'] );
			}

			delete_post_meta( $post_id, '_veu_custom_css' );
			wp_delete_post( $post_id, true );
			// 使用後は必ず片付ける（親テストの do_save() と同じ考え方）。
			// Always clean up after use ( same idea as do_save() in the parent test ).
			$_POST = array();
		}
	}

	/**
	 * test_save_custom_field_allows_author_of_own_post
	 *
	 * edit_others_posts を持たない author ロールでも、自分自身の投稿であれば
	 * 保存できることを検証する。edit_post capability チェックを採用したことで、
	 * 自分の投稿を編集できる author / contributor 等にリグレッションが無いことの確認
	 * ( issue #1471 のレビュー指摘 )。
	 *
	 * Verify that an author role ( which lacks edit_others_posts ) can still save
	 * custom CSS on their own post. Confirms adopting the edit_post capability check
	 * does not regress authors / contributors editing their own posts
	 * ( issue #1471 review feedback ).
	 */
	public function test_save_custom_field_allows_author_of_own_post() {

		$author_id = self::factory()->user->create( array( 'role' => 'author' ) );
		wp_set_current_user( $author_id );

		// 前のテストの tearDown() が $_POST を空にしてくれていることに依存しないよう、
		// wp_insert_post()（save_post 発火）より前に明示的にクリアする。
		// Do not rely on the previous test's tearDown() having emptied $_POST; clear it
		// explicitly before wp_insert_post() ( which fires save_post ).
		$_POST = array();

		$post_id = wp_insert_post(
			array(
				'post_title'  => 'Own Post',
				'post_status' => 'publish',
				'post_type'   => 'post',
				'post_author' => $author_id,
			)
		);

		$metabox = new VEU_Metabox_CSS_Customize();
		$this->set_post_data( $metabox, 'div { color: red; }' );
		$metabox->save_custom_field( $post_id );

		$this->assertSame( 'div { color: red; }', get_post_meta( $post_id, '_veu_custom_css', true ), '自分の投稿であれば author ロールでも保存できる' );

		delete_post_meta( $post_id, '_veu_custom_css' );
		wp_delete_post( $post_id, true );
		$_POST = array();
	}

	/**
	 * test_save_custom_field
	 *
	 * 寄稿者が保存した CSS について、範囲指定の < > や子セレクタ > が欠けずに保存され、
	 * <style> 要素を抜け出す </style や script は保存されないことを検証する ( issue #1556 )。
	 * Verify that CSS saved by a contributor keeps range-syntax < > and child combinator >
	 * intact, while </style ( which escapes the <style> element ) and script are not saved
	 * ( issue #1556 ).
	 */
	public function test_save_custom_field() {

		// PCRE の上限（JIT スタック・バックトラック）を超える長さの文字列。大きな SVG のデータ URI を想定する
		// A string longer than the PCRE limits ( JIT stack / backtracking ), like a large SVG data URI.
		$long = str_repeat( 'x', 200000 );

		$test_cases = array(
			array(
				'test_condition_name' => '範囲指定 <= と子セレクタ > を含む場合 => そのまま保存される',
				'input'               => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
				'expected'            => "@media (width <= 992px) {\n  .global-nav-list > li { width: auto; }\n}",
			),
			array(
				'test_condition_name' => '範囲指定 (400px < width < 800px) と >= と a>b を含む場合 => そのまま保存される',
				'input'               => '@media (400px < width < 800px) and (height >= 300px) { a>b { color: red; } }',
				'expected'            => '@media (400px < width < 800px) and (height >= 300px) { a>b { color: red; } }',
			),
			array(
				'test_condition_name' => '(400px<width ) and (width>800px) を含む場合 => そのまま保存される',
				'input'               => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
				'expected'            => '@media (400px<width ) and (width>800px) { .a > li { color:red; } }',
			),
			array(
				'test_condition_name' => '(400px <width) と (400px<width) と (width>800px) を含む場合 => そのまま保存される',
				'input'               => '@media (400px <width) and (width>800px) { a { color: red; } } @media (400px<width) { b { color: red; } }',
				'expected'            => '@media (400px <width) and (width>800px) { a { color: red; } } @media (400px<width) { b { color: red; } }',
			),
			array(
				'test_condition_name' => 'HTML コメントを含む場合 => コメントを中身ごと除去して保存される',
				'input'               => "<!-- ヘッダーの調整 -->\n.site-header { background: red; }",
				'expected'            => '.site-header { background: red; }',
			),
			array(
				'test_condition_name' => '</style><script> を含む場合 => style の終了タグと script は保存されない',
				'input'               => 'div { color: red; }</style><script>alert(1)</script>',
				'expected'            => 'div { color: red; }',
			),
			array(
				'test_condition_name' => 'ルールの後ろに img タグがある場合 => img タグを除去して保存される',
				'input'               => 'div{color:red;}<img src=x onerror=alert(1)>',
				'expected'            => 'div{color:red;}',
			),
			array(
				'test_condition_name' => 'ルールの間に br タグがある場合 => br を除去して保存される',
				'input'               => '.a{color:red;}<br>.b{color:blue;}',
				'expected'            => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'p タグで囲まれている場合 => p タグを除去して保存される',
				'input'               => '<p>.a{color:red;}</p>',
				'expected'            => '.a{color:red;}',
			),
			array(
				'test_condition_name' => '大文字の <BR> の場合 => 大文字小文字を区別せず除去して保存される',
				'input'               => '.a{color:red;}<BR>.b{color:blue;}',
				'expected'            => '.a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => '廃止済みの要素 <center> <font color=red> </font> の場合 => 除去して保存される',
				'input'               => 'div{color:red;}<center><font color=red>.a{color:blue;}</font></center>',
				'expected'            => 'div{color:red;}.a{color:blue;}',
			),
			array(
				'test_condition_name' => '「"」で囲んだ SVG のデータ URI の場合 => 中のタグを残し、</ だけ無効化して保存される',
				'input'               => '.a{background:url("data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 10 10\'><path d=\'M0 0h10v10z\' fill=\'red\'/></svg>");}',
				'expected'            => '.a{background:url("data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 10 10\'><path d=\'M0 0h10v10z\' fill=\'red\'/><\\/svg>");}',
			),
			array(
				'test_condition_name' => '「\'」で囲んだ SVG のデータ URI と文字列の外の <br> の場合 => 文字列の中は残し、外の <br> は除去して保存される',
				'input'               => '.a{background:url(\'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0"/></svg>\');}<br>.b{color:blue;}',
				'expected'            => '.a{background:url(\'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0"/><\\/svg>\');}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'content:"<br>" と文字列の外の <p> の場合 => 文字列の中は残し、外の <p> は除去して保存される',
				'input'               => '.a::before{content:"<br>";}<p>.b{color:blue;}</p>',
				'expected'            => '.a::before{content:"<br>";}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'CSS コメントの中に \' がある場合 => 後ろの <br> を除去して保存される',
				'input'               => '/* it\'s <p> */ .a{color:red;}<br>.b{color:blue;}',
				'expected'            => '/* it\'s <p> */ .a{color:red;}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'a > b と a>b を含む場合 => そのまま保存される',
				'input'               => 'a > b { color: red; } a>b { color: blue; }',
				'expected'            => 'a > b { color: red; } a>b { color: blue; }',
			),
			array(
				'test_condition_name' => '> で閉じない </STYLE を含む場合 => </ を無効化して保存される',
				'input'               => "div { color: red; }</STYLE\n.a { color: blue; }",
				'expected'            => "div { color: red; }<\\/STYLE\n.a { color: blue; }",
			),
			array(
				'test_condition_name' => '「"」の文字列の中に 200,000 文字ある場合 => CSS が消えず、外のタグを除去し </ を無効化して保存される',
				'input'               => '.a{background:url("data:image/svg+xml,<svg>' . $long . '</svg>");}<br></style>.b{color:blue;}',
				'expected'            => '.a{background:url("data:image/svg+xml,<svg>' . $long . '<\\/svg>");}.b{color:blue;}',
			),
			array(
				'test_condition_name' => 'セレクタ内でエスケープした \\" の後ろに <br> がある場合 => <br> を除去して保存される',
				'input'               => '.foo\\"{color:red;}<br>',
				'expected'            => '.foo\\"{color:red;}',
			),
			array(
				'test_condition_name' => '文字列の中の \\ の直後が CRLF の場合 => 文字列の中の <br> を残して保存される',
				'input'               => ".a{content:\"x\\\r\n<br>\";}<br>",
				'expected'            => ".a{content:\"x\\\r\n<br>\";}",
			),
			array(
				'test_condition_name' => '文字列の中の \\ の直後が LF の場合 => 文字列の中の <br> を残して保存される',
				'input'               => ".a{content:\"x\\\n<br>\";}<br>",
				'expected'            => ".a{content:\"x\\\n<br>\";}",
			),
			array(
				'test_condition_name' => '文字列の中の \\ の直後が FF の場合 => 文字列の中の <br> を残して保存される',
				'input'               => ".a{content:\"x\\\f<br>\";}<br>",
				'expected'            => ".a{content:\"x\\\f<br>\";}",
			),
			array(
				'test_condition_name' => 'エスケープしていない改行で終わる閉じていない文字列の場合 => 改行で文字列が終わり、次の行のタグを除去して保存される',
				'input'               => ".a{content:\"x<br>\n}<br>.b{color:blue;}",
				'expected'            => ".a{content:\"x<br>\n}.b{color:blue;}",
			),
		);

		// edit_post 権限を持つ最も低いロール（寄稿者）で、自分の下書き投稿に保存する
		// Save to the user's own draft as a contributor, the lowest role with edit_post.
		$contributor_id = self::factory()->user->create( array( 'role' => 'contributor' ) );
		wp_set_current_user( $contributor_id );

		foreach ( $test_cases as $case ) {
			// 投稿作成時の save_post で前周の値が保存されないよう、先に $_POST を空にする
			// Clear $_POST first so save_post on post creation does not save the previous value.
			$_POST = array();

			$post_id = wp_insert_post(
				array(
					'post_title'  => 'Contributor Post',
					'post_status' => 'draft',
					'post_type'   => 'post',
					'post_author' => $contributor_id,
				)
			);

			// 実際の HTTP リクエストと同じく、スラッシュを付加した値で保存する
			// Save a slashed value, as a real HTTP request does.
			$metabox = new VEU_Metabox_CSS_Customize();
			$this->set_post_data( $metabox, wp_slash( $case['input'] ) );
			$metabox->save_custom_field( $post_id );

			$actual = get_post_meta( $post_id, '_veu_custom_css', true );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
			$this->assertStringNotContainsStringIgnoringCase( '</style', $actual, $case['test_condition_name'] );

			// テスト用データを削除する
			// Delete the test data.
			wp_delete_post( $post_id, true );
			$_POST = array();
		}
	}

	/**
	 * test_save_custom_field_with_invalid_nonce
	 *
	 * nonce が不正な場合はカスタム CSS が保存されないことを検証する。
	 * Verify that custom CSS is not saved when the nonce is invalid.
	 */
	public function test_save_custom_field_with_invalid_nonce() {

		$post_id = $this->create_test_post();
		$this->login_as_administrator();

		$metabox = new VEU_Metabox_CSS_Customize();

		$_POST[ 'noncename__' . $metabox->args['cf_name'] ] = 'invalid_nonce_value';
		$_POST[ $metabox->args['cf_name'] ]                 = 'div { color: red; }';

		$metabox->save_custom_field( $post_id );

		$actual = get_post_meta( $post_id, '_veu_custom_css', true );
		$this->assertEmpty( $actual, 'nonce が不正な場合は保存されない' );

		wp_delete_post( $post_id, true );
	}

	/**
	 * test_save_custom_field_does_not_double_unslash_backslash_values
	 *
	 * $_POST の値に含まれる本物のバックスラッシュが二重に unslash されず、
	 * 1 回だけ除去された状態で保存されることを検証する ( issue #1471 項目2 )。
	 *
	 * WordPress の magic quotes 互換仕様では、実際の HTTP リクエストで $_POST の値に
	 * スラッシュが 1 段だけ付加されて渡ってくる。ここではその状態を、実際の 1 本の
	 * バックスラッシュを意図した値として二重バックスラッシュ ( PHP 文字列リテラルの
	 * \\\\ は実体として 2 文字 ) で再現する。
	 *
	 * Verify that a genuine backslash in a $_POST value is not double-unslashed
	 * ( issue #1471 item 2 ) and survives as a single backslash after saving.
	 *
	 * Under WordPress's magic-quotes-compatible behavior, a real HTTP request delivers
	 * $_POST values with one extra level of slashes added. This test reproduces that
	 * state with a doubled backslash ( standing in for one intended real backslash,
	 * as explained above ).
	 */
	public function test_save_custom_field_does_not_double_unslash_backslash_values() {

		$post_id = $this->create_test_post();
		$this->login_as_administrator();

		$metabox = new VEU_Metabox_CSS_Customize();

		// 実際の HTTP リクエストで $_POST に渡ってくる、スラッシュが 1 段付加された状態を再現する。
		// 実体としては 2 文字のバックスラッシュ ( \\ ) で、意図する実際の値は 1 本のバックスラッシュ。
		// Reproduce the one-level-slashed state that arrives in $_POST on a real HTTP
		// request. This is 2 real backslash characters, standing in for the one
		// intended real backslash.
		$raw_value = 'div::before { content: "\\\\f101"; }';

		$this->set_post_data( $metabox, $raw_value );
		$metabox->save_custom_field( $post_id );

		// 1 回だけ unslash された状態でサニタイズされた値が期待値。
		// The expected value is sanitized after being unslashed exactly once.
		$expected = veu_sanitize_custom_css_input( stripslashes( $raw_value ) );

		$this->assertSame( $expected, get_post_meta( $post_id, '_veu_custom_css', true ) );

		wp_delete_post( $post_id, true );
	}
}
