<?php

add_filter( 'the_content', 'vkExUnit_add_follow');

function vkExUnit_add_follow($content){
	$postType = vkExUnit_get_post_type();
	
	if ( is_single() && $postType['slug'] == 'post' ) :

		$options = vkExUnit_get_sns_options();
		if(!$options['enableFollowMe']){ return $content; }

		if (isset($options['followMe_title']) && $options['followMe_title'] ) {
			$title = $options['followMe_title'];
		} else {
		 	$title = __('Follow me','vkExUnit').'!'; 
		}

		// https://about.twitter.com/resources/buttons#follow
		$follow_html = '';

		$fbPageUrl = (isset($options['fbPageUrl'])) ? $options['fbPageUrl'] : '';
		$twitterId = (isset($options['twitterId'])) ? $options['twitterId'] : '';

		if ( $fbPageUrl || $twitterId ) {
			$follow_html .= '<div class="followSet vkContentAddSection">'."\n";
			$follow_html .= '<h3 class="followSet_title">'.$title.'</h3>';
		}

		if ( $fbPageUrl ) {
			$follow_html .= '<div class="follow_btn fb-like" data-href="'.esc_url( $options['fbPageUrl'] ).'" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>'."\n";	
		}

		if ( $twitterId ) {
			$follow_html .= '<div class="follow_btn follow_twitter"><a href="https://twitter.com/'.esc_html( $options['twitterId'] ).'" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@'.esc_html( $options['twitterId'] ).'</a></div>'."\n";	
		}

		if ( $fbPageUrl || $twitterId ) {
			$follow_html .= '</div><!-- [ /.followSet ] -->'."\n";
		}

		$content .= $follow_html;

	endif; // if ( $postType == 'post' && is_single() ) :
	return $content;
}