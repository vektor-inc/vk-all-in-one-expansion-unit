<?php
/**
 * Share button
 *
 * @package vk-all-in-one-expantion-unit
 */

if ( 'content' === veu_content_filter_state() ) {
	add_filter( 'the_content', 'veu_add_sns_btns', 200, 1 );
} else {
	add_action( 'loop_end', 'veu_add_sns_btns_loopend' );
}

/**
 * Display share button on loop end
 *
 * @param object $query : main query.
 * @return void
 */
function veu_add_sns_btns_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	if ( is_front_page() || is_home() || is_404() ) {
		return;
	}
	echo veu_add_sns_btns( '' );
}

/**
 * Check sns btn display
 *
 * @return bool
 */
function veu_is_sns_btns_display() {
	global $post;
	$options      = veu_get_sns_options();
	$ignore_posts = explode( ',', $options['snsBtn_ignorePosts'] );
	$post_type    = vk_get_post_type();
	$post_type    = $post_type['slug'];
	// 404ページの内容を G3 ProUnit で指定の記事本文に書き換えた場合に表示されないように
	if ( is_404() ){
		return false;
	}
	if ( empty( $options['enableSnsBtns'] ) ) {
		return false;
	}
	if ( isset( $options['snsBtn_exclude_post_types'][ $post_type ] ) && $options['snsBtn_exclude_post_types'][ $post_type ] ) {
		return false;
	} elseif ( ! isset( $options['snsBtn_ignorePosts'] ) ) {
		return true;
	} elseif ( isset( $options['snsBtn_ignorePosts'] ) && $options['snsBtn_ignorePosts'] === $post->ID ) {
		return false;
	} elseif ( is_array( $ignore_posts ) && in_array( $post->ID, $ignore_posts, true ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * メイン設定で非表示に指定されているかどうか
 *
 * @param string $post_type : 投稿タイプスラッグ.
 * @return bool
 */
function veu_sns_is_sns_btns_meta_chekbox_hide( $post_type ) {
	// SNS設定のオプション値を取得.
	$options = veu_get_sns_options();

	// 表示する にチェックが入っていない場合は 投稿詳細画面でボタン非表示のチェックボックスを表示しない.
	if ( empty( $options['enableSnsBtns'] ) ) {
		return false;
	}

	// シェアボタンを表示しない投稿タイプが配列で指定されている場合（チェックが入ってたら）.
	if ( isset( $options['snsBtn_exclude_post_types'] ) && is_array( $options['snsBtn_exclude_post_types'] ) ) {
		foreach ( $options['snsBtn_exclude_post_types'] as $key => $value ) {
			// 非表示チェックが入っている場合.
			if ( $value ) {
				// 今の投稿タイプと比較。同じだったら...
				if ( $post_type === $key ) {
					return false;
				}
			}
		}
	}
	return true;
}

/**
 * シェアボタンのCSS
 *
 * @param array $options : オプション値.
 * @return string $outer_css : style
 */
function veu_sns_outer_css( $options ) {

	// snsBtn_bg_fill_not が定義されている場合.
	$sns_btn_bg_fill_not = false;
	if ( ! empty( $options['snsBtn_bg_fill_not'] ) ) {
		$sns_btn_bg_fill_not = true;
	}

	// snsBtn_color が定義されている場合.
	if ( isset( $options['snsBtn_color'] ) ) {
		$sns_btn_color = esc_html( $options['snsBtn_color'] );
	} else {
		$sns_btn_color = '';
	}

	// 背景塗り && 色指定がない場合.
	if ( ! $sns_btn_bg_fill_not && ! $sns_btn_color ) {
		// （ ExUnitのCSSファイルに書かれている色が適用されているので個別には出力しなくてよい ）
		$outer_css = '';

		// 背景なし枠線の場合.
	} elseif ( $sns_btn_bg_fill_not ) {
		// 色指定がない場合.
		if ( ! $sns_btn_color ) {
			$sns_btn_color = '#ccc';
		}
		$outer_css = ' style="border:1px solid ' . $sns_btn_color . ';background:none;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"';

		// それ以外（ 背景塗りの時 ）.
	} else {
		$outer_css = ' style="border:1px solid ' . $sns_btn_color . ';background-color:' . $sns_btn_color . ';box-shadow: 0 2px 0 rgba(0,0,0,0.15);"';
	}
	return $outer_css;
}

/**
 * シェアボタンのアイコンと文字部分のCSS
 *
 * @param array $options : オプション値.
 * @return string $style : style
 */
function veu_sns_icon_css( $options ) {
	// snsBtn_bg_fill_not が定義されている場合.
	$sns_btn_bg_fill_not = '';
	if ( ! empty( $options['snsBtn_bg_fill_not'] ) ) {
		$sns_btn_bg_fill_not = true;
	}

	// snsBtn_color が定義されている場合.
	if ( isset( $options['snsBtn_color'] ) ) {
		$style = esc_html( $options['snsBtn_color'] );
	} else {
		$style = '';
	}

	if ( ! $sns_btn_bg_fill_not && ! $style ) {
		$style = '';
	} elseif ( $sns_btn_bg_fill_not ) {
		// 線のとき.
		if ( ! $style ) {
			$style = '#ccc';
		}
		$style = ' style="color:' . $style . ';"';
	} else {
		// 塗りのとき.
		$style = ' style="color:#fff;"';
	}
	return $style;
}

/**
 * Render Block
 *
 * @param array $attr : btn parametors.
 * @return string : sns dom.
 */
function veu_sns_block_callback( $attr ) {
	return veu_get_sns_btns( $attr );
}

/**
 * Share button html
 *
 * @param array $attr : class / position and so on.
 * @return string Button DOM
 */
function veu_get_sns_btns( $attr = array() ) {

	include dirname( dirname( __FILE__ ) ) . '/vk-blocks/hidden-utils.php';

	$options   = veu_get_sns_options();
	$outer_css = veu_sns_outer_css( $options );
	$icon_css  = veu_sns_icon_css( $options );

	$link_url   = rawurlencode( get_permalink() );
	$page_title = rawurlencode( veu_get_the_sns_title() );

	$classes = '';
	if ( function_exists( 'vk_add_hidden_class' ) ) {
		$classes .= vk_add_hidden_class( $classes, $attr );
	}

	if ( isset( $attr['position'] ) ) {
		$classes .= ' veu_socialSet-position-' . $attr['position'];
	}
	if ( isset( $attr['className'] ) ) {
		$classes .= ' ' . $attr['className'];
	}

	$social_btns = '<div class="veu_socialSet' . esc_attr( $classes ) . ' veu_contentAddSection"><script>window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));</script><ul>';
	// facebook.
	if ( ! empty( $options['useFacebook'] ) ) {
		$social_btns .= '<li class="sb_facebook sb_icon">';
		$social_btns .= '<a class="sb_icon_inner" href="//www.facebook.com/sharer.php?src=bm&u=' . $link_url . '&amp;t=' . $page_title . '" target="_blank" ' . $outer_css . 'onclick="window.open(this.href,\'FBwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;">';
		$social_btns .= '<span class="vk_icon_w_r_sns_fb icon_sns"' . $icon_css . '></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>Facebook</span>';
		$social_btns .= '<span class="veu_count_sns_fb"' . $icon_css . '></span>';
		$social_btns .= '</a>';
		$social_btns .= '</li>';
	}

	// Twitter.
	if ( ! empty( $options['useTwitter'] ) ) {
		$social_btns .= '<li class="sb_twitter sb_icon">';
		$social_btns .= '<a class="sb_icon_inner" href="//twitter.com/intent/tweet?url=' . $link_url . '&amp;text=' . $page_title . '" target="_blank" ' . $outer_css . '>';
		$social_btns .= '<span class="vk_icon_w_r_sns_twitter icon_sns"' . $icon_css . '></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>twitter</span>';
		$social_btns .= '</a>';
		$social_btns .= '</li>';
	}

	// hatena.
	if ( ! empty( $options['useHatena'] ) ) {
		$social_btns .= '<li class="sb_hatena sb_icon">';
		$social_btns .= '<a class="sb_icon_inner" href="//b.hatena.ne.jp/add?mode=confirm&url=' . $link_url . '&amp;title=' . $page_title . '" target="_blank" ' . $outer_css . ' onclick="window.open(this.href,\'Hatenawindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;">';
		$social_btns .= '<span class="vk_icon_w_r_sns_hatena icon_sns"' . $icon_css . '></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>Hatena</span>';
		$social_btns .= '<span class="veu_count_sns_hb"' . $icon_css . '></span>';
		$social_btns .= '</a>';
		$social_btns .= '</li>';
	}

	// line.
	if ( wp_is_mobile() && ! empty( $options['useLine'] ) ) :
		$social_btns .= '<li class="sb_line sb_icon">';
		$social_btns .= '<a class="sb_icon_inner"  href="line://msg/text/' . $page_title . ' ' . $link_url . '" ' . $outer_css . '>';
		$social_btns .= '<span class="vk_icon_w_r_sns_line icon_sns"' . $icon_css . '></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>LINE</span>';
		$social_btns .= '</a>';
		$social_btns .= '</li>';
	endif;
	// pocket.
	if ( $options['usePocket'] ) {
		$social_btns .= '<li class="sb_pocket sb_icon">';
		$social_btns .= '<a class="sb_icon_inner"  href="//getpocket.com/edit?url=' . $link_url . '&title=' . $page_title . '" target="_blank" ' . $outer_css . ' onclick="window.open(this.href,\'Pokcetwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;">';
		$social_btns .= '<span class="vk_icon_w_r_sns_pocket icon_sns"' . $icon_css . '></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>Pocket</span>';
		$social_btns .= '<span class="veu_count_sns_pocket"' . $icon_css . '></span>';
		$social_btns .= '</a>';
		$social_btns .= '</li>';
	}
	// copy.
	if ( ! empty( $options['useCopy'] ) ) {
		$social_btns .= '<li class="sb_copy sb_icon">';
		$social_btns .= '<button class="copy-button sb_icon_inner"' . $outer_css . 'data-clipboard-text="' . urldecode( $page_title ) . ' ' . urldecode( $link_url ) . '">';
		$social_btns .= '<span class="vk_icon_w_r_sns_copy icon_sns"' . $icon_css . '><i class="fas fa-copy"></i></span>';
		$social_btns .= '<span class="sns_txt"' . $icon_css . '>Copy</span>';
		$social_btns .= '</button>';
		$social_btns .= '</li>';
	}

	$social_btns .= '</ul></div><!-- [ /.socialSet ] -->';
	return $social_btns;
}

/**
 * Add sns btn to $content
 *
 * @param string $content : post content.
 * @return string $content add sns btns
 */
function veu_add_sns_btns( $content ) {

	// 個別の記事で ボタンを表示しない指定にしてある場合.
	global $post;
	if ( isset( $post->sns_share_botton_hide ) && $post->sns_share_botton_hide ) {
		return $content;
	}

	// ウィジェットなら表示しない.
	global $is_pagewidget;
	if ( $is_pagewidget ) {
		return $content; }

	// 抜粋でも表示しない.
	if ( function_exists( 'vk_is_excerpt' ) ) {
		if ( vk_is_excerpt() ) {
			return $content; }
	}

	// アーカイブページでも表示しない.
	if ( is_archive() ) {
		return $content; }

	if ( veu_is_sns_btns_display() ) {

		$options = veu_get_sns_options();

		if ( ! empty( $options['snsBtn_position']['before'] ) ) {
			$content = veu_get_sns_btns( array( 'position' => 'before' ) ) . $content;
		}

		if ( ! empty( $options['snsBtn_position']['after'] ) ) {
			$content .= veu_get_sns_btns( array( 'position' => 'after' ) );
		}
	}

	return $content;
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'vk_ex_unit/v1',
			'/hatena_entry/(?P<linkurl>.+)',
			array(
				'methods'             => 'GET',
				'callback'            => 'vew_sns_hatena_restapi_callback',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/hatena_entry',
			array(
				'methods'             => 'POST',
				'callback'            => 'vew_sns_hatena_restapi_callback',
				'args'                => array(
					'linkurl' => array(
						'description' => 'linkurl',
						'required'    => true,
						'type'        => 'string',
					),
				),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/facebook_entry/(?P<linkurl>.+)',
			array(
				'methods'             => 'GET',
				'callback'            => 'vew_sns_facebook_restapi_callback',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/facebook_entry',
			array(
				'methods'             => 'POST',
				'callback'            => 'vew_sns_facebook_restapi_callback',
				'args'                => array(
					'linkurl' => array(
						'description' => 'linkurl',
						'required'    => true,
						'type'        => 'string',
					),
				),
				'permission_callback' => '__return_true',
			)
		);
	}
);

add_filter(
	'vkExUnit_master_js_options',
	function( $options ) {
		$opt                              = veu_get_sns_options();
		$options['hatena_entry']          = get_rest_url( 0, 'vk_ex_unit/v1/hatena_entry/' );
		$options['facebook_entry']        = get_rest_url( 0, 'vk_ex_unit/v1/facebook_entry/' );
		$options['facebook_count_enable'] = false;
		$options['entry_count']           = (bool) ( 'disable' !== $opt['entry_count'] );
		$options['entry_from_post']       = (bool) ( 'post' !== $opt['entry_count'] );

		$opt = veu_get_sns_options();
		if ( ! empty( $opt['fbAccessToken'] ) ) {
			$options['facebook_count_enable'] = true;
		}
		return $options;
	},
	10,
	1
);

/**
 * Hatena count
 *
 * @param string $data : Setting parametor ( url and so on ).
 * @return string api response
 */
function vew_sns_hatena_restapi_callback( $data ) {
	$link_url = $data['linkurl'];
	$siteurl  = get_site_url();

	if ( strpos( preg_replace( '/^https?:\/\//', '', $link_url ), preg_replace( '/^https?:\/\//', '', $siteurl ) ) < 0 ) {
		$response = new WP_REST_Response( array() );
		$response->set_status( 403 );
		return $response;
	}

	$r = wp_safe_remote_get( 'https://bookmark.hatenaapis.com/count/entry?url=' . $link_url );

	if ( ! is_wp_error( $r ) ) {
		$response = new WP_REST_Response( array( 'count' => $r['body'] ) );
		if ( 'GET' === $data->get_method() ) {
			if ( empty( $r['headers']['cache-control'] ) ) {
				$cache_control = 'Cache-Control: public, max-age=3600, s-maxage=3600';
			} else {
				$cache_control = $r['headers']['cache-control'];
			}
			$response->header( 'Cache-Control', $cache_control );
		} else {
			$response->header( 'Cache-Control', 'no-cache' );
		}
		$response->set_status( 200 );
		return $response;
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status( 503 );

	return $response;
}

/**
 * Facebook count
 *
 * @param string $data : Setting parametor ( url and so on ).
 * @return string api response
 */
function vew_sns_facebook_restapi_callback( $data ) {
	$link_url = $data['linkurl'];
	$siteurl  = get_site_url();

	if ( strpos( preg_replace( '/^https?:\/\//', '', $link_url ), preg_replace( '/^https?:\/\//', '', $siteurl ) ) < 0 ) {
		$response = new WP_REST_Response( array() );
		$response->set_status( 403 );
		return $response;
	}

	$options = veu_get_sns_options();
	if ( empty( $options['fbAccessToken'] ) ) {
		$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
		$response->set_status( 503 );
		return $response;
	}

	$r = wp_safe_remote_get( 'https://graph.facebook.com/?fields=engagement&access_token=' . $options['fbAccessToken'] . '&id=' . $link_url );

	if ( ! is_wp_error( $r ) ) {
		$j = json_decode( $r['body'] );

		if ( isset( $j->engagement->share_count ) ) {
			$response = new WP_REST_Response( array( 'count' => $j->engagement->share_count ) );
			if ( 'GET' === $data->get_method() ) {
				$response->header( 'Cache-Control', 'Cache-Control: public, max-age=3600, s-maxage=3600' );
			} else {
				$response->header( 'Cache-Control', 'no-cache' );
			}
			$response->set_status( 200 );
			return $response;
		}
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status( 503 );

	return $response;
}
