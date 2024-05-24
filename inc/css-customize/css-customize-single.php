<?php
/*
  Custom CSS
/* ------------------------------------------- */

function veu_css_customize_single_load(){
	$hook_point = apply_filters( 'veu_enqueue_point_css_customize_single', 'wp_head' );
	add_action( $hook_point, 'veu_insert_custom_css', 201 );
}

add_action( 'after_setup_theme', 'veu_css_customize_single_load' );

/*
 入力された CSS をソースに出力
/* ------------------------------------------------ */
function veu_insert_custom_css() {

	if ( is_singular() ) {
		global $post;
		$css = veu_get_the_custom_css_single( $post );
		if ( $css ) {
			// HTMLエンティティをデコードし、HTMLタグとその内容を削除
			$css = html_entity_decode($css, ENT_QUOTES | ENT_HTML5);
			echo '<style type="text/css">/* '. esc_html( veu_get_short_name() ).' CSS Customize Single */' . $css . '</style>';
		}
	}
}

function veu_get_the_custom_css_single( $post ) {
	$css_customize = get_post_meta( $post->ID, '_veu_custom_css', true );
	if ( $css_customize ) {
		// タブの削除
		$css_customize = preg_replace( '/[\n\r\t]/', '', $css_customize );
		// 複数スペースを単一スペースに変換
		$css_customize = preg_replace( '/\s+/', ' ', $css_customize );
		// 適切なスペースを確保し、余分なスペースを削除
		$css_customize = preg_replace( '/\s*([{}:;])\s*/', '$1', $css_customize );
		// コメントの削除
		$css_customize = preg_replace( '/\/\*.*?\*\//', '', $css_customize );
		// HTMLタグの削除（styleやmediaタグは保持）
		$css_customize = preg_replace('/<(?!\/?style|\/?media\b)[^>]+>/', '', $css_customize);
		// 改行の削除
		$css_customize = str_replace( PHP_EOL, '', $css_customize );
		// 先頭と末尾のスペースを削除
		$css_customize = trim($css_customize);
	}
	return $css_customize;
}
