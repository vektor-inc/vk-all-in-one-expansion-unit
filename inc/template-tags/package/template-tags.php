<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

/*
	Chack use post top page
	Chack post type info
	Archive title
	Page description
	vk_is_plugin_active
	Sanitize
	Post Type Check Box
	vk_is_checked
*/

if ( ! function_exists( 'vk_is_template_tags_exist' ) ) {
	function vk_is_template_tags_exist() {
		return true;
	}
}


if ( ! function_exists( 'vk_is_excerpt' ) ) {
	function vk_is_excerpt() {
		global $wp_current_filter;
		if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) {
			return true; }
		return false;
	}
}


/*
	Chack use post top page
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


/*
	Chack post type info
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_post_type' ) ) {
	function vk_get_post_type() {

		$postType = array();

		$url = $_SERVER['REQUEST_URI'];

		// 管理画面の投稿タイプ
		// ※ phpunitで is_admin()判定が効かない場合のため strpos( $url, 'wp-admin' ) を使用
		if ( is_admin() || strpos( $url, 'wp-admin' ) ) {
			global $post;
			$postType['slug'] = get_post_type( $post );
			if ( ! $postType['slug'] ) {
				if ( ! empty( $_GET['post_type'] ) ) {
					$postType['slug'] = $_GET['post_type'];
				} elseif ( ! empty( $_GET['post'] ) ) {
					$admin_post = get_post( $_GET['post'] );
					if ( ! empty( $admin_post->post_type ) ) {
						$postType['slug'] = $admin_post->post_type;
					}
				}
			}
			return $postType;
		}

		/*-------------------------------------------*/
		global $wp_query;
		$page_for_posts   = vk_get_page_for_posts();
		$postType['slug'] = get_post_type();
		if ( ! $postType['slug'] ) {

			if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {

				$postType['slug'] = $wp_query->query_vars['post_type'];

			} else {
				// Case of no post type query
				if ( ! empty( $wp_query->queried_object->taxonomy ) ) {
					// Case of tax archive and no posts
					$taxonomy         = $wp_query->queried_object->taxonomy;
					$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];

				} else {
					// Case of no tax query and no post type query and no posts
					$postType['slug'] = 'post';

				} // if ( ! empty( $wp_query->queried_object->taxonomy ) ) {
			}
		}

		// Get post type name
		/*-------------------------------------------*/
		$post_type_object = get_post_type_object( $postType['slug'] );
		if ( $post_type_object ) {
			$allowed_html = array(
				'span' => array( 'class' => array() ),
				'b'    => array(),
			);
			if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
				$postType['name'] = wp_kses( get_the_title( $page_for_posts['post_top_id'] ), $allowed_html );
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

/*
	Archive title
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_the_archive_title' ) ) {
	function vk_get_the_archive_title() {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = sprintf( __( 'Author: %s', 'vk-all-in-one-expansion-unit' ), '<span class="vcard">' . get_the_author() . '</span>' );
		} elseif ( is_year() ) {
			$title = get_the_date( _x( 'Y', 'yearly archives date format', 'vk-all-in-one-expansion-unit' ) );
		} elseif ( is_month() ) {
			$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'vk-all-in-one-expansion-unit' ) );
		} elseif ( is_day() ) {
			$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'vk-all-in-one-expansion-unit' ) );
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
				$title = __( 'Archives', 'vk-all-in-one-expansion-unit' );
			}
		}
		return apply_filters( 'vk_get_the_archive_title', $title );
	}
}


/*
	Page description
/*-------------------------------------------*/
if ( ! function_exists( 'vk_get_page_description' ) ) {
	function vk_get_page_description() {
		global $wp_query;
		$page_description = '';
		$post             = $wp_query->get_queried_object();
		if ( is_search() || is_404() ) {
			$page_description = '';
		} elseif ( is_front_page() ) {
			if ( isset( $post->post_excerpt ) && $post->post_excerpt && ! post_password_required( $post->ID ) ) {
				$page_description = get_the_excerpt( $post->ID );
			} else {
				$page_description = get_bloginfo( 'description' );
			}
		} elseif ( is_home() ) {
			$page_for_posts = vk_get_page_for_posts();
			if ( $page_for_posts['post_top_use'] ) {
				$page = get_post( $page_for_posts['post_top_id'] );
				if ( ! empty( $page->post_excerpt ) && ! post_password_required( $page->ID ) ) {
					$page_description = get_the_excerpt( $page->ID );
				} else {
					$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( $page_for_posts['post_top_name'] ) );
					$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
				}
			} else {
				$page_description = get_bloginfo( 'description' );
			}
		} elseif ( is_category() || is_tax() ) {
			if ( empty( $post->description ) ) {
				$page_description = sprintf( __( 'About %s', 'vk-all-in-one-expansion-unit' ), single_cat_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} else {
				$page_description = $post->description;
			}
		} elseif ( is_tag() ) {
			$page_description = strip_tags( tag_description() );
			$page_description = str_replace( array( "\r\n", "\r", "\n" ), '', $page_description );  // delete br
			if ( ! $page_description ) {
				$page_description = sprintf( __( 'About %s', 'vk-all-in-one-expansion-unit' ), single_tag_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			}
		} elseif ( is_archive() ) {
			if ( is_year() ) {
				$description_date  = get_the_date( _x( 'Y', 'yearly archives date format', 'vk-all-in-one-expansion-unit' ) );
				$page_description  = sprintf( _x( 'Article of %s.', 'Yearly archive description', 'vk-all-in-one-expansion-unit' ), $description_date );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} elseif ( is_month() ) {
				$description_date  = get_the_date( _x( 'F Y', 'monthly archives date format', 'vk-all-in-one-expansion-unit' ) );
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), $description_date );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} elseif ( is_author() ) {
				$userObj           = get_queried_object();
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( $userObj->display_name ) );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} else {
				$postType = get_post_type();
				if ( $postType ) {
					$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( get_post_type_object( $postType )->label ) );
					$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
				} else {
					$page_description = get_bloginfo( 'description' );
				}
			}
		} elseif ( is_page() || is_single() ) {
			if ( post_password_required( $post->ID ) ) {
				$page_description = __( 'This article is protected by a password.', 'vk-all-in-one-expansion-unit' );
			} elseif ( ! empty( $post->post_excerpt ) ) {
				$page_description = get_the_excerpt( $post->ID );
			} else {
				$page_description = get_the_content( null, false, $post->ID );
			}
		} else {
			$page_description = get_bloginfo( 'description' );
		}
		global $paged;
		if ( $paged != '0' ) {
			$page_description = '[' . sprintf( __( 'Page of %s', 'vk-all-in-one-expansion-unit' ), $paged ) . '] ' . $page_description;
		}
		// This filter (vkExUnit_pageDescriptionCustom) is deprecated.
		$page_description = apply_filters( 'vkExUnit_pageDescriptionCustom', $page_description );

		/*
		いままで
		* 画像ギャラリーなどのショートコードがそのまま表示される
		* ショートコードの中の引数の "" が入るとタグの終了がおかしくなりシェアやRSSで問題が出る
		という理由で do_shortcode で実行した後 html タグを除去していた
		$page_description = esc_html( strip_tags( do_shortcode( $page_description ) ) );

		しかし、ここで do_shortcode 入れるとWooCommerceなどのエラーメッセージが正常に表示されなくなる。
		なので、ショートコードの実行は行わないが、ショートコードの引き値としての " は不具合の原因となるので
		" esc_attr でエスケープを実施する
		本来ショートコードが出る場合は適切に抜粋欄に記入して運用でカバーする。
		動的ブロックの場合も同様とする。
		*/
		// この関数は get_the_ ではないので関数内では esc_attr() は行わない

		// 余計なスタイルタグ・スクリプトタグを除去
		$page_description = preg_replace( '/<(style|script).*?>(.|\r|\n)*?<\/(style|script)>/', '', $page_description );

		// HTML タグを除去
		$page_description = strip_tags( $page_description );

		// ショートコードを削除
		$page_description = strip_shortcodes( $page_description );

		if ( is_singular() ) {
			$page_description = mb_substr( $page_description, 0, 240 ); // kill tags and trim 240 chara
		}

		// Delete Line break
		$page_description = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $page_description );

		return apply_filters( 'vk_get_page_description', $page_description );
	}
}

/*
	vk_is_plugin_active
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

/*
	Sanitize
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

if ( ! function_exists( 'vk_sanitize_number' ) ) {
	function vk_sanitize_number( $input ) {
		$return = intval( mb_convert_kana( $input, 'n' ) );
		return esc_attr( $return );
	}
}
if ( ! function_exists( 'vk_sanitize_array' ) ) {
	function vk_sanitize_array( $input ) {
		if ( is_array( $input ) ) {
			$return = array();
			foreach ( $input as $key => $value ) {
				$return[ $key ] = wp_kses_post( $value );
			}
		}
		return $return;
	}
}

/*
	Post Type Check Box
/*-------------------------------------------*/
/**
 * 投稿タイプのチェックボックスを表示する関数
 *
 * @param  [type] $args 取得する投稿タイプ情報の判別や保存されてる値の情報など
 * @return [type]       [description]
 */
if ( ! function_exists( 'vk_the_post_type_check_list' ) ) {
	function vk_the_post_type_check_list( $args ) {
		$default    = array(
			'post_types_args'    => array(
				'public' => true,
			),
			'name'               => '',
			'checked'            => array( 'post' => true ),
			'id'                 => '',
			'exclude_post_types' => array( 'attachment' ),
		);
		$args       = wp_parse_args( $args, $default );
		$post_types = get_post_types( $args['post_types_args'], 'object' );

		echo '<ul class="no-style">';
		foreach ( $post_types as $key => $value ) {

			if ( ! in_array( $key, $args['exclude_post_types'] ) ) {

				$checked = ( isset( $args['checked'][ $key ] ) && ( $args['checked'][ $key ] ) ) ? ' checked' : '';

				if ( ! empty( $args['id'][ $key ] ) ) {
					$id = ' id="' . esc_attr( $args['id'][ $key ] ) . '"';
				} elseif ( ! empty( $args['name'][ $key ] ) ) {
					$id = ' id="' . esc_attr( $args['name'][ $key ] ) . '"';
				} else {
					$id = '';
				}

				echo '<li><label>';
				echo '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '[' . $key . ']"' . $id . ' value="true"' . $checked . ' />' . esc_html( $value->label );
				echo '</label></li>';
			}
		}
		echo '</ul>';
	}
}

/**
 * vk_the_post_type_check_list で保存される配列が、キーに投稿タイプ名が入る微妙な仕様のため、投稿タイプだけを配列で返すように変換
 *
 * @param  [type] $post_types : array( 'post' => 'true', 'info' => '' );
 * @return [type] $return : array( 'post' );
 */
if ( ! function_exists( 'vk_the_post_type_check_list_saved_array_convert' ) ) {
	function vk_the_post_type_check_list_saved_array_convert( $post_types ) {
		$return = array();
		if ( is_array( $post_types ) ) {
			foreach ( $post_types as $post_type => $value ) {
				if ( $value ) {
					$return[] = $post_type;
				}
			}
		}
		return $return;
	}
}

/*
	vk_is_checked
/*-------------------------------------------*/
/**
 * [vk_is_checked description]
 *
 * @param  string  $checked_value checkedにする場合の値
 * @param  string  $value 保存値
 * @return boolean        [description]
 */
if ( ! function_exists( 'vk_is_checked' ) ) {
	function vk_is_checked( $checked_value = '', $value = '' ) {
		$checked = '';
		if ( $checked_value == $value ) {
			$checked = ' checked';
		}
		echo $checked;
	}
}
