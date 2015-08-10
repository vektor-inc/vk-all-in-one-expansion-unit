<?php
function vkExUnit_admin_banner(){
	if (strtoupper(get_locale()) == 'JA'){
		echo '<a href="http://lightning.bizvektor.com/ja/" target="_blank"><img src="'.vkExUnit_get_directory_uri('/images/lightning_bnr_ja.jpg').'" alt="lightning_bnr_ja" /></a>';
	} else {
		echo '<a href="http://lightning.bizvektor.com/" target="_blank"><img src="'.vkExUnit_get_directory_uri('/images/lightning_bnr_en.jpg').'" alt="lightning_bnr_en" /></a>';
	}
}