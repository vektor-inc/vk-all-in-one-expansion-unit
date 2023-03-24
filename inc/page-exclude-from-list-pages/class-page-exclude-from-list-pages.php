<?php
/**
 * VEU_Page_Exclude_From_List_Pages
 * 
 * @package vektor-inc/all-in-one-expansion-unit
 */

class VEU_Page_Exclude_From_List_Pages {
	private static $instance = null;

	// ExUnit 独自の統合した独自 UI に表示するため、VEU_Metabox クラスに値を渡しやすいように public にしている
	public static $meta_key      = '_exclude_from_list_pages';
	public static $metabox_id    = 'exclude_from_list_pages';
	public static $metabox_title = '';
	public static $label         = '';

	private function __construct() {

		// ExUnit の統合した独自 UI で表示するため、ここではコメントアウトしている
		// ExUnit の VEU_Metabox を使わない場合は、コメントアウトを解除するとこのクラス単独で処理できる

		// add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		// add_action( 'save_post', array( $this, 'save_meta_data' ) );

		// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

		add_filter( 'wp_list_pages_excludes', array( $this, 'exclude_pages_from_list_pages' ) );
		self::$metabox_title = __( 'Exclusion settings from the page list', 'vk-all-in-one-expansion-unit' );
		self::$label         = __( 'Exclude from displaying Page List (wp_list_pages)', 'vk-all-in-one-expansion-unit' );
	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_meta_box() {
		add_meta_box(
			self::$metabox_id,
			self::$metabox_title,
			array( $this, 'meta_box_callback' ),
			'page',
			'side',
			'high'
		);
	}

	public function get_meta_box_body( $post ) {
		global $post;
		wp_nonce_field( 'exclude_from_list_pages_meta_box', 'exclude_from_list_pages_meta_box_nonce' );
		$exclude_from_list_pages = get_post_meta( $post->ID, self::$meta_key, true );
		$meta_body               = '<p class="vk_checklist_item vk_checklist_item-style-vertical">
		<input type="checkbox" id="' . esc_attr( self::$meta_key ) . '" name="' . esc_attr( self::$meta_key ) . '" value="true" ' . checked( $exclude_from_list_pages, 'true', false ) . '>
		<label for="' . esc_attr( self::$meta_key ) . '" class="vk_checklist_item_label">' . __( self::$label, 'vk-all-in-one-expansion-unit' ) . '</label>
		</p>';
		return $meta_body;
	}

	public function meta_box_callback( $post ) {
		$meta_body = self::get_meta_box_body( $post );
		echo $meta_body;
	}

	public function save_meta_data( $post_id ) {
		if ( ! isset( $_POST['exclude_from_list_pages_meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['exclude_from_list_pages_meta_box_nonce'], 'exclude_from_list_pages_meta_box' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		if ( isset( $_POST['exclude_from_list_pages'] ) ) {
			update_post_meta( $post_id, self::$meta_key, 'true' );
		} else {
			delete_post_meta( $post_id, self::$meta_key );
		}
	}

	public function exclude_pages_from_list_pages( $exclude_array ) {
		$excluded_pages = get_posts(
			array(
				'post_type'      => 'page',
				'meta_key'       => self::$meta_key,
				'meta_value'     => 'true',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $excluded_pages ) ) {
			$exclude_array = array_merge( $exclude_array, $excluded_pages );
		}
		return $exclude_array;
	}
}

VEU_Page_Exclude_From_List_Pages::get_instance();
