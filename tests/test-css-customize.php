<?php
/**
 * Class veu_css_customize
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * veu_css_customize test case.
 */
class CssCustomizeTest extends WP_UnitTestCase {

	/**
	 * カスタマイズCSSのテスト
	 */
	function test_css_customize_get_the_css_min() {
			$tests = array(
				array(
					'option'  => 'div > h1 { color:red;   }',
					'correct' => 'div > h1 { color:red; }',
				),
				array(
					'option'  => 'div > h1 {
						color:red;
						}',
					'correct' => 'div > h1 {color:red;}',
				),
				array(
					'option'  => '<script></script>div > h1 {color:red;}',
					'correct' => 'div > h1 {color:red;}',
				),
			);

			print PHP_EOL;
			print '------------------------------------' . PHP_EOL;
			print 'veu_css_customize' . PHP_EOL;
			print '------------------------------------' . PHP_EOL;
			$before_option = get_option( 'vkExUnit_css_customize' );

		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_css_customize', $test_value['option'] );
			$return = veu_css_customize::css_customize_get_the_css_min();
			print PHP_EOL;
			print 'return    :' . $return . PHP_EOL;
			print 'correct   :' . $test_value['correct'] . PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );
		} // foreach ( $tests as $key => $test_value ) {
		$before_option = update_option( 'vkExUnit_css_customize', $before_option );
	} // function test_css_customize_get_the_css_min() {

	/* Singular page css */
	function test_veu_get_the_custom_css_single() {

		// 要件と期待する結果
		$test_array = array(
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 { color:red;   }',
				'correct'    => 'div > h1 { color:red; }',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 {
						color:red;
						}',
				'correct'    => 'div > h1 {color:red;}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '<script></script>div > h1 {color:red;}',
				'correct'    => 'div > h1 {color:red;}',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_the_custom_css_single' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			// テスト用のデータを投稿する
			$post_data['post_content'] = $value['post_title'];

			// 投稿が成功すると投稿IDが返ってくる
			$post_id = wp_insert_post( $post_data );

			// カスタムCSSをカスタムフィールドに投稿
			add_post_meta( $post_id, '_veu_custom_css', $value['post_meta'] );

			// 実際に投稿されたデータを取得する
			$post = get_post( $post_id );

			// その投稿データの場合のカスタムCSS
			$return = veu_get_the_custom_css_single( $post );

			// 返ってきた値と期待する結果が同じかどうかテスト
			$this->assertEquals( $value['correct'], $return );

			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;

			// テスト用データを消去
			wp_delete_post( $post_id, true );

		} // foreach ( $test_array as $key => $value ) {

	} // function test_veu_get_the_custom_css_single() {

}
