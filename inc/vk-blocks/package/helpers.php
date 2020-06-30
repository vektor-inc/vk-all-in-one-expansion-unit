<?php
function vkblocks_is_lightning() {

	// テーマがLightning系の場合読み込まない
	$theme_textdomain = wp_get_theme()->get( 'TextDomain' );
	if ( $theme_textdomain == 'lightning' || $theme_textdomain == 'lightning-pro' || $theme_textdomain == 'katawara'  ) {
		return true;
	}

	$theme_template = wp_get_theme()->get( 'Template' );
	if ( $theme_template == 'lightning' || $theme_template == 'lightning-pro' || $theme_textdomain == 'katawara' ) {
		return true;
	}

	return false;

}

/**
 * カスタマイザー用のチェックボックス
 *
 * @param $checked
 *
 * @return bool
 */
function vkblocks_sanitize_checkbox( $checked ) {
	if ( isset( $checked ) && $checked ) {
		return true;
	} else {
		return false;
	}
}
