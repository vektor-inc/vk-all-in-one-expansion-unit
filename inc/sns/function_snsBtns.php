<?php

if ( veu_content_filter_state() == 'content' ) {
	add_filter( 'the_content', 'veu_add_sns_btns', 200, 1 );
} else {
	add_action( 'loop_end', 'veu_add_sns_btns_loopend' );
}

function veu_add_sns_btns_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	if ( is_front_page() || is_home() ) {
		return;
	}
	echo veu_add_sns_btns( '' );
}

function veu_is_sns_btns_display() {
	global $post;
	$options     = veu_get_sns_options();
	$ignorePosts = explode( ',', $options['snsBtn_ignorePosts'] );
	$post_type   = vk_get_post_type();
	$post_type   = $post_type['slug'];

	if ( isset( $options['snsBtn_exclude_post_types'][ $post_type ] ) && $options['snsBtn_exclude_post_types'][ $post_type ] ) {
		return false;
	} elseif ( ! isset( $options['snsBtn_ignorePosts'] ) ) {
		return true;
	} elseif ( isset( $options['snsBtn_ignorePosts'] ) && $options['snsBtn_ignorePosts'] == $post->ID ) {
		return false;
	} elseif ( is_array( $ignorePosts ) && in_array( $post->ID, $ignorePosts ) ) {
		return false;
	} else {
		return true;
	}
}

function veu_sns_is_sns_btns_meta_chekbox_hide( $post_type ) {
	// SNS設定のオプション値を取得
	$options = veu_get_sns_options();

	// 表示する にチェックが入っていない場合は 投稿詳細画面でボタン非表示のチェックボックスを表示しない
	if ( empty( $options['enableSnsBtns'] ) ) {
		return false;
	}

	// シェアボタンを表示しない投稿タイプが配列で指定されている場合（チェックが入ってたら）
	if ( isset( $options['snsBtn_exclude_post_types'] ) && is_array( $options['snsBtn_exclude_post_types'] ) ) {
		foreach ( $options['snsBtn_exclude_post_types'] as $key => $value ) {
			// 非表示チェックが入っている場合
			if ( $value ) {
				// 今の投稿タイプと比較。同じだったら...
				if ( $post_type == $key ) {
					return false;
				}
			}
		}
	}
	return true;
}

/*
  SNSアイコンに出力するCSSを出力する関数
/*-------------------------------------------*/

function veu_sns_outer_css( $options ) {
	// snsBtn_bg_fill_not が定義されている場合
	if ( isset( $options['snsBtn_bg_fill_not'] ) ) {
		$snsBtn_bg_fill_not = esc_html( $options['snsBtn_bg_fill_not'] ); // 中身が ''の場合もありえる
	} else {
		$snsBtn_bg_fill_not = '';
	}

	// snsBtn_color が定義されている場合
	if ( isset( $options['snsBtn_color'] ) ) {
		$snsBtn_color = esc_html( $options['snsBtn_color'] );
	} else {
		$snsBtn_color = '';
	}

	// 背景塗り && 色指定がない場合
	if ( ! $snsBtn_bg_fill_not && ! $snsBtn_color ) {
		// （ ExUnitのCSSファイルに書かれている色が適用されているので個別には出力しなくてよい ）
		$outer_css = '';

		// 背景なし枠線の場合
	} elseif ( $snsBtn_bg_fill_not == true ) {
		// 色指定がない場合
		if ( ! $snsBtn_color ) {
			$snsBtn_color = '#ccc';
		}
		$outer_css = ' style="border:1px solid ' . $snsBtn_color . ';background:none;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"';

		// それ以外（ 背景塗りの時 ）
	} else {
		$outer_css = ' style="border:1px solid ' . $snsBtn_color . ';background-color:' . $snsBtn_color . ';box-shadow: 0 2px 0 rgba(0,0,0,0.15)"';
	}
	return $outer_css;
}

function veu_sns_icon_css( $options ) {
	// snsBtn_bg_fill_not が定義されている場合
	if ( isset( $options['snsBtn_bg_fill_not'] ) ) {
		$snsBtn_bg_fill_not = esc_html( $options['snsBtn_bg_fill_not'] ); // 中身が ''の場合もありえる
	} else {
		$snsBtn_bg_fill_not = '';
	}

	// snsBtn_color が定義されている場合
	if ( isset( $options['snsBtn_color'] ) ) {
		$snsBtn_color = esc_html( $options['snsBtn_color'] );
	} else {
		$snsBtn_color = '';
	}

	if ( ! $snsBtn_bg_fill_not && ! $snsBtn_color ) {
		$snsBtn_color = '';
	} elseif ( $snsBtn_bg_fill_not == true ) {
		// 線のとき
		if ( ! $snsBtn_color ) {
			$snsBtn_color = '#ccc';
		}
		$snsBtn_color = ' style="color:' . $snsBtn_color . ';"';
	} else {
		// 塗りのとき
		$snsBtn_color = ' style="color:#fff;"';
	}
	return $snsBtn_color;
}

function veu_sns_block_callback( $attr ) {
	return veu_get_sns_btns( $attr );
}

function veu_get_sns_btns( $attr = array() ) {

	include dirname(dirname(__FILE__)) . '/vk-blocks/hidden-utils.php';

	$options   = veu_get_sns_options();
	$outer_css = veu_sns_outer_css( $options );
	$icon_css  = veu_sns_icon_css( $options );

	$linkUrl   = urlencode( get_permalink() );
	$pageTitle = urlencode( veu_get_the_sns_title() );

	$classes = '';
	if( function_exists('vk_add_hidden_class') ){
		$classes .= vk_add_hidden_class( $classes, $attr );
	}

	if( isset( $attr["position"] ) ){
		$classes .= ' veu_socialSet-position-' . $attr["position"];
	}
	if( isset( $attr["className"] ) ){
		$classes .= ' ' . $attr["className"];
	}

	$socialSet = '<div class="veu_socialSet' . esc_attr( $classes ) . ' veu_contentAddSection"><script>window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));</script><ul>';
	// facebook
	if ( $options['useFacebook'] ) {
		$socialSet .= '<li class="sb_facebook sb_icon"><a href="//www.facebook.com/sharer.php?src=bm&u=' . $linkUrl . '&amp;t=' . $pageTitle . '" target="_blank" ' . $outer_css . 'onclick="window.open(this.href,\'FBwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><span class="vk_icon_w_r_sns_fb icon_sns"' . $icon_css . '></span><span class="sns_txt"' . $icon_css . '>Facebook</span><span class="veu_count_sns_fb"' . $icon_css . '></span></a></li>';
	}

	// Twitter
	if ( $options['useTwitter'] ) {
		$socialSet .= '<li class="sb_twitter sb_icon"><a href="//twitter.com/intent/tweet?url=' . $linkUrl . '&amp;text=' . $pageTitle . '" target="_blank" ' . $outer_css . '><span class="vk_icon_w_r_sns_twitter icon_sns"' . $icon_css . '></span><span class="sns_txt"' . $icon_css . '>twitter</span></a></li>';
	}

	// hatena
	if ( $options['useHatena'] ) {
		$socialSet .= '<li class="sb_hatena sb_icon"><a href="//b.hatena.ne.jp/add?mode=confirm&url=' . $linkUrl . '&amp;title=' . $pageTitle . '" target="_blank" ' . $outer_css . ' onclick="window.open(this.href,\'Hatenawindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><span class="vk_icon_w_r_sns_hatena icon_sns"' . $icon_css . '></span><span class="sns_txt"' . $icon_css . '>Hatena</span><span class="veu_count_sns_hb"' . $icon_css . '></span></a></li>';
	}

	// line
	if ( wp_is_mobile() && $options['useLine'] ) :
		$socialSet .= '<li class="sb_line sb_icon">
	<a href="line://msg/text/' . $pageTitle . ' ' . $linkUrl . '" ' . $outer_css . '><span class="vk_icon_w_r_sns_line icon_sns"' . $icon_css . '></span><span class="sns_txt"' . $icon_css . '>LINE</span></a></li>';
	endif;
	// pocket
	if ( $options['usePocket'] ) {
		$socialSet .= '<li class="sb_pocket sb_icon"><a href="//getpocket.com/edit?url=' . $linkUrl . '&title=' . $pageTitle . '" target="_blank" ' . $outer_css . ' onclick="window.open(this.href,\'Pokcetwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><span class="vk_icon_w_r_sns_pocket icon_sns"' . $icon_css . '></span><span class="sns_txt"' . $icon_css . '>Pocket</span><span class="veu_count_sns_pocket"' . $icon_css . '></span></a></li>';
	}

	$socialSet .= '</ul></div><!-- [ /.socialSet ] -->';
	return $socialSet;
}

function veu_add_sns_btns( $content ) {

	// 個別の記事で ボタンを表示しない指定にしてある場合
	global $post;
	if ( isset( $post->sns_share_botton_hide ) && $post->sns_share_botton_hide ) {
		return $content;
	}

	// ウィジェットなら表示しない
	global $is_pagewidget;
	if ( $is_pagewidget ) {
		return $content; }

	// 抜粋でも表示しない
	if ( function_exists( 'vk_is_excerpt' ) ) {
		if ( vk_is_excerpt() ) {
			return $content; }
	}

	// アーカイブページでも表示しない
	if ( is_archive() ) {
		return $content; }

	if ( veu_is_sns_btns_display() ) {

		$options = veu_get_sns_options();

		if ( ! empty( $options['snsBtn_position']['before'] ) ) {
			$content = veu_get_sns_btns( array('position'  => 'before') ) . $content;
		}

		if ( ! empty( $options['snsBtn_position']['after'] ) ) {
			$content .= veu_get_sns_btns( array('position' => 'after') );
		}
	}

	return $content;
}

add_action( 'rest_api_init', function () {
	register_rest_route(
		'vk_ex_unit/v1',
		'/hatena_entry/(?P<linkurl>.+)',
		array(
			'methods' => 'GET',
			'callback' => 'vew_sns_hatena_restapi_callback',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'vk_ex_unit/v1',
		'/hatena_entry',
		array(
			'methods' => 'POST',
			'callback' => 'vew_sns_hatena_restapi_callback',
			'args' => array(
				'linkurl' => array(
					'description' => 'linkurl',
					'required' => true,
					'type' => 'string',
				)
			),
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'vk_ex_unit/v1',
		'/facebook_entry/(?P<linkurl>.+)',
		array(
			'methods' => 'GET',
			'callback' => 'vew_sns_facebook_restapi_callback',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'vk_ex_unit/v1',
		'/facebook_entry',
		array(
			'methods' => 'POST',
			'callback' => 'vew_sns_facebook_restapi_callback',
			'args' => array(
				'linkurl' => array(
					'description' => 'linkurl',
					'required' => true,
					'type' => 'string',
				)
			),
			'permission_callback' => '__return_true',
		)
	);
});

add_filter( 'vkExUnit_master_js_options', function( $options ){
	$opt = veu_get_sns_options();
	$options['hatena_entry'] = get_rest_url(0, 'vk_ex_unit/v1/hatena_entry/');
	$options['facebook_entry'] = get_rest_url(0, 'vk_ex_unit/v1/facebook_entry/');
	$options['facebook_count_enable'] = false;
	$options['entry_count'] = (bool) ($opt['entry_count'] != 'disable');
	$options['entry_from_post'] = (bool) ($opt['entry_count'] == 'post');

	$opt = veu_get_sns_options();
	if ( ! empty( $opt['fbAccessToken'] ) ) {
		$options['facebook_count_enable'] = true;
	}
	return $options;
}, 10, 1 );

function vew_sns_hatena_restapi_callback( $data ) {
	$linkurl = $data['linkurl'];
	$siteurl = get_site_url();

	if (strpos(preg_replace('/^https?:\/\//', '', $linkurl), preg_replace('/^https?:\/\//', '', $siteurl)) < 0) {
		$response = new WP_REST_Response(array());
		$response->set_status(403);
		return $response;
	}

	$r = wp_safe_remote_get('https://bookmark.hatenaapis.com/count/entry?url=' . $linkurl);

	if ( ! is_wp_error( $r ) ) {
		$response = new WP_REST_Response(array( 'count' => $r['body'] ) );
		if($data->get_method() == 'GET') {
			if ( empty($r['headers']['cache-control']) ) {
				$cache_control = 'Cache-Control: public, max-age=3600, s-maxage=3600';
			}else{
				$cache_control = $r['headers']['cache-control'];
			}
			$response->header( 'Cache-Control', $cache_control );
		} else {
			$response->header( 'Cache-Control', 'no-cache' );
		}
		$response->set_status(200);
		return $response;
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status(503);

	return $response;
}

function vew_sns_facebook_restapi_callback( $data ) {
	$linkurl = $data['linkurl'];
	$siteurl = get_site_url();

	if (strpos(preg_replace('/^https?:\/\//', '', $linkurl), preg_replace('/^https?:\/\//', '', $siteurl)) < 0) {
		$response = new WP_REST_Response(array());
		$response->set_status(403);
		return $response;
	}

	$options = veu_get_sns_options();
	if ( empty( $options['fbAccessToken'] ) ) {
		$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
		$response->set_status(503);
		return $response;
	}

	$r = wp_safe_remote_get('https://graph.facebook.com/?fields=engagement&access_token=' . $options['fbAccessToken'] . '&id=' . $linkurl);

	if ( ! is_wp_error( $r ) ) {
		$j = json_decode($r['body']);

		if( isset( $j->engagement->share_count ) ) {
			$response = new WP_REST_Response( array( 'count' => $j->engagement->share_count ) );
			if($data->get_method() == 'GET') {
				$response->header('Cache-Control', 'Cache-Control: public, max-age=3600, s-maxage=3600' );
			} else {
				$response->header( 'Cache-Control', 'no-cache' );
			}
			$response->set_status(200);
			return $response;
		}
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status(503);

	return $response;
}
