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

		add_filter( 'wp_die_handler', $die_filter );

		ob_start();
		try {
			VK_Post_Type_Manager::ajax_check_taxonomy_shared();
		} catch ( Exception $e ) {
			// Expected: wp_send_json_* ends with wp_die().
			$this->assertSame( 'wp_die', $e->getMessage() );
		}
		$output = ob_get_clean();

		remove_filter( 'wp_die_handler', $die_filter );

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
	 * Required: Supports missing should not save meta, but should store draft transient.
	 */
	public function test_save_cf_value_missing_supports_sets_transients_and_does_not_update_meta() {
		$post_id = $this->create_post_type_manage_post();

		update_post_meta( $post_id, 'veu_menu_position', '1' );

		$_POST = array(
			'noncename__post_type_manager' => $this->create_post_type_manager_nonce(),
			'veu_post_type_id'             => 'event',
			// 'veu_post_type_items' is intentionally missing.
			'veu_menu_position'            => '10',
		);

		VK_Post_Type_Manager::save_cf_value( $post_id );

		$this->assertSame( '1', get_post_meta( $post_id, 'veu_menu_position', true ) );
		$this->assertSame( '', get_post_meta( $post_id, 'veu_post_type_id', true ) );

		$errors = get_transient( $this->get_validation_error_key( $post_id ) );
		$this->assertIsArray( $errors );
		$this->assertNotEmpty( $errors );

		$draft = get_transient( $this->get_validation_draft_key( $post_id ) );
		$this->assertIsArray( $draft );
		$this->assertSame( 'event', $draft['veu_post_type_id'] );
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
		$this->assertIsArray( get_post_meta( $post_id, 'veu_post_type_items', true ) );
		$this->assertSame( '10', get_post_meta( $post_id, 'veu_menu_position', true ) );
		$this->assertSame( 'dashicons-admin-post', get_post_meta( $post_id, 'veu_menu_icon', true ) );
		$this->assertSame( 'true', get_post_meta( $post_id, 'veu_post_type_export_to_api', true ) );
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
