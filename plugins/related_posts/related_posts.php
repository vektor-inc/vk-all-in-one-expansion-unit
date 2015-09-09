<?php
add_filter( 'the_content', 'vkExUnit_add_relatedPosts' , 800 , 1 );

function vkExUnit_add_relatedPosts($content){
	/*-------------------------------------------*/
	/*	Related posts
	/*-------------------------------------------*/

	$max_show_posts = 10;
	$border_of_and_to_in = 2;

	$args_base = array(
		'posts_per_page'   => $max_show_posts,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post__not_in'     => array(get_the_id()),
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);

	if ( get_post_type() == 'post' && is_single() ) :
	global $is_pagewidget;
	if($is_pagewidget) return $content;

	$mytags = get_the_tags();

	if( !count($mytags) ) return ;

	$tags = array();
	foreach( $mytags as $t ) $tags[] = $t->term_id;

	$args = $args_base;
	$args['tag__and'] = $tags;

	$posts_array = get_posts( $args );

	if( count( $posts_array ) < $border_of_and_to_in && count( $tags ) > 1 ){
		$args = $args_base;
		$args['tag__in'] = $tags;
		$posts_array = get_posts( $args );
	}
	$tag_posts = $posts_array;

	// foreach($tag_posts as $tp) echo $tp->post_title." - \n<br/>";

	// $posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");

	if ( $tag_posts ) {
		$relatedPostsHtml = '<!-- [ .relatedPosts ] -->';
		$relatedPostsHtml .= '<aside class="relatedPosts subSection vkContentAddSection">';
		$relatedPostsHtml .= '<h2>'.__('Related posts','vkExUnit').'</h2>';
		$i = 1;
		$relatedPostsHtml .= '<div class="row">';
		foreach ($tag_posts as $key => $post) {
			$relatedPostsHtml .= '<div class="col-sm-6">';
			$relatedPostsHtml .= '<div class="media">';
			if ( has_post_thumbnail($post->ID)) :
			$relatedPostsHtml .= '<div class="media-left postList_thumbnail">';
			$relatedPostsHtml .= '<a href="'.get_the_permalink($post->ID).'">';
			$relatedPostsHtml .= get_the_post_thumbnail($post->ID,'thumbnail');
			$relatedPostsHtml .= '</a>';
			$relatedPostsHtml .= '</div>';
			endif;
			$relatedPostsHtml .= '<div class="media-body">';
			$relatedPostsHtml .= '<div class="media-heading"><a href="'.get_the_permalink($post->ID).'">'.$post->post_title.'</a></div>';
			$relatedPostsHtml .= '<div><i class="fa fa-calendar"></i>&nbsp;'.get_the_date(false , $post->ID).'</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>'."\n";
			$i++;
		} // foreach
		$relatedPostsHtml .= '</div>';
		$relatedPostsHtml .= '</aside><!-- [ /.relatedPosts ] -->';
		$content .= $relatedPostsHtml;
	} // if ( $tag_posts )
	endif;
	wp_reset_postdata();

	return $content;
}