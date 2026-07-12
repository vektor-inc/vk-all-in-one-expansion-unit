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

	/**
	 * 各テスト後に、アイコンアクセシビリティのフィルターを元の登録内容で復元する。
	 * Restore the icon accessibility filters ( with the original arguments / priority ) after each test.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		add_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		add_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10, 2 );
		parent::tearDown();
	}

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

		// テスト条件（インスタンス）と、出力に含まれる／含まれない事を期待する文字列の組み合わせ。
		// アイコン指定のパターンを後から増やしやすいよう配列で持つ。
		// Combinations of the test instance and the strings expected to be present / absent in the output.
		// Kept as an array so icon patterns can be added later.
		$test_cases = array(
			array(
				'test_condition_name' => '前後両方のアイコン指定 => 前後どちらの <i> にも aria-hidden が付く',
				'instance'            => array(
					'title'       => 'Read more',
					'linkurl'     => 'https://example.com',
					'icon_before' => 'fa-solid fa-star',
					'icon_after'  => 'fa-solid fa-arrow-right',
				),
				'contains'            => array(
					'<i class="fa-solid fa-star font_icon" aria-hidden="true"></i>',
					'<i class="fa-solid fa-arrow-right font_icon" aria-hidden="true"></i>',
				),
				'not_contains'        => array(),
			),
			array(
				'test_condition_name' => '前アイコンのみ指定 => 前の <i> に aria-hidden が付き、後アイコンは出力されない',
				'instance'            => array(
					'title'       => 'Read more',
					'linkurl'     => 'https://example.com',
					'icon_before' => 'fa-solid fa-star',
				),
				'contains'            => array(
					'<i class="fa-solid fa-star font_icon" aria-hidden="true"></i>',
				),
				'not_contains'        => array(
					'fa-arrow-right',
				),
			),
			array(
				'test_condition_name' => 'アイコン未指定 => 装飾アイコンの <i>（font_icon）が出力されない',
				'instance'            => array(
					'title'   => 'Read more',
					'linkurl' => 'https://example.com',
				),
				'contains'            => array(),
				'not_contains'        => array(
					'font_icon',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			ob_start();
			$widget->widget( $args, $case['instance'] );
			$output = ob_get_clean();

			// 出力に含まれるべき装飾アイコンの <i>（aria-hidden 付き）を確認。
			// Check the decorative icon <i> ( with aria-hidden ) that must be present in the output.
			foreach ( $case['contains'] as $needle ) {
				$this->assertStringContainsString( $needle, $output, $case['test_condition_name'] );
			}
			// 出力に含まれてはいけない文字列（未指定アイコン等）を確認。
			// Check strings that must be absent ( e.g. an icon that was not specified ).
			foreach ( $case['not_contains'] as $needle ) {
				$this->assertStringNotContainsString( $needle, $output, $case['test_condition_name'] );
			}
		}
	}
}
