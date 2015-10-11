<?php

$btn_text  = get_post_meta( $id, 'vkExUnit_cta_button_text', true );
$url       = get_post_meta( $id, 'vkExUnit_cta_url', true );
$text      = get_post_meta( $id, 'vkExUnit_cta_text', true );
$text      = preg_replace( '/\n/', '<br/>', $text );
$imgid     = get_post_meta( $id, 'vkExUnit_cta_img', true );


$image_position = get_post_meta( $id, 'vkExUnit_cta_img_position', true );
if ( ! $image_position ) {  $image_position = 'right'; }

$content  = '';
$content .= '<section class="veu_cta">';
$content .= '<h1 class="cta_title">' . $post->post_title . '</h1>';
$content .= '<div class="cta_body">';
if ( $imgid ) {
	$cta_image = wp_get_attachment_image_src( $imgid, 'large' );
	$content .= '<div class="cta_body_image cta_body_image_'.$image_position.'">';
	$content .= ( $url )? '<a href="'.$url.'" target="_blank">':'';
	$content .= '<img src="'. $cta_image[0] .'" />';
	$content .= ( $url )? '</a>':'';
	$content .= '</div>';
}
$content .= '<div class="cta_body_txt '.(($imgid)? 'image_exist' : 'image_no').'">';
$content .= $text;
$content .= '</div>';
if ( $url && $btn_text ) {
	$content .= '<div class="cta_body_link">';
	$content .= '<a href="'.$url.'" class="btn btn-primary btn-block btn-lg" target="_blank">';
	$content .= $btn_text;
	$content .= '</a>';
	$content .= '</div>';
}
$content .= '</div><!-- [ /.vkExUnit_cta_body ] -->';
$content .= '</section>';

if ( $url = get_edit_post_link( $post->ID ) ) {
	$content .= '<div class="veu_adminEdit"><a href="'.$url.'" class="btn btn-default">'.__( 'Edit CTA','vkExUnit' ).'</a></div>';
}
