<?php
require_once 'customize.php';
/**
 * Defualt Thumbnail Change.
 *
 * @param string       $html output of html.
 * @param int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global `$post`.
 * @param string|array $size Optional. Image size to use. Accepts any valid image size, or
 *                           an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 */
 function veu_post_thumbnail_html( $html, $post = null, $size = 'post-thumbnail', $attr = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	$size = apply_filters( 'post_thumbnail_size', $size, $post->ID );

	$image_option     = get_option( 'veu_defualt_thumbnail' );
	$image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';

	if ( ! $post_thumbnail_id ) {
		if ( $image_default_id ) {
			do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $image_default_id, $size );
			if ( in_the_loop() ) {
				update_post_thumbnail_cache();
			}
			$html = wp_get_attachment_image( $image_default_id, $size, false, $attr );
			do_action( 'end_fetch_post_thumbnail_html', $post->ID, $image_default_id, $size );
		} else {
			$html = '';
		}
	}
	return $html;
}
add_filter( 'post_thumbnail_html', 'veu_post_thumbnail_html' );

/**
 * Change Has Post Thumbnail.
 *
 * @param bool        $has_thumbnail Post has thumbnail or not.
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 */
function veu_has_post_thumbnail( $has_thumbnail, $post = null ) {
	$thumbnail_id = get_post_thumbnail_id( $post );

	$image_option     = get_option( 'veu_defualt_thumbnail' );
	$image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';

	if ( $thumbnail_id ) {
		$has_thumbnail = true;
	} elseif ( $image_default_id ) {
		$has_thumbnail = true;
	} else {
		$has_thumbnail = false;
	}
	return $has_thumbnail;
}
add_filter( 'has_post_thumbnail', 'veu_has_post_thumbnail' );