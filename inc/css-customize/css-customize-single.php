<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CSS Customize Single Load to Front Page
 */
function veu_css_customize_single_load() {
	$hook_point = apply_filters( 'veu_enqueue_point_css_customize_single', 'wp_head' );
	add_action( $hook_point, 'veu_insert_custom_css', 201 );
}
add_action( 'after_setup_theme', 'veu_css_customize_single_load', 11 );

if ( ! function_exists( 'veu_get_html_element_names_for_css' ) ) {
	/**
	 * HTML element names whose tags are removed from Custom CSS.
	 * カスタムCSSからタグを除去する HTML の要素名。
	 *
	 * The element names of the HTML Living Standard ( https://html.spec.whatwg.org/multipage/indices.html#elements-3 ),
	 * plus the obsolete elements that old HTML pasted into Custom CSS may contain
	 * ( https://html.spec.whatwg.org/multipage/obsolete.html#non-conforming-features , and "image",
	 * which the HTML parser turns into "img" ).
	 * None of them is a CSS media feature name ( width, height, aspect-ratio, resolution, color,
	 * hover, pointer, prefers-* ... ), so "( 400px<width )" is never taken for a tag.
	 * HTML Living Standard の要素名と、古い HTML の貼り付けに含まれうる廃止済みの要素名
	 * （「Non-conforming features」の節に載っている要素と、HTML の解析で img に置き換えられる image）。
	 * CSS のメディア特性名（width, height, aspect-ratio, resolution, color, hover, pointer, prefers-* など）
	 * とは重ならないため、「( 400px<width )」をタグとみなさない。
	 *
	 * @return string[] Lowercase element names. / 小文字の要素名.
	 */
	function veu_get_html_element_names_for_css() {
		return array(
			'a',
			'abbr',
			'address',
			'area',
			'article',
			'aside',
			'audio',
			'b',
			'base',
			'bdi',
			'bdo',
			'blockquote',
			'body',
			'br',
			'button',
			'canvas',
			'caption',
			'cite',
			'code',
			'col',
			'colgroup',
			'data',
			'datalist',
			'dd',
			'del',
			'details',
			'dfn',
			'dialog',
			'div',
			'dl',
			'dt',
			'em',
			'embed',
			'fieldset',
			'figcaption',
			'figure',
			'footer',
			'form',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'head',
			'header',
			'hgroup',
			'hr',
			'html',
			'i',
			'iframe',
			'img',
			'input',
			'ins',
			'kbd',
			'label',
			'legend',
			'li',
			'link',
			'main',
			'map',
			'mark',
			'math',
			'menu',
			'meta',
			'meter',
			'nav',
			'noscript',
			'object',
			'ol',
			'optgroup',
			'option',
			'output',
			'p',
			'picture',
			'pre',
			'progress',
			'q',
			'rp',
			'rt',
			'ruby',
			's',
			'samp',
			'script',
			'search',
			'section',
			'select',
			'selectedcontent',
			'slot',
			'small',
			'source',
			'span',
			'strong',
			'style',
			'sub',
			'summary',
			'sup',
			'svg',
			'table',
			'tbody',
			'td',
			'template',
			'textarea',
			'tfoot',
			'th',
			'thead',
			'time',
			'title',
			'tr',
			'track',
			'u',
			'ul',
			'var',
			'video',
			'wbr',
			// Obsolete elements / 廃止済みの要素.
			'acronym',
			'applet',
			'basefont',
			'bgsound',
			'big',
			'blink',
			'center',
			'dir',
			'font',
			'frame',
			'frameset',
			'image',
			'isindex',
			'keygen',
			'listing',
			'marquee',
			'menuitem',
			'multicol',
			'nextid',
			'nobr',
			'noembed',
			'noframes',
			'param',
			'plaintext',
			'rb',
			'rtc',
			'spacer',
			'strike',
			'tt',
			'xmp',
		);
	}
}

if ( ! function_exists( 'veu_remove_html_comments_from_css' ) ) {
	/**
	 * Remove HTML comments ( <!-- ... --> ) from CSS together with their content.
	 * CSS から HTML コメント（<!-- ... -->）を中身ごと除去する。
	 *
	 * Left in place, the text in a comment joins the next selector and that rule stops
	 * working. Comments inside CSS strings are removed too: their text often contains
	 * quotes ( "it's" ), which would otherwise be read as the start of a CSS string and
	 * stop the tag removal after it. An unclosed "<!--" is removed to the end of its line,
	 * so the CSS on the following lines still works.
	 * Uses strpos() instead of a regular expression, so a long comment never makes the
	 * function fail ( PCRE JIT stack / backtrack limit ) and lose the CSS.
	 * 残すとコメント内の文字が次のセレクタとつながり、そのルールが効かなくなるため。
	 * CSS の文字列の中のコメントも除去する。中の文字には引用符（it's など）がよく入り、残すと CSS の文字列の開始とみなされて、
	 * 後ろのタグが除去されなくなるため。
	 * 閉じていない「<!--」はその行の末尾まで除去し、次の行以降の CSS は効かせる。
	 * 正規表現ではなく strpos() で探すため、長いコメントでも失敗（PCRE の JIT スタック・バックトラックの上限）して CSS が消えることはない。
	 *
	 * @param string $css CSS. / CSS.
	 * @return string CSS without HTML comments. / HTML コメントを除去した CSS.
	 */
	function veu_remove_html_comments_from_css( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		// Remove closed comments first. Once no "-->" is left, no later "<!--" is closed either.
		// 先に閉じているコメントを除去する。「-->」が見つからなくなれば、後ろの「<!--」も閉じていない。
		$result = '';
		$pos    = 0;
		while ( true ) {
			$start = strpos( $css, '<!--', $pos );
			if ( false === $start ) {
				break;
			}
			$end = strpos( $css, '-->', $start + 4 );
			if ( false === $end ) {
				break;
			}
			$result .= substr( $css, $pos, $start - $pos );
			$pos     = $end + 3;
		}
		$css = $result . substr( $css, $pos );

		// Remove each unclosed "<!--" to the end of its line.
		// 閉じていない「<!--」をその行の末尾まで除去する。
		$result = '';
		$pos    = 0;
		while ( true ) {
			$start = strpos( $css, '<!--', $pos );
			if ( false === $start ) {
				break;
			}
			$result .= substr( $css, $pos, $start - $pos );
			$pos     = $start + strcspn( $css, "\r\n", $start );
		}
		return $result . substr( $css, $pos );
	}
}

if ( ! function_exists( 'veu_remove_script_elements_from_css' ) ) {
	/**
	 * Remove <script> elements from CSS together with their content.
	 * CSS から <script> 要素を中身ごと除去する。
	 *
	 * A <script> element is "<script" ( case-insensitive ) not followed by a letter, digit
	 * or "_", up to the first ">", then the content up to the first "</script" followed by
	 * optional whitespace and ">". A <script> without its end tag is not removed here ( its
	 * start tag is removed by veu_remove_html_tags_from_css() ).
	 * Elements inside CSS strings are removed too: their content often contains quotes
	 * ( alert('x') ), which would otherwise be read as the start of a CSS string and stop
	 * the tag removal after it.
	 * Uses stripos() instead of a regular expression, so a long script never makes the
	 * function fail and lose the CSS.
	 * <script> 要素とは、「<script」（大文字小文字は区別しない）の直後が英数字・「_」でなく、最初の「>」まで続く開始タグと、
	 * その後ろで最初に現れる「</script」＋空白（省略可）＋「>」までの中身。終了タグの無い <script> はここでは除去しない
	 * （開始タグは veu_remove_html_tags_from_css() で除去する）。
	 * CSS の文字列の中の要素も除去する。中の文字には引用符（alert('x') など）がよく入り、残すと CSS の文字列の開始とみなされて、
	 * 後ろのタグが除去されなくなるため。
	 * 正規表現ではなく stripos() で探すため、長いスクリプトでも失敗して CSS が消えることはない。
	 *
	 * @param string $css CSS. / CSS.
	 * @return string CSS without <script> elements. / <script> 要素を除去した CSS.
	 */
	function veu_remove_script_elements_from_css( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$word_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
		$whitespace = " \t\n\x0B\f\r";
		$length     = strlen( $css );
		$result     = '';
		$pos        = 0;

		while ( true ) {
			$start = stripos( $css, '<script', $pos );
			if ( false === $start ) {
				break;
			}
			// "<scripting>" and the like are not a <script> start tag.
			// 「<scripting>」などは <script> の開始タグではない。
			$name_end = $start + 7;
			if ( 0 < strspn( $css, $word_chars, $name_end, 1 ) ) {
				$result .= substr( $css, $pos, $name_end - $pos );
				$pos     = $name_end;
				continue;
			}

			// Find the end of the start tag. Without it, no later <script> has one either.
			// 開始タグの終わりを探す。無ければ後ろの <script> にも無い。
			$open_end = strpos( $css, '>', $name_end );
			if ( false === $open_end ) {
				break;
			}

			// Find the first end tag ( "</script" + optional whitespace + ">" ) after the start tag.
			// 開始タグの後ろで最初の終了タグ（「</script」＋空白（省略可）＋「>」）を探す。
			$element_end = false;
			$search_from = $open_end + 1;
			while ( true ) {
				$close = stripos( $css, '</script', $search_from );
				if ( false === $close ) {
					break;
				}
				$close_end = $close + 8;
				$close_end = $close_end + strspn( $css, $whitespace, $close_end );
				if ( $close_end < $length && '>' === $css[ $close_end ] ) {
					$element_end = $close_end + 1;
					break;
				}
				$search_from = $close + 1;
			}
			// Without an end tag, no later <script> has one either.
			// 終了タグが無ければ、後ろの <script> にも無い。
			if ( false === $element_end ) {
				break;
			}

			$result .= substr( $css, $pos, $start - $pos );
			$pos     = $element_end;
		}

		return $result . substr( $css, $pos );
	}
}

if ( ! function_exists( 'veu_get_css_escape_end' ) ) {
	/**
	 * Get the position right after a CSS escape ( CSS Syntax Level 3, 4.3.7 ).
	 * CSS のエスケープの直後の位置を返す（CSS Syntax Level 3 の 4.3.7）。
	 *
	 * Up to 6 hex digits plus one following whitespace ( CRLF counts as one ), or any other
	 * single character. The caller handles a backslash followed by a newline or the end of
	 * the CSS. A non-ASCII character is read as its first byte only: the remaining bytes
	 * are not ASCII, so they are never taken for a delimiter.
	 * 最大 6 桁の 16 進数と、その直後の空白 1 つ（CRLF は 1 つとみなす）。それ以外は 1 文字。
	 * バックスラッシュの直後が改行や CSS の末尾のときの扱いは呼び出し側で行う。
	 * ASCII 以外の文字は先頭の 1 バイトだけ読む。残りのバイトは ASCII ではないため、区切りの記号とみなされることはない。
	 *
	 * @param string $css    CSS. / CSS.
	 * @param int    $pos    Position right after the backslash. / バックスラッシュの直後の位置.
	 * @param int    $length Length of $css in bytes. / $css のバイト数.
	 * @return int Position right after the escape. / エスケープの直後の位置.
	 */
	function veu_get_css_escape_end( $css, $pos, $length ) {
		$hex_length = strspn( $css, '0123456789abcdefABCDEF', $pos, 6 );
		if ( 0 === $hex_length ) {
			return $pos + 1;
		}
		$pos += $hex_length;
		if ( "\r\n" === substr( $css, $pos, 2 ) ) {
			return $pos + 2;
		}
		if ( $pos < $length && false !== strpos( " \t\n\r\f", $css[ $pos ] ) ) {
			return $pos + 1;
		}
		return $pos;
	}
}

if ( ! function_exists( 'veu_get_css_string_end' ) ) {
	/**
	 * Get the position right after a CSS string ( CSS Syntax Level 3, 4.3.5 ).
	 * CSS の文字列の直後の位置を返す（CSS Syntax Level 3 の 4.3.5）。
	 *
	 * The string ends at the matching quote ( included ), at an unescaped newline ( LF, CR
	 * or FF; not included, it is read again outside the string ), or at the end of the CSS.
	 * A backslash followed by a newline ( CRLF counts as one ) continues the string.
	 * 文字列は、対応する引用符（含める）、エスケープされていない改行（LF・CR・FF。含めず、文字列の外として読み直す）、
	 * CSS の末尾のいずれかで終わる。バックスラッシュの直後の改行（CRLF は 1 組）は続き行として読む。
	 *
	 * @param string $css    CSS. / CSS.
	 * @param int    $pos    Position right after the opening quote. / 開始の引用符の直後の位置.
	 * @param string $quote  The opening quote ( " or ' ). / 開始の引用符（" か '）.
	 * @param int    $length Length of $css in bytes. / $css のバイト数.
	 * @return int Position right after the string. / 文字列の直後の位置.
	 */
	function veu_get_css_string_end( $css, $pos, $quote, $length ) {
		while ( $pos < $length ) {
			// Skip to the next quote, backslash or newline.
			// 次の引用符・バックスラッシュ・改行まで進む。
			$pos += strcspn( $css, $quote . "\\\n\r\f", $pos );
			if ( $pos >= $length ) {
				return $length;
			}
			$char = $css[ $pos ];
			if ( $quote === $char ) {
				return $pos + 1;
			}
			if ( '\\' !== $char ) {
				// An unescaped newline ends the string.
				// エスケープされていない改行で文字列が終わる。
				return $pos;
			}

			// Backslash: a following newline continues the string; otherwise read an escape.
			// バックスラッシュ: 直後が改行なら続き行、それ以外はエスケープとして読む。
			$next = $pos + 1;
			if ( $next >= $length ) {
				return $length;
			}
			if ( "\r\n" === substr( $css, $next, 2 ) ) {
				$pos = $next + 2;
			} elseif ( false !== strpos( "\n\r\f", $css[ $next ] ) ) {
				$pos = $next + 1;
			} else {
				$pos = veu_get_css_escape_end( $css, $next, $length );
			}
		}
		return $length;
	}
}

if ( ! function_exists( 'veu_get_html_tag_end_in_css' ) ) {
	/**
	 * Get the position right after an HTML tag that starts at a "<" in CSS.
	 * CSS の「<」から始まる HTML のタグの直後の位置を返す。
	 *
	 * A tag is "<" or "</", an element name from veu_get_html_element_names_for_css()
	 * ( case-insensitive ), then whitespace, "/" or ">", closed by a ">" that comes before
	 * any "<", "{" or "}". Element names are letters and digits only, so the name is the
	 * longest run of letters and digits.
	 * タグとは、「<」か「</」の直後に veu_get_html_element_names_for_css() の要素名（大文字小文字は区別しない）、
	 * その直後が空白・「/」・「>」で、「<」「{」「}」より前の「>」で閉じているもの。
	 * 要素名は英数字だけなので、英数字が続く範囲を要素名として読む。
	 *
	 * @param string $css    CSS. / CSS.
	 * @param int    $pos    Position of the "<". / 「<」の位置.
	 * @param int    $length Length of $css in bytes. / $css のバイト数.
	 * @return int|false Position right after the tag, or false when it is not a tag. / タグの直後の位置。タグでなければ false.
	 */
	function veu_get_html_tag_end_in_css( $css, $pos, $length ) {
		static $element_names = null;
		if ( null === $element_names ) {
			$element_names = array_flip( veu_get_html_element_names_for_css() );
		}

		// Read the element name.
		// 要素名を読む。
		$name_start = $pos + 1;
		if ( $name_start < $length && '/' === $css[ $name_start ] ) {
			++$name_start;
		}
		$name_length = strspn( $css, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $name_start );
		if ( 0 === $name_length ) {
			return false;
		}
		$name_end = $name_start + $name_length;
		if ( $name_end >= $length || false === strpos( " \t\n\x0B\f\r/>", $css[ $name_end ] ) ) {
			return false;
		}
		if ( ! isset( $element_names[ strtolower( substr( $css, $name_start, $name_length ) ) ] ) ) {
			return false;
		}

		// The tag must close with ">" before any "<", "{" or "}".
		// 「<」「{」「}」より前の「>」で閉じていること。
		$close = $name_end + strcspn( $css, '<>{}', $name_end );
		if ( $close >= $length || '>' !== $css[ $close ] ) {
			return false;
		}
		return $close + 1;
	}
}

if ( ! function_exists( 'veu_remove_html_tags_from_css' ) ) {
	/**
	 * Remove tags with an HTML element name from CSS, outside CSS strings and comments.
	 * CSS の文字列とコメントの外にある、HTML の要素名のタグを除去する。
	 *
	 * Reads the CSS once from the start, following the CSS Syntax Level 3 tokenizer
	 * ( https://www.w3.org/TR/css-syntax-3/#tokenization ) only as far as needed to tell
	 * strings ( "..." / '...' ), comments ( /* ... *\/ ) and escapes ( \ ) apart, and checks
	 * for a tag only at a "<" outside them. Tags inside strings and comments are kept, so
	 * SVG data URIs and content: "<br>" keep working, and an escaped quote ( .foo\" ) does
	 * not start a string. No regular expression is used, so the time grows linearly with
	 * the length of the CSS and a long string never makes the function fail and lose the CSS.
	 * The CSS is read byte by byte: every delimiter is ASCII, and the bytes of a multibyte
	 * UTF-8 character are never ASCII, so multibyte characters are kept intact.
	 * Other "<" are left as they are, so CSS such as "( 400px<width ) and ( width>800px )"
	 * survives. Tags must not be left in the CSS: the block editor parses the per-post CSS
	 * ( PostCSS ) and drops all of it on a syntax error.
	 * CSS を先頭から 1 回だけ読み、CSS Syntax Level 3 の tokenizer に沿って文字列（"..." / '...'）・
	 * コメント（/* ... *\/）・エスケープ（\）を見分け、その外の「<」でだけタグかどうかを判定する。
	 * 文字列とコメントの中のタグは残すため、SVG のデータ URI や content: "<br>" は壊れず、
	 * エスケープした引用符（.foo\"）を文字列の開始とみなすこともない。
	 * 正規表現を使わないため、処理時間は CSS の長さに比例し、長い文字列で失敗して CSS が消えることはない。
	 * バイト単位で読む。区切りの記号はすべて ASCII で、UTF-8 の多バイト文字のバイトは ASCII にならないため、多バイト文字は壊れない。
	 * タグでない「<」は残すため、「( 400px<width ) and ( width>800px )」のような CSS は消えない。
	 * タグを残してはいけない。ブロックエディタは投稿ごとの CSS を解析（PostCSS）し、構文エラーがあると全体を捨てる。
	 *
	 * @param string $css CSS. / CSS.
	 * @return string CSS without the tags. / タグを除去した CSS.
	 */
	function veu_remove_html_tags_from_css( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$length = strlen( $css );
		$result = '';
		// Start of the part not yet copied to $result. / まだ $result に写していない部分の先頭.
		$copy_from = 0;
		$pos       = 0;

		while ( $pos < $length ) {
			// Skip to the next character that can start a string, escape, comment or tag.
			// 文字列・エスケープ・コメント・タグの開始になりうる次の文字まで進む。
			$pos += strcspn( $css, "\"'\\/<", $pos );
			if ( $pos >= $length ) {
				break;
			}
			$char = $css[ $pos ];

			if ( '"' === $char || "'" === $char ) {
				// CSS string. / CSS の文字列.
				$pos = veu_get_css_string_end( $css, $pos + 1, $char, $length );
			} elseif ( '\\' === $char ) {
				// Outside strings, a backslash escapes the next character unless it is a newline.
				// 文字列の外では、バックスラッシュは次の文字（改行を除く）をエスケープする。
				$next = $pos + 1;
				if ( $next < $length && false === strpos( "\n\r\f", $css[ $next ] ) ) {
					$pos = veu_get_css_escape_end( $css, $next, $length );
				} else {
					$pos = $next;
				}
			} elseif ( '/' === $char ) {
				// CSS comment. An unclosed comment runs to the end of the CSS.
				// CSS コメント。閉じていないコメントは CSS の末尾まで。
				if ( $pos + 1 < $length && '*' === $css[ $pos + 1 ] ) {
					$comment_end = strpos( $css, '*/', $pos + 2 );
					$pos         = false === $comment_end ? $length : $comment_end + 2;
				} else {
					++$pos;
				}
			} else {
				// "<": remove it with the rest of the tag when it starts a tag.
				// 「<」: タグの開始ならタグごと除去する。
				$tag_end = veu_get_html_tag_end_in_css( $css, $pos, $length );
				if ( false === $tag_end ) {
					++$pos;
				} else {
					$result   .= substr( $css, $copy_from, $pos - $copy_from );
					$pos       = $tag_end;
					$copy_from = $tag_end;
				}
			}
		}

		return $result . substr( $css, $copy_from );
	}
}

if ( ! function_exists( 'veu_strip_html_from_css' ) ) {
	/**
	 * Remove the HTML that is commonly pasted into Custom CSS, keeping CSS "<" and ">" intact.
	 * カスタムCSSによく貼り付けられる HTML を、CSS の「<」「>」を残したまま除去する。
	 *
	 * Removes the following, in this order.
	 * - HTML comments with their content ( veu_remove_html_comments_from_css() ).
	 * - <script> elements with their content ( veu_remove_script_elements_from_css() ).
	 * - Tags with an HTML element name outside CSS strings and comments ( veu_remove_html_tags_from_css() ).
	 * 次の順に除去する。
	 * - HTML コメントを中身ごと（veu_remove_html_comments_from_css()）。
	 * - <script> 要素を中身ごと（veu_remove_script_elements_from_css()）。
	 * - CSS の文字列とコメントの外にある HTML の要素名のタグ（veu_remove_html_tags_from_css()）。
	 *
	 * This does not make the CSS safe to put inside a <style> element.
	 * Use veu_escape_css_for_style_element() for that.
	 * <style> 要素に入れて安全にする処理ではない。安全化は veu_escape_css_for_style_element() で行う。
	 *
	 * @param string $css CSS that may contain HTML. / HTML を含みうる CSS.
	 * @return string CSS without the HTML above. / 上記の HTML を除去した CSS.
	 */
	function veu_strip_html_from_css( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$css = veu_remove_html_comments_from_css( $css );
		$css = veu_remove_script_elements_from_css( $css );
		return veu_remove_html_tags_from_css( $css );
	}
}

if ( ! function_exists( 'veu_minify_css' ) ) {
	/**
	 * Minify CSS for output: remove line breaks and tabs, collapse whitespace, remove the
	 * whitespace around "{", "}", ":" and ";", and remove CSS comments.
	 * 出力用に CSS を圧縮する。改行とタブを除去し、連続する空白を 1 つにまとめ、「{」「}」「:」「;」の前後の空白と CSS コメントを除去する。
	 *
	 * A failed step ( e.g. the PCRE backtrack limit ) is skipped instead of returning null,
	 * so a long CSS is never lost. Comments are removed with strpos(): a regular expression
	 * failed on a CSS comment of about 2 MB.
	 * 失敗した処理（PCRE のバックトラックの上限など）は null を返さずに飛ばすため、長い CSS が消えることはない。
	 * コメントは strpos() で除去する。正規表現では 2 MB ほどの CSS コメントで失敗したため。
	 *
	 * This does not make the CSS safe to put inside a <style> element. Pass the result to
	 * veu_escape_css_for_style_element(): removing comments can join "<" and "/" into a new "</".
	 * <style> 要素に入れて安全にする処理ではない。コメントの除去で「<」と「/」がつながり新しい「</」ができうるため、
	 * 結果は veu_escape_css_for_style_element() に通すこと。
	 *
	 * @param string $css CSS. / CSS.
	 * @return string Minified CSS. / 圧縮した CSS.
	 */
	function veu_minify_css( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		// Remove line breaks and tabs, then collapse whitespace.
		// 改行とタブを除去し、連続する空白を 1 つにまとめる。
		$css      = str_replace( PHP_EOL, '', $css );
		$patterns = array(
			'/[\n\r\t]/'       => '',
			'/\s+/'            => ' ',
			'/\s*([{}:;])\s*/' => '$1',
		);
		foreach ( $patterns as $pattern => $replacement ) {
			$replaced = preg_replace( $pattern, $replacement, $css );
			if ( null !== $replaced ) {
				$css = $replaced;
			}
		}

		// Remove CSS comments. An unclosed comment is left as it is.
		// CSS コメントを除去する。閉じていないコメントはそのまま残す。
		$result = '';
		$pos    = 0;
		while ( true ) {
			$start = strpos( $css, '/*', $pos );
			if ( false === $start ) {
				break;
			}
			$end = strpos( $css, '*/', $start + 2 );
			if ( false === $end ) {
				break;
			}
			$result .= substr( $css, $pos, $start - $pos );
			$pos     = $end + 2;
		}
		return $result . substr( $css, $pos );
	}
}

if ( ! function_exists( 'veu_escape_css_for_style_element' ) ) {
	/**
	 * Make CSS safe to output inside a <style> element.
	 * CSS を <style> 要素の中に出力しても安全な形にする。
	 *
	 * The only way to leave a <style> element is its end tag "</style", so every "</"
	 * is turned into "<\/". "</" never appears in valid CSS outside strings and
	 * comments, and inside a CSS string "<\/" means the same as "</".
	 * <style> 要素を抜け出す手段は終了タグ「</style」だけなので、「</」をすべて「<\/」に置き換える。
	 * 「</」は文字列とコメント以外の正しい CSS には現れず、CSS の文字列内では「<\/」は「</」と同じ意味になる。
	 *
	 * This must be the last step before output: removing comments or tags can join
	 * "<" and "/" into a new "</" ( e.g. "</**\/style" ).
	 * コメントやタグの除去で「<」と「/」がつながり新しい「</」ができるため、出力直前の最後に通すこと。
	 *
	 * @param string $css CSS to output. / 出力する CSS.
	 * @return string CSS without "</". / 「</」を含まない CSS.
	 */
	function veu_escape_css_for_style_element( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}
		return str_replace( '</', '<\\/', $css );
	}
}

if ( ! function_exists( 'veu_sanitize_custom_css_input' ) ) {
	/**
	 * Basic sanitization for the Custom CSS meta field.
	 * Removes pasted HTML while keeping CSS-specific characters ( including "<" and ">" ) intact,
	 * and neutralizes "</" so the CSS cannot close the <style> element.
	 * 投稿ごとのカスタムCSSの基本的なサニタイズ。
	 * CSS で使う文字（「<」「>」を含む）を残したまま貼り付けられた HTML を除去し、
	 * <style> 要素を閉じられないよう「</」を無効化する。
	 *
	 * @param string $css Raw CSS provided by editors. / 編集者が入力した CSS.
	 * @return string Sanitized CSS string. / サニタイズ済みの CSS.
	 */
	function veu_sanitize_custom_css_input( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$css = wp_check_invalid_utf8( $css );
		$css = html_entity_decode( $css, ENT_QUOTES | ENT_HTML5 );
		// wp_strip_all_tags() treats "<" followed by a non-space as a tag start and drops
		// CSS such as "<= 992px) { ... >" ( issue #1556 ), so use the CSS-aware stripper.
		// wp_strip_all_tags() は「<」の直後が空白以外だとタグの開始とみなし、
		// 「<= 992px) { ... >」のような CSS を消してしまうため（issue #1556）、CSS 用の除去処理を使う。
		$css = veu_strip_html_from_css( $css );
		$css = veu_escape_css_for_style_element( $css );
		$css = trim( $css );

		return $css;
	}
}

/**
 * Add the per-post Custom CSS to the block editor content area.
 * 投稿ごとのカスタムCSSをブロックエディタの本文領域に適用する。
 *
 * The CSS is passed through the editor `styles` setting, so the block editor
 * injects it inside the editor iframe scoped to `.editor-styles-wrapper`. This
 * limits the preview to the post content and prevents the CSS from leaking onto
 * the surrounding admin screen (e.g. sidebar / meta box headings), which was the
 * cause of the reported admin-screen layout breakage.
 * エディタの `styles` 設定として渡すことで、ブロックエディタが iframe 内
 * （`.editor-styles-wrapper` 配下）にスコープして注入する。これによりプレビューは
 * 投稿本文に限定され、管理画面の枠（サイドバーやメタボックス見出し等）に CSS が
 * 漏れて表示が崩れる不具合を防ぐ。
 *
 * @param array                   $editor_settings The block editor settings.
 * @param WP_Block_Editor_Context $editor_context  The current editor context.
 * @return array The filtered block editor settings.
 */
function veu_css_customize_single_editor_styles( $editor_settings, $editor_context ) {

	// Only the post editor provides a post; skip the site / widget editors.
	// 投稿エディタのときだけ post が入る。サイト/ウィジェットエディタでは処理しない。
	if ( empty( $editor_context->post ) ) {
		return $editor_settings;
	}

	// Get and sanitize the per-post Custom CSS.
	// 投稿ごとのカスタムCSSを取得してサニタイズする。
	$css = veu_get_sanitized_custom_css_single( $editor_context->post );
	if ( ! $css ) {
		return $editor_settings;
	}

	// Register the CSS as an editor style so it is rendered inside the editor iframe.
	// エディタスタイルとして登録し、エディタの iframe 内に描画させる。
	if ( ! isset( $editor_settings['styles'] ) || ! is_array( $editor_settings['styles'] ) ) {
		$editor_settings['styles'] = array();
	}
	$editor_settings['styles'][] = array( 'css' => $css );

	return $editor_settings;
}
add_filter( 'block_editor_settings_all', 'veu_css_customize_single_editor_styles', 10, 2 );


/**
 * Output the per-post Custom CSS on the front-end singular page.
 * 個別投稿ページ（フロント）に、投稿ごとのカスタムCSSを出力する。
 *
 * @return void
 */
function veu_insert_custom_css() {

	// Output only on the front-end singular page; the admin/editor preview is
	// handled by veu_css_customize_single_editor_styles() instead.
	// フロントの個別投稿ページでのみ出力する。管理画面/エディタのプレビューは
	// veu_css_customize_single_editor_styles() 側で扱う。
	if ( is_singular() ) {
		global $post;
		if ( $post ) {
			$css = veu_get_sanitized_custom_css_single( $post );
			if ( $css ) {
				echo '<style type="text/css">/* ' . esc_html( veu_get_short_name() ) . ' CSS Customize Single */' . $css . '</style>';
			}
		}
	}
}

function veu_get_the_custom_css_single( $post ) {
	$css_customize = get_post_meta( $post->ID, '_veu_custom_css', true );
	if ( $css_customize ) {
		$css_customize = veu_sanitize_custom_css_input( $css_customize );
		// Remove line breaks, extra whitespace and comments.
		// 改行・余分な空白・コメントを除去する。
		$css_customize = veu_minify_css( $css_customize );
		// Comment removal can create a new "</", so neutralize it again as the last step.
		// コメント除去で新しい「</」ができうるため、最後にもう一度無効化する。
		$css_customize = veu_escape_css_for_style_element( $css_customize );
		// Delete leading and trailing spaces
		$css_customize = trim( $css_customize );
	}
	return $css_customize;
}

/**
 * Get the sanitized per-post Custom CSS for a post.
 * 投稿ごとのカスタムCSSを取得・サニタイズして返す。
 *
 * Wraps veu_get_the_custom_css_single() and runs the shared sanitizer so that
 * the front-end output and the editor-style injection share a single code path.
 * veu_get_the_custom_css_single() をラップし、共通サニタイザを通すことで、
 * フロント出力とエディタスタイル注入で処理を1本化する。
 *
 * @param WP_Post $post The post object.
 * @return string The sanitized CSS, or an empty string when there is none.
 */
function veu_get_sanitized_custom_css_single( $post ) {
	$css = veu_get_the_custom_css_single( $post );
	if ( $css ) {
		$css = veu_sanitize_custom_css_input( $css );
	}
	return $css;
}
