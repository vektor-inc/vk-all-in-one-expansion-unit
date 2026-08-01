<?php
/**
 * CTA image position ( vkExUnit_cta_img_position ) test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * CTA image position test case.
 *
 * The vkExUnit_cta_img_position value is concatenated into a class attribute on the front end.
 * This test covers both the save side ( allowlist validation ) and the output side ( escaping ).
 *
 * vkExUnit_cta_img_position はフロント側で class 属性に連結されるため、
 * 保存時のホワイトリスト検証と出力時のエスケープの両方を検証する。
 */
class CTAImagePositionTest extends WP_UnitTestCase {

	/**
	 * Stored XSS PoC value reported in issue #1434.
	 * issue #1434 で報告された Stored XSS の PoC 値。
	 */
	const XSS_IMAGE_POSITION = 'right" onmouseover=alert(1)//';

	/**
	 * Reset globals after each test.
	 * テストごとにグローバルを初期化する。
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();
		$_POST = array();
	}

	/**
	 * Create a CTA post rendered with the classic ( non block ) layout.
	 * クラシックレイアウトで描画される CTA 投稿を作成する。
	 *
	 * @return int 作成した CTA の投稿ID / Created CTA post ID.
	 */
	private function create_classic_cta() {
		// post_content を空にするとクラシックレイアウト ( view-actionbox.php ) が使われる。
		// An empty post_content makes the classic layout ( view-actionbox.php ) render.
		return self::factory()->post->create(
			array(
				'post_type'    => Vk_Call_To_Action::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => 'CTA Image Position',
				'post_content' => '',
			)
		);
	}

	/**
	 * Write post meta directly into the database, bypassing sanitize_callback.
	 * sanitize_callback を経由せずに直接データベースへ post meta を書き込む。
	 *
	 * This reproduces the state where a malicious value is already stored in the database,
	 * because it does not go through the sanitize_callback of register_post_meta().
	 *
	 * register_post_meta() の sanitize_callback を通さないため、
	 * 「不正な値が既にデータベースへ保存されている」状態を再現できる。
	 *
	 * @param int    $post_id    投稿ID / Post ID.
	 * @param string $meta_key   メタキー / Meta key.
	 * @param string $meta_value 生のメタ値 / Raw meta value.
	 * @return void
	 */
	private function set_raw_post_meta( $post_id, $meta_key, $meta_value ) {
		global $wpdb;
		// 意図的に sanitize_callback を迂回するため、直接クエリを実行する。
		// The query is run directly on purpose so the sanitize_callback is bypassed.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		$wpdb->delete(
			$wpdb->postmeta,
			array(
				'post_id'  => $post_id,
				'meta_key' => $meta_key,
			)
		);
		$wpdb->insert(
			$wpdb->postmeta,
			array(
				'post_id'    => $post_id,
				'meta_key'   => $meta_key,
				'meta_value' => $meta_value,
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		// 直接書き込んだためオブジェクトキャッシュを破棄する。
		// Flush the object cache because the row was written directly.
		wp_cache_delete( $post_id, 'post_meta' );
	}

	/**
	 * Set up the CTA meta used by the classic layout image block.
	 * クラシックレイアウトの画像ブロック描画に必要な CTA メタを設定する。
	 *
	 * @param int    $cta_id         CTA の投稿ID / CTA post ID.
	 * @param string $image_position 生の画像位置の値 / Raw image position value.
	 * @return void
	 */
	private function set_image_meta( $cta_id, $image_position ) {
		// $imgid が真値のときだけ画像ブロックが出力されるため、存在しないアタッチメントIDを入れておく。
		// 存在しないIDなので wp_get_attachment_image() は空文字を返し、比較対象のHTMLが安定する。
		// The image block is rendered only when $imgid is truthy, so store a non-existent attachment ID.
		// Since the ID does not exist, wp_get_attachment_image() returns an empty string and keeps the HTML stable.
		update_post_meta( $cta_id, 'vkExUnit_cta_img', '999999' );
		update_post_meta( $cta_id, 'vkExUnit_cta_text', 'cta' );
		// ボタンを出力させないため URL は空にする。
		// Empty the URL so the button is not rendered.
		update_post_meta( $cta_id, 'vkExUnit_cta_url', '' );
		$this->set_raw_post_meta( $cta_id, 'vkExUnit_cta_img_position', $image_position );
	}

	/**
	 * Test cases shared by the classic output path and the block output path.
	 * クラシック出力経路とブロック出力経路で共有するテストケース。
	 *
	 * @return array Test cases. テストケース。
	 */
	private function get_image_position_output_cases() {
		return array(
			array(
				'test_condition_name' => '画像位置が right の場合 => cta_body_image_right',
				'image_position'      => 'right',
				'expected'            => '<div class="cta_body_image cta_body_image_right">',
			),
			array(
				'test_condition_name' => '画像位置が left の場合 => cta_body_image_left',
				'image_position'      => 'left',
				'expected'            => '<div class="cta_body_image cta_body_image_left">',
			),
			array(
				'test_condition_name' => '画像位置が未設定の場合 => 既定値の cta_body_image_right',
				'image_position'      => '',
				'expected'            => '<div class="cta_body_image cta_body_image_right">',
			),
			array(
				// 既にデータベースへ保存されてしまった不正値。class 属性を抜け出す形の値でも
				// エスケープされて属性値の中に留まり、onmouseover 属性が生成されない事を検証する。
				// A malicious value already stored in the database. Even a value that breaks out of the class
				// attribute must stay escaped inside the attribute value and must not produce an onmouseover attribute.
				'test_condition_name' => '画像位置に属性を抜け出す不正値が保存されている場合 => エスケープされ onmouseover 属性にならない',
				'image_position'      => self::XSS_IMAGE_POSITION,
				'expected'            => '<div class="cta_body_image cta_body_image_right&quot; onmouseover=alert(1)//">',
				'not_expected'        => 'cta_body_image_right" onmouseover=alert(1)//',
			),
		);
	}

	/**
	 * Test Vk_Call_To_Action::sanitize_image_position()
	 *
	 * @return void
	 */
	public function test_sanitize_image_position() {
		$test_cases = array(
			array(
				'test_condition_name' => 'right の場合 => right',
				'value'               => 'right',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => 'center の場合 => center',
				'value'               => 'center',
				'expected'            => 'center',
			),
			array(
				'test_condition_name' => 'left の場合 => left',
				'value'               => 'left',
				'expected'            => 'left',
			),
			array(
				'test_condition_name' => '空文字の場合 => 既定値の right',
				'value'               => '',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '許可値以外の文字列の場合 => 既定値の right',
				'value'               => 'top',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '大文字の場合は許可値と一致しないため => 既定値の right',
				'value'               => 'RIGHT',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '属性を抜け出す不正値の場合 => 既定値の right',
				'value'               => self::XSS_IMAGE_POSITION,
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => 'null の場合 => 既定値の right',
				'value'               => null,
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '配列などスカラー以外の場合 => 既定値の right',
				'value'               => array( 'right' ),
				'expected'            => 'right',
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = Vk_Call_To_Action::sanitize_image_position( $case['value'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Test Vk_Call_To_Action::render_cta_content()
	 *
	 * クラシック表示経路 ( view-actionbox.php ) の class 属性出力を検証する。
	 * Verify the class attribute output of the classic display path ( view-actionbox.php ).
	 *
	 * @return void
	 */
	public function test_render_cta_content() {
		$cta_id = $this->create_classic_cta();

		foreach ( $this->get_image_position_output_cases() as $case ) {
			$this->set_image_meta( $cta_id, $case['image_position'] );

			$actual = Vk_Call_To_Action::render_cta_content( $cta_id );

			$this->assertStringContainsString( $case['expected'], $actual, $case['test_condition_name'] );
			if ( isset( $case['not_expected'] ) ) {
				$this->assertStringNotContainsString( $case['not_expected'], $actual, $case['test_condition_name'] );
			}
		}
	}

	/**
	 * Test veu_cta_block_callback()
	 *
	 * ブロック表示経路 ( block/index.php ) の class 属性出力を検証する。
	 * Verify the class attribute output of the block display path ( block/index.php ).
	 *
	 * @return void
	 */
	public function test_veu_cta_block_callback() {
		$cta_id = $this->create_classic_cta();

		// CTA を配置する表示先のページ。
		// The page where the CTA block is placed.
		$page_id = self::factory()->post->create(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'CTA Image Position Page',
				'post_content' => 'page content',
			)
		);
		$this->go_to( get_permalink( $page_id ) );

		foreach ( $this->get_image_position_output_cases() as $case ) {
			$this->set_image_meta( $cta_id, $case['image_position'] );

			$actual = veu_cta_block_callback( array( 'postId' => $cta_id ), '' );

			$this->assertStringContainsString( $case['expected'], $actual, $case['test_condition_name'] );
			if ( isset( $case['not_expected'] ) ) {
				$this->assertStringNotContainsString( $case['not_expected'], $actual, $case['test_condition_name'] );
			}
		}
	}

	/**
	 * Test Vk_Call_To_Action::save_custom_field()
	 *
	 * クラシック編集画面 ( メタボックス ) からの保存時にホワイトリスト検証が働く事を検証する。
	 * Verify that the allowlist validation is applied when saving from the classic edit screen ( metabox ).
	 *
	 * @return void
	 */
	public function test_save_custom_field() {
		$cta_id = $this->create_classic_cta();

		// save_custom_field() が要求する nonce を作成する。
		// Create the nonce required by save_custom_field().
		$reflection = new ReflectionClass( 'Vk_Call_To_Action' );
		$nonce      = wp_create_nonce( plugin_basename( $reflection->getFileName() ) );

		$test_cases = array(
			array(
				'test_condition_name' => 'right が送信された場合 => right',
				'post_value'          => 'right',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => 'center が送信された場合 => center',
				'post_value'          => 'center',
				'expected'            => 'center',
			),
			array(
				'test_condition_name' => 'left が送信された場合 => left',
				'post_value'          => 'left',
				'expected'            => 'left',
			),
			array(
				'test_condition_name' => '許可値以外が送信された場合 => 既定値の right',
				'post_value'          => 'top',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '属性を抜け出す不正値が送信された場合 => 既定値の right',
				'post_value'          => self::XSS_IMAGE_POSITION,
				'expected'            => 'right',
			),
		);

		foreach ( $test_cases as $case ) {
			$_POST = array(
				'_nonce_vkExUnit_custom_cta' => $nonce,
				'_vkExUnit_cta_switch'       => 'cta_content',
				'vkExUnit_cta_img_position'  => $case['post_value'],
			);

			Vk_Call_To_Action::save_custom_field( $cta_id );

			$this->assertSame(
				$case['expected'],
				get_post_meta( $cta_id, 'vkExUnit_cta_img_position', true ),
				$case['test_condition_name']
			);
		}
	}

	/**
	 * Test veu_register_active_feature_meta()
	 *
	 * ブロックエディタ ( REST API ) 経由の保存で使われる sanitize_callback を検証する。
	 * update_post_meta() は登録済みメタの sanitize_callback を通るため、それを利用して検証する。
	 * Verify the sanitize_callback used when saving through the block editor ( REST API ).
	 * update_post_meta() runs the registered sanitize_callback, so it is used for the assertion.
	 *
	 * @return void
	 */
	public function test_veu_register_active_feature_meta() {
		$cta_id = $this->create_classic_cta();

		$test_cases = array(
			array(
				'test_condition_name' => 'right を保存した場合 => right',
				'value'               => 'right',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => 'center を保存した場合 => center',
				'value'               => 'center',
				'expected'            => 'center',
			),
			array(
				'test_condition_name' => '許可値以外を保存した場合 => 既定値の right',
				'value'               => 'top',
				'expected'            => 'right',
			),
			array(
				'test_condition_name' => '属性を抜け出す不正値を保存した場合 => 既定値の right',
				'value'               => self::XSS_IMAGE_POSITION,
				'expected'            => 'right',
			),
		);

		foreach ( $test_cases as $case ) {
			update_post_meta( $cta_id, 'vkExUnit_cta_img_position', $case['value'] );

			$this->assertSame(
				$case['expected'],
				get_post_meta( $cta_id, 'vkExUnit_cta_img_position', true ),
				$case['test_condition_name']
			);
		}
	}
}
