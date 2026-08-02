<?php
/**
 * CTA display path escaping test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * CTA display path escaping test case.
 *
 * The classic CTA layout concatenates custom field values straight into the markup.
 * This test covers the image position ( save side allowlist and output side escaping )
 * and the CTA title ( output side filtering ).
 *
 * クラシックレイアウトの CTA はカスタムフィールドの値をそのままマークアップへ連結する。
 * ここでは画像位置 ( 保存時のホワイトリストと出力時のエスケープ ) と
 * CTA タイトル ( 出力時のフィルタ ) を検証する。
 */
class CTAOutputEscapingTest extends WP_UnitTestCase {

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
		wp_set_current_user( 0 );
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
				'post_title'   => 'CTA Output Escaping',
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
	 * Save a post title keeping its HTML tags.
	 * HTML タグを保持したまま投稿タイトルを保存する。
	 *
	 * WordPress は unfiltered_html 権限を持たないユーザーの保存時にタイトルへ kses を適用し、
	 * <br> などのタグを除去してしまう。管理者が保存した実運用の状態を再現するため、
	 * 一時的に管理者ユーザーへ切り替えて保存し、描画前に未ログイン状態へ戻す。
	 * WordPress applies kses to the title when the saving user lacks the unfiltered_html capability,
	 * stripping tags such as <br>. To reproduce the real-world state saved by an administrator,
	 * switch to an administrator to save and switch back to a logged-out state before rendering.
	 *
	 * @param int    $post_id 投稿ID / Post ID.
	 * @param string $title   保存するタイトル / Title to save.
	 * @return void
	 */
	private function set_unfiltered_post_title( $post_id, $title ) {
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
		// 未ログイン状態に戻す。管理者のままだと描画結果に編集ボタンが混ざるため。
		// Switch back to a logged-out state, otherwise the edit button would be mixed into the rendered output.
		wp_set_current_user( 0 );
	}

	/**
	 * Test cases shared by the classic output path and the block output path.
	 * クラシック出力経路とブロック出力経路で共有するテストケース。
	 *
	 * @return array テストケース / Test cases.
	 */
	private function get_output_cases() {
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
				'test_condition_name' => '画像位置が未設定 ( Normal ) の場合 => 既定値の cta_body_image_right',
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
			array(
				// タイトルは wp_kses_post で出力するため、<br> は残しつつ <script> は除去される。
				// esc_html だと <br> が文字として表示されてしまうため、その退行がない事も併せて検証する。
				// The title is output through wp_kses_post, so <br> is kept while <script> is stripped.
				// esc_html would render <br> as text, so this also guards against that regression.
				'test_condition_name' => 'タイトルに <br> と <script> が含まれる場合 => <br> は残り <script> は除去される',
				'image_position'      => 'right',
				'post_title'          => 'CTA Title<br><script>alert(1)</script>',
				'expected'            => '<h1 class="cta_title">CTA Title<br>alert(1)</h1>',
				'not_expected'        => '<script>',
			),
			array(
				'test_condition_name' => 'タイトルに on* 属性付きのタグが含まれる場合 => 属性が除去される',
				'image_position'      => 'right',
				'post_title'          => 'CTA <span onmouseover="alert(1)">Title</span>',
				'expected'            => '<h1 class="cta_title">CTA <span>Title</span></h1>',
				'not_expected'        => 'onmouseover',
			),
		);
	}

	/**
	 * Apply the conditions of one output test case to the CTA post.
	 * 出力テストケースの条件を CTA 投稿へ適用する。
	 *
	 * @param int   $cta_id    CTA の投稿ID / CTA post ID.
	 * @param array $test_case テストケース / Test case.
	 * @return void
	 */
	private function apply_output_case( $cta_id, $test_case ) {
		// $imgid が真値のときだけ画像ブロックが出力されるため、存在しないアタッチメントIDを入れておく。
		// 存在しないIDなので wp_get_attachment_image() は空文字を返し、比較対象のHTMLが安定する。
		// The image block is rendered only when $imgid is truthy, so store a non-existent attachment ID.
		// Since the ID does not exist, wp_get_attachment_image() returns an empty string and keeps the HTML stable.
		update_post_meta( $cta_id, 'vkExUnit_cta_img', '999999' );
		update_post_meta( $cta_id, 'vkExUnit_cta_text', 'cta' );
		// ボタンを出力させないため URL は空にする。
		// Empty the URL so the button is not rendered.
		update_post_meta( $cta_id, 'vkExUnit_cta_url', '' );
		$this->set_raw_post_meta( $cta_id, 'vkExUnit_cta_img_position', $test_case['image_position'] );

		if ( isset( $test_case['post_title'] ) ) {
			$this->set_unfiltered_post_title( $cta_id, $test_case['post_title'] );
		}
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
				// 空文字はブロックエディタの「Normal」( 位置指定なし ) を表す正当な値のため丸めない。
				// 丸めてしまうと Normal を選んで保存した後に Right が選択された状態で表示される退行になる。
				// An empty string is the valid "Normal" ( no position ) value in the block editor, so it must not be normalized.
				// Normalizing it would regress into showing "Right" as selected after saving "Normal".
				'test_condition_name' => '空文字 ( Normal ) の場合 => 空文字のまま',
				'value'               => '',
				'expected'            => '',
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
				'test_condition_name' => 'null の場合はスカラーではないため => 既定値の right',
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
	 * クラシック表示経路 ( view-actionbox.php ) の出力を検証する。
	 * Verify the output of the classic display path ( view-actionbox.php ).
	 *
	 * @return void
	 */
	public function test_render_cta_content() {
		$cta_id = $this->create_classic_cta();

		foreach ( $this->get_output_cases() as $case ) {
			$this->apply_output_case( $cta_id, $case );

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
	 * ブロック表示経路 ( block/index.php ) の出力を検証する。
	 * Verify the output of the block display path ( block/index.php ).
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
				'post_title'   => 'CTA Output Escaping Page',
				'post_content' => 'page content',
			)
		);
		$this->go_to( get_permalink( $page_id ) );

		foreach ( $this->get_output_cases() as $case ) {
			$this->apply_output_case( $cta_id, $case );

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
				'test_condition_name' => '空文字が送信された場合 => 空文字のまま',
				'post_value'          => '',
				'expected'            => '',
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
				// ブロックエディタのセレクトで「Normal」を選んだ場合。空文字のまま保存されないと
				// リロード時に Right が選択された状態で表示されてしまう。
				// Selecting "Normal" in the block editor's select. If it is not stored as an empty string,
				// "Right" would appear selected after reloading.
				'test_condition_name' => '空文字 ( Normal ) を保存した場合 => 空文字のまま',
				'value'               => '',
				'expected'            => '',
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
