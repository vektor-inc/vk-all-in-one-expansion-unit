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
			// Decode entities and remove HTML tags and their contents
			$css = html_entity_decode($css);
			$css = strip_tags($css);
			echo '<style type="text/css">/* '. esc_html( veu_get_short_name() ).' CSS Customize Single */' . $css . '</style>';
		}
	}
}

function veu_get_the_custom_css_single( $post ) {
	$css_customize = get_post_meta( $post->ID, '_veu_custom_css', true );
	if ( $css_customize ) {
		// Delete tab
		$css_customize = preg_replace( '/[\n\r\t]/', '', $css_customize );
		// Multi space convert to single space
		$css_customize = preg_replace( '/\s(?=\s)/', '', $css_customize );
		// Delete comment
		$css_customize = preg_replace( '/[\s\t]*\/\*\/?(\n|[^\/]|[^*]\/)*\*\//', '', $css_customize );
		// Remove HTML tags
		$css_customize = strip_tags($css_customize);
	}
	return $css_customize;
}
