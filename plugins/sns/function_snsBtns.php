<?php
if( vkExUnit_content_filter_state() == 'content' ) add_filter( 'the_content', 'vkExUnit_add_snsBtns', 200, 1 );
else add_action( 'loop_end', 'vkExUnit_add_snsBtns_loopend' );


function vkExUnit_add_snsBtns_loopend( $query ){
	if( ! $query->is_main_query() ) return;
	if( is_front_page() || is_home() ) return;
	echo vkExUnit_add_snsBtns('');
}

function vkExUnit_sns_set_location_option( $opt ){
	if(!vkExUnit_is_snsBtns_display())return $opt;
	$opt['sns_linkurl'] = vkExUnit_sns_get_url();
	return $opt;
}


function vkExUnit_is_snsBtns_display(){
	global $post;
	$options = vkExUnit_get_sns_options();
	$ignorePosts = explode(",", $options['snsBtn_ignorePosts']);
	$post_type = vkExUnit_get_post_type();
	$post_type = $post_type['slug'];

	if ( isset( $options['snsBtn_exclude_post_types'][$post_type] ) && $options['snsBtn_exclude_post_types'][$post_type] ){
		return false;
	} else if ( !isset( $options['snsBtn_ignorePosts'] ) ){
		return true;
	} else if ( isset( $options['snsBtn_ignorePosts'] ) && $options['snsBtn_ignorePosts'] == $post->ID ) {
		return false;
	} else if ( is_array( $ignorePosts ) && in_array( $post->ID, $ignorePosts ) ){
		return false;
	} else {
		return true;
	}
}


function vkExUnit_add_snsBtns( $content ) {
	global $is_pagewidget;
	if ( $is_pagewidget ) { return $content; }
	if ( is_archive() ) { return $content; }

	$linkUrl = get_permalink();

	$pageTitle = '';
	if ( is_single() || is_page() ) {
		$pageTitle = get_post_meta( get_the_id(), 'vkExUnit_sns_title', true );
	}
	if ( empty( $pageTitle ) ) {
		$pageTitle = urlencode( strip_tags( wp_title( '', false ) ) );
	}

	if ( vkExUnit_is_snsBtns_display() ) {
		$socialSet = '<div class="veu_socialSet veu_contentAddSection"><script>window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));</script><ul>';
		// facebook
		$socialSet .= '<li class="sb_facebook sb_icon"><a href="//www.facebook.com/sharer.php?src=bm&u='.$linkUrl.'&amp;t='.$pageTitle.'" target="_blank" onclick="window.open(this.href,\'FBwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><span class="vk_icon_w_r_sns_fb icon_sns"></span><span class="sns_txt">Facebook</span><span class="veu_count_sns_fb"></span></a></li>';
		// twitter
		$socialSet .= '<li class="sb_twitter sb_icon"><a href="//twitter.com/intent/tweet?url='.$linkUrl.'&amp;text='.$pageTitle.'" target="_blank" onclick="window.open(this.href,\'Twitterwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;" ><span class="vk_icon_w_r_sns_twitter icon_sns"></span><span class="sns_txt">twitter</span></a></li>';
		// hatena
		$socialSet .= '<li class="sb_hatena sb_icon"><a href="//b.hatena.ne.jp/add?mode=confirm&url='.$linkUrl.'&amp;title='.$pageTitle.'" target="_blank" onclick="window.open(this.href,\'Hatenawindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><span class="vk_icon_w_r_sns_hatena icon_sns"></span><span class="sns_txt">Hatena</span><span class="veu_count_sns_hb"></span></a></li>';
		// line
		if ( wp_is_mobile() ) :
			$socialSet .= '<li class="sb_line sb_icon">
		<a href="line://msg/text/'.$pageTitle.' '.$linkUrl.'"><span class="vk_icon_w_r_sns_line icon_sns"></span><span class="sns_txt">LINE</span></a></li>';
		endif;
		// pocket

		$socialSet .= '<li class="sb_pocket sb_icon"><a href="//getpocket.com/edit?url='. $linkUrl .'&title=' . $pageTitle . '" target="_blank" onclick="window.open(this.href,\'Pokcetwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;"><i class="fa fa-get-pocket" aria-hidden="true"></i><span class="sns_txt">Pocket</span><span class="veu_count_sns_pocket"></span></a></li>';


		$socialSet .= '</ul></div><!-- [ /.socialSet ] -->';
		$content .= $socialSet;
	} // if ( !isset( $options['snsBtn_ignorePosts'] ) || $options['snsBtn_ignorePosts'] != $post->ID ) {

	return $content;
}

add_action('wp_ajax_vkex_pocket_tunnel', 'vkExUnit_sns_pocket_tunnel');
add_action('wp_ajax_nopriv_vkex_pocket_tunnel', 'vkExUnit_sns_pocket_tunnel');
function vkExUnit_sns_pocket_tunnel(){
	ini_set( 'display_errors', 0 );
	$linkurl = urldecode( filter_input( INPUT_POST, "linkurl" ) );
	if( $s["host"] != $p["host"] ){ echo "0"; die(); }
	$r = wp_safe_remote_get('https://widgets.getpocket.com/v1/button?label=pocket&count=vertical&v=1&url=' . $linkurl .'&title=title&src='.$linkurl.'&r='.rand(1,100));
	if( is_wp_error($r) ){ echo "0"; die(); }
	echo $r['body'];
	die();
}
