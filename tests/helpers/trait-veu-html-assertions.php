<?php
/**
 * HTML assertion helpers that do not depend on the attribute order of tags.
 * タグの属性の並び順に依存しない HTML 検証用ヘルパー。
 *
 * WordPress 7.2 changed wp_kses() / wp_kses_post() so that the attributes of a tag are output in alphabetical order.
 * WordPress 7.2 で wp_kses() / wp_kses_post() が、タグの属性をアルファベット順で出力するようになった。
 * The helpers below compare the set of attributes ( names and exact values ) and the structure, but not the order of attributes.
 * ここのヘルパーは、属性の組み合わせ（名前と値の完全一致）と構造は比較するが、属性の並び順は比較しない。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Trait VEU_Test_Html_Assertions
 * 属性の並び順に依存しない HTML のアサーション。
 */
trait VEU_Test_Html_Assertions {

	/**
	 * Sort the attributes of every start tag that has two or more attributes by attribute name.
	 * 属性を持つ開始タグごとに、属性を属性名の昇順へ並べ替える。
	 *
	 * Values, tag names and text are kept as they are, so only the order of attributes is neutralized.
	 * 値・タグ名・テキストはそのままなので、属性の並び順の差だけが無効になる。
	 *
	 * @param string $html HTML.
	 * @return string Attribute order normalized HTML. / 属性の並び順を揃えた HTML.
	 */
	private function normalize_html_attribute_order( $html ) {
		$tag_pattern       = '/<([a-zA-Z][a-zA-Z0-9-]*)((?:\s+[^\s"\'<>\/=]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?)+)(\s*\/?)>/';
		$attribute_pattern = '/([^\s"\'<>\/=]+)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?/';

		return preg_replace_callback(
			$tag_pattern,
			function ( $tag_match ) use ( $attribute_pattern ) {
				preg_match_all( $attribute_pattern, $tag_match[2], $attribute_matches, PREG_SET_ORDER );

				$attributes = array();
				foreach ( $attribute_matches as $index => $attribute_match ) {
					$attributes[] = array(
						'name'  => strtolower( $attribute_match[1] ),
						'text'  => $attribute_match[0],
						'index' => $index,
					);
				}
				// 同名の属性があっても並びが安定するよう、元の位置を第2キーにする。
				// Use the original position as the second key so the order is stable even with duplicate names.
				usort(
					$attributes,
					function ( $a, $b ) {
						$by_name = strcmp( $a['name'], $b['name'] );
						return 0 !== $by_name ? $by_name : $a['index'] - $b['index'];
					}
				);

				return '<' . $tag_match[1] . ' ' . implode( ' ', array_column( $attributes, 'text' ) ) . $tag_match[3] . '>';
			},
			$html
		);
	}

	/**
	 * Assert that the HTML contains the given string, ignoring the order of the attributes in tags.
	 * HTML が指定の文字列を含む事を、タグ内の属性の並び順を無視して検証する。
	 *
	 * @param string $needle  Expected string. / 期待する文字列。閉じていないタグ（ `<img src="x"` など）は渡さない。
	 * @param string $html    Actual HTML. / 実際の HTML.
	 * @param string $message Failure message. / 失敗時のメッセージ.
	 * @return void
	 */
	protected function assert_html_contains( $needle, $html, $message = '' ) {
		$this->assertStringContainsString(
			$this->normalize_html_attribute_order( $needle ),
			$this->normalize_html_attribute_order( $html ),
			$message
		);
	}

	/**
	 * Assert that two HTML strings are identical, ignoring the order of the attributes in tags.
	 * 2つの HTML が、タグ内の属性の並び順を除いて一致する事を検証する。
	 *
	 * @param string $expected Expected HTML. / 期待する HTML.
	 * @param string $actual   Actual HTML. / 実際の HTML.
	 * @param string $message  Failure message. / 失敗時のメッセージ.
	 * @return void
	 */
	protected function assert_html_same( $expected, $actual, $message = '' ) {
		$this->assertSame(
			$this->normalize_html_attribute_order( $expected ),
			$this->normalize_html_attribute_order( $actual ),
			$message
		);
	}

	/**
	 * Assert that at least one tag with the given name has all the given attributes with exactly the given values.
	 * 指定したタグ名のタグのうち、少なくとも1つが、指定した属性をすべて完全一致の値で持つ事を検証する。
	 *
	 * @param string               $html          Actual HTML. / 実際の HTML.
	 * @param string               $tag_name      Tag name. / タグ名.
	 * @param array<string,string> $attributes    Attribute name => expected (decoded) value. / 属性名 => 期待する値（デコード後）.
	 * @param string               $message       Failure message. / 失敗時のメッセージ.
	 * @return void
	 */
	protected function assert_html_tag_has_attributes( $html, $tag_name, $attributes, $message = '' ) {
		$processor = new WP_HTML_Tag_Processor( $html );
		$found     = false;

		while ( ! $found && $processor->next_tag( $tag_name ) ) {
			$found = true;
			foreach ( $attributes as $name => $value ) {
				if ( $value !== $processor->get_attribute( $name ) ) {
					$found = false;
					break;
				}
			}
		}

		$this->assertTrue( $found, $message );
	}
}
