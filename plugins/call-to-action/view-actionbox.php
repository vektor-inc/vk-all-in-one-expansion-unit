<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

global $vk_call_to_action_textdomain;

$btn_text   = get_post_meta( $id, 'vkExUnit_cta_button_text', true );
$btn_before = get_post_meta( $id, 'vkExUnit_cta_button_icon_before', true );
if ( $btn_before ) {
		$btn_before = '<i class="fa ' . esc_attr( $btn_before ) . ' font_icon"></i> ';
}
$btn_after = get_post_meta( $id, 'vkExUnit_cta_button_icon_after', true );
if ( $btn_after ) {
		$btn_after = ' <i class="fa ' . esc_attr( $btn_after ) . ' font_icon"></i>';
}
$url   = get_post_meta( $id, 'vkExUnit_cta_url', true );
$text  = get_post_meta( $id, 'vkExUnit_cta_text', true );
$text  = preg_replace( '/\n/', '<br/>', $text );
$imgid = get_post_meta( $id, 'vkExUnit_cta_img', true );


$image_position = get_post_meta( $id, 'vkExUnit_cta_img_position', true );
if ( ! $image_position ) {
	$image_position = 'right'; }

$content  = '';
$content .= '<section class="veu_cta">';
$content .= '<h1 class="cta_title">' . $post->post_title . '</h1>';
$content .= '<div class="cta_body">';


	////// 別ウィンドウで開くかどうかのカスタムフィールドの値を取得 //////
		$target_blank = get_post_meta( $id, 'vkExUnit_cta_url_blank', true );
		if ( $target_blank != 'window_self' ) {
			$target = ' target="_blank"';
		} else {
			$target = '';
		}
	////////////////////////////////////////////////////////////


if ( $imgid ) {
	$cta_image = wp_get_attachment_image_src( $imgid, 'large' );
	$content  .= '<div class="cta_body_image cta_body_image_' . $image_position . '">';
	$content  .= ( $url ) ? '<a href="' . $url.'"' . $target .'>' : '';
	$content  .= '<img src="' . $cta_image[0] . '" />';
	$content  .= ( $url ) ? '</a>' : '';
	$content  .= '</div>';
}
$content .= '<div class="cta_body_txt ' . ( ( $imgid ) ? 'image_exist' : 'image_no' ) . '">';
$content .= do_shortcode( $text );
$content .= '</div>';
if ( $url && $btn_text ) {
	$content .= '<div class="cta_body_link">';

	$content .= '<a href="' . $url . '" class="btn btn-primary btn-block btn-lg"' . $target . '>';
	$content .= $btn_before . $btn_text . $btn_after;
	$content .= '</a>';
	$content .= '</div>';
}
$content .= '</div><!-- [ /.vkExUnit_cta_body ] -->';
$content .= '</section>';

if ( $url = get_edit_post_link( $post->ID ) ) {
	$content .= '<div class="veu_adminEdit"><a href="' . $url . '" class="btn btn-default" target="_blank">' . __( 'Edit CTA', $vk_call_to_action_textdomain ) . '</a></div>';
}
