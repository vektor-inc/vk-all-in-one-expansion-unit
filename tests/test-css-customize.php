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
	 * Test that backslashes are preserved when saving custom CSS via metabox
	 */
	public function test_custom_css_metabox_preserves_backslash() {
		// テスト用の投稿を作成
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Test Post',
				'post_content' => 'Test content',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			)
		);

		// VEU_Metabox_CSS_Customize のインスタンスを取得
		global $veu_metabox_css_customize;
		if ( ! isset( $veu_metabox_css_customize ) ) {
			require_once dirname( __DIR__ ) . '/inc/css-customize/class-veu-metabox-css-customize.php';
			$veu_metabox_css_customize = new VEU_Metabox_CSS_Customize();
		}

		// バックスラッシュを含むCSSのテストケース
		$test_cases = array(
			array(
				'input'    => "h2::before {\n  font-family: \"Font Awesome 5 Free\";\n  font-weight: 900;\n  content: \"\\f508\";\n  margin-right: 0.5em;\n}",
				'expected' => "h2::before {\n  font-family: \"Font Awesome 5 Free\";\n  font-weight: 900;\n  content: \"\\f508\";\n  margin-right: 0.5em;\n}",
			),
			array(
				'input'    => 'content: "\f00c";',
				'expected' => 'content: "\f00c";',
			),
			array(
				'input'    => 'url("path\\to\\file")',
				'expected' => 'url("path\\to\\file")',
			),
		);

		foreach ( $test_cases as $test_case ) {
			// $_POST をモック
			$_POST['_veu_custom_css'] = $test_case['input'];
			// nonce_action は 'veu_metabox_' . cf_name の形式
			$nonce_action = 'veu_metabox__veu_custom_css';
			$_POST[ 'noncename__' . $veu_metabox_css_customize->args['cf_name'] ] = wp_create_nonce( $nonce_action );

			// save_custom_field を呼び出し
			$veu_metabox_css_customize->save_custom_field( $post_id );

			// 保存されたメタデータを取得
			$saved_css = get_post_meta( $post_id, '_veu_custom_css', true );

			// バックスラッシュが正しく保存されているか確認
			$this->assertStringContainsString( '\\f508', $saved_css, 'Backslash should be preserved in Font Awesome icon code' );
			$this->assertEquals( $test_case['expected'], $saved_css, 'CSS with backslash should be saved correctly' );

			// クリーンアップ
			delete_post_meta( $post_id, '_veu_custom_css' );
		}

		// $_POST をクリーンアップ
		unset( $_POST['_veu_custom_css'] );
		unset( $_POST[ 'noncename__' . $veu_metabox_css_customize->args['cf_name'] ] );

		// テスト用データを消去
		wp_delete_post( $post_id, true );
	}
}
