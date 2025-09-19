<?php
/**
 * Taxonomy Title Tag Setting
 *
 * @package VK All in One Expansion Unit
 */

class VEU_Taxonomy_Head_Title {

	public function __construct() {
		// カテゴリー編集画面にフィールドを追加
		add_action( 'category_edit_form_fields', array( $this, 'add_title_field' ) );
		// タグ編集画面にフィールドを追加
		add_action( 'post_tag_edit_form_fields', array( $this, 'add_title_field' ) );

		// カスタムタクソノミーにも対応（動的にフックを追加）
		add_action( 'admin_init', array( $this, 'add_custom_taxonomy_hooks' ) );

		// 保存処理
		add_action( 'edited_category', array( $this, 'save_title_field' ) );
		add_action( 'edited_post_tag', array( $this, 'save_title_field' ) );
		add_action( 'edited_term', array( $this, 'save_title_field' ) );
	}

	/**
	 * カスタムタクソノミー用のフックを動的に追加
	 */
	public function add_custom_taxonomy_hooks() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			// カテゴリーとタグは既に登録済みなのでスキップ
			if ( in_array( $taxonomy->name, array( 'category', 'post_tag' ) ) ) {
				continue;
			}

			// カスタムタクソノミーの編集画面フックを追加
			$hook_name = $taxonomy->name . '_edit_form_fields';
			add_action( $hook_name, array( $this, 'add_title_field' ) );
		}
	}

	/**
	 * タクソノミー編集画面にタイトルタグ設定フィールドを追加
	 *
	 * @param WP_Term $term
	 */
	public function add_title_field( $term ) {
		$term_id  = $term->term_id;
		$taxonomy = $term->taxonomy;

		// カスタムタクソノミーの場合は別の処理
		if ( ! is_object( $term ) ) {
			return;
		}

		$meta_key  = 'veu_taxonomy_title';
		$term_meta = get_term_meta( $term_id, $meta_key, true );

		// 共通ヘルパーを使用
		echo VEU_Title_Form_Helper::render_taxonomy_form_row( $meta_key, $term_meta );
	}

	/**
	 * タクソノミーのタイトルタグ設定を保存
	 *
	 * @param int $term_id
	 */
	public function save_title_field( $term_id ) {
		$meta_key = 'veu_taxonomy_title';

		// CSRF保護: nonce検証
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-tag_' . $term_id ) ) {
			return;
		}

		// 権限チェック: タクソノミーに応じた適切な権限をチェック
		$term = get_term( $term_id );
		if ( ! $term || is_wp_error( $term ) ) {
			return;
		}

		$taxonomy = get_taxonomy( $term->taxonomy );
		if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
			return;
		}

		// データの取得と検証
		if ( isset( $_POST[ $meta_key ] ) ) {
			$title_data = array(
				'title'          => sanitize_text_field( $_POST[ $meta_key ]['title'] ),
				'add_site_title' => isset( $_POST[ $meta_key ]['add_site_title'] ) ? 1 : 0,
			);

			// 空の場合は削除
			if ( empty( $title_data['title'] ) && empty( $title_data['add_site_title'] ) ) {
				delete_term_meta( $term_id, $meta_key );
			} else {
				update_term_meta( $term_id, $meta_key, $title_data );
			}
		}
	}

	/**
	 * タクソノミーのカスタムタイトルタグを取得
	 *
	 * @param int $term_id
	 * @return array|false
	 */
	public static function get_taxonomy_title( $term_id ) {
		return get_term_meta( $term_id, 'veu_taxonomy_title', true );
	}
} // class VEU_Taxonomy_Head_Title {
