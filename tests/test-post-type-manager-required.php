<?php
/**
 * Tests for Post Type Manager required validation.
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Post Type Manager required validation test case.
 */
class PostTypeManagerRequiredValidationTest extends WP_UnitTestCase {

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
	 * @return int Post ID.
	 */
	private function create_post_type_manage_post() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => 'post_type_manage',
				'post_status' => 'publish',
				'post_title'  => 'Test CPT Setting',
			)
		);

		// Ensure global $post is available (the code references it).
		$GLOBALS['post'] = get_post( $post_id );

		return $post_id;
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
}
