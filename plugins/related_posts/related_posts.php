<?php
add_filter( 'the_content', 'vkExUnit_add_relatedPosts' , 800 , 1 );

function vkExUnit_add_relatedPosts( $content ) {
	/*-------------------------------------------*/
	/*  Related posts
	/*-------------------------------------------*/

	$max_show_posts      = 10;

	$args_base = array(
		'posts_per_page'   => $max_show_posts,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post__not_in'     => array( get_the_id() ),
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	if ( ! is_single() || get_post_type() != 'post' ) { return $content; }

	global $is_pagewidget;
	if ( $is_pagewidget ) { return $content; }

	$mytags = get_the_tags();

	if ( ! $mytags  || ! is_array( $mytags ) ) { return $content; }

	$tags = array();
	foreach ( $mytags as $t ) { $tags[] = $t->term_id; }

	$args = $args_base;
	$args['tag__and'] = $tags;

	$posts_array = get_posts( $args );
	if ( !is_array( $posts_array ) ) { $posts_array = array(); }

	$post_shortage = $max_show_posts - count( $posts_array );
	if ( $post_shortage > 0 ) {
		$args = $args_base;
		$args['posts_per_page'] = $post_shortage;
		foreach ( $posts_array as $post ) { $args['post__not_in'][] = $post->ID; }
		$args['tag__in'] = $tags;
		$singletags = get_posts( $args );
		if ( is_array( $singletags ) && count( $singletags ) ) { $posts_array = array_merge( $posts_array, $singletags ); }
	}

	$tag_posts = $posts_array;

	// $posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");

	if ( $tag_posts ) {
		$relatedPostsHtml = '<!-- [ .relatedPosts ] -->';
		$relatedPostsHtml .= '<aside class="veu_relatedPosts subSection veu_contentAddSection">';
		$relatedPostsHtml .= '<h1 class="mainSection-title">'.__( 'Related posts','vkExUnit' ).'</h1>';
		$i = 1;
		$relatedPostsHtml .= '<div class="row">';
		foreach ( $tag_posts as $key => $post ) {
			$relatedPostsHtml .= '<div class="col-sm-6 relatedPosts_item">';
			$relatedPostsHtml .= '<div class="media">';
			if ( has_post_thumbnail( $post->ID ) ) :
				$relatedPostsHtml .= '<div class="media-left postList_thumbnail">';
				$relatedPostsHtml .= '<a href="'.get_the_permalink( $post->ID ).'">';
				$relatedPostsHtml .= get_the_post_thumbnail( $post->ID,'thumbnail' );
				$relatedPostsHtml .= '</a>';
				$relatedPostsHtml .= '</div>';
			endif;
			$relatedPostsHtml .= '<div class="media-body">';
			$relatedPostsHtml .= '<div class="media-heading"><a href="'.get_the_permalink( $post->ID ).'">'.$post->post_title.'</a></div>';
			$relatedPostsHtml .= '<div><i class="fa fa-calendar"></i>&nbsp;'.get_the_date( false , $post->ID ).'</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>';
			$relatedPostsHtml .= '</div>'."\n";
			$i++;
		} // foreach
		$relatedPostsHtml .= '</div>';
		$relatedPostsHtml .= '</aside><!-- [ /.relatedPosts ] -->';
		$content .= $relatedPostsHtml;
	}

	wp_reset_postdata();

	return $content;
}
