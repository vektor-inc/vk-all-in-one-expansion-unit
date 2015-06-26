<?php


add_action('admin_menu', 'vkExUnit_add_custom_field_metaKeyword');
add_action('save_post' , 'vkExUnit_save_custom_field_metaKeyword');

function vkExUnit_add_custom_field_metaKeyword(){
	if(function_exists('add_custom_field_metaKeyword') || true){
		add_meta_box('div1', __('Meta Keywords', 'vkExUnit'), 'vkExUnit_metaKeyword_render_meta_box', 'page', 'normal', 'high');
		add_meta_box('div1', __('Meta Keywords', 'vkExUnit'), 'vkExUnit_metaKeyword_render_meta_box', 'post', 'normal', 'high');
	}
}

function vkExUnit_metaKeyword_render_meta_box(){
	global $post;
	echo '<input type="hidden" name="_nonce_vkExUnit__custom_field_metaKeyword" id="_nonce_vkExUnit__custom_field_metaKeyword" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
	echo '<label class="hidden" for="vkExUnit_metaKeyword">'.__('Meta Keywords', 'biz-vektor').'</label><input type="text" id="vkExUnit_metaKeyword" name="vkExUnit_metaKeyword" size="50" value="'.get_post_meta($post->ID, 'vkExUnit_metaKeyword', true).'" />';
	echo '<p>'.__('To distinguish between individual keywords, please enter a , delimiter (optional).', 'vkExUnit').'<br />';
	$theme_option_seo_link = '<a href="'.get_admin_url().'/themes.php?page=theme_options#seoSetting" target="_blank">'._x('','link to seo setting', 'vkExUnit').'</a>';
	sprintf(__('* keywords common to the entire site can be set from %s.', 'vkExUnit'),$theme_option_seo_link);
	echo '</p>';
}

function vkExUnit_save_custom_field_metaKeyword($post_id){
	$metaKeyword = isset($_POST['_nonce_vkExUnit__custom_field_metaKeyword']) ? htmlspecialchars($_POST['_nonce_vkExUnit__custom_field_metaKeyword']) : null;

    // ドラフトなら破棄
    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
       return $post_id;

	if(!wp_verify_nonce($metaKeyword, plugin_basename(__FILE__))){
		return $post_id;
	}
	if('page' == $_POST['vkExUnit_metaKeyword']){
		if(!current_user_can('edit_page', $post_id)) return $post_id;
	}else{
		if(!current_user_can('edit_post', $post_id)) return $post_id;
	}

	$data = $_POST['vkExUnit_metaKeyword'];

	if(get_post_meta($post_id, 'vkExUnit_metaKeyword') == ""){
		add_post_meta($post_id, 'vkExUnit_metaKeyword', $data, true);
	}elseif($data != get_post_meta($post_id, 'vkExUnit_metaKeyword', true)){
		update_post_meta($post_id, 'vkExUnit_metaKeyword', $data);
	}elseif($data == ""){
		delete_post_meta($post_id, 'vkExUnit_metaKeyword', get_post_meta($post_id, 'vkExUnit_metaKeyword', true));
	}
}

function vkExUnit_metaKeyword_get_postKeyword(){
	$post_id = get_the_id();
	if(empty($post_id))
		return null;

	$keyword = get_post_meta($post_id, 'vkExUnit_metaKeyword');
	return $keyword;
}