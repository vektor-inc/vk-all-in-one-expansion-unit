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

// function veu_sns_set_location_option( $opt ){
// 	if( ! veu_is_sns_btns_display() ) return $opt;
// 	$opt['sns_linkurl'] = veu_sns_get_url();
// 	return $opt;
// }

function veu_is_sns_btns_display() {
	global $post;
	$options     = veu_get_sns_options();
	$ignorePosts = explode( ',', $options['snsBtn_ignorePosts'] );
	$post_type   = vkExUnit_get_post_type();
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

/*-------------------------------------------*/
/*  SNSアイコンに出力するCSSを出力する関数
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


function veu_get_sns_btns() {

	$options   = veu_get_sns_options();
	$outer_css = veu_sns_outer_css( $options );
	$icon_css  = veu_sns_icon_css( $options );

	$linkUrl = urlencode( get_permalink() );

	$pageTitle = '';
	if ( is_single() || is_page() ) {
		$pageTitle = get_post_meta( get_the_id(), 'vkExUnit_sns_title', true );
	}
	if ( empty( $pageTitle ) ) {
		$pageTitle = urlencode( strip_tags( wp_title( '', false ) ) );
	}

	$socialSet = '<div class="veu_socialSet veu_contentAddSection"><script>window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));</script><ul>';
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
	if ( $post->sns_share_botton_hide ) {
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
		$content .= veu_get_sns_btns();
	} // if ( !isset( $options['snsBtn_ignorePosts'] ) || $options['snsBtn_ignorePosts'] != $post->ID ) {

	return $content;
}

add_action( 'wp_ajax_vkex_pocket_tunnel', 'veu_sns_pocket_tunnel' );
add_action( 'wp_ajax_nopriv_vkex_pocket_tunnel', 'veu_sns_pocket_tunnel' );
function veu_sns_pocket_tunnel() {
	ini_set( 'display_errors', 0 );
	$linkurl = urldecode( filter_input( INPUT_POST, 'linkurl' ) );
	if ( $s['host'] != $p['host'] ) {
		echo '0';
		die(); }
	$r = wp_safe_remote_get( 'https://widgets.getpocket.com/v1/button?label=pocket&count=vertical&v=1&url=' . $linkurl . '&title=title&src=' . $linkurl . '&r=' . rand( 1, 100 ) );
	if ( is_wp_error( $r ) ) {
		echo '0';
		die(); }
	echo $r['body'];
	die();
}
