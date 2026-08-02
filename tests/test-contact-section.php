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
	 * render_contact_section_html のテスト。
	 * 1. 装飾アイコン（電話 / 封筒 / 矢印）に aria-hidden="true" が付く事。
	 * 2. バナー画像表示時に、代替テキストと aria-label が排他で出力される事。
	 *
	 * Test for render_contact_section_html.
	 * 1. The decorative icons ( phone / envelope / arrow ) get aria-hidden="true".
	 * 2. In banner image mode, the alternative text and aria-label are output exclusively.
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
			array(
				// class より前に別属性（aria-hidden）を持つ <i>。属性順・追加属性に依存せず class を抽出できる事の回帰テスト。
				// An <i> tag with another attribute ( aria-hidden ) before class. Regression test that class is extracted regardless of attribute order / extra attributes.
				'test_condition_name' => 'tel_icon が aria-hidden を class より前に持つ <i> の場合 => class を正しく抽出し電話アイコンに aria-hidden が付く',
				'tel_icon'            => '<i aria-hidden="true" class="fa-solid fa-phone"></i>',
				'expected'            => '<i class="contact_txt_tel_icon fa-solid fa-phone" aria-hidden="true"></i>',
			),
		);

		// バナー画像表示のテスト用に、メディアライブラリの添付ファイルを2つ用意する。
		// 片方にはメディアライブラリ側の代替テキストを設定し、もう片方は未設定のままにする。
		// Prepare two media library attachments for the banner image test.
		// One has an alternative text set on the media library, the other has none.
		$plugin_dir                = dirname( __DIR__ );
		$factory                   = new WP_UnitTest_Factory();
		$attachment_id_with_alt    = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-1.png' );
		$attachment_id_without_alt = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-2.png' );
		update_post_meta( $attachment_id_with_alt, '_wp_attachment_image_alt', 'メディアライブラリの代替テキスト' );

		$image_url_with_alt    = wp_get_attachment_url( $attachment_id_with_alt );
		$image_url_without_alt = wp_get_attachment_url( $attachment_id_without_alt );
		$external_image_url    = 'https://example.com/foo.jpg';

		// バナー画像モード（contact_image 指定時）の出力テスト。
		// options は update_option 経由で保存するため、保存時の代替テキスト補完も同時に検証される。
		// Output test for the banner image mode ( when contact_image is set ).
		// The options are stored through update_option, so the save-time alternative text fill-in is verified too.
		$banner_image_cases = array(
			array(
				'test_condition_name' => '代替テキストの入力欄に値がある場合 => その値が alt に出て aria-label は出ない',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '入力欄に設定した代替テキスト',
					'button_text'       => 'お問い合わせはこちら',
				),
				'expected_contains'   => array(
					'alt="入力欄に設定した代替テキスト"',
					'<img src="' . esc_url( $image_url_with_alt ) . '"',
				),
				'expected_not'        => array(
					'aria-label',
				),
			),
			array(
				'test_condition_name' => '入力欄が空でメディアライブラリ側に代替テキストがある場合 => 保存時に取り込まれ alt に出る',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '',
					'button_text'       => 'お問い合わせはこちら',
				),
				'expected_contains'   => array(
					'alt="メディアライブラリの代替テキスト"',
				),
				'expected_not'        => array(
					'aria-label',
				),
			),
			array(
				'test_condition_name' => '入力欄もメディアライブラリ側も空の場合 => alt="" かつ aria-label にお問い合わせボタンの文言が出る',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $image_url_without_alt,
					'contact_image_alt' => '',
					'button_text'       => 'お問い合わせはこちら',
				),
				'expected_contains'   => array(
					'alt=""',
					'aria-label="お問い合わせはこちら"',
				),
				'expected_not'        => array(),
			),
			array(
				'test_condition_name' => '代替テキストが空でお問い合わせボタンの文言も空の場合 => aria-label が既定文字列になる',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $image_url_without_alt,
					'contact_image_alt' => '',
					'button_text'       => '',
				),
				'expected_contains'   => array(
					'alt=""',
					'aria-label="Contact us"',
				),
				'expected_not'        => array(),
			),
			array(
				// button_text は管理画面が textarea のため改行を含みうる。属性値に改行が残らない事の確認。
				// button_text can contain line breaks because the admin screen uses a textarea. Confirms no line break remains in the attribute value.
				'test_condition_name' => 'お問い合わせボタンの文言が改行を含む場合 => aria-label の改行が半角スペース1つに正規化される',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $image_url_without_alt,
					'contact_image_alt' => '',
					'button_text'       => "  メールで\nお問い合わせ  ",
				),
				'expected_contains'   => array(
					'alt=""',
					'aria-label="メールで お問い合わせ"',
				),
				'expected_not'        => array(
					"aria-label=\"メールで\nお問い合わせ\"",
				),
			),
			array(
				// メディアライブラリに存在しない外部 URL。逆引きに失敗しても致命的エラーにならない事の確認。
				// An external URL that does not exist in the media library. Confirms the failed lookup causes no fatal error.
				'test_condition_name' => 'バナー画像が外部 URL の場合 => 逆引きできず alt="" かつ aria-label にお問い合わせボタンの文言が出る',
				'options'             => array(
					'contact_link'      => 'https://example.com/contact/',
					'contact_image'     => $external_image_url,
					'contact_image_alt' => '',
					'button_text'       => 'お問い合わせはこちら',
				),
				'expected_contains'   => array(
					'alt=""',
					'aria-label="お問い合わせはこちら"',
					'<img src="' . esc_url( $external_image_url ) . '"',
				),
				'expected_not'        => array(),
			),
		);

		// アサーション失敗時も元の設定値を確実に戻すため、ループ実行前に元の値を保持し try/finally で復元する。
		// Preserve the original option before the loop and restore it in finally so the value is restored even if an assertion fails.
		$original_option = get_option( 'vkExUnit_contact', false );

		try {
			foreach ( $banner_image_cases as $case ) {
				// バナー画像表示に必要なオプションを設定 / Set the option required to render the banner image.
				update_option( 'vkExUnit_contact', $case['options'] );

				// お問い合わせセクションの HTML を取得 / Get the contact section HTML.
				$html = VkExUnit_Contact::render_contact_section_html();

				// 期待する文字列が含まれる事を確認 / Check the expected strings are included.
				foreach ( $case['expected_contains'] as $expected ) {
					$this->assertStringContainsString( $expected, $html, $case['test_condition_name'] );
				}

				// 出力されてはいけない文字列が含まれない事を確認 / Check the strings that must not be output are absent.
				foreach ( $case['expected_not'] as $not_expected ) {
					$this->assertStringNotContainsString( $not_expected, $html, $case['test_condition_name'] );
				}

				// オプションをクリーンアップ / Clean up the option.
				delete_option( 'vkExUnit_contact' );
			}

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
		} finally {
			// 元の設定値を復元（元々未設定だったら削除）/ Restore the original option value ( delete if it was originally unset ).
			if ( false === $original_option ) {
				delete_option( 'vkExUnit_contact' );
			} else {
				update_option( 'vkExUnit_contact', $original_option );
			}
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

		// アサーション失敗時も元の設定値を確実に戻すため、ループ実行前に元の値を保持し try/finally で復元する。
		// Preserve the original option before the loop and restore it in finally so the value is restored even if an assertion fails.
		$original_option = get_option( 'vkExUnit_contact', false );

		try {
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
		} finally {
			// 元の設定値を復元（元々未設定だったら削除）/ Restore the original option value ( delete if it was originally unset ).
			if ( false === $original_option ) {
				delete_option( 'vkExUnit_contact' );
			} else {
				update_option( 'vkExUnit_contact', $original_option );
			}
		}
	}

	/**
	 * fill_contact_image_alt が、保存時にメディアライブラリ側の代替テキストで補完する事のテスト。
	 * Test that fill_contact_image_alt fills the alternative text from the media library at save time.
	 *
	 * @return void
	 */
	function test_fill_contact_image_alt() {
		// 代替テキストありの添付ファイルと、代替テキストなしの添付ファイルを用意する。
		// Prepare an attachment with an alternative text and one without.
		$plugin_dir                = dirname( __DIR__ );
		$factory                   = new WP_UnitTest_Factory();
		$attachment_id_with_alt    = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-1.png' );
		$attachment_id_without_alt = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-2.png' );
		update_post_meta( $attachment_id_with_alt, '_wp_attachment_image_alt', 'メディアライブラリの代替テキスト' );

		$image_url_with_alt    = wp_get_attachment_url( $attachment_id_with_alt );
		$image_url_without_alt = wp_get_attachment_url( $attachment_id_without_alt );

		$test_cases = array(
			array(
				'test_condition_name' => '代替テキストが空でメディアライブラリ側に代替テキストがある場合 => メディアライブラリの値で補完される',
				'input'               => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '',
				),
				'expected'            => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => 'メディアライブラリの代替テキスト',
				),
			),
			array(
				'test_condition_name' => '代替テキストが入力済みの場合 => メディアライブラリの値で上書きしない',
				'input'               => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '入力欄に設定した代替テキスト',
				),
				'expected'            => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '入力欄に設定した代替テキスト',
				),
			),
			array(
				// 空判定が ! empty() だと "0" が空扱いになり、メディアライブラリの値へ黙って上書きされてしまう。
				// With a ! empty() check, "0" would count as empty and get silently overwritten by the media library value.
				'test_condition_name' => '代替テキストが文字列 "0" の場合 => 空扱いせず上書きしない',
				'input'               => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '0',
				),
				'expected'            => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => '0',
				),
			),
			array(
				'test_condition_name' => '代替テキストのキー自体が無い場合 => 空扱いして補完される',
				'input'               => array(
					'contact_image' => $image_url_with_alt,
				),
				'expected'            => array(
					'contact_image'     => $image_url_with_alt,
					'contact_image_alt' => 'メディアライブラリの代替テキスト',
				),
			),
			array(
				// 配列のまま attachment_url_to_postid() に渡すと、内部の parse_url() が PHP 8 で TypeError になる。
				// Passing an array to attachment_url_to_postid() would make its internal parse_url() throw a TypeError on PHP 8.
				'test_condition_name' => 'バナー画像が文字列以外（配列）の場合 => 致命的エラーにならずそのまま返す',
				'input'               => array(
					'contact_image'     => array( 'x' ),
					'contact_image_alt' => '',
				),
				'expected'            => array(
					'contact_image'     => array( 'x' ),
					'contact_image_alt' => '',
				),
			),
			array(
				'test_condition_name' => 'メディアライブラリ側にも代替テキストが無い場合 => 空のまま',
				'input'               => array(
					'contact_image'     => $image_url_without_alt,
					'contact_image_alt' => '',
				),
				'expected'            => array(
					'contact_image'     => $image_url_without_alt,
					'contact_image_alt' => '',
				),
			),
			array(
				// 逆引きできない外部 URL。存在しない添付 ID を参照して致命的エラーにならない事の確認。
				// An external URL that cannot be resolved. Confirms it does not fatal by referencing a missing attachment ID.
				'test_condition_name' => 'バナー画像が外部 URL の場合 => 逆引きできず空のまま',
				'input'               => array(
					'contact_image'     => 'https://example.com/foo.jpg',
					'contact_image_alt' => '',
				),
				'expected'            => array(
					'contact_image'     => 'https://example.com/foo.jpg',
					'contact_image_alt' => '',
				),
			),
			array(
				'test_condition_name' => 'バナー画像が未設定の場合 => 何もしない',
				'input'               => array(
					'contact_image'     => '',
					'contact_image_alt' => '',
				),
				'expected'            => array(
					'contact_image'     => '',
					'contact_image_alt' => '',
				),
			),
			array(
				// オプション値が配列以外で渡された異常系。そのまま返す事を確認する。
				// Abnormal case where a non array option value is passed. Confirms it is returned untouched.
				'test_condition_name' => 'オプション値が配列以外の場合 => そのまま返す',
				'input'               => 'not-an-array',
				'expected'            => 'not-an-array',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = VkExUnit_Contact::fill_contact_image_alt( $case['input'] );
			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * option_sanitaize が、バナー画像の代替テキストを安全なテキストとして保存する事のテスト。
	 * Test that option_sanitaize saves the banner image alternative text as safe text.
	 *
	 * @return void
	 */
	function test_option_sanitaize() {
		// サニタイズ対象の他項目は文字列前提のため、代替テキスト以外は固定値で埋めておく。
		// The other options are expected to be strings, so fill everything but the alternative text with fixed values.
		$base_option = array(
			'contact_txt'       => 'お気軽にお問い合わせください',
			'tel_number'        => '000-000-0000',
			'tel_icon'          => '<i class="fa-solid fa-phone"></i>',
			'contact_time'      => '9:00 - 18:00',
			'contact_link'      => 'https://example.com/contact/',
			'button_text'       => 'Contact us',
			'button_text_small' => '',
			'short_text'        => 'Contact us',
			'contact_image'     => 'https://example.com/foo.jpg',
			'contact_html'      => '',
		);

		$test_cases = array(
			array(
				'test_condition_name' => '代替テキストが通常の文字列の場合 => そのまま保存される',
				'contact_image_alt'   => 'お問い合わせはこちら',
				'expected'            => 'お問い合わせはこちら',
			),
			array(
				'test_condition_name' => '代替テキストに HTML タグが含まれる場合 => タグが除去される',
				'contact_image_alt'   => '<script>alert(1)</script>お問い合わせはこちら',
				'expected'            => 'お問い合わせはこちら',
			),
			array(
				// stripslashes() のままだと配列を渡された時点で PHP 8 の TypeError になり保存処理全体が落ちる。
				// With stripslashes() an array would throw a PHP 8 TypeError and take the whole save down.
				'test_condition_name' => '代替テキストが文字列以外（配列）で送信された場合 => 致命的エラーにならず空文字になる',
				'contact_image_alt'   => array( 'x' ),
				'expected'            => '',
			),
			array(
				'test_condition_name' => '代替テキストのキー自体が無い場合 => 空文字になる',
				'contact_image_alt'   => null,
				'expected'            => '',
			),
		);

		$contact = VkExUnit_Contact::instance();

		foreach ( $test_cases as $case ) {
			$option = $base_option;
			// null のケースはキー自体が送信されなかった状況を再現する / The null case reproduces a key that was never posted.
			if ( null !== $case['contact_image_alt'] ) {
				$option['contact_image_alt'] = $case['contact_image_alt'];
			}

			$actual = $contact->option_sanitaize( $option );

			$this->assertSame( $case['expected'], $actual['contact_image_alt'], $case['test_condition_name'] );
		}
	}

	/**
	 * normalize_attribute_text が、属性値へ入れる文字列の空白とタグを正規化する事のテスト。
	 * Test that normalize_attribute_text normalizes the whitespace and tags of a string that goes into an attribute value.
	 *
	 * @return void
	 */
	function test_normalize_attribute_text() {
		$test_cases = array(
			array(
				'test_condition_name' => '文字列の途中に改行がある場合 => 半角スペース1つにまとまる',
				'input'               => "メールで\nお問い合わせ",
				'expected'            => 'メールで お問い合わせ',
			),
			array(
				'test_condition_name' => '改行・タブ・連続空白が混在する場合 => それぞれ半角スペース1つにまとまる',
				'input'               => "メールで\r\n\tお問い合わせ   ください",
				'expected'            => 'メールで お問い合わせ ください',
			),
			array(
				'test_condition_name' => '前後に空白と改行がある場合 => 前後が削られる',
				'input'               => "  \n お問い合わせ \n  ",
				'expected'            => 'お問い合わせ',
			),
			array(
				'test_condition_name' => 'HTML タグを含む場合 => タグが除去され中身だけ残る',
				'input'               => '<strong>メールで</strong><br />お問い合わせ',
				'expected'            => 'メールで お問い合わせ',
			),
			array(
				'test_condition_name' => '空文字の場合 => 空文字のまま',
				'input'               => '',
				'expected'            => '',
			),
			array(
				// 想定外の型が渡された異常系。属性値に使えるよう必ず文字列を返す事の確認。
				// Abnormal case where an unexpected type is passed. Confirms a string is always returned so it can be used as an attribute value.
				'test_condition_name' => '文字列以外（null）が渡された場合 => 空文字を返す',
				'input'               => null,
				'expected'            => '',
			),
			array(
				'test_condition_name' => '文字列以外（配列）が渡された場合 => 空文字を返す',
				'input'               => array( 'お問い合わせ' ),
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = VkExUnit_Contact::normalize_attribute_text( $case['input'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}
}
