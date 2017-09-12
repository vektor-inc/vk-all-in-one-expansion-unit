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
				'name' => 'bootstrap',
				'correct' => false,
			),
			array(
				'name' => 'metaDescription',
				'correct' => true,
			),
		);

		print PHP_EOL;
		print '------------------------------------'.PHP_EOL;
		print 'test_package_manager'.PHP_EOL;
		print '------------------------------------'.PHP_EOL;
		foreach ( $test_array as $key => $test_value) {
			// 判定結果
			$result = vkExUnit_package_is_enable( $test_value['name'] );

			// 取得できた値と、想定する値が等しいかテスト
			$this->assertEquals( $test_value['correct'], $result );

			print PHP_EOL;
			print 'Package         :'.$result.PHP_EOL;
			print 'Package Correct :'.$test_value['correct'].PHP_EOL;
		}

		// $this->assertTrue( true );
	}
}
