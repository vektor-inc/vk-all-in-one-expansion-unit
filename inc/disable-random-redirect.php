<?php
/**
 * Disable the /random/ redirect added in WordPress 7.2.
 * WordPress 7.2 以降の /random/ 転送機能を無効化する。
 *
 * Included only when wp_is_random_content_redirect_enabled() exists.
 * wp_is_random_content_redirect_enabled() があるときだけ読み込まれる。
 *
 * @package vk-all-in-one-expansion-unit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the filter that disables the /random/ redirect.
 * /random/ への転送を止めるフィルターを登録する。
 *
 * @return void
 */
function veu_disable_random_content_redirect() {
	add_filter( 'wp_enable_random_content_redirect', '__return_false' );
}
veu_disable_random_content_redirect();
