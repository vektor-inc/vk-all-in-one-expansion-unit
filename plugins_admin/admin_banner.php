<?php
function vkExUnit_admin_banner(){
	if (strtoupper(get_locale()) == 'JA'){
		$banner = '<a href="http://lightning.bizvektor.com/ja/" target="_blank"><img src="'.vkExUnit_get_directory_uri('/images/lightning_bnr_ja.jpg').'" alt="lightning_bnr_ja" /></a>';
	} else {
		$banner = '<a href="http://lightning.bizvektor.com/" target="_blank"><img src="'.vkExUnit_get_directory_uri('/images/lightning_bnr_en.jpg').'" alt="lightning_bnr_en" /></a>';
	}
    echo apply_filters( 'vkExUnit_news_admin_banner_html' , $banner );
}