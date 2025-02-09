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
			// print 'correct ::::' . esc_attr( $test_value['correct'] ) . PHP_EOL;
			// print 'return  ::::' . esc_attr( $return ) . PHP_EOL;
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
		$page               = array(
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
			'Deleted CTA'                 => array(
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
			// print 'expected ::' . $test_value['expected'] . PHP_EOL;
			// print 'actual ::::' . $actual . PHP_EOL;
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
		$cta_post                  = array(
			'post_title'   => 'cta_published',
			'post_type'    => 'cta',
			'post_status'  => 'publish',
			'post_content' => 'CTA content',
		);
		$test_posts['cta_post_id'] = wp_insert_post( $cta_post );

		// テスト配列
		$test_array = array(
			'post_content text'        => array(
				'cta_content' => 'cta',
				'expected'    => 'cta',
			),
			'post_content html simple' => array(
				'cta_content' => '<!-- wp:heading --><h2 class="wp-block-heading">テストタイトル</h2><!-- /wp:heading -->',
				'expected'    => '<h2 class="wp-block-heading">テストタイトル</h2>',
			),
			'post_content html middle' => array(
				'cta_content' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"black","layout":{"type":"constrained"}} --><div class="wp-block-group has-black-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"textAlign":"center","className":"vkp-title-short-border\u002d\u002dcenter is-style-vk-heading-plain vk_block-margin-0\u002d\u002dmargin-bottom vk_custom_css","style":{"typography":{"fontSize":"2.2rem","letterSpacing":"2px"}},"textColor":"white"} --><h2 class="wp-block-heading has-text-align-center vkp-title-short-border--center is-style-vk-heading-plain vk_block-margin-0--margin-bottom vk_custom_css has-white-color has-text-color" style="font-size:2.2rem;letter-spacing:2px">テストタイトル</h2><!-- /wp:heading --><!-- wp:vk-blocks/button {"buttonUrl":"https://demo.dev3.biz/architect/recruit/","buttonType":"1","buttonColor":"custom","buttonColorCustom":"white","buttonAlign":"center","blockId":"e6f30732-b8f9-435d-8f9d-27d691e7d303","className":"vkp_button-through-arrow vk_custom_css"} --><div class="wp-block-vk-blocks-button vk_button vk_button-color-custom vk_button-align-center vkp_button-through-arrow vk_custom_css"><a href="https://demo.dev3.biz/architect/recruit/" class="vk_button_link btn has-text-color is-style-outline has-white-color btn-md" role="button" aria-pressed="true" rel="noopener"><div class="vk_button_link_caption"><span class="vk_button_link_txt">ボタン</span></div></a></div><!-- /wp:vk-blocks/button --></div><!-- /wp:group -->',
				'expected'    => '<div class="wp-block-group has-black-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><div class="wp-block-group__inner-container is-layout-constrained wp-block-group-is-layout-constrained"><h2 class="wp-block-heading has-text-align-center vkp-title-short-border--center is-style-vk-heading-plain vk_block-margin-0--margin-bottom vk_custom_css has-white-color has-text-color" style="font-size:2.2rem;letter-spacing:2px">テストタイトル</h2><div class="wp-block-vk-blocks-button vk_button vk_button-color-custom vk_button-align-center vkp_button-through-arrow vk_custom_css"><a href="https://demo.dev3.biz/architect/recruit/" class="vk_button_link btn has-text-color is-style-outline has-white-color btn-md" role="button" rel="noopener"><div class="vk_button_link_caption"><span class="vk_button_link_txt">ボタン</span></div></a></div></div></div>',
			),
			'post_content xss test'    => array(
				'cta_content' => '"><script>alert(0)</script>',
				'expected'    => '"&gt;alert(0)',
			),
			'classic CTA test'         => array(
				'cta_title'   => 'Classic Title',
				'cta_content' => '',
				'post_meta'   => array(
					'vkExUnit_cta_text'        => 'cta',
					'vkExUnit_cta_button_text' => 'Read more',
					'vkExUnit_cta_url'         => 'https://example.com',
				),
				'expected'    => '<section class="veu_cta" id="veu_cta-' . $test_posts['cta_post_id'] . '"><h1 class="cta_title">Classic Title</h1><div class="cta_body"><div class="cta_body_txt image_no">cta</div><div class="cta_body_link"><a href="https://example.com" class="btn btn-primary btn-block btn-lg" target="_blank">Read more</a></div></div><!-- [ /.vkExUnit_cta_body ] --></section>',
			),
			'classic CTA XSS test'     => array(
				'cta_title'   => 'Classic Title',
				'cta_content' => '',
				'post_meta'   => array(
					'vkExUnit_cta_text'        => '"><script>alert(0)</script>',
					'vkExUnit_cta_button_text' => '"><script>alert(0)</script>',
					'vkExUnit_cta_url'         => 'https://example.com',
				),
				'expected'    => '<section class="veu_cta" id="veu_cta-' . $test_posts['cta_post_id'] . '"><h1 class="cta_title">Classic Title</h1><div class="cta_body"><div class="cta_body_txt image_no">"&gt;alert(0)</div><div class="cta_body_link"><a href="https://example.com" class="btn btn-primary btn-block btn-lg" target="_blank">"&gt;alert(0)</a></div></div><!-- [ /.vkExUnit_cta_body ] --></section>',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_render_cta_content' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$content = '';
		foreach ( $test_array as $key => $value ) {
			// $test_posts['cta_post_id'] の投稿の post_content の中身を $value['cta_content'] で上書きする
			$post_args = array(
				'ID'           => $test_posts['cta_post_id'],
				'post_content' => $value['cta_content'],
			);
			if ( ! empty( $value['cta_title'] ) ) {
				$post_args['post_title'] = $value['cta_title'];
			}
			wp_update_post( $post_args );
			if ( ! empty( $value['post_meta'] ) ) {
				foreach ( $value['post_meta'] as $meta_key => $meta_value ) {
					update_post_meta( $test_posts['cta_post_id'], $meta_key, $meta_value );
				}
			}

			$actual = Vk_Call_To_Action::render_cta_content( $test_posts['cta_post_id'] );
			// print 'expected ::' . $value['expected'] . PHP_EOL;
			// print 'actual ::::' . $actual . PHP_EOL;
			$this->assertEquals( $value['expected'], $actual );
		}
	}

	/**
	 * Test safe_kses_post()
	 *
	 * @return void
	 */
	function test_safe_kses_post() {
		$test_cases = array(
			array(
				'name'     => 'allow google',
				'input'    => '<iframe src="https://www.google.com/maps/embed?pb=!1m18"></iframe>',
				'expected' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18"></iframe>',
			),
			array(
				'name'     => 'allow iframe youtube',
				'input'    => '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>',
				'expected' => '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>',
			),
			array(
				'name'     => 'allow iframe openstreetmap',
				'input'    => '<iframe src="https://www.openstreetmap.org/export/embed.html"></iframe>',
				'expected' => '<iframe src="https://www.openstreetmap.org/export/embed.html"></iframe>',
			),
			array(
				'name'     => 'allow iframe vimeo',
				'input'    => '<iframe src="https://player.vimeo.com/video/123456789"></iframe>',
				'expected' => '<iframe src="https://player.vimeo.com/video/123456789"></iframe>',
			),
			array(
				'name'     => 'escape iframe',
				'input'    => '<iframe src="https://www.vektor-inc.co.jp/"></iframe>',
				'expected' => '',
			),
			array(
				'name'     => 'class and style',
				'input'    => '<div class="class-name" style="margin-bottom:3rem">CTA</div>',
				'expected' => '<div class="class-name" style="margin-bottom:3rem">CTA</div>',
			),
			array(
				'name'     => 'allow style tag',
				'input'    => '<div>CTA</div><style>.target-class { background-image: url(http://localhost:8888/image.jpg);}</style>',
				'expected' => '<div>CTA</div><style>.target-class { background-image: url(http://localhost:8888/image.jpg);}</style>',
			),
			array(
				'name'     => 'allow style tag with media query',
				'input'    => '<div>CTA</div><style>@media screen and (max-width: 575.98px) {.target-class {
			background-image: url(http://localhost:8888/image.jpg);
			background-position: 50% 50%!important;}}</style>',
				'expected' => '<div>CTA</div><style>@media screen and (max-width: 575.98px) {.target-class {
			background-image: url(http://localhost:8888/image.jpg);
			background-position: 50% 50%!important;}}</style>',
			),
			array(
				'name'     => 'allow style tag with various style properties',
				'input'    => '<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);background-image:url(\'http://localhost:8888/wp-content/uploads/2021/06/pr-img.png\');background-size:cover"><p class="has-text-align-center">text</p></div>',
				'expected' => '<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);background-image:url(\'http://localhost:8888/wp-content/uploads/2021/06/pr-img.png\');background-size:cover"><p class="has-text-align-center">text</p></div>',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = Vk_Call_To_Action::safe_kses_post( $case['input'] );
			$this->assertEquals( $case['expected'], $actual, $case['name'] );
		}
	}
}
