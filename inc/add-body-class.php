<?php
add_filter( 'body_class', 'veu_add_body_class' );
function veu_add_body_class( $class ) {
	if ( is_singular() ) {
		global $post;
		if ( $post->post_name ) {
			$class[] = 'post-name-' . esc_attr( $post->post_name );
		}
		// カテゴリーIDを追加
		$categories = get_the_category( $post->ID );
		if ( $categories && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$class[] = 'category-id-' . $category->term_id;
			}
		}

		// タグIDを追加
		$tags = get_the_tags( $post->ID );
		if ( $tags && ! is_wp_error( $tags ) ) {
			foreach ( $tags as $tag ) {
				$class[] = 'tag-id-' . $tag->term_id;
			}
		}

		// カスタムタクソノミーIDを追加
		$taxonomies = get_object_taxonomies( get_post_type( $post ), 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			// カテゴリー・タグ以外のタクソノミーのみ対象にする
			if ( in_array( $taxonomy->name, array( 'category', 'post_tag' ) ) ) {
				continue;
			}
			$terms = wp_get_post_terms( $post->ID, $taxonomy->name );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$class[] = $taxonomy->name . '-id-' . $term->term_id;
				}
			}
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
