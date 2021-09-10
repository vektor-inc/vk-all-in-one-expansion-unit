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
}
