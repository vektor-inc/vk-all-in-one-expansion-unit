<?php
/**
 * CSS Customize Single Load to Front Page
 */
function veu_css_customize_single_load() {
	$hook_point = apply_filters( 'veu_enqueue_point_css_customize_single', 'wp_head' );
	add_action( $hook_point, 'veu_insert_custom_css', 201 );
}
add_action( 'after_setup_theme', 'veu_css_customize_single_load', 11 );

if ( ! function_exists( 'veu_sanitize_custom_css_input' ) ) {
	/**
	 * Basic sanitization for the Custom CSS meta field.
	 * Removes HTML tags while keeping CSS-specific characters intact.
	 *
	 * @param string $css Raw CSS provided by editors.
	 * @return string Sanitized CSS string.
	 */
	function veu_sanitize_custom_css_input( $css ) {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$css = wp_check_invalid_utf8( $css );
		$css = html_entity_decode( $css, ENT_QUOTES | ENT_HTML5 );
		$css = wp_strip_all_tags( $css, false );
		$css = preg_replace( '/<\/?style[^>]*>/i', '', $css );
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
		// Delete HTML tags, but keep <style> and <media> tags
		$css_customize = preg_replace( '/<(?!\/?style|\/?media\b)[^>]+>/', '', $css_customize );
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
	return $css ? $css : '';
}
