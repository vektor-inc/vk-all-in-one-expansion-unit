<?php
	
 
// メタボックスを追加する関数
function add_custom_field_childPageIndex() {
//    add_meta_box('nskw_meta_post_page', 'Test Meta Box', 'nskw_meta_box_inside', 'post', 'side', 'low' );
    add_meta_box('div2', __('Child page index', 'vkExUnit'), 'childPageIndex_meta_box', 'page', 'normal', 'high');
}

// フックする
add_action('admin_menu', 'add_custom_field_childPageIndex' );

function html_source_for_layout_custom_box() {
    $page_layout = get_post_meta( $_GET['post'], 'div2' );
 
    echo '<label for="page_layout">レイアウトタイプを選択してください。</label> ';
}
?>