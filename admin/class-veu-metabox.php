<?php

class VEU_Metabox {

	/**
	 * @var array {
	 *  slug : string
	 *  cf_name : string
	 *  title : string
	 *  priority : int
	 *  individual : bool
	 *  poat_types : array
	 * } $args
	 */
	public $args;
	public $veu_get_common_options;

	public function __construct( $args = array() ) {

		$this->veu_get_common_options = veu_get_common_options();

		$post_type_paras = array(
			'public' => true,
		);

		$defaults = array(
			'slug'       => '',
			'cf_name'    => '',
			'title'      => '',
			'priority'   => 10,
			'individual' => $this->veu_get_common_options['post_metabox_individual'],
			'post_types' => get_post_types( $post_type_paras ),
		);

		$this->args = wp_parse_args( $args, $defaults );

		if ( $this->args['individual'] ) {
			// 各機能毎に独立metaboxを表示
			// 通常メタボックスの追加タイミングは admin_menu だが,
			// ここでは admin_init でないと反映されないため
			add_action( 'admin_init', array( $this, 'add_individual_metabox' ) );

		} else {

			// Parent metabox activate
			// 実行タイミングで正常に動作しないのでコメントアウト
			add_filter( 'veu_parent_metabox_activation', array( $this, 'metabox_activate' ), 10, 1 );
			// 共通のメタボックスの中身を呼び込む
			add_action( 'veu_post_metabox_body', array( $this, 'the_meta_section' ), $this->args['priority'] );

			/*
			VEU_Metabox 内の get_post_type が実行タイミングによっては
			カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
			admin_menu のタイミングで読み込んでいる
			*/
			add_action( 'admin_menu', array( $this, 'add_sub_parent_metabox_insert_items' ) );
		}

		add_action( 'save_post', array( $this, 'save_custom_field' ) );

	}

	public function add_sub_parent_metabox_insert_items() {
		// 子ページリストやサイトマップなど「挿入アイテムの設定」を読み込むための子metaboxを読み込む
		require_once dirname( __FILE__ ) . '/class-veu-metabox-insert-items.php';
	}


	// 実行タイミングで正常に動作しない事があるため
	// veu_is_parent_metabox_display_maual() で手動補正している
	public function metabox_activate( $flag ) {
		foreach ( $this->args['post_types'] as $key => $post_type ) {
			return true;
		}
	}

	/**
	 * add_individual_metabox
	 * === Now use common metabox that this function is not used
	 */
	public function add_individual_metabox() {
		// add_meta_box( 'aaa', 'ArrayIterator', array( $this, 'metabox_body' ), 'page', 'normal', 'high' );
		foreach ( $this->args['post_types'] as $key => $post_type ) {
			add_meta_box( $this->args['slug'], $this->args['title'], array( $this, 'metabox_body' ), $post_type, 'normal', 'high' );
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
				echo '<h3 class="veu_metabox_section_title">' . wp_kses_post( $this->args['title'] ) . '';
				echo '<span class="veu_metabox_section_title_status_btn close"><i class="fas fa-caret-down"></i></span>';
				echo '<span class="veu_metabox_section_title_status_btn open"><i class="fas fa-caret-up"></i></span>';
				echo '</h3>';
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
