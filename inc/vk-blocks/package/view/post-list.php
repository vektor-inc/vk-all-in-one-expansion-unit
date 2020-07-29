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

		if ( ! empty($attributes['className'] ) ){
			$options_loop['class_loop_outer'] .= ' '.$attributes['className'];
		}

		if ( ! isset( $wp_query ) || $wp_query === false || $wp_query === 'false' || $wp_query->posts === array() ) {
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
			'col_xs'                     => esc_html( $attributes['col_xs'] ),
			'col_sm'                     => esc_html( $attributes['col_sm'] ),
			'col_md'                     => esc_html( $attributes['col_md'] ),
			'col_lg'                     => esc_html( $attributes['col_lg'] ),
			'col_xl'                     => esc_html( $attributes['col_xl'] ),
			'class_outer'                => '',
			'class_title'                => '',
			'body_prepend'               => '',
			'body_append'                => '',
			'vkb_hidden'                 => $attributes['vkb_hidden'],
			'vkb_hidden_xl'              => $attributes['vkb_hidden_xl'],
			'vkb_hidden_lg'              => $attributes['vkb_hidden_lg'],
			'vkb_hidden_md'              => $attributes['vkb_hidden_md'],
			'vkb_hidden_sm'              => $attributes['vkb_hidden_sm'],
			'vkb_hidden_xs'              => $attributes['vkb_hidden_xs'],
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

			$term      = get_term( $value );
			$new_array = array(
				'taxonomy' => isset( $term->taxonomy ) ? $term->taxonomy : $key,
				'field'    => 'term_id',
				'terms'    => $value,
			);
			array_push( $return, $new_array );

		}
		return $return;
	}

	public function get_loop_query( $attributes ) {

		$isCheckedPostType = json_decode( $attributes['isCheckedPostType'], true );

		$isCheckedTerms = json_decode( $attributes['isCheckedTerms'], true );

		if ( empty( $isCheckedPostType ) ) {
			return false;
		}

		$post__not_in = array();
		if ( ! empty( $attributes['selfIgnore'] ) ) {
			$post__not_in = array( get_the_ID() );
		}
		$offset = '';
		if ( ! empty( $attributes['offset'] ) ) {
			$offset = intval( $attributes['offset'] );
		}

		$args = array(
			'post_type'      => $isCheckedPostType,
			'tax_query'      => $this::format_terms( $isCheckedTerms ),
			'paged'          => 1,
			// 0で全件取得
			'posts_per_page' => intval( $attributes['numberPosts'] ),
			'order'          => 'DESC',
			'orderby'        => $attributes['orderby'],
			'offset'         => $offset,
			'post__not_in'   => $post__not_in,
		);
		return new WP_Query( $args );
	}

	public function get_loop_query_child( $attributes ) {



		// ParentIdを指定
		if ( isset( $attributes['selectId'] ) && $attributes['selectId'] !== 'false' ) {

			$selectId = ($attributes['selectId'] > 0)? $attributes['selectId']: get_the_ID();

			$post__not_in = array();
			if ( ! empty( $attributes['selfIgnore'] ) ) {
				$post__not_in = array( get_the_ID() );
			}

			$offset = '';
			if ( ! empty( $attributes['offset'] ) ) {
				$offset = intval( $attributes['offset'] );
			}

			$args = array(
				'post_type'      => 'page',
				'paged'          => 0,
				// 0で全件取得
				'posts_per_page' => -1,
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
				'post_parent'    => intval( $selectId ),
				'offset'         => $offset,
				'post__not_in'   => $post__not_in,
			);
			return new WP_Query( $args );

		} else {
			return false;
		}
	}

	public function renderNoPost() {
		return '<div class="alert alert-warning text-center">' . __( 'No Post is selected', 'vk-blocks' ) . '</div>';
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
