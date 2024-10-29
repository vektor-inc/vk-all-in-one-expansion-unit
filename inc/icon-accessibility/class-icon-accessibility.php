<?php

class VEU_Icon_Accessibility {
    /**
     * Font Awesome アイコンに aria-hidden="true" を付与する
     *
     * @param string $content 変換前のコンテンツ文字列
     * @return string 変換後のコンテンツ文字列
     */
    public static function add_aria_hidden_to_fontawesome($content) {
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
}