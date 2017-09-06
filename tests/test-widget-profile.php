<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetProfile extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_sample() {
		// テスト用の投稿を追加する

		$test_array = array(
			array(
				'iconFont_bgType' => '',
				'icon_color' => '',
				'correct_outer_css' => '',
				'correct_icon_css' => '',
			),
			array(
				'iconFont_bgType' => 'no_paint',
				'icon_color' => '#f00',
				'correct_outer_css' => ' style="border:1px solid #f00;background:none;"',
				'correct_icon_css' => ' style="color:#f00;"',
			),
			array(
				'iconFont_bgType' => 'no_paint',
				'icon_color' => '',
				'correct_outer_css' => ' style="border:1px solid #ccc;background-color:#ccc;"',
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
			$iconFont_bgType = $test_value['iconFont_bgType'];
			$icon_color = $test_value['icon_color'];
			// 外枠に付与するCSSを取得
			$outer_css = WP_Widget_vkExUnit_profile::outer_css( $iconFont_bgType, $icon_color );
			// アイコンフォントに付与するCSSを取得
			$icon_css = WP_Widget_vkExUnit_profile::icon_css( $iconFont_bgType, $icon_color );
			// 取得できたCSSと、想定する正しいCSSが等しいかテスト
			$this->assertEquals( $test_value['correct_outer_css'], $outer_css );
			$this->assertEquals( $test_value['correct_icon_css'], $icon_css );
		}

		$this->assertTrue( true );
	}
}
