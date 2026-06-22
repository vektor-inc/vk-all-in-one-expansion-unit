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

	function test_image_align() {

		$test_media_align = array(
			// 旧フィールドが保存されている / 新フィールド（media_align）未保存の場合
			array(
				'mediaAlign_left'          => '',
				'mediaAlign'               => null,
				'correct_media_align_flag' => 'center',
			),
			array(
				'mediaAlign_left'          => true,
				'mediaAlign'               => null,
				'correct_media_align_flag' => 'left',
			),

			// 旧フィールドが保存されている / 新フィールドも保存されている
			array(
				'mediaAlign_left'          => false, // 中央指定
				'mediaAlign'               => 'left', // 左指定
				'correct_media_align_flag' => 'left',
			),
			array(
				'mediaAlign_left'          => true, // 左指定
				'mediaAlign'               => 'center', // 中央指定
				'correct_media_align_flag' => 'center',
			),

			array(
				'mediaAlign'               => 'left',
				'correct_media_align_flag' => 'left',
			),
			array(
				'mediaAlign'               => 'center',
				'correct_media_align_flag' => 'center',
			),
		);

		foreach ( $test_media_align as $key => $test_value ) {
			$media_align = WP_Widget_vkExUnit_profile::image_align( $test_value );
			$this->assertEquals( $test_value['correct_media_align_flag'], $media_align );
		}
	} // function test_image_align() {


	function test_image_outer_size_css() {
		/*
		round => true の場合 トリミングはtrue
		 */
		$test_image_outer_size_css = array(
			/* ピン角の場合 */
			array(
				'mediaSize'                    => false,
				'mediaRound'                   => false,
				'correct_media_outer_size_css' => '',
				'correct_media_width'          => 'max-height:100%;', // CSS側で指定
				'correct_media_height'         => 'auto', // CSS側で指定
			),
			array(
				'mediaSize'                    => null,
				'mediaRound'                   => null,
				'correct_media_outer_size_css' => '',
				'correct_media_width'          => 'max-height:100%;', // CSS側で指定
				'correct_media_height'         => 'auto', // CSS側で指定
			),
			array(
				'mediaSize'                    => '200',
				'mediaRound'                   => false,
				'correct_media_outer_size_css' => 'width:200px;',
				'correct_media_width'          => 'max-height:100%;', // CSS側で指定
				'correct_media_height'         => 'auto', // CSS側で指定
			),
			/*
			丸抜きの場合 */
			/* 画像自体は飛ばして背景画像にするので値は関係なくなる */
			array(
				'mediaSize'                    => null,
				'mediaRound'                   => true,
				'correct_media_outer_size_css' => '', // CSSで width:120px;height:120px;
			),
			array(
				'mediaSize'                    => '200',
				'mediaRound'                   => true,
				'correct_media_outer_size_css' => 'width:200px;height:200px;',
			),

		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_image_outer_size_css' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_image_outer_size_css as $key => $test_value ) {
			$image_outer_size_css = WP_Widget_vkExUnit_profile::image_outer_size_css( $test_value );
			$this->assertEquals( $test_value['correct_media_outer_size_css'], $image_outer_size_css );

			// print PHP_EOL;
			// print 'image_outer_size_css        :'.$image_outer_size_css.PHP_EOL;
			// print 'correct_media_outer_size_css:'.$test_value['correct_media_outer_size_css'].PHP_EOL;

		} // foreach ( $test_image_round as $key => $test_value) {
	} // function test_image_outer_size_css() {

	/**
	 * アイコンカラー出力CSSのテスト
	 */
	function test_icon_color() {
		// テスト用の投稿を追加する

		$test_array = array(
			// どちらも未定義の場合（既存ユーザー）
			array(
				'correct_outer_css' => ' class="bg_fill"',
				'correct_icon_css'  => '',
			),
			array(
				'iconFont_bgType'   => '',
				'icon_color'        => '',
				'correct_outer_css' => ' class="bg_fill"',
				'correct_icon_css'  => '',
			),
			array(
				'iconFont_bgType'   => '',
				'icon_color'        => '#f00',
				'correct_outer_css' => ' style="border-color:#f00;background-color:#f00;"',
				'correct_icon_css'  => ' style="color:#fff;"',
			),
			// 塗りなし（枠あり） / 色指定あり
			array(
				'iconFont_bgType'   => 'no_paint',
				'icon_color'        => '#f00',
				'correct_outer_css' => ' style="border-color: #f00; background:none;"',
				'correct_icon_css'  => ' style="color:#f00;"',
			),
			// 塗りなし（枠あり） / 色指定なし
			array(
				'iconFont_bgType'   => 'no_paint',
				'icon_color'        => '',
				'correct_outer_css' => ' style="background:none;"',
				'correct_icon_css'  => '',
			),
			// no_paint_frame / 色指定なし
			array(
				'iconFont_bgType'   => 'no_paint_frame',
				'icon_color'        => '',
				'correct_outer_css' => ' style="border:none;background:none; width:30px; height:30px;"',
				'correct_icon_css'  => '',
			),
		);

		foreach ( $test_array as $key => $test_value ) {
			// 外枠に付与するCSSを取得
			$outer_css = WP_Widget_vkExUnit_profile::outer_css( $test_value );
			// アイコンフォントに付与するCSSを取得
			$icon_css = WP_Widget_vkExUnit_profile::icon_css( $test_value );

			// 取得できたCSSと、想定する正しいCSSが等しいかテスト
			$this->assertEquals( $test_value['correct_outer_css'], $outer_css );
			$this->assertEquals( $test_value['correct_icon_css'], $icon_css );

			// print PHP_EOL;
			// print 'outer_css_correct :'.$test_value['correct_outer_css'].PHP_EOL;
			// print 'outer_css         :'.$outer_css.PHP_EOL;
			// print 'icon_css_correct  :'.$test_value['correct_icon_css'].PHP_EOL;
			// print 'icon_css          :'.$icon_css.PHP_EOL;

		} // foreach ( $test_array as $key => $test_value) {
	} // function test_icon_color() {

	/**
	 * update() がチェックボックス未送信のキーをガードすることのテスト。
	 *
	 * チェックボックス（mediaRound / mediaFloat）は未チェックだと送信されず
	 * $new_instance にキーが存在しない。その状態でも PHP 8.x の警告が出ず、
	 * 値が空文字（falsy）へフォールバックすることを検証する。
	 *
	 * @return void
	 */
	function test_update_guards_missing_checkbox_keys() {
		$widget = new WP_Widget_vkExUnit_profile();

		// テスト条件（送信内容）と期待する結果を配列で持ち、ループで検証する。
		$test_cases = array(
			array(
				'test_condition_name' => 'チェックボックス（mediaRound / mediaFloat）と廃止済み mediaAlign_left を未送信',
				// チェックボックス系（mediaRound / mediaFloat）と廃止済みの mediaAlign_left を
				// あえて含めない $new_instance（未チェック保存を再現）。
				'new_instance'        => array(
					'label'           => 'My Profile',
					'mediaFile'       => '',
					'mediaAlt'        => '',
					'profile'         => 'profile text',
					'mediaAlign'      => 'left',
					'mediaSize'       => '',
					'facebook'        => '',
					'twitter'         => '',
					'mail'            => '',
					'youtube'         => '',
					'rss'             => '',
					'instagram'       => '',
					'linkedin'        => '',
					'iconFont_bgType' => '',
					'icon_color'      => '',
				),
				// 未送信のチェックボックスは空文字（falsy）へ、送信値はそのまま保持されること。
				'expected_same'       => array(
					'mediaRound' => '',
					'mediaFloat' => '',
					'mediaAlign' => 'left',
					'label'      => 'My Profile',
				),
				// 廃止済みの mediaAlign_left は保存対象から外れ、キー自体が存在しないこと。
				'expected_absent'     => array( 'mediaAlign_left' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$result = $widget->update( $case['new_instance'], array() );

			foreach ( $case['expected_same'] as $key => $value ) {
				$this->assertSame( $value, $result[ $key ], $case['test_condition_name'] . ' / ' . $key );
			}
			foreach ( $case['expected_absent'] as $key ) {
				$this->assertArrayNotHasKey( $key, $result, $case['test_condition_name'] . ' / ' . $key );
			}
		}
	}

	/**
	 * 既存ユーザーの mediaAlign_left 保存値が update() 後も保持されることのテスト。
	 *
	 * update() で mediaAlign_left を保存対象から外したが、$old_instance に保存済みの
	 * レガシー値はそのまま引き継がれ、image_align() の移行フォールバックが
	 * これまでどおり機能することを検証する。
	 *
	 * @return void
	 */
	function test_update_keeps_legacy_media_align_left() {
		$widget = new WP_Widget_vkExUnit_profile();

		// mediaAlign は既定でチェック済みのラジオボタンのため常に送信される。
		// ここでは mediaAlign_left の引き継ぎ確認が目的なので、現実に即して mediaAlign は含める。
		$new_instance = array(
			'label'           => '',
			'mediaFile'       => '',
			'mediaAlt'        => '',
			'profile'         => '',
			'mediaAlign'      => 'left',
			'mediaSize'       => '',
			'facebook'        => '',
			'twitter'         => '',
			'mail'            => '',
			'youtube'         => '',
			'rss'             => '',
			'instagram'       => '',
			'linkedin'        => '',
			'iconFont_bgType' => '',
			'icon_color'      => '',
		);

		// 旧フィールド（mediaAlign_left）のみ保存されている既存ユーザーの保存値ごとに、
		// update() 後もその値が引き継がれることを検証する。
		$test_cases = array(
			array(
				'test_condition_name'       => 'mediaAlign_left=true が引き継がれる',
				'old_instance'              => array( 'mediaAlign_left' => true ),
				'expected_media_align_left' => true,
			),
			array(
				'test_condition_name'       => 'mediaAlign_left=false が引き継がれる',
				'old_instance'              => array( 'mediaAlign_left' => false ),
				'expected_media_align_left' => false,
			),
		);

		foreach ( $test_cases as $case ) {
			$result = $widget->update( $new_instance, $case['old_instance'] );

			// $old_instance のレガシー値が維持されていること。
			$this->assertArrayHasKey( 'mediaAlign_left', $result, $case['test_condition_name'] );
			$this->assertSame( $case['expected_media_align_left'], $result['mediaAlign_left'], $case['test_condition_name'] );
		}
	}
}
