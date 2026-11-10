<?php
/**
 * Class PackageManagerTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * PackageManager test case.
 */
class PackageManagerTest extends WP_UnitTestCase {

	/**
	 * パッケージマネージャーの有効化判定テスト
	 */
	function test_package_manager() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_package_manager' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		/**
		 * インストールされたばかりでオプション値が存在しないとき
		 */
		$test_array = array(
			// どちらも未定義の場合（既存ユーザー）
			array(
				'name'    => 'metaDescription',
				'correct' => true,
			),
		);

		foreach ( $test_array as $key => $test_value ) {
			// 判定結果
			$result = veu_package_is_enable( $test_value['name'] );

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $test_value['correct'], $result );

			// print PHP_EOL;
			// print 'Package         :' . $result . PHP_EOL;
			// print 'Package Correct :' . $test_value['correct'] . PHP_EOL;
		}
	}

	function test_veu_common_options_validate() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_common_options_validate' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		/**
		 * 保存バリデート
		 */
		$test_array = array(
			// どちらも未定義の場合
			array(
				// 'active_pagetop_button' => null,
				'correct' => false,
			),
			array(
				'active_pagetop_button' => null,
				'correct'               => false,
			),
			array(
				'active_pagetop_button' => 1,
				'correct'               => true,
			),
			array(
				'active_pagetop_button' => '',
				'correct'               => false,
			),
		);

		foreach ( $test_array as $key => $test_value ) {
			// 判定結果
			$output = veu_common_options_validate( $test_value );

			// print PHP_EOL;
			// print 'options_validate         :' . $output['active_pagetop_button'] . PHP_EOL;
			// print 'options_validate Correct :' . $test_value['correct'] . PHP_EOL;

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $test_value['correct'], $output['active_pagetop_button'] );
		}

		$test_array = array(
			array(
				'active_pagetop_button' => array(
					'value'   => '',
					'correct' => false,
				),
			),
			array(
				'active_pagetop_button' => array(
					'value'   => null,
					'correct' => false,
				),
			),
		);
		foreach ( $test_array as $key => $test_value ) {

			// テスト用の配列を実際のoptionの配列形式に変換
			foreach ( $test_value as $key => $value ) {
					$options = array( $key => $value['value'] );
			}

			// 判定結果
			$output = veu_common_options_validate( $options );

			// print PHP_EOL;
			// print 'options_validate 2         :' . $output[ $key ] . PHP_EOL;
			// print 'options_validate Correct 2 :' . $value['correct'] . PHP_EOL;

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $value['correct'], $output[ $key ] );
		}
	}

	function test_veu_common_package_options_validate_empty_value() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_common_package_options_validate_empty_value' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$output = array();
		$input  = array(
			'active_metaDescription' => '',
		);

		$result = veu_common_package_options_validate( $output, $input );

		$this->assertFalse( $result['active_metaDescription'] );
	}

	/**
	 * /random/ 転送の無効化パッケージのテスト
	 *
	 * 設定値ごとに veu_package_include() を実行し、フィルターが登録されるかを確認する。
	 * veu_package_include() は require_once のため、同一プロセスで一度読み込まれると再実行されない。
	 * そのため「読み込まれない条件」を先に、「読み込まれる条件」を最後に並べている。
	 */
	function test_veu_disable_random_content_redirect() {

		global $vkExUnit_packages;

		// 説明文にパーマリンク設定画面へのリンクがあること
		$this->assertStringContainsString( 'options-permalink.php', $vkExUnit_packages['disable_random_redirect']['description'] );

		$include_file = VEU_DIRECTORY_PATH . '/inc/disable-random-redirect.php';

		$test_array = array(
			// 未保存（初期値）：無効のまま
			array(
				'options'  => false,
				'correct'  => false,
			),
			// 明示的に無効
			array(
				'options'  => array( 'active_disable_random_redirect' => false ),
				'correct'  => false,
			),
			// 有効：フィルターが登録され、転送が止まる
			array(
				'options'  => array( 'active_disable_random_redirect' => true ),
				'correct'  => true,
			),
		);

		foreach ( $test_array as $test_value ) {
			remove_filter( 'wp_enable_random_content_redirect', '__return_false' );
			if ( false === $test_value['options'] ) {
				delete_option( 'vkExUnit_common_options' );
			} else {
				update_option( 'vkExUnit_common_options', $test_value['options'] );
			}

			// 他のテストで読み込み済みだと require_once が再実行されないため、検証できない
			if ( $test_value['correct'] && in_array( realpath( $include_file ), get_included_files(), true ) ) {
				$this->markTestSkipped( 'disable-random-redirect.php は読み込み済みのため、有効時の登録を検証できません。' );
			}

			veu_package_include();

			$this->assertSame( $test_value['correct'], (bool) has_filter( 'wp_enable_random_content_redirect', '__return_false' ) );
			$this->assertSame( ! $test_value['correct'], apply_filters( 'wp_enable_random_content_redirect', true ) );
		}

		// 後続テストへ影響しないよう後片付け
		remove_filter( 'wp_enable_random_content_redirect', '__return_false' );
		delete_option( 'vkExUnit_common_options' );
	}
}
