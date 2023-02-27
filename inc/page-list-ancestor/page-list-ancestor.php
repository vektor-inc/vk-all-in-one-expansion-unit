<?php
/*-------------------------------------------*/
/*  pageList_ancestor
/*-------------------------------------------*/

add_shortcode( 'pageList_ancestor', 'vkExUnit_pageList_ancestor_shortcode' );

if ( veu_content_filter_state() == 'content' ) {
	add_filter( 'the_content', 'vkExUnit_pageList_ancestor_contentHook', 10, 1 );
} else {
	add_action( 'loop_end', 'vkExUnit_pageList_ancestor_loopend', 10, 1 );
}


function vkExUnit_pageList_ancestor_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	echo vkExUnit_pageList_ancestor_shortcode();
}


function vkExUnit_pageList_ancestor_shortcode( $class_name = '', $force = false ) {
	global $is_pagewidget;

	if ( $is_pagewidget ) {
		global $widget_pageid;
		$post = get_post( $widget_pageid );
	} else {
		global $post;
		if(
			!$force
			&& (
				! is_page()
				|| ! get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true )
			)
		){
			return '';
		}
	}

	if ( $post->ancestors ) {
		foreach ( $post->ancestors as $post_anc_id ) {
			$post_id = $post_anc_id;
		}
	} else {
		$post_id = $post->ID;
	}

	if ( $post_id ) {
		$children = wp_list_pages( array(
			'title_li' => '',
			'child_of' => $post_id,
			'echo' => 0
		) );

		$classes = 'veu_pageList_ancestor veu_card';
		if ( ! empty( $class_name ) ) {
			$classes .= ' ' . $class_name;
		}

		if ( $children ) {
			$pageList_ancestor_html  = '<section class="' . esc_attr( $classes ) . '">';
			$pageList_ancestor_html .= '<div class="veu_card_inner">';
			$pageList_ancestor_html .= '<h3 class="pageList_ancestor_title veu_card_title"><a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a></h3>';
			$pageList_ancestor_html .= '<ul class="pageList">';
			$pageList_ancestor_html .= $children;
			$pageList_ancestor_html .= '</ul>';
			$pageList_ancestor_html .= '</div>';
			$pageList_ancestor_html .= '</section>';
		} else {
			return '';
		}
	}
	wp_reset_query();
	wp_reset_postdata();
	return wp_kses_post( $pageList_ancestor_html );
}


function vkExUnit_pageList_ancestor_contentHook( $content ) {

	global $post;

	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true ) ) {
		return $content;
	}

	if ( get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true ) ) {
		$content .= "\n[pageList_ancestor]";
	}
	return $content;
}

/*-------------------------------------------*/
/* admin_metabox_content
/*-------------------------------------------*/
add_action( 'veu_metabox_insert_items', 'vkExUnit_pageList_ancestor_admin_metabox_content' );
function vkExUnit_pageList_ancestor_admin_metabox_content() {
	global $post;
	$enable = get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true ); ?>

<div>
<input type="hidden" name="_nonce_vkExUnit__custom_field_pageList_ancestor" id="_nonce_vkExUnit__custom_field_pageList_ancestor" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
<label for="vkExUnit_pageList_ancestor">
	<input type="checkbox" id="vkExUnit_pageList_ancestor" name="vkExUnit_pageList_ancestor"<?php echo ( $enable ) ? ' checked' : ''; ?> />
	<?php _e( 'Display a page list from ancestor', 'vk-all-in-one-expansion-unit' ); ?>
</label>
</div>
<?php
}


/*-------------------------------------------*/
/* save_custom_field
/*-------------------------------------------*/
add_action( 'save_post', 'veu_page_list_ancestor_save_custom_field' );
function veu_page_list_ancestor_save_custom_field( $post_id ) {

	$pageList_ancestor = isset( $_POST['_nonce_vkExUnit__custom_field_pageList_ancestor'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_pageList_ancestor'] ) : null;

	if ( ! wp_verify_nonce( $pageList_ancestor, plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$mydata = isset( $_POST['vkExUnit_pageList_ancestor'] ) ? htmlspecialchars( $_POST['vkExUnit_pageList_ancestor'] ) : null;

	if ( 'page' == $mydata ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_pageList_ancestor' ) ) {
		add_post_meta( $post_id, 'vkExUnit_pageList_ancestor', $mydata, true );
	} elseif ( $mydata != get_post_meta( $post_id, 'vkExUnit_pageList_ancestor' ) ) {
		update_post_meta( $post_id, 'vkExUnit_pageList_ancestor', $mydata );
	} elseif ( '' == $mydata ) {
		delete_post_meta( $post_id, 'vkExUnit_pageList_ancestor' );
	}

	do_action( 'vkExUnit_customField_Page_save_customField' );
}

require_once dirname( __FILE__ ) . '/block/index.php';
