<?php
/**
 * Class WidgetPrBlocksTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * PR Blocks ウィジェットの update() のテスト。
 */
class WidgetPrBlocksTest extends WP_UnitTestCase {

	/**
	 * block_count が4未満のとき、存在しないブロックのキーを update() がガードすることのテスト。
	 *
	 * update() のループは常にブロック1〜4を処理するが、block_count が3以下のとき
	 * フォームにブロック4の入力欄が無く、$new_instance にブロック4のキーは存在しない。
	 * その状態でも PHP 8.x の警告（未定義キー / null を文字列関数へ渡す）が出ず、
	 * 値が空文字へフォールバックすることを検証する。
	 *
	 * @return void
	 */
	function test_update_guards_missing_block_keys() {
		$widget = new WP_Widget_vkExUnit_PR_Blocks();

		// ブロックごとの文字列フィールド（未送信時に空文字へフォールバックすべきキー）。
		$string_fields = array(
			'label_',
			'media_image_',
			'media_alt_',
			'iconFont_class_',
			'iconFont_bgColor_',
			'iconFont_bgType_',
			'summary_',
			'linkurl_',
		);

		// テスト条件と期待する結果の組み合わせ。block_count を変えた検証を追加しやすいよう配列で持つ。
		$test_cases = array(
			array(
				'test_condition_name' => 'block_count=3 のとき未送信のブロック4がガードされる',
				'block_count'         => 3, // フォームに入力欄がある（＝送信される）ブロック数。
				'missing_block'       => 4, // 入力欄が無く $new_instance にキーが存在しないブロック番号。
			),
		);

		foreach ( $test_cases as $case ) {
			// 送信されるブロック（1〜block_count）のキーのみを詰める（それ以外は未送信）。
			$new_instance = array(
				'block_count' => (string) $case['block_count'],
			);
			for ( $i = 1; $i <= $case['block_count']; $i++ ) {
				$new_instance[ 'label_' . $i ]            = 'Label ' . $i;
				$new_instance[ 'media_image_' . $i ]      = '';
				$new_instance[ 'media_alt_' . $i ]        = '';
				$new_instance[ 'iconFont_class_' . $i ]   = 'far fa-file-alt';
				$new_instance[ 'iconFont_bgColor_' . $i ] = '#337ab7';
				$new_instance[ 'iconFont_bgType_' . $i ]  = '';
				$new_instance[ 'summary_' . $i ]          = '';
				$new_instance[ 'linkurl_' . $i ]          = '';
				// blank_$i（チェックボックス）は未チェック想定で未送信。
			}

			$result = $widget->update( $new_instance, array() );

			// 送信されたブロック1の値はそのまま保持されること（挙動が変わっていないことの確認）。
			$this->assertSame( 'Label 1', $result['label_1'], $case['test_condition_name'] );

			// 未送信ブロックの文字列フィールドは null ではなく空文字へフォールバックしていること。
			$missing = $case['missing_block'];
			foreach ( $string_fields as $field ) {
				$this->assertSame( '', $result[ $field . $missing ], $case['test_condition_name'] . ' / ' . $field . $missing );
			}

			// 未送信のチェックボックスは空文字ではなく false へフォールバックすること。
			$this->assertFalse( $result[ 'blank_' . $missing ], $case['test_condition_name'] . ' / blank_' . $missing );
		}
	}
}
