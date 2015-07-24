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