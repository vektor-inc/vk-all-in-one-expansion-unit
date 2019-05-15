<?php

class VEU_Metabox {

	public $args;

	public function __construct( $args = array() ) {

		$post_type_paras = array(
			'public' => true,
		);

		$defaults = array(
			'slug'       => '',
			'cf_name'    => '',
			'title'      => '',
			'priority'   => 10,
			'individual' => false,
			'post_types' => get_post_types( $post_type_paras ),
		);

		$this->args = wp_parse_args( $args, $defaults );

		if ( $this->args['individual'] ) {
			add_action( 'admin_menu', array( $this, 'add_individual_metabox' ) );
		} else {
			add_action( 'veu_post_metabox_body', array( $this, 'the_meta_section' ), $this->args['priority'] );
		}
		add_action( 'save_post', array( $this, 'save_custom_field' ) );
	}

	/**
	 * add_individual_metabox
	 * === Now use common metabox that this function is not used
	 */
	public function add_individual_metabox() {
		foreach ( $this->args['post_types'] as $key => $post_type ) {
			add_meta_box( $this->args['slug'], $this->args['title'], array( $this, 'metabox_body' ), 'page', 'normal', 'high' );
		}
	}

	/**
	 * the_meta_section
	 *
	 * @return [type] [description]
	 */
	public function the_meta_section() {

		// 今編集しているページの投稿タイプ
		$now_post_type = get_post_type();

		// このメタボックスを表示する投稿タイプの時
		if ( is_array( $this->args['post_types'] ) && in_array( $now_post_type, $this->args['post_types'] ) ) {
			// Outer class
			$outer_class = '';
			if ( ! empty( $this->args['slug'] ) ) {
				$outer_class = ' ' . $this->args['slug'];
			}
			echo '<div class="veu_metabox_section' . $outer_class . '">';
			// Section title
			if ( ! empty( $this->args['title'] ) ) {
				echo '<h3 class="veu_metabox_section_title">' . wp_kses_post( $this->args['title'] ) . '</h3>';
			}
			echo '<div class="veu_metabox_section_body">';
			echo $this->metabox_body( false );
			echo '</div><!-- [ /.veu_metabox_section_body ] -->';
			echo '</div><!-- [ /.veu_metabox_section ] -->';
		}

	} // if ( is_array( $this->args['post_types'] ) && in_array( $now_post_type, $this->args['post_types'] ) ) {

	/**
	 * metabox_body
	 * フォームの外側共通部分
	 *
	 * @return [type] [description]
	 */
	public function metabox_body( $display = true ) {

		global $post;
		$cf_value = get_post_meta( get_the_id(), $this->args['cf_name'], true );

		$body  = '';
		$body .= wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__' . $this->args['cf_name'], true, false );

		$body .= $this->metabox_body_form( $cf_value );

		if ( $display ) {
			echo $body;
		} else {
			return $body;
		}
	}

	/**
	 * metabox_body_form
	 * フォーム内側部分。クラスの継承で上書きする前提
	 *
	 * @return [type] [description]
	 */
	public function metabox_body_form( $cf_value ) {

		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = __( 'Hide this page to HTML Sitemap.', 'vk-all-in-one-expansion-unit' );

		$form  = '';
		$form .= '<ul>';
		$form .= '<li><label>' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

	public function save_custom_field( $post_id ) {

		// if autosave then deny
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id; }

		// 設定したnonce を取得（CSRF対策）
		$noncename__value = isset( $_POST[ 'noncename__' . $this->args['cf_name'] ] ) ? $_POST[ 'noncename__' . $this->args['cf_name'] ] : null;

		// nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
		if ( ! wp_verify_nonce( $noncename__value, wp_create_nonce( __FILE__ ) ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, $this->args['cf_name'] );
		if ( ! empty( $_POST[ $this->args['cf_name'] ] ) ) {
			add_post_meta( $post_id, $this->args['cf_name'], $_POST[ $this->args['cf_name'] ] );
		}

	}
} // class VEU_Metabox {
