<?php
/**
 * Class WidgetPage
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Call to Action test case.
 */
class SnsBtnsTest extends WP_UnitTestCase {

	/**
	 * SNSボタンを本文欄やフックで自動挿入するかしないかのテスト
	 */
	function test_veu_is_sns_btns_auto_insert() {
		$test_array = array(
			// 初期で自動挿入になっている。ブロックテーマが主流になったらこちらはデフォルトでオフに切り替えたい.
			'null'                => array(
				'vkExUnit_sns_options' => null,
				'expected'             => true,
			),
			'enableSnsBtns_false' => array(
				'vkExUnit_sns_options' => array( 'enableSnsBtns' => null ),
				'expected'             => false,
			),
			'enableSnsBtns_false' => array(
				'vkExUnit_sns_options' => array( 'enableSnsBtns' => true ),
				'expected'             => true,
			),
		);
		foreach ( $test_array as $key => $test_value ) {
			update_option( 'vkExUnit_sns_options', $test_value['vkExUnit_sns_options'] );
			$actual = veu_is_sns_btns_auto_insert();
			$this->assertEquals( $test_value['expected'], $actual );
		}
	}

	public function test_veu_is_sns_btns_display() {

		// 投稿タイプイベントを作成
		register_post_type(
			'event',
			array(
				'label'       => 'Event',
				'has_archive' => true,
				'public'      => true,
			)
		);

		// イベントにイベントカテゴリーを関連付け
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// カテゴリ「テスト」を追加
		$catarr              = array(
			'cat_name'          => 'Category Test',
			'category_nicename' => 'category-test',
		);
		$data['category_id'] = wp_insert_category( $catarr );

		// イベントカテゴリ「テスト」を追加
		$args                 = array(
			'slug' => 'event-category-test',
		);
		$term_info            = wp_insert_term( 'Event Category Test', 'event_cat', $args );
		$data['event_cat_id'] = $term_info['term_id'];

		// 投稿「テスト01」を追加
		$post               = array(
			'post_title'    => 'Post Test 01',
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 01',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_01'] = wp_insert_post( $post );
		add_post_meta( $data['post_id_01'], 'sns_share_botton_hide', true );

		// 投稿「テスト02」を追加
		$post               = array(
			'post_title'    => 'Post Test 02',
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 02',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_02'] = wp_insert_post( $post );
		add_post_meta( $data['post_id_02'], 'sns_share_botton_hide', false );

		// 固定ページ「テスト01」を追加
		$post               = array(
			'post_title'   => 'Page Test 01',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test 01',
		);
		$data['page_id_01'] = wp_insert_post( $post );
		add_post_meta( $data['page_id_01'], 'sns_share_botton_hide', true );

		// 固定ページ「テスト02」を追加
		$post               = array(
			'post_title'   => 'Page Test 02',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Page Test 02',
		);
		$data['page_id_02'] = wp_insert_post( $post );
		add_post_meta( $data['page_id_02'], 'sns_share_botton_hide', false );

		// イベント「テスト01」を追加
		$post                = array(
			'post_title'   => 'Event Test 01',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test 01',
		);
		$data['event_id_01'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_01'], 'event_category_name', 'event_cat' );
		add_post_meta( $data['event_id_01'], 'sns_share_botton_hide', true );

		// イベント「テスト02」を追加
		$post                = array(
			'post_title'   => 'Event Test 02',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'Event Test 02',
		);
		$data['event_id_02'] = wp_insert_post( $post );
		wp_set_object_terms( $data['event_id_02'], 'event_category_name', 'event_cat' );
		add_post_meta( $data['event_id_02'], 'sns_share_botton_hide', false );

		$test_array = array(
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => false,
					),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
				),
				'target_url' => get_permalink( $data['post_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'post' => true,
					),
				),
				'target_url' => get_permalink( $data['post_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => false,
					),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
				),
				'target_url' => get_permalink( $data['page_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'page' => true,
					),
				),
				'target_url' => get_permalink( $data['page_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => false,
					),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
				),
				'target_url' => get_permalink( $data['event_id_01'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
				),
				'target_url' => get_permalink( $data['event_id_02'] ),
				'correct'    => false,
			),
			array(
				'options'    => array(
					'snsBtn_exclude_post_types' => array(
						'event' => true,
					),
				),
				'target_url' => home_url( '/' ) . '?p=9999',
				'correct'    => false,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'SNS Button Display Condition Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		foreach ( $test_array as $test_value ) {

			// Set site name
			update_option( 'vkExUnit_sns_options', $test_value['options'] );
			$this->go_to( $test_value['target_url'] );
			$return  = veu_is_sns_btns_display();
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return );

			// print PHP_EOL;
			// print 'url     ::::' . $test_value['target_url'] . PHP_EOL;
			// print 'correct ::::' . $correct . PHP_EOL;
			// print 'return  ::::' . $return . PHP_EOL;

		}
	}

	/**
	 * シェアボタンの HTML 出力（各 SNS の表示 ON/OFF）のテスト
	 * Test for the share button HTML output ( display ON/OFF for each SNS ).
	 * ここでは Threads ボタンがオプションに応じて出力される事と、
	 * 各シェア URL のエスケープ（esc_url / esc_attr）を確認する
	 * Here we check that the Threads button is output according to the option,
	 * and that each share URL is escaped ( esc_url / esc_attr ).
	 */
	public function test_veu_get_sns_btns() {

		// テスト用の投稿を作成し、その投稿ページに遷移する（veu_is_sns_btns_display() を true にするため）
		// Create a test post and go to its page ( to make veu_is_sns_btns_display() return true ).
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Threads Button Test',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Threads Button Test',
			)
		);
		$this->go_to( get_permalink( $post_id ) );

		// Threads の intent URL の先頭部分（この文字列が出力に含まれれば Threads ボタンが出ている）
		// The beginning part of the Threads intent URL ( if the output contains this string, the Threads button is rendered ).
		$threads_intent = 'https://www.threads.net/intent/post?text=';

		$test_cases = array(
			array(
				'test_condition_name' => 'useThreads が true の場合 => Threads ボタン（sb_threads と intent URL）が出力に含まれる',
				'options'             => array( 'useThreads' => true ),
				'expected_contains'   => true,
			),
			array(
				'test_condition_name' => 'useThreads が false の場合 => Threads ボタンは出力に含まれない',
				'options'             => array( 'useThreads' => false ),
				'expected_contains'   => false,
			),
			array(
				'test_condition_name' => 'useThreads キーが未設定の場合 => デフォルト値（ON）が適用され Threads ボタンが出力に含まれる',
				'options'             => array(),
				'expected_contains'   => true,
			),
		);

		foreach ( $test_cases as $case ) {
			// オプション値を設定 / Set option value.
			update_option( 'vkExUnit_sns_options', $case['options'] );

			// シェアボタンの HTML を取得 / Get the share button HTML.
			$actual = veu_get_sns_btns();

			if ( $case['expected_contains'] ) {
				// Threads ボタンの li クラスと intent URL の両方が含まれる事を確認
				// Check that both the li class of the Threads button and the intent URL are included.
				$this->assertStringContainsString( 'sb_threads', $actual, $case['test_condition_name'] );
				$this->assertStringContainsString( $threads_intent, $actual, $case['test_condition_name'] );
				$this->assertStringContainsString( 'fa-threads', $actual, $case['test_condition_name'] );
			} else {
				// Threads ボタンが含まれない事を確認（li クラス・intent URL・アイコンの全てが出力されない）
				// Check that the Threads button is not included ( none of the li class, intent URL, or icon are output ).
				$this->assertStringNotContainsString( 'sb_threads', $actual, $case['test_condition_name'] );
				$this->assertStringNotContainsString( $threads_intent, $actual, $case['test_condition_name'] );
				$this->assertStringNotContainsString( 'fa-threads', $actual, $case['test_condition_name'] );
			}

			// オプション値をクリーンアップ / Clean up the option value.
			delete_option( 'vkExUnit_sns_options' );
		}

		// href の URL エスケープ（esc_url / esc_attr）が適用されている事のテスト
		// Test that URL escaping ( esc_url / esc_attr ) is applied to the href attributes.
		$test_cases = array(
			array(
				'test_condition_name' => 'useFacebook が true の場合 => href が esc_url でエスケープされ & が &#038; になる',
				'options'             => array( 'useFacebook' => true ),
				'expected_contains'   => array( '//www.facebook.com/sharer.php?src=bm&#038;u=', '&#038;t=' ),
			),
			array(
				'test_condition_name' => 'useTwitter が true の場合 => href が esc_url でエスケープされ & が &#038; になる',
				'options'             => array( 'useTwitter' => true ),
				'expected_contains'   => array( '//twitter.com/intent/tweet?url=', '&#038;text=' ),
			),
			array(
				'test_condition_name' => 'useHatena が true の場合 => href が esc_url でエスケープされ & が &#038; になる',
				'options'             => array( 'useHatena' => true ),
				'expected_contains'   => array( '//b.hatena.ne.jp/add?mode=confirm&#038;url=', '&#038;title=' ),
			),
			array(
				'test_condition_name' => 'useThreads が true の場合 => エスケープ後もタイトルと URL の間の改行 %0A が保持される（esc_url だと除去される境界値）',
				'options'             => array( 'useThreads' => true ),
				'expected_contains'   => array( 'https://www.threads.net/intent/post?text=', '%0A' ),
			),
			array(
				'test_condition_name' => 'useBluesky が true の場合 => エスケープ後もタイトルと URL の間の改行 %0A が保持される（esc_url だと除去される境界値）',
				'options'             => array( 'useBluesky' => true ),
				'expected_contains'   => array( 'https://bsky.app/intent/compose?text=', '%0A' ),
			),
		);

		foreach ( $test_cases as $case ) {
			// オプション値を設定 / Set option value.
			update_option( 'vkExUnit_sns_options', $case['options'] );

			// シェアボタンの HTML を取得 / Get the share button HTML.
			$actual = veu_get_sns_btns();

			// 期待する文字列が全て含まれる事を確認 / Check that all expected strings are included.
			foreach ( $case['expected_contains'] as $expected ) {
				$this->assertStringContainsString( $expected, $actual, $case['test_condition_name'] );
			}

			// オプション値をクリーンアップ / Clean up the option value.
			delete_option( 'vkExUnit_sns_options' );
		}

		// 装飾アイコン（Threads / Copy）の <i> に aria-hidden="true" が付き読み上げから除外される事のテスト
		// Test that the decorative icons ( Threads / Copy ) get aria-hidden="true" on their <i> and are hidden from screen readers.
		// アイコンアクセシビリティのフィルター有無に依存しない事を確かめるため、フィルターを外した状態で検証する。
		// Verify with the filter removed to confirm the attribute does not depend on the icon accessibility filter.
		remove_filter( 'the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ) );
		remove_filter( 'render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10 );

		$aria_cases = array(
			array(
				'test_condition_name' => 'useThreads が true の場合 => Threads の <i> に aria-hidden="true" が付く',
				'options'             => array( 'useThreads' => true ),
				'expected'            => '<i class="fa-brands fa-threads" aria-hidden="true"></i>',
			),
			array(
				'test_condition_name' => 'useCopy が true の場合 => Copy の <i> に aria-hidden="true" が付く',
				'options'             => array( 'useCopy' => true ),
				'expected'            => '<i class="fas fa-copy" aria-hidden="true"></i>',
			),
		);

		foreach ( $aria_cases as $case ) {
			// オプション値を設定 / Set option value.
			update_option( 'vkExUnit_sns_options', $case['options'] );

			// シェアボタンの HTML を取得 / Get the share button HTML.
			$actual = veu_get_sns_btns();

			// 装飾アイコンに aria-hidden が付いている事を確認 / Check the decorative icon has aria-hidden.
			$this->assertStringContainsString( $case['expected'], $actual, $case['test_condition_name'] );

			// オプション値をクリーンアップ / Clean up the option value.
			delete_option( 'vkExUnit_sns_options' );
		}

		// 自前 web フォント（vk_sns）の空 span アイコン（fb / x / bluesky / hatena / line）にも aria-hidden="true" が付き読み上げから除外される事のテスト
		// Test that the empty in-house web font ( vk_sns ) span icons ( fb / x / bluesky / hatena / line ) also get aria-hidden="true" and are hidden from screen readers.
		// class 直後・$icon_css の前に aria-hidden が入る実出力に合わせてリテラルで検証する。
		// Verify against the literal output where aria-hidden comes right after class and before $icon_css.
		// LINE はモバイル環境（wp_is_mobile() が true）かつ useLine が有効な時のみ出力されるため、ケースごとに user_agent を設定して分岐を通す。
		// LINE is only output on mobile ( wp_is_mobile() returns true ) with useLine enabled, so set user_agent per case to go through the branch.
		// iPhone の UA。文字列に "Mobile" を含むため wp_is_mobile() が true を返す。
		// iPhone UA. It contains "Mobile", so wp_is_mobile() returns true.
		$mobile_ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

		$webfont_cases = array(
			array(
				'test_condition_name' => 'useFacebook が true の場合 => Facebook の web フォント span に aria-hidden="true" が付く',
				'options'             => array( 'useFacebook' => true ),
				'user_agent'          => null,
				'expected_contains'   => array( '<span class="vk_icon_w_r_sns_fb icon_sns" aria-hidden="true"' ),
			),
			array(
				'test_condition_name' => 'useTwitter が true の場合 => X の web フォント span に aria-hidden="true" が付く',
				'options'             => array( 'useTwitter' => true ),
				'user_agent'          => null,
				'expected_contains'   => array( '<span class="vk_icon_w_r_sns_x_twitter icon_sns" aria-hidden="true"' ),
			),
			array(
				'test_condition_name' => 'useBluesky が true の場合 => Bluesky の web フォント span に aria-hidden="true" が付く',
				'options'             => array( 'useBluesky' => true ),
				'user_agent'          => null,
				'expected_contains'   => array( '<span class="vk_icon_w_r_sns_bluesky icon_sns" aria-hidden="true"' ),
			),
			array(
				'test_condition_name' => 'useHatena が true の場合 => Hatena の web フォント span に aria-hidden="true" が付く',
				'options'             => array( 'useHatena' => true ),
				'user_agent'          => null,
				'expected_contains'   => array( '<span class="vk_icon_w_r_sns_hatena icon_sns" aria-hidden="true"' ),
			),
			array(
				// LINE はモバイル UA で wp_is_mobile() を true にした時のみ出力される。sb_line が含まれる事＝モバイル分岐を実際に通った裏取り（他に出力経路が無い）。
				// LINE is only output when wp_is_mobile() is true via the mobile UA. Containing sb_line proves the mobile branch was actually taken ( no other output path ).
				'test_condition_name' => 'useLine が true かつモバイルの場合 => LINE ボタンが出力され LINE の web フォント span に aria-hidden="true" が付く',
				'options'             => array( 'useLine' => true ),
				'user_agent'          => $mobile_ua,
				'expected_contains'   => array( 'sb_line', '<span class="vk_icon_w_r_sns_line icon_sns" aria-hidden="true"' ),
			),
		);

		// アサーション失敗時も元の UA を確実に戻すため、ループ実行前に元の値を保持し try/finally で復元する。
		// Preserve the original UA before the loop and restore it in finally so it is restored even if an assertion fails.
		$original_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;

		try {
			foreach ( $webfont_cases as $case ) {
				// ケースごとに UA を設定（null なら未設定にする）。LINE はモバイル UA で分岐を通す。
				// Set the UA per case ( unset if null ). LINE goes through the branch with the mobile UA.
				if ( null === $case['user_agent'] ) {
					unset( $_SERVER['HTTP_USER_AGENT'] );
				} else {
					$_SERVER['HTTP_USER_AGENT'] = $case['user_agent'];
				}

				// オプション値を設定 / Set option value.
				update_option( 'vkExUnit_sns_options', $case['options'] );

				// シェアボタンの HTML を取得 / Get the share button HTML.
				$actual = veu_get_sns_btns();

				// 期待する文字列が全て含まれる事を確認（装飾アイコン span の aria-hidden 等）/ Check that all expected strings are included ( aria-hidden on the decorative span icon, etc. ).
				foreach ( $case['expected_contains'] as $expected ) {
					$this->assertStringContainsString( $expected, $actual, $case['test_condition_name'] );
				}

				// オプション値をクリーンアップ / Clean up the option value.
				delete_option( 'vkExUnit_sns_options' );
			}
		} finally {
			// テスト後は $_SERVER['HTTP_USER_AGENT'] を必ず元に戻す（未設定だったら unset）。
			// Always restore $_SERVER['HTTP_USER_AGENT'] after the test ( unset if it was not set ).
			if ( null === $original_ua ) {
				unset( $_SERVER['HTTP_USER_AGENT'] );
			} else {
				$_SERVER['HTTP_USER_AGENT'] = $original_ua;
			}
		}
	}

	/**
	 * Issue #1453: 「シェアボタンブロックを常に表示する」設定（snsBtn_block_ignore_exclude）と、
	 * 呼び出し文脈（'auto' = 自動挿入 / 'block' = シェアボタンブロック）による表示判定の違いをテストする。
	 * この関数の修正前は、投稿タイプ除外 ON かつ新設定 ON でもブロック文脈が常に false になり、
	 * 3番目のケース（ブロック文脈で表示される事）が失敗する ( red ).
	 *
	 * Issue #1453: Test the display judgement differences caused by the "Always display the share
	 * button block" option ( snsBtn_block_ignore_exclude ) and the calling context ( 'auto' for
	 * automatic insertion / 'block' for the share button block ). Before the fix, the 'block'
	 * context always returned false regardless of the new option, so the second case below
	 * ( expecting true ) fails ( red ).
	 */
	public function test_veu_is_sns_btns_display_block_context() {

		// 投稿タイプ post が除外対象になるケース用の投稿
		// A "post" type post used for the post type exclusion cases.
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Block Context Test 01',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Block Context Test 01',
			)
		);

		// 記事単位の非表示指定（post meta）が付いた投稿。新設定より常に優先される事を確認するため。
		// A post with the per-post hide meta set, to verify it always takes priority over the new option.
		$hidden_post_id = wp_insert_post(
			array(
				'post_title'   => 'Block Context Test 02',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Block Context Test 02',
			)
		);
		add_post_meta( $hidden_post_id, 'sns_share_botton_hide', true );

		$test_cases = array(
			array(
				'test_condition_name' => '投稿タイプ除外 ON かつ新設定 OFF の場合 => ブロック文脈でも非表示 ( false )',
				'options'             => array(
					'snsBtn_exclude_post_types'   => array( 'post' => true ),
					'snsBtn_block_ignore_exclude' => false,
				),
				'target_post_id'      => $post_id,
				'context'             => 'block',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '投稿タイプ除外 ON かつ新設定 ON の場合 => ブロック文脈では表示される ( true )',
				'options'             => array(
					'snsBtn_exclude_post_types'   => array( 'post' => true ),
					'snsBtn_block_ignore_exclude' => true,
				),
				'target_post_id'      => $post_id,
				'context'             => 'block',
				'expected'            => true,
			),
			array(
				'test_condition_name' => '投稿タイプ除外 ON かつ新設定 ON でも、自動挿入文脈（引数なし呼び出し）は従来どおり非表示のまま ( false )',
				'options'             => array(
					'snsBtn_exclude_post_types'   => array( 'post' => true ),
					'snsBtn_block_ignore_exclude' => true,
				),
				'target_post_id'      => $post_id,
				'context'             => null, // 引数なし呼び出し ( 'auto' 相当 ) を表す / represents a no-argument call ( equivalent to 'auto' ).
				'expected'            => false,
			),
			array(
				'test_condition_name' => '投稿タイプ除外 ON かつ新設定 ON でも、post meta の非表示指定がある記事はブロック文脈でも非表示 ( false )',
				'options'             => array(
					'snsBtn_exclude_post_types'   => array( 'post' => true ),
					'snsBtn_block_ignore_exclude' => true,
				),
				'target_post_id'      => $hidden_post_id,
				'context'             => 'block',
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {
			// オプション値を設定 / Set option value.
			update_option( 'vkExUnit_sns_options', $case['options'] );

			// 対象の投稿ページへ移動 / Go to the target post.
			$this->go_to( get_permalink( $case['target_post_id'] ) );

			// $case['context'] が null なら引数なしで呼び、既定値 'auto' の挙動を確認する。
			// If $case['context'] is null, call with no argument to verify the 'auto' default behavior.
			if ( null === $case['context'] ) {
				$actual = veu_is_sns_btns_display();
			} else {
				$actual = veu_is_sns_btns_display( $case['context'] );
			}

			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );

			// オプション値をクリーンアップ / Clean up the option value.
			delete_option( 'vkExUnit_sns_options' );
		}
	}

	/**
	 * Issue #1453: シェアボタンブロックが非表示と判定された場合の、公開画面（editor preview で
	 * ない、または REST リクエストでない）では通知ではなく空文字が返る事をテストする。
	 * 修正前は "context=edit" バイパスにより編集画面で実際のボタンが描画されてしまい、
	 * 公開画面との食い違いが編集者に伝わらなかった ( この不具合の再現テスト = red ).
	 * ここでは REST_REQUEST 定数を実際に define() しない（PHP の定数は undefine できず、
	 * 同じ PHPUnit プロセス内で後に実行される無関係なテスト — 実際に本テスト作成時、
	 * front page 関連のテストで発覚 — に影響が漏れてしまうため）。PHPUnit プロセスの中では
	 * REST_REQUEST は定義されていないので、$_GET['context'] = 'edit' を立てて編集権限のある
	 * ユーザーでアクセスしても、REST リクエストでない限り通知は出ない事を確認できる。
	 * veu_is_block_editor_preview() 自体の判定ロジック（REST + context + 権限）は
	 * test_veu_is_block_editor_preview() で、通知の中身は test_veu_sns_btns_editor_notice() で
	 * それぞれ REST_REQUEST を define() せずに個別にテストする。
	 *
	 * Issue #1453: Test that the front end ( not an editor preview, or not a REST request ) gets an
	 * empty string instead of the notice when the share button is judged hidden. Before the fix, the
	 * "context=edit" bypass rendered live buttons in the editor, hiding the front-end/editor mismatch
	 * from editors ( this is the regression test for that bug = red ). This test intentionally never
	 * calls `define( 'REST_REQUEST', true )` — PHP constants cannot be undefined, and doing so was
	 * found ( while writing this test ) to leak into unrelated front-page tests that run later in the
	 * same PHPUnit process. Since REST_REQUEST is never defined in the PHPUnit process, setting
	 * $_GET['context'] = 'edit' and logging in as a user with edit permission still must not produce
	 * the notice unless the request is an actual REST request — proving the REST gate itself is
	 * required, not just the context/permission checks. veu_is_block_editor_preview()'s own logic
	 * ( REST + context + permission ) is covered separately by test_veu_is_block_editor_preview() via
	 * its injectable $is_rest_request parameter, and the notice content is covered separately by
	 * test_veu_sns_btns_editor_notice() — neither of those needs to define REST_REQUEST either.
	 */
	public function test_veu_get_sns_btns_block_context_notice() {

		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Block Notice Test 01',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Block Notice Test 01',
			)
		);

		// 編集権限を持つユーザー ( 投稿の編集者 ) を用意する。
		// このユーザーでも REST リクエストでなければ通知が出ない事を確認するため。
		// Prepare a user with edit permission ( post author / editor ), to verify that even this user
		// does not get the notice unless the request is an actual REST request.
		$editor_user_id = self::factory()->user->create( array( 'role' => 'editor' ) );

		$test_cases = array(
			array(
				'test_condition_name' => '投稿タイプ除外で非表示、公開画面（$_GET[context] なし） => 何も出力されない',
				'is_editor_preview'   => false,
				'current_user_id'     => 0,
			),
			array(
				'test_condition_name' => '投稿タイプ除外で非表示、$_GET[context]=edit かつ編集権限があるユーザーでも、REST リクエストでなければ何も出力されない',
				'is_editor_preview'   => true,
				'current_user_id'     => $editor_user_id,
			),
		);

		// アサーション失敗時も元のユーザーへ確実に戻すため、ループ実行前に元の値を保持し try/finally で復元する.
		// Preserve the original current user before the loop and restore it in finally so it is
		// restored even if an assertion fails.
		$original_user_id = get_current_user_id();

		update_option( 'vkExUnit_sns_options', array( 'snsBtn_exclude_post_types' => array( 'post' => true ) ) );

		try {
			foreach ( $test_cases as $case ) {
				// 対象の投稿ページへ移動 / Go to the target post.
				$this->go_to( get_permalink( $post_id ) );

				// ケースごとに現在のユーザーを設定する ( 編集権限の有無を再現する ).
				// Set the current user per case ( to reproduce the presence/absence of edit permission ).
				wp_set_current_user( $case['current_user_id'] );

				// ブロックエディタのキャンバスからのリクエストを $_GET['context'] = 'edit' で再現する.
				// Simulate a request from the block editor canvas via $_GET['context'] = 'edit'.
				if ( $case['is_editor_preview'] ) {
					$_GET['context'] = 'edit';
				} else {
					unset( $_GET['context'] );
				}

				// シェアボタンブロックからの呼び出し ( 'context' => 'block' ) を再現する.
				// Simulate the call from the share button block ( 'context' => 'block' ).
				$actual = veu_get_sns_btns( array( 'context' => 'block' ) );

				$this->assertSame( '', $actual, $case['test_condition_name'] );
			}
		} finally {
			// 後片付け： $_GET['context'] と現在のユーザーを必ず元へ戻す.
			// Clean up: always restore $_GET['context'] and the current user.
			unset( $_GET['context'] );
			wp_set_current_user( $original_user_id );
			delete_option( 'vkExUnit_sns_options' );
		}
	}

	/**
	 * Issue #1453 code review ( 安藤 ): veu_is_block_editor_preview() の判定ロジック
	 * ( REST リクエストである事 / context=edit である事 / 編集権限を持つ事 ) をテストする。
	 * REST_REQUEST 定数を define() せずに済むよう、テスト用の $is_rest_request 引数を使う
	 * （理由は test_veu_get_sns_btns_block_context_notice() のコメントを参照）。
	 *
	 * Issue #1453 code review ( security ): Test veu_is_block_editor_preview()'s gating logic
	 * ( must be a REST request / context=edit / user has edit permission ). Uses the test-only
	 * $is_rest_request argument instead of defining the REST_REQUEST constant ( see the comment on
	 * test_veu_get_sns_btns_block_context_notice() for why ).
	 */
	public function test_veu_is_block_editor_preview() {

		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Editor Preview Test 01',
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_content' => 'Editor Preview Test 01',
			)
		);

		$editor_user_id     = self::factory()->user->create( array( 'role' => 'editor' ) );
		$subscriber_user_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );

		$test_cases = array(
			array(
				'test_condition_name' => 'REST リクエストかつ context=edit かつ編集権限あり => true',
				'is_rest_request'     => true,
				'context'             => 'edit',
				'current_user_id'     => $editor_user_id,
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'REST リクエストかつ context=edit でも、編集権限が無いユーザー ( 購読者 ) => false',
				'is_rest_request'     => true,
				'context'             => 'edit',
				'current_user_id'     => $subscriber_user_id,
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'REST リクエストかつ context=edit でも、未ログイン ( 匿名 ) ユーザー => false',
				'is_rest_request'     => true,
				'context'             => 'edit',
				'current_user_id'     => 0,
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'REST リクエストで編集権限があっても、context が edit でなければ => false',
				'is_rest_request'     => true,
				'context'             => 'view',
				'current_user_id'     => $editor_user_id,
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'context=edit かつ編集権限があっても、REST リクエストでなければ => false',
				'is_rest_request'     => false,
				'context'             => 'edit',
				'current_user_id'     => $editor_user_id,
				'expected'            => false,
			),
		);

		$original_user_id = get_current_user_id();

		try {
			foreach ( $test_cases as $case ) {
				$this->go_to( get_permalink( $post_id ) );
				wp_set_current_user( $case['current_user_id'] );

				if ( null === $case['context'] ) {
					unset( $_GET['context'] );
				} else {
					$_GET['context'] = $case['context'];
				}

				$actual = veu_is_block_editor_preview( $case['is_rest_request'] );

				$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
			}
		} finally {
			unset( $_GET['context'] );
			wp_set_current_user( $original_user_id );
		}
	}

	/**
	 * Issue #1453 code review ( 植草 ): 編集画面通知の文言・内容をテストする。
	 * 主語が「記事」ではなく「シェアボタン（ブロック）」になっている事、投稿タイプ除外の場合は
	 * 設定画面への実リンクが含まれる事、post meta の場合はリンクが無い事を確認する。
	 *
	 * Issue #1453 code review ( UX ): Test the editor notice's wording and content. Verifies the
	 * subject is "the share button" / "the share button block", not "this post", and that the post
	 * type exclusion reason includes a real link to the settings screen while the per-post reason
	 * does not.
	 */
	public function test_veu_sns_btns_editor_notice() {

		$test_cases = array(
			array(
				'test_condition_name'   => "reason = 'post_meta' の場合 => 主語が share button の通知が返り、設定画面へのリンクは含まれない",
				'reason'                => 'post_meta',
				'expected_contains'     => array( 'veu_share_button_block-notice', 'The share button will not appear on the front end' ),
				'expected_not_contains' => array( 'This post will not appear', 'This post type will not appear', admin_url( 'admin.php?page=vkExUnit_main_setting' ) ),
			),
			array(
				'test_condition_name'   => "reason = 'post_type' の場合 => 主語が share button block の通知と、設定画面への実リンクが返る",
				'reason'                => 'post_type',
				'expected_contains'     => array( 'veu_share_button_block-notice', 'The share button block will not appear on the front end', admin_url( 'admin.php?page=vkExUnit_main_setting' ), 'target="_blank"' ),
				'expected_not_contains' => array( 'This post type will not appear', 'This post will not appear' ),
			),
			array(
				'test_condition_name'   => "reason = '404' の場合 => 専用メッセージが無いため空文字",
				'reason'                => '404',
				'expected_contains'     => array(),
				'expected_not_contains' => array( 'veu_share_button_block-notice' ),
			),
			array(
				'test_condition_name'   => "reason = '' ( 非表示でない ) の場合 => 空文字",
				'reason'                => '',
				'expected_contains'     => array(),
				'expected_not_contains' => array( 'veu_share_button_block-notice' ),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = veu_sns_btns_editor_notice( $case['reason'] );

			foreach ( $case['expected_contains'] as $expected ) {
				$this->assertStringContainsString( $expected, $actual, $case['test_condition_name'] );
			}
			foreach ( $case['expected_not_contains'] as $not_expected ) {
				$this->assertStringNotContainsString( $not_expected, $actual, $case['test_condition_name'] );
			}
		}

		// reason が '404' / '' の場合は通知が無い ( 空文字 ) 事も明示的に確認する.
		// Also explicitly confirm reason '404' / '' return an empty string ( no notice ).
		$this->assertSame( '', veu_sns_btns_editor_notice( '404' ), "reason = '404' => 空文字" );
		$this->assertSame( '', veu_sns_btns_editor_notice( '' ), "reason = '' => 空文字" );
	}
}
