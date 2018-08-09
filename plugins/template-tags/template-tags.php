<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/


if ( ! function_exists( 'vk_is_excerpt' ) ) {
	function vk_is_excerpt() {
		global $wp_current_filter;
		if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) {
			return true; }
		return false;
	}
}


/*-------------------------------------------*/
/*  Chack use post top page
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_page_for_posts' ) ) {
	function vk_get_page_for_posts() {
		// Get post top page by setting display page.
		$page_for_posts['post_top_id'] = get_option( 'page_for_posts' );

		// Set use post top page flag.
		$page_for_posts['post_top_use'] = ( isset( $page_for_posts['post_top_id'] ) && $page_for_posts['post_top_id'] ) ? true : false;

		// When use post top page that get post top page name.
		$page_for_posts['post_top_name'] = ( $page_for_posts['post_top_use'] ) ? get_the_title( $page_for_posts['post_top_id'] ) : '';

		return $page_for_posts;
	}
}


/*-------------------------------------------*/
/*  Chack post type info
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_post_type' ) ) {
	function vk_get_post_type() {

		$page_for_posts = vk_get_page_for_posts();

		// Get post type slug
		/*-------------------------------------------*/
		$postType['slug'] = get_post_type();
		if ( ! $postType['slug'] ) {
			global $wp_query;
			if ( $wp_query->query_vars['post_type'] ) {
				$postType['slug'] = $wp_query->query_vars['post_type'];
			} else {
				// Case of tax archive and no posts
				$taxonomy         = get_queried_object()->taxonomy;
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
			$postType['url'] = get_post_type_archive_link( $postType['slug'] );
		}

		$postType = apply_filters( 'vkExUnit_postType_custom', $postType );
		return $postType;
	}
}

/*-------------------------------------------*/
/*  Archive title
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_the_archive_title' ) ) {
	function vk_get_the_archive_title() {
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
			$title                   = $vkExUnit_page_for_posts['post_top_name'];
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
		return apply_filters( 'vk_get_the_archive_title', $title );
	}
}


/*-------------------------------------------*/
/*  Page description
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_page_description' ) ) {
	function vk_get_page_description() {
		global $wp_query;
		$post = $wp_query->get_queried_object();
		if ( is_front_page() ) {
			if ( isset( $post->post_excerpt ) && $post->post_excerpt ) {
				$page_description = get_the_excerpt();
			} else {
				$page_description = get_bloginfo( 'description' );
			}
		} elseif ( is_home() ) {
			$page_for_posts = vkExUnit_get_page_for_posts();
			if ( $page_for_posts['post_top_use'] ) {
				$page             = get_post( $page_for_posts['post_top_id'] );
				$page_description = $page->post_excerpt;
			} else {
				$page_description = get_bloginfo( 'description' );
			}
		} elseif ( is_category() || is_tax() ) {
			if ( ! $post->description ) {
				$page_description = sprintf( __( 'About %s', 'vkExUnit' ), single_cat_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} else {
				$page_description = $post->description;
			}
		} elseif ( is_tag() ) {
			$page_description = strip_tags( tag_description() );
			$page_description = str_replace( array( "\r\n", "\r", "\n" ), '', $page_description );  // delete br
			if ( ! $page_description ) {
				$page_description = sprintf( __( 'About %s', 'vkExUnit' ), single_tag_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			}
		} elseif ( is_archive() ) {
			if ( is_year() ) {
				$description_date  = get_the_date( _x( 'Y', 'yearly archives date format', 'vkExUnit' ) );
				$page_description  = sprintf( _x( 'Article of %s.', 'Yearly archive description', 'vkExUnit' ), $description_date );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} elseif ( is_month() ) {
				$description_date  = get_the_date( _x( 'F Y', 'monthly archives date format', 'vkExUnit' ) );
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vkExUnit' ), $description_date );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} elseif ( is_author() ) {
				$userObj           = get_queried_object();
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vkExUnit' ), esc_html( $userObj->display_name ) );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} else {
				$postType = get_post_type();
				if ( $postType ) {
					$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vkExUnit' ), esc_html( get_post_type_object( $postType )->label ) );
					$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
				} else {
					$page_description = get_bloginfo( 'description' );
				}
			}
		} elseif ( is_page() || is_single() ) {
			$metaExcerpt = $post->post_excerpt;
			if ( $metaExcerpt ) {
				$page_description = $metaExcerpt;
			} else {
				$page_description = mb_substr( strip_tags( $post->post_content ), 0, 240 ); // kill tags and trim 240 chara
			}
		} else {
			$page_description = get_bloginfo( 'description' );
		}
		global $paged;
		if ( $paged != '0' ) {
			$page_description = '[' . sprintf( __( 'Page of %s', 'vkExUnit' ), $paged ) . '] ' . $page_description;
		}
		$page_description = apply_filters( 'vkExUnit_pageDescriptionCustom', $page_description );
		$page_description = esc_html( strip_tags( do_shortcode( $page_description ) ) );
		// Delete Line break
		$page_description = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $page_description );
		$page_description = preg_replace( '/\[(.*?)\]/m', '', $page_description );
		return $page_description;
	}
}

/*-------------------------------------------*/
/*  vk_is_plugin_active
/*-------------------------------------------*/
if ( ! function_exists( 'vk_is_plugin_active' ) ) {
	function vk_is_plugin_active( $plugin_path = '' ) {
		if ( function_exists( 'is_plugin_active' ) ) {
				return is_plugin_active( $plugin_path );
		} else {
				return in_array(
					$plugin_path,
					get_option( 'active_plugins' )
				);
		}
	}
}

/*-------------------------------------------*/
/*  Sanitize
/*-------------------------------------------*/
if ( ! function_exists( 'veu_sanitize_boolean' ) ) {
	function veu_sanitize_boolean( $input ) {
		if ( $input == true ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'veu_sanitize_radio' ) ) {
	function veu_sanitize_radio( $input ) {
		return esc_attr( $input );
	}
}
