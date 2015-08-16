<?php
	

/*-------------------------------------------*/
/*	Child page index
/*-------------------------------------------*/
add_filter('the_content', 'show_childPageIndex', 7);

function show_childPageIndex($content) {
	remove_filter('the_content','wpautop');
	global $post;
	$childPageIndex_value = get_post_meta( $post->ID, 'vkExUnit_childPageIndex' );
	if(!empty($childPageIndex_value)){
		// check the childPageIndex_value
		if( is_page() && $childPageIndex_value[0] == 'active'){

			$parentId = $post->ID;
			$args = array(
				'post_type'			=> 'page',
				'posts_per_page'	=> -1,
				'order'				=> 'asc',
				'orderby'			=> 'menu_order',
				'post_parent'		=> $parentId,
				);
			$childrens = new WP_Query($args);

			if ($childrens->have_posts()) :
				$childPageList_html = PHP_EOL.'<div class="row childPage_list">'.PHP_EOL;
				while ( $childrens->have_posts() ) : $childrens->the_post();

						// Set Excerpt
						$postExcerpt = $post->post_excerpt;
						if ( !$postExcerpt) {
							$postExcerpt =  esc_html(mb_substr( strip_tags($post->post_content), 0, 120 )); // kill tags and trim 120 chara
						}

						// Page Item build
				        $childPageList_html .= '<a href="'.esc_url(get_permalink()).'"><div class="childPage_list_box col-md-6">';
				        $childPageList_html .= '<h3 class="childPage_list_title">'.esc_html(get_the_title()).'</h3>';
				        $childPageList_html .= '<div class="childPage_list_body">'.get_the_post_thumbnail( $post->ID, 'large' );
				        $childPageList_html .= '<p class="childPage_list_text">'.esc_html($postExcerpt).'</p></div>';
				        $childPageList_html .= '<span class="childPage_list_more btn btn-default btn-sm">'.__('Read more', 'vkExUnit').'</span>';
				        $childPageList_html .= '</div></a>'.PHP_EOL;

				endwhile;
				$childPageList_html .= PHP_EOL.'</div><!-- [ /.childPage_list ] -->'.PHP_EOL;

				return wpautop($content).$childPageList_html;

			else :

				return wpautop($content);

			endif;
		} // if( is_page() && $childPageIndex_value[0] == 'active'){
	} // if(!empty($childPageIndex_value)){

	return wpautop($content);
	add_filter('the_content','wpautop');
}


add_filter('vkExUnit_customField_Page_activation', 'vkExUnit_childPageIndex_activate_meta_box', 10, 1);
function vkExUnit_childPageIndex_activate_meta_box( $flag ){
	return true;
}


add_action('vkExUnit_customField_Page_box', 'vkExUnit_childPageIndex_meta_box');
function vkExUnit_childPageIndex_meta_box(){
	global $post;
	// childPageIndex display
	$childPageIndex_active = get_post_meta( $post->ID, 'vkExUnit_childPageIndex' );
	echo '<div><input type="hidden" name="_nonce_vkExUnit__custom_field_childPageIndex" id="_nonce_vkExUnit__custom_field_childPageIndex" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
	echo '<label class="hidden" for="vkExUnit_childPageIndex">'.__('Choose display a child page index', 'vkExUnit').'</label>
		<input type="checkbox" id="vkExUnit_childPageIndex" name="vkExUnit_childPageIndex" value="active"';	
	if( !empty($childPageIndex_active) ) {
		if( $childPageIndex_active[0] === 'active' ) echo ' checked="checked"';
	}
	echo '/>'.__('if checked you will display a child page index ', 'vkExUnit').'</div>';
}


// save custom field childPageIndex
add_action('save_post', 'vkExUnit_save_custom_field_postdata');
function vkExUnit_save_custom_field_postdata( $post_id ) {
    $childPageIndex = isset($_POST['_nonce_vkExUnit__custom_field_childPageIndex']) ? htmlspecialchars($_POST['_nonce_vkExUnit__custom_field_childPageIndex']) : null;
    
	if( !wp_verify_nonce( $childPageIndex, plugin_basename(__FILE__) )){
  		return $post_id;
	}
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;
    
    $data = isset($_POST['vkExUnit_childPageIndex']) ? htmlspecialchars($_POST['vkExUnit_childPageIndex']) : null;
       
	if('page' == $data){
		if(!current_user_can('edit_page', $post_id)) return $post_id;
	}
		
    if ( "" == get_post_meta( $post_id, 'vkExUnit_childPageIndex' )) {
        add_post_meta( $post_id, 'vkExUnit_childPageIndex', $data, true ) ;
    } else if ( $data != get_post_meta( $post_id, 'vkExUnit_childPageIndex' )) {
        update_post_meta( $post_id, 'vkExUnit_childPageIndex', $data ) ;
    } else if ( "" == $data ) {
        delete_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ;
    }

    do_action('vkExUnit_customField_Page_save_customField');
}