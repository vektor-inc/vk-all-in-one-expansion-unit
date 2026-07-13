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
	public function test_css_customize_get_the_css_min() {
		$tests = array(
			array(
				'option'  => 'div > h1 { color:red;   }',
				'correct' => 'div > h1{color:red;}',
			),
			array(
				'option'  => 'div > h1 {
					color:red;
					}',
				'correct' => 'div > h1{color:red;}',
			),
			array(
				'option'  => '<script></script>div > h1 {color:red;}',
				'correct' => 'div > h1{color:red;}',
			),
			// メディアクエリがある状態のテストケース
			array(
				'option'  => '@media (width > 1000px) {p { color: red   ;}}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'option'  => '@media (width > 1000px) {
					p {
						color: red;
					}
				}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'option'  => '<script></script>@media (width > 1000px) {p { color: red;}}',
				'correct' => '@media (width > 1000px){p{color:red;}}',
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
			// print PHP_EOL;
			// print 'return    :' . $return . PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );
		} // foreach ( $tests as $key => $test_value ) {
			$before_option = update_option( 'vkExUnit_css_customize', $before_option );
	} // function test_css_customize_get_the_css_min() {

	/**
	 * Singular page css
	 */
	public function test_veu_get_the_custom_css_single() {

		// 要件と期待する結果
		$test_array = array(
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 { color:red;   }',
				'correct'    => 'div > h1{color:red;}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => 'div > h1 {
						color:red;
						}',
				'correct'    => 'div > h1{color:red;}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '<script></script>div > h1 {color:red;}',
				'correct'    => 'div > h1{color:red;}',
			), // メディアクエリがある状態のテストケース
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '@media (width > 1000px) {p { color: red   ;}}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '@media (width > 1000px) {
					p {
						color: red;
					}
				}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
			array(
				'post_title' => 'タイトル',
				'post_meta'  => '<script></script>@media (width > 1000px) {p { color: red;}}',
				'correct'    => '@media (width > 1000px){p{color:red;}}',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_get_the_custom_css_single' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			// テスト用のデータを投稿する
			$post_data = array(
				'post_title'   => $value['post_title'],
				'post_content' => $value['post_title'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
			);

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

			// print 'return  :' . $return . PHP_EOL;
			// print 'correct :' . $value['correct'] . PHP_EOL;

			// テスト用データを消去
			wp_delete_post( $post_id, true );
		} // foreach ( $test_array as $key => $value ) {
	} // function test_veu_get_the_custom_css_single() {

	/**
	 * Editor styles injection for the block editor.
	 * ブロックエディタへのエディタスタイル注入のテスト。
	 */
	public function test_veu_css_customize_single_editor_styles() {

		// テスト条件と期待する結果
		// with_post       : エディタコンテキストに投稿を含めるか（false = サイト/ウィジェットエディタ相当）
		// custom_css      : 投稿に設定するカスタムCSS（null = カスタムCSS未設定）
		// input           : block_editor_settings_all に渡す既存の editor settings
		// expected_styles : 期待する $settings['styles']（null = 'styles' が付与されず設定は未変更）
		$test_cases = array(
			array(
				'test_condition_name' => 'ケース1: post コンテキストありのとき、圧縮済み CSS がエディタスタイルに追加される',
				'with_post'           => true,
				'custom_css'          => 'div > h1 { color:red;   }',
				'input'               => array(),
				'expected_styles'     => array(
					array( 'css' => 'div > h1{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース2: 既存の styles を保持し、新しい CSS はその後ろに追加される',
				'with_post'           => true,
				'custom_css'          => 'div > h1 { color:red;   }',
				'input'               => array( 'styles' => array( array( 'css' => 'body{}' ) ) ),
				'expected_styles'     => array(
					array( 'css' => 'body{}' ),
					array( 'css' => 'div > h1{color:red;}' ),
				),
			),
			array(
				'test_condition_name' => 'ケース3: post コンテキストなし（サイト/ウィジェットエディタ）のとき、設定は未変更',
				'with_post'           => false,
				'custom_css'          => null,
				'input'               => array(),
				'expected_styles'     => null,
			),
			array(
				'test_condition_name' => 'ケース4: カスタムCSS未設定の投稿では設定は未変更',
				'with_post'           => true,
				'custom_css'          => null,
				'input'               => array(),
				'expected_styles'     => null,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_css_customize_single_editor_styles' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_cases as $case ) {

			// テスト用の投稿とエディタコンテキストを準備する。
			$post_id = 0;
			if ( $case['with_post'] ) {
				$post_id = wp_insert_post(
					array(
						'post_title'   => 'タイトル',
						'post_content' => 'content',
						'post_status'  => 'publish',
						'post_type'    => 'post',
					)
				);
				if ( null !== $case['custom_css'] ) {
					add_post_meta( $post_id, '_veu_custom_css', $case['custom_css'] );
				}
				$context = new WP_Block_Editor_Context( array( 'post' => get_post( $post_id ) ) );
			} else {
				$context = new WP_Block_Editor_Context();
			}

			$settings = veu_css_customize_single_editor_styles( $case['input'], $context );

			if ( null === $case['expected_styles'] ) {
				// 設定は未変更のまま返る。
				$this->assertSame( $case['input'], $settings, $case['test_condition_name'] );
			} else {
				$this->assertSame( $case['expected_styles'], $settings['styles'], $case['test_condition_name'] );
			}

			// テスト用データを削除する。
			if ( $post_id ) {
				wp_delete_post( $post_id, true );
			}
		} // foreach ( $test_cases as $case ) {
	} // function test_veu_css_customize_single_editor_styles() {
}
