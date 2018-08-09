<?php

add_post_type_support( 'page', 'excerpt' ); // add excerpt

/*-------------------------------------------*/
/*  Add OGP
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_print_og', 20 );
function vkExUnit_print_og() {
	global $vkExUnit_sns_options;
	$title = '';
	if ( is_single() || is_page() ) {
		$title = get_post_meta( get_the_id(), 'vkExUnit_sns_title', true );
	}
	if ( ! $title ) {
		$title = strip_tags( wp_title( '', false ) );
	}

	//$ogImage = $vkExUnit_sns_options['ogImage'];
	//$fbAppId = $vkExUnit_sns_options['fbAppId'];
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if ( is_home() || is_front_page() ) {
		$linkUrl = home_url( '/' );
	} elseif ( is_single() || is_page() ) {
		$linkUrl = get_permalink();
	} else {
		$linkUrl = get_permalink();
	}
	$vkExUnitOGP  = '<!-- [ ' . vkExUnit_get_name() . ' OGP ] -->' . "\n";
	$vkExUnitOGP .= '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:url" content="' . $linkUrl . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:title" content="' . $title . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:description" content="' . vk_get_page_description() . '" />' . "\n";
	if ( isset( $vkExUnit_sns_options['fbAppId'] ) && $vkExUnit_sns_options['fbAppId'] ) {
		$vkExUnitOGP = $vkExUnitOGP . '<meta property="fb:app_id" content="' . $vkExUnit_sns_options['fbAppId'] . '" />' . "\n";
	}
	if ( is_front_page() || is_home() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="website" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";
		}
	} elseif ( is_category() || is_archive() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";
		}
	} elseif ( is_page() || is_single() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
		// image
		if ( has_post_thumbnail() ) {
			$image_id     = get_post_thumbnail_id();
			$image_url    = wp_get_attachment_image_src( $image_id, 'large', true );
			$vkExUnitOGP .= '<meta property="og:image" content="' . $image_url[0] . '" />' . "\n";
		} elseif ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";
		}
	} else {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";
		}
	}

	$vkExUnitOGP .= '<!-- [ / ' . vkExUnit_get_name() . ' OGP ] -->' . "\n";
	if ( isset( $vkExUnit_sns_options['ogTagDisplay'] ) && $vkExUnit_sns_options['ogTagDisplay'] != true ) {
		$vkExUnitOGP = '';
	}
	$vkExUnitOGP = apply_filters( 'vkExUnitOGPCustom', $vkExUnitOGP );
	echo $vkExUnitOGP;
}
