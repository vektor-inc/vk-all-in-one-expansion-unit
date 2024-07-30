<?php
/**
 * CTA test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * CTA Test.
 */
class CTATest extends WP_UnitTestCase {

	/**
	 * Create test post
	 *
	 * @return array $test_posts
	 */
	public static function create_cta() {

		$test_posts = array();
		// Create test post.
		$post                  = array(
			'post_title'   => 'cta_published',
			'post_type'    => 'CTA',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['post_id'] = wp_insert_post( $post );

		$post                          = array(
			'post_title'   => 'cta_private',
			'post_type'    => 'CTA',
			'post_status'  => 'private',
			'post_content' => 'content',
		);
		$test_posts['post_id_private'] = wp_insert_post( $post );

		$post                        = array(
			'post_title'   => 'cta_draft',
			'post_type'    => 'CTA',
			'post_status'  => 'draft',
			'post_content' => 'content',
		);
		$test_posts['post_id_draft'] = wp_insert_post( $post );

		return $test_posts;
	}

	/**
	 * Test get_cta_post()
	 *
	 * @return void
	 */
	function test_get_cta_post() {
		$test_posts = self::create_cta();
		$test_array = array(
			// 公開.
			array(
				'post_id' => $test_posts['post_id'],
				'correct' => 'cta_published',
			),
			// 非公開.
			array(
				'post_id' => $test_posts['post_id_private'],
				'correct' => null,
			),
			// 下書きは表示しない.
			array(
				'post_id' => $test_posts['post_id_draft'],
				'correct' => null,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_get_cta_post' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $test_value ) {
			$return = '';
			$return = Vk_Call_To_Action::get_cta_post( $test_value['post_id'] );
			if ( isset( $return->post_title ) ) {
				$return = $return->post_title;
			}
			print 'correct ::::' . esc_attr( $test_value['correct'] ) . PHP_EOL;
			print 'return  ::::' . esc_attr( $return ) . PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );

		}
	}

	/**
	 * Test veu_cta_block_callback()
	 *
	 * @return void
	 */
	function test_veu_cta_block_callback() {
		$test_posts = array();

		// テスト用のCTAを作成
		$post                      = array(
			'post_title'   => 'cta_published',
			'post_type'    => 'cta',
			'post_status'  => 'publish',
			'post_content' => 'CTA content',
		);
		$test_posts['cta_post_id'] = wp_insert_post( $post );

		// テスト用のページを作成
		$page                      = array(
			'post_title'   => 'Page',
			'post_type'    => 'Page',
			'post_status'  => 'publish',
			'post_content' => 'Page',
		);
		$test_posts['page'] = wp_insert_post( $page );

		// テスト配列
		$test_array = array(
			'XSS test'                    => array(
				'target_url' => get_permalink( $test_posts['page'] ),
				'attributes' => array(
					'postId'    => $test_posts['cta_post_id'],
					'className' => '" onmouseover="alert(/XSS/)" style="background:red;"',
				),
				'expected'   => '<div class="veu-cta-block &quot; onmouseover=&quot;alert(/XSS/)&quot; style=&quot;background:red;&quot;">CTA content</div>',
			),
			'No CTA specified'            => array(
				'attributes' => array(
					'postId' => null,
				),
				'expected'   => '',
				// 未指定の場合は index.jsx の方で表示されるので、コールバック関数としては空を返す
				// 'expected'   => '<div class="veu-cta-block-edit-alert alert alert-warning">Please select CTA from Setting sidebar.</div>',
			),
			'Deleted CTA'            => array(
				'attributes' => array(
					'postId' => 999999,
				),
				'delete_cta' => true,
				'expected'   => '',
				// Show only current_user_can( 'edit_page' ) user
				// ログインして編集権限のあるユーザーの場合は表示される
				// 'expected'   => '<div class="alert alert-warning"><div class="alert-title">Specified CTA does not exist.</div></div>',
			),
			'random CTA but No CTA Front' => array(
				'attributes' => array(
					'postId' => 'random',
				),
				'delete_cta' => true,
				'expected'   => '',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_veu_cta_block_callback' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$content = '';
		foreach ( $test_array as $key => $test_value ) {
			if ( isset( $test_value['target_url'] ) ) {
				$this->go_to( $test_value['target_url'] );
			}
			if ( ! empty( $test_value['delete_cta'] ) ) {
				// 投稿タイプctaの投稿を全て取得
				$args      = array(
					'post_type'      => 'cta',
					'posts_per_page' => -1,
				);
				$cta_posts = get_posts( $args );

				if ( ! empty( $cta_posts ) ) {
					foreach ( $cta_posts as $post ) {
						wp_delete_post( $post->ID, true );
					}
				}
			}
			$actual = '';
			$actual = veu_cta_block_callback( $test_value['attributes'], $content );
			print 'expected ::' . $test_value['expected'] . PHP_EOL;
			print 'actual ::::' . $actual . PHP_EOL;
			$this->assertEquals( $test_value['expected'], $actual );
		}
	}

	/**
	 * Test render_cta_content()
	 *
	 * @return void
	 */
	function test_render_cta_content() {
		$test_posts = array();

		// テスト用のCTAを作成
		$cta_post                      = array(
			'post_title'   => 'cta_published',
			'post_type'    => 'cta',
			'post_status'  => 'publish',
			'post_content' => 'CTA content',
		);
		$test_posts['cta_post_id'] = wp_insert_post( $cta_post );

		// テスト配列
		$test_array = array(
			'normal text'                    => array(
				'cta_content' => 'cta',
				'expected'   => 'cta',
			),
			'xss test'                    => array(
				'cta_content' => '"><script>alert(0)</script>',
				'expected'   => '"&gt;alert(0)',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_render_cta_content' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$content = '';
		foreach ( $test_array as $key => $value ) {
			// $test_posts['cta_post_id'] の投稿の post_content の中身を $value['cta_content'] で上書きする
			wp_update_post(
				array(
					'ID'           => $test_posts['cta_post_id'],
					'post_content' => $value['cta_content'],
				)
			);

			$actual = Vk_Call_To_Action::render_cta_content( $test_posts['cta_post_id'] );
			print 'expected ::' . $value['expected'] . PHP_EOL;
			print 'actual ::::' . $actual . PHP_EOL;
			$this->assertEquals( $value['expected'], $actual );
		}
	}

}
