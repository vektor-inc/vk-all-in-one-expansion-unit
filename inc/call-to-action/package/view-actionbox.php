<?php
/**
 * View call to action ( classic style )
 *
 * @package ExUnit Call To Action
 */

global $vk_call_to_action_textdomain;

$fa = '';
if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
	$fa = Vk_Font_Awesome_Versions::print_fa();
}

$btn_text   = get_post_meta( $id, 'vkExUnit_cta_button_text', true );
$btn_before = get_post_meta( $id, 'vkExUnit_cta_button_icon_before', true );
if ( $btn_before ) {
		$btn_before = '<i class="' . $fa . esc_attr( $btn_before ) . ' font_icon"></i> ';
}
$btn_after = get_post_meta( $id, 'vkExUnit_cta_button_icon_after', true );
if ( $btn_after ) {
		$btn_after = ' <i class="' . $fa . esc_attr( $btn_after ) . ' font_icon"></i>';
}
$url   = get_post_meta( $id, 'vkExUnit_cta_url', true );
$text  = get_post_meta( $id, 'vkExUnit_cta_text', true );
$text  = preg_replace( '/\n/', '<br/>', $text );
$imgid = get_post_meta( $id, 'vkExUnit_cta_img', true );


$image_position = get_post_meta( $id, 'vkExUnit_cta_img_position', true );
if ( ! $image_position ) {
	$image_position = 'right'; }

$content  = '';
$content .= '<section class="veu_cta" id="veu_cta-' . $id . '">';
$content .= '<h1 class="cta_title">' . $cta_post->post_title . '</h1>';
$content .= '<div class="cta_body">';


// 別ウィンドウで開くかどうかのカスタムフィールドの値を取得 //////.
$target_blank = get_post_meta( $id, 'vkExUnit_cta_url_blank', true );
if ( 'window_self' !== $target_blank ) {
	$target = ' target="_blank"';
} else {
	$target = '';
}
if ( $imgid ) {
	$content .= '<div class="cta_body_image cta_body_image_' . $image_position . '">';
	$content .= ( $url ) ? '<a href="' . $url . '"' . $target . '>' : '';
	$content .= wp_get_attachment_image( $imgid, 'large' );
	$content .= ( $url ) ? '</a>' : '';
	$content .= '</div>';
}
$content .= '<div class="cta_body_txt ' . ( ( $imgid ) ? 'image_exist' : 'image_no' ) . '">';
$content .= wp_kses_post( do_shortcode( $text ) );
$content .= '</div>';
if ( $url && $btn_text ) {
	$content .= '<div class="cta_body_link">';
	$content .= '<a href="' . $url . '" class="btn btn-primary btn-block btn-lg"' . $target . '>';
	$content .= wp_kses_post( $btn_before . $btn_text . $btn_after );
	$content .= '</a>';
	$content .= '</div>';
}
$content .= '</div><!-- [ /.vkExUnit_cta_body ] -->';
$content .= '</section>';
