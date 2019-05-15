<?php

class VEU_Metabox_SNS_Title {

	public static $slug    = 'veu_sns_title';
	public static $cf_name = 'vkExUnit_sns_title';

	public static function init() {
		// add_action( 'admin_menu', array( __CLASS__, 'add_individual_metabox' ) );
		add_action( 'veu_post_metabox_body', array( __CLASS__, 'the_meta_section' ), 50 );
		add_action( 'save_post', array( __CLASS__, 'save_custom_field' ) );
	}

	public static function metabox_title() {
		return __( 'SNS Title', 'vk-all-in-one-expansion-unit' );
	}

	/**
	 * add_individual_metabox
	 * === Now use common metabox that this function is not used
	 */
	public static function add_individual_metabox() {
		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args, 'object' );
		foreach ( $post_types as $key => $post_type ) {
			$title = self::metabox_title();
			add_meta_box( self::$slug, $title, array( __CLASS__, 'metabox_body' ), $key, 'normal', 'high' );
		}
	}


	/**
	 * the_meta_section
	 *
	 * @return [type] [description]
	 */
	public static function the_meta_section() {

		$args = array(
			'slug'  => self::$slug,
			'title' => self::metabox_title(),
			'body'  => self::metabox_body( false ),
		);

		veu_metabox_section( $args );

	}

	/**
	 * [metabox_body description]
	 *
	 * @return [type] [description]
	 */
	public static function metabox_body( $display = true ) {
		global $post;
		$cf_value = get_post_meta( get_the_id(), self::$cf_name, true );
		$body     = '';
		$body    .= wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__' . self::$cf_name, true, false );
		$body    .= '<input type=text name="' . esc_attr( self::$cf_name ) . '" value="' . esc_attr( $cf_value ) . '" size=50 />';
		$body    .= '<p>' . __( 'if filled this area then override title of OGP and Twitter Card', 'vk-all-in-one-expansion-unit' ) . '</p>';
		if ( $display ) {
			echo $body;
		} else {
			return $body;
		}
	}


	public static function save_custom_field( $post_id ) {

		// if autosave then deny
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id; }

		// 設定したnonce を取得（CSRF対策）
		$noncename__value = isset( $_POST[ 'noncename__' . self::$cf_name ] ) ? $_POST[ 'noncename__' . self::$cf_name ] : null;

		// nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
		if ( ! wp_verify_nonce( $noncename__value, wp_create_nonce( __FILE__ ) ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, self::$cf_name );
		if ( isset( $_POST[ self::$cf_name ] ) ) {
			add_post_meta( $post_id, self::$cf_name, $_POST[ self::$cf_name ] );
		}

	}
}

VEU_Metabox_SNS_Title::init();
