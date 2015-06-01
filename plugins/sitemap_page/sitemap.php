<?php
function vkExUnit_sitemap($atts) {

    extract(shortcode_atts(array(
        'exclude' => '',
        'add_post_type' => '',
    ), $atts));

	$sitemap_html = '<div class="row sitemap">';

	/*-------------------------------------------*/
	/* pages
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">';
	$sitemap_html .= '<ul class="link-list">';
	$args = array(
		'title_li' 	=> '',
		'echo'		=> 0,
		'exclude_tree'	=> $exclude,
	);
	$sitemap_html .= wp_list_pages($args);

	$sitemap_html .= '</ul>';
	$sitemap_html .= '</div>';


	/*-------------------------------------------*/
	/* Posts & Custom posts
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">';

	$page_for_posts = vkExUnit_get_page_for_posts();

	//post types to display on sitemap
	$postTypes 	= array('post');
	if ($add_post_type){
		$addPostTypes = explode(",",$add_post_type);
		$postTypes = array_merge($postTypes, $addPostTypes);
	}

	// Get All taxonomy data
	$taxonomies = get_taxonomies();

	// Loop all post types
	foreach ($postTypes as $key => $postType) {

		$sitemap_html .= '<div class="sectionBox">';
		$post_type_object = get_post_type_object($postType);
		if($post_type_object){

			// Post type name
			if ( $postType == 'post' && $page_for_posts['post_top_use'] ){
				$postTypeName = $page_for_posts['post_top_name'];
				$postTypeTopUrl = get_the_permalink($page_for_posts['post_top_id']);
			} else {
				$postTypeName = $post_type_object->labels->name;
				$postTypeTopUrl = home_url().'/?post_type='.$postType;
			}
			$sitemap_html .= '<h4><a href="'.$postTypeTopUrl.'">'.esc_html($postTypeName).'</a></h4>';
			
			// Loop for all taxonomies
			foreach ($taxonomies as $key => $taxonomy) {
				$taxonomy_info = get_taxonomy( $taxonomy );

				// Get tax related post type
				$taxonomy_postType = $taxonomy_info->object_type[0];

				if ( $taxonomy_postType == $postType && ( $taxonomy_info->name != 'post_format')){
					$sitemap_html .= '<h5>'.$taxonomy_info->labels->name.'</h5>';
					$sitemap_html .= '<ul class="link-list">';
										$args = array(
											'taxonomy' => $taxonomy,
											'title_li' => '',
											'orderby' => 'order',
											'echo'	=> 0,
											'show_option_none' => '',
										);
					$sitemap_html .= wp_list_categories( $args );
					$sitemap_html .= '</ul>';
				}
			}
		} // if($post_type_object)
		$sitemap_html .= '</div>';
	} // foreach ($postTypes as $key => $postType)
	$sitemap_html .= '</div>';
	$sitemap_html .= '</div><!-- [ /.row ] -->';

    return $sitemap_html;
}
add_shortcode('vkExUnit_sitemap', 'vkExUnit_sitemap');