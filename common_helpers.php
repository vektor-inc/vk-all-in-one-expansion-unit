<?php
/*-------------------------------------------*/
/*  basic setting
/*-------------------------------------------*/
/*  Chack use post top page
/*-------------------------------------------*/
/*  Chack post type info
/*-------------------------------------------*/
/*  Head title
/*-------------------------------------------*/
/*  Page description
/*-------------------------------------------*/
/*  Archive title
/*-------------------------------------------*/

/*-------------------------------------------*/
/*  basic setting
/*-------------------------------------------*/
function vkExUnit_get_name() {
	$system_name = apply_filters( 'vkExUnit_get_name_custom','VK All in one Expansion Unit' );
	return $system_name;
}
function vkExUnit_get_little_short_name() {
	$little_short_name = apply_filters( 'vkExUnit_get_little_short_name_custom','VK ExUnit' );
	return $little_short_name;
}
function vkExUnit_get_short_name() {
	$short_name = apply_filters( 'vkExUnit_get_short_name_custom','VK' );
	return $short_name;
}
function vkExUnit_get_capability_required() {
	$capability_required = 'activate_plugins';
	return $capability_required;
}
function vkExUnit_get_systemlogo() {
	$logo = '<div class="logo_exUnit">';
	$logo .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', vkExUnit_get_directory_uri( '/images/head_logo_ExUnit.png' ) ) . '" alt="VK ExUnit" />';
	$logo .= '</div>';
	return $logo;
}

/*-------------------------------------------*/
/*  Chack use post top page
/*-------------------------------------------*/
function vkExUnit_get_page_for_posts() {
	// Get post top page by setting display page.
	$page_for_posts['post_top_id'] = get_option( 'page_for_posts' );

	// Set use post top page flag.
	$page_for_posts['post_top_use'] = ( isset( $page_for_posts['post_top_id'] ) && $page_for_posts['post_top_id'] ) ? true : false ;

	// When use post top page that get post top page name.
	$page_for_posts['post_top_name'] = ( $page_for_posts['post_top_use'] ) ? get_the_title( $page_for_posts['post_top_id'] ) : '';

	return $page_for_posts;
}

/*-------------------------------------------*/
/*  Chack post type info
/*-------------------------------------------*/
function vkExUnit_get_post_type() {

	$page_for_posts = vkExUnit_get_page_for_posts();

	// Get post type slug
	/*-------------------------------------------*/
	$postType['slug'] = get_post_type();
	if ( ! $postType['slug'] ) {
		global $wp_query;
		if ( $wp_query->query_vars['post_type'] ) {
			$postType['slug'] = $wp_query->query_vars['post_type'];
		} else {
			// Case of tax archive and no posts
			$taxonomy = get_queried_object()->taxonomy;
			$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];
		}
	}

	// Get post type name
	/*-------------------------------------------*/
	$post_type_object = get_post_type_object( $postType['slug'] );
	if ( $post_type_object ) {
		if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
			$postType['name'] = esc_html( get_the_title( $page_for_posts['post_top_id'] ) );
		} else {
			$postType['name'] = esc_html( $post_type_object->labels->name );
		}
	}

	// Get post type archive url
	/*-------------------------------------------*/
	if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
		$postType['url'] = get_the_permalink( $page_for_posts['post_top_id'] );
	} else {
		$postType['url'] = home_url().'/?post_type='.$postType['slug'];
	}

	$postType = apply_filters( 'vkExUnit_postType_custom',$postType );
	return $postType;
}
/*-------------------------------------------*/
/*  Archive title
/*-------------------------------------------*/

function vkExUnit_get_the_archive_title() {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'vkExUnit' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = get_the_date( _x( 'Y', 'yearly archives date format', 'vkExUnit' ) );
	} elseif ( is_month() ) {
		$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'vkExUnit' ) );
	} elseif ( is_day() ) {
		$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'vkExUnit' ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_home() && ! is_front_page() ) {
		$vkExUnit_page_for_posts = vkExUnit_get_page_for_posts();
		$title = $vkExUnit_page_for_posts['post_top_name'];
	} else {
		global $wp_query;
		// get post type
		$postType = $wp_query->query_vars['post_type'];
		if ( $postType ) {
			$title = get_post_type_object( $postType )->labels->name;
		} else {
			$title = __( 'Archives', 'vkExUnit' );
		}
	}

	return apply_filters( 'vkExUnit_get_the_archive_title', $title );
}

/*-------------------------------------------*/
/*  Head title
/*-------------------------------------------*/
function vkExUnit_get_wp_head_title() {
	global $wp_query;
	$post = $wp_query->get_queried_object();
	$sep = ' | ';
	$sep = apply_filters( 'vkExUnit_get_wp_head_title', $sep );

	if ( is_front_page() ) {
		$title = get_bloginfo( 'name' ).$sep.get_bloginfo( 'description' );
	} else if ( is_home() && ! is_front_page() ) {
		$title = vkExUnit_get_the_archive_title().$sep.get_bloginfo( 'name' );
	} else if ( is_archive() ) {
		$title = vkExUnit_get_the_archive_title().$sep.get_bloginfo( 'name' );
		// Page
	} else if ( is_page() ) {
		// Sub Pages
		if ( $post->post_parent ) {
			if ( $post->ancestors ) {
				foreach ( $post->ancestors as $post_anc_id ) {
					$post_id = $post_anc_id;
				}
			} else {
				$post_id = $post->ID;
			}
			$title = get_the_title().$sep.get_the_title( $post_id ).$sep.get_bloginfo( 'name' );
			// Not Sub Pages
		} else {
			$title = get_the_title().$sep.get_bloginfo( 'name' );
		}
	} else if ( is_single() || is_attachment() ) {
		$title = get_the_title().$sep.get_bloginfo( 'name' );

		// Search
	} else if ( is_search() ) {
		$title = sprintf( __( 'Search Results for : %s', 'vkExUnit' ),get_search_query() ).$sep.get_bloginfo( 'name' );
		// 404
	} else if ( is_404() ) {
		$title = __( 'Not found', 'vkExUnit' ).$sep.get_bloginfo( 'name' );
		// Other
	} else {
		$title = get_bloginfo( 'name' );
	}

	// Add Page numner.
	global $paged;
	if ( $paged >= 2 ) {
		$title = '['.sprintf( __( 'Page of %s', 'vkExUnit' ),$paged ).'] '.$title;
	}

	$title = apply_filters( 'vkExUnit_get_wp_head_title', $title );

	// Remove Tags(ex:<i>) & return
	return strip_tags( $title );
}


/*-------------------------------------------*/
/*  Page description
/*-------------------------------------------*/
function vkExUnit_get_pageDescription() {
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if ( is_front_page() ) {
		if ( isset( $post->post_excerpt ) && $post->post_excerpt ) {
			$pageDescription = get_the_excerpt();
		} else {
			$pageDescription = get_bloginfo( 'description' );
		}
	} else if ( is_home() ) {
		$page_for_posts = vkExUnit_get_page_for_posts();
		if ( $page_for_posts['post_top_use'] ) {
			$page = get_post( $page_for_posts['post_top_id'] );
			$pageDescription = $page->post_excerpt;
		} else {
			$pageDescription = get_bloginfo( 'description' );
		}
	} else if ( is_category() || is_tax() ) {
		if ( ! $post->description ) {
			$pageDescription = sprintf( __( 'About %s', 'vkExUnit' ),single_cat_title( '',false ) ).' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		} else {
			$pageDescription = $post->description;
		}
	} else if ( is_tag() ) {
		$pageDescription = strip_tags( tag_description() );
		$pageDescription = str_replace( array( "\r\n", "\r", "\n" ), '', $pageDescription );  // delete br
		if ( ! $pageDescription ) {
			$pageDescription = sprintf( __( 'About %s', 'vkExUnit' ),single_tag_title( '',false ) ).' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		}
	} else if ( is_archive() ) {
		if ( is_year() ) {
			$description_date = get_the_date( _x( 'Y', 'yearly archives date format', 'vkExUnit' ) );
			$pageDescription = sprintf( _x( 'Article of %s.','Yearly archive description', 'vkExUnit' ), $description_date );
			$pageDescription .= ' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		} else if ( is_month() ) {
			$description_date = get_the_date( _x( 'F Y', 'monthly archives date format', 'vkExUnit' ) );
			$pageDescription = sprintf( _x( 'Article of %s.','Archive description', 'vkExUnit' ),$description_date );
			$pageDescription .= ' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		} else if ( is_author() ) {
			$userObj = get_queried_object();
			$pageDescription = sprintf( _x( 'Article of %s.','Archive description', 'vkExUnit' ),esc_html( $userObj->display_name ) );
			$pageDescription .= ' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		} else {
			$postType = get_post_type();
			$pageDescription = sprintf( _x( 'Article of %s.','Archive description', 'vkExUnit' ),esc_html( get_post_type_object( $postType )->labels->name ) );
			$pageDescription .= ' '.get_bloginfo( 'name' ).' '.get_bloginfo( 'description' );
		}
	} else if ( is_page() || is_single() ) {
		$metaExcerpt = $post->post_excerpt;
		if ( $metaExcerpt ) {
			$pageDescription = $metaExcerpt;
		} else {
			$pageDescription = mb_substr( strip_tags( $post->post_content ), 0, 240 ); // kill tags and trim 240 chara
		}
	} else {
		$pageDescription = get_bloginfo( 'description' );
	}
	global $paged;
	if ( $paged != '0' ) {
		$pageDescription = '['.sprintf( __( 'Page of %s', 'vkExUnit' ),$paged ).'] '.$pageDescription;
	}
	$pageDescription = apply_filters( 'vkExUnit_pageDescriptionCustom', $pageDescription );
	$pageDescription = esc_html( strip_tags( $pageDescription ) );
	// Delete Line break
	$pageDescription = str_replace( array( "\r\n", "\r", "\n" ), '', $pageDescription );
	return $pageDescription;
}

function vkExUnit_is_excerpt() {
	global $wp_current_filter;
	if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) { return true; }
	return false;
}
