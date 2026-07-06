<?php
/**
 * Tests for Post Type Manager.
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Post Type Manager test case.
 */
class PostTypeManagerTest extends WP_UnitTestCase {

	/**
	 * Absolute path to the target class file.
	 *
	 * @var string
	 */
	private $class_file_path;

	/**
	 * Test user ID.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * Set up.
	 */
	public function set_up() {
		parent::set_up();

		$this->class_file_path = realpath( dirname( __DIR__ ) . '/inc/post-type-manager/package/class.post-type-manager.php' );

		$this->user_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $this->user_id );
	}

	/**
	 * Tear down.
	 */
	public function tear_down() {
		// Clean up globals.
		$_POST = array();
		delete_option( 'veu_global_taxonomy_settings' );
		parent::tear_down();
	}

	/**
	 * Create nonce value compatible with VK_Post_Type_Manager::save_cf_value().
	 *
	 * @return string
	 */
	private function create_post_type_manager_nonce() {
		$action = wp_create_nonce( $this->class_file_path );
		return wp_create_nonce( $action );
	}

	/**
	 * Get validation error transient key.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_validation_error_key( $post_id ) {
		return 'veu_ptm_validation_errors_' . get_current_user_id() . '_' . intval( $post_id );
	}

	/**
	 * Get validation draft transient key.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_validation_draft_key( $post_id ) {
		return 'veu_ptm_validation_draft_' . get_current_user_id() . '_' . intval( $post_id );
	}

	/**
	 * Create a post_type_manage post.
	 *
	 * @param array $args Optional post args override.
	 * @return int Post ID.
	 */
	private function create_post_type_manage_post( $args = array() ) {
		$post_id = self::factory()->post->create(
			array_merge(
				array(
					'post_type'   => 'post_type_manage',
					'post_status' => 'publish',
					'post_title'  => 'Test CPT Setting',
				),
				$args
			)
		);

		// Ensure global $post is available (the code references it).
		$GLOBALS['post'] = get_post( $post_id );

		return $post_id;
	}

	/**
	 * Run AJAX handler and return decoded JSON.
	 *
	 * @param array $post_data $_POST data.
	 * @return array Decoded JSON as array.
	 */
	private function run_ajax_and_get_json( $post_data ) {
		$_POST = $post_data;

		$die_filter = function () {
			return function () {
				throw new Exception( 'wp_die' );
			};
		};

		// wp_send_json_*() は AJAX コンテキストで wp_die() を呼び、その際は wp_die_handler ではなく
		// wp_die_ajax_handler フィルタが使われる。AJAX 判定を true にした上で AJAX 用ハンドラを例外に
		// 差し替えることで、プレーンな die によるプロセス終了（phpunit のサマリ・末尾改行の欠落や
		// JSON の出力リーク）を防ぎ、出力を確実に捕捉する。
		// wp_send_json_*() calls wp_die() in an AJAX context, where the wp_die_ajax_handler filter (not
		// wp_die_handler) is used. Force the AJAX flag and replace the AJAX handler with a thrower so the
		// output is captured instead of terminating the process via a plain die (which would drop the
		// PHPUnit summary / trailing newline and leak the JSON to stdout).
		add_filter( 'wp_doing_ajax', '__return_true' );
		add_filter( 'wp_die_ajax_handler', $die_filter );

		ob_start();
		try {
			VK_Post_Type_Manager::ajax_check_taxonomy_shared();
		} catch ( Exception $e ) {
			// Expected: wp_send_json_* ends with wp_die().
			$this->assertSame( 'wp_die', $e->getMessage() );
		} finally {
			$output = ob_get_clean();
			remove_filter( 'wp_die_ajax_handler', $die_filter );
			remove_filter( 'wp_doing_ajax', '__return_true' );
		}

		$json = json_decode( $output, true );
		$this->assertIsArray( $json, 'AJAX response should be valid JSON.' );

		return $json;
	}

	/**
	 * Required: Post Type ID missing should not save meta, but should store draft transient.
	 */
	public function test_save_cf_value_missing_post_type_id_sets_transients_and_does_not_update_meta() {
		$post_id = $this->create_post_type_manage_post();

		// Existing meta should remain unchanged on validation failure.
		update_post_meta( $post_id, 'veu_menu_position', '1' );

		$_POST = array(
			'noncename__post_type_manager' => $this->create_post_type_manager_nonce(),
			// 'veu_post_type_id' is intentionally missing.
			'veu_post_type_items'          => array( 'title' => 'true' ),
			'veu_menu_position'            => '10',
			'veu_menu_icon'                => 'dashicons-admin-post',
			'veu_post_type_export_to_api'  => 'true',
		);

		VK_Post_Type_Manager::save_cf_value( $post_id );

		// Meta should not be updated.
		$this->assertSame( '1', get_post_meta( $post_id, 'veu_menu_position', true ) );
		$this->assertSame( '', get_post_meta( $post_id, 'veu_post_type_id', true ) );

		// Error + draft transients should be set.
		$errors = get_transient( $this->get_validation_error_key( $post_id ) );
		$this->assertIsArray( $errors );
		$this->assertNotEmpty( $errors );

		$draft = get_transient( $this->get_validation_draft_key( $post_id ) );
		$this->assertIsArray( $draft );
		$this->assertSame( '10', $draft['veu_menu_position'] );
		$this->assertSame( 'dashicons-admin-post', $draft['veu_menu_icon'] );
		$this->assertSame( 'true', $draft['veu_post_type_export_to_api'] );
	}

	/**
	 * Supports が POST に含まれない・空配列でも custom-fields が強制補完されるため
	 * バリデーションエラーは発生せずメタが保存される事を、複数条件でまとめて検証する。
	 * Issue #1322 の仕様に伴うテーブル駆動テスト。
	 *
	 * Table-driven test verifying that custom-fields is force-injected even when
	 * Supports is missing or empty in POST, and that Post Type ID validation
	 * still gates meta updates as a boundary case (issue #1322).
	 */
	public function test_save_cf_value_missing_supports_still_saves_with_forced_custom_fields() {
		// テスト条件と期待値の配列 / Test conditions and expected results.
		$test_cases = array(
			array(
				'test_condition_name'          => 'Supports が POST に含まれない場合 => custom-fields が補完されメタが保存される',
				'post_type_id'                 => 'event',
				'include_post_type_items_post' => false,
				'post_type_items_post'         => null,
				'expected_meta_saved'          => true,
				'expected_post_type_id_meta'   => 'event',
				'expected_validation_error'    => false,
			),
			array(
				'test_condition_name'          => 'Supports が空配列の場合 => custom-fields のみ補完されメタが保存される',
				'post_type_id'                 => 'event',
				'include_post_type_items_post' => true,
				'post_type_items_post'         => array(),
				'expected_meta_saved'          => true,
				'expected_post_type_id_meta'   => 'event',
				'expected_validation_error'    => false,
			),
			array(
				'test_condition_name'          => 'Post Type ID 空 + Supports 未指定 => バリデーションエラーでメタは更新されない（境界値）',
				'post_type_id'                 => '',
				'include_post_type_items_post' => false,
				'post_type_items_post'         => null,
				'expected_meta_saved'          => false,
				'expected_post_type_id_meta'   => '',
				'expected_validation_error'    => true,
			),
		);

		foreach ( $test_cases as $case ) {
			// ケース毎にクリーンな投稿を作成 / Create a fresh post per case.
			$post_id = $this->create_post_type_manage_post();

			$_POST = array(
				'noncename__post_type_manager' => $this->create_post_type_manager_nonce(),
				'veu_post_type_id'             => $case['post_type_id'],
				'veu_menu_position'            => '10',
			);
			// Supports（veu_post_type_items）を POST に含めるかどうかをケースで切替.
			// Decide whether to include veu_post_type_items in POST per case.
			if ( $case['include_post_type_items_post'] ) {
				$_POST['veu_post_type_items'] = $case['post_type_items_post'];
			}

			VK_Post_Type_Manager::save_cf_value( $post_id );

			// Post Type ID メタの期待値を検証 / Verify Post Type ID meta.
			$this->assertSame(
				$case['expected_post_type_id_meta'],
				get_post_meta( $post_id, 'veu_post_type_id', true ),
				$case['test_condition_name']
			);

			$saved_items = get_post_meta( $post_id, 'veu_post_type_items', true );

			if ( $case['expected_meta_saved'] ) {
				// custom-fields が強制補完されメタが保存されている事を検証.
				// Verify that custom-fields is force-injected and meta is persisted.
				$this->assertIsArray( $saved_items, $case['test_condition_name'] );
				$this->assertArrayHasKey( 'custom-fields', $saved_items, $case['test_condition_name'] );
				$this->assertSame( 'true', $saved_items['custom-fields'], $case['test_condition_name'] );
			} else {
				// バリデーション失敗時はメタが更新されていない事を検証.
				// On validation failure, meta should not be updated.
				$this->assertSame( '', $saved_items, $case['test_condition_name'] );
			}

			// バリデーションエラー transient の状態を検証.
			// Verify validation error transient state.
			$errors = get_transient( $this->get_validation_error_key( $post_id ) );
			if ( $case['expected_validation_error'] ) {
				$this->assertIsArray( $errors, $case['test_condition_name'] );
				$this->assertNotEmpty( $errors, $case['test_condition_name'] );
			} else {
				$this->assertFalse( $errors, $case['test_condition_name'] );
			}

			// 後片付け / Cleanup.
			delete_transient( $this->get_validation_error_key( $post_id ) );
			delete_transient( $this->get_validation_draft_key( $post_id ) );
			wp_delete_post( $post_id, true );
			$_POST = array();
		}
	}

	/**
	 * Required satisfied: meta should be saved.
	 */
	public function test_save_cf_value_with_required_fields_saves_meta() {
		$post_id = $this->create_post_type_manage_post();

		$_POST = array(
			'noncename__post_type_manager' => $this->create_post_type_manager_nonce(),
			'veu_post_type_id'             => 'event',
			'veu_post_type_items'          => array( 'title' => 'true' ),
			'veu_menu_position'            => '10',
			'veu_menu_icon'                => 'dashicons-admin-post',
			'veu_post_type_export_to_api'  => 'true',
		);

		VK_Post_Type_Manager::save_cf_value( $post_id );

		$this->assertSame( 'event', get_post_meta( $post_id, 'veu_post_type_id', true ) );
		$saved_items = get_post_meta( $post_id, 'veu_post_type_items', true );
		$this->assertIsArray( $saved_items );
		// custom-fields は強制有効化されるため必ず含まれる（issue #1322）.
		// custom-fields must always be included (issue #1322).
		$this->assertArrayHasKey( 'custom-fields', $saved_items );
		$this->assertSame( 'true', $saved_items['custom-fields'] );
		$this->assertSame( '10', get_post_meta( $post_id, 'veu_menu_position', true ) );
		$this->assertSame( 'dashicons-admin-post', get_post_meta( $post_id, 'veu_menu_icon', true ) );
		$this->assertSame( 'true', get_post_meta( $post_id, 'veu_post_type_export_to_api', true ) );
	}

	/**
	 * custom-fields should always be present in saved meta even when not posted.
	 * issue #1322
	 */
	public function test_save_cf_value_always_includes_custom_fields_meta() {
		// テスト条件と期待値の配列。custom-fields の有無に関わらず必ずメタに含まれる事を確認する.
		// Each case verifies that 'custom-fields' is always present in saved meta.
		$test_cases = array(
			array(
				'test_condition_name' => 'POST に custom-fields が含まれない場合でも、保存後メタに custom-fields => true が含まれる',
				'post_items'          => array( 'title' => 'true' ),
			),
			array(
				'test_condition_name' => 'POST に複数項目（title, editor）が含まれていても、custom-fields も自動付与される',
				'post_items'          => array(
					'title'  => 'true',
					'editor' => 'true',
				),
			),
			array(
				'test_condition_name' => 'POST に custom-fields が明示指定されている場合も、メタに custom-fields => true が保持される',
				'post_items'          => array(
					'title'         => 'true',
					'custom-fields' => 'true',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$post_id = $this->create_post_type_manage_post();

			$_POST = array(
				'noncename__post_type_manager' => $this->create_post_type_manager_nonce(),
				'veu_post_type_id'             => 'event',
				'veu_post_type_items'          => $case['post_items'],
			);

			VK_Post_Type_Manager::save_cf_value( $post_id );

			$saved_items = get_post_meta( $post_id, 'veu_post_type_items', true );
			$this->assertIsArray( $saved_items, $case['test_condition_name'] );
			$this->assertArrayHasKey( 'custom-fields', $saved_items, $case['test_condition_name'] );
			$this->assertSame( 'true', $saved_items['custom-fields'], $case['test_condition_name'] );

			// 後片付け / Cleanup.
			wp_delete_post( $post_id, true );
			$_POST = array();
		}
	}

	/**
	 * Registered CPT should always declare 'custom-fields' support, even when meta does not include it.
	 * Issue #1322 の仕様に伴うテーブル駆動テスト。
	 *
	 * Table-driven test verifying that VK_Post_Type_Manager::add_post_type() always
	 * registers the CPT with custom-fields support, regardless of stored meta state
	 * (legacy data without custom-fields, partial meta, or already-explicit meta).
	 */
	public function test_add_post_type_registers_with_custom_fields_support() {
		// テスト条件と期待値の配列 / Test conditions and expected results.
		$test_cases = array(
			array(
				'test_condition_name' => 'メタに veu_post_type_items が未保存（旧データ）でも、登録時に custom-fields がサポートされる',
				'post_type_id'        => 'force_cf_cpt_legacy',
				'set_post_type_items' => false,
				'post_type_items'     => null,
				'expected_supports'   => array( 'title', 'custom-fields' ),
			),
			array(
				'test_condition_name' => 'メタに title のみ含まれる場合でも、登録時に custom-fields が補完される',
				'post_type_id'        => 'force_cf_cpt_partial',
				'set_post_type_items' => true,
				'post_type_items'     => array( 'title' => 'true' ),
				'expected_supports'   => array( 'title', 'custom-fields' ),
			),
			array(
				'test_condition_name' => 'メタに既に custom-fields が含まれる場合は重複追加されない（境界値）',
				// 投稿タイプ名は WP の20文字制限に合わせて add_post_type() 側で切り詰められるため、20文字以内の値にする。
				// Keep within WordPress' 20-char post type name limit, since add_post_type() truncates longer names.
				'post_type_id'        => 'force_cf_explicit',
				'set_post_type_items' => true,
				'post_type_items'     => array(
					'title'         => 'true',
					'custom-fields' => 'true',
				),
				'expected_supports'   => array( 'title', 'custom-fields' ),
			),
		);

		foreach ( $test_cases as $case ) {
			// ケース毎にクリーンな post_type_manage 投稿を作成.
			// Create a fresh post_type_manage post per case.
			$post_id = $this->create_post_type_manage_post(
				array(
					'post_title' => 'Force CF CPT (' . $case['post_type_id'] . ')',
				)
			);

			update_post_meta( $post_id, 'veu_post_type_id', $case['post_type_id'] );
			if ( $case['set_post_type_items'] ) {
				update_post_meta( $post_id, 'veu_post_type_items', $case['post_type_items'] );
			}

			// 念のため既に登録されていれば一度解除する / Unregister if already registered.
			if ( post_type_exists( $case['post_type_id'] ) ) {
				unregister_post_type( $case['post_type_id'] );
			}

			VK_Post_Type_Manager::add_post_type();

			// 期待される全 supports が宣言されている事を検証.
			// Verify that all expected supports are declared.
			foreach ( $case['expected_supports'] as $support ) {
				$this->assertTrue(
					post_type_supports( $case['post_type_id'], $support ),
					$case['test_condition_name'] . ' / supports: ' . $support
				);
			}

			// 後片付け / Cleanup.
			unregister_post_type( $case['post_type_id'] );
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * Validation error redirect should remove core success message.
	 */
	public function test_add_validation_error_query_arg_removes_message_param() {
		$post_id = $this->create_post_type_manage_post();

		// Set error transient to simulate validation failure.
		set_transient( $this->get_validation_error_key( $post_id ), array( 'dummy' ), 30 );

		$location = admin_url( 'post.php?post=' . $post_id . '&action=edit&message=1' );
		$location = VK_Post_Type_Manager::add_validation_error_query_arg( $location, $post_id );

		$parsed = wp_parse_url( $location );
		$query  = array();
		if ( ! empty( $parsed['query'] ) ) {
			parse_str( $parsed['query'], $query );
		}

		$this->assertSame( '1', $query['veu_ptm_validation_error'] );
		$this->assertArrayNotHasKey( 'message', $query );
	}

	/**
	 * Import existing settings should prefer global settings when present.
	 */
	public function test_ajax_check_taxonomy_shared_existing_settings_prefers_global_settings() {
		$post_a = $this->create_post_type_manage_post(
			array(
				'post_title' => 'CPT A',
			)
		);
		$post_b = $this->create_post_type_manage_post(
			array(
				'post_title' => 'CPT B',
			)
		);

		update_post_meta( $post_a, 'veu_post_type_id', 'cpt_a' );
		update_post_meta(
			$post_a,
			'veu_taxonomy',
			array(
				1 => array(
					'slug'     => 'test-cat4',
					'label'    => 'Label from meta',
					'tag'      => 'true',
					'rest_api' => 'false',
				),
			)
		);

		update_option(
			'veu_global_taxonomy_settings',
			array(
				'test-cat4' => array(
					'label'    => 'Global label',
					'tag'      => 'false',
					'rest_api' => 'true',
				),
			)
		);

		$json = $this->run_ajax_and_get_json(
			array(
				'nonce'           => wp_create_nonce( 'check_taxonomy_shared' ),
				'taxonomy_slug'   => 'test-cat4',
				'current_post_id' => (string) $post_b,
			)
		);

		$this->assertTrue( $json['success'] );
		$this->assertSame( 'Global label', $json['data']['existing_settings']['label'] );
		$this->assertSame( 'false', $json['data']['existing_settings']['tag'] );
		$this->assertSame( 'true', $json['data']['existing_settings']['rest_api'] );
	}

	/**
	 * Import existing settings should be retrieved from matching post meta when no global settings exist.
	 */
	public function test_ajax_check_taxonomy_shared_existing_settings_falls_back_to_post_meta_scan() {
		$post_a = $this->create_post_type_manage_post(
			array(
				'post_title' => 'CPT A',
			)
		);
		$post_b = $this->create_post_type_manage_post(
			array(
				'post_title' => 'CPT B',
			)
		);

		update_post_meta( $post_a, 'veu_post_type_id', 'cpt_a' );

		// Ensure there is at least one other post that does NOT match, so "first post only" would fail.
		update_post_meta(
			$post_b,
			'veu_taxonomy',
			array(
				1 => array(
					'slug'     => 'other-slug',
					'label'    => 'Other',
					'tag'      => 'false',
					'rest_api' => 'true',
				),
			)
		);

		update_post_meta(
			$post_a,
			'veu_taxonomy',
			array(
				1 => array(
					'slug'     => 'test-cat4',
					'label'    => 'Recruit status',
					'tag'      => 'true',
					'rest_api' => 'true',
				),
			)
		);

		// No global settings.
		delete_option( 'veu_global_taxonomy_settings' );

		$json = $this->run_ajax_and_get_json(
			array(
				'nonce'           => wp_create_nonce( 'check_taxonomy_shared' ),
				'taxonomy_slug'   => 'test-cat4',
				'current_post_id' => (string) $post_b,
			)
		);

		$this->assertTrue( $json['success'] );
		$this->assertSame( 'Recruit status', $json['data']['existing_settings']['label'] );
		$this->assertSame( 'true', $json['data']['existing_settings']['tag'] );
		$this->assertSame( 'true', $json['data']['existing_settings']['rest_api'] );
	}
}
