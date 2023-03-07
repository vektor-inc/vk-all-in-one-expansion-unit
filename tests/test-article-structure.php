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
		$userdata = array(
			'user_login'  =>  'sutekivektor',
			'user_url'    => 'https://vektor-inc.co.jp',
			'user_pass'   =>  'user_pass',
			'display_name' => 'vekujirou'
		);
		$user_id = wp_insert_user( $userdata ) ;


		$test_data = array(
			// 独自実装のユーザー情報フィールド : すべて埋められている場合 && person
			array(
				'author' => array(
					'author_type' => 'person',
					'author_name' => 'vekutarou',
					'author_url' => 'https://vektor-inc.co.jp/author/vekutarou',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct' => array(
					"@type" => 'person',
					"name" =>  'vekutarou',
					"url" => 'https://vektor-inc.co.jp/author/vekutarou',
					"sameAs" => 'https://twitter.com/vektor_inc',
				),
			),
			// チェック対象 : url
			// 独自実装のユーザー情報フィールド : organization && url指定あり
			array(
				'author' => array(
					'author_type' => 'organization',
					'author_name' => 'vekutarou',
					'author_url' => 'https://vektor-inc.co.jp/',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct' => array(
					"@type" => 'organization',
					"name" =>  'vekutarou',
					"url" => 'https://vektor-inc.co.jp/',
					"sameAs" => 'https://twitter.com/vektor_inc',
				),
			),
			// チェック対象 : url
			// 独自実装のユーザー情報フィールド : organization && url指定なし
			array(
				'author' => array(
					'author_type' => 'organization',
					'author_name' => 'vekutarou',
					'author_url' => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct' => array(
					"@type" => 'organization',
					"name" =>  'vekutarou',
					"url" => home_url( '/' ),
					"sameAs" => 'https://twitter.com/vektor_inc',
				),
			),
			// チェック対象 : url
			// 独自実装のユーザー情報フィールド : person && url指定なし → 投稿者アーカイブのURL
			array(
				'author' => array(
					'author_type' => 'person',
					'author_name' => 'vekujirou',
					'author_url' => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct' => array(
					"@type" => 'person',
					"name" =>  'vekujirou',
					"url" => get_author_posts_url( $user_id ),
					"sameAs" => 'https://twitter.com/vektor_inc',
				),
			),
			// チェック対象 : name
			// 独自実装のユーザー情報フィールド : author_name 指定なし  → デフォルトのユーザーの表示名が適用されるか
			array(
				'author' => array(
					'author_type' => 'organization',
					'author_name' => '',
					'author_url' => '',
					'author_sameAs' => 'https://twitter.com/vektor_inc',
				),
				'correct' => array(
					"@type" => 'organization',
					"name" =>  'vekujirou',
					"url" => home_url( '/' ),
					"sameAs" => 'https://twitter.com/vektor_inc',
				),
			),
		);

		foreach( $test_data as $test_value) {

			foreach ( $test_value['author'] as $key => $value ){
				update_user_meta( $user_id, $key, $value );
			}

			$return = VK_Article_Srtuctured_Data::get_author_array( $user_id );
			$correct = $test_value['correct'];

			print PHP_EOL;

			print 'correct ::::' . PHP_EOL;
			var_dump( $correct );
			print 'return  ::::' . PHP_EOL;
			var_dump( $return );
			$this->assertEquals( $correct, $return );
		}

		// テストで発行したユーザーを削除
		wp_delete_user( $user_id ) ;
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
					'user_login'  =>  'login_person',
					'user_pass'   =>  'password',
					'display_name' => 'Display_Person'
				),
				'user_meta' => array(
					'author_type' => 'person',
					'author_name' => 'Author Person ',
					'author_url' => 'https://person.jp',
					'author_sameAs' => 'https://twitter.com/person',
				),
			),
			'org_02' => array(
				'user_data' => array(
					'user_login'  =>  'login_org',
					'user_pass'   =>  'password',
					'display_name' => 'Display_Org'
				),
				'user_meta' => array(
					'author_type' => 'organization',
					'author_name' =>  'Author Org',
					'author_url' => 'https://org.jp/',
					'author_sameAs' => 'https://twitter.com/org',
				),
			),
		);

		foreach ( $test_users as $user_key => $user ){

			// 発行したユーザーIDを、元の配列に格納
			$test_users[$user_key]['user_id']= wp_insert_user( $user['user_data'], $user['user_data']['user_pass'] );

			// ユーザーメタを更新
			foreach ( $user['user_meta'] as $meta_field => $value ){
				update_user_meta( $test_users[$user_key]['user_id'], $meta_field, $value );
			}
		}

		// テスト用投稿データ発行 //////////////////////////

		// apply_filters( 'post_thumbnail_url', $thumbnail_url, $post, $size );

		$test_data = array(
			array(
				// 'target_url' => get_permalink( $data['post_id_person'] ),
				'post_data' => array(
					'post_title'    => 'Post Person',
					'post_status'   => 'publish',
					'post_content'  => 'Post Test',
					'post_author' => $test_users['person_01']['user_id'],
				),
				'thumbnail_url' => 'https://image_person.com',
				'correct' => array(
					'@context' => 'https://schema.org/',
					'@type' => 'Article',
					'headline' => 'Post Person',
					'image' => '',
					'datePublished'    => 'ここは投稿作成してから上書きする',
					'dateModified'     => 'ここは投稿作成してから上書きする',
					'author'           => array(
					  '@type' => $test_users['person_01']['user_meta']['author_type'],
					  'name' => $test_users['person_01']['user_meta']['author_name'],
					  'url' => $test_users['person_01']['user_meta']['author_url'],
					  'sameAs' => $test_users['person_01']['user_meta']['author_sameAs'],
					),
					// Google側で必須事項ではなく要件が不明確なのでコメントアウト。
					// 'publisher'        => array(
					//   '@context'    => 'http://schema.org',
					//   '@type'       => $test_users['person_01']['user_meta']['author_type'],
					//   'name'        => get_bloginfo( 'name' ),
					//   'description' => get_bloginfo( 'description' ),
					//   'logo'        => array(
					// 	'@type' => 'ImageObject',
					// 	'url'   => get_custom_logo(),
					//   ),
					// ),
				),
			),
			// 組織投稿の場合
			array(
				// 'target_url' => get_permalink( $data['post_id_org'] ),
				'post_data' => array(
					'post_title'    => 'Post Org',
					'post_status'   => 'publish',
					'post_content'  => 'Post Test Org',
					'post_author' => $test_users['org_02']['user_id'],
				),
				'thumbnail_url' => 'https://image_org.com',
				'correct' => array(
					'@context' => 'https://schema.org/',
					'@type' => 'Article',
					'headline' => 'Post Org',
					'image' => '',
					'datePublished'    => 'ここは投稿作成してから上書きする',
					'dateModified'     => 'ここは投稿作成してから上書きする',
					'author'           => array(
						'@type' => $test_users['org_02']['user_meta']['author_type'],
						'name' => $test_users['org_02']['user_meta']['author_name'],
						'url' => $test_users['org_02']['user_meta']['author_url'],
						'sameAs' => $test_users['org_02']['user_meta']['author_sameAs'],
					),
					// Google側で必須事項ではなく要件が不明確なのでコメントアウト。
					// 'publisher'        => array(
					// 	'@context'    => 'http://schema.org',
					// 	'@type'       => $test_users['org_02']['user_meta']['author_type'],
					// 	'name'        => get_bloginfo( 'name' ),
					// 	'description' => get_bloginfo( 'description' ),
					// 	'logo'        => array(
					// 	'@type' => 'ImageObject',
					// 	'url'   => get_custom_logo(),
					// 	),
					// ),
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


		foreach ( $test_data as $test_value ){

			$target_post_id = wp_insert_post( $test_value['post_data'] );

			$test_value['correct']['datePublished'] = get_the_time( 'c', $target_post_id );
			$test_value['correct']['dateModified'] = get_the_modified_time( 'c', $target_post_id );

			// Move to test page
			$this->go_to( get_permalink( $target_post_id ) );

			// add_filter( 'post_thumbnail_url', function( $thumbnail_url ) use ( $test_value) {
			// 	$thumbnail_url = $test_value['thumbnail_url'];
			// 	return $thumbnail_url;
			// } );

			$return = VK_Article_Srtuctured_Data::get_article_structure_array();
			$correct = $test_value['correct'];

			print PHP_EOL;

			// print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			// print 'return  ::::' . $return['author'] . PHP_EOL;

			$this->assertEquals( $correct, $return );

			// テスト投稿削除
			wp_delete_post( $target_post_id );
			// とりあえずトップに戻る
			$this->go_to( home_url() );
		}

		// テストで発行したユーザーを削除 ///////////////////////////
		wp_delete_user( $test_users['person_01']['user_id'] ) ;
		wp_delete_user( $test_users['org_02']['user_id'] ) ;
		// wp_delete_post( $data['post_id_person'] );
		// wp_delete_post( $data['post_id_org'] );

	}
}
