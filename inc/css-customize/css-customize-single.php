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
		// if 現在の WordPress クエリにループできる結果があるかどうか
		// while 記事がある間ループして１件ずつ処理する
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
					global $post;
					$css = veu_get_the_custom_css_single( $post );
					if ( $css ){
						echo '<style type="text/css">/* '. esc_html( veu_get_short_name() ).' CSS Customize Single */' . $css . '</style>';
					}
				endwhile;
		endif;
		// ページ上の別の場所で同じクエリを再利用するために、ループの投稿情報を巻き戻し、前回と同じ順序で先頭の投稿を取得できるように
		rewind_posts();
	}
} // function veu_insert_custom_css() {

function veu_get_the_custom_css_single( $post ) {
	$css_customize = get_post_meta( $post->ID, '_veu_custom_css', true );
	if ( $css_customize ) {
		// delete br
		$css_customize = str_replace( PHP_EOL, '', $css_customize );
		// delete tab
		$css_customize = preg_replace( '/[\n\r\t]/', '', $css_customize );
		// multi space convert to single space
		$css_customize = preg_replace( '/\s(?=\s)/', '', $css_customize );
		// Delete Comment
		$css_customize = preg_replace( '/[\s\t]*\/\*\/?(\n|[^\/]|[^*]\/)*\*\//', '', $css_customize );
	}
	return strip_tags( $css_customize );
}
