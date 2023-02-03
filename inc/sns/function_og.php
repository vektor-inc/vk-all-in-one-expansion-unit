<?php

add_post_type_support( 'page', 'excerpt' ); // add excerpt

/*
  Add OGP
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_print_og', 20 );
function vkExUnit_print_og() {
	global $vkExUnit_sns_options;

	// $ogImage = $vkExUnit_sns_options['ogImage'];
	// $fbAppId = $vkExUnit_sns_options['fbAppId'];
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if ( is_home() || is_front_page() ) {
		$linkUrl = home_url( '/' );
	} elseif ( is_single() || is_page() ) {
		$linkUrl = get_permalink();
	} elseif ( is_post_type_archive() ) {
		$linkUrl = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} else {
		$linkUrl = get_permalink();
	}
	$vkExUnitOGP  = '<!-- [ ' . veu_get_name() . ' OGP ] -->' . "\n";
	$vkExUnitOGP .= '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:url" content="' . esc_url( $linkUrl ) . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:title" content="' . veu_get_the_sns_title() . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:description" content="' . esc_attr( vk_get_page_description() ) . '" />' . "\n";

	$addImageTag = false;

	if ( isset( $vkExUnit_sns_options['fbAppId'] ) && $vkExUnit_sns_options['fbAppId'] ) {
		$vkExUnitOGP = $vkExUnitOGP . '<meta property="fb:app_id" content="' . esc_attr( $vkExUnit_sns_options['fbAppId'] ) . '" />' . "\n";
	}

	if ( is_front_page() || is_home() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="website" />' . "\n";
	} else {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
	}

	if ( is_singular() && has_post_thumbnail() ) {

		$image_id         = get_post_thumbnail_id();
		$image_default_id = '';

		if ( veu_package_is_enable( 'default_thumbnail' ) ) {
			$image_option     = get_option( 'veu_defualt_thumbnail' );
			$image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';
		}

		if ( ! empty( $image_id ) ) {
			$image_url    = wp_get_attachment_image_src( $image_id, 'large', true );
			$vkExUnitOGP .= '<meta property="og:image" content="' . $image_url[0] . '" />' . "\n";

			// image:width,image:height INSERT
			$addImageTag = array(
				'type'   => 'id',
				'width'  => $image_url[1],
				'height' => $image_url[2],
			);
		} elseif ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . esc_url( $vkExUnit_sns_options['ogImage'] ) . '" />' . "\n";

			// image:width,image:height INSERT
			$addImageTag = array(
				'type' => 'url',
				'url'  => $vkExUnit_sns_options['ogImage'],
			);
		} elseif ( ! empty( $image_default_id ) ) {
			$image_url    = wp_get_attachment_image_src( $image_default_id, 'large', true );
			$vkExUnitOGP .= '<meta property="og:image" content="' . $image_url[0] . '" />' . "\n";

			// image:width,image:height INSERT
			$addImageTag = array(
				'type'   => 'id',
				'width'  => $image_url[1],
				'height' => $image_url[2],
			);
		}
	} elseif ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
		$vkExUnitOGP .= '<meta property="og:image" content="' . esc_url( $vkExUnit_sns_options['ogImage'] ) . '" />' . "\n";

		// image:width,image:height INSERT
		$addImageTag = array(
			'type' => 'url',
			'url'  => $vkExUnit_sns_options['ogImage'],
		);
	}

	// image:width,image:height INSERT
	if ( is_array( $addImageTag ) ) {

		if ( $addImageTag['type'] == 'id' ) {
			$width  = $addImageTag['width'];
			$height = $addImageTag['height'];

		} elseif ( $addImageTag['type'] == 'url' ) {

			$findPos = strpos( $addImageTag['url'], '/uploads/' );
			if ( $findPos > 0 ) {
				$lpath = '';
				$lpath = substr( $addImageTag['url'], strlen( '/uploads/' ) + $findPos );
				if ( strlen( $lpath ) ) {
					$imgpath                         = dirname( get_theme_root() ) . '/uploads/' . $lpath;
					list($width,$height,$type,$text) = @getimagesize( $imgpath );
				}
			}
		}

		if ( ! empty( $width ) && ! empty( $height ) ) {
			$vkExUnitOGP .= '<meta property="og:image:width" content="' . esc_attr( $width ) . '" />' . "\n";
			$vkExUnitOGP .= '<meta property="og:image:height" content="' . esc_attr( $height ) . '" />' . "\n";
		}
	} // if ( is_array( $addImageTag ) ) {

	$vkExUnitOGP .= '<!-- [ / ' . veu_get_name() . ' OGP ] -->' . "\n";
	if ( isset( $vkExUnit_sns_options['ogTagDisplay'] ) && $vkExUnit_sns_options['ogTagDisplay'] != true ) {
		$vkExUnitOGP = '';
	}
	$vkExUnitOGP = apply_filters( 'vkExUnitOGPCustom', $vkExUnitOGP );
	echo $vkExUnitOGP;
}
