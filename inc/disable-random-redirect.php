<?php
/**
 * WordPress 7.2 以降の /random/ 転送機能を無効化する。
 *
 * @package vk-all-in-one-expansion-unit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * /random/ への転送機能を無効にするフィルターを登録する。
 *
 * WordPress 7.2 未満にはこのフィルターが存在しないため、登録しても何も起きない。
 *
 * @return void
 */
function veu_disable_random_content_redirect() {
	add_filter( 'wp_enable_random_content_redirect', '__return_false' );
}
veu_disable_random_content_redirect();
