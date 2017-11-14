<?php

if( veu_content_filter_state() == 'content' )  add_filter( 'the_content', 'vkExUnit_add_relatedPosts_html' , 800 , 1 );
else add_action( 'loop_end', 'vkExUnit_add_related_loopend', 800, 1 );


function vkExUnit_add_related_loopend( $query ){
	if( ! $query->is_main_query() ) return;
	echo vkExUnit_add_relatedPosts_html('');
}

function vkExUnit_get_relatedPosts( $post_type = 'post', $taxonomy = 'post_tag', $max_show_posts = 10 ){
	$posts_array = '';
	$post_id = get_the_id();

	$terms = get_the_terms( $post_id, $taxonomy );

	if ( ! $terms  || ! is_array( $terms ) ) { return $posts_array; }
	$tags = array();
	foreach ( $terms as $t ) { $tags[] = $t->term_id; }

	$args_base = array(
		'posts_per_page'   => $max_show_posts,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post__not_in'     => array( $post_id ),
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	$args = $args_base;

	$args['tax_query'] = array(array(
        'taxonomy' => $taxonomy,
        'field' => 'id',
        'terms' => $tags,
        'include_children' => false,
        'operator' => 'AND'
    ) );

	$posts_array = get_posts( $args );

	if ( !is_array( $posts_array ) ) { $posts_array = array(); }

	$post_shortage = $max_show_posts - count( $posts_array );
	if ( $post_shortage > 0 ) {
		$args = $args_base;
		$args['posts_per_page'] = $post_shortage;
		foreach ( $posts_array as $post ) {
			$args['post__not_in'][] = $post->ID;
		}
		$args['tax_query'] = array( array(
	        'taxonomy' => $taxonomy,
	        'field' => 'id',
	        'terms' => $tags,
	        'include_children' => false,
	        'operator' => 'IN'
	      ) );
		$singletags = get_posts( $args );
		if ( is_array( $singletags ) && count( $singletags ) ) { $posts_array = array_merge( $posts_array, $singletags ); }
	}

	$related_posts = $posts_array;
	return $related_posts;
}

function vkExUnit_add_relatedPosts_html( $content ) {

	if( ! is_single() ) return $content;

	global $is_pagewidget;
	if ( $is_pagewidget ) return $content;

	$related_post_types = apply_filters( 'veu_related_post_types', array( 'post' ) );
	if ( !in_array( get_post_type(), $related_post_types ) ) return $content;

	/*-------------------------------------------*/
	/*  Related posts
	/*-------------------------------------------*/
	$related_post_args = apply_filters( 'veu_related_post_args', array(
		'post_type' => 'post',
		'taxonomy' => 'post_tag',
		'max_show_posts' => 10
		) );
	$related_posts = vkExUnit_get_relatedPosts( $related_post_args['post_type'], $related_post_args['taxonomy'], $related_post_args['max_show_posts'] );

	if ( !$related_posts ) { return $content; }

	// $posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");
	if ( $related_posts ) {
		$relatedPostsHtml = '<!-- [ .relatedPosts ] -->';
		$relatedPostsHtml .= '<aside class="veu_relatedPosts veu_contentAddSection">';

		$output = get_option( 'vkExUnit_related_options');
    // テキストフィールドに値が入っていたら、表示させる。
		if ( ! empty( $output['related_title'] ) ) {
			$relatedPostTitle = $output['related_title'];
		} else {
		// 何も入っていなかったら既存のタイトルを表示させる。
			$relatedPostTitle = __( 'Related posts','vkExUnit' );
		}
		// 書き換え用フィルターフック（カスタマイザーで変更出来るが、既存ユーザーで使用しているかもしれないため削除不可）
		$relatedPostTitle = apply_filters( 'veu_related_post_title', $relatedPostTitle );
		$relatedPostsHtml .= '<h1 class="mainSection-title">'.$relatedPostTitle.'</h1>';

		$i = 1;
		$relatedPostsHtml .= '<div class="row">';
		foreach ( $related_posts as $key => $post ) {
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

// カスタマイザーの設定

if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_related' );
}

function veu_customize_register_related( $wp_customize ) {
  // セクション追加
	$wp_customize->add_section( 'veu_related_setting', array(
		'title'				=> __('Related Settings', 'vkExUnit'),
		'priority'			=> 1000,
		'panel'				=> 'veu_setting',
	) );
	// セッティング
	$wp_customize->add_setting( 'vkExUnit_related_options[related_title]', array(
		'default'			=> '',
		 'type'				=> 'option', // 保存先 option or theme_mod
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
  // コントロール
	$wp_customize->add_control( 'related_title', array(
		'label'		=> __( 'Title:', 'vkExUnit' ),
		'section'	=> 'veu_related_setting',
		'settings'  => 'vkExUnit_related_options[related_title]',
		'type'		=> 'text',
		'priority'	=> 1,
	) );
	/*-------------------------------------------*/
 /*	Add Edit Customize Link Btn
 /*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial( 'vkExUnit_related_options[related_title]', array(
		'selector' => '.veu_relatedPosts',
		'render_callback' => '',
	) );
}

/*

hook sample

add_filter('veu_related_post_types', 'veu_related_post_types_custom');
function veu_related_post_types_custom( $related_post_types ){
    $related_post_types[] = 'item';
    return $related_post_types;
}

add_filter('veu_related_post_args', 'veu_related_post_args_custom');
function veu_related_post_args_custom( $related_post_args ){
    if ( get_post_type() == 'item' ) {
        $related_post_args = array(
            'post_type' => 'item',
            'taxonomy' => 'item-category',
            'max_show_posts' => 10
        );
    }
    return $related_post_args;
}

*/
