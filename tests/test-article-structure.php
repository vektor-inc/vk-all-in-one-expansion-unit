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
				if ( ! empty( $test_value['strip_image_size'] ) ) {
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
					$image_full                     = wp_get_attachment_image_src( $attachment_id, 'full' );
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
