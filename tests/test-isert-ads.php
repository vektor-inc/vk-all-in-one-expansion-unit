<?php
/**
 * Class InsertAdsTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
/*
cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
 */
/**
 * WidgetPage test case.
 */
class InsertAdsTest extends WP_UnitTestCase {

	/**
	 * vExUnit_Ads::sanitize_config() は before/more/after が未送信でも
	 * Undefined array key / stripslashes(null) を出さず、既定値で保存する。
	 */
	function test_sanitize_config() {
		$ads = vExUnit_Ads::instance();

		$test_cases = array(
			array(
				'test_condition_name' => 'before/more/after が全て指定されている場合 => stripslashes 済みで保持される（正常系）',
				'input'               => array(
					'before' => array( 'before0', 'before1' ),
					'more'   => array( 'more0', 'more1' ),
					'after'  => array( 'after0', 'after1' ),
				),
				'expected'            => array(
					'before' => array( 'before0', 'before1' ),
					'more'   => array( 'more0', 'more1' ),
					'after'  => array( 'after0', 'after1' ),
				),
			),
			array(
				'test_condition_name' => 'エスケープされたクォートを含む場合 => stripslashes される（正常系）',
				'input'               => array(
					'before' => array( "O\\'clock", '' ),
					'more'   => array( '', '' ),
					'after'  => array( '', '' ),
				),
				'expected'            => array(
					'before' => array( "O'clock" ),
					'more'   => array( '' ),
					'after'  => array( '' ),
				),
			),
			array(
				'test_condition_name' => '入力が空配列（フィールド未送信）=> Undefined array key / stripslashes(null) を出さず既定値を返す（境界値）',
				'input'               => array(),
				'expected'            => array(
					'before' => array( '' ),
					'more'   => array( '' ),
					'after'  => array( '' ),
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = $ads->sanitize_config( $case['input'] );
			$this->assertSame( $case['expected']['before'], $actual['before'], $case['test_condition_name'] . ' / before' );
			$this->assertSame( $case['expected']['more'], $actual['more'], $case['test_condition_name'] . ' / more' );
			$this->assertSame( $case['expected']['after'], $actual['after'], $case['test_condition_name'] . ' / after' );
		}
	}

	function test_vExUnit_Ads_get_option() {

		$tests = array(
			// 投稿タイプ指定が存在しなかった（投稿タイプ選択機能実装後に保存された事がない）時用
			array(
				'option'  => array(),
				'correct' => array( 'post' => true ),
			),
			array(
				'option'  => array(
					'post_types' => array( 'post' => false ),
				),
				'correct' => array( 'post' => false ),
			),
			// 固定ページにチェックがはいっている場合
			array(
				'option'  => array(
					'post_types' => array( 'page' => true ),
				),
				'correct' => array( 'page' => true ),
			),
			// 投稿にチェックがはいっている場合
			array(
				'option'  => array(
					'post_types' => array( 'post' => true ),
				),
				'correct' => array( 'post' => true ),
			),
		);

		$before_option = get_option( 'vkExUnit_Ads' );
		delete_option( 'vkExUnit_Ads' );

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_vExUnit_Ads_get_option' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_Ads', $test_value['option'] );

			$return = vExUnit_Ads::get_option();

			// PHPunit
			$this->assertEquals( $test_value['correct'], $return['post_types'] );
			// print PHP_EOL;
			// 帰り値が配列だから print してもエラーになるだけなのでコメントアウト
			// print 'return    :' . $return['post_types'] . PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
			// // .php test
			// print '<pre style="text-align:left">';
			// print_r( $return['post_types'] ) . PHP_EOL;
			// print_r( $test_value['correct'] );
			// print '</pre>';
			delete_option( 'vkExUnit_Ads' );
		}
		update_option( 'vkExUnit_Ads', $before_option );
	}
}
