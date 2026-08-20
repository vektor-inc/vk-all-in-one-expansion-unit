<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_CSS_Customize extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_custom_css',
			'cf_name'  => '_veu_custom_css',
			'title'    => __( 'Custom CSS', 'vk-all-in-one-expansion-unit' ),
			'priority' => 100,
		);

		parent::__construct( $this->args );
	}

	/**
	 * metabox_body_form
	 * Form inner
	 *
	 * @return [type] [description]
	 */
	public function metabox_body_form( $cf_value ) {

		$form = '';

		$form .= '<textarea name="' . esc_attr( $this->args['cf_name'] ) . '" id="' . esc_attr( $this->args['cf_name'] ) . '" rows="5" cols="30" style="width:100%;">' . esc_textarea( $cf_value ) . '</textarea>';

		return $form;
	}

	/**
	 * Override parent save to sanitize CSS payloads before persisting.
	 *
	 * @param int $post_id Current post ID.
	 * @return int
	 */
	public function save_custom_field( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$nonce_key = 'noncename__' . $this->args['cf_name'];
		// nonce 自体の値なので wp_verify_nonce() に渡す前に wp_unslash() + sanitize_text_field() を通す。
		// This is the nonce value itself, so unslash and sanitize it with sanitize_text_field()
		// before passing it to wp_verify_nonce().
		$nonce_value = isset( $_POST[ $nonce_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ) : null;

		if ( ! wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return $post_id;
		}

		// nonce 検証だけでは CSRF は防げても権限の無いユーザーによる保存は防げないため、
		// 投稿の編集権限があるかどうかを確認する（多層防御）。
		// nonce verification alone does not prevent a user without edit permission from saving,
		// so also confirm the current user can edit this post ( defense in depth ).
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, $this->args['cf_name'] );

		if ( empty( $_POST[ $this->args['cf_name'] ] ) ) {
			return $post_id;
		}

		// WordPress は magic quotes 互換のため $_POST の値にスラッシュを付加して渡してくるので、
		// veu_sanitize_custom_css_input() へ渡す前に wp_unslash() で除去する。サニタイズ自体は
		// veu_sanitize_custom_css_input()（wp_strip_all_tags() 等）が行うが、phpcs の静的解析は
		// 独自関数内のサニタイズを認識できないため、この行単体では未サニタイズと誤検知される。
		// WordPress adds slashes to $_POST values for magic-quotes compatibility, so strip them
		// with wp_unslash() before passing the value to veu_sanitize_custom_css_input(). The actual
		// sanitization happens inside veu_sanitize_custom_css_input() ( via wp_strip_all_tags(), etc. ),
		// but phpcs's static analysis cannot see sanitization performed inside a custom function, so
		// this line alone is flagged as a false positive.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by veu_sanitize_custom_css_input() on the next line ( custom sanitizer not recognized by static analysis ).
		$raw_css       = wp_unslash( $_POST[ $this->args['cf_name'] ] );
		$sanitized_css = veu_sanitize_custom_css_input( $raw_css );
		if ( '' !== $sanitized_css ) {
			// add_post_meta() は「スラッシュ付きの値を受け取り、内部で wp_unslash() する」仕様
			// ( expected_slashed ) のため、上で既に wp_unslash() 済みの $sanitized_css をそのまま渡すと
			// ここでもう一度スラッシュが除去され、CSS に含まれる本物のバックスラッシュ（\f101 のような
			// エスケープ記法など）が消えてしまう。そのため保存直前に wp_slash() で相殺し、
			// $sanitized_css の中身がそのまま保存されるようにする。
			// add_post_meta() expects a slashed value and calls wp_unslash() on it internally
			// ( expected_slashed ). Passing the already-unslashed $sanitized_css as is would strip
			// slashes a second time and corrupt genuine backslashes in the CSS ( e.g. \f101 escape
			// sequences ), so wp_slash() it right before saving to cancel that out and store
			// $sanitized_css unchanged.
			add_post_meta( $post_id, $this->args['cf_name'], wp_slash( $sanitized_css ) );
		}

		return $post_id;
	}
} // class VEU_Metabox_CSS_Customize {

$veu_metabox_css_customize = new VEU_Metabox_CSS_Customize();
