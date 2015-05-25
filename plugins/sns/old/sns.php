<?php

/*-------------------------------------------*/
/*	Add menu
/*-------------------------------------------*/
/*	Add setting page
/*-------------------------------------------*/
/*	Add OGP
/*-------------------------------------------*/
/*	snsBtns
/*-------------------------------------------*/
/*	snsBtns _ display page
/*-------------------------------------------*/
/*	facebook comment display page
/*-------------------------------------------*/
/*	facebookLikeBox
/*-------------------------------------------*/
/*	Print facebook Application ID 
/*-------------------------------------------*/
/*	facebook twitter banner
/*-------------------------------------------*/
/*	WP_Widget_snsBnrs Class
/*-------------------------------------------*/
/*	WP_Widget_fbLikeBox Class
/*-------------------------------------------*/




function biz_vektor_sns_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_biz_vektor_options', 'biz_vektor_sns_option_page_capability' );




/*-------------------------------------------*/
/*	Add setting page
/*-------------------------------------------*/

function add_vk_sns_options(){
	require dirname( __FILE__ ) . '/sns_admin.php';
}



function biz_vektor_sns_options_init() {
	if ( false === biz_vektor_get_sns_options() )
		add_option( 'biz_vektor_sns_options', biz_vektor_get_sns_options_default() );

	register_setting(
		'biz_vektor_sns_options_fields', 	//  Immediately following form tag of edit page.
		'biz_vektor_sns_options',			// name attr
		'biz_vektor_sns_options_validate'
	);
}
add_action( 'admin_init', 'biz_vektor_sns_options_init' );

function biz_vektor_get_sns_options() {
	return get_option( 'biz_vektor_sns_options', biz_vektor_get_sns_options_default() );
}

function biz_vektor_get_sns_options_default() {
	$default_options = array(

	);
	return apply_filters( 'biz_vektor_default_options', $default_options );
}

/*-------------------------------------------*/
/*	Set option default
/*	$opstions_default = biz_vektor_get_sns_options_default(); に移行して順次廃止	// 0.11.0
/*-------------------------------------------*/
function biz_vektor_sns_options_default() {
	global $biz_vektor_sns_options_default;
	$biz_vektor_sns_options_default = array(
		// 'pr1_title' => __('Rich theme options', 'biz-vektor'),
	);
}

/*-------------------------------------------*/
/*	Print option
/*-------------------------------------------*/
function biz_vektor_sns_options($optionLabel) {
	$options = biz_vektor_get_sns_options();
	if ( $options[$optionLabel] != false ) { // If !='' that 0 true
		return $options[$optionLabel];
	} else {
		$options_default = biz_vektor_get_sns_options_default();
		if (isset($options_default[$optionLabel]))
		return $options_default[$optionLabel];
	}
}

/*-------------------------------------------*/
/*	validate
/*-------------------------------------------*/
// function biz_vektor_sns_options_validate( $input ) {
// 	$output = $defaults = biz_vektor_get_default_theme_options();

function biz_vektor_sns_options_validate( $input ) {
	$output = $defaults = biz_vektor_get_sns_options_default();

	$output['twitter'] = $input['twitter'];

	// $output['facebook'] = $input['facebook'];

	$output['fbAppId'] = $input['fbAppId'];

	$output['fbAdminId'] = $input['fbAdminId'];

	$output['ogpImage'] = $input['ogpImage'];

	$output['ogTagDisplay'] = $input['ogTagDisplay'];

	$output['snsBtnsFront'] = $input['snsBtnsFront'];
	$output['snsBtnsPage'] = $input['snsBtnsPage'];
	$output['snsBtnsPost'] = $input['snsBtnsPost'];
	$output['snsBtnsInfo'] = $input['snsBtnsInfo'];
	$output['snsBtnsHidden'] = $input['snsBtnsHidden'];

	$output['fbCommentsFront'] = $input['fbCommentsFront'];
	$output['fbCommentsPage'] = $input['fbCommentsPage'];
	$output['fbCommentsPost'] = $input['fbCommentsPost'];
	$output['fbCommentsInfo'] = $input['fbCommentsInfo'];
	$output['fbCommentsHidden'] = $input['fbCommentsHidden'];

	$output['fbLikeBoxFront'] = $input['fbLikeBoxFront'];
	$output['fbLikeBoxSide'] = $input['fbLikeBoxSide'];
	$output['fbLikeBoxURL'] = $input['fbLikeBoxURL'];
	$output['fbLikeBoxStream'] = $input['fbLikeBoxStream'];
	$output['fbLikeBoxFace'] = $input['fbLikeBoxFace'];
	$output['fbLikeBoxHeight'] = $input['fbLikeBoxHeight'];

	return apply_filters( 'biz_vektor_sns_options_validate', $output, $input, $defaults );
}

add_filter('biz_vektor_body_next','biz_vektor_fb_body_next');
function biz_vektor_fb_body_next($body_next){
	$body_next .= '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&appId='.biz_vektor_fbAppId().'&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
	return $body_next;
}

/*-------------------------------------------*/
/*	Add OGP
/*-------------------------------------------*/
add_action('wp_head', 'biz_vektor_ogp' );
function biz_vektor_ogp() {
	//if ( function_exists('biz_vektor_get_theme_options')) {
	$options = biz_vektor_get_sns_options();
	//$ogpImage = $options['ogpImage'];
	//$fbAppId = $options['fbAppId'];
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if (is_home() || is_front_page()) {
		$linkUrl = home_url();
	} else if (is_single() || is_page()) {
		$linkUrl = get_permalink();
	} else {
		$linkUrl = get_permalink();
	}
	$bizVektorOGP = '<!-- [ BizVektorOGP ] -->'."\n";
	$bizVektorOGP .= '<meta property="og:site_name" content="'.get_bloginfo('name').'" />'."\n";
	$bizVektorOGP .= '<meta property="og:url" content="'.$linkUrl.'" />'."\n";
	if ($options['fbAppId']){
		$bizVektorOGP = $bizVektorOGP.'<meta property="fb:app_id" content="'.$options['fbAppId'].'" />'."\n";
	}
	if (is_front_page() || is_home()) {
		$bizVektorOGP .= '<meta property="og:type" content="website" />'."\n";
		if ($options['ogpImage']){
			$bizVektorOGP .= '<meta property="og:image" content="'.$options['ogpImage'].'" />'."\n";
		}
		$bizVektorOGP .= '<meta property="og:title" content="'.get_bloginfo('name').'" />'."\n";
		$bizVektorOGP .= '<meta property="og:description" content="'.get_bloginfo('description').'" />'."\n";
	} else if (is_category() || is_archive()) {
		$bizVektorOGP .= '<meta property="og:type" content="article" />'."\n";
		if ($options['ogpImage']){
			$bizVektorOGP .= '<meta property="og:image" content="'.$options['ogpImage'].'" />'."\n";
		}
	} else if (is_page() || is_single()) {
		$bizVektorOGP .= '<meta property="og:type" content="article" />'."\n";
		// image
		if (has_post_thumbnail()) {
			$image_id = get_post_thumbnail_id();
			$image_url = wp_get_attachment_image_src($image_id,'large', true);
			$bizVektorOGP .= '<meta property="og:image" content="'.$image_url[0].'" />'."\n";
		} else if ($options['ogpImage']){
			$bizVektorOGP .= '<meta property="og:image" content="'.$options['ogpImage'].'" />'."\n";
		}
		// description
		$metaExcerpt = $post->post_excerpt;
		if ($metaExcerpt) {
			$metadescription = $post->post_excerpt;
		} else {
			$metadescription = mb_substr( strip_tags($post->post_content), 0, 240 ); // kill tags and trim 240 chara
			$metadescription = str_replace(array("\r\n","\r","\n"), ' ', $metadescription);
		}
		$bizVektorOGP .= '<meta property="og:title" content="'.get_the_title().' | '.get_bloginfo('name').'" />'."\n";
		$bizVektorOGP .= '<meta property="og:description" content="'.$metadescription.'" />'."\n";
	} else {
		$bizVektorOGP .= '<meta property="og:type" content="article" />'."\n";
		if ($options['ogpImage']){
			$bizVektorOGP .= '<meta property="og:image" content="'.$options['ogpImage'].'" />'."\n";
		}
	}
	$bizVektorOGP .= '<!-- [ /BizVektorOGP ] -->'."\n";
	if ( isset($options['ogTagDisplay']) && $options['ogTagDisplay'] == 'ogp_off' ) {
		$bizVektorOGP = '';
	}
	$bizVektorOGP = apply_filters('bizVektorOGPCustom', $bizVektorOGP );
	echo $bizVektorOGP;
	//} // function_exist
}

// Add BizVektor SNS module style
add_action('wp_head','bizVektorAddSnsStyle');
function bizVektorAddSnsStyle(){
	$cssPath = apply_filters( "snsStyleCustom", plugins_url("plugins/sns/style_bizvektor_sns.css", __FILE__) );
	// wp_enqueue_style( 'vkExUnit_css_path', $cssPath , false, '2013-05-13b');
	$optionStyle = '<link rel="stylesheet" id="bizvektor-ex-unit-css" href="'.$cssPath.'?=20140601" type="text/css" media="all" />'."\n";
	echo $optionStyle;
}

/*-------------------------------------------*/
/*	snsBtns
/*-------------------------------------------*/
function twitterID() {
	$options = biz_vektor_get_sns_options();
	return $options['twitter'];
}

/*-------------------------------------------*/
/*	snsBtns _ display page
/*-------------------------------------------*/
function biz_vektor_snsBtns() {
	$options = biz_vektor_get_sns_options();
	$snsBtnsFront = $options['snsBtnsFront'];
	$snsBtnsPage = $options['snsBtnsPage'];
	$snsBtnsPost = $options['snsBtnsPost'];
	$snsBtnsInfo = $options['snsBtnsInfo'];
	$snsBtnsHidden = $options['snsBtnsHidden'];
	global $wp_query;
	$post = $wp_query->get_queried_object();
	$snsHiddenFlag = false;
	// $snsBtnsHidden divide "," and insert to $snsHiddens by array
	$snsHiddens = explode(",",$snsBtnsHidden);
	foreach( $snsHiddens as $snsHidden ){
		if (get_the_ID() == $snsHidden) {
			$snsHiddenFlag = true ;
		}
	}
	wp_reset_query();
	if (!$snsHiddenFlag) {
		if (
			( is_front_page() && $snsBtnsFront ) ||
			( is_page() && $snsBtnsPage && !is_front_page() ) || 
			( get_post_type() == 'info' && $snsBtnsInfo ) || 
			( get_post_type() == 'post' && $snsBtnsPost ) 
		) {
			get_template_part('plugins/sns/module_snsBtns');
		}
	}
}

/*-------------------------------------------*/
/*	facebook comment display page
/*-------------------------------------------*/
function biz_vektor_fbComments() {
	$options = biz_vektor_get_sns_options();
	global $wp_query;
	$post = $wp_query->get_queried_object();
	$fbCommentHiddenFlag = false ;
	// is stored as an array to $snsHiddens to split with "," $snsBtnsHidden
	$fbCommentHiddens = explode(",",$options['fbCommentsHidden']);
	foreach( $fbCommentHiddens as $fbCommentHidden ){
		if (get_the_ID() == $fbCommentHidden) {
			$fbCommentHiddenFlag = true ;
		}
	}
	wp_reset_query();
	if (!$fbCommentHiddenFlag) {
		if (
			( is_front_page() && $options['fbCommentsFront'] ) || 
			( is_page() && $options['fbCommentsPage'] && !is_front_page() ) || 
			( get_post_type() == 'info' && $options['fbCommentsInfo'] ) || 
			( get_post_type() == 'post' && $options['fbCommentsPost'] )
			) 
		{
			?>
			<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-num-posts="2" data-width="640"></div>
			<style>
			.fb-comments,
			.fb-comments span,
			.fb-comments iframe[style] { width:100% !important; }
			</style>
			<?php
		}
	}
}

/*-------------------------------------------*/
/*	facebookLikeBox
/*-------------------------------------------*/
function biz_vektor_fbLikeBoxFront() {
	$options = biz_vektor_get_sns_options();
	if ( $options['fbLikeBoxFront'] ) {
		biz_vektor_fbLikeBox();
	}
}
function biz_vektor_fbLikeBoxSide() {
	$options = biz_vektor_get_sns_options();
	if ( $options['fbLikeBoxSide'] ) {
		biz_vektor_fbLikeBox();
	}
}
function biz_vektor_fbLikeBox() {
	$options = biz_vektor_get_sns_options();
	$fbLikeBoxStream = $options['fbLikeBoxStream'];
	$fbLikeBoxFace = $options['fbLikeBoxFace'];
	$fbLikeBoxHeight = $options['fbLikeBoxHeight'];
	if ($fbLikeBoxStream) { $fbLikeBoxStream = 'true'; } else { $fbLikeBoxStream = 'false'; }
	if ($fbLikeBoxFace) { $fbLikeBoxFace = 'true'; } else { $fbLikeBoxFace = 'false'; }
	if ($fbLikeBoxHeight) {
		$fbLikeBoxHeight = 'data-height="'.$fbLikeBoxHeight.'" ';
	}
	add_action('wp_footer','biz_vektor_likebox_resize');
?>
<div id="fb-like-box">
<div class="fb-like-box" data-href="<?php echo $options['fbLikeBoxURL'] ?>" data-width="640" <?php echo $fbLikeBoxHeight ?>data-show-faces="<?php echo $fbLikeBoxFace ?>" data-stream="<?php echo $fbLikeBoxStream ?>" data-header="true"></div>
</div>
<?php }

function biz_vektor_likebox_resize(){ ?>
<script type="text/javascript">
likeBoxReSize();
jQuery(window).resize(function(){
	likeBoxReSize();
});
// When load page / window resize
function likeBoxReSize(){
	jQuery('.fb-like-box').each(function(){
		var element = jQuery('.fb-like-box').parent().width();
		console.log(element);
		jQuery(this).attr('data-width',element);
		jQuery(this).children('span:first').css({"width":element});
		jQuery(this).children('span iframe.fb_ltr').css({"width":element});
	});
}
</script>
<?php }

/*-------------------------------------------*/
/*	Print facebook Application ID 
/*-------------------------------------------*/
function biz_vektor_fbAppId () {
	$options = biz_vektor_get_sns_options();
	$fbAppId = $options['fbAppId'];
	return $fbAppId;
}

/*-------------------------------------------*/
/*	WP_Widget_fbLikeBox Class
/*-------------------------------------------*/

class WP_Widget_fbLikeBox extends WP_Widget {
	/** constructor */
	function WP_Widget_fbLikeBox() {
		$widget_ops = array(
			'classname' => 'WP_Widget_fbLikeBox',
			'description' => __( '*　It is necessary to set the Theme options page.', 'biz-vektor' ),
		);
		$widget_name = 'facebook Like Box'.' ('.get_biz_vektor_name().')';
		$this->WP_Widget('fbLikeBox', $widget_name, $widget_ops);
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( $args );
		$options = biz_vektor_get_sns_options();
		if ( !(is_front_page() && $options['fbLikeBoxFront']) && function_exists('biz_vektor_fbLikeBox')) {
			biz_vektor_fbLikeBox();
		}
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {	}

} // class WP_Widget_fbLikeBox

// register WP_Widget_fbLikeBox widget
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_fbLikeBox");'));
