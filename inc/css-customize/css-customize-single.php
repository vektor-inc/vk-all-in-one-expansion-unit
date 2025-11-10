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
 * CSS Customize Single Load to Edit Page
 */
function veu_css_customize_single_load_edit() {
	global $post;
	veu_insert_custom_css();
}
add_action( 'admin_footer', 'veu_css_customize_single_load_edit', 11 );


/*
入力された CSS をソースに出力
/* ------------------------------------------------ */
function veu_insert_custom_css() {

	if ( is_singular() || ( is_admin() && isset( $_GET['post'] ) ) ) {
		global $post;
		if ( $post ) {
			$css = veu_get_the_custom_css_single( $post );
			if ( $css ) {
				$css = veu_sanitize_custom_css_input( $css );
				if ( $css ) {
					echo '<style type="text/css">/* ' . esc_html( veu_get_short_name() ) . ' CSS Customize Single */' . $css . '</style>';
				}
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
