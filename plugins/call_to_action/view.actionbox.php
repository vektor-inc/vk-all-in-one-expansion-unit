<?php

$url_title = get_post_meta($id, 'vkExUnit_cta_url_title', true);
$url = get_post_meta($id, 'vkExUnit_cta_url', true);

$content = '';
$content .= '<div class="vkExUnit-cta_area">';
$content .= '<div class="_content">';
$content .= $post->post_content;
$content .= '</div>';
$content .= '<div class="_link">';
if($url && $url != 'http://'){
    $content .= '<a href="'.$url.'">';
    $content .= $url_title;
    $content .= '</div>';
}
$content .= '</div>';
