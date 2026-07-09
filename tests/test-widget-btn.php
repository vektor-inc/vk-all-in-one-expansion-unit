<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * WidgetPage test case.
 */
class WidgetBtnTest extends WP_UnitTestCase {

	function test_get_btn_options() {
		$tests = array(
			array(
				'maintext' => 'メインテキスト',
				'title'    => null,
				'correct'  => 'メインテキスト',
			),
			array(
				'maintext' => 'メインテキスト',
				'title'    => '',
				'correct'  => '',
			),
			array(
				'maintext' => 'メインテキスト',
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
			array(
				'maintext' => null,
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
			array(
				'maintext' => '',
				'title'    => 'タイトル',
				'correct'  => 'タイトル',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'WP_Widget_Button' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			$return = WP_Widget_Button::get_btn_options( $test_value );
			$this->assertEquals( $test_value['correct'], $return['title'] );

			// print PHP_EOL;
			// print 'return    :' . $return['title'] . PHP_EOL;
			// print 'correct   :' . $test_value['correct'] . PHP_EOL;
		}
	}

	/**
	 * ボタン前後の装飾アイコンの <i> に aria-hidden="true" が付く事のテスト。
	 * Test that the decorative icons before / after the button label get aria-hidden="true" on their <i>.
	 *
	 * @return void
	 */
	function test_widget() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attribute does not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		$widget = new WP_Widget_Button();

		// ウィジェット出力に必要な最小限の $args / Minimal $args required to render the widget.
		$args = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		);

		// 前後アイコンとラベル・リンクをセットしたインスタンス / Instance with before / after icons, a label and a link.
		$instance = array(
			'title'       => 'Read more',
			'linkurl'     => 'https://example.com',
			'icon_before' => 'fa-solid fa-star',
			'icon_after'  => 'fa-solid fa-arrow-right',
		);

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		// 前後どちらの装飾アイコンにも aria-hidden="true" が付いている事を確認。
		// Check both the before and after decorative icons have aria-hidden="true".
		$this->assertStringContainsString( '<i class="fa-solid fa-star font_icon" aria-hidden="true"></i>', $output, 'ボタン前アイコンに aria-hidden が付く' );
		$this->assertStringContainsString( '<i class="fa-solid fa-arrow-right font_icon" aria-hidden="true"></i>', $output, 'ボタン後アイコンに aria-hidden が付く' );
	}
}
