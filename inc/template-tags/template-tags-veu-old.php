<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

function vkExUnit_get_directory( $path = '' ) {
	return veu_get_directory( $path );
}
function vkExUnit_get_directory_uri( $path = '' ) {
	return veu_get_directory_uri( $path );
}

if ( ! function_exists( 'vkExUnit_get_common_options' ) ) {
	function vkExUnit_get_common_options() {
		return veu_get_common_options();
	}
}
if ( ! function_exists( 'vkExUnit_is_excerpt' ) ) {
	function vkExUnit_is_excerpt() {
		return vk_is_excerpt();
	}
}

if ( ! function_exists( 'vkExUnit_get_name' ) ) {
	function vkExUnit_get_name() {
		return veu_get_name();
	}
}

if ( ! function_exists( 'vkExUnit_get_little_short_name' ) ) {
	function vkExUnit_get_little_short_name() {
		return veu_get_little_short_name();
	}
}

if ( ! function_exists( 'vkExUnit_get_short_name' ) ) {
	function vkExUnit_get_short_name() {
		return veu_get_short_name();
	}
}

if ( ! function_exists( 'vkExUnit_get_page_for_posts' ) ) {
	function vkExUnit_get_page_for_posts() {
		return vk_get_page_for_posts();
	}
}

if ( ! function_exists( 'vkExUnit_get_post_type' ) ) {
	function vkExUnit_get_post_type() {
		return vk_get_post_type();
	}
}

if ( ! function_exists( 'vkExUnit_get_the_archive_title' ) ) {
	function vkExUnit_get_the_archive_title() {
		$title = vk_get_the_archive_title();
		return apply_filters( 'vkExUnit_get_the_archive_title', $title );
	}
}

if ( ! function_exists( 'vkExUnit_get_pageDescription' ) ) {
	function vkExUnit_get_pageDescription() {
		$title = vk_get_page_description();
		return apply_filters( 'vkExUnit_pageDescriptionCustom', $title );
	}
}
