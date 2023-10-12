<?php
/**
 * VK_Front_Page_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

class VK_Front_Page_Srtuctured_Data {

	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'the_front_page_structure_data' ), 9999 );
	}

	/**
	 * Print Front Page Structure Data
	 *
	 * @return void
	 */
	public static function the_front_page_structure_data() {
		if ( is_front_page() ) {
			$front_page_array = self::get_front_page_structure_array();
			if ( $front_page_array && is_array( $front_page_array ) ) {
				echo '<!-- [ VK All in One Expansion Unit Front Page Structure Data ] -->';
                echo '<script type="application/ld+json">' . wp_kses( json_encode( $front_page_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), array() ) . '</script>';
				echo '<!-- [ / VK All in One Expansion Unit Front Page Structure Data ] -->';
			}
		}
	}

	/**
	 * フロントページの構造化データの情報を配列で返す
	 *
	 * @return array $front_page_array
	 */
	public static function get_front_page_structure_array() {

		$front_page_array = array(
			'@context'      => 'https://schema.org/',
			'@type'         => 'WebSite',
			'name'          => get_bloginfo('name'),
			'url'           => home_url(),
		);

		return $front_page_array;
	}
}

new VK_Front_Page_Srtuctured_Data();
