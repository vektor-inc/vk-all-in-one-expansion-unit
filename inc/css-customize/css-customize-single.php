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

if ( ! function_exists( 'veu_strip_html_from_css' ) ) {
	/**
	 * Remove the HTML that is commonly pasted into Custom CSS, keeping CSS "<" and ">" intact.
	 * カスタムCSSによく貼り付けられる HTML を、CSS の「<」「>」を残したまま除去する。
	 *
	 * The following are removed.
	 * - HTML comments ( <!-- ... --> ) together with their content. An unclosed "<!--"
	 *   is removed to the end of its line.
	 * - <script> elements together with their content.
	 * - Tags with an HTML element name ( e.g. <br>, <p class="a">, </style> ), keeping the
	 *   text between them. A tag is "<" or "</", an element name from
	 *   veu_get_html_element_names_for_css() ( case-insensitive ), then whitespace, "/" or
	 *   ">", closed by a ">" that comes before any "<", "{" or "}". Tags inside CSS strings
	 *   ( "..." / '...' ) and CSS comments are kept, so SVG data URIs and content: "<br>"
	 *   keep working.
	 * 次を除去する。
	 * - HTML コメント（<!-- ... -->）は中身ごと。閉じていない「<!--」はその行の末尾まで。
	 * - <script> 要素は中身ごと。
	 * - HTML の要素名のタグ（<br>、<p class="a">、</style> など）。タグの間の文字は残す。
	 *   タグとみなすのは、「<」か「</」の直後に veu_get_html_element_names_for_css() の要素名
	 *   （大文字小文字は区別しない）、その直後が空白・「/」・「>」で、「<」「{」「}」より前の「>」で
	 *   閉じているもの。CSS の文字列（"..." / '...'）と CSS コメントの中のタグは残す。
	 *   SVG のデータ URI や content: "<br>" を壊さないため。
	 *
	 * HTML comments and <script> elements are removed everywhere, including inside CSS
	 * strings: their text often contains quotes ( "it's", alert('x') ), which would
	 * otherwise be read as the start of a CSS string and stop the tag removal after it.
	 * HTML コメントと <script> 要素は CSS の文字列の中も含めて除去する。中の文字には引用符
	 * （it's、alert('x') など）がよく入り、残すと CSS の文字列の開始とみなされて、後ろのタグが除去されなくなるため。
	 *
	 * Other "<" are left as they are, so CSS such as "( 400px<width ) and ( width>800px )"
	 * survives. Tags must not be left in the CSS: the block editor parses the per-post CSS
	 * ( PostCSS ) and drops all of it on a syntax error.
	 * それ以外の「<」は残すため、「( 400px<width ) and ( width>800px )」のような CSS は消えない。
	 * タグを残してはいけない。ブロックエディタは投稿ごとの CSS を解析（PostCSS）し、構文エラーがあると全体を捨てる。
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

		// Remove HTML comments with their content. Left in place, the text in a comment
		// joins the next selector and that rule stops working.
		// HTML コメントを中身ごと除去する。残すとコメント内の文字が次のセレクタとつながり、そのルールが効かなくなる。
		$css = preg_replace( '#<!--.*?-->#s', '', $css );
		// For an unclosed "<!--", remove it to the end of its line: the text on that line
		// is treated as the comment, and the CSS on the following lines still works.
		// 閉じていない「<!--」はその行の末尾まで除去する。同じ行の文字はコメントとみなし、次の行以降の CSS は効かせる。
		$css = preg_replace( '#<!--[^\r\n]*#', '', $css );

		// Remove <script> elements together with their content.
		// <script> 要素を中身ごと除去する。
		$css = preg_replace( '#<script\b[^>]*>.*?</script\s*>#is', '', $css );

		// Remove the remaining tags with an HTML element name, keeping the text between them.
		// CSS strings and CSS comments are matched by the same pattern and returned as they
		// are, so tags inside them ( e.g. an SVG data URI ) are kept, and a quote inside a
		// comment ( e.g. "it's" ) does not start a string. As in CSS, an unclosed string ends
		// at the end of its line, and an unclosed comment runs to the end of the CSS.
		// 残った HTML の要素名のタグを、間の文字は残して除去する。
		// CSS の文字列と CSS コメントも同じパターンで拾ってそのまま返すため、その中のタグ（SVG のデータ URI など）は残り、
		// コメント内の引用符（it's など）が文字列の開始とみなされることもない。
		// CSS と同じく、閉じていない文字列はその行の末尾まで、閉じていないコメントは CSS の末尾までとする。
		$element_names = implode( '|', veu_get_html_element_names_for_css() );
		$pattern       = '#'
			// "..." ( a backslash escapes the next character ). / 「"」の文字列（バックスラッシュは次の1文字をエスケープ）.
			. '"(?:[^"\\\\\r\n]|\\\\[\s\S])*"?'
			// '...' ( same as above ). / 「'」の文字列（同上）.
			. '|\'(?:[^\'\\\\\r\n]|\\\\[\s\S])*\'?'
			// CSS comment. / CSS コメント.
			. '|/\*[\s\S]*?(?:\*/|\z)'
			// Tag with an HTML element name. / HTML の要素名のタグ.
			. '|</?(?:' . $element_names . ')(?=[\s/>])[^<>{}]*>'
			. '#i';
		$css = preg_replace_callback(
			$pattern,
			function ( $matches ) {
				// Remove only tags; return CSS strings and comments as they are.
				// タグだけを除去し、CSS の文字列とコメントはそのまま返す.
				return '<' === $matches[0][0] ? '' : $matches[0];
			},
			$css
		);

		return $css;
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
		// Delete br
		$css_customize = str_replace( PHP_EOL, '', $css_customize );
		// Delete tab
		$css_customize = preg_replace( '/[\n\r\t]/', '', $css_customize );
		// Multi space convert to single space
		$css_customize = preg_replace( '/\s+/', ' ', $css_customize );
		// Ensure proper spacing and remove extra spaces
		$css_customize = preg_replace( '/\s*([{}:;])\s*/', '$1', $css_customize );
		// Delete Comment
		$css_customize = preg_replace( '/\/\*.*?\*\//', '', $css_customize );
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
