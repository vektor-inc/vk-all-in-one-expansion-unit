<?php

/*-------------------------------------------*/
/*	Add OGP
/*-------------------------------------------*/
add_action('wp_head', 'vkExUnit_print_og' );
function vkExUnit_print_og() {
	$options = vkExUnit_get_sns_options();
	if ($options['ogTagDisplay'] == 'og_on') {

	//$ogImage = $options['ogImage'];
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
	$vkExUnitOGP = '<!-- [ vkExUnitOGP ] -->'."\n";
	$vkExUnitOGP .= '<meta property="og:site_name" content="'.get_bloginfo('name').'" />'."\n";
	$vkExUnitOGP .= '<meta property="og:url" content="'.$linkUrl.'" />'."\n";
	if ($options['fbAppId']){
		$vkExUnitOGP = $vkExUnitOGP.'<meta property="fb:app_id" content="'.$options['fbAppId'].'" />'."\n";
	}
	if (is_front_page() || is_home()) {
		$vkExUnitOGP .= '<meta property="og:type" content="website" />'."\n";
		if ($options['ogImage']){
			$vkExUnitOGP .= '<meta property="og:image" content="'.$options['ogImage'].'" />'."\n";
		}
		$vkExUnitOGP .= '<meta property="og:title" content="'.get_bloginfo('name').'" />'."\n";
		$vkExUnitOGP .= '<meta property="og:description" content="'.get_bloginfo('description').'" />'."\n";
	} else if (is_category() || is_archive()) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />'."\n";
		if ($options['ogImage']){
			$vkExUnitOGP .= '<meta property="og:image" content="'.$options['ogImage'].'" />'."\n";
		}
	} else if (is_page() || is_single()) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />'."\n";
		// image
		if (has_post_thumbnail()) {
			$image_id = get_post_thumbnail_id();
			$image_url = wp_get_attachment_image_src($image_id,'large', true);
			$vkExUnitOGP .= '<meta property="og:image" content="'.$image_url[0].'" />'."\n";
		} else if ($options['ogImage']){
			$vkExUnitOGP .= '<meta property="og:image" content="'.$options['ogImage'].'" />'."\n";
		}
		// description
		$metaExcerpt = $post->post_excerpt;
		if ($metaExcerpt) {
			$metadescription = $post->post_excerpt;
		} else {
			$metadescription = mb_substr( strip_tags($post->post_content), 0, 240 ); // kill tags and trim 240 chara
			$metadescription = str_replace(array("\r\n","\r","\n"), ' ', $metadescription);
		}
		$vkExUnitOGP .= '<meta property="og:title" content="'.get_the_title().' | '.get_bloginfo('name').'" />'."\n";
		$vkExUnitOGP .= '<meta property="og:description" content="'.$metadescription.'" />'."\n";
	} else {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />'."\n";
		if ($options['ogImage']){
			$vkExUnitOGP .= '<meta property="og:image" content="'.$options['ogImage'].'" />'."\n";
		}
	}
	$vkExUnitOGP .= '<!-- [ /vkExUnitOGP ] -->'."\n";
	if ( isset($options['ogTagDisplay']) && $options['ogTagDisplay'] == 'ogp_off' ) {
		$vkExUnitOGP = '';
	}
	$vkExUnitOGP = apply_filters('vkExUnitOGPCustom', $vkExUnitOGP );
	echo $vkExUnitOGP;
	} // if ($options['ogTagDisplay'] == 'og_on')
}