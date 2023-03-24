<?php
class VEU_Page_Exclude_From_List_Pages {
	const META_KEY = 'exclude_from_list_pages';
	private static $instance = null;

	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_exclude_from_list_pages_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_exclude_from_list_pages_meta_data' ) );
		add_filter( 'wp_list_pages_excludes', array( $this, 'exclude_pages_from_list_pages' ) );
	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_exclude_from_list_pages_meta_box() {
		add_meta_box(
			'exclude_from_list_pages_meta_box',
			__( 'ページリストからの除外設定', 'vk-all-in-one-expansion-unit' ),
			array( $this, 'exclude_from_list_pages_meta_box_callback' ),
			'page',
			'side',
			'high'
		);
	}

	public function exclude_from_list_pages_meta_box_callback( $post ) {
		wp_nonce_field( 'exclude_from_list_pages_meta_box', 'exclude_from_list_pages_meta_box_nonce' );
		$exclude_from_list_pages = get_post_meta( $post->ID, self::META_KEY, true );
		?>
		<p>
		  <input type="checkbox" id="exclude_from_list_pages" name="exclude_from_list_pages" value="true" <?php checked( $exclude_from_list_pages, 'true' ); ?>>
		  <label for="exclude_from_list_pages"><?php _e( 'ページリスト（wp_list_pages）の表示から除外する', 'vk-all-in-one-expansion-unit' ); ?></label>
		</p>
		<?php
	}

	public function save_exclude_from_list_pages_meta_data( $post_id ) {
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
			update_post_meta( $post_id, self::META_KEY, 'true' );
		} else {
			delete_post_meta( $post_id, self::META_KEY );
		}
	}

	public function exclude_pages_from_list_pages( $exclude_array ) {
		$excluded_pages = get_posts(
			array(
				'post_type'      => 'page',
				'meta_key'       => self::META_KEY,
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