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

		// Create a post that has per-post Custom CSS.
		// 投稿ごとのカスタムCSSを持つ投稿を作成する。
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'タイトル',
				'post_content' => 'content',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			)
		);
		add_post_meta( $post_id, '_veu_custom_css', 'div > h1 { color:red;   }' );
		$context = new WP_Block_Editor_Context( array( 'post' => get_post( $post_id ) ) );

		// Case 1: with a post context, the minified CSS is appended to the editor styles.
		// ケース1: post コンテキストありのとき、圧縮済み CSS がエディタスタイルに追加される。
		$settings = veu_css_customize_single_editor_styles( array(), $context );
		$this->assertNotEmpty( $settings['styles'] );
		$this->assertSame( 'div > h1{color:red;}', $settings['styles'][0]['css'] );

		// Case 2: existing styles are preserved and the new CSS is appended after them.
		// ケース2: 既存の styles を保持し、新しい CSS はその後ろに追加される。
		$settings = veu_css_customize_single_editor_styles( array( 'styles' => array( array( 'css' => 'body{}' ) ) ), $context );
		$this->assertCount( 2, $settings['styles'] );
		$this->assertSame( 'div > h1{color:red;}', $settings['styles'][1]['css'] );

		// Case 3: without a post in context (site / widget editor), the settings are unchanged.
		// ケース3: post コンテキストなし（サイト/ウィジェットエディタ）のとき、設定は変更されない。
		$settings = veu_css_customize_single_editor_styles( array(), new WP_Block_Editor_Context() );
		$this->assertArrayNotHasKey( 'styles', $settings );

		// Case 4: a post without Custom CSS leaves the settings unchanged.
		// ケース4: カスタムCSS未設定の投稿では設定は変更されない。
		$post_id_no_css = wp_insert_post(
			array(
				'post_title'  => 'no css',
				'post_status' => 'publish',
				'post_type'   => 'post',
			)
		);
		$context_no_css = new WP_Block_Editor_Context( array( 'post' => get_post( $post_id_no_css ) ) );
		$settings       = veu_css_customize_single_editor_styles( array(), $context_no_css );
		$this->assertArrayNotHasKey( 'styles', $settings );

		// Cleanup test posts.
		// テスト用の投稿を削除する。
		wp_delete_post( $post_id, true );
		wp_delete_post( $post_id_no_css, true );
	} // function test_veu_css_customize_single_editor_styles() {
}
