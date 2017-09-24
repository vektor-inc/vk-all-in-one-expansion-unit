<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetProfileTest extends WP_UnitTestCase {

	/**
	 * アイコンカラー出力CSSのテスト
	 */
	function test_icon_color() {
		// テスト用の投稿を追加する

		$test_array = array(
			// どちらも未定義の場合（既存ユーザー）
			array(
				'correct_outer_css' => '',
				'correct_icon_css' => '',
			),
			array(
				'iconFont_bgType' => '',
				'icon_color' => '',
				'correct_outer_css' => '',
				'correct_icon_css' => '',
			),
			// 塗りなし / 色指定あり
			array(
				'iconFont_bgType' => 'no_paint',
				'icon_color' => '#f00',
				'correct_outer_css' => ' style="border:1px solid #f00;background:none;"',
				'correct_icon_css' => ' style="color:#f00;"',
			),
			// 塗りなし / 色指定なし
			array(
				'iconFont_bgType' => 'no_paint',
				'icon_color' => '',
				'correct_outer_css' => ' style="border:1px solid #ccc;background:none;"',
				'correct_icon_css' => ' style="color:#ccc;"',
			),
			array(
				'iconFont_bgType' => '',
				'icon_color' => '#f00',
				'correct_outer_css' => ' style="border:1px solid #f00;background-color:#f00;"',
				'correct_icon_css' => ' style="color:#fff;"',
			),
		);

		foreach ( $test_array as $key => $test_value) {
			// 外枠に付与するCSSを取得
			$outer_css = WP_Widget_vkExUnit_profile::outer_css( $test_value );
			// アイコンフォントに付与するCSSを取得
			$icon_css = WP_Widget_vkExUnit_profile::icon_css( $test_value );

			// 取得できたCSSと、想定する正しいCSSが等しいかテスト
			$this->assertEquals( $test_value['correct_outer_css'], $outer_css );
			$this->assertEquals( $test_value['correct_icon_css'], $icon_css );

			print PHP_EOL;
			print 'outer_css_correct :'.$test_value['correct_outer_css'].PHP_EOL;
			print 'outer_css         :'.$outer_css.PHP_EOL;
			print 'icon_css_correct  :'.$test_value['correct_icon_css'].PHP_EOL;
			print 'icon_css          :'.$icon_css.PHP_EOL;
		}
	}
}
