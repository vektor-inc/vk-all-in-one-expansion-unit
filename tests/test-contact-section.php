<?php
/**
 * Class ContactSectionTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * お問い合わせセクション（VkExUnit_Contact）の描画テスト。
 * Test for the contact section ( VkExUnit_Contact ) rendering.
 */
class ContactSectionTest extends WP_UnitTestCase {

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

	/**
	 * render_contact_section_html の装飾アイコン（電話 / 封筒 / 矢印）に aria-hidden="true" が付く事のテスト。
	 * Test that the decorative icons ( phone / envelope / arrow ) in render_contact_section_html get aria-hidden="true".
	 *
	 * @return void
	 */
	function test_render_contact_section_html() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attributes do not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		// tel_icon の保存値（<i> 丸ごと / クラス文字列）ごとに、組み立て直した電話アイコンへ aria-hidden が付く事を検証する。
		// For each saved tel_icon value ( full <i> tag / class string ), verify the rebuilt phone icon gets aria-hidden.
		$tel_icon_cases = array(
			array(
				'test_condition_name' => 'tel_icon が <i> 丸ごとの場合 => 電話アイコンに aria-hidden が付く',
				'tel_icon'            => '<i class="fa-solid fa-phone"></i>',
				'expected'            => '<i class="contact_txt_tel_icon fa-solid fa-phone" aria-hidden="true"></i>',
			),
			array(
				'test_condition_name' => 'tel_icon がクラス文字列の場合 => 電話アイコンに aria-hidden が付く',
				'tel_icon'            => 'fa-solid fa-phone',
				'expected'            => '<i class="contact_txt_tel_icon fa-solid fa-phone" aria-hidden="true"></i>',
			),
		);

		foreach ( $tel_icon_cases as $case ) {
			// お問い合わせ情報のオプションを設定 / Set the contact information option.
			update_option(
				'vkExUnit_contact',
				array(
					'tel_icon'     => $case['tel_icon'],
					'tel_number'   => '000-000-0000',
					'contact_link' => 'https://example.com',
					'button_text'  => 'Contact us',
					'short_text'   => 'Contact us',
				)
			);

			// お問い合わせセクションの HTML を取得 / Get the contact section HTML.
			$html = VkExUnit_Contact::render_contact_section_html();

			// 電話アイコン（保存値によらず組み立て直した <i>）に aria-hidden が付く事を確認。
			// Check the phone icon ( the <i> rebuilt regardless of the saved value ) has aria-hidden.
			$this->assertStringContainsString( $case['expected'], $html, $case['test_condition_name'] );
			// ボタン前の封筒アイコンに aria-hidden が付く事を確認 / Check the envelope icon before the button has aria-hidden.
			$this->assertStringContainsString( '<i class="fa-regular fa-envelope" aria-hidden="true"></i>', $html, $case['test_condition_name'] );
			// ボタン後の矢印アイコンに aria-hidden が付く事を確認 / Check the arrow icon after the button has aria-hidden.
			$this->assertStringContainsString( '<i class="fa-regular fa-circle-right" aria-hidden="true"></i>', $html, $case['test_condition_name'] );

			// オプションをクリーンアップ / Clean up the option.
			delete_option( 'vkExUnit_contact' );
		}
	}

	/**
	 * render_widget_contact_btn_html の装飾アイコン（封筒 / 矢印）に aria-hidden="true" が付く事のテスト。
	 * Test that the decorative icons ( envelope / arrow ) in render_widget_contact_btn_html get aria-hidden="true".
	 *
	 * @return void
	 */
	function test_render_widget_contact_btn_html() {
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attributes do not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		// 保存オプションの組み合わせごとに、封筒・矢印の装飾アイコンへ aria-hidden が付く事を検証する。
		// For each combination of saved options, verify the decorative envelope / arrow icons get aria-hidden.
		$test_cases = array(
			array(
				'test_condition_name' => 'リンクと短いテキストのみ設定した場合 => 封筒・矢印アイコンに aria-hidden が付く',
				'options'             => array(
					'contact_link' => 'https://example.com',
					'short_text'   => 'Contact us',
				),
				'expected'            => array(
					'<i class="fa-regular fa-envelope" aria-hidden="true"></i>',
					'<i class="fa-regular fa-circle-right" aria-hidden="true"></i>',
				),
			),
			array(
				'test_condition_name' => 'ボタン補足テキストも設定した場合 => 封筒・矢印アイコンに aria-hidden が付く',
				'options'             => array(
					'contact_link'      => 'https://example.com',
					'short_text'        => 'Contact us',
					'button_text_small' => 'お気軽にどうぞ',
				),
				'expected'            => array(
					'<i class="fa-regular fa-envelope" aria-hidden="true"></i>',
					'<i class="fa-regular fa-circle-right" aria-hidden="true"></i>',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			// お問い合わせボタンウィジェットの描画に必要なオプションを設定。
			// Set the option required to render the contact button widget.
			update_option( 'vkExUnit_contact', $case['options'] );

			// お問い合わせボタンウィジェットの HTML を取得 / Get the contact button widget HTML.
			$html = VkExUnit_Contact::render_widget_contact_btn_html();

			// 封筒・矢印の装飾アイコンに aria-hidden が付く事を確認 / Check the decorative envelope / arrow icons have aria-hidden.
			foreach ( $case['expected'] as $expected ) {
				$this->assertStringContainsString( $expected, $html, $case['test_condition_name'] );
			}

			// オプションをクリーンアップ / Clean up the option.
			delete_option( 'vkExUnit_contact' );
		}
	}
}
