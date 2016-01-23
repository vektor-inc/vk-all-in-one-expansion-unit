<?php
/**
 * VkExUnit auto_eyecatch.php
 * insert thumbnail for top of content automatically
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    8/Jul/2015
 */

class vExUnit_eyecatch {
	private static $instance;

	public static $allowed_post_types = array( 'post', 'page' );

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vExUnit_eyecatch;
		self::$instance->run_init();
		return self::$instance;
	}


	private function __construct() {
		/***    do noting    ***/
	}


	protected function run_init() {
		add_action( 'admin_menu', array( $this, 'add_custom_field' ) );
		add_action( 'save_post' , array( $this, 'save_custom_field' ) );
		add_filter( 'the_content',    array( $this, 'set_eyecatch' ), 1 );
	}


	public function add_custom_field() {
		foreach ( self::$allowed_post_types as $post_type ) {
			add_meta_box( 'vkExUnit_EyeCatch', __( 'Automatic EyeCatch', 'vkExUnit' ), array( $this, 'render_meta_box' ), $post_type, 'normal', 'high' );
		}
	}


	public function render_meta_box() {
		global $post;
		$disable_autoeyecatch = get_post_meta( get_the_id(), 'vkExUnit_EyeCatch_disable', true );

		echo '<input type="hidden" name="_nonce_vkExUnit__custom_auto_eyecatch" id="_nonce_vkExUnit__custom_auto_eyecatch_noonce" value="'.wp_create_nonce( "vkEx_AYC_" . get_the_id() ).'" />';
		echo '<label ><input type="checkbox" name="vkExUnit_auto_eyecatch" value="true" ' . ( ($disable_autoeyecatch)? 'checked' : '' ) . ' />'.__( 'Do not set eyecatch image automatic.', 'vkExUnit' ).'</label>';

	}


	public function save_custom_field( $post_id ) {

		$metaKeyword = isset( $_POST['_nonce_vkExUnit__custom_auto_eyecatch'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_auto_eyecatch'] ) : null;

		$keyword = get_post_meta( $post_id, 'vkExUnit_metaKeyword', true );

		// if autosave then deny
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id; }

		if ( ! wp_verify_nonce( $metaKeyword, "vkEx_AYC_" . $post_id ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, 'vkExUnit_EyeCatch_disable' );
		if ( isset( $_POST['vkExUnit_auto_eyecatch'] ) && $_POST['vkExUnit_auto_eyecatch'] ) {
			add_post_meta( $post_id, 'vkExUnit_EyeCatch_disable', true );
		}
	}


	public static function is_my_turn() {

		if ( vkExUnit_is_excerpt() ) { return false; }

		global $is_pagewidget;
		if( $is_pagewidget ){ return false; }

		if ( get_the_id() ) {

			if ( in_array( get_post_type( get_the_id() ), self::$allowed_post_types ) ) {

				if ( has_post_thumbnail( get_the_id() ) ) {

					if ( ! get_post_meta( get_the_id(), 'vkExUnit_EyeCatch_disable', true ) ) {

						return true;
					}
				}
			}
		}

		return false;
	}


	public function set_eyecatch( $content ) {

		if ( ! self::is_my_turn() ) {  return $content; }

		$imageHtml = self::render_eyecatch( get_the_id() );

		$content = $imageHtml . $content;

		return $content;
	}


	public function render_eyecatch( $post_id ) {
		$html = '';

		$image_tag = get_the_post_thumbnail( $post_id, 'large' );

		$html = '<div class="veu_autoEyeCatchBox">' . $image_tag . '</div>';
		return $html;
	}
}

vExUnit_eyecatch::instance();
