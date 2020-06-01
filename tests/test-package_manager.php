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

		/**
		 * インストールされたばかりでオプション値が存在しないとき
		 */
		$test_array = array(
			// どちらも未定義の場合（既存ユーザー）
			array(
				'name'    => 'bootstrap',
				'correct' => false,
			),
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
		}

	}

	function test_veu_common_options_validate() {

		/**
		 * 保存バリデート
		 */
		$test_array = array(
			// どちらも未定義の場合
			array(
				// 'active_bootstrap' => null,
				'correct' => false,
			),
			array(
				'active_bootstrap' => null,
				'correct'          => false,
			),
			array(
				'active_bootstrap' => 1,
				'correct'          => true,
			),
			array(
				'active_bootstrap' => '',
				'correct'          => '',
			),
		);

		foreach ( $test_array as $key => $test_value ) {
			// 判定結果
			$output = veu_common_options_validate( $test_value );

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $test_value['correct'], $output['active_bootstrap'] );

		}

		$test_array = array(
			array(
				'active_bootstrap' => array(
					'value'   => '',
					'correct' => false,
				),
			),
			array(
				'active_bootstrap' => array(
					'value'   => null,
					'correct' => false,
				),
			),
			array(
				'active_bootstrap' => array(
					'value'   => 1,
					'correct' => true,
				),
			),
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

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $value['correct'], $output[ $key ] );

		}

	}
}
