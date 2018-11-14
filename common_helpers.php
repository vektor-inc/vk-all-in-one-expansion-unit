<?php
/*-------------------------------------------*/
/*  basic setting
/*-------------------------------------------*/
/*  Chack use post top page
/*-------------------------------------------*/
/*  Chack post type info
/*-------------------------------------------*/
/*  Page description
/*-------------------------------------------*/
/*  Archive title
/*-------------------------------------------*/
/*  Sanitize
/*-------------------------------------------*/

/*-------------------------------------------*/
/*  basic setting
/*-------------------------------------------*/

require_once( vkExUnit_get_directory() . '/plugins/template-tags/template-tags.php' );
require_once( vkExUnit_get_directory() . '/plugins/template-tags/template-tags-veu.php' );
require_once( vkExUnit_get_directory() . '/plugins/template-tags/template-tags-veu-old.php' );

/*
ExUnit独自の関数
template-tags-veu.php に書かれているのもExUnit固有の関数だが、
ExUnitの機能を複製しているために独立化したプラグインにも使用される関数
 */
function veu_get_capability_required() {
	return add_filter( 'veu_get_capability_required', 'edit_theme_options' );
}

function veu_get_systemlogo_html() {
	$logo  = '<div class="logo_exUnit">';
	$logo .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', vkExUnit_get_directory_uri( '/images/head_logo_ExUnit.png' ) ) . '" alt="VK ExUnit" />';
	$logo .= '</div>';
	$logo  = apply_filters( 'veu_get_systemlogo_html', $logo );
	return $logo;
}
/*
7.0 になったら削除
 */
function vkExUnit_get_capability_required() {
	return veu_get_capability_required();
}
function vkExUnit_get_systemlogo() {
	return veu_get_systemlogo_html();
}
