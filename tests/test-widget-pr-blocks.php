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

		// block_count = 3 で、ブロック1〜3のキーのみを送信する（ブロック4は未送信）。
		$new_instance = array(
			'block_count' => '3',
		);
		for ( $i = 1; $i <= 3; $i++ ) {
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
		$this->assertSame( 'Label 1', $result['label_1'] );

		// 未送信のブロック4は null ではなく空文字へフォールバックしていること。
		$this->assertSame( '', $result['label_4'] );
		$this->assertSame( '', $result['media_image_4'] );
		$this->assertSame( '', $result['media_alt_4'] );
		$this->assertSame( '', $result['iconFont_class_4'] );
		$this->assertSame( '', $result['iconFont_bgColor_4'] );
		$this->assertSame( '', $result['iconFont_bgType_4'] );
		$this->assertSame( '', $result['summary_4'] );
		$this->assertSame( '', $result['linkurl_4'] );

		// 未送信のチェックボックスは false になっていること。
		$this->assertFalse( $result['blank_4'] );
	}
}
