<?php
/**
 * HTML assertion helpers that do not depend on the attribute order of tags.
 * タグの属性の並び順に依存しない HTML 検証用ヘルパー。
 *
 * WordPress 7.2 changed wp_kses() / wp_kses_post() so that the attributes of a tag are output in alphabetical order.
 * WordPress 7.2 で wp_kses() / wp_kses_post() が、タグの属性をアルファベット順で出力するようになった。
 * WordPress 7.2 also drops the self-closing slash of void elements ( img, br and so on ): `<img src="#" />` becomes `<img src="#">`.
 * WordPress 7.2 では、void 要素（img・br など）の自己終了の ` /` も落ちる（ `<img src="#" />` が `<img src="#">` になる）。
 * Script content removal ( the third difference ) is handled in each test by building the expected value from wp_kses_post().
 * なお、許可されない script の中身が消える差は、各テストで期待値を wp_kses_post() から作って吸収している。
 * The helpers below compare the set of attributes ( names and exact values ) and the structure, but neither the order of attributes
 * nor the presence of the ` /` of void elements. The `/>` of non-void elements is still distinguished.
 * ここのヘルパーは、属性の組み合わせ（名前と値の完全一致）と構造は比較するが、属性の並び順と、void 要素の ` /` の有無は比較しない（非 void 要素の `/>` は区別する）。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Trait VEU_Test_Html_Assertions
 * 属性の並び順に依存しない HTML のアサーション。
 */
trait VEU_Test_Html_Assertions {

	/**
	 * Sort the attributes of every start tag by attribute name, and drop the ` /` at the end of void element start tags.
	 * 開始タグごとに属性を属性名の昇順へ並べ替え、void 要素の開始タグ末尾の ` /` を取り除く。
	 *
	 * Values, tag names and text are kept as they are, so only the order of attributes and the ` /` of void elements are neutralized.
	 * The `/>` of non-void elements is kept.
	 * 値・タグ名・テキストはそのままなので、属性の並び順と void 要素の ` /` の差だけが無効になる（非 void 要素の `/>` は残す）。
	 *
	 * @param string $html HTML.
	 * @return string Attribute order normalized HTML. / 属性の並び順を揃えた HTML.
	 */
	private function normalize_html_attribute_order( $html ) {
		$void_elements     = array( 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr' );
		$tag_pattern       = '/<([a-zA-Z][a-zA-Z0-9-]*)((?:\s+[^\s"\'<>\/=]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?)+)(\s*\/?)>/';
		$attribute_pattern = '/([^\s"\'<>\/=]+)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?/';

		// 属性を持たない void 要素（ `<br />` など）。
		// Void elements without attributes ( such as `<br />` ).
		$html = preg_replace( '/<(' . implode( '|', $void_elements ) . ')\s*\/>/i', '<$1>', $html );

		return preg_replace_callback(
			$tag_pattern,
			function ( $tag_match ) use ( $attribute_pattern, $void_elements ) {
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

				$tail = in_array( strtolower( $tag_match[1] ), $void_elements, true ) ? '' : $tag_match[3];

				return '<' . $tag_match[1] . ' ' . implode( ' ', array_column( $attributes, 'text' ) ) . $tail . '>';
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
