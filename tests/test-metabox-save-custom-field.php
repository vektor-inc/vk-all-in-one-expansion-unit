<?php
/**
 * Class VEUMetaboxSaveCustomFieldTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * VEU_Metabox::save_custom_field() のテスト
 *
 * 主に以下の動作を検証する：
 * - 配列形式のカスタムフィールド（veu_head_title 等）が正しく保存されること（#1284 修正対象）
 * - 文字列形式のカスタムフィールドが正しく保存されること
 * - XSS 文字列がサニタイズされて保存されること
 * - nonce が不正な場合は保存されないこと
 */
class VEUMetaboxSaveCustomFieldTest extends WP_UnitTestCase {

	/**
	 * テスト用投稿を作成する
	 *
	 * @return int 作成した投稿 ID
	 */
	protected function create_test_post() {
		return wp_insert_post(
			array(
				'post_title'  => 'Metabox Test Post',
				'post_status' => 'publish',
				'post_type'   => 'post',
			)
		);
	}

	/**
	 * $_POST に nonce と値をセットして save_custom_field を呼び出すヘルパー
	 *
	 * @param VEU_Metabox $metabox テスト対象のメタボックスインスタンス
	 * @param int         $post_id 投稿 ID
	 * @param mixed       $value   保存する値（文字列または配列）
	 */
	protected function do_save( $metabox, $post_id, $value ) {
		$cf_name      = $metabox->args['cf_name'];
		$nonce_key    = 'noncename__' . $cf_name;
		$nonce_action = 'veu_metabox_' . $cf_name;

		// 正規の nonce をセット
		$_POST[ $nonce_key ] = wp_create_nonce( $nonce_action );
		$_POST[ $cf_name ]   = $value;

		$metabox->save_custom_field( $post_id );

		// テスト後に $_POST をクリーンアップ
		unset( $_POST[ $nonce_key ], $_POST[ $cf_name ] );
	}

	/**
	 * test_save_custom_field
	 *
	 * 各種入力パターンでカスタムフィールドが正しく保存されるかを検証する。
	 */
	public function test_save_custom_field() {

		$post_id = $this->create_test_post();

		$test_cases = array(
			// 配列形式の値が正しく保存される（#1284 の修正対象）
			// sanitize_text_field() は文字列のみ対応のため、配列を渡すと保存されなかった問題を修正
			array(
				'test_condition_name' => '配列形式の値（veu_head_title）が正しく保存される',
				'cf_name'             => 'veu_head_title',
				'value'               => array(
					'title'          => 'Custom Title',
					'add_site_title' => 'true',
				),
				'expected'            => array(
					'title'          => 'Custom Title',
					'add_site_title' => 'true',
				),
			),
			// 配列形式の値に XSS が含まれる場合、script タグが除去されてから保存される
			array(
				'test_condition_name' => '配列形式の値に XSS が含まれる場合は script タグが除去されて保存される',
				'cf_name'             => 'veu_head_title',
				'value'               => array(
					'title'          => '<script>alert(1)</script>Custom Title',
					'add_site_title' => 'true',
				),
				'expected'            => array(
					'title'          => 'Custom Title',
					'add_site_title' => 'true',
				),
			),
			// 文字列形式の値が正しく保存される（SNS タイトル等）
			array(
				'test_condition_name' => '文字列形式の値（vkExUnit_sns_title）が正しく保存される',
				'cf_name'             => 'vkExUnit_sns_title',
				'value'               => 'SNS Custom Title',
				'expected'            => 'SNS Custom Title',
			),
			// 文字列形式の値に XSS が含まれる場合、script タグが除去されてから保存される
			// ※ これが元々の XSS 修正（#1282）で対処したケース
			array(
				'test_condition_name' => '文字列形式の値に XSS が含まれる場合は script タグが除去されて保存される',
				'cf_name'             => 'vkExUnit_sns_title',
				'value'               => '<script>alert(1)</script>SNS Title',
				'expected'            => 'SNS Title',
			),
		);

		foreach ( $test_cases as $case ) {
			// テスト用のメタボックスインスタンスを作成
			// individual=true にして admin_menu 等のフックへの影響を限定する
			$metabox = new VEU_Metabox(
				array(
					'slug'       => 'test_' . $case['cf_name'],
					'cf_name'    => $case['cf_name'],
					'individual' => true,
				)
			);

			// 事前に古いメタ値を削除してクリーンな状態にする
			delete_post_meta( $post_id, $case['cf_name'] );

			// save_custom_field を実行
			$this->do_save( $metabox, $post_id, $case['value'] );

			// 保存された値を検証
			$actual = get_post_meta( $post_id, $case['cf_name'], true );
			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );

			// 後片付け
			delete_post_meta( $post_id, $case['cf_name'] );
		}
	}

	/**
	 * test_save_custom_field_with_invalid_nonce
	 *
	 * nonce が不正な場合はカスタムフィールドが保存されないことを検証する。
	 */
	public function test_save_custom_field_with_invalid_nonce() {

		$post_id = $this->create_test_post();
		$cf_name = 'test_invalid_nonce_field';

		$metabox = new VEU_Metabox(
			array(
				'slug'       => 'test_invalid_nonce',
				'cf_name'    => $cf_name,
				'individual' => true,
			)
		);

		// 不正な nonce をセット（CSRF 対策の検証）
		$_POST[ 'noncename__' . $cf_name ] = 'invalid_nonce_value';
		$_POST[ $cf_name ]                 = 'should not be saved';

		$metabox->save_custom_field( $post_id );

		unset( $_POST[ 'noncename__' . $cf_name ], $_POST[ $cf_name ] );

		// 保存されていないことを確認
		$actual = get_post_meta( $post_id, $cf_name, true );
		$this->assertEmpty( $actual, 'nonce が不正な場合は保存されない' );

		delete_post_meta( $post_id, $cf_name );
	}
}
