<?php

/*
  Child page index
/*-------------------------------------------*/

function veu_child_page_excerpt( $post ) {

	// そもそも post_excerpt が存在しなかったらreturn（ $post自体が正しく受け取れてない ）
	if ( ! isset( $post->post_excerpt ) ) {
		return; }

	// 抜粋を取得
	$page_excerpt = nl2br( esc_textarea( strip_tags( $post->post_excerpt ) ) );

	// 抜粋欄が未入力だった場合（本文欄の内容を引っ張る）
	if ( ! $page_excerpt ) {

		// 本文欄から取得し、タグを除去
		$page_excerpt = esc_textarea( strip_tags( $post->post_content ) );

		if ( 90 < mb_strlen( $page_excerpt ) ) {
			// 90文字でトリム
			$page_excerpt = mb_substr( $page_excerpt, 0, 90 );
			// ... を追加
			$page_excerpt .= '...';

		}
	}

	$page_excerpt = str_replace( PHP_EOL, '', $page_excerpt );

	return $page_excerpt;
}

add_shortcode( 'vkExUnit_childs', 'vkExUnit_childPageIndex_shortcode' );
function vkExUnit_childPageIndex_shortcode( $parentId = null, $classes = '' ) {

	if ( empty( $parentId ) ) {
		global $is_pagewidget;

		if ( $is_pagewidget ) {
			global $widget_pageid;

			$parentId = $widget_pageid;
		} else {
			global $post;
			$parentId = $post->ID;
		}
	}

	$args      = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'post_parent'    => $parentId,
	);
	$childrens = get_posts( $args );

	if ( empty( $childrens ) ) {
		wp_reset_query();
		return false; }

	$childPageList_html = PHP_EOL . '<div class="veu_childPage_list ' . esc_attr( $classes ) . '">' . PHP_EOL;
	foreach ( $childrens as $children ) :

			$postExcerpt = veu_child_page_excerpt( $children );
			$outer_class = 'childPage_list_box veu_card ' . implode( ' ', get_post_class( '', $children->ID ) );

			// Page Item build
			$childPageList_html .= '<a href="' . esc_url( get_permalink( $children->ID ) ) . '" id="post-' . esc_attr( $children->ID ) . '" class="' . $outer_class . '">';
			$childPageList_html .= '<div class="childPage_list_box_inner veu_card_inner">';
			$childPageList_html .= '<h3 class="childPage_list_title veu_card_title">' . wp_kses_post( $children->post_title ) . '</h3>';
			$childPageList_html .= '<div class="childPage_list_body">';
			$childPageList_html .= apply_filters( 'veu_child_index_thumbnail', get_the_post_thumbnail( $children->ID, 'thumbnail' ), $children->ID );
			/*
		Customize example
		add_filter( 'veu_child_index_thumbnail', function( $return, $id ){
		$return = '<div class="xxx">' . get_the_post_thumbnail( $id, 'thumbnail' ) . '</div>';
		return $return;
		}, 10, 2 );
			*/
			$childPageList_html .= '<p class="childPage_list_text">' . $postExcerpt . '</p>';
			$childPageList_html .= '<span class="childPage_list_more btn btn-primary btn-sm">' . apply_filters( 'veu_childPage_list_read_more_txt', __( 'Read more', 'vk-all-in-one-expansion-unit' ) ) . '</span>';
			$childPageList_html .= '</div>';

			$childPageList_html .= '</div></a>' . PHP_EOL;
	endforeach;

	$childPageList_html .= PHP_EOL . '</div><!-- [ /.veu_childPage_list ] -->' . PHP_EOL;
	wp_reset_query();

	return $childPageList_html;
}


if ( veu_content_filter_state() == 'content' ) {
	add_filter( 'the_content', 'vkExUnit_childPageIndex_contentHook', 7, 1 );
} else {
	add_action( 'loop_end', 'vkExUnit_chidPageIndex_loopend', 10, 1 );
}


function vkExUnit_chidPageIndex_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	echo vkExUnit_childPageIndex_shortcode();
}

/*
  Print Child Page Box at Page
/*-------------------------------------------*/
function vkExUnit_childPageIndex_contentHook( $content ) {

	// 抜粋だったらそのまま返す
	if ( vkExUnit_is_excerpt() ) {
		return $content; }

	// ウィジェットだったらそのまま返す
	global $is_pagewidget;
	if ( $is_pagewidget ) {
		return $content; }

	// 固定ページじゅあないか、子ページインデックスを出力する設定でない場合はそのまま返す
	global $post;
	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_childPageIndex', true ) ) {
		return $content; }

	$content .= "\n[vkExUnit_childs]";

	return $content;
}

/*
 admin_metabox_content
/*-------------------------------------------*/
add_action( 'veu_metabox_insert_items', 'veu_child_page_index_admin_metabox_content' );
function veu_child_page_index_admin_metabox_content() {
	global $post;
	// childPageIndex display
	$enable = get_post_meta( $post->ID, 'vkExUnit_childPageIndex', true );?>
<div>
<input type="hidden" name="_nonce_vkExUnit__custom_field_childPageIndex" id="_nonce_vkExUnit__custom_field_childPageIndex" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
<label for="vkExUnit_childPageIndex">
	<input type="checkbox" id="vkExUnit_childPageIndex" name="vkExUnit_childPageIndex"<?php echo ( $enable ) ? ' checked' : ''; ?> />
	<?php _e( 'Display a child page index', 'vk-all-in-one-expansion-unit' ); ?>
</label>
</div>
	<?php
}

/*
 save_custom_field
/*-------------------------------------------*/
add_action( 'save_post', 'veu_child_page_index_save_custom_field' );
function veu_child_page_index_save_custom_field( $post_id ) {
	$childPageIndex = isset( $_POST['_nonce_vkExUnit__custom_field_childPageIndex'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_childPageIndex'] ) : null;

	if ( ! wp_verify_nonce( $childPageIndex, plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$data = isset( $_POST['vkExUnit_childPageIndex'] ) ? htmlspecialchars( $_POST['vkExUnit_childPageIndex'] ) : null;

	if ( 'page' == $data ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ) {
		add_post_meta( $post_id, 'vkExUnit_childPageIndex', $data, true );
	} elseif ( $data != get_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ) {
		update_post_meta( $post_id, 'vkExUnit_childPageIndex', $data );
	} elseif ( '' == $data ) {
		delete_post_meta( $post_id, 'vkExUnit_childPageIndex' );
	}

	do_action( 'vkExUnit_customField_Page_save_customField' );
}

require_once dirname( __FILE__ ) . '/block/index.php';
