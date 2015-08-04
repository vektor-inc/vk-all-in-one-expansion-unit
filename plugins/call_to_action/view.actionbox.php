<?php

$url_title = get_post_meta( $id, 'vkExUnit_cta_url_title', true );
$url       = get_post_meta( $id, 'vkExUnit_cta_url', true );

$content  = '';
$content .= '<div class="vkExUnit-cta_area">';
$content .= '<span class="_title">' . $post->post_title . '</span>';
$content .= '<div class="_content">';
$content .= $post->post_content;
$content .= '</div>';
if( $url ){
    $content .= '<div class="_link">';
    $content .= '<a href="'.$url.'">';
    $content .= $url_title;
    $content .= '</a>';
    $content .= '</div>';
}
$content .= '</div>';