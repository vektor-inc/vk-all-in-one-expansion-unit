<?php
/**
 * VkExUnit auto_eyecatch.php
 * insert thumbnail for top of content automatically
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    8/Jul/2015
 */

class VEU_Auto_Eyecatch {
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new VEU_Auto_Eyecatch;
		self::$instance->run_init();
		return self::$instance;
	}

	private function __construct() {
		/***    do noting    */
	}


	protected function run_init() {
		add_filter( 'the_content', array( $this, 'set_eyecatch' ), 1 );
	}

	public static function post_types() {

		$allowed_post_types = apply_filters( 'veu_auto_eye_catch_post_types', array( 'post', 'page' ) );

		return $allowed_post_types;
	}

	public static function is_my_turn() {

		if ( vkExUnit_is_excerpt() ) {
			return false; }

		global $is_pagewidget;
		if ( $is_pagewidget ) {
			return false; }

		if ( get_the_id() ) {

			$post_types = self::post_types();

			if ( in_array( get_post_type( get_the_id() ), $post_types ) ) {

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

		if ( ! self::is_my_turn() ) {
			return $content; }

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

VEU_Auto_Eyecatch::instance();
