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

require_once( vkExUnit_get_directory().'/plugins/template-tags/template-tags.php' );
require_once( vkExUnit_get_directory().'/plugins/template-tags/template-tags-veu.php' );
require_once( vkExUnit_get_directory().'/plugins/template-tags/template-tags-veu-old.php' );

function vkExUnit_get_capability_required() {
	$capability_required = 'activate_plugins';
	return $capability_required;
}

function vkExUnit_get_systemlogo() {
	$logo = '<div class="logo_exUnit">';
	$logo .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', vkExUnit_get_directory_uri( '/images/head_logo_ExUnit.png' ) ) . '" alt="VK ExUnit" />';
	$logo .= '</div>';
	return $logo;
}
