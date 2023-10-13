<?php
/**
 * VK_WebSite_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

class VK_WebSite_Srtuctured_Data {

	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'the_website_structure_data' ), 9999 );
	}

	/**
	 * Print Website Structure Data
	 *
	 * @return void
	 */
	public static function the_website_structure_data() {
		if ( is_front_page() ) {
			$website_array = self::get_website_structure_array();
			if ( $website_array && is_array( $website_array ) ) {
				echo '<!-- [ VK All in One Expansion Unit WebSite Structure Data ] -->';
                echo '<script type="application/ld+json">' . wp_kses( json_encode( $website_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), array() ) . '</script>';
				echo '<!-- [ / VK All in One Expansion Unit WebSite Structure Data ] -->';
			}
		}
	}

	/**
	 * フロントページの構造化データの情報を配列で返す
	 *
	 * @return array $website_array
	 */
	public static function get_website_structure_array() {

		$website_array = array(
			'@context'      => 'https://schema.org/',
			'@type'         => 'WebSite',
			'name'          => get_bloginfo('name'),
			'url'           => home_url(),
		);

		return $website_array;
	}
}

new VK_WebSite_Srtuctured_Data();
