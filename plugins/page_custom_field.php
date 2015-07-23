<?php 

/*-------------------------------------------*/
/*	add page custom field 
/*-------------------------------------------*/
add_action('admin_menu', 'add_custom_field_pageOption' );
add_action('save_post', 'save_custom_field_postdata');
add_action('save_post', 'save_custom_field_sitemapData');

// add meta_box
function add_custom_field_pageOption() {
    add_meta_box('pageOption', __('Setting of insert items', 'vkExUnit'), 'pageOption_box', 'page', 'normal', 'high');
}

// display a meta_box
function pageOption_box(){
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
	
	// sitemap display
	$active_sitemap_page = get_post_meta( $post->ID, 'vkExUnit_sitemap' );
	echo '<input type="hidden" name="_nonce_vkExUnit__custom_field_sitemap" id="_nonce_vkExUnit__custom_field_sitemap" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
	echo '<label class="hidden" for="vkExUnit_sitemap">'.__('Choose display a child page index', 'vkExUnit').'</label>
		<input type="checkbox" id="vkExUnit_sitemap" name="vkExUnit_sitemap" value="active"';	
	if( !empty($active_sitemap_page) ) {
		if( $active_sitemap_page[0] === 'active' ) echo ' checked="checked"';
	}
	echo '/>'.__('if checked you will display a sitemap', 'vkExUnit');
}

// save custom field childPageIndex
function save_custom_field_postdata( $post_id ) {
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
}

// save custom field sitemap
function save_custom_field_sitemapData( $post_id ) {
    $sitemap = isset($_POST['_nonce_vkExUnit__custom_field_sitemap']) ? htmlspecialchars($_POST['_nonce_vkExUnit__custom_field_sitemap']) : null;
    
	if( !wp_verify_nonce( $sitemap, plugin_basename(__FILE__) )){
  		return $post_id;
	}
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;
    
    $data = isset($_POST['vkExUnit_sitemap']) ? htmlspecialchars($_POST['vkExUnit_sitemap']) : null;
       
	if('page' == $data){
		if(!current_user_can('edit_page', $post_id)) return $post_id;
	}
		
    if ( "" == get_post_meta( $post_id, 'vkExUnit_sitemap' )) {
        add_post_meta( $post_id, 'vkExUnit_sitemap', $data, true ) ;
    } else if ( $data != get_post_meta( $post_id, 'vkExUnit_sitemap' )) {
        update_post_meta( $post_id, 'vkExUnit_sitemap', $data ) ;
    } else if ( "" == $data ) {
        delete_post_meta( $post_id, 'vkExUnit_sitemap' ) ;
    }
}