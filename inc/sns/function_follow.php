<?php

add_filter( 'the_content', 'veu_add_follow' );

/**
 * @since 7.0.0 -
 * @param [type] $content [description]
 */
function veu_add_follow( $content ) {

	global $is_pagewidget;

	if ( $is_pagewidget ) { return $content; }

	$postType = vk_get_post_type();

	if ( is_single() && $postType['slug'] == 'post' ) :

		$content .= veu_get_follow_html();

	endif;

	return $content;

}

/*----------------------------------------------*/
/* 記事がよかったらいいね　ここから
/*----------------------------------------------*/
/**
 * since 9.37.0.0
 */
function veu_get_follow_html() {

	$options = veu_get_sns_options();
	if ( ! $options['enableFollowMe'] ) {
		return $content; }

	if ( isset( $options['followMe_title'] ) && $options['followMe_title'] ) {
		$title = $options['followMe_title'];
	} else {
		$title = __( 'Follow me', 'vk-all-in-one-expansion-unit' ) . '!';
	}

	$fbPageUrl = ( isset( $options['fbPageUrl'] ) ) ? $options['fbPageUrl'] : '';
	$twitterId = ( isset( $options['twitterId'] ) ) ? $options['twitterId'] : '';

	$image_id  = get_post_thumbnail_id();
	$image_url = wp_get_attachment_image_src( $image_id, true );

	$follow_html = '<div class="veu_followSet">';
	
	// 画像
	if ( has_post_thumbnail() ) {
		if ( ! $image_url ) {
		   if ( veu_package_is_enable( 'default_thumbnail' ) ) {
				$image_option     = get_option( 'veu_defualt_thumbnail' );
				$image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';
				if ( $image_default_id ) {
					$image_url = wp_get_attachment_image_src( $image_default_id, true );
				}
			}
		}
		if ( ! empty( $image_url ) ) {
			$follow_html .= '<div class="followSet_img" style="background-image: url(\'' . $image_url[0] . '\')"></div>';
		}
	}

	$follow_html .= '
	<div class="followSet_body">
	<p class="followSet_title">' . wp_kses_post( $title ). '</p>' . "\n";
	// fb
	if ( $fbPageUrl ) {
		$follow_html .= '
		<div class="followSet_fb_page">
		<div class="fb-like" data-href="' . esc_url( $options['fbPageUrl'] ) . '" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
		</div>' . "\n";
	}
	// twitter
	if ( $twitterId ) {
		$follow_html .= '<div class="followSet_tw_follow">' . "\n";
		$follow_html .= '<a href="https://twitter.com/' . esc_html( $options['twitterId'] ) . '" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@' . esc_html( $options['twitterId'] ) . '</a>		</div><!-- [ /.twitter ] -->' . "\n";
	}
	// feedly
	$follow_html .= '<div class="follow_feedly"><a href="https://feedly.com/i/subscription/feed/' . esc_url( home_url() ) . '/feed/" target="blank"><img id="feedlyFollow" src="https://s3.feedly.com/img/follows/feedly-follow-rectangle-volume-small_2x.png" alt="follow us in feedly" width="66" height="20"></a></div>' . "\n";
	$follow_html .= '</div><!-- [ /.followSet_body ] -->';

	$follow_html .= '</div>' . "\n";
	// 記事がよかったらいいね　ここまで

	global $post;
	if ( $url = get_edit_post_link( $post->ID ) ) {
		$url          = admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options' );
		$follow_html .= '<div class="veu_adminEdit"><a href="' . $url . '" class="btn btn-default" target="_blank">' . __( 'Edit follow button', 'vk-all-in-one-expansion-unit' ) . '</a></div>';
	}

	return $follow_html;
}