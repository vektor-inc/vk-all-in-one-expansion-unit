<?php

class VkBlocksPostList {

	/**
	 * Return html to display latest post list.
	 *
	 * @param $name
	 * @param $attributes
	 *
	 * @return string
	 */
	public function render_post_list( $attributes ) {

		if ( isset( $attributes['name'] ) ) {

			$name = esc_html( $attributes['name'] );
			if ( $name === 'vk-blocks/child-page' ) {
				$wp_query     = $this->get_loop_query_child( $attributes );
				$options_loop = array( 'class_loop_outer' => 'vk_childPage' );

			} elseif ( $name === 'vk-blocks/post-list' ) {
				$wp_query     = $this->get_loop_query( $attributes );
				$options_loop = array( 'class_loop_outer' => 'vk_postList' );
			}
		} else {
			$wp_query     = $this->get_loop_query( $attributes );
			$options_loop = array( 'class_loop_outer' => '' );
		}

		if ( $wp_query === false || $wp_query === 'false' || $wp_query->posts === array() ) {
			return $this->renderNoPost();
		}

		$options = array(
			'layout'                     => esc_html( $attributes['layout'] ),
			'slug'                       => '',
			'display_image'              => esc_html( $attributes['display_image'] ),
			'display_image_overlay_term' => esc_html( $attributes['display_image_overlay_term'] ),
			'display_excerpt'            => esc_html( $attributes['display_excerpt'] ),
			'display_date'               => esc_html( $attributes['display_date'] ),
			'display_new'                => esc_html( $attributes['display_new'] ),
			'display_btn'                => esc_html( $attributes['display_btn'] ),
			'image_default_url'          => VK_BLOCKS_URL . 'images/no-image.png',
			'overlay'                    => false,
			'new_text'                   => esc_html( $attributes['new_text'] ),
			'new_date'                   => esc_html( $attributes['new_date'] ),
			'btn_text'                   => esc_html( $attributes['btn_text'] ),
			'btn_align'                  => esc_html( $attributes['btn_align'] ),
			'class_outer'                => VK_Component_Posts::get_col_size_classes( $attributes ),
			'class_title'                => '',
			'body_prepend'               => '',
			'body_append'                => '',
		);

		$elm = VK_Component_Posts::get_loop( $wp_query, $options, $options_loop );

		wp_reset_query();
		wp_reset_postdata();

		return $elm;
	}

	private function isArrayExist( $array ) {
		if ( ! $array ) {
			return false;
		}
		return true;
	}

	private function format_terms( $isCheckedTerms ) {

		$return             = array();
		$return['relation'] = 'OR';

		foreach ( $isCheckedTerms as $key => $value ) {

			if ( $value !== array() ) {

				$new_array = array(
					'taxonomy' => $key,
					'field'    => 'slug',
					'terms'    => $value,
				);
				array_push( $return, $new_array );
			}
		}
		return $return;
	}

	public function get_loop_query( $attributes ) {

		$isCheckedPostType = json_decode( $attributes['isCheckedPostType'], true );
		$isCheckedTerms    = json_decode( $attributes['isCheckedTerms'], true );

		if ( empty( $isCheckedPostType ) ) {
			return false;
		}

		$args = array(
			'post_type'      => $isCheckedPostType,
			'tax_query'      => $this::format_terms( $isCheckedTerms ),
			'paged'          => 1,
			// 0で全件取得
			'posts_per_page' => intval( $attributes['numberPosts'] ),
			'order'          => 'DESC',
			'orderby'        => 'date',
		);
		return new WP_Query( $args );
	}

	public function get_loop_query_child( $attributes ) {

		// ParentIdを指定
		if ( isset( $attributes['selectId'] ) && $attributes['selectId'] !== 'false' ) {
			$args = array(
				'post_type'      => 'page',
				'paged'          => 0,
				// 0で全件取得
				'posts_per_page' => -1,
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
				'post_parent'    => intval( $attributes['selectId'] ),
			);
			return new WP_Query( $args );

		} else {
			return false;
		}
	}

	public function renderNoPost() {
		return '<div>' . __( 'No Post is selected', 'vk-blocks' ) . '</div>';
	}

}

/**
 * Gutenberg Callback function.
 *
 * @param $name
 * @param $attributes
 *
 * @return string
 */
function vk_blocks_render_post_list( $attributes ) {

	$PostList = new VkBlocksPostList();

	return $PostList->render_post_list( $attributes );

}
