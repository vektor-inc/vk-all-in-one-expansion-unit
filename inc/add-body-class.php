<?php
add_filter( 'body_class', 'veu_add_body_class' );
function veu_add_body_class( $class ) {
	if ( is_singular() ) {
		global $post;
		if ( $post->post_name ) {
			$class[] = 'post-name-' . esc_attr( $post->post_name );
		}
		// カテゴリーslugを追加
		$categories = get_the_category( $post->ID );
		if ( $categories && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$slug_class = 'category-' . $category->slug;
				if ( ! in_array( $slug_class, $class, true ) ) {
					$class[] = $slug_class;
				}
			}
		}

		// タグslugを追加
		$tags = get_the_tags( $post->ID );
		if ( $tags && ! is_wp_error( $tags ) ) {
			foreach ( $tags as $tag ) {
				$slug_class = 'tag-' . $tag->slug;
				if ( ! in_array( $slug_class, $class, true ) ) {
					$class[] = $slug_class;
				}
			}
		}

		// カスタムタクソノミー名及び、slugを追加
		$taxonomies = get_object_taxonomies( get_post_type( $post ), 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			if ( in_array( $taxonomy->name, array( 'category', 'post_tag' ), true ) ) {
				continue;
			}
			$terms = wp_get_post_terms( $post->ID, $taxonomy->name );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$tax_class = 'tax-' . $taxonomy->name;
				if ( ! in_array( $tax_class, $class, true ) ) {
					$class[] = $tax_class;
				}

				foreach ( $terms as $term ) {
					$term_class = $taxonomy->name . '-' . $term->slug;
					if ( ! in_array( $term_class, $class, true ) ) {
						$class[] = $term_class;
					}
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
