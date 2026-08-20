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
	 *
	 * @return int 作成した投稿 ID
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
	 * @return int 作成した管理者ユーザーの ID
	 */
	protected function login_as_administrator() {
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );
		return $admin_id;
	}

	/**
	 * $_POST に正規の nonce と CSS 値をセットするヘルパー
	 *
	 * @param VEU_Metabox_CSS_Customize $metabox テスト対象のメタボックスインスタンス
	 * @param string                    $value   保存する CSS 値
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
		}
	}

	/**
	 * test_save_custom_field_with_invalid_nonce
	 *
	 * nonce が不正な場合はカスタム CSS が保存されないことを検証する。
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
	 */
	public function test_save_custom_field_does_not_double_unslash_backslash_values() {

		$post_id = $this->create_test_post();
		$this->login_as_administrator();

		$metabox = new VEU_Metabox_CSS_Customize();

		// 実際の HTTP リクエストで $_POST に渡ってくる、スラッシュが 1 段付加された状態を再現する。
		// 実体としては 2 文字のバックスラッシュ ( \\ ) で、意図する実際の値は 1 本のバックスラッシュ。
		$raw_value = 'div::before { content: "\\\\f101"; }';

		$this->set_post_data( $metabox, $raw_value );
		$metabox->save_custom_field( $post_id );

		// 1 回だけ unslash された状態でサニタイズされた値が期待値。
		$expected = veu_sanitize_custom_css_input( stripslashes( $raw_value ) );

		$this->assertSame( $expected, get_post_meta( $post_id, '_veu_custom_css', true ) );

		wp_delete_post( $post_id, true );
	}
}
