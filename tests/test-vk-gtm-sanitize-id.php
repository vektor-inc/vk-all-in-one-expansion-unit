<?php
/**
 * Google Tag Manager ID allowlist test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

// Google Tag Manager のモジュールは標準では読み込まれないため、テスト対象のファイルを読み込む。
// The Google Tag Manager module is not loaded by default, so load the files under test.
require_once dirname( __DIR__ ) . '/inc/vk-google-tag-manager/package/class-vk-google-tag-manager.php';
require_once dirname( __DIR__ ) . '/inc/vk-google-tag-manager/admin.php';

/**
 * Test case for vk_gtm_sanitize_id(), veu_gtm_options_validate(), vk_gtm_head() and vk_gtm_body().
 * vk_gtm_sanitize_id() / veu_gtm_options_validate() / vk_gtm_head() / vk_gtm_body() のテスト。
 */
class VkGtmSanitizeIdTest extends WP_UnitTestCase {

	/**
	 * Delete the option after each test.
	 * テストごとに option を削除する。
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		delete_option( 'vk_google_tag_manager_related_options' );
		parent::tearDown();
	}

	/**
	 * Test the ID allowlist.
	 * ID の許可リストをテストする。
	 *
	 * @return void
	 */
	public function test_vk_gtm_sanitize_id() {
		$test_array = array(
			// 既存の保存値 ( レガシー ) は変わらない。
			// Existing saved values ( legacy ) are not changed.
			array( 'ABC123', 'ABC123' ),
			array( 'abc123', 'abc123' ),
			array( ' ABC123 ', 'ABC123' ),
			// 「GTM-」付きで保存されていた値は接頭辞を取り除く。
			// A value saved with "GTM-" has the prefix removed.
			array( 'GTM-ABC123', 'ABC123' ),
			array( '', '' ),
			array( null, '' ),
			// 変更前は通っていた不正な値は空になる。
			// Invalid values that used to pass become empty.
			array( "ABC123');alert(1);//", '' ),
			array( 'ABC123"><script>', '' ),
			array( 'ABC 123', '' ),
			array( 'ABC-123', '' ),
			array( array( 'ABC123' ), '' ),
		);
		foreach ( $test_array as $value ) {
			$this->assertSame( $value[1], vk_gtm_sanitize_id( $value[0] ) );
		}
	}

	/**
	 * Test the save handler of the main settings screen.
	 * メイン設定画面の保存処理をテストする。
	 *
	 * @return void
	 */
	public function test_veu_gtm_options_validate() {
		$test_array = array(
			// 既存の保存値は変わらない。
			// Existing saved values are not changed.
			array( array( 'gtm_id' => 'ABC123' ), 'ABC123' ),
			// 「GTM-」付きの値は接頭辞を取り除く ( 画面上の接頭辞が重複しない )。
			// A value with "GTM-" has the prefix removed ( so the prefix shown on screen is not doubled ).
			array( array( 'gtm_id' => 'GTM-ABC123' ), 'ABC123' ),
			array( array( 'gtm_id' => "ABC123');alert(1);//" ), '' ),
			array( array( 'gtm_id' => 'ABC-123' ), '' ),
			array( array( 'gtm_id' => array( 'ABC123' ) ), '' ),
			array( array(), '' ),
			// 一括保存時に当該オプション群が無いと null が渡る。
			// null is passed when this option group is absent during the bulk save.
			array( null, '' ),
		);
		foreach ( $test_array as $value ) {
			$this->assertSame( array( 'gtm_id' => $value[1] ), veu_gtm_options_validate( $value[0] ) );
		}
	}

	/**
	 * Test the head / body output ( nothing is output for an invalid ID ).
	 * head / body の出力をテストする ( 不正な ID では何も出力しない )。
	 *
	 * @return void
	 */
	public function test_vk_gtm_output() {
		$test_array = array(
			array( 'ABC123', true ),
			array( 'GTM-ABC123', true ),
			array( "ABC123');alert(1);//", false ),
		);
		foreach ( $test_array as $value ) {
			update_option( 'vk_google_tag_manager_related_options', array( 'gtm_id' => $value[0] ) );
			ob_start();
			vk_gtm_head();
			$head = ob_get_clean();
			ob_start();
			vk_gtm_body();
			$body = ob_get_clean();
			if ( $value[1] ) {
				// 「GTM-」が二重にならず 1 回だけ出力される。
				// "GTM-" is output exactly once, not doubled.
				$this->assertStringContainsString( "'GTM-ABC123'", $head );
				$this->assertStringContainsString( 'id=GTM-ABC123"', $body );
			} else {
				$this->assertSame( '', $head );
				$this->assertSame( '', $body );
			}
		}
	}
}
