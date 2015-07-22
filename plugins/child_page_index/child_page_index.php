<?php
	

/*-------------------------------------------*/
/*	Child page index
/*-------------------------------------------*/
add_filter('the_content', 'show_childPageIndex', 7);
remove_filter('the_content','wpautop');

function show_childPageIndex($content) {
global $post;
$childPageIndex_value = get_post_meta( $post->ID, 'vkExUnit_childPageIndex' );
if(!empty($childPageIndex_value)){
// check the childPageIndex_value
	if( is_page() && $childPageIndex_value[0] == 'active'){ 
		
			$my_wp_query = new WP_Query();
			$all_wp_pages = $my_wp_query -> query(array('post_type' => 'page', 'posts_per_page' => -1, 'order' => 'ASC'));
			
			$childrens = get_page_children( $post->post_parent, $all_wp_pages );
			$parentId = $post->ID;

			$childPageList = PHP_EOL.'<div class="row childPage_list">'.PHP_EOL;
			if( $post->post_parent ){
			// child page
				foreach($childrens as $s){
			        $page = $s->ID;
			        $pageData = get_page($page);
			        $pageTitle = $pageData->post_title;
			        $pageContent = strip_tags($pageData->post_content);
			        if(!empty($pageExcerpt)){
					    $pageContent = $pageExcerpt;
				    }
			        $childPageList .= '<a href="'.esc_url(get_permalink($page)).'"><div class="childPage_list_box col-md-6"><h3 class="childPage_list_title">'.esc_html($pageTitle).'</h3><div class="childPage_list_body">'.get_the_post_thumbnail( $page, 'large' ).'<p class="childPage_list_text">'.mb_substr(esc_html($pageContent), 0, 50).'</p></div><span class="childPage_list_more btn btn-default btn-sm">'.__('Read more', 'vkExUnit').'</span></div></a>'.PHP_EOL;
			    }
		    } else { 		
			// parent page
				foreach($childrens as $s){
					if($s->post_parent === $parentId){
				        $page = $s->ID;
				        $pageData = get_page($page);
				        $pageTitle = $pageData->post_title;
				        $pageExcerpt = $pageData->post_excerpt;
				        $pageContent = strip_tags(get_post_field('post_content', $page));
				        if(!empty($pageExcerpt)){
					        $pageContent = $pageExcerpt;
				        }
				        $childPageList .= '<a href="'.esc_url(get_permalink($page)).'"><div class="childPage_list_box col-md-6"><h3 class="childPage_list_title">'.esc_html($pageTitle).'</h3><div class="childPage_list_body">'.get_the_post_thumbnail( $page, 'large' ).'<p class="childPage_list_text">'.mb_substr(esc_html($pageContent), 0, 50).'</p><span class="childPage_list_more btn btn-default btn-sm">'.__('Read more', 'vkExUnit').'</span></div></div></a>'.PHP_EOL; 
					}
			    }	
			}
			
		$content = wpautop($content).$childPageList.'</div>'.PHP_EOL.'<!-- [ /.childPage_list ] -->'.PHP_EOL;
		return $content;
	} 
} 
return wpautop($content);
}

?>