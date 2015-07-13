<?php

// admin_menu アクションフックでカスタムボックスを定義
add_action('admin_menu', 'add_custom_field_childPageIndex' );

// データが入力された際 save_post アクションフックを使って何か行う 
add_action('save_post', 'save_custom_field_postdata');

// 固定ページの場合コンテンツの内容を加工する
add_filter('the_content', 'show_childPageIndex', 7);


// メタボックスを追加する関数
function add_custom_field_childPageIndex() {
    add_meta_box('child_Page_index', __('Display a child page index', 'vkExUnit'), 'childPageIndex_box', 'page', 'normal', 'high');
}

// メタボックス内に表示する内容
function childPageIndex_box(){
	global $post;
	$childPageIndex_active = get_post_meta( $post->ID, 'vkExUnit_childPageIndex' );
	echo '<input type="hidden" name="_nonce_vkExUnit__custom_field_childPageIndex" id="_nonce_vkExUnit__custom_field_childPageIndex" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
	echo '<label class="hidden" for="vkExUnit_childPageIndex">'.__('Choose display a child page index', 'vkExUnit').'</label>
		<input type="checkbox" id="vkExUnit_childPageIndex" name="vkExUnit_childPageIndex" value="active"';	
	if( !empty($childPageIndex_active) ) {
		if( $childPageIndex_active[0] === 'active' ) echo ' checked="checked"';
	}
	echo '/>'.__('if checked you will display a child page index ', 'vkExUnit');
}

/* 設定したカスタムフィールドの値をDBに書き込む記述 */
function save_custom_field_postdata( $post_id ) {
    
    $childPageIndex = isset($_POST['_nonce_vkExUnit__custom_field_childPageIndex']) ? htmlspecialchars($_POST['_nonce_vkExUnit__custom_field_childPageIndex']) : null;
    
    // データが先ほど作った編集フォームのから適切な認証とともに送られてきたかどうかを確認。
  // save_post は他の時にも起動する場合がある。
  if( !wp_verify_nonce( $childPageIndex, plugin_basename(__FILE__) )){
  	return $post_id;
	}
	
	
	// 自動保存ルーチンかどうかチェック。そうだった場合はフォームを送信しない（何もしない）
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;
    
    
    // パーミッションチェック 
    $data = isset($_POST['vkExUnit_childPageIndex']) ? htmlspecialchars($_POST['vkExUnit_childPageIndex']) : null;
       
	if('page' == $data){
		if(!current_user_can('edit_page', $post_id)) return $post_id;
	}
		
    if ( "" == get_post_meta( $post_id, 'vkExUnit_childPageIndex' )) {
        /* page_layoutというキーでデータが保存されていなかった場合、新しく保存 */
        add_post_meta( $post_id, 'vkExUnit_childPageIndex', $data, true ) ;
    } else if ( $data != get_post_meta( $post_id, 'vkExUnit_childPageIndex' )) {
        /* page_layoutというキーのデータと、現在のデータが不一致の場合、更新 */
        update_post_meta( $post_id, 'vkExUnit_childPageIndex', $data ) ;
    } else if ( "" == $data ) {
        /* 現在のデータが無い場合、page_layoutというキーの値を削除 */
        delete_post_meta( $post_id, 'vkExUnit_childPageIndex' ) ;
    }
}

// コンテンツの内容を加工する関数
function show_childPageIndex($content) {
global $post;
$childPageIndex_value = get_post_meta( $post->ID, 'vkExUnit_childPageIndex' );
if(!empty($childPageIndex_value)){

	if( is_page() && $childPageIndex_value[0] == 'active'){ 
		
			$my_wp_query = new WP_Query();
			$all_wp_pages = $my_wp_query -> query(array('post_type' => 'page', 'posts_per_page' => -1, 'order' => 'ASC'));
			
			$childrens = get_page_children( $post->post_parent, $all_wp_pages );
			$childPageList = PHP_EOL.'<div class="row childPage_list">'.PHP_EOL;
			if( $post->post_parent ){
			// 子ページ用
				foreach($childrens as $s){
			        $page = $s->ID;
			        $page_data = get_page($page);
			        $pageTitle = $page_data->post_title;
			        $pageContent = $page_data->post_content;
			        $pageLink = $page_data->guid;
			        $childPageList .= PHP_EOL.'<div class="col-md-3"><a href="'.get_permalink($page).'"><h3 class="childPage_list_title">'.$pageTitle.'</h3>'.get_the_post_thumbnail( $page ).'<p class="childPage_list_body">'.mb_substr($pageContent, 0, 50).'<span class="childPage_list_more btn btn-default btn-sm">詳しくはこちら</span></p></a></div>'.PHP_EOL;
			    }
		    } else { 		
			// 親ページ用
				foreach($childrens as $s){
					if($s->post_parent){
				        $page = $s->ID;
				        $page_data = get_page($page);
				        $pageTitle = $page_data->post_title;
				        $pageexcerpt = $page_data->post_excerpt;
				        $pageContent = $page_data->post_content;
				        $pageLink = $page_data->guid;
				        if(!empty($pageexcerpt)){
					        $pageContent = $pageexcerpt;
				        }
				        $childPageList .= PHP_EOL.'<div class="col-md-3"><a href="'.get_permalink($page).'">'.PHP_EOL.'<h3 class="childPage_list_title">'.$pageTitle.'</h3>'.get_the_post_thumbnail( $page ).'<p class="childPage_list_body">'.mb_substr($pageContent, 0, 50).'<span class="childPage_list_more btn btn-default btn-sm">詳しくはこちら</span></p></a></div>'.PHP_EOL; 
					}
			    }	
			}
		$content = $content.$childPageList.'</div>'.PHP_EOL;
		return $content;
	} 
} 
return $content;
}
?>