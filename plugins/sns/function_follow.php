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

		$follow_html .= '<section class="veu_followSet">'."\n";?>

			<!-- 記事がよかったらいいね　ここから -->
			<?php 			//スマホ表示分岐
			function is_mobile(){
			$useragents = array(
			'iPhone', // iPhone
			'iPod', // iPod touch
			'Android.*Mobile', // 1.5+ Android *** Only mobile
			'Windows.*Phone', // *** Windows Phone
			'dream', // Pre 1.5 Android
			'CUPCAKE', // 1.5+ Android
			'blackberry9500', // Storm
			'blackberry9530', // Storm
			'blackberry9520', // Storm v2
			'blackberry9550', // Storm v2
			'blackberry9800', // Torch
			'webOS', // Palm Pre Experimental
			'incognito', // Other iPhone browser
			'webmate' // Other iPhone browser
			);
			$pattern = '/'.implode('|', $useragents).'/i';
			return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
			}
			// <!-- 記事がよかったらいいね　ここから -->
			if (is_mobile()) :?>
			<div class="p-shareButton p-asideList p-shareButton-bottom">
			<div class="p-shareButton__cont">
			<div class="p-shareButton__a-cont">
			<div class="p-shareButton__a-cont__img" style="background-image: url('<?php
				$image_id = get_post_thumbnail_id();
				$image_url = wp_get_attachment_image_src($image_id, true);
			?>
			<?php if ( has_post_thumbnail() ): ?>
				<?php echo $image_url[0]; ?>
			<?php else: ?>
				<?php bloginfo('template_url'); ?>/images/no_image.png
			<?php endif; ?>')"></div>
			<div class="p-shareButton__a-cont__btn">
			<p><?php $title ?></p>

			<?php
			$fb_html = '';
			if ( $fbPageUrl ) {
				$fb_html .= '
				<div class="p-shareButton__fb-cont p-shareButton__fb">
				<div class="fb-like" data-href="'.esc_url( $options['fbPageUrl'] ).'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
				<span class="p-shareButton__fb-unable"></span>'."\n";
			}
			echo $fb_html;
			?>

			</div>
			</div>
			</div>
			<div class="p-asideFollowUs__twitter">
			<div class="p-asideFollowUs__twitter__cont">
			<p class="p-asideFollowUs__twitter__item">Twitterで〇〇名前を</p>


			<?php
			$tw_html = '';
			if ( $twitterId ) {
				$tw_html .= '<div class="follow_btn follow_twitter"><a href="https://twitter.com/'.esc_html( $options['twitterId'] ).'" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@'.esc_html( $options['twitterId'] ).'</a></div>'."\n";
			}
			echo $tw_html;
			?>
			</div>
			</div>
			</div>
			<?php else: ?>
			<div style="padding:10px 0px;"></div>
			<!-- 記事がよかったらいいねPC -->
			<div class="p-entry__push">
			<div class="p-entry__pushThumb" style="background-image: url('<?php
				$image_id = get_post_thumbnail_id();
				$image_url = wp_get_attachment_image_src($image_id, true);
			?>
			<?php if ( has_post_thumbnail() ): ?>
				<?php echo $image_url[0]; ?>
			<?php else: ?>
				<?php bloginfo('template_url'); ?>/images/no_image.png
			<?php endif; ?>')"></div>
			<div class="p-entry__pushLike">
			<p><?php echo $title ?></p>


			<?php
			$fb_html = '';
			if ( $fbPageUrl ) {
				$fb_html .= '
				<div class="p-entry__pushButton">
				<div class="fb-like" data-href="'.esc_url( $options['fbPageUrl'] ).'" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
				</div>'."\n";
			}
			echo $fb_html;
			?>
			</div>
			</div>
			<div class="p-entry__tw-follow">
			<div class="p-entry__tw-follow__cont">
			<?php echo '<p class="p-entry__tw-follow__item">Twitterで@'.esc_html( $options['twitterId'] ).'をフォローしよう！</p>'?>



			<?php
			$tw_html = '';
			if ( $twitterId ) {
				$tw_html .= '<a href="https://twitter.com/'.esc_html( $options['twitterId'] ).'" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@'.esc_html( $options['twitterId'] ).'</a>'."\n";
			}
			echo $tw_html;
			?>
			</div>
			</div>
			<?php endif; ?>
			<!-- 記事がよかったらいいね　ここまで -->
<style>

/*記事がよかったら、いいねスマホ*/
.p-shareButton-bottom {
padding-bottom: 15px;
overflow: hidden;
}
.p-shareButton__buttons {
font-weight: 700;
color: #fff;
font-size: 13px;
text-align: center;
}
.p-shareButton__buttons>li {
padding-left: 3px;
padding-right: 4px;
}
.p-shareButton__buttons .c-btn {
padding: 8px 0;
border-radius: 2px;
}
.p-shareButton__buttons .c-ico {
display: block;
margin: auto auto 5px;
}
.p-shareButton__fb {
-webkit-transform: scale(1.2);
-ms-transform: scale(1.2);
transform: scale(1.2);
width: 115px;
}
.p-shareButton__fb-cont {
position: relative;
width: 108px;
margin: 0 auto;
}
.p-shareButton__fb-unable {
position: absolute;
top: 0;
left: 0;
width: 20px;
height: 20px;
}
.p-shareButton__cont {
margin: 15px 0 0;
}
.p-shareButton__a-cont {
background: #2e2e2e;
display: table;
width: 100%;
}
.p-shareButton__a-cont__img {
display: table-cell;
min-width: 130px;
-webkit-background-size: cover;
background-size: cover;
background-repeat: no-repeat;
background-position: center;
}
.p-shareButton__a-cont__btn {
display: table-cell;
padding: 12px;
text-align: center;
}
.p-shareButton__a-cont__btn p {
font-size: 12px;
color: #fff;
font-weight: 700;
padding: 5px 0 15px;
line-height: 1.4;
margin-bottom: 0px;
}
.p-asideFollowUs__twitter {
border: 2px solid #e6e6e6;
margin-top: 15px;
padding: 12px 0;
}
.p-asideFollowUs__twitter__cont {
text-align: center;
font-size: 13px;
color: #252525;
font-weight: 700;
}
.p-asideFollowUs__twitter__item {
display: inline-block;
vertical-align: middle;
margin: 0 2px;
}



/*記事がよかったら、いいねPC*/
.p-entry__push {
margin-bottom: 20px;
display: table;
table-layout: fix;
width: 100%;
background-color: #2b2b2b;
color: #fff;
}
.p-entry__pushThumb {
display: table-cell;
min-width: 240px;
background-position: center;
background-size:cover;
}
.p-entry__pushLike {
display: table-cell;
padding: 20px;
text-align: center;
vertical-align: middle;
line-height: 1.4;
font-size: 20px;
}
.p-entry__pushButton {
margin-top: 15px;
display: inline-block;
width: 200px;
height: 40px;
line-height: 40px;
-webkit-transform: scale(1.2);
-ms-transform: scale(1.2);
transform: scale(1.2);
}
.p-entry__pushButtonLike {
line-height: 1;
}
.p-entry__tw-follow {
margin-bottom: 10px;
background: #f4f4f4;
width: 100%;
padding: 15px 0;
}
.p-entry__tw-follow__cont {
text-align: center;
font-size: 15px;
color: #252525;
}
.p-entry__tw-follow__item {
display: inline-block;
vertical-align: middle;
margin: 0 15px;
}
</style>
<?php
		// $follow_html .= '<h1 class="followSet_title">'.$title.'</h1>';
		// $follow_html .= '<div class="followSet_body">';
		//
		// if ( $fbPageUrl ) {
		// 	$follow_html .= '<div class="follow_btn fb-like" data-href="'.esc_url( $options['fbPageUrl'] ).'" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>'."\n";
		// }
		//
		// if ( $twitterId ) {
		// 	$follow_html .= '<div class="follow_btn follow_twitter"><a href="https://twitter.com/'.esc_html( $options['twitterId'] ).'" class="twitter-follow-button" data-show-count="false" data-lang="ja" data-show-screen-name="false">@'.esc_html( $options['twitterId'] ).'</a></div>'."\n";
		// }
		//
		// $follow_html .= '<div class="follow_btn follow_feedly"><a href="http://cloud.feedly.com/#subscription/feed/'.home_url().'/feed/" target="blank"><img id="feedlyFollow" src="http://s3.feedly.com/img/follows/feedly-follow-rectangle-volume-small_2x.png" alt="follow us in feedly" width="66" height="20"></a></div>'."\n";
		// $follow_html .= '</div><!-- [ /.followSet_body ] -->'."\n";
		$follow_html .= '</section><!-- [ /.followSet ] -->'."\n";

		global $post;
		if ( $url = get_edit_post_link( $post->ID ) ) {
			$url = admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options' );
			$follow_html .= '<div class="veu_adminEdit"><a href="'.$url.'" class="btn btn-default" target="_blank">'.__( 'Edit follow button', 'vkExUnit' ).'</a></div>';
		}

		$content .= $follow_html;

	endif; // if ( $postType == 'post' && is_single() ) :
	return $content;
}
