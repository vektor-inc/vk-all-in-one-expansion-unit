<?php

/**
 * 
 *
 * @param [type] $content
 * @return void
 */
function add_aria_hidden_to_fontawesome($content) {
    $pattern = '/<i ([^>]*class=["\'][^"\']*fa[^"\']*["\'][^>]*)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $tag_content = $matches[1];
        
        if (strpos($tag_content, 'aria-hidden') !== false) {
            return '<i ' . $tag_content . '>';
        } else {
            return '<i ' . $tag_content . ' aria-hidden="true">';
        }
    }, $content);
    
    return $content;
}
add_filter('the_content', 'add_aria_hidden_to_fontawesome');