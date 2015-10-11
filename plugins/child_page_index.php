<?php

/*-------------------------------------------*/
/*  Child page index
/*-------------------------------------------*/


add_shortcode( 'vkExUnit_childs', 'vkExUnit_childPageIndex_shortcode' );
function vkExUnit_childPageIndex_shortcode() {
	global $post;
	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_childPageIndex', true ) ) { return; }

	$parentId = $post->ID;
	$args = array(
		'post_type'			=> 'page',
		'posts_per_page'	=> -1,
		'order'				=> 'asc',
		'orderby'			=> 'menu_order',
		'post_parent'		=> $parentId,
		);
	$childrens = new WP_Query( $args );

	if ( ! $childrens->have_posts() ) { return; }

	$childPageList_html = PHP_EOL.'<div class="row veu_childPage_list">'.PHP_EOL;
	while ( $childrens->have_posts() ) : $childrens->the_post();

			// Set Excerpt
			$postExcerpt = $post->post_excerpt;
		if ( ! $postExcerpt ) {
			$postExcerpt = esc_html( mb_substr( strip_tags( $post->post_content ), 0, 90 ) ); // kill tags and trim 120 chara
			if ( mb_strlen( $postExcerpt ) >= 90  ) { $postExcerpt .= '...'; }
		}

			// Page Item build
			$childPageList_html .= '<a href="'.esc_url( get_permalink() ).'" class="col-sm-6 childPage_list_box"><div class="childPage_list_box_inner">';
			$childPageList_html .= '<h3 class="childPage_list_title">'.esc_html( get_the_title() ).'</h3>';
			$childPageList_html .= '<div class="childPage_list_body">'.get_the_post_thumbnail( $post->ID, 'large' );
			$childPageList_html .= '<p class="childPage_list_text">'.esc_html( $postExcerpt ).'</p></div>';
			$childPageList_html .= '<span class="childPage_list_more btn btn-primary btn-xs">'.__( 'Read more', 'vkExUnit' ).'</span>';
			$childPageList_html .= '</div></a>'.PHP_EOL;

	endwhile;
	wp_reset_postdata();
	$childPageList_html .= PHP_EOL.'</div><!-- [ /.childPage_list ] -->'.PHP_EOL;

	return $childPageList_html;
}


add_filter( 'the_content', 'vkExUnit_childPageIndex_contentHook', 7, 1 );
function vkExUnit_childPageIndex_contentHook( $content ) {
	if ( vkExUnit_is_excerpt() ) { return $content; }
	global $is_pagewidget;
	if ( $is_pagewidget ) { return $content; }
	global $post;
	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_childPageIndex',true ) ) { return $content; }

	$content .= "\n[vkExUnit_childs]";

	return $content;
}



add_filter( 'vkExUnit_customField_Page_activation', 'vkExUnit_childPageIndex_activate_meta_box', 10, 1 );
function vkExUnit_childPageIndex_activate_meta_box( $flag ) {
	return true;
}



add_action( 'vkExUnit_customField_Page_box', 'vkExUnit_childPageIndex_meta_box' );
function vkExUnit_childPageIndex_meta_box() {
	global $post;
	// childPageIndex display
	$enable = get_post_meta( $post->ID, 'vkExUnit_childPageIndex', true );?>
<div>
<input type="hidden" name="_nonce_vkExUnit__custom_field_childPageIndex" id="_nonce_vkExUnit__custom_field_childPageIndex" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) );?>" />
<label for="vkExUnit_childPageIndex">
	<input type="checkbox" id="vkExUnit_childPageIndex" name="vkExUnit_childPageIndex"<?php echo ($enable)? ' checked' : ''; ?> />
	<?php _e( 'Display a child page index', 'vkExUnit' );?>
</label>
</div>

	<?php
}


// save custom field childPageIndex
add_action( 'save_post', 'vkExUnit_save_custom_field_postdata' );
function vkExUnit_save_custom_field_postdata( $post_id ) {
	$childPageIndex = isset( $_POST['_nonce_vkExUnit__custom_field_childPageIndex'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_childPageIndex'] ) : null;

	if ( ! wp_verify_nonce( $childPageIndex, plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$data = isset( $_POST['vkExUnit_childPageIndex'] ) ? htmlspecialchars( $_POST['vkExUnit_childPageIndex'] ) : null;

	if ( 'page' == $data ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ) {
		add_post_meta( $post_id, 'vkExUnit_childPageIndex', $data, true );
	} else if ( $data != get_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ) {
		update_post_meta( $post_id, 'vkExUnit_childPageIndex', $data );
	} else if ( '' == $data ) {
		delete_post_meta( $post_id, 'vkExUnit_childPageIndex' );
	}

	do_action( 'vkExUnit_customField_Page_save_customField' );
}
