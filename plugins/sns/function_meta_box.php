<?php

class vkExUnit_sns_metabox {

	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vkExUnit_sns_metabox;
		self::$instance->run_init();
		return self::$instance;
	}


	protected function run_init() {
		add_action( 'admin_menu', array( $this, 'add_custom_field' ) );
		add_action( 'save_post', array( $this, 'save_custom_field' ) );
	}


	public function add_custom_field() {
		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args, 'object' );
		foreach ( $post_types as $key => $post_type ) {
			add_meta_box( 'vkExUnit_SnsTitle', __( 'Sns Title', 'vk-all-in-one-expansion-unit' ), array( $this, 'render_meta_box' ), $key, 'normal', 'high' );
		}
	}


	public function render_meta_box() {
		global $post;
		$disable_autoeyecatch = get_post_meta( get_the_id(), 'vkExUnit_sns_title', true );

		echo '<input type="hidden" name="_nonce_vkExUnit_sns_title" id="_nonce_vkExUnit_sns_title_noonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		echo '<input type=text name="vkExUnit_sns_title" value="' . $disable_autoeyecatch . '" size=50 />';
		echo '<p>' . __( 'if filled this area then override title of OGP and Twitter Card', 'vk-all-in-one-expansion-unit' ) . '</p>';

	}


	public function save_custom_field( $post_id ) {

		$metaKeyword = isset( $_POST['_nonce_vkExUnit_sns_title'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit_sns_title'] ) : null;

		// if autosave then deny
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id; }

		if ( ! wp_verify_nonce( $metaKeyword, plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, 'vkExUnit_sns_title' );
		if ( isset( $_POST['vkExUnit_sns_title'] ) ) {
			add_post_meta( $post_id, 'vkExUnit_sns_title', $_POST['vkExUnit_sns_title'] );
		}
	}
}


vkExUnit_sns_metabox::instance();
