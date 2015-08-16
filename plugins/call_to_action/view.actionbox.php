<?php

$url_title = get_post_meta( $id, 'vkExUnit_cta_button_text', true );
$url       = get_post_meta( $id, 'vkExUnit_cta_url', true );
$text      = get_post_meta( $id, 'vkExUnit_cta_text', true);
$text = preg_replace('/\n/', '<br/>', $text);
$imgid     = get_post_meta( $id, 'vkExUnit_cta_img', true);
$cta_image = wp_get_attachment_image_src($imgid,full);
$image_position = get_post_meta( $id, 'vkExUnit_cta_img_position', true);
if(!$image_position){ $image_position = 'right'; }

$content  = '';
$content .= '<section class="vkExUnit_cta">';
$content .= '<h1 class="vkExUnit_cta_title">' . $post->post_title . '</h1>';
$content .= '<div class="vkExUnit_cta_body">';
if( $imgid ){
    $content .= '<div class="vkExUnit_cta_body_image vkExUnit_cta_body_image_'.$image_position.'">';
    $content .= '<img src="'. $cta_image[0] .'" />';
    $content .= '</div>';
}
$content .= '<div class="vkExUnit_cta_body_txt '.(($imgid)? 'image_exist' : 'image_no').'">';
$content .= $text;
$content .= '</div>';
if( $url ){
    $content .= '<div class="vkExUnit_cta_body_link">';
    $content .= '<a href="'.$url.'" class="btn btn-primary btn-block btn-lg" target="_blank">';
    $content .= $url_title;
    $content .= '</a>';
    $content .= '</div>';
}
$content .= '</div><!-- [ /.vkExUnit_cta_body ] -->';
$content .= '</section>';