<?php

require_once dirname( __FILE__ ) . '/customize.php';

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
 function veu_post_thumbnail_html( $html, $post = null, $post_thumbnail_id = null, $size = 'post-thumbnail', $attr = '' ) {
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
add_filter( 'post_thumbnail_html', 'veu_post_thumbnail_html', 10, 5 );

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

function veu_change_vk_components_image_default_url( $options ) {
    if ( veu_package_is_enable( 'default_thumbnail' ) ) {
        $image_option     = get_option( 'veu_defualt_thumbnail' );
        $image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';
        if ( $image_default_id ) {
            $image = wp_get_attachment_image_src( $image_default_id, 'large', true );
            $options['image_default_url'] = $image[0];
        }
    }
    return $options;
}
add_filter( 'vk_post_options', 'veu_change_vk_components_image_default_url' );

function veu_default_thumbnail_options_init() {
	vkExUnit_register_setting(
		__( 'Default Thumbnail', 'vk-all-in-one-expansion-unit' ),    // tab label.
		'veu_defualt_thumbnail',         // name attr
		'veu_default_thumbnail_options_validate', // sanitaise function name
		'veu_add_default_thumbnail_options_page'  // setting_page function name
	);
}
add_action( 'veu_package_init', 'veu_default_thumbnail_options_init' );

function veu_default_thumbnail_options_validate( $input ) {
    $output['default_thumbnail_image'] = vk_sanitize_number( $input['default_thumbnail_image'] );
    return $output;
}

/*
  Add setting page
/*-------------------------------------------*/

function veu_add_default_thumbnail_options_page() {
	require dirname( __FILE__ ) . '/default-thumbnail-admin.php';

}