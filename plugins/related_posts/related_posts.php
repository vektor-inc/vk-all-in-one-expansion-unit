<?php
add_filter( 'the_content', 'vkExUnit_add_relatedPosts');

function vkExUnit_add_relatedPosts($content){

	/*-------------------------------------------*/
	/*	Related posts
	/*-------------------------------------------*/
	if ( get_post_type() == 'post' && is_single() ) :
	global $post;
	// Get now post's tag(terms)
	$relatedPostCount = 10;
	if (isset($relatedPostCount) && $relatedPostCount ) {
	$terms = get_the_terms($post->ID,'post_tag');
	$tag_count = count($terms);
	if ($terms) {
	$posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");
	// Set basic arrays
	$args = array( 'post-type' => 'post' ,'post__not_in' => array($post->ID), 'posts_per_page' => $posts_count );
	// Set tag(term) arrays
	if ( $terms && $tag_count == 1 ) {
		foreach ( $terms as $key => $value) {
			$args['tag_id'] = $value->term_id ;
		}
	} else if ( $terms ) {
		foreach ( $terms as $key => $value) {
			$args['tag__in'][] = $value->term_id ;
		}
	}
	$tag_posts = get_posts($args);
	if ( $tag_posts ) {
		$relatedPostsHtml = '<!-- [ .relatedPosts ] -->';
		$relatedPostsHtml .= '<aside class="relatedPosts subSection vkContentAddSection">';
		$relatedPostsHtml .= '<h2>'.__('Related posts','vkExUnit').'</h2>';
		$i = 1;
		$relatedPostsHtml .= '<div class="row">';
		foreach ($tag_posts as $key => $post) {
			$relatedPostsHtml .= '<div class="col-sm-6">';
			$relatedPostsHtml .= '<div class="media">';
			if ( has_post_thumbnail()) :
			$relatedPostsHtml .= '<div class="media-left postList_thumbnail">';
			$relatedPostsHtml .= '<a href="'.get_the_permalink().'">';
			$relatedPostsHtml .= get_the_post_thumbnail($post->ID,'thumbnail');
			$relatedPostsHtml .= '</a>';
			$relatedPostsHtml .= '</div>';
			endif;
			$relatedPostsHtml .= '<div class="media-body">';
			$relatedPostsHtml .= '<div class="media-heading"><a href="'.get_the_permalink().'">'.get_the_title().'</a></div>';
			$relatedPostsHtml .= '<div><i class="fa fa-calendar"></i>&nbsp;'.get_the_date().'</div>';   
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>'."\n";
			$i++;
		} // foreach 
		$relatedPostsHtml .= '</div>';
		$relatedPostsHtml .= '</aside><!-- [ /.relatedPosts ] -->';
		$content .= $relatedPostsHtml;
	} // if ( $tag_posts )
	} // if ( $terms )
	} // if ( $relatedPostCount ) {
	endif;
	wp_reset_postdata();

	return $content;
}