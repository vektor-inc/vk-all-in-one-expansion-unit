<?php
/**
 * Promotion Alert Test
 *
 * @package VK All in One Expansion Unit
 */

/**
 * Promotion Alert Test
 */
class PromotionAlertTest extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();

		// アイコンアクセサビリティのテストが影響するのでフィルターを外す
		remove_filter('the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ));
		remove_filter('render_block', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10);
	}

    /**
     * テストデータ作成
     */
    public function setup_data() {
        
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
		$catarr             = array(
			'cat_name'          => 'Category Test',
			'category_nicename' => 'category-test',
		);
		$data['category_id'] = wp_insert_category( $catarr );

		// イベントカテゴリ「テスト」を追加
		$args          = array(
			'slug' => 'event-category-test',
		);
		$term_info     = wp_insert_term( 'Event Category Test', 'event_cat', $args );
		$data['event_cat_id'] = $term_info['term_id'];

		// 投稿「テスト01」を追加
		$post    = array(
			'post_title'    => 'Post Test 01',
            'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 01',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_01'] = wp_insert_post( $post );
        add_post_meta( $data['post_id_01'], 'veu_display_promotion_alert', 'common' );

        // 投稿「テスト02」を追加
		$post    = array(
			'post_title'    => 'Post Test 02',
            'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 02',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_02'] = wp_insert_post( $post );
        add_post_meta( $data['post_id_02'], 'veu_display_promotion_alert', 'display' );

        // 投稿「テスト03」を追加
		$post    = array(
			'post_title'    => 'Post Test 03',
            'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_content'  => 'Post Test 03',
			'post_category' => array( $data['category_id'] ),
		);
		$data['post_id_03'] = wp_insert_post( $post );
        add_post_meta( $data['post_id_03'], 'veu_display_promotion_alert', 'hide' );

		// 固定ページ「テスト0１」を追加
		$post    = array(
			'post_title'    => 'Page Test 01',
            'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'Page Test 01',
		);
		$data['page_id_01'] = wp_insert_post( $post );
        add_post_meta( $data['page_id_01'], 'veu_display_promotion_alert', 'common' );

		// 固定ページ「テスト02」を追加
		$post    = array(
			'post_title'    => 'Page Test 02',
            'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'Page Test 02',
		);
		$data['page_id_02'] = wp_insert_post( $post );
        add_post_meta( $data['page_id_02'], 'veu_display_promotion_alert', 'display' );

		// 固定ページ「テスト03」を追加
		$post    = array(
			'post_title'    => 'Page Test 03',
            'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_content'  => 'Page Test 03',
		);
		$data['page_id_03'] = wp_insert_post( $post );
        add_post_meta( $data['page_id_03'], 'veu_display_promotion_alert', 'hide' );

		// イベント「テスト01」を追加
		$post    = array(
			'post_title'    => 'Event Test 01',
            'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_content'  => 'Event Test 01',
		);
		$data['event_id_01'] = wp_insert_post( $post );
        wp_set_object_terms( $data['event_id_01'], 'event_category_name', 'event_cat' );
        add_post_meta( $data['event_id_01'], 'veu_display_promotion_alert', 'common' );

        // イベント「テスト02」を追加
		$post    = array(
			'post_title'    => 'Event Test 02',
            'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_content'  => 'Event Test 02',
		);
		$data['event_id_02'] = wp_insert_post( $post );
        wp_set_object_terms( $data['event_id_02'], 'event_category_name', 'event_cat' );
        add_post_meta( $data['event_id_02'], 'veu_display_promotion_alert', 'display' );

        // イベント「テスト03」を追加
		$post    = array(
			'post_title'    => 'Event Test 03',
            'post_type'     => 'event',
			'post_status'   => 'publish',
			'post_content'  => 'Event Test 03',
		);
		$data['event_id_03'] = wp_insert_post( $post );
        wp_set_object_terms( $data['event_id_03'], 'event_category_name', 'event_cat' );
        add_post_meta( $data['event_id_03'], 'veu_display_promotion_alert', 'hide' );

        return $data;
		
    }

    public function test_get_display_condition() {

        $data = self::setup_data();

        $test_array = array(
			/* 投稿タイプ：投稿 */
			// オプション：表示、メタ：共通
			array(
				'post_id' => $data['post_id_01'],
				'options' => array(
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：共通
			array(
				'post_id' => $data['post_id_01'],
				'options' => array(
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => false,
			),
			// オプション：表示、メタ：表示
			array(
				'post_id' => $data['post_id_02'],
				'options' => array(
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：表示
			array(
				'post_id' => $data['post_id_02'],
				'options' => array(
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => true,
			),
			// オプション：表示、メタ：非表示
			array(
				'post_id' => $data['post_id_03'],
				'options' => array(
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => false,
			),
			// オプション：非表示、メタ：非表示
			array(
				'post_id' => $data['post_id_03'],
				'options' => array(
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => false,
			),
			/* 投稿タイプ：固定ページ */
			// オプション：表示、メタ：共通
			array(
				'post_id' => $data['page_id_01'],
				'options' => array(
					'alert-display' => array(
						'page' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：共通
			array(
				'post_id' => $data['page_id_01'],
				'options' => array(
					'alert-display' => array(
						'page' => 'hide',
					)
				),
				'correct' => false,
			),
			// オプション：表示、メタ：表示
			array(
				'post_id' => $data['page_id_02'],
				'options' => array(
					'alert-display' => array(
						'page' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：表示
			array(
				'post_id' => $data['page_id_02'],
				'options' => array(
					'alert-display' => array(
						'page' => 'hide',
					)
				),
				'correct' => true,
			),
			// オプション：表示、メタ：非表示
			array(
				'post_id' => $data['page_id_03'],
				'options' => array(
					'alert-display' => array(
						'page' => 'display',
					)
				),
				'correct' => false,
			),
			// オプション：非表示、メタ：非表示
			array(
				'post_id' => $data['page_id_03'],
				'options' => array(
					'alert-display' => array(
						'page' => 'hide',
					)
				),
				'correct' => false,
			),
			/* 投稿タイプ：イベント */
			// オプション：表示、メタ：共通
			array(
				'post_id' => $data['event_id_01'],
				'options' => array(
					'alert-display' => array(
						'event' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：共通
			array(
				'post_id' => $data['event_id_01'],
				'options' => array(
					'alert-display' => array(
						'event' => 'hide',
					)
				),
				'correct' => false,
			),
			// オプション：表示、メタ：表示
			array(
				'post_id' => $data['event_id_02'],
				'options' => array(
					'alert-display' => array(
						'event' => 'display',
					)
				),
				'correct' => true,
			),
			// オプション：非表示、メタ：表示
			array(
				'post_id' => $data['event_id_02'],
				'options' => array(
					'alert-display' => array(
						'event' => 'hide',
					)
				),
				'correct' => true,
			),
			// オプション：表示、メタ：非表示
			array(
				'post_id' => $data['event_id_03'],
				'options' => array(
					'alert-display' => array(
						'event' => 'display',
					)
				),
				'correct' => false,
			),
			// オプション：非表示、メタ：非表示
			array(
				'post_id' => $data['event_id_03'],
				'options' => array(
					'alert-display' => array(
						'event' => 'hide',
					)
				),
				'correct' => false,
			),
        );

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'Promotion Alert Condition Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		foreach ( $test_array as $test_value ) {

			// Set site name
			update_option( 'vkExUnit_PA', $test_value['options'] );

			$return  = VEU_Promotion_Alert::get_display_condition( $test_value['post_id'] );
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return );

			// print PHP_EOL;
			// print 'correct ::::' . $correct . PHP_EOL;
			// print 'return  ::::' . $return . PHP_EOL;

		}

    }

	public function test_get_alert_content() {

	

		$data = self::setup_data();

        $test_array = array(
			array(
				'options' => array(
					'alert-text'    => '',
					'alert-content' => '',
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'alert-text'    => 'aaaa',
					'alert-content' => '',
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => '<div class="veu_promotion-alert" data-nosnippet><div class="veu_promotion-alert__content--text"><span class="veu_promotion-alert__icon"><i class="fa-solid fa-circle-info"></i></span><span class="veu_promotion-alert__text">aaaa</span></div></div>',
			),
			array(
				'options' => array(
					'alert-text'    => '',
					'alert-content' => 'bbbb',
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => '<div class="veu_promotion-alert" data-nosnippet><div class="veu_promotion-alert__content--custom">bbbb</div></div>',
			),
			array(
				'options' => array(
					'alert-text'    => 'aaaa',
					'alert-content' => 'bbbb',
					'alert-display' => array(
						'post' => 'display',
					)
				),
				'correct' => '<div class="veu_promotion-alert" data-nosnippet><div class="veu_promotion-alert__content--custom">bbbb</div></div>',
			),
			array(
				'options' => array(
					'alert-text'    => '',
					'alert-content' => '',
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'alert-text'    => 'aaaa',
					'alert-content' => '',
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'alert-text'    => '',
					'alert-content' => 'bbbb',
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'alert-text'    => 'aaaa',
					'alert-content' => 'bbbb',
					'alert-display' => array(
						'post' => 'hide',
					)
				),
				'correct' => '',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'Promotion Alert Content Test' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		foreach ( $test_array as $test_value ) {			

			// Set site name
			update_option( 'vkExUnit_PA', $test_value['options'] );

			$this->go_to( get_permalink( $data['post_id_01'] ) );

			$options = get_option( 'vkExUnit_common_options' );

			// var_dump($options['active_icon_accessibility']);

			$return  = VEU_Promotion_Alert::get_alert_content();
			$correct = $test_value['correct'];

			$this->assertEquals( $correct, $return );

			// print PHP_EOL;
			// print 'correct ::::' . $correct . PHP_EOL;
			// print 'return  ::::' . $return . PHP_EOL;

		}

	}

}