<?php
add_filter( 'body_class', 'veu_add_body_class' );
function veu_add_body_class( $class ) {
	if ( is_singular() ) {
		global $post;
		if ( $post->post_name ) {
			$class[] = 'post-name-' . esc_attr( $post->post_name );
		}
	}

	if ( is_archive() || is_singular() || ( is_home() && ! is_front_page() ) ) {
		if ( function_exists( 'vk_get_post_type' ) ) {
			$post_type_info = vk_get_post_type();
			if ( ! empty( $post_type_info['slug'] ) ) {
				$class[] = 'post-type-' . $post_type_info['slug'];
			} // if ( ! empty( $post_type_info['slug'] ) ) {
		} // if ( function_exists( 'vk_get_post_type' ) ) {
	} // if ( is_archive() ) {

	return $class;

} // function veu_add_body_class( $class ) {
