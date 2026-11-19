<?php
/**
 * VK_WebSite_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
				// JSON-LD は HTML ではなく JSON として読まれるため、& を &amp; に変える wp_kses() は通さない。
				// JSON_HEX_TAG を外さないこと。< と > を \u003C・\u003E にして、値の中の </script> や <!-- で script 要素が閉じたり壊れたりするのを防いでいる。
				// JSON-LD is read as JSON, not as HTML, so it is not passed through wp_kses(), which turns & into &amp;.
				// Do not remove JSON_HEX_TAG. It turns < and > into \u003C and \u003E, so a </script> or <!-- in a value cannot close or break the script element.
				echo '<script type="application/ld+json">' . wp_json_encode( $website_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON_HEX_TAG により < と > を含まない JSON を出力するため。
				echo '<!-- [ / VK All in One Expansion Unit WebSite Structure Data ] -->';
			}
		}
	}

	/**
	 * フロントページの構造化データの情報を配列で返す
	 * name は文字参照（&amp; など）を元の文字に戻した文字列で、HTML 用にはエスケープしていない。
	 *
	 * Return the structured data of the front page as an array.
	 * name is a string with character references (such as &amp;) turned back into characters, and is not escaped for HTML.
	 *
	 * @return array $website_array フロントページの構造化データ。 Structured data of the front page.
	 */
	public static function get_website_structure_array() {

		$website_array = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'WebSite',
			// サイト名は保存時に & が &amp; などになるため、検索エンジンに文字参照のまま伝わらないよう元の文字に戻す。
			// The site name is saved with & etc. turned into references such as &amp;, so turn them back into characters before they reach search engines.
			'name'     => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			'url'      => home_url(),
		);

		return $website_array;
	}
}

new VK_WebSite_Srtuctured_Data();
