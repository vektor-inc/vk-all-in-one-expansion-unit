<?php

class VEU_Metabox_Sitemap {

	public static $slug    = 'veu_sitemap';
	public static $cf_name = 'sitemap_hide';

	public static function init() {
		// add_action( 'admin_menu', array( __CLASS__, 'add_individual_metabox' ) );
		add_action( 'veu_post_metabox_body', array( __CLASS__, 'the_meta_section' ), 50 );
		add_action( 'save_post', array( __CLASS__, 'save_custom_field' ) );
	}

	public static function metabox_title() {
		return __( 'Hide setting of HTML sitemap', 'vk-all-in-one-expansion-unit' );
	}

	/**
	 * add_individual_metabox
	 * === Now use common metabox that this function is not used
	 */
	public static function add_individual_metabox() {
		$title = self::metabox_title();
		add_meta_box( self::$slug, $title, array( __CLASS__, 'metabox_body' ), 'page', 'normal', 'high' );
	}


	/**
	 * the_meta_section
	 *
	 * @return [type] [description]
	 */
	public static function the_meta_section() {

		if ( get_post_type() != 'page' ) {
			return;
		}

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

		$body  = '';
		$body .= wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__' . self::$cf_name, true, false );

		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Hide this page to HTML Sitemap.', 'vk-all-in-one-expansion-unit' );

		$body .= '<ul>';
		$body .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( self::$cf_name ) . '" name="' . esc_attr( self::$cf_name ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$body .= '</ul>';

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
		if ( ! empty( $_POST[ self::$cf_name ] ) ) {
			add_post_meta( $post_id, self::$cf_name, $_POST[ self::$cf_name ] );
		}

	}
}

VEU_Metabox_Sitemap::init();
