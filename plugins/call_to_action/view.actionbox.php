<?php

$url_title = get_post_meta( $id, 'vkExUnit_cta_url_title', true );
$url       = get_post_meta( $id, 'vkExUnit_cta_url', true );

$content  = '';
$content .= '<section class="vkExUnit_cta">';
$content .= '<h1 class="vkExUnit_cta_title">' . $post->post_title . '</h1>';
$content .= '<div class="vkExUnit_cta_body">';
$content .= '<div class="vkExUnit_cta_content">';
$content .= $post->post_content;
$content .= '</div>';
if( $url ){
    $content .= '<div class="vkExUnit_cta_link">';
    $content .= '<a href="'.$url.'" class="btn btn-primary btn-block btn-lg">';
    $content .= $url_title;
    $content .= '</a>';
    $content .= '</div>';
}
$content .= '</div>';
$content .= '</section>';