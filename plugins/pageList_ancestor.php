<?php
/*-------------------------------------------*/
/*  pageList_ancestor
/*-------------------------------------------*/

add_shortcode( 'pageList_ancestor', 'vkExUnit_pageList_ancestor_shortcode' );
function vkExUnit_pageList_ancestor_shortcode() {
	global $post;
	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true ) ) { return; }

	$pageList_ancestor_html = PHP_EOL.'<section class="veu_pageList_ancestor">'.PHP_EOL;

	if ( $post->ancestors ) {
		foreach ( $post->ancestors as $post_anc_id ) {
			$post_id = $post_anc_id;
		}
	} else {
		$post_id = $post->ID;
	}

	if ( $post_id ) {
			$children = wp_list_pages( 'title_li=&child_of='.$post_id.'&echo=0' );
		if ( $children ) {
			$pageList_ancestor_html .= '<h1 class="section_title subSection-title"><a href="'.get_permalink( $post_id ).'">'.get_the_title( $post_id ).'</a></h1>';
			$pageList_ancestor_html .= '<ul class="pageList">';
			$pageList_ancestor_html .= $children;
			$pageList_ancestor_html .= '</ul>';
			$pageList_ancestor_html .= '</section>';
		}
	}
	return $pageList_ancestor_html;
}

add_filter( 'the_content', 'vkExUnit_pageList_ancestor_contentHook' );
function vkExUnit_pageList_ancestor_contentHook( $content ) {

	global $post;

	if ( ! is_page() || ! get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor',true ) ) { return $content; }

	if ( get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor',true ) ) {
		$content .= "\n[pageList_ancestor]";
	}
	return $content;
}
add_filter( 'vkExUnit_customField_Page_activation', 'vkExUnit_pageList_ancestor_activate_meta_box', 10, 1 );
function vkExUnit_pageList_ancestor_activate_meta_box( $flag ) {
	return true;
}

// admin screen -------------------------------

add_action( 'vkExUnit_customField_Page_box', 'vkExUnit_pageList_ancestor_meta_box' );
add_action( 'save_post', 'vkExUnit_pageList_ancestor' );

function vkExUnit_pageList_ancestor_meta_box() {
	global $post;
	$enable = get_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true ); ?>

<div>   
<input type="hidden" name="_nonce_vkExUnit__custom_field_pageList_ancestor" id="_nonce_vkExUnit__custom_field_pageList_ancestor" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) );?>" />
<label for="vkExUnit_pageList_ancestor">
	<input type="checkbox" id="vkExUnit_pageList_ancestor" name="vkExUnit_pageList_ancestor"<?php echo ($enable)? ' checked' : ''; ?> />
	<?php _e( 'Display a page list from ancestor', 'vkExUnit' );?>
</label>
</div>
<?php
}

// seve function -------------------------------

function vkExUnit_pageList_ancestor( $post_id ) {

	$pageList_ancestor = isset( $_POST['_nonce_vkExUnit__custom_field_pageList_ancestor'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_pageList_ancestor'] ) : null;

	if ( ! wp_verify_nonce( $pageList_ancestor, plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$mydata = isset( $_POST['vkExUnit_pageList_ancestor'] ) ? htmlspecialchars( $_POST['vkExUnit_pageList_ancestor'] ) : null;

	if ( 'page' == $mydata ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_pageList_ancestor' ) ) {
		add_post_meta( $post_id, 'vkExUnit_pageList_ancestor', $mydata, true );
	} else if ( $mydata != get_post_meta( $post_id, 'vkExUnit_pageList_ancestor' ) ) {
		update_post_meta( $post_id, 'vkExUnit_pageList_ancestor', $mydata );
	} else if ( '' == $mydata ) {
		delete_post_meta( $post_id, 'vkExUnit_pageList_ancestor' );
	}

	do_action( 'vkExUnit_customField_Page_save_customField' );
}
