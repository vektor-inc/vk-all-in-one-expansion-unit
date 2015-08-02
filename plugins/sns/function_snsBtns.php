<?php

	add_filter( 'the_content', 'vkExUnit_add_snsBtns');
 // is_single()

function vkExUnit_add_snsBtns($content){
	if (is_single() || is_page()) :
		if (is_home() || is_front_page()) {
			$linkUrl = home_url();
			$twitterUrl = home_url();
		} else if ( is_single() || is_archive() || ( is_page() && !is_front_page() ) ) {
			// $twitterUrl = home_url().'/?p='.get_the_ID();
			// URL is shortened it's id, but perm link because it does not count URL becomes separately
			$twitterUrl = get_permalink();
			$linkUrl = get_permalink();
		} else {
			$linkUrl = get_permalink();
		}
		$pageTitle = '';
		if(is_single() || is_page()){
			$pageTitle = get_post_meta(get_the_id(), 'vkExUnit_sns_title', true);
		}
		if(!$pageTitle){
			$pageTitle = urlencode(vkExUnit_get_wp_head_title());
		}
		$socialSet = '<div class="socialSet vkContentAddSection"><ul>';
		// facebook
		$socialSet .= '<li class="sb_facebook sb_icon"><a href="http://www.facebook.com/sharer.php?src=bm&u='.$linkUrl.'&amp;t='.$pageTitle.'" target="_blank"" ><span class="vk_icon_w_r_sns_fb icon_sns"></span><span class="sns_txt">Facebook</span></a></li>';
		// twitter
		$socialSet .= '<li class="sb_twitter sb_icon"><a href="http://twitter.com/intent/tweet?url='.$linkUrl.'&amp;text='.$pageTitle.'" target="_blank" onclick="javascript:" ><span class="vk_icon_w_r_sns_twitter icon_sns"></span><span class="sns_txt">twitter</span></a></li>';
		// hatena
		$socialSet .= '<li class="sb_hatena sb_icon"><a href="http://b.hatena.ne.jp/add?mode=confirm&url='.$linkUrl.'&amp;title='.$pageTitle.'" target="_blank" onclick="snsWindowOpen();"><span class="vk_icon_w_r_sns_hatena icon_sns"></span><span class="sns_txt">Hatena</span></a></li>';
		// line
		if ( wp_is_mobile() ) :
		$socialSet .= '<li class="sb_line sb_icon">
		<a href="line://msg/text/'.$pageTitle.' '.$linkUrl.'"><span class="vk_icon_w_r_sns_line icon_sns"></span><span class="sns_txt">LINE</span></a></li>';
		endif;
		// pocket
		$socialSet .= '<li class="sb_pocket"><?php /* do not delete span */?><span></span><a data-pocket-label="pocket" data-pocket-count="horizontal" class="pocket-btn" data-save-url="'.$linkUrl.'" data-lang="en"></a><script type="text/javascript">!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="https://widgets.getpocket.com/v1/j/btn.js?v=1";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pocket-btn-js");</script></li>';

		$socialSet .= '</ul></div><!-- [ /.socialSet ] -->';
		$content .= $socialSet;
	endif;
	return $content;
}