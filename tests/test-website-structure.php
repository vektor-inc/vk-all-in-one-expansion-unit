<?php
/**
 * Class Website_Structure_Test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

class Website_Structure_Test extends WP_UnitTestCase {
	function test_get_website_array() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_get_website_array' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_data = array(
			array(
				'target_url' => home_url( '/' ),
				'options'    => array(
					'blogname' => 'Site name',
				),
				'correct'    => array(
					'@context' => 'https://schema.org/',
					'@type'    => 'WebSite',
					'name'     => 'Site name',
					'url'      => home_url(),
				),
			),
			// サイト名は保存時に & が &amp; などの文字参照になるため、元の文字に戻した値になる。
			// The site name is saved with & etc. turned into character references such as &amp;, so the value has them restored.
			array(
				'target_url' => home_url( '/' ),
				'options'    => array(
					'blogname' => 'Tom & Jerry "1 > 0"',
				),
				'correct'    => array(
					'@context' => 'https://schema.org/',
					'@type'    => 'WebSite',
					'name'     => 'Tom & Jerry "1 > 0"',
					'url'      => home_url(),
				),
			),
		);

		foreach ( $test_data as $test_value ) {
			if ( ! empty( $test_value['options'] ) && is_array( $test_value['options'] ) ) {
				foreach ( $test_value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			$this->go_to( $test_value['target_url'] );

			$actual  = VK_WebSite_Srtuctured_Data::get_website_structure_array();
			$correct = $test_value['correct'];

			// print PHP_EOL;
			// print 'correct ::::' . PHP_EOL;
			// var_dump( $correct );
			// print 'return  ::::' . PHP_EOL;
			// var_dump( $actual );

			$this->assertEquals( $correct, $actual );

			if ( ! empty( $test_value['options'] ) && is_array( $test_value['options'] ) ) {
				foreach ( $test_value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}
	}

	/**
	 * Test the_website_structure_data().
	 * the_website_structure_data() のテスト。
	 *
	 * @return void
	 */
	public function test_the_website_structure_data() {

		$post_id = self::factory()->post->create();

		$test_cases = array(
			array(
				'test_condition_name' => 'トップページでサイト名が通常の文字列 => そのまま出力される',
				'conditions'          => array(
					'options' => array( 'blogname' => 'Site name' ),
				),
				'target_url'          => home_url( '/' ),
				'expected_payload'    => '{"@context":"https://schema.org/","@type":"WebSite","name":"Site name","url":"' . home_url() . '"}',
			),
			array(
				'test_condition_name' => 'トップページでサイト名に & を含む => & は &amp; にならずそのまま出力される',
				'conditions'          => array(
					'options' => array( 'blogname' => 'Tom & Jerry のサイト' ),
				),
				'target_url'          => home_url( '/' ),
				'expected_payload'    => '{"@context":"https://schema.org/","@type":"WebSite","name":"Tom & Jerry のサイト","url":"' . home_url() . '"}',
			),
			array(
				'test_condition_name' => 'トップページで URL に & を含む => & は &amp; にならずそのまま出力される',
				'conditions'          => array(
					'options' => array( 'blogname' => 'Site name' ),
					'filters' => array(
						'home_url' => function () {
							return 'https://example.com/?a=1&b=2';
						},
					),
				),
				'target_url'          => home_url( '/' ),
				'expected_payload'    => '{"@context":"https://schema.org/","@type":"WebSite","name":"Site name","url":"https://example.com/?a=1&b=2"}',
			),
			array(
				'test_condition_name' => 'トップページでサイト名に > を含む => \u003E で出力される',
				'conditions'          => array(
					'options' => array( 'blogname' => '1 > 0' ),
				),
				'target_url'          => home_url( '/' ),
				'expected_payload'    => '{"@context":"https://schema.org/","@type":"WebSite","name":"1 \u003E 0","url":"' . home_url() . '"}',
			),
			// サイト名の保存処理は &apos; を &amp;apos; にするため、ほかのプラグインがフィルターで渡すサイト名を想定してフィルターで与える。
			// Saving the site name turns &apos; into &amp;apos;, so the site name is given by a filter, assuming one passed by another plugin.
			array(
				'test_condition_name' => 'トップページでサイト名に &apos; と &#039; を含む => どちらも同じ \' に戻して出力される',
				'conditions'          => array(
					'options' => array( 'blogname' => 'Site name' ),
					'filters' => array(
						'option_blogname' => function () {
							return 'Tom&apos;s &amp; Jerry&#039;s';
						},
					),
				),
				'target_url'          => home_url( '/' ),
				'expected_payload'    => '{"@context":"https://schema.org/","@type":"WebSite","name":"Tom\'s & Jerry\'s","url":"' . home_url() . '"}',
			),
			array(
				'test_condition_name' => '投稿の個別ページ => 出力されない',
				'conditions'          => array(
					'options' => array( 'blogname' => 'Tom & Jerry のサイト' ),
				),
				'target_url'          => get_permalink( $post_id ),
				'expected_payload'    => null,
			),
		);

		foreach ( $test_cases as $case ) {
			// 設定を用意して対象ページへ移動する。
			// Prepare the options and move to the target page.
			foreach ( $case['conditions']['options'] as $option_name => $option_value ) {
				update_option( $option_name, $option_value );
			}
			$this->go_to( $case['target_url'] );

			// 移動先の URL を変えないよう、フィルターは移動後に追加する。
			// Add the filters after moving, so that they do not change the URL to move to.
			$filters = isset( $case['conditions']['filters'] ) ? $case['conditions']['filters'] : array();
			foreach ( $filters as $hook_name => $callback ) {
				add_filter( $hook_name, $callback );
			}

			// 出力を取得し、script 要素の中身を取り出す。
			// Capture the output and extract the content of the script element.
			ob_start();
			VK_WebSite_Srtuctured_Data::the_website_structure_data();
			$output = ob_get_clean();
			preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $output, $matches );
			$payload = isset( $matches[1] ) ? $matches[1] : null;

			if ( null === $case['expected_payload'] ) {
				// 出力しないケースでは、script 要素だけでなく前後のコメントも含めて何も出力されないことを確かめる。
				// For the cases without output, check that nothing is output at all, including the comments around the script element.
				$this->assertSame( '', $output, $case['test_condition_name'] );
			} else {
				$this->assertSame( $case['expected_payload'], $payload, $case['test_condition_name'] );

				// JSON として読み直した値が出力元の配列と一致することも確かめる。
				// Also check that the value decoded as JSON matches the source array.
				$this->assertSame( VK_WebSite_Srtuctured_Data::get_website_structure_array(), json_decode( (string) $payload, true ), $case['test_condition_name'] );
			}

			// 後続のケースへ影響させないよう後始末する。
			// Clean up so that later cases are not affected.
			foreach ( $filters as $hook_name => $callback ) {
				remove_filter( $hook_name, $callback );
			}
			foreach ( $case['conditions']['options'] as $option_name => $option_value ) {
				delete_option( $option_name );
			}
		}
	}
}
