<?php
/**
 * Class Article_Structure_Test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

class Article_Structure_Test extends WP_UnitTestCase {
	function test_get_author_array() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_get_author_array' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テスト用ユーザーを発行
		// Create a test user.
		$userdata = array(
			'user_login'   => 'sutekivektor',
			'user_url'     => 'https://vektor-inc.co.jp',
			'user_pass'    => 'user_pass',
			'display_name' => 'vekujirou',
		);
		$user_id  = wp_insert_user( $userdata );

		// 存在しないユーザーID（十分に大きな値）。get_userdata() が false を返す異常系の検証に使う。
		// A non-existent user ID (a sufficiently large value), used for the abnormal case where get_userdata() returns false.
		$invalid_user_id = 999999;
		// 前提として当該ユーザーが存在しないことを確認しておく。
		// Make sure beforehand that the user really does not exist.
		$this->assertFalse( get_userdata( $invalid_user_id ) );

		$test_data = array(
			// 正常系 : 独自実装のユーザー情報フィールドがすべて埋められている && person
			// Normal case: all custom author fields are filled in && person.
			array(
				'test_condition_name' => '著者フィールドが全て入力済み && person の場合 => 入力値がそのまま返る',
				'user_id'             => $user_id,
				'author'              => array(
					'author_type'   => 'person',
					'author_name'   => 'vekutarou',
					'author_url'    => 'https://vektor-inc.co.jp/author/vekutarou',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct'             => array(
					'@type'  => 'person',
					'name'   => 'vekutarou',
					'url'    => 'https://vektor-inc.co.jp/author/vekutarou',
					'sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 正常系 : organization && url指定あり（チェック対象 : url）
			// Normal case: organization && url provided (target: url).
			array(
				'test_condition_name' => 'organization && url 指定ありの場合 => 指定した url が返る',
				'user_id'             => $user_id,
				'author'              => array(
					'author_type'   => 'organization',
					'author_name'   => 'vekutarou',
					'author_url'    => 'https://vektor-inc.co.jp/',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct'             => array(
					'@type'  => 'organization',
					'name'   => 'vekutarou',
					'url'    => 'https://vektor-inc.co.jp/',
					'sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 正常系 : organization && url指定なし（チェック対象 : url）
			// Normal case: organization && url not provided (target: url).
			array(
				'test_condition_name' => 'organization && url 指定なしの場合 => サイトトップ URL が返る',
				'user_id'             => $user_id,
				'author'              => array(
					'author_type'   => 'organization',
					'author_name'   => 'vekutarou',
					'author_url'    => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct'             => array(
					'@type'  => 'organization',
					'name'   => 'vekutarou',
					'url'    => home_url( '/' ),
					'sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 正常系 : person && url指定なし → 投稿者アーカイブのURL（チェック対象 : url）
			// Normal case: person && url not provided -> author archive URL (target: url).
			array(
				'test_condition_name' => 'person && url 指定なしの場合 => 投稿者アーカイブ URL が返る',
				'user_id'             => $user_id,
				'author'              => array(
					'author_type'   => 'person',
					'author_name'   => 'vekujirou',
					'author_url'    => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct'             => array(
					'@type'  => 'person',
					'name'   => 'vekujirou',
					'url'    => get_author_posts_url( $user_id ),
					'sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 正常系 : author_name 指定なし → デフォルトのユーザー表示名が適用されるか（チェック対象 : name）
			// Normal case: author_name not provided -> the user's display name is applied (target: name).
			array(
				'test_condition_name' => 'author_name 指定なしの場合 => ユーザーの表示名が返る',
				'user_id'             => $user_id,
				'author'              => array(
					'author_type'   => 'organization',
					'author_name'   => '',
					'author_url'    => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct'             => array(
					'@type'  => 'organization',
					'name'   => 'vekujirou',
					'url'    => home_url( '/' ),
					'sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 異常系 : 存在しないユーザーID → get_userdata() が false を返しても Warning を出さず name が空文字になるか
			// 不具合: 修正前は false に対し $author->display_name へアクセスし Warning が発生していた。
			// Abnormal case: a non-existent user ID -> even though get_userdata() returns false, no warning is raised and name becomes an empty string.
			// Bug: before the fix, accessing $author->display_name on false raised a warning.
			array(
				'test_condition_name' => '存在しないユーザーIDの場合 => Warning を出さず name が空文字で返る',
				'user_id'             => $invalid_user_id,
				// 存在しないユーザーには user_meta を設定しないため空配列。
				// No user_meta is set for a non-existent user, so this is empty.
				'author'              => array(),
				'correct'             => array(
					'@type'  => '',
					'name'   => '',
					'url'    => home_url( '/' ),
					'sameAs' => '',
				),
			),
		);

		foreach ( $test_data as $test_value ) {
			// 各ケースで使用するユーザーIDを取得する。
			// Get the user ID used by each case.
			$target_user_id = $test_value['user_id'];

			// ケースに応じてユーザーメタを設定する（存在しないユーザーの場合は author が空配列なので何もしない）。
			// Set the user meta according to the case (for a non-existent user, author is empty so nothing happens).
			foreach ( $test_value['author'] as $key => $value ) {
				update_user_meta( $target_user_id, $key, $value );
			}

			// WP_UnitTestCase は PHP の警告を例外に変換するため、
			// 修正前は存在しないユーザーIDのケースでこの呼び出し自体が警告→例外で失敗する（red）。
			// WP_UnitTestCase converts PHP warnings into exceptions, so before the fix
			// the non-existent user case fails (red) here due to the warning being thrown.
			$return  = VK_Article_Srtuctured_Data::get_author_array( $target_user_id );
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return, $test_value['test_condition_name'] );
		}

		// テストで発行したユーザーを削除
		// Delete the user created for the test.
		wp_delete_user( $user_id );
	}


	/**
	 * update_author_structure_data() のサニタイズ挙動を検証する。
	 * - author_type はホワイトリスト（organization / person）以外の値では保存されず既存値が維持される。
	 * - author_name は sanitize_text_field() でタグ等が除去される。
	 * - author_url / author_sameAs は、送信値が空でないのに esc_url_raw() が空文字を返す（許可されない
	 *   スキーム等で弾かれた）場合は保存されず既存値が維持される。送信値自体が空（意図的な空欄クリア）の
	 *   場合はサニタイズ後の値（空文字）で保存される。
	 * - 項目が送信されない（isset() が false）場合は、その項目の既存値が維持される。
	 * - 配列が送信されても Fatal error にならず、文字列でない値はサニタイズ対象外として無視される。
	 * - 既存の保存済みユーザーメタが、サニタイズ追加によって壊れたり消えたりしない（後方互換）。
	 * - メタが一度も保存されていない新規ユーザーに対しても、正常に保存できる（update_user_meta() の
	 *   第4引数 $prev_value が空文字になる、本番での主経路）。
	 *
	 * Verify the sanitization behavior of update_author_structure_data().
	 * - author_type is not saved (the existing value is kept) unless it is one of the whitelisted values (organization / person).
	 * - author_name has tags etc. stripped via sanitize_text_field().
	 * - For author_url / author_sameAs, when esc_url_raw() returns an empty string even though the submitted
	 *   value was not empty (rejected, e.g. a disallowed scheme), the value is not saved and the existing
	 *   value is kept. When the submitted value itself is empty (an intentional clear), it is saved as the
	 *   sanitized (empty) value.
	 * - When a field is not submitted (isset() is false), that field's existing value is kept.
	 * - Submitting an array does not cause a Fatal error; non-string values are ignored and not sanitized.
	 * - Existing saved user meta is not broken or lost by adding this sanitization (backward compatibility).
	 * - Saving also works normally for a fresh user whose meta has never been saved before (the $prev_value
	 *   argument to update_user_meta() being an empty string is the main path in production).
	 */
	function test_update_author_structure_data() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_update_author_structure_data' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テスト用ユーザーを発行。
		// Create a test user.
		$user_id = wp_insert_user(
			array(
				'user_login'   => 'sanitize_test_user',
				'user_pass'    => 'user_pass',
				'display_name' => 'Sanitize Test User',
			)
		);

		// update_author_structure_data() には current_user_can( 'edit_user', $user_id ) のチェックがあるため、
		// 自分自身のプロフィールを編集する体で current user をこのテストユーザーにしておく（自己編集は
		// 権限昇格なしに許可されるメタ capability のため、role を付与しなくても edit_user は true になる）。
		// update_author_structure_data() has a current_user_can( 'edit_user', $user_id ) check, so set the
		// current user to this test user as if they were editing their own profile (self-editing is allowed
		// by the edit_user meta capability without granting a role, so no privilege escalation is involved).
		wp_set_current_user( $user_id );

		// 各ケースの前提となる「既存の保存済みユーザーメタ」。サニタイズ追加後も、保存対象外・無効値のケースで
		// このデータが壊れず維持されることを確認する（後方互換の検証）。
		// 空配列を指定したケースは、メタを一切事前設定せず「未保存」の状態から始める。
		// The "existing saved user meta" that each case starts from. Used to verify that, even after adding this
		// sanitization, this data is kept intact when a field is not submitted or an invalid value is submitted
		// (backward compatibility check).
		// A case that specifies an empty array pre-sets no meta at all, starting from an "unsaved" state.
		$default_existing_meta = array(
			'author_type'   => 'organization',
			'author_name'   => 'Existing Name',
			'author_url'    => 'https://example.com/existing',
			'author_sameAs' => 'https://example.com/existing-sns',
		);

		$test_cases = array(
			// 正常系 : すべて正しい値（organization）を送信した場合。
			// Normal case: submitting all valid values (organization).
			array(
				'test_condition_name' => '正常系 : author_type が organization で全項目が正しい値の場合 => そのまま保存される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Vektor Inc.',
					'author_url'    => 'https://vektor-inc.co.jp/',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Vektor Inc.',
					'author_url'    => 'https://vektor-inc.co.jp/',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
			),
			// 正常系 : author_type が person の場合。
			// Normal case: author_type is person.
			array(
				'test_condition_name' => '正常系 : author_type が person で全項目が正しい値の場合 => そのまま保存される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'person',
					'author_name'   => 'Vekutarou',
					'author_url'    => 'https://vektor-inc.co.jp/author/vekutarou',
					'author_sameAs' => 'https://twitter.com/vekutarou',
				),
				'expected'            => array(
					'author_type'   => 'person',
					'author_name'   => 'Vekutarou',
					'author_url'    => 'https://vektor-inc.co.jp/author/vekutarou',
					'author_sameAs' => 'https://twitter.com/vekutarou',
				),
			),
			// 正常系（境界値） : メタが一度も保存されていない新規ユーザーが初めて保存する場合
			// （update_user_meta() の $prev_value が空文字になる、本番での主経路）=> 送信値がそのまま保存される。
			// Normal case (boundary): a fresh user whose meta has never been saved saves it for the first time
			// (the $prev_value argument to update_user_meta() being an empty string is the main path in production) -> the submitted values are saved as-is.
			array(
				'test_condition_name' => '正常系 : メタ未設定の新規ユーザーが初めて保存する場合 => 送信値がそのまま保存される',
				'existing_meta'       => array(),
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'New User',
					'author_url'    => 'https://example.com/new',
					'author_sameAs' => 'https://example.com/new-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'New User',
					'author_url'    => 'https://example.com/new',
					'author_sameAs' => 'https://example.com/new-sns',
				),
			),
			// 異常系（境界値） : author_type が空文字（ホワイトリスト外）の場合 => 保存されず既存値が維持される。
			// Abnormal case (boundary): author_type is an empty string (outside the whitelist) -> not saved, the existing value is kept.
			array(
				'test_condition_name' => '異常系 : author_type が空文字の場合 => 保存されず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => '',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系 : author_type にタグ入りの不正な値が届いた場合 => 保存されず既存値が維持される。
			// Abnormal case: author_type receives an invalid value containing a tag -> not saved, the existing value is kept.
			array(
				'test_condition_name' => '異常系 : author_type にホワイトリスト外の値（タグ入り）が届いた場合 => 保存されず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => '<script>alert(1)</script>',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系 : author_name にタグ入り文字列（属性に仕込んだ XSS）が届いた場合
			// => sanitize_text_field() でタグごと除去され、リテラル 'Vektor' として保存される。
			// Abnormal case: author_name receives a string containing a tag (XSS embedded in an attribute)
			// -> the tag itself is stripped via sanitize_text_field() and saved as the literal 'Vektor'.
			array(
				'test_condition_name' => '異常系 : author_name にタグ入り文字列が届いた場合 => タグが除去され Vektor が保存される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => '<img src=x onerror=alert(1)>Vektor',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Vektor',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系（メタ未設定から） : author_url に許可されないスキーム（javascript:）の URL が届いた場合
			// => 保存されず、メタは未設定のまま（get_user_meta() の既定値である空文字が返る）。
			// esc_url_raw() を直接呼んでリテラルを算出せず、'' を直書きすることで
			// 「不正なスキームが確実に弾かれる」ことそのものを検証する。
			// Abnormal case (starting with no saved meta): author_url receives a URL with a disallowed scheme
			// (javascript:) -> it is not saved, and the meta remains unset (get_user_meta() returns its
			// default, an empty string). The literal '' is hardcoded instead of calling esc_url_raw() here,
			// to actually assert that the disallowed scheme is rejected.
			array(
				'test_condition_name' => '異常系 : メタ未設定の状態で author_url に不正なスキームの URL が届いた場合 => 保存されずメタ未設定のまま',
				'existing_meta'       => array(),
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'New User',
					'author_url'    => 'javascript:alert(1)',
					'author_sameAs' => 'https://example.com/new-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'New User',
					'author_url'    => '',
					'author_sameAs' => 'https://example.com/new-sns',
				),
			),
			// 異常系 : author_sameAs に許可されないスキーム（javascript:）の URL が届いた場合
			// => 保存されず既存値が維持される（author_url と揃えた挙動）。
			// Abnormal case: author_sameAs receives a URL with a disallowed scheme (javascript:)
			// -> it is not saved and the existing value is kept (behavior aligned with author_url).
			array(
				'test_condition_name' => '異常系 : author_sameAs に不正なスキームの URL が届いた場合 => 保存されず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'javascript:alert(document.cookie)',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 正常系（意図的な空欄クリア） : author_url に空文字が届いた場合 => サニタイズ後の値（空文字）で保存される。
			// docblock の「送信値自体が空（意図的な空欄クリア）の場合はサニタイズ後の値（空文字）で保存される」を検証する。
			// Normal case (intentional clear): author_url receives an empty string -> saved as the sanitized (empty) value.
			// Verifies the docblock's statement that an intentional clear (the submitted value itself is empty) is saved as the sanitized (empty) value.
			array(
				'test_condition_name' => '正常系 : author_url が空文字（意図的な空欄クリア）で送信された場合 => 空文字で保存される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => '',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => '',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系（回帰テスト） : author_url に <script>...</script> が届いた場合 => 保存されず既存値が維持される。
			// sanitize_text_field() は script タグを中身ごと除去するため、以前の実装ではこの非空の不正値が
			// 「意図的な空欄クリア」と誤判定され、既存値が消えてしまう不具合があった（trim() のみで判定するよう修正済み）。
			// Abnormal case (regression test): author_url receives <script>...</script> -> not saved, the existing value is kept.
			// sanitize_text_field() strips a script tag together with its content, so the previous implementation
			// misdetected this non-empty invalid value as an intentional clear and lost the existing value
			// (fixed by detecting emptiness via trim() alone instead).
			array(
				'test_condition_name' => '異常系 : author_url に <script>...</script> が届いた場合 => 保存されず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => '<script>javascript:alert(1)</script>',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系（回帰テスト） : author_url に <> が届いた場合 => 保存されず既存値が維持される。
			// sanitize_text_field() と esc_url_raw() の双方でこの文字列が空文字になり誤判定に至っていた境界ケース。
			// Abnormal case (regression test): author_url receives <> -> not saved, the existing value is kept.
			// A boundary case where both sanitize_text_field() and esc_url_raw() used to reduce this string to
			// an empty string, causing the misdetection.
			array(
				'test_condition_name' => '異常系 : author_url に <> が届いた場合 => 保存されず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => '<>',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系（回帰テスト） : author_url の前後に空白が付いた正しい URL が届いた場合
			// => 前後の空白が trim() で除去されてから esc_url_raw() に渡され、%20 を含まずに保存される。
			// 修正前は末尾の空白が esc_url_raw() によって %20 に変換され、URL の一部として保存されてしまっていた。
			// Abnormal case (regression test): a valid URL with leading/trailing whitespace is submitted
			// -> the whitespace is trim()'d before being passed to esc_url_raw(), and it is saved without %20.
			// Before the fix, the trailing space was converted into %20 by esc_url_raw() and saved as part of the URL.
			array(
				'test_condition_name' => '異常系 : author_url の前後に空白が付いた URL が届いた場合 => %20 を含まずに保存される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => '  https://ok.example/  ',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://ok.example/',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系 : author_url に配列が送信された場合 => Fatal error にならず、is_string() チェックにより
			// サニタイズ対象外として無視され、既存値が維持される。
			// Abnormal case: author_url receives an array -> no Fatal error occurs; the is_string() guard
			// excludes it from sanitization and the existing value is kept.
			array(
				'test_condition_name' => '異常系 : author_url に配列が送信された場合 => Fatal error にならず既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => array( 'x' ),
					'author_sameAs' => 'https://example.com/existing-sns',
				),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
			// 異常系 : 4項目とも一切送信されない場合（$_POST が空）=> isset() が false になり、
			// 4項目すべての既存値がそのまま維持される。
			// Abnormal case: none of the 4 fields are submitted at all (empty $_POST) -> isset() is false for
			// all of them, so the existing value of all 4 fields is kept as-is.
			array(
				'test_condition_name' => '異常系 : 4項目とも送信されない場合 => 4項目とも既存値が維持される',
				'existing_meta'       => $default_existing_meta,
				'post'                => array(),
				'expected'            => array(
					'author_type'   => 'organization',
					'author_name'   => 'Existing Name',
					'author_url'    => 'https://example.com/existing',
					'author_sameAs' => 'https://example.com/existing-sns',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			// 各ケースの前提として、既存の保存済みユーザーメタを設定する（空配列の場合は何も設定せず未保存状態から始める）。
			// Set up the existing saved user meta as the premise for each case (an empty array sets nothing, starting from an unsaved state).
			foreach ( $case['existing_meta'] as $key => $value ) {
				update_user_meta( $user_id, $key, $value );
			}

			// フォーム送信を再現するため $_POST を差し替える。
			// Replace $_POST to reproduce a form submission.
			$_POST = $case['post'];

			// profile_update フックが渡す「更新前のユーザーデータ」を再現する。
			// WP_User の未定義プロパティは、コア（WP_User::__get()）が get_user_meta() へフォールバックするため、
			// 現在保存されているユーザーメタが $old_user_data->author_type 等として参照できる。
			// Reproduce the "pre-update user data" passed by the profile_update hook.
			// WP_User's undefined properties fall back to get_user_meta() via core (WP_User::__get()),
			// so the currently saved user meta can be referenced as $old_user_data->author_type, etc.
			$old_user_data = get_userdata( $user_id );

			VK_Article_Srtuctured_Data::update_author_structure_data( $user_id, $old_user_data );

			$actual = array(
				'author_type'   => get_user_meta( $user_id, 'author_type', true ),
				'author_name'   => get_user_meta( $user_id, 'author_name', true ),
				'author_url'    => get_user_meta( $user_id, 'author_url', true ),
				'author_sameAs' => get_user_meta( $user_id, 'author_sameAs', true ),
			);

			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );

			// 後始末（$_POST とユーザーメタを次のケースのために初期化する）。
			// Clean up ($_POST and the user meta, to prepare for the next case).
			$_POST = array();
			delete_user_meta( $user_id, 'author_type' );
			delete_user_meta( $user_id, 'author_name' );
			delete_user_meta( $user_id, 'author_url' );
			delete_user_meta( $user_id, 'author_sameAs' );
		}

		// current user とテストで発行したユーザーを後始末する。
		// Clean up the current user and the user created for the test.
		wp_set_current_user( 0 );
		wp_delete_user( $user_id );
	}

	/**
	 * update_author_structure_data() に追加した capability チェック（current_user_can( 'edit_user', $user_id )）を検証する。
	 * 対象ユーザーを編集する権限を持たないユーザーが呼び出した場合、保存は行われず既存値が維持されることを確認する。
	 *
	 * Verify the capability check (current_user_can( 'edit_user', $user_id )) added to update_author_structure_data().
	 * When called by a user who lacks permission to edit the target user, verify that nothing is saved and the existing value is kept.
	 */
	function test_update_author_structure_data_requires_capability() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_update_author_structure_data_requires_capability' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 更新対象のユーザー。
		// The target user to be updated.
		$target_user_id = wp_insert_user(
			array(
				'user_login'   => 'capability_target_user',
				'user_pass'    => 'user_pass',
				'display_name' => 'Capability Target User',
			)
		);

		// 対象ユーザーを編集する権限を持たない、購読者ロールのユーザー。
		// A subscriber-role user who has no permission to edit the target user.
		$subscriber_user_id = wp_insert_user(
			array(
				'user_login'   => 'capability_subscriber_user',
				'user_pass'    => 'user_pass',
				'display_name' => 'Capability Subscriber User',
				'role'         => 'subscriber',
			)
		);

		$existing_meta = array(
			'author_type'   => 'organization',
			'author_name'   => 'Existing Name',
			'author_url'    => 'https://example.com/existing',
			'author_sameAs' => 'https://example.com/existing-sns',
		);
		foreach ( $existing_meta as $key => $value ) {
			update_user_meta( $target_user_id, $key, $value );
		}

		// 購読者ユーザーとしてログインし、対象ユーザーの構造化データを書き換えようとする。
		// Log in as the subscriber user and attempt to overwrite the target user's structured data.
		wp_set_current_user( $subscriber_user_id );

		$_POST = array(
			'author_type'   => 'person',
			'author_name'   => 'Hijacked Name',
			'author_url'    => 'https://evil.example.com/',
			'author_sameAs' => 'https://evil.example.com/sns',
		);

		$old_user_data = get_userdata( $target_user_id );

		VK_Article_Srtuctured_Data::update_author_structure_data( $target_user_id, $old_user_data );

		$actual = array(
			'author_type'   => get_user_meta( $target_user_id, 'author_type', true ),
			'author_name'   => get_user_meta( $target_user_id, 'author_name', true ),
			'author_url'    => get_user_meta( $target_user_id, 'author_url', true ),
			'author_sameAs' => get_user_meta( $target_user_id, 'author_sameAs', true ),
		);

		$this->assertEquals( $existing_meta, $actual, '権限を持たないユーザーからの呼び出しでは、既存値が維持され上書きされない' );

		// 後始末。
		// Clean up.
		$_POST = array();
		wp_set_current_user( 0 );
		wp_delete_user( $target_user_id );
		wp_delete_user( $subscriber_user_id );
	}


	function test_get_article_structure_array() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_get_article_structure_array' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テスト用ユーザーを発行 //////////////////////////

		$test_users = array(
			'person_01' => array(
				'user_data' => array(
					'user_login'   => 'login_person',
					'user_pass'    => 'password',
					'display_name' => 'Display_Person',
				),
				'user_meta' => array(
					'author_type'   => 'person',
					'author_name'   => 'Author Person ',
					'author_url'    => 'https://person.jp',
					'author_sameAs' => 'https://twitter.com/person',
				),
			),
			'org_02'    => array(
				'user_data' => array(
					'user_login'   => 'login_org',
					'user_pass'    => 'password',
					'display_name' => 'Display_Org',
				),
				'user_meta' => array(
					'author_type'   => 'organization',
					'author_name'   => 'Author Org',
					'author_url'    => 'https://org.jp/',
					'author_sameAs' => 'https://twitter.com/org',
				),
			),
		);

		foreach ( $test_users as $user_key => $user ) {

			// 発行したユーザーIDを、元の配列に格納
			$test_users[ $user_key ]['user_id'] = wp_insert_user( $user['user_data'], $user['user_data']['user_pass'] );

			// ユーザーメタを更新
			foreach ( $user['user_meta'] as $meta_field => $value ) {
				update_user_meta( $test_users[ $user_key ]['user_id'], $meta_field, $value );
			}
		}

		// テスト用投稿データ発行 //////////////////////////

		// apply_filters( 'post_thumbnail_url', $thumbnail_url, $post, $size );

		$test_data = array(
			// 正常系 : アイキャッチ未設定 => image キー自体が出力されない
			// Normal case: no featured image -> the image key itself is not output.
			array(
				'test_condition_name' => 'アイキャッチ未設定の場合 => image キーが出力されない',
				// 'target_url' => get_permalink( $data['post_id_person'] ),
				'post_data'           => array(
					'post_title'   => 'Post Person',
					'post_status'  => 'publish',
					'post_content' => 'Post Test',
					'post_author'  => $test_users['person_01']['user_id'],
				),
				// アイキャッチを設定しないケース。
				// Case where no featured image is set.
				'set_thumbnail'       => false,
				'correct'             => array(
					'@context'      => 'https://schema.org/',
					'@type'         => 'Article',
					'headline'      => 'Post Person',
					'datePublished' => 'ここは投稿作成してから上書きする',
					'dateModified'  => 'ここは投稿作成してから上書きする',
					'author'        => array(
						'@type'  => $test_users['person_01']['user_meta']['author_type'],
						'name'   => $test_users['person_01']['user_meta']['author_name'],
						'url'    => $test_users['person_01']['user_meta']['author_url'],
						'sameAs' => $test_users['person_01']['user_meta']['author_sameAs'],
					),
					// Google側で必須事項ではなく要件が不明確なのでコメントアウト。
					// 'publisher'        => array(
					// '@context'    => 'http://schema.org',
					// '@type'       => $test_users['person_01']['user_meta']['author_type'],
					// 'name'        => get_bloginfo( 'name' ),
					// 'description' => get_bloginfo( 'description' ),
					// 'logo'        => array(
					// '@type' => 'ImageObject',
					// 'url'   => get_custom_logo(),
					// ),
					// ),
				),
			),
			// 正常系 : アイキャッチ設定済み => image が ImageObject 形式（url/width/height 込み）で出力される（組織投稿の場合）
			// Normal case: featured image is set -> image is output as an ImageObject (with url/width/height) (organization post).
			array(
				'test_condition_name' => 'アイキャッチ設定済みの場合 => image が ImageObject 形式（url/width/height）で出力される',
				// 'target_url' => get_permalink( $data['post_id_org'] ),
				'post_data'           => array(
					'post_title'   => 'Post Org',
					'post_status'  => 'publish',
					'post_content' => 'Post Test Org',
					'post_author'  => $test_users['org_02']['user_id'],
				),
				// アイキャッチを設定するケース。実際の URL・実寸はループ内で correct に上書きする。
				// Case where a featured image is set. The actual URL and dimensions are overwritten into correct inside the loop.
				'set_thumbnail'       => true,
				'correct'             => array(
					'@context'      => 'https://schema.org/',
					'@type'         => 'Article',
					'headline'      => 'Post Org',
					// image はループ内で ImageObject 形式（url/width/height）に上書きする。
					// image is overwritten into the ImageObject format (url/width/height) inside the loop.
					'image'         => 'ここはアイキャッチ設定してから上書きする',
					'datePublished' => 'ここは投稿作成してから上書きする',
					'dateModified'  => 'ここは投稿作成してから上書きする',
					'author'        => array(
						'@type'  => $test_users['org_02']['user_meta']['author_type'],
						'name'   => $test_users['org_02']['user_meta']['author_name'],
						'url'    => $test_users['org_02']['user_meta']['author_url'],
						'sameAs' => $test_users['org_02']['user_meta']['author_sameAs'],
					),
					// Google側で必須事項ではなく要件が不明確なのでコメントアウト。
					// 'publisher'        => array(
					// '@context'    => 'http://schema.org',
					// '@type'       => $test_users['org_02']['user_meta']['author_type'],
					// 'name'        => get_bloginfo( 'name' ),
					// 'description' => get_bloginfo( 'description' ),
					// 'logo'        => array(
					// '@type' => 'ImageObject',
					// 'url'   => get_custom_logo(),
					// ),
					// ),
				),
			),
			// 異常系 : アイキャッチ設定済みだが実寸（width/height）が取得できない => url のみの ImageObject が出力される
			// Abnormal case: featured image is set but the dimensions (width/height) cannot be retrieved -> an ImageObject with url only is output.
			array(
				'test_condition_name' => 'アイキャッチ設定済みだが実寸が取得できない場合 => width/height を含まない ImageObject が出力される',
				'post_data'           => array(
					'post_title'   => 'Post Org No Size',
					'post_status'  => 'publish',
					'post_content' => 'Post Test Org No Size',
					'post_author'  => $test_users['org_02']['user_id'],
				),
				// アイキャッチは設定するが、フィルターで実寸を消した状態を再現する。
				// Set a featured image, but reproduce a state where the dimensions are stripped via a filter.
				'set_thumbnail'       => true,
				'strip_image_size'    => true,
				'correct'             => array(
					'@context'      => 'https://schema.org/',
					'@type'         => 'Article',
					'headline'      => 'Post Org No Size',
					// image はループ内で width/height なしの ImageObject に上書きする。
					// image is overwritten into an ImageObject without width/height inside the loop.
					'image'         => 'ここはアイキャッチ設定してから上書きする',
					'datePublished' => 'ここは投稿作成してから上書きする',
					'dateModified'  => 'ここは投稿作成してから上書きする',
					'author'        => array(
						'@type'  => $test_users['org_02']['user_meta']['author_type'],
						'name'   => $test_users['org_02']['user_meta']['author_name'],
						'url'    => $test_users['org_02']['user_meta']['author_url'],
						'sameAs' => $test_users['org_02']['user_meta']['author_sameAs'],
					),
				),
			),
			// 異常系 : アイキャッチ設定済みだが wp_get_attachment_image_src() が false（URL 取得失敗）を返す => image キー自体が出力されない
			// Abnormal case: featured image is set but wp_get_attachment_image_src() returns false (URL retrieval fails) -> the image key itself is not output.
			array(
				'test_condition_name' => 'アイキャッチ設定済みだが画像 URL が取得できない場合 => image キーが出力されない',
				'post_data'           => array(
					'post_title'   => 'Post Org No Url',
					'post_status'  => 'publish',
					'post_content' => 'Post Test Org No Url',
					'post_author'  => $test_users['org_02']['user_id'],
				),
				// アイキャッチは設定するが、フィルターで wp_get_attachment_image_src() を false に上書きした状態を再現する。
				// Set a featured image, but reproduce a state where wp_get_attachment_image_src() is forced to false via a filter.
				'set_thumbnail'       => true,
				'false_image_src'     => true,
				// image キーが出力されないことを検証するため、correct には image を含めない。
				// To verify the image key is not output, correct does not contain image.
				'correct'             => array(
					'@context'      => 'https://schema.org/',
					'@type'         => 'Article',
					'headline'      => 'Post Org No Url',
					'datePublished' => 'ここは投稿作成してから上書きする',
					'dateModified'  => 'ここは投稿作成してから上書きする',
					'author'        => array(
						'@type'  => $test_users['org_02']['user_meta']['author_type'],
						'name'   => $test_users['org_02']['user_meta']['author_name'],
						'url'    => $test_users['org_02']['user_meta']['author_url'],
						'sameAs' => $test_users['org_02']['user_meta']['author_sameAs'],
					),
				),
			),
			// 個別の投稿ページじゃないページで空で返ってきてるか？
			/**
			 * get_author_structure_array()はとにかくそのページの著者の配列データ$author_arrayを作る
			 * 個別投稿ではない固定ページやアーカイブページでも$author_arrayは作成されるが、
			 * 配列をjson形式でheadに出力する関数print_jsonLD_in_head()で投稿ページにのみ表示する仕様となっている。
			 * そのため、ここのテストでは$author_arrayの内容が空で返ってくることがない。
			 */
		);

		// 後始末用に発行した添付ファイルIDを保持する。
		// Keep created attachment IDs so they can be cleaned up afterward.
		$attachment_ids = array();

		foreach ( $test_data as $test_value ) {
			$target_post_id = wp_insert_post( $test_value['post_data'] );

			$test_value['correct']['datePublished'] = get_the_time( 'c', $target_post_id );
			$test_value['correct']['dateModified']  = get_the_modified_time( 'c', $target_post_id );

			// set_thumbnail が true のケースではダミー添付ファイルを作成しアイキャッチに設定する。
			// 元画像（フル解像度）の URL・実寸を取得し、ImageObject 形式の期待値（correct['image']）へ反映する。
			// For cases with set_thumbnail true, create a dummy attachment and set it as the featured image.
			// Retrieve the URL and the actual dimensions of the original (full-resolution) image and reflect them into the expected ImageObject value (correct['image']).
			$filter_callback = null;
			if ( ! empty( $test_value['set_thumbnail'] ) ) {
				$attachment_id = $this->factory->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $target_post_id );
				set_post_thumbnail( $target_post_id, $attachment_id );
				$attachment_ids[] = $attachment_id;

				// strip_image_size が true のケースでは、wp_get_attachment_image_src の width/height を 0 に潰し、
				// 実寸が取得できない状況を再現する（width/height キーが出ないことを検証するため）。
				// For the strip_image_size case, force the width/height of wp_get_attachment_image_src to 0
				// to reproduce a situation where the dimensions cannot be retrieved (to verify the width/height keys are omitted).
				if ( ! empty( $test_value['false_image_src'] ) ) {
					// false_image_src が true のケースでは、wp_get_attachment_image_src を false に潰し、
					// URL（および実寸）が一切取得できない状況を再現する（image キー自体が出ないことを検証するため）。
					// For the false_image_src case, force wp_get_attachment_image_src to false
					// to reproduce a situation where the URL (and dimensions) cannot be retrieved at all (to verify the image key itself is omitted).
					$filter_callback = function () {
						return false;
					};
					add_filter( 'wp_get_attachment_image_src', $filter_callback );

					// このケースでは correct に image を含めないため、上書きは行わない。
					// In this case correct does not contain image, so no overwrite is performed.
				} elseif ( ! empty( $test_value['strip_image_size'] ) ) {
					$filter_callback = function ( $image ) {
						if ( is_array( $image ) ) {
							$image[1] = 0;
							$image[2] = 0;
						}
						return $image;
					};
					add_filter( 'wp_get_attachment_image_src', $filter_callback );

					// 実寸を消したケースの期待値は url のみの ImageObject。
					// The expected value for the stripped-size case is an ImageObject with url only.
					// この期待値算出には実寸が必要なため、いったんフィルターを外して取得し、再度付け直す。
					// The expected value needs the real dimensions, so temporarily remove the filter to retrieve them, then add it back.
					remove_filter( 'wp_get_attachment_image_src', $filter_callback );
					$image_full = wp_get_attachment_image_src( $attachment_id, 'full' );
					add_filter( 'wp_get_attachment_image_src', $filter_callback );
					$test_value['correct']['image'] = array(
						'@type' => 'ImageObject',
						'url'   => $image_full[0],
					);
				} else {
					// 通常ケースの期待値は元画像（フル解像度）の url/width/height 込み ImageObject。
					// The expected value for the normal case is an ImageObject with url/width/height of the original (full-resolution) image.
					$image_full                     = wp_get_attachment_image_src( $attachment_id, 'full' );
					$test_value['correct']['image'] = array(
						'@type'  => 'ImageObject',
						'url'    => $image_full[0],
						'width'  => $image_full[1],
						'height' => $image_full[2],
					);
				}
			}

			// Move to test page
			$this->go_to( get_permalink( $target_post_id ) );

			$return  = VK_Article_Srtuctured_Data::get_article_structure_array();
			$correct = $test_value['correct'];

			// print PHP_EOL;
			// print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			// print 'return  ::::' . $return['author'] . PHP_EOL;

			$this->assertEquals( $correct, $return, $test_value['test_condition_name'] );

			// このケースで追加したフィルターを後始末する（後続ケースへ影響させない）。
			// Clean up the filter added in this case so it does not affect subsequent cases.
			if ( null !== $filter_callback ) {
				remove_filter( 'wp_get_attachment_image_src', $filter_callback );
			}

			// テスト投稿削除
			wp_delete_post( $target_post_id );
			// とりあえずトップに戻る
			$this->go_to( home_url() );
		}

		// テストで発行した添付ファイルを削除する。
		// Delete the attachments created during the test.
		foreach ( $attachment_ids as $attachment_id ) {
			wp_delete_attachment( $attachment_id, true );
		}

		// テストで発行したユーザーを削除 ///////////////////////////
		wp_delete_user( $test_users['person_01']['user_id'] );
		wp_delete_user( $test_users['org_02']['user_id'] );
		// wp_delete_post( $data['post_id_person'] );
		// wp_delete_post( $data['post_id_org'] );
	}
}
