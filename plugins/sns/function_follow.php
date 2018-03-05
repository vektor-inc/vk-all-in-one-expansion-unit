<?php

add_filter( 'the_content', 'vkExUnit_add_follow' );

function vkExUnit_add_follow( $content ) {
	$postType = vkExUnit_get_post_type();

	if ( is_single() && $postType['slug'] == 'post' ) :

		$options = veu_get_sns_options();
		if ( ! $options['enableFollowMe'] ) {  return $content; }

		if ( isset( $options['followMe_title'] ) && $options['followMe_title'] ) {
			$title = $options['followMe_title'];
		} else {
		 	$title = __( 'Follow me','vkExUnit' ).'!';
		}

		// https://about.twitter.com/resources/buttons#follow
		$follow_html = '';

		$fbPageUrl = (isset( $options['fbPageUrl'] )) ? $options['fbPageUrl'] : '';
		$twitterId = (isset( $options['twitterId'] )) ? $options['twitterId'] : '';


/*----------------------------------------------*/
		/* 記事がよかったらいいね　ここから
/*----------------------------------------------*/

		$image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id, true);
		if ( has_post_thumbnail() ):
			$follow_thumbnail = $image_url[0];
			$follow_thumbnail = '<div class="p-entry__pushThumb" style="background-image: url(\''.$follow_thumbnail.'\')"></div>';
		else:
			$follow_thumbnail = '';
		endif;
		$follow_html .= '
		<div class="p-entry__push">
		'."\n";
		$follow_html .= $follow_thumbnail;

		$follow_html .= '
		<div class="p-entry__pushLike">
		<p>'.$title.'</p>'."\n";
		// fb
		if ( $fbPageUrl ) {
			$follow_html .= '
			<div class="p-entry__pushButton">
			<div class="fb-like" data-href="'.esc_url( $options['fbPageUrl'] ).'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
			</div>'."\n";
		}
		// twitter
		$follow_html .= '<div class="p-entry__tw-follow">'."\n";
		if ( $twitterId ) {
			$follow_html .= '<a href="https://twitter.com/'.esc_html( $options['twitterId'] ).'" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@'.esc_html( $options['twitterId'] ).'</a>		</div>'."\n";
		}
		// feedly
		$follow_html .= '<div class="follow_btn follow_feedly"><a href="http://cloud.feedly.com/#subscription/feed/'.home_url().'/feed/" target="blank"><img id="feedlyFollow" src="http://s3.feedly.com/img/follows/feedly-follow-rectangle-volume-small_2x.png" alt="follow us in feedly" width="66" height="20"></a></div>'."\n";
		$follow_html .= '</div><!-- [ /.followSet_body ] -->
		</div>'."\n";
		// 記事がよかったらいいね　ここまで

		global $post;
		if ( $url = get_edit_post_link( $post->ID ) ) {
			$url = admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options' );
			$follow_html .= '<div class="veu_adminEdit"><a href="'.$url.'" class="btn btn-default" target="_blank">'.__( 'Edit follow button', 'vkExUnit' ).'</a></div>';
		}

		$content .= $follow_html;

	endif; // if ( $postType == 'post' && is_single() ) :
	return $content;
}
